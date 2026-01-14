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
    
    // CÁC HẰNG SỐ BUFFER ĐƯỢC GIỮ NGUYÊN (Giá trị mặc định hoặc được cập nhật trong pjActionIndex)
    public $buffer_time_seconds = 300; // 5 phút buffer cộng vào thời gian di chuyển
    public $unload_payment_buffer_seconds = 300; // 5 phút buffer cho Unload/Payment sau Drop-off
    public $total_post_trip_buffer = 600; // TỔNG BUFFER SAU KHI DROP-OFF
    
    public $force_start_from_base = true;
    
    // Hàm scheduleByAILog được thêm vào để thay thế error_log nếu cần log trong môi trường cụ thể
    protected function scheduleByAILoga($msg) {
        // Nếu cần ghi log vào file hoặc DB, hãy uncomment dòng dưới
        // error_log($msg);
    }
    
    public function pjActionIndex()
    {
        $this->checkLogin();
        
        $this->setAjax(true);
        if ($this->isXHR()) {
            ini_set("display_errors", "On");
            error_reporting(E_ALL);
            // LẤY CÁC THAM SỐ CẤU HÌNH TỪ OPTIONS (ĐÃ KHÔI PHỤC TÊN FIELDS CỦA BẠN)
            // 'o_buffer' và 'o_unload_payment_buffer_seconds' được nhân 60 để chuyển sang giây.
            $this->buffer_time_seconds = (int)$this->option_arr['o_buffer'] * 60;
            $this->unload_payment_buffer_seconds = (int)$this->option_arr['o_unload_payment_buffer_seconds'] * 60;
            $this->max_wait_time_seconds = (int)$this->option_arr['o_max_wait_time_seconds'] * 60;
            // Tính lại tổng buffer sau Drop-off (TOTAL_POST_TRIP_BUFFER = buffer_time_seconds + unload_payment_buffer_seconds)
            $this->total_post_trip_buffer = $this->buffer_time_seconds + $this->unload_payment_buffer_seconds;
            
            $targetDate = $this->_post->toString('selected_date');
            if ($this->_get->check('type') && $this->_get->toString('type') == 'reset') {
                pjBookingModel::factory()->where('DATE(booking_date)="'.$targetDate.'"')->modifyAll(array('vehicle_id' => 0));
            } else {
                set_time_limit(0);
                $this->runAssignmentAlgorithm($targetDate);
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
     */
    protected function getActualTravelData($lat1, $lon1, $lat2, $lon2, $bookingId = 'UNKNOWN') {
        // 1. Lọc sơ bộ bằng Haversine
        $distanceKm = $this->haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        if ($distanceKm > $this->max_distance_km) {
            return ['duration_seconds' => 999999, 'distance_meters' => 999999999, 'distance_km' => $distanceKm];
        }
        
        $hashKey = hash('sha256', "{$lat1},{$lon1},{$lat2},{$lon2}");
        
        // 2. Kiểm tra Cache (SỬ DỤNG pjApiCacheDistanceModel)
        $pjApiCacheDistanceModel = pjApiCacheDistanceModel::factory();
        $cached = $pjApiCacheDistanceModel->where('hash_key', $hashKey)->limit(1)->findAll()->getData();
        
        if (!empty($cached)) {
            $cached = $cached[0];
            return [
                'duration_seconds' => (int)$cached['duration_sec'],
                'distance_meters' => (int)$cached['distance_meters'],
                'distance_km' => (int)$cached['distance_meters'] / 1000.0,
            ];
        }
        
        // 3. Gọi Google Maps API (Nếu không có Cache)
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?";
        $url .= "origins={$lat1},{$lon1}&destinations={$lat2},{$lon2}&key=" . $this->option_arr['o_google_api_key'];
        $url .= "&mode=driving";
        
        $response = @file_get_contents($url);
        if ($response === FALSE) {
            $this->scheduleByAILog("Google Maps API failed to retrieve data for booking #{$bookingId}. Skipping.");
            return ['duration_seconds' => 999999, 'distance_meters' => 999999999, 'distance_km' => $distanceKm];
        }
        
        $data = json_decode($response, true);
        
        $duration = 999999;
        $distance = 999999999;
        
        if (isset($data['status']) && $data['status'] == 'OK' && !empty($data['rows'][0]['elements'][0])) {
            $element = $data['rows'][0]['elements'][0];
            if (isset($element['status']) && $element['status'] == 'OK') {
                $duration = $element['duration']['value'];
                $distance = $element['distance']['value'];
                
                // 4. Lưu vào Cache (SỬ DỤNG CÁC FIELDS ĐƯỢC CHỈ ĐỊNH)
                $pjApiCacheDistanceModel->setAttributes([
                    'hash_key' => $hashKey,
                    'duration_sec' => $duration,
                    'distance_meters' => $distance,
                    'created' => date('Y-m-d H:i:s')
                ])->insert();
            } else {
                $this->scheduleByAILog("Google Maps element status not OK for booking #{$bookingId}. Status: " . ($element['status'] ?? 'N/A'));
            }
        } else {
            $this->scheduleByAILog("Google Maps response status not OK for booking #{$bookingId}. Status: " . ($data['status'] ?? 'N/A'));
        }
        
        return [
            'duration_seconds' => $duration,
            'distance_meters' => $distance,
            'distance_km' => $distance / 1000.0,
        ];
    }
    
    
    protected function runAssignmentAlgorithm($date)
    {
        $pjBookingModel = pjBookingModel::factory();
        $tblBookingExtra = pjBookingExtraModel::factory()->getTable();
        $tblExtra = pjExtraModel::factory()->getTable();
        // 1. LẤY DỮ LIỆU ĐÃ GÁN (vehicle_id IS NOT NULL AND > 0)
        $assignedTrips = $pjBookingModel
        ->select('t1.*,
        (
            SELECT COUNT(*) FROM `'.$tblBookingExtra.'` AS tbe INNER JOIN `'.$tblExtra.'` AS te ON te.id=tbe.extra_id
            WHERE tbe.booking_id=t1.id AND te.external_id IN (5,6)
        ) AS `is_ski_snowboard`')
        ->where('DATE(t1.booking_date)', $date)
        ->where('vehicle_id > 0')
        ->orderBy('t1.booking_date ASC')
        ->findAll()
        ->getData();
        
        // 2. LẤY DỮ LIỆU CHƯA GÁN (vehicle_id IS NULL OR = 0)
        $unassignedBookings = $pjBookingModel->reset()
        ->select('t1.*,
        (
            SELECT COUNT(*) FROM `'.$tblBookingExtra.'` AS tbe INNER JOIN `'.$tblExtra.'` AS te ON te.id=tbe.extra_id
            WHERE tbe.booking_id=t1.id AND te.external_id IN (5,6)
        ) AS `is_ski_snowboard`')
        ->where('DATE(t1.booking_date)', $date)
        ->where('(vehicle_id <= 0 OR vehicle_id IS NULL)')
        ->orderBy('t1.booking_date ASC')
        ->findAll()
        ->getData();
        
        $vehicles = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
        ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->where('t1.schedule_status', 'T')
        ->where('t1.type', 'own')
        ->orderBy('t1.seats ASC, t1.order ASC, t2.content ASC')
        ->findAll()
        ->getData();
        
        if (empty($unassignedBookings)) {
            return ['assigned_count' => 0, 'unassigned_ids' => []];
        }
        
        // 3. Chuẩn bị lịch trình (Schedule)
        $schedule = [];
        $vehicleMap = array_combine(array_column($vehicles, 'id'), $vehicles);
        
        foreach ($vehicles as $v) {
            $schedule[$v['id']] = [];
        }
        // Phân bổ các chuyến đã gán vào schedule
        foreach ($assignedTrips as $trip) {
            $schedule[$trip['vehicle_id']][] = $trip;
        }
        // Đảm bảo thứ tự thời gian
        foreach ($schedule as $vId => &$trips) {
            usort($trips, fn($a, $b) => strtotime($a['booking_date']) <=> strtotime($b['booking_date']));
        }
        unset($trips);
        
        $unassignedAfterPrimary = array_combine(array_column($unassignedBookings, 'id'), $unassignedBookings);
        
        $finalScheduleUpdates = [];
        $assignedCount = 0;
        
        // =================================================================
        // 4.1. PHASE 1: GÁN CUỐI (PRIMARY ASSIGNMENT) - GÁN VÀO VỊ TRÍ CUỐI CÙNG
        // =================================================================
        $this->scheduleByAILog("PHASE 1: Starting Primary Assignment. Bookings to process: " . count($unassignedAfterPrimary));
        
        do {
            $foundAssignment = false;
            $bestAssignment = ['cost' => 999999999, 'bookingId' => null, 'vehicleId' => null, 'times' => null];
            
            foreach ($unassignedAfterPrimary as $bId => $booking) {
                if (empty($booking['pickup_lat']) || empty($booking['pickup_lng'])) {
                    $this->scheduleByAILog("AI_FAIL_DATA: Booking #{$booking['id']} missing pickup coordinates. Skipping.");
                    continue;
                }
                
                $requiredCapacity = $booking['passengers'];
                $pickupTimeUnix = strtotime($booking['booking_date']);
                
                // Thời điểm muộn nhất xe phải đến (Latest Arrival Time): Pickup Time - Safety Buffer
                $latestArrivalUnix = $pickupTimeUnix - $this->buffer_time_seconds;
                
                // Nếu giờ đón quá sớm so với buffer, có thể loại trừ
                if ($latestArrivalUnix < $pickupTimeUnix - $this->max_wait_time_seconds - $this->buffer_time_seconds) {
                    $this->scheduleByAILog("AI_FAIL_TIME_EARLY: Booking #{$booking['id']} is too early.");
                    continue;
                }
                
                foreach ($schedule as $vId => $trips) {
                    $vehicleCapacity = $vehicleMap[$vId]['seats'];
                    
                    // LỌC 1: KIỂM TRA SỨC CHỨA
                    if ($requiredCapacity > $vehicleCapacity) {
                        continue;
                    }
                    
                    $lastTrip = end($trips) ?: null;
                    $isFirstTrip = empty($lastTrip);
                    
                    // 1. XÁC ĐỊNH ĐIỂM KHỞI HÀNH (Start Point) và Ready Time
                    if ($isFirstTrip) {
                        // CHUYẾN ĐẦU TIÊN TRONG NGÀY
                        $startLat = $this->vehicle_base_lat;
                        $startLng = $this->vehicle_base_lng;
                        // Thời gian xe sẵn sàng (Ready Time): 00:00:00 của ngày booking
                        $readyTimeUnix = strtotime(date('Y-m-d', $pickupTimeUnix));
                    } else {
                        // CÁC CHUYẾN SAU: Ready Time = (Drop-off Time cũ) + total_post_trip_buffer
                        $prevDuration = (int) ($lastTrip['duration'] ?? 1800); // Lấy duration của chuyến trước
                        $prevPickupTimeUnix = strtotime($lastTrip['booking_date']);
                        $prevDropoffTimeUnix = $prevPickupTimeUnix + $prevDuration; // Drop-off Time thực tế
                        
                        $readyTimeUnix = $prevDropoffTimeUnix + $this->total_post_trip_buffer;
                        
                        $startLat = $lastTrip['dropoff_lat'];
                        $startLng = $lastTrip['dropoff_lng'];
                        
                        // Nếu xe không thể sẵn sàng kịp để đến điểm đón đúng giờ (ngay cả với travel time = 0)
                        if ($readyTimeUnix > $latestArrivalUnix) {
                            $this->scheduleByAILog("AI_FAIL_TIME_READY: Booking #{$booking['id']} (Prev: #{$lastTrip['id']}) - Ready Time (" . date('H:i:s', $readyTimeUnix) . ") is LATER than Latest Arrival (" . date('H:i:s', $latestArrivalUnix) . ").");
                            continue;
                        }
                    }
                    
                    $pickup_lat = $booking['pickup_lat'];
                    $pickup_lng = $booking['pickup_lng'];
                    
                    // 2. TÍNH TOÁN THỜI GIAN DI CHUYỂN
                    $travelData = $this->getActualTravelData($startLat, $startLng, $pickup_lat, $pickup_lng, $booking['id']);
                    
                    if ($travelData['duration_seconds'] >= 999999) {
                        $this->scheduleByAILog("AI_FAIL_TRAVEL_DATA: Booking #{$booking['id']} - Travel data fail (too far/API error). Distance KM: {$travelData['distance_km']}");
                        continue; // Không thể di chuyển hoặc quá xa
                    }
                    
                    $totalTravelDuration = $travelData['duration_seconds'];
                    
                    // Thời gian bắt đầu di chuyển rỗng (Start Time to Pick-up)
                    $travelStartTimeUnix = $readyTimeUnix;
                    
                    // *************************************************************
                    // LOGIC CẬP NHẬT: Điều chỉnh Start Time cho chuyến đầu tiên (First Trip)
                    if ($isFirstTrip) {
                        // Thời điểm muộn nhất cần rời khỏi Base để đến đúng Latest Arrival
                        // Latest Departure = Latest Arrival - Travel Duration
                        $latestDepartureUnix = $latestArrivalUnix - $totalTravelDuration;
                        
                        // Start Time = max(Thời gian sẵn sàng (00:00:00), Thời điểm cần rời đi)
                        // Điều này đảm bảo xe không rời đi quá sớm nếu chuyến đi ngắn VÀ ĐẢM BẢO KHÔNG BỊ LOẠI
                        $travelStartTimeUnix = max($readyTimeUnix, $latestDepartureUnix);
                    }
                    // *************************************************************
                    
                    // Thời gian đến điểm đón (Arrival Time at Pick-up)
                    $arrivalTimeUnix = $travelStartTimeUnix + $totalTravelDuration;
                    
                    // 3. KIỂM TRA TÍNH KHẢ THI (Latest Arrival Time)
                    if ($arrivalTimeUnix > $latestArrivalUnix) {
                        $this->scheduleByAILog("AI_FAIL_TIME_ARRIVAL: Booking #{$booking['id']} - Arrival Time (" . date('H:i:s', $arrivalTimeUnix) . ") is LATER than Latest Arrival (" . date('H:i:s', $latestArrivalUnix) . "). Travel Time: {$totalTravelDuration}s.");
                        continue;
                    }
                    
                    // 4. TÍNH CÁC MỐC THỜI GIAN KHÁC
                    $waitTimeSeconds = $pickupTimeUnix - $arrivalTimeUnix;
                    
                    // *************************************************************
                    // LOGIC CẬP NHẬT MỚI: Bỏ qua kiểm tra max_wait_time_seconds cho chuyến đầu tiên.
                    $isWaitTimeValid = ($waitTimeSeconds >= 0);
                    
                    if (!$isFirstTrip) {
                        // ÁP DỤNG GIỚI HẠN WAIT TIME cho CÁC CHUYẾN KHÔNG PHẢI LÀ CHUYẾN ĐẦU TIÊN
                        $isWaitTimeValid = $isWaitTimeValid && ($waitTimeSeconds <= $this->max_wait_time_seconds);
                    }
                    
                    if (!$isWaitTimeValid) {
                        $this->scheduleByAILog("AI_FAIL_WAIT_TIME: Booking #{$booking['id']} - Wait Time ({$waitTimeSeconds}s) exceeds max limit or is negative (Only checked for non-first trip or if negative).");
                        continue;
                    }
                    // *************************************************************
                    
                    // 5. KẾT QUẢ TẠM THỜI
                    $times = [
                        'travel_start_time' => date('Y-m-d H:i:s', $travelStartTimeUnix),
                        'arrival_time' => date('Y-m-d H:i:s', $arrivalTimeUnix),
                        'wait_time_seconds' => $waitTimeSeconds,
                        'empty_travel_distance' => $travelData['distance_km'],
                    ];
                    
                    // Chi phí là khoảng cách di chuyển rỗng
                    $cost = $times['empty_travel_distance'];
                    
                    // 6. Tìm chi phí tối ưu nhất
                    if ($cost < $bestAssignment['cost']) {
                        $bestAssignment = [
                            'cost' => $cost,
                            'bookingId' => $bId,
                            'vehicleId' => $vId,
                            'times' => $times
                        ];
                    }
                }
            }
            
            // Thực hiện Gán Tốt Nhất (nếu tìm thấy)
            if ($bestAssignment['bookingId'] !== null) {
                $foundAssignment = true;
                $bId = $bestAssignment['bookingId'];
                $vId = $bestAssignment['vehicleId'];
                $times = $bestAssignment['times'];
                $bookingToAssign = $unassignedAfterPrimary[$bId];
                
                $this->scheduleByAILog("-> P1: Assigned Booking #{$bId} to Vehicle #{$vId} (Cost: {$bestAssignment['cost']} KM). Start Time: {$times['travel_start_time']}");
                
                // Chuẩn bị dữ liệu cập nhật
                $finalScheduleUpdates[$bId] = [
                    'vehicle_id' => $vId,
                    'empty_travel_start_time' => $times['travel_start_time'],
                    'empty_travel_arrival_time' => $times['arrival_time'],
                    'empty_travel_distance' => $times['empty_travel_distance'],
                    'status' => 'assigned' // Cập nhật status sang assigned
                ];
                
                // Cập nhật LỊCH TRÌNH trong bộ nhớ
                $bookingToAssign['vehicle_id'] = $vId;
                $bookingToAssign['empty_travel_start_time'] = $times['travel_start_time'];
                $bookingToAssign['empty_travel_arrival_time'] = $times['arrival_time'];
                $bookingToAssign['empty_travel_distance'] = $times['empty_travel_distance'];
                $bookingToAssign['status'] = 'assigned';
                
                $schedule[$vId][] = $bookingToAssign;
                
                unset($unassignedAfterPrimary[$bId]);
                $assignedCount++;
            }
            
        } while ($foundAssignment);
        
        // ... (PHASE 2: GAP FILLING - Không thay đổi)
        
        // =================================================================
        // 5. CẬP NHẬT DỮ LIỆU CUỐI CÙNG VÀO DATABASE
        // =================================================================
        
        // --- PHASE 1 & 2: Cập nhật Times, Vehicle ID và Order ---
        $pjBookingModel = pjBookingModel::factory();
        
        if ($finalScheduleUpdates) {
            foreach ($finalScheduleUpdates as $id => $updateData) {
                // Thêm/Cập nhật vehicle_order và bỏ status
                unset($updateData['status']);
                $updateData['vehicle_order'] = 1; // Giá trị tạm thời, sẽ được sắp xếp lại
                $pjBookingModel->reset()->set('id', $id)->modify($updateData);
            }
        }
        
        unset($trips);
        
        // Trả về kết quả
        $unassignedAfterAssignment = array_keys($unassignedAfterPrimary);
        return [
            'assigned_count' => $assignedCount,
            'unassigned_ids' => $unassignedAfterAssignment
        ];
    }
}