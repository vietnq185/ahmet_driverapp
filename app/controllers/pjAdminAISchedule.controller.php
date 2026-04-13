<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}

class pjAdminAISchedule extends pjAdmin
{
    // Cấu hình tọa độ Base (Innsbruck Airport)
    public $vehicle_base_lat = '47.2576489';
    public $vehicle_base_lng = '11.3513075';
    
    // Cấu hình Buffer theo sơ đồ khách hàng (Trang 1 PDF)
    public $travel_safety_buffer_seconds = 900;      // 15 phút an toàn trước khi đón
    public $unload_payment_buffer_seconds = 900;     // 15 phút sau khi trả khách (Unload/Payment)
    
    public $max_wait_time_seconds = 7200;   
    public $base_max_wait_time_seconds = 7200; // 2 giờ chờ tối đa// 120 phút chờ tối đa
    public $max_distance_km = 200;                   // Giới hạn lọc sơ bộ 100km
    public $force_start_from_base = true;            // Mặc định khởi hành từ Base mỗi ngày
    
    public function pjActionIndex()
    {
        $this->checkLogin();
        
        $this->setAjax(true);
        if ($this->isXHR()) {
            $this->travel_safety_buffer_seconds = (int)$this->option_arr['o_buffer'] * 60;
            $this->unload_payment_buffer_seconds = (int)$this->option_arr['o_unload_payment_buffer_seconds'] * 60;
            $this->max_wait_time_seconds = (int)$this->option_arr['o_max_wait_time_seconds'] * 60;
            $this->base_max_wait_time_seconds = (int)$this->option_arr['o_base_max_wait_time_seconds'] * 60;
            
            $targetDate = $this->_post->toString('selected_date');
            if ($this->_get->check('type') && $this->_get->toString('type') == 'reset') {
                pjBookingModel::factory()->where('DATE(booking_date)="'.$targetDate.'"')->modifyAll(array('vehicle_id' => 0));
            } else {
                $this->pjActionRunAI($targetDate);
            }
            
            pjAppController::jsonResponse(array('status' => 'OK'));
        }
    }
    
