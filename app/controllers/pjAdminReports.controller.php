<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminReports extends pjAdmin
{
	public function pjActionIndex()
	{
	    $this->checkLogin();
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
	    if (self::isGet())
	    {
	        $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('t1.status', 'T')
				->orderBy('t1.order ASC, t2.content ASC')
				->findAll()
				->getData();
			$this->set('vehicle_arr', $vehicle_arr);
				
			$driver_arr = pjMainDriverModel::factory()->select('t1.id, t1.name, t1.email, t1.phone')
				->where('t1.status', 'T')
				->where('t1.role_id', 3)
				->orderBy('t1.name ASC')
				->findAll()
				->getData();
			$this->set('driver_arr', $driver_arr);
	        
	        $date_from = date('Y-m-d', strtotime('-1 month'));
	        $date_to = date('Y-m-d', strtotime('+1 month'));
	        
	        $this->set('date_from', $date_from);
	        $this->set('date_to', $date_to);
	        
	        $first_daye_of_month = date('Y-m-01'); 
	        $last_daye_of_month  = date('Y-m-t'); 
	        $this->set('first_daye_of_month', $first_daye_of_month);
	        $this->set('last_daye_of_month', $last_daye_of_month);
	        
	        $this->appendJs('chart.js');
	        $this->appendJs('chartjs-plugin-datalabels.js');
	        
	        $this->appendCss('datepicker3.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
	        $this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
	        $this->appendJs('pjAdminReports.js');
	    }
	}
	
	public function pjActionPrint()
	{
	    $this->checkLogin();
	    if (!pjAuth::factory()->hasAccess())
	    {
	        $this->sendForbidden();
	        return;
	    }
	    $this->setLayout('pjActionPrint');
	    
	    $date_from = pjDateTime::formatDate($this->_get->toString('date_from'), $this->option_arr['o_date_format']);
	    $date_to = pjDateTime::formatDate($this->_get->toString('date_to'), $this->option_arr['o_date_format']);
	    $driver_id = $this->_get->toInt('driver_id');
	    $vehicle_id = $this->_get->toString('vehicle_id');
	    
	    if($vehicle_id > 0)
	    {
	        $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->find($vehicle_id)
				->getData();
			$this->set('vehicle', $vehicle_arr);
	    }
	    if ($driver_id > 0) {
	    	$driver_arr = pjAuthUserModel::factory()->find($driver_id)->getData();
	    	$this->set('driver', $driver_arr);
	    }
	    $this->getReportData($driver_id, $vehicle_id, $date_from, $date_to);
	}
	
	public function pjActionGenerate()
	{
		$this->setAjax(true);
		
		if (!$this->isXHR())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
		}
		if (!self::isPost())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'HTTP method not allowed.'));
		}
		if (!pjAuth::factory()->hasAccess())
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Access denied.'));
		}
		if (!($this->_post->toInt('generate_report') && $this->_post->toString('date_from') && $this->_post->toString('date_to')))
		{
		    self::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Missing, empty or invalid parameters.'));
		}
		$date_from = pjDateTime::formatDate($this->_post->toString('date_from'), $this->option_arr['o_date_format']);
		$date_to = pjDateTime::formatDate($this->_post->toString('date_to'), $this->option_arr['o_date_format']);
		$driver_id = $this->_post->toInt('driver_id');
	    $vehicle_id = $this->_post->toString('vehicle_id');		
		$this->getReportData($driver_id, $vehicle_id, $date_from, $date_to);
	}
	
	protected function getReportData($driver_id, $vehicle_id, $date_from, $date_to)
	{
	    if((int)$vehicle_id > 0) {
	        $pjBookingModel = pjBookingModel::factory()
	        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
	        ->join('pjClient', "t6.id=t1.client_id", 'left')
	        ->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
	        ->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
	        ->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer');
	    } else {
	        $pjBookingModel = pjBookingModel::factory();
	    }
	    $pjBookingModel->where('t1.status !=', 'cancelled')->whereNotIn('t1.driver_status', array(4,5));
	    
	    if($driver_id > 0)
	    {
	        $driver_vehicle_arr = pjDriverVehicleModel::factory()
	        ->where('t1.driver_id', (int)$driver_id)
	        ->where('t1.date BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
	        ->findAll()->getDataPair('id', 'date');
	        
	        if ($driver_vehicle_arr) {
	            $pjBookingModel->whereIn('DATE(t1.booking_date)', array_values($driver_vehicle_arr));
	            $pjBookingModel->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `id` IN ('.implode(",", array_keys($driver_vehicle_arr)).'))');
	        } else {
	            $pjBookingModel->where('t1.id', 0);
	        }
	    } else {
	        $pjBookingModel->where(sprintf("(DATE(t1.booking_date) BETWEEN '%1\$s' AND '%2\$s')", $date_from, $date_to));
	    }
	    if(!empty($vehicle_id))
	    {
	        if ($vehicle_id == 'own_vehicles') {
	            $pjBookingModel->where('t1.vehicle_id IN (SELECT `id` FROM `'.pjVehicleModel::factory()->getTable().'` WHERE `type`="own")');
	        } else {
	           $pjBookingModel->where('t1.vehicle_id', $vehicle_id);
	        }
	    }
	    if((int)$vehicle_id > 0) {
	        $order_arr = $pjBookingModel->select("t1.*, t2.content as fleet,
					IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
					IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
					t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2,
					IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
					IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
					t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
					t6.title, t6.fname, t6.lname, t6.email,t6.phone")->orderBy('t1.booking_date DESC')->findAll()->getData();
	    } else {
	        $order_arr = $pjBookingModel->findAll()->getData();
	    }
	    $provider_arr = pjProviderModel::factory()->orderBy('t1.name ASC')->findAll()->getData();
	    $this->set('order_arr', $order_arr);
	    $this->set('provider_arr', $provider_arr);
	    $this->set('vehicle_id', $vehicle_id);
	}
	
	public function pjActionDailyPerformance()
	{
	    $this->setAjax(true);
	    
	    $pjBookingModel = pjBookingModel::factory();
	    $date = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
	    $report_selector = $this->_post->toString('report_selector');
	    switch ($report_selector) {
	        case 'destinations':
	            $top_destination_arr = $pjBookingModel->select("
                    (IF(t1.return_id IS NOT NULL AND t1.return_id>0, (IF (t1.pickup_type='server', t3.content, t1.pickup_address)), (IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t8.content,' - ', t6.content), t1.dropoff_address))))) AS `destination`,
                    COUNT(*) AS cnt_bookings
                ")
                ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
                ->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t1.dropoff_place_id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjAreaCoord', "t7.id=t1.dropoff_place_id", 'left')
                ->join('pjMultiLang', "t8.model='pjArea' AND t8.foreign_id=t7.area_id AND t8.field='name' AND t8.locale='".$this->getLocaleId()."'", 'left outer')
                ->join('pjVehicle', 't9.id=t1.vehicle_id', 'inner')
                ->where('DATE(t1.booking_date)', $date)
                ->where('t1.status !=', 'cancelled')
                ->groupBy('1')
                ->orderBy('2 DESC')
                ->findAll()->getData();
                $total_bookings = 0;
                foreach ($top_destination_arr as $val) {
                    $total_bookings += $val['cnt_bookings'];
                }
                $this->set('top_destination_arr', $top_destination_arr);
                $this->set('total_bookings', $total_bookings);
	        break;
	        case 'vehicles':
	            $arr = pjBookingModel::factory()
	            ->select("t1.vehicle_id, t1.distance, t1.pickup_lat, t1.pickup_lng, t1.dropoff_lat, t1.dropoff_lng, t2.registration_number, t2.maker_modell, t2.fuel_consumption")
	            ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
	            ->where('t2.type', 'own')
	            ->where("DATE(t1.booking_date)='".$date."'")
	            ->where("t1.status !='cancelled'")
	            ->orderBy("t1.vehicle_id ASC, t1.booking_date ASC")
	            ->findAll()->getData();
	            $bookings = array();
	            foreach ($arr as $booking) {
	                $bookings[$booking['vehicle_id']][] = $booking;
	            }
	            $results = [];
	            $lastCoords = []; // Lưu tọa độ điểm kết thúc của xe trước đó
	            
	            foreach ($bookings as $vehId => $val) {
	                $lastBooking = end($val);
	                foreach ($val as $b) {
	                    if (!isset($lastCoords[$vehId])) {
	                        $prevLat = $this->vehicle_base_lat;
	                        $prevLng = $this->vehicle_base_lng;
	                        $results[$vehId]['total_driven_km'] = 0;
	                        if (!empty($b['maker_modell'])) {
	                            $results[$vehId]['vehicle_name'] = pjSanitize::clean($b['registration_number'].' | '.$b['maker_modell']);
	                        } else {
	                           $results[$vehId]['vehicle_name'] = pjSanitize::clean($b['registration_number']);
	                        }
	                        $results[$vehId]['fuel_consumption'] = $b['fuel_consumption'];
	                    } else {
	                        $prevLat = $lastCoords[$vehId]['lat'];
	                        $prevLng = $lastCoords[$vehId]['lng'];
	                    }
	                    
	                    $emptyRunData = pjAppController::calcEmptyRunDistance($prevLat, $prevLng, (float)$b['pickup_lat'], (float)$b['pickup_lng'], $this->option_arr);
	                    $emptyRun = 0;
	                    if ($emptyRunData) {
	                        $emptyRun = $emptyRunData['distance'] / 1000;
	                    }
	                    
	                    if ((float)$b['distance'] <= 0) {
	                        $booking_distance = pjAppController::calcEmptyRunDistance((float)$b['pickup_lat'], (float)$b['pickup_lng'], (float)$b['dropoff_lat'], (float)$b['dropoff_lng'], $this->option_arr);
	                        $distance = $booking_distance['distance'] / 1000;
	                    } else {
	                        $distance = (float)$b['distance'];
	                    }
	                    
	                    $results[$vehId]['total_driven_km'] += ($emptyRun + (float)$distance);
	                    
	                    $lastCoords[$vehId] = ['lat' => $b['dropoff_lat'], 'lng' => $b['dropoff_lng']];
	                }
	                // Tính khoảng cách đường chim bay từ điểm trả cuối về Base
	                $distanceToBase = pjAppController::calcEmptyRunDistance(
	                    (float)$lastBooking['dropoff_lat'],
	                    (float)$lastBooking['dropoff_lng'],
	                    $this->vehicle_base_lat,
	                    $this->vehicle_base_lng,
	                    $this->option_arr
	                    );
	                // Nếu khoảng cách lớn hơn 100m mới cần tính thêm chặng về
	                if ($distanceToBase && $distanceToBase['distance'] > $this->threshold) {
	                    $results[$vehId]['total_driven_km'] += $distanceToBase['distance'] / 1000;
	                }
	            }
	            
	            $top_vehicle_arr = array();
	            if ($results) {
	                uasort($results, function($a, $b) {
	                    return $b['total_driven_km'] <=> $a['total_driven_km'];
	                });
                    foreach ($results as $vehId => $val) {
                        $total_fuel_cost = 0;
                        if ((float)$this->option_arr['o_fuel_price'] > 0 && (float)$val['fuel_consumption'] > 0) {
                            $cost_per_km = ((float)$val['fuel_consumption']/100)*(float)$this->option_arr['o_fuel_price'];
                            $total_fuel_cost = round($val['total_driven_km'] * $cost_per_km, 2);
                        }
                        $val['total_fuel_cost'] = $total_fuel_cost;
                        $val['total_driven_km'] = round($val['total_driven_km']);
                        $top_vehicle_arr[] = $val;
                    }
	            }
	            $this->set('top_vehicle_arr', $top_vehicle_arr);
	            break;
	        default:
	            $top_driver_arr = pjBookingModel::factory()->reset()->select('t1.app_driver_id, t2.name AS driver_name, COUNT(t1.id) AS total_bookings, SUM(t1.price) as total_revenue')
	            ->join('pjMainDriver', 't2.id=t1.app_driver_id', 'inner')
	            ->join('pjVehicle', 't3.id=t1.vehicle_id', 'inner')
	            ->where('t3.type', 'own')
	            ->where('DATE(t1.booking_date)', $date)
	            ->where('t1.status !=', 'cancelled')
	            ->groupBy('t1.app_driver_id')
	            ->orderBy('total_revenue DESC, total_bookings DESC')
	            ->findAll()
	            ->getData();
	            $this->set('top_driver_arr', $top_driver_arr);
	        break;
	    }
	}
	
	public function pjActionGetVisualReports() {
	    $this->setAjax(true);
	    
	    $pjBookingModel = pjBookingModel::factory();
	    $date_from = pjDateTime::formatDate($this->_post->toString('visual_date_from'), $this->option_arr['o_date_format']);
	    $date_to = pjDateTime::formatDate($this->_post->toString('visual_date_to'), $this->option_arr['o_date_format']);
	    
	    $tblBooking = pjBookingModel::factory()->getTable();
	    $provider_arr = pjProviderModel::factory()
	    ->select('t1.name AS label, COALESCE(SUM(t2.price), 0) AS value')
	    ->join('pjBooking', 't1.url = t2.domain', 'left outer')
	    ->where('DATE(t2.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
	    ->where('t2.status !=', 'cancelled')
	    ->groupBy('1')
	    ->orderBy('2 DESC')
	    ->findAll()->getData();
	    
	    $booking_arr = $pjBookingModel
	    ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
	    ->where('t1.status !=', 'cancelled')
	    ->findAll()->getData();
	    $payment_arr = array();
	    $payment_arr['total_cash'] = 0;
	    $payment_arr['total_cc'] = 0;
	    $payment_arr['total_paysafe'] = 0;
	    $payment_arr['total_paid'] = 0;
	    foreach ($booking_arr as $k => $val) {
	        if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
	            $payment_arr['total_cash'] += $val['price'];
	        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
	            $payment_arr['total_cc'] += $val['price'];
	        } elseif (in_array($val['payment_method'], array('cash','creditcard_later')) && !empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
	            $payment_arr['total_paysafe'] += $val['price'];
	        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
	            $payment_arr['total_paid'] += $val['price'];
	        } elseif ($val['payment_method'] == 'cash'){
	            $payment_arr['total_cash'] += $val['price'];
	        } elseif ($val['payment_method'] == 'creditcard_later'){
	            $payment_arr['total_cc'] += $val['price'];
	        } else {
	            $payment_arr['total_paid'] += $val['price'];
	        }
	    }
	    $map_pm = array(
	        'total_cash' => 'Cash',
	        'total_cc' => 'Credit card',
	        'total_paysafe' => 'Paysafe QR Code',
	        'total_paid' => 'Paid'
	    );
	    $payment_method_arr = array();
	    foreach ($payment_arr as $k => $v) {
	        $payment_method_arr[] = array(
	            'label' => $map_pm[$k],
	            'value' => $v
	        );
	    }
	    
	    $top_driver_arr = $pjBookingModel->reset()->select('t2.name AS label, SUM(t1.price) as value')
	    ->join('pjMainDriver', 't2.id=t1.app_driver_id', 'inner')
	    ->join('pjVehicle', 't3.id=t1.vehicle_id', 'inner')
	    ->where('t3.type', 'own')
	    ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
	    ->where('t1.status !=', 'cancelled')
	    ->groupBy('1')
	    ->orderBy('2 DESC')
	    ->limit(5)
	    ->findAll()
	    ->getData();
	    foreach ($top_driver_arr as $k => $v) {
	        $driver_name_decoded = html_entity_decode($v['label'], ENT_QUOTES, 'UTF-8');
	        $top_driver_arr[$k]['label'] = $driver_name_decoded;
	    }
	    
	    $arr = $pjBookingModel->reset()
	    ->select("t1.vehicle_id, t1.distance, t1.pickup_lat, t1.pickup_lng, t1.dropoff_lat, t1.dropoff_lng, t2.registration_number, t2.fuel_consumption")
	    ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
	    ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
	    ->where("t1.status !='cancelled'")
	    ->where('t2.type', 'own')
	    ->orderBy("t1.vehicle_id ASC, t1.booking_date ASC")
	    ->findAll()->getData();
	    $bookings = array();
	    foreach ($arr as $booking) {
	        $bookings[$booking['vehicle_id']][] = $booking;
	    }
	    $results = [];
	    $lastCoords = []; // Lưu tọa độ điểm kết thúc của xe trước đó
	    
	    foreach ($bookings as $vehId => $val) {
	        $lastBooking = end($val);
	        foreach ($val as $b) {
	            if (!isset($lastCoords[$vehId])) {
	                $prevLat = $this->vehicle_base_lat;
	                $prevLng = $this->vehicle_base_lng;
	                $results[$vehId]['total_driven_km'] = 0;
	                $results[$vehId]['vehicle_name'] = pjSanitize::clean($b['registration_number']);
	                $results[$vehId]['fuel_consumption'] = $b['fuel_consumption'];
	            } else {
	                $prevLat = $lastCoords[$vehId]['lat'];
	                $prevLng = $lastCoords[$vehId]['lng'];
	            }
	            
	            $emptyRunData = pjAppController::calcEmptyRunDistance($prevLat, $prevLng, (float)$b['pickup_lat'], (float)$b['pickup_lng'], $this->option_arr);
	            $emptyRun = 0;
	            if ($emptyRunData) {
	                $emptyRun = $emptyRunData['distance'] / 1000;
	            }
	            
	            if ((float)$b['distance'] <= 0) {
	                $booking_distance = pjAppController::calcEmptyRunDistance((float)$b['pickup_lat'], (float)$b['pickup_lng'], (float)$b['dropoff_lat'], (float)$b['dropoff_lng'], $this->option_arr);
	                $distance = $booking_distance['distance'] / 1000;
	            } else {
	                $distance = (float)$b['distance'];
	            }
	            
	            $results[$vehId]['total_driven_km'] += ($emptyRun + (float)$distance);
	            
	            $lastCoords[$vehId] = ['lat' => $b['dropoff_lat'], 'lng' => $b['dropoff_lng']];
	        }
	        // Tính khoảng cách đường chim bay từ điểm trả cuối về Base
	        $distanceToBase = pjAppController::calcEmptyRunDistance(
	            (float)$lastBooking['dropoff_lat'],
	            (float)$lastBooking['dropoff_lng'],
	            $this->vehicle_base_lat,
	            $this->vehicle_base_lng,
	            $this->option_arr
	            );
	        // Nếu khoảng cách lớn hơn 100m mới cần tính thêm chặng về
	        if ($distanceToBase && $distanceToBase['distance'] > $this->threshold) {
	            $results[$vehId]['total_driven_km'] += $distanceToBase['distance'] / 1000;
	        }
	    }
	    
	    $top_vehicle_arr = array();
	    if ($results) {
	        uasort($results, function($a, $b) {
	            return $b['total_driven_km'] <=> $a['total_driven_km'];
	        });
            $idx = 0;
            foreach ($results as $vehId => $val) {
                $total_fuel_cost = 0;
                if ((float)$this->option_arr['o_fuel_price'] > 0 && (float)$val['fuel_consumption'] > 0) {
                    $cost_per_km = ((float)$val['fuel_consumption']/100)*(float)$this->option_arr['o_fuel_price'];
                    $total_fuel_cost = round($val['total_driven_km'] * $cost_per_km, 2);
                }
                
                if ($idx < 5) {
                    $top_vehicle_arr['vehicle_name'][] = $val['vehicle_name'];
                    $top_vehicle_arr['driven_km'][] = round($val['total_driven_km']);
                    $top_vehicle_arr['fuel_cost'][] = $total_fuel_cost;
                }
                $idx++;
            }
	    }
	    
	    $top_destination_arr = $pjBookingModel->reset()->select("
            (IF(t1.return_id IS NOT NULL AND t1.return_id>0, (IF (t1.pickup_type='server', t3.content, t1.pickup_address)), (IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t8.content,' - ', t6.content), t1.dropoff_address))))) AS label,
            COUNT(*) AS value
        ")
        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
        ->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t1.dropoff_place_id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjAreaCoord', "t7.id=t1.dropoff_place_id", 'left')
        ->join('pjMultiLang', "t8.model='pjArea' AND t8.foreign_id=t7.area_id AND t8.field='name' AND t8.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjVehicle', 't9.id=t1.vehicle_id', 'inner')
        ->where('t9.type', 'own')
        ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
        ->where('t1.status !=', 'cancelled')
        ->groupBy('1')
        ->orderBy('2 DESC')
        ->limit(5)
        ->findAll()->getData();
	    
        $top_airport_arr = $pjBookingModel->reset()->select("
            (IF(t1.return_id IS NOT NULL AND t1.return_id>0, IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t8.content,' - ', t6.content), t1.dropoff_address)), (IF (t1.pickup_type='server', t3.content, t1.pickup_address)))) AS label,
            COUNT(*) AS value
        ")
        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
        ->join('pjMultiLang', "t6.model='pjAreaCoord' AND t6.foreign_id=t1.dropoff_place_id AND t6.field='place_name' AND t6.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjAreaCoord', "t7.id=t1.dropoff_place_id", 'left')
        ->join('pjMultiLang', "t8.model='pjArea' AND t8.foreign_id=t7.area_id AND t8.field='name' AND t8.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjVehicle', 't9.id=t1.vehicle_id', 'inner')
        ->where('t9.type', 'own')
        ->where('t1.pickup_is_airport', 1)
        ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
        ->where('t1.status !=', 'cancelled')
        ->groupBy('1')
        ->orderBy('2 DESC')
        ->limit(5)
        ->findAll()->getData();
	    
	    pjAppController::jsonResponse(array(
	        'provider_arr' => $provider_arr,
	        'payment_method_arr' => $payment_method_arr,
	        'top_driver_arr' => $top_driver_arr,
	        'top_vehicle_arr' => $top_vehicle_arr,
	        'top_destination_arr' => $top_destination_arr,
	        'top_airport_arr' => $top_airport_arr
	    ));
	}
}
?>