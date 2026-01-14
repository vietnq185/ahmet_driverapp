<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}

class pjAdminAISchedule extends pjAdmin
{
    public $vehicle_base_lat = '47.2576489';
    public $vehicle_base_lng = '11.3513075';
    public $vehicle_base_address = 'Innsbruck Airport, Fürstenweg 180, A-6020 Innsbruck, Tirol, Austria';
    
    public $max_wait_time_seconds = 7200; // 2 giờ chờ tối đa
    public $max_distance_km = 200; // Giới hạn khoảng cách Haversine (Lọc sơ bộ)
    
    public $buffer_time_seconds = 300; // 15 phút là thời gian nghỉ cố định/chuẩn bị xe sau mỗi lần Drop-off // 15 phút buffer cộng vào thời gian di chuyển
    public $unload_payment_buffer_seconds = 300; //5 phút buffer cho Unload/Payment
    public $total_post_trip_buffer = 600; // total buffer time
    
    public $force_start_from_base = true;
    
    public function pjActionIndex()
    {
        $this->checkLogin();
        
        $this->setAjax(true);
        if ($this->isXHR()) {
            $this->buffer_time_seconds = (int)$this->option_arr['o_buffer'] * 60;
            $this->unload_payment_buffer_seconds = (int)$this->option_arr['o_unload_payment_buffer_seconds'] * 60;
            $this->max_wait_time_seconds = (int)$this->option_arr['o_max_wait_time_seconds'] * 60;
            
            $this->total_post_trip_buffer = $this->unload_payment_buffer_seconds;
            
            $targetDate = $this->_post->toString('selected_date');
            if ($this->_get->check('type') && $this->_get->toString('type') == 'reset') {
                pjBookingModel::factory()->where('DATE(booking_date)="'.$targetDate.'"')->modifyAll(array('vehicle_id' => 0));
            } else {
                $this->runBatchAssignment($targetDate);
            }
            
            pjAppController::jsonResponse(array('status' => 'OK'));
        }
    }
    
    /**
     * Tính khoảng cách đường chim bay (Haversine).
     */
    protected function haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
    
    /**
     * Lấy thời gian di chuyển (giây) và khoảng cách (mét) qua Google Maps API (có Cache).
     * Sử dụng SHA-256 cho Hash Key.
     */
    protected function getActualTravelData($lat1, $lon1, $lat2, $lon2, $bookingId = 'UNKNOWN') {
        
        // Cấu trúc hashKey theo yêu cầu: SHA-256
        $hashKey = hash('sha256', "{$lat1},{$lon1},{$lat2},{$lon2}");
        
        // 1. Lọc sơ bộ bằng Haversine
        $distanceKm = $this->haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        
        if ($distanceKm > $this->max_distance_km) {
            // Nếu quá xa, trả về ước tính tối thiểu để có thể bị loại bỏ sau
            if ($bookingId != 'UNKNOWN') {
                //echo ("FAIL HAS BOOKING_ID #{$bookingId}: Travel distance exceeds {$this->max_distance_km} ({$distanceKm} KM > " . $this->max_distance_km . " KM).");
            } else {
                //echo ("FAIL #{$bookingId}: Travel distance exceeds {$this->max_distance_km} ({$distanceKm} KM > " . $this->max_distance_km . " KM).");
            }
            return [
                'duration_sec' => round($distanceKm * 100),
                'distance_meters' => round($distanceKm * 1000),
                'distance_km' => $distanceKm
            ];
        }
        
        // --- LOGIC CACHING (KHÔNG CÓ KIỂM TRA class_exists như yêu cầu) ---
        // Giả định pjApiCacheDistanceModel::factory() tồn tại
        $cachedData = pjApiCacheDistanceModel::factory()
        ->where('hash_key', $hashKey)
        ->limit(1)
        ->findAll()
        ->getData();
        
        if (!empty($cachedData)) {
            return [
                'duration_sec' => (int)$cachedData[0]['duration_sec'],
                'distance_meters' => (int)$cachedData[0]['distance_meters'],
                'distance_km' => (int)$cachedData[0]['distance_meters'] / 1000.0
            ];
        }
        // --- KẾT THÚC LOGIC CACHING ---
        
        // Gọi Google Maps API
        $origin = "{$lat1},{$lon1}";
        $destination = "{$lat2},{$lon2}";
        
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?" .
            "origins=" . urlencode($origin) .
            "&destinations=" . urlencode($destination) .
            "&key=" . $this->option_arr['o_google_api_key'] .
            "&mode=driving" .
            "&departure_time=now";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200 || $response === false) {
            // Trả về ước tính nếu API lỗi
            //echo ("FAIL #{$bookingId}: Google API failed or returned non-OK status: " . ($data['status'] ?? 'Unknown Error'));
            return [
                'duration_sec' => round($distanceKm * 120),
                'distance_meters' => round($distanceKm * 1000),
                'distance_km' => $distanceKm
            ];
        }
        