    protected function getDataForDay($date) {
        $tblBookingExtra = pjBookingExtraModel::factory()->getTable();
        $tblExtra = pjExtraModel::factory()->getTable();
        $allBookings = pjBookingModel::factory()
        ->select('t1.*,
        (
            SELECT COUNT(*) FROM `'.$tblBookingExtra.'` AS tbe INNER JOIN `'.$tblExtra.'` AS te ON te.id=tbe.extra_id
            WHERE tbe.booking_id=t1.id AND te.external_id IN (5,6)
        ) AS `is_ski_snowboard`')
        ->where('DATE(t1.booking_date)', $date)
        ->where('t1.status != "cancelled"')
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
        
        return [
            'all_bookings' => $allBookings,
            'vehicles' => $vehicles
        ];
    }
    
    public function isInnsbruckAirportRadius($pickupLat, $pickupLng) {
        // Tọa độ trung tâm Sân bay Innsbruck (INN)
        //$airportLat = 47.260278;
        //$airportLng = 11.343889;
        $airportLat = $this->vehicle_base_lat;
        $airportLng = $this->vehicle_base_lng;
        
        $thresholdKM = $this->option_arr['o_base_radius']; // Giới hạn bán kính là 30 km
        
        // Tính khoảng cách bằng kilômét
        $distanceKM = $this->getDistance($pickupLat, $pickupLng, $airportLat, $airportLng, 'km');
        
        // Kiểm tra xem khoảng cách có nhỏ hơn hoặc bằng 30 km hay không
        return $distanceKM <= $thresholdKM;
    } 
    
    /**
     * Lấy dữ liệu di chuyển với cơ chế Cache để tối ưu tốc độ
     */
    private function getTravelData($lat1, $lng1, $lat2, $lng2) {
        $hashKey = hash('sha256', "{$lat1},{$lng1},{$lat2},{$lng2}");
        $cachedData = pjApiCacheDistanceModel::factory()->reset()
        ->where('hash_key', $hashKey)
        ->limit(1)
        ->findAll()
        ->getDataIndex(0);
        
        if ($cachedData) {
            return [
                'duration' => (int)$cachedData['duration_sec'], // giây
                'distance' => (int)$cachedData['distance_meters']  // mét
            ];
        }
        
        // Bước 1: Tính Haversine trước
        $hDist = $this->getDistance($lat1, $lng1, $lat2, $lng2, 'km');
        if ($hDist > $this->max_distance_km) return null;
        
        // Bước 2: Gọi Google API
        $origin = "$lat1,$lng1";
        $destination = "$lat2,$lng2";
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origin&destinations=$destination&key={$this->option_arr['o_google_api_key']}&units=metric&mode=driving";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout ngắn để tránh treo
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['rows'][0]['elements'][0]['status']) && $data['rows'][0]['elements'][0]['status'] === 'OK') {
                $result = [
                    'duration' => (int)$data['rows'][0]['elements'][0]['duration']['value'],
                    'distance' => (int)$data['rows'][0]['elements'][0]['distance']['value']
                ];
                pjApiCacheDistanceModel::factory()->reset()->setAttributes([
                    'hash_key' => $hashKey,
                    'duration_sec' => (int)$data['rows'][0]['elements'][0]['duration']['value'],
                    'distance_meters' => (int)$data['rows'][0]['elements'][0]['distance']['value'],
                    'created' => date('Y-m-d H:i:s')
                ])->insert();
                return $result;
            }
        }
        
        // Fallback: Nếu API lỗi, dùng ước tính dựa trên Haversine để không làm dừng tiến trình
        $estimate = [
            'duration' => ($hDist / 50) * 3600, // Giả định 50km/h
            'distance' => $hDist * 1000
        ];
        return $estimate;
    }
    

    private function checkInsertionFeasibility($tripA, $tripB, $tripC = null) {
        $latA = $tripA ? $tripA['dropoff_lat'] : $this->vehicle_base_lat;
        $lngA = $tripA ? $tripA['dropoff_lng'] : $this->vehicle_base_lng;
        
        $travelAB = $this->getTravelData($latA, $lngA, $tripB['pickup_lat'], $tripB['pickup_lng']);
        if (!$travelAB) return ['feasible' => false];
        
        $requiredB = strtotime($tripB['booking_date']);
        
        // Tính thời điểm sẵn sàng tại A
        if ($tripA) {
            $tripDurationSeconds = (int)$tripA['duration'] * 60;
            $readyAtA = strtotime($tripA['booking_date']) + $tripDurationSeconds + $this->unload_payment_buffer_seconds;
        } else {
            // Xe tại Base xuất phát linh hoạt để kịp giờ
            //$readyAtA = $requiredB - $travelAB['duration'] - $this->travel_safety_buffer_seconds;
            
            // TỪ BASE: Cho phép xe xuất phát bất kỳ lúc nào để kịp giờ (kể cả từ sáng sớm)
            // Thời gian cần thiết = Thời gian chạy thực tế + 15p an toàn
            $timeNeeded = $travelAB['duration'] + $this->travel_safety_buffer_seconds;
            $readyAtA = $requiredB - $timeNeeded;
        }
        
        $arrivalAtB = $readyAtA + $travelAB['duration'];
        
        if (($arrivalAtB + $this->travel_safety_buffer_seconds) > $requiredB) return ['feasible' => false];
        
        $max_wait_time_seconds = $this->max_wait_time_seconds;
        if ($this->isInnsbruckAirportRadius($tripB['pickup_lat'], $tripB['pickup_lng'])) {
            $max_wait_time_seconds = $this->base_max_wait_time_seconds;
        }
        if ($tripA && ($requiredB - $arrivalAtB) > $max_wait_time_seconds) return ['feasible' => false];
        
        $nextUpdate = null;
        if ($tripC) {
            $tripDurationSeconds = (int)$tripB['duration'] * 60;
            $readyAtB = $requiredB + $tripDurationSeconds + $this->unload_payment_buffer_seconds;
            $travelBC = $this->getTravelData($tripB['dropoff_lat'], $tripB['dropoff_lng'], $tripC['pickup_lat'], $tripC['pickup_lng']);
            
            if (!$travelBC) return ['feasible' => false];
            
            $arrivalAtC = $readyAtB + $travelBC['duration'];
            $requiredC = strtotime($tripC['booking_date']);
            
            if (($arrivalAtC + $this->travel_safety_buffer_seconds) > $requiredC) return ['feasible' => false];
            
            $nextUpdate = [
                'start_time' => date('Y-m-d H:i:s', $readyAtB),
                'arrival_time' => date('Y-m-d H:i:s', $arrivalAtC)
            ];
        }
        
        return [
            'feasible' => true,
            'distance_meters' => $travelAB['distance'],
            'start_time' => date('Y-m-d H:i:s', $readyAtA),
            'arrival_time' => date('Y-m-d H:i:s', $arrivalAtB),
            'next_update' => $nextUpdate
        ];
    }
    
    public function pjActionRunAI($date)
    {
        $this->setAjax(true);
        $pjBookingModel = pjBookingModel::factory();
        
        $data = $this->getDataForDay($date);
        $bookings = $data['all_bookings'];
        $vehicles = $data['vehicles'];
        
        
        $schedule = [];
        foreach ($vehicles as $v) $schedule[$v['id']] = [];
        
        $pending = [];
        foreach ($bookings as $b) {
            if (!empty($b['vehicle_id'])) {
                $schedule[$b['vehicle_id']][] = $b;
            } else {
                $pending[] = $b;
            }
        }
        
        $assignedCount = 0;
        foreach ($pending as $bookingB) {
            $bestFit = ['vId' => null, 'minDist' => PHP_INT_MAX, 'pos' => -1, 'times' => null];
            
            foreach ($vehicles as $vehicle) {
                if (
                    ($bookingB['passengers'] > $vehicle['seats'] && $bookingB['passengers'] <= 8) ||
                    ($bookingB['passengers'] > 8 && !in_array((int)$vehicle['seats'], array(7,8))) ||
                    (in_array((int)$bookingB['passengers'], array(7,8,14,15,16)) && (int)$bookingB['is_ski_snowboard'] > 0 && (int)$vehicle['is_ski'] == 0 && (int)$vehicle['is_snowboard'] == 0)
                    ) continue;
                
                
                $vId = $vehicle['id'];
                $trips = $schedule[$vId];
                
                for ($i = 0; $i <= count($trips); $i++) {
                    $tripA = ($i === 0) ? null : $trips[$i - 1];
                    $tripC = ($i === count($trips)) ? null : $trips[$i];
                    
                    // Tối ưu: Nếu chèn giữa, kiểm tra nhanh xem khoảng thời gian giữa A và C có đủ cho B không
                    if ($tripA && $tripC) {
                        $tripDurationSeconds = (int)$tripA['duration'] * 60;
                        $gap = strtotime($tripC['booking_date']) - (strtotime($tripA['booking_date']) + $tripDurationSeconds);
                        
                        $tripDurationSeconds = (int)$bookingB['duration'] * 60;
                        $minRequired = $tripDurationSeconds + ($this->travel_safety_buffer_seconds * 2) + $this->unload_payment_buffer_seconds;
                        if ($gap < $minRequired) continue; // Bỏ qua nếu khe hở quá hẹp
                    }
                    
                    $res = $this->checkInsertionFeasibility($tripA, $bookingB, $tripC);
                    
                    if ($res['feasible']) {
                        if ($res['distance_meters'] < $bestFit['minDist']) {
                            $bestFit = [
                                'vId' => $vId,
                                'minDist' => $res['distance_meters'],
                                'pos' => $i,
                                'times' => $res
                            ];
                        }
                    }
                }
            }
            
            if ($bestFit['vId'] !== null) {
                $vId = $bestFit['vId'];
                $times = $bestFit['times'];
                $bookingB['vehicle_id'] = $vId;
                
                array_splice($schedule[$vId], $bestFit['pos'], 0, [$bookingB]);
                
                $pjBookingModel->reset()->set('id', $bookingB['id'])->modify([
                    'vehicle_id' => $vId,
                    'vehicle_order' => 1,
                    'empty_travel_start_time' => $times['start_time'],
                    'empty_travel_arrival_time' => $times['arrival_time']
                ]);
                
                if ($times['next_update']) {
                    $tripC = $schedule[$vId][$bestFit['pos'] + 1];
                    $pjBookingModel->reset()->set('id', $tripC['id'])->modify([
                        'empty_travel_start_time' => $times['next_update']['start_time'],
                        'empty_travel_arrival_time' => $times['next_update']['arrival_time']
                    ]);
                }
                $assignedCount++;
            }
        }
        
        pjAppController::jsonResponse(['status' => 'OK', 'assigned' => $assignedCount]);
    }
    
    public function getDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km') {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) return 0;
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist); $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($unit == "km") ? ($miles * 1.609344) : $miles;
    }
    
}
?>