        $data = json_decode($response, true);
        $element = $data['rows'][0]['elements'][0];
        
        if ($element['status'] !== 'OK') {
            // Trả về ước tính nếu API không tìm thấy đường đi (ZERO_RESULTS, v.v.)
            //echo ("FAIL #{$bookingId}: Google API returned status '{$element['status']}' for travel data.");
            return [
                'duration_sec' => round($distanceKm * 120),
                'distance_meters' => round($distanceKm * 1000),
                'distance_km' => $distanceKm
            ];
        }
        
        $actualDuration = (int)$element['duration']['value'];
        $actualDistance = (int)$element['distance']['value']; // Giá trị trả về bằng Meters
        
        // --- LOGIC LƯU CACHE ---
        pjApiCacheDistanceModel::factory()->setAttributes([
            'hash_key' => $hashKey,
            'duration_sec' => $actualDuration,
            'distance_meters' => $actualDistance,
            'created' => date('Y-m-d H:i:s')
        ])->insert();
        // --- KẾT THÚC LOGIC LƯU CACHE ---
        
        return ['duration_sec' => $actualDuration, 'distance_meters' => $actualDistance, 'distance_km' => $distanceKm];
    }
    /**
     * TÍNH TOÁN: Tính thời điểm xe sẵn sàng cho chuyến tiếp theo (Ready Time).
     * Đây là thời điểm xe hoàn thành Drop-off + TOTAL_POST_TRIP_BUFFER
     */
    protected function calculateReadyTime($trip) {
        
        // Nếu là Phantom Trip/Base, thời điểm sẵn sàng là 00:00:00 của ngày booking_date của nó.
        if (isset($trip['is_phantom']) && $trip['is_phantom'] === true) {
            return strtotime(date('Y-m-d 00:00:00', strtotime($trip['booking_date'])));
        }
        
        // Giả định 'duration' là phút (Nếu trip là booking thật)
        $tripDurationSeconds = (int)$trip['duration'] * 60;
        $T_Pickup = strtotime($trip['booking_date']);
        
        // 1. T_Dropoff_End (Thời điểm hoàn thành Drop-off) = T_Pickup + Trip Duration
        $T_Dropoff_End = $T_Pickup + $tripDurationSeconds;
        
        // 2. T_Ready_After_Dropoff = T_Dropoff_End + TOTAL_POST_TRIP_BUFFER
        $T_Ready_After_Dropoff = $T_Dropoff_End + $this->total_post_trip_buffer;
        
        return $T_Ready_After_Dropoff;
    }
    
    // =================================================================
    // 3. HÀM LOGIC XỬ LÝ CHI PHÍ VÀ KHẢ THI
    // =================================================================
    
    /**
     * Xử lý lastTrip để chuẩn hóa dữ liệu đầu vào.
     */
    protected function getEffectiveLastTrip($vehicleId, $trips, $previousDayTrips, $targetDate) {
        
        if (!empty($trips)) {
            // Trips đã được sắp xếp, end() sẽ là chuyến cuối cùng (muộn nhất)
            return end($trips);
        }
        
        if (!$this->force_start_from_base && isset($previousDayTrips[$vehicleId]) && !empty($previousDayTrips[$vehicleId])) {
            return $previousDayTrips[$vehicleId];
        }
        
        // Khởi hành từ Base (Base/Phantom Trip)
        return [
            'id' => 0,
            'is_phantom' => true,
            'dropoff_lat' => $this->vehicle_base_lat,
            'dropoff_lng' => $this->vehicle_base_lng,
            'booking_date' => "{$targetDate} 00:00:00",
            'duration' => 0
            ];
    }
    
    
    
    /**
     * Tính Chi phí (DISTANCE) và Khả thi (FEASIBILITY) để thêm booking MỚI vào CHUỖI.
     * Chi phí là quãng đường rỗng (meters) từ điểm cuối cùng đến điểm đón mới.
     * @param array $lastTrip Chuyến cuối cùng (hoặc Phantom Trip)
     * @param array $newBooking Booking chưa gán
     * @param string $targetDate Ngày gán xe
     * @return array|null Mảng ['distance_meters', 'travel_start_time', 'arrival_time']
     * hoặc null nếu không khả thi.
     */
    protected function calculateTripCost($lastTrip, $newBooking, $targetDate) {
        
        $targetDayStartTimestamp = strtotime("{$targetDate} 00:00:00");
        
        // 1. TÍNH T_Ready_After_Dropoff
        $readyAfterDropoff = $this->calculateReadyTime($lastTrip);
        
        // 2. TÍNH T_Start_Empty_Travel (Thời điểm xe BẮT ĐẦU di chuyển rỗng)
        // Đảm bảo không bắt đầu trước 00:00:00 của targetDate
        $travelStartTimeTimestamp = max($readyAfterDropoff, $targetDayStartTimestamp);
        
        // Tọa độ
        $startLat = $lastTrip['dropoff_lat'];
        $startLng = $lastTrip['dropoff_lng'];
        
        $pickup_lat = $newBooking['pickup_lat'];
        $pickup_lng = $newBooking['pickup_lng'];
        
        /* if ($newBooking['id'] == 80682 && @$lastTrip['id'] == 80681) {
            $origin = "47.432102,12.2142721";
            //$destination = "47.4477519,12.3151773";
            $pickup_lat = '47.4477519';
            $pickup_lng = '12.3151773';
        } */
        
        // Lấy thời gian di chuyển và khoảng cách (meters) từ vị trí cuối cùng/Base đến Pickup mới
        $travelData = $this->getActualTravelData($startLat, $startLng, $pickup_lat, $pickup_lng);
        //$travelData = $this->getActualTravelData($startLat, $startLng, $newBooking['pickup_lat'], $newBooking['pickup_lng']);
        
        $travelTimeSeconds = $travelData['duration_sec'];
        $travelDistanceMeters = $travelData['distance_meters'];
        
        // 4. TÍNH T_Arrival_New_Pickup (Thời điểm xe ĐẾN điểm đón mới)
        $driverArrivalTimeTimestamp = $travelStartTimeTimestamp + $travelTimeSeconds;
        
        // 5. Tính T_Pickup_New_Booking
        $newPickupTimestamp = strtotime($newBooking['booking_date']);
        
        // 6. TÍNH T_Latest_Allowed_Arrival (Thời điểm phải đến TỐI ĐA)
        $latestAllowedArrivalTimestamp = $newPickupTimestamp - $this->buffer_time_seconds;
        
        
        // --- KIỂM TRA KHẢ THI (FEASIBILITY CHECK) ---
        
        // A. Kiểm tra Đến muộn
        if ($driverArrivalTimeTimestamp > $latestAllowedArrivalTimestamp) {
            //echo ("-> FAILED: Booking #{$newBooking['id']} Late Arrival. Arrived: " . date('Y-m-d H:i:s', $driverArrivalTimeTimestamp) . ", Must Arrive Before: " . date('Y-m-d H:i:s', $latestAllowedArrivalTimestamp));
            return null;
        }
        
        // B. Kiểm tra Giới hạn Chờ Tối đa
        $waitTimeSeconds = $newPickupTimestamp - $driverArrivalTimeTimestamp;
        $isPhantomTrip = (isset($lastTrip['is_phantom']) && $lastTrip['is_phantom'] === true);
        
        if ($waitTimeSeconds > $this->max_wait_time_seconds && !$isPhantomTrip) {
            //error_log("-> FAILED: Booking #{$newBooking['id']} Wait Time {$waitTimeSeconds}s exceeds MAX_WAIT_TIME_SECONDS ({$this->max_wait_time_seconds})");
            return null;
        }
        
        // Nếu khả thi...
        return [
            'distance_meters' => $travelDistanceMeters,
            'travel_start_time' => date('Y-m-d H:i:s', $travelStartTimeTimestamp),
            'arrival_time' => date('Y-m-d H:i:s', $driverArrivalTimeTimestamp),
            'wait_time_seconds' => $waitTimeSeconds
        ];
    }
    
    /**
     * TÍNH TOÁN (ĐÃ SỬA LỖI BUFFER): Tính Chi phí và Khả thi khi chèn booking B vào giữa trip A và trip C (Gap Insertion).
     * Chuỗi: A -> B -> C
     */
    protected function calculateGapCost($tripA, $bookingB, $tripC, $targetDate) {
        
        $targetDayStartTimestamp = strtotime("{$targetDate} 00:00:00");
        
        // KIỂM TRA THỨ TỰ THỜI GIAN BẮT BUỘC CHO GAP INSERTION: A < B < C
        $timeA = strtotime($tripA['booking_date']);
        $timeB = strtotime($bookingB['booking_date']);
        $timeC = strtotime($tripC['booking_date']);
        
        if ($timeB < $timeA || $timeB > $timeC) {
            //error_log("-> FAILED CHRONO GAP: B (#{$bookingB['id']}) is not chronologically between A and C. A={$timeA}, B={$timeB}, C={$timeC}");
            return null; // Lỗi nghiêm trọng: B không nằm giữa A và C theo thời gian đón
        }
        
        // 1. CHẶNG A -> B:
        
        $costAB = $this->calculateTripCost($tripA, $bookingB, $targetDate);
        if ($costAB === null) {
            return null; // B không thể đến điểm đón kịp từ A
        }
        
        // Tạo "Chuyến cuối cùng hiệu quả" sau khi hoàn thành B (để tính Ready Time)
        $lastTripB = [
            'id' => $bookingB['id'],
            'booking_date' => $bookingB['booking_date'],
            'duration' => $bookingB['duration'],
            'dropoff_lat' => $bookingB['dropoff_lat'],
            'dropoff_lng' => $bookingB['dropoff_lng'],
            'is_phantom' => false
        ];
        
        // 2. CHẶNG B -> C: Tính toán chi phí và khả thi B -> C
        
        // TÍNH T_Ready_After_Dropoff sau khi hoàn thành B
        $readyAfterDropoffB = $this->calculateReadyTime($lastTripB);
        
        // Thời điểm bắt đầu di chuyển rỗng B->C phải là MAX(Thời điểm sẵn sàng, 00:00:00 của targetDate)
        $travelStartTimeTimestamp_BC = max($readyAfterDropoffB, $targetDayStartTimestamp);
        
        // Lấy thời gian di chuyển B -> C (Thời gian thực tế)
        $travelData_BC = $this->getActualTravelData($lastTripB['dropoff_lat'], $lastTripB['dropoff_lng'], $tripC['pickup_lat'], $tripC['pickup_lng']);
        
        $travelTimeSeconds_BC = $travelData_BC['duration_sec'];
        $travelDistanceMeters_BC = $travelData_BC['distance_meters'];
        
        // TÍNH T_Arrival_New_Pickup (Thời điểm xe ĐẾN điểm đón C)
        $driverArrivalTimeTimestamp_C = $travelStartTimeTimestamp_BC + $travelTimeSeconds_BC;
        
        // TÍNH T_Latest_Allowed_Arrival (Thời điểm phải đến tối đa) cho C
        $latestAllowedArrivalTimestamp_C = strtotime($tripC['booking_date']) - $this->buffer_time_seconds;
        
        
        // KIỂM TRA KHẢ THI C: C có bị muộn do B không?
        if ($driverArrivalTimeTimestamp_C > $latestAllowedArrivalTimestamp_C) {
            //error_log("-> FAILED GAP: Trip C #{$tripC['id']} Late Arrival due to insertion of B #{$bookingB['id']}. Arrived: " . date('Y-m-d H:i:s', $driverArrivalTimeTimestamp_C) . ", Must Arrive Before: " . date('Y-m-d H:i:s', $latestAllowedArrivalTimestamp_C));
            return null; // C bị muộn do chèn B
        }
        
        // 3. TÍNH Insertion Cost
        $travelData_AC = $this->getActualTravelData($tripA['dropoff_lat'], $tripA['dropoff_lng'], $tripC['pickup_lat'], $tripC['pickup_lng']);
        $initialDistance = $travelData_AC['distance_meters'];
        
        $totalGapDistance = $costAB['distance_meters'] + $travelDistanceMeters_BC;
        
        // Gap Cost = (A->B + B->C) - (A->C)
        $insertionCost = $totalGapDistance - $initialDistance;
        
        
        return [
            'insertion_cost_meters' => $insertionCost,
            'distance_ab' => $costAB['distance_meters'],
            'distance_bc' => $travelDistanceMeters_BC,
            // Dữ liệu thời gian cho A -> B
            'travel_start_time' => $costAB['travel_start_time'], // Start A->B
            'arrival_time' => $costAB['arrival_time'],           // Arrival B
            // Dữ liệu thời gian cho B -> C
            'next_start_time' => date('Y-m-d H:i:s', $travelStartTimeTimestamp_BC), // Start B->C
            'next_arrival_time' => date('Y-m-d H:i:s', $driverArrivalTimeTimestamp_C),  // Arrival C
            'total_empty_distance' => $totalGapDistance
        ];
    }
    
    
    // =================================================================
    // 4. HÀM TRUY VẤN VÀ GÁN XE CHÍNH (NEAREST NEIGHBOR GREEDY)
    // =================================================================
    
    protected function getDataForDay($date) {
        // Giả định pjBookingModel::factory() tồn tại
        $tblBookingExtra = pjBookingExtraModel::factory()->getTable();
        $tblExtra = pjExtraModel::factory()->getTable();
        $allBookings = pjBookingModel::factory()
        ->select('t1.*, 
        (
            SELECT COUNT(*) FROM `'.$tblBookingExtra.'` AS tbe INNER JOIN `'.$tblExtra.'` AS te ON te.id=tbe.extra_id 
            WHERE tbe.booking_id=t1.id AND te.external_id IN (5,6)
        ) AS `is_ski_snowboard`')
        ->where('DATE(t1.booking_date)', $date)
        ->orderBy('t1.booking_date ASC')
        ->findAll()
        ->getData();
        
        // Lấy tất cả xe
        $vehicles = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
        ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->where('t1.status', 'T')
        ->where('t1.type', 'own')
        ->orderBy('t1.seats ASC, t1.order ASC, t2.content ASC')
        ->findAll()
        ->getData();
        
        $vehiclePreviousDayTrip = [];
        if (!$this->force_start_from_base && $vehicles) {
            // Lấy chuyến cuối cùng của mỗi xe TỪ HÔM TRƯỚC (vị trí khởi điểm)
            $vehicle_ids_arr = array();
            foreach ($vehicles as $vehicle) {
                $vehicle_ids_arr[] = $vehicle['id'];
            }
            $tmp_vehicle_booking_last_trip_arr = pjBookingModel::factory()->reset()
            ->whereIn('t1.vehicle_id', $vehicle_ids_arr)
            ->where('DATE(t1.booking_date) < "'.$date.'"')
            ->orderBy('t1.booking_date DESC')
            ->findAll()->getDataPair('vehicle_id', null);
            $vehicle_booking_last_trip_arr = array();
            foreach ($tmp_vehicle_booking_last_trip_arr as $vb) {
                $vehicle_booking_last_trip_arr[$vb['vehicle_id']] = $vb;
            }
            
            foreach ($vehicles as $vehicle) {
                if (isset($vehicle_booking_last_trip_arr[$vehicle['id']])) {
                    $vehiclePreviousDayTrip[$vehicle['id']] = $vehicle_booking_last_trip_arr[$vehicle['id']];
                } else {
                    $vehiclePreviousDayTrip[$vehicle['id']] = array();
                }
            }
        }
        
        return [
            'all_bookings' => $allBookings,
            'vehicles' => $vehicles,
            'vehicle_previous_day_trip' => $vehiclePreviousDayTrip
        ];
    }
    
    
    protected function runBatchAssignment($date) {
        
        $data = $this->getDataForDay($date);
        $allBookings = $data['all_bookings'];
        $vehicles = $data['vehicles'];
        $previousDayTrips = $data['vehicle_previous_day_trip'];
        
        $schedule = [];
        $unassignedBookings = [];
        $finalScheduleUpdates = [];
        
        // Khởi tạo lịch trình và nhóm booking đã/chưa gán
        foreach ($vehicles as $vehicle) {
            $schedule[$vehicle['id']] = [];
        }
        $vehicleMap = array_column($vehicles, null, 'id');
        
        // PHÂN LOẠI BOOKING ĐÃ GÁN VÀ CHƯA GÁN
        foreach ($allBookings as $booking) {
            if ($booking['vehicle_id'] !== null && $booking['vehicle_id'] > 0 && isset($schedule[$booking['vehicle_id']])) {
                $schedule[$booking['vehicle_id']][] = $booking;
            } else {
                $unassignedBookings[$booking['id']] = $booking;
            }
        }
        
        // Sắp xếp lịch trình đã gán theo thứ tự chuyến xe (booking_date)
        foreach ($schedule as $vehicleId => &$trips) {
            usort($trips, fn($a, $b) => strtotime($a['booking_date']) <=> strtotime($b['booking_date']));
        }
        unset($trips);
        
        $assignedCount = 0;
        $unassignedAfterPrimary = $unassignedBookings;
        
        // =================================================================
        // PHASE 1: GÁN CƠ BẢN (Gán vào CUỐI chuỗi - Nearest Neighbor Greedy)
        // =================================================================
        //error_log("\n--- PHASE 1: GÁN CƠ BẢN VÀO CUỐI CHUỖI ---");
        do {
            $bestGlobalAssignment = [
                'bookingId' => null,
                'vehicleId' => null,
                'minDistance' => PHP_INT_MAX,
                'times' => null
            ];
            
            $foundAssignment = false;
            
            foreach ($unassignedAfterPrimary as $bookingId => $booking) {
                
                foreach ($vehicleMap as $vehicleId => $vehicle) {
                    if (
                        ($booking['passengers'] > $vehicle['seats'] && $booking['passengers'] <= 8) || 
                        ($booking['passengers'] > 8 && !in_array((int)$vehicle['seats'], array(7,8))) || 
                        (in_array((int)$booking['passengers'], array(7,8,14,15,16)) && (int)$booking['is_ski_snowboard'] > 0 && (int)$vehicle['is_ski'] == 0 && (int)$vehicle['is_snowboard'] == 0)
                    ) continue;
                    
                    $lastTrip = $this->getEffectiveLastTrip($vehicleId, $schedule[$vehicleId], $previousDayTrips, $date);
                    
                    // --- KIỂM TRA THỨ TỰ THỜI GIAN BẮT BUỘC (ĐỂ KHÔNG PHÁ VỠ CHUỖI) ---
                    // P1 chỉ được thêm nếu chuyến mới muộn hơn chuyến cuối cùng trong chuỗi.
                    $isPhantom = $lastTrip['is_phantom'] ?? false;
                    if (!$isPhantom && strtotime($booking['booking_date']) < strtotime($lastTrip['booking_date'])) {
                        //error_log("-> P1 FAILED CHRONO: Booking #{$bookingId} ({$booking['booking_date']}) is earlier than last trip #{$lastTrip['id']} ({$lastTrip['booking_date']}). Skip P1 for this pairing.");
                        continue;
                    }
                    // -----------------------------------------------------------------
                    
                    $costResult = $this->calculateTripCost($lastTrip, $booking, $date);
                    
                    if ($costResult !== null && $costResult['distance_meters'] < $bestGlobalAssignment['minDistance']) {
                        $bestGlobalAssignment = [
                            'bookingId' => $bookingId,
                            'vehicleId' => $vehicleId,
                            'minDistance' => $costResult['distance_meters'],
                            'times' => $costResult
                        ];
                        $foundAssignment = true;
                    }
                }
            }
            
            if ($foundAssignment) {
                $bId = $bestGlobalAssignment['bookingId'];
                $vId = $bestGlobalAssignment['vehicleId'];
                $times = $bestGlobalAssignment['times'];
                $distanceKm = round($bestGlobalAssignment['minDistance'] / 1000, 2);
                
                $bookingToAssign = $unassignedAfterPrimary[$bId];
                
                // Cập nhật mảng Updates cho DB (chưa có vehicle_order)
                $finalScheduleUpdates[$bId] = [
                    'vehicle_id' => $vId,
                    'empty_travel_start_time' => $times['travel_start_time'],
                    'empty_travel_arrival_time' => $times['arrival_time'],
                    'status' => 'assigned'
                ];
                
                // Cập nhật LỊCH TRÌNH trong bộ nhớ
                $bookingToAssign['vehicle_id'] = $vId;
                $bookingToAssign['status'] = 'assigned';
                $schedule[$vId][] = $bookingToAssign;
                
                // Sắp xếp lại lịch trình của xe theo thời gian/thứ tự (Bắt buộc)
                usort($schedule[$vId], fn($a, $b) => strtotime($a['booking_date']) <=> strtotime($b['booking_date']));
                
                // Xóa booking ra khỏi mảng unassigned
                unset($unassignedAfterPrimary[$bId]);
                $assignedCount++;
                //error_log("-> P1: Assigned Booking #{$bId} to Vehicle #{$vId} (Cost: {$distanceKm} KM empty travel).");
            }
            
        } while ($foundAssignment);
        
        // =================================================================
        // PHASE 2: TỐI ƯU HÓA CHÈN VÀO KHOẢNG TRỐNG (GAP INSERTION)
        // =================================================================
        //error_log("\n--- PHASE 2: TỐI ƯU HÓA CHÈN VÀO KHOẢNG TRỐNG (GAP INSERTION) ---");
        
        do {
            $bestGapInsertion = [
                'unassignedIdx' => null,
                'vehicleId' => null,
                'gapIndex' => null,
                'minInsertionCost' => PHP_INT_MAX,
                'times' => null
            ];
            $foundInsertion = false;
            
            foreach ($unassignedAfterPrimary as $bookingId => $bookingB) {
                
                foreach ($schedule as $vehicleId => $trips) {
                    if (empty($trips)) continue;
                    
                    for ($index = 0; $index <= count($trips); $index++) {
                        
                        // Lấy Trip A (trip trước B)
                        if ($index === 0) {
                            $tripA = $this->getEffectiveLastTrip($vehicleId, [], $previousDayTrips, $date);
                        } else {
                            $tripA = $trips[$index - 1];
                        }
                        
                        // Lấy Trip C (trip sau B)
                        if ($index === count($trips)) {
                            // Chèn vào cuối: Đã được P1 xử lý, bỏ qua.
                            continue;
                        }
                        $tripC = $trips[$index];
                        
                        if (
                            ($bookingB['passengers'] > $vehicleMap[$vehicleId]['seats'] && $bookingB['passengers'] <= 8) || 
                            ($bookingB['passengers'] > 8 && !in_array((int)$vehicleMap[$vehicleId]['seats'], array(7,8))) || 
                            (in_array((int)$bookingB['passengers'], array(7,8,14,15,16)) && (int)$bookingB['is_ski_snowboard'] > 0 && (int)$vehicleMap[$vehicleId]['is_ski'] == 0 && (int)$vehicleMap[$vehicleId]['is_snowboard'] == 0)
                        ) continue;
                        
                        // Tính toán chi phí chèn (A -> B -> C)
                        // Hàm này đã có kiểm tra B phải nằm giữa A và C về mặt thời gian.
                        $gapCostResult = $this->calculateGapCost($tripA, $bookingB, $tripC, $date);
                        
                        if ($gapCostResult !== null && $gapCostResult['insertion_cost_meters'] < $bestGapInsertion['minInsertionCost']) {
                            $bestGapInsertion = [
                                'unassignedIdx' => $bookingId,
                                'vehicleId' => $vehicleId,
                                'gapIndex' => $index,
                                'minInsertionCost' => $gapCostResult['insertion_cost_meters'],
                                'times' => $gapCostResult
                            ];
                            $foundInsertion = true;
                        }
                    }
                }
            }
            
            // THỰC HIỆN CHÈN TỐI ƯU NHẤT
            if ($foundInsertion) {
                $bId = $bestGapInsertion['unassignedIdx'];
                $vId = $bestGapInsertion['vehicleId'];
                $index = $bestGapInsertion['gapIndex'];
                $times = $bestGapInsertion['times'];
                $costAddedKm = round($bestGapInsertion['minInsertionCost'] / 1000, 2);
                
                $bookingB = $unassignedAfterPrimary[$bId];
                $tripC = $schedule[$vId][$index];
                
                // Cập nhật mảng Updates cho Booking B (chuyến mới gán)
                $finalScheduleUpdates[$bId] = [
                    'vehicle_id' => $vId,
                    'empty_travel_start_time' => $times['travel_start_time'],
                    'empty_travel_arrival_time' => $times['arrival_time'],
                    'status' => 'assigned',
                ];
                
                // Cập nhật mảng Updates cho Booking C (chuyến bị ảnh hưởng)
                // Cần đảm bảo cập nhật thời gian di chuyển rỗng mới cho chuyến C
                $finalScheduleUpdates[$tripC['id']] = [
                    'vehicle_id' => $vId,
                    'empty_travel_start_time' => $times['next_start_time'],
                    'empty_travel_arrival_time' => $times['next_arrival_time'],
                    'status' => 'assigned'
                ];
                
                // Cập nhật LỊCH TRÌNH trong bộ nhớ
                $bookingB['vehicle_id'] = $vId;
                $bookingB['status'] = 'assigned';
                
                // Chèn B vào vị trí index
                array_splice($schedule[$vId], $index, 0, [$bookingB]);
                
                // Sắp xếp lại lịch trình của xe để đảm bảo thứ tự thời gian
                usort($schedule[$vId], fn($a, $b) => strtotime($a['booking_date']) <=> strtotime($b['booking_date']));
                
                // Xóa booking ra khỏi mảng unassigned
                unset($unassignedAfterPrimary[$bId]);
                $assignedCount++;
                //error_log("-> P2: Gap Inserted Booking #{$bId} to Vehicle #{$vId} at index {$index} (Cost Added: {$costAddedKm} KM).");
            }
        } while ($foundInsertion);
        
        //error_log("\n--- KẾT THÚC GÁN XE: Tổng cộng {$assignedCount} bookings mới được gán. ---");
        //error_log("Số booking không gán được (cần xử lý thủ công): " . count($unassignedAfterPrimary));
        
        
        // =================================================================
        // 5. CẬP NHẬT DỮ LIỆU CUỐI CÙNG VÀO DATABASE
        // =================================================================
        
        $pjBookingModel = pjBookingModel::factory();
        
        // --- PHASE 1 & 2: Cập nhật Times, Vehicle ID và Status ---
        if ($finalScheduleUpdates) {
            foreach ($finalScheduleUpdates as $id => $updateData) {
                // Thêm/Cập nhật vehicle_order (được tính toán ở bước sau)
                unset($updateData['status']);
                $updateData['vehicle_order'] = 1;
                $pjBookingModel->reset()->set('id', $id)->modify($updateData);
            }
        }
        
        // Trả về kết quả hoặc log
        $unassignedAfterAssignment = array_keys($unassignedAfterPrimary);
        return [
            'assigned_count' => $assignedCount,
            'unassigned_ids' => $unassignedAfterAssignment
        ];
    }
}
?>