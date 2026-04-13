<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjAdminSchedule extends pjAdmin
{
    public $vehicle_base_lat = '47.2576489';
    public $vehicle_base_lng = '11.3513075';
    public $max_distance_km = 100; // Giới hạn khoảng cách Haversine (Lọc sơ bộ)
    public $threshold = 1; // Ngưỡng 1000 mét
    public $vehicle_base_address = 'Innsbruck Airport, Fürstenweg 180, A-6020 Innsbruck, Tirol, Austria';
    public $buffer_time_seconds = 300; // 5 phút là thời gian nghỉ cố định/chuẩn bị xe sau mỗi lần Drop-off
    public $max_wait_time_seconds = 7200; // 2 giờ chờ tối đa
    public $min_gap_fill_seconds = 2700; // 45 phút tối thiểu để lấp đầy khoảng trống
    public $defaultCaptcha = 'admin_captcha';
    
    public function pjActionIndex()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        $driver_arr = pjMainDriverModel::factory()->find($this->getUserId())->getData();
        $this->set('driver_arr', $driver_arr);
        
        $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
        ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->where('t1.status', 'T')
        ->orderBy('t1.order ASC, t2.content ASC')
        ->findAll()
        ->getData();
        $this->set('vehicle_arr', $vehicle_arr);
        
        $date = date('Y-m-d');
        $this->set('date', $date);
        $driver_confirmed_job = 1;
        if ($this->isDriver()) {
            $popup_message = pjDriverPopupModel::factory()->where('t1.driver_id', $this->getUserId())->where('t1.is_displayed', 0)->findAll()->getDataPair(null, 'message');
            $this->set('popup_message', $popup_message);
            
            $driver_confirmed_job = $this->isDriverConfirmedJobs($this->getUserId(), $date);
        }
        $this->set('driver_confirmed_job', $driver_confirmed_job);
        
        $this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
        $this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
        
        $this->appendCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', '', true, false);
        $this->appendJs('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', '', true, false);
        
        $this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
        $this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
        $this->appendCss('clockpicker.css', PJ_THIRD_PARTY_PATH . 'clockpicker/');
        $this->appendJs('clockpicker.js');
        
        $this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
        $this->appendJs('jquery-sortable.js');
        $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
        $this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/')
        ->appendJs('pjAdminSchedule.js');
    }
    
    private function isDriverConfirmedJobs($driver_id, $date) {
        $arr = pjDriverJobStatusModel::factory()
            ->where('t1.driver_id', $driver_id)
            ->where('t1.date', $date)
            ->limit(1)
            ->findAll()->getDataIndex(0);
        if ($arr && $arr['status'] == 'T') {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function pjActionDriverConfirmedJobs() {
        $this->setAjax(true);
        if ($this->_post->check('driver_confirmed_job')) {
            $pjDriverJobStatusModel = pjDriverJobStatusModel::factory();
            $date = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
            $arr = $pjDriverJobStatusModel->where('t1.driver_id', $this->getUserId())->where('t1.date', $date)->limit(1)->findAll()->getDataIndex(0);
            if ($arr) {
                $pjDriverJobStatusModel->reset()->set('id', $pjDriverJobStatusModel['id'])->modify(array('status' => 'T'));
            } else {
                $id = $pjDriverJobStatusModel->reset()->setAttributes(array('driver_id' => $this->getUserId(), 'date' => $date, 'status' => 'T'))->insert()->getInsertId();
                if ($id !== false && (int)$id > 0) {
                    pjAppController::jsonResponse(array('status' => 'OK'));
                } else {
                    pjAppController::jsonResponse(array('status' => 'ERR'));
                }
            }
        }
        pjAppController::jsonResponse(array('status' => 'OK'));
    }
    
    public function pjActionCheckDriverConfirmedJobs() {
        $this->setAjax(true);
        $date = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
        $driver_confirmed_job = $this->isDriverConfirmedJobs($this->getUserId(), $date);
        if ((int)$driver_confirmed_job == 1) {
            pjAppController::jsonResponse(array('status' => 'OK'));
        } else {
            pjAppController::jsonResponse(array('status' => 'ERR'));
        }
    }
    
    public function pjActionGetDriverJobStatus() {
        $this->setAjax(true);
        
        $driver_arr = pjMainDriverModel::factory()->find($this->_get->toInt('driver_id'))->getData();
        $date = pjDateTime::formatDate($this->_get->toString('date'), $this->option_arr['o_date_format']);
        $job_status_arr = pjDriverJobStatusModel::factory()
        ->where('t1.driver_id', $this->_get->toInt('driver_id'))
        ->where('t1.date', $date)
        ->limit(1)
        ->findAll()
        ->getDataIndex(0);
        
        $this->set('driver_arr', $driver_arr)
        ->set('job_status_arr', $job_status_arr);
    }
    
    private function getOrders($post=array()) {
        $pjBookingModel = pjBookingModel::factory()
        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
        ->join('pjClient', "t6.id=t1.client_id", 'left')
        ->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
        ->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjLocation', 't11.id=t1.location_id', 'left outer')
        ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))');
        $pjBookingModel->where('t1.vehicle_id', 0);
        if (!$this->isDriver()) {
            $pjBookingModel->where('t1.admin_confirm_cancelled', 0);
        }
        if (isset($post['q']) && !empty($post['q']))
        {
            $q = pjObject::escapeString($post['q']);
            $pjBookingModel->where("(
                t1.uuid LIKE '%$q%' OR 
                t1.passengers LIKE '%$q%' OR 
                t2.content LIKE '%$q%' OR 
                (IF (t1.pickup_type='server', t3.content, t1.pickup_address)) LIKE '%$q%' OR 
                (IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address))) LIKE '%$q%' OR 
                (IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address))) LIKE '%$q%' OR 
                (IF (t1.pickup_type='server', t3.content, t1.pickup_address)) LIKE '%$q%')");
        }
        if (isset($post['date']) && !empty($post['date']))
        {
            $date = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
            $pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')");
        }
        $arr = $pjBookingModel
        ->select("t1.*, t2.content as fleet,
	    (IF (t1.return_id != '', (SELECT `uuid` FROM `".pjBookingModel::factory()->getTable()."` WHERE `id`=t1.return_id LIMIT 1), '')) AS return_uuid,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
		t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
		t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
		t6.title, t6.fname, t6.lname, t6.email,t6.phone, t10.content AS c_country_title, t11.color AS location_color")
		->orderBy("t1.booking_date ASC")
		->findAll()
		->getData();
		$booking_ids_arr = $booking_extras_arr = array();
		foreach ($arr as $val) {
		    $booking_ids_arr[] = $val['id'];
		}
		if ($booking_ids_arr) {
		    $be_arr = pjBookingExtraModel::factory()->select('t1.*, t2.domain, t2.image_path, t3.content AS name')
		    ->join('pjExtra', 't2.id=t1.extra_id', 'left outer')
		    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
		    ->whereIn('t1.booking_id', $booking_ids_arr)
		    ->orderBy('t3.content ASC')
		    ->findAll()
		    ->getData();
		    foreach ($be_arr as $val) {
		        $booking_extras_arr[$val['booking_id']][] = $val;
		    }
		}
		foreach ($arr as $i => $val) {
		    $arr[$i]['extra_arr'] = isset($booking_extras_arr[$val['id']]) ? $booking_extras_arr[$val['id']] : array();
		}
		return $arr;
    }
    
    private function getSchedule($post=array()) {
        $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
        ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->where('t1.status', 'T')
        ->orderBy('t1.order ASC, t2.content ASC')
        ->findAll()
        ->getData();
        
        $driver_arr = pjMainDriverModel::factory()->select('t1.id, t1.name, t1.email, t1.phone')
        ->where('t1.status', 'T')
        ->where('t1.role_id', 3)
        ->orderBy('t1.name ASC')
        ->findAll()
        ->getData();
        
        $pjBookingModel = pjBookingModel::factory()
        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
        ->join('pjClient', "t6.id=t1.client_id", 'left')
        ->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
        ->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjLocation', 't11.id=t1.location_id', 'left outer')
        ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))');
        $pjBookingModel->where('t1.vehicle_id <>', 0);
        if (!$this->isDriver()) {
            $pjBookingModel->where('t1.admin_confirm_cancelled', 0);
        }
        $date = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
        $pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')");
        $arr = $pjBookingModel
        ->select("t1.*, t2.content as fleet,
        (IF (t1.return_id != '', (SELECT `uuid` FROM `".pjBookingModel::factory()->getTable()."` WHERE `id`=t1.return_id LIMIT 1), '')) AS return_uuid,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
		t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
		t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
		t6.title, t6.fname, t6.lname, t6.email,t6.phone, t10.content AS c_country_title, IF (t1.return_id > 0, '', t11.color) AS location_color")
		->orderBy("t1.booking_date ASC")
		->findAll()
		->getData();
		$booking_ids_arr = $booking_extras_arr = array();
		foreach ($arr as $val) {
		    $booking_ids_arr[] = $val['id'];
		}
		if ($booking_ids_arr) {
		    $be_arr = pjBookingExtraModel::factory()->select('t1.*, t2.content AS name, t3.domain, t3.image_path')
		    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		    ->join('pjExtra', 't3.id=t1.extra_id', 'left outer')
		    ->whereIn('t1.booking_id', $booking_ids_arr)
		    ->orderBy('t2.content ASC')
		    ->findAll()
		    ->getData();
		    foreach ($be_arr as $val) {
		        $booking_extras_arr[$val['booking_id']][] = $val;
		    }
		}
		foreach ($arr as $i => $val) {
		    $arr[$i]['extra_arr'] = isset($booking_extras_arr[$val['id']]) ? $booking_extras_arr[$val['id']] : array();
		}
		$booking_arr = array();
		foreach ($arr as $val) {
		    $booking_arr[$val['vehicle_id']][] = $val;
		}
		
		$assigned_driver_arr = array();
		$pjDriverVehicleModel = pjDriverVehicleModel::factory()
		->join('pjMainDriver', 't2.id=t1.driver_id', 'left')
		->where('t1.date', $date);
		$dv_arr = $pjDriverVehicleModel->select('t1.*, t2.name AS driver_name')->findAll()->getData();
		$driver_vehicle_arr = $assigned_driver_name_arr = array();
		foreach ($dv_arr as $val) {
		    $driver_vehicle_arr[$val['vehicle_id']][$val['order']] = $val['driver_id'];
		    $assigned_driver_name_arr[$val['vehicle_id']][$val['order']] = $val['driver_name'];
		    $assigned_driver_arr[$val['order']][] = $val['driver_id'];
		}
		
		$note_arr = pjNoteModel::factory()->where('t1.status', 'T')->where('t1.date', $date)->orderBy('t1.id DESC')->findAll()->getData();
		$vehicle_note_arr = array();
		foreach ($note_arr as $note) {
		    $vehicle_note_arr[$note['vehicle_id']][] = $note;
		}
		
		$driver_job_status_arr = pjDriverJobStatusModel::factory()
		->where('t1.date', $date)
		->findAll()
		->getDataPair('driver_id', 'status');
		
		$vehicle_driven_km_arr = $this->getVehicleDrivenKm($date);
		
		return array(
		    'booking_arr' => $booking_arr,
		    'vehicle_arr' => $vehicle_arr,
		    'driver_arr' => $driver_arr,
		    'driver_vehicle_arr' => $driver_vehicle_arr,
		    'assigned_driver_name_arr' => $assigned_driver_name_arr,
		    'date' => $date,
		    'assigned_driver_arr' => $assigned_driver_arr,
		    'vehicle_note_arr' => $vehicle_note_arr,
		    'driver_job_status_arr' => $driver_job_status_arr,
		    'vehicle_driven_km_arr' => $vehicle_driven_km_arr
		);
    }
    
    protected function getVehicleDrivenKm($date, $vehicle_ids_arr=array()) {
        $pjBookingModel = pjBookingModel::factory();
        if ($vehicle_ids_arr) {
            $pjBookingModel->whereIn('t1.vehicle_id', $vehicle_ids_arr);
        }
        $arr = $pjBookingModel
        ->select("t1.vehicle_id, t1.distance, t1.pickup_lat, t1.pickup_lng, t1.dropoff_lat, t1.dropoff_lng")
        ->where("DATE(t1.booking_date)='".$date."'")
        ->where("t1.status !='cancelled'")
        ->where('t1.vehicle_id > 0')
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
                } else {
                    $prevLat = $lastCoords[$vehId]['lat'];
                    $prevLng = $lastCoords[$vehId]['lng'];
                }
                
                $emptyRunData = pjAppController::calcEmptyRunDistance($prevLat, $prevLng, (float)$b['pickup_lat'], (float)$b['pickup_lng'], $this->option_arr);
                $emptyRun = 0;
                if ($emptyRunData) {
                    $emptyRun = $emptyRunData['distance'] / 1000;
                }
                
                $results[$vehId]['total_driven_km'] += ($emptyRun + (float)$b['distance']);
                
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
        if ($results) {
            foreach ($results as $vehId => $val) {
                $results[$vehId]['total_driven_km'] = round($val['total_driven_km']);
            }
        }
        return $results;
    }
    
    public function pjActionGetVehicleDrivenKm() {
        $date = $date = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
        $vehicle_ids = $this->_post->toString('vehicle_ids');
        if (!empty($vehicle_ids)) {
            $vehicle_ids_arr = explode("-", $vehicle_ids);
        } else {
            $vehicle_ids_arr = array();
        }
        $arr = $this->getVehicleDrivenKm($date, $vehicle_ids_arr);
        if ($vehicle_ids_arr) {
            $data = array();
            foreach ($vehicle_ids_arr as $vehicle_id) {
                $data[$vehicle_id] = isset($arr[$vehicle_id]) ? $arr[$vehicle_id] : array();
            }
            pjAppController::jsonResponse($data);
        } else {
            pjAppController::jsonResponse($arr);
        }
    }
    
    public function pjActionGetOrders() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $post = $this->_post->raw();
            $order_arr = $this->getOrders($post);
            $this->set('order_arr', $order_arr);
        }
    }
    
    public function pjActionGetSchedule() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $post = $this->_post->raw();
            $schedule_arr = $this->getSchedule($post);
            
            $this->set('schedule_arr', $schedule_arr['booking_arr'])
            ->set('vehicle_arr', $schedule_arr['vehicle_arr'])
            ->set('driver_arr', $schedule_arr['driver_arr'])
            ->set('driver_vehicle_arr', $schedule_arr['driver_vehicle_arr'])
            ->set('date', $schedule_arr['date'])
            ->set('assigned_driver_arr', $schedule_arr['assigned_driver_arr'])
            ->set('assigned_driver_name_arr', $schedule_arr['assigned_driver_name_arr'])
            ->set('vehicle_note_arr', $schedule_arr['vehicle_note_arr'])
            ->set('driver_job_status_arr', $schedule_arr['driver_job_status_arr'])
            ->set('vehicle_driven_km_arr', $schedule_arr['vehicle_driven_km_arr']);
            
            $vehicle_from_api_arr = $this->getVehiclesFromAPI();
            $this->set('vehicle_from_api_arr', $vehicle_from_api_arr);
            
            $date = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
            $this->set('date', $date);
        }
    }
    
    public function pjActionConfirmTimeChange() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
                $arr = pjBookingModel::factory()->find($this->_post->toInt('id'))->getData();
                pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('id'))->modify(array('prev_booking_date' => $arr['booking_date'], 'prev_passengers' => $arr['passengers']));
            }
            pjAppController::jsonResponse(array('status' => 'OK'));
        }
    }
    
    public function pjActionUpdateBooking() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('type')) {
                switch ($this->_post->toString('type')) {
                    case 'assign_vehicle':
                        $is_manual = 0;
                        $app_driver_id = 0;
                        $vehicle_ids = array();
                        if($this->_post->toInt('vehicle_id') > 0){
                            $is_manual = 1;
                            $app_driver_id = $this->_post->check('driver_id') ? $this->_post->toInt('driver_id') : 0;
                            $vehicle_ids[] = $this->_post->toInt('vehicle_id');
                        }
                        
                        $msg_arr = array();
                        if ($booking_ids = $this->_post->toArray('booking_ids')) {
                            $original_booking_arr = pjBookingModel::factory()->select('t1.*, t2.content AS vehicle_name, t3.registration_number')
                            ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.vehicle_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                            ->join('pjVehicle', 't3.id=t1.vehicle_id', 'left outer')
                            ->whereIn('t1.id', $booking_ids)->findAll()->getData();
                            
                            $booking_uuids = array();
                            $registration_number = '';
                            foreach ($original_booking_arr as $ob) {
                                $booking_uuids[] = $ob['uuid'];
                                $registration_number = $ob['registration_number'];
                                if ((int)$ob['vehicle_id'] > 0) {
                                    $vehicle_ids[] = (int)$ob['vehicle_id'];
                                }
                            }
                            
                            pjBookingModel::factory()->reset()->whereIn('id', $booking_ids)->modifyAll(
                                array(
                                    'is_manual' => $is_manual, 
                                    'vehicle_id' => $this->_post->toInt('vehicle_id'), 
                                    'vehicle_order' => $this->_post->toInt('vehicle_order'),
                                    'app_driver_id' => $app_driver_id
                                )
                            );
                            
                            if ($is_manual == 1) {
                                $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS vehicle_name')
                                ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                                ->find($this->_post->toInt('vehicle_id'))->getData();
                                $booking_arr = pjBookingModel::factory()->reset()
                                ->whereIn('t1.id', $booking_ids)
                                ->where('t1.passengers > '.(int)$vehicle_arr['seats'])
                                ->findAll()->getData();
                                if ($booking_arr) {
                                    foreach ($booking_arr as $booking) {
                                        $msg_arr[] = sprintf(__('infoCapacityWarningDetails', true), '#'.$booking['id'], $booking['passengers'], $vehicle_arr['seats']);
                                    }
                                }
                                $data_log = array(
                                    'booking_id' => implode(",", $booking_ids),
                                    'action' => "Bookings [".implode(",", $booking_uuids)."] assigned to vehicle [".$vehicle_arr["registration_number"]."]",
                                    'user_id' => $this->getUserId()
                                );
                                if ($this->_post->check('date') && $this->_post->toString('date') != '') {
                                    $data_log['booking_date'] = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
                                }
                                if (count($original_booking_arr) == 1 && $original_booking_arr[0]['vehicle_id'] > 0) {
                                    $data_log['action'] = "Booking [".$original_booking_arr[0]['id']."]: Vehicle reassigned. Old: [Vehicle ".$original_booking_arr[0]['registration_number']."] | New: [Vehicle ".$vehicle_arr['registration_number']."]";
                                }
                                pjLogModel::factory()->setAttributes($data_log)->insert();
                            } else {
                                $data_log = array(
                                    'booking_id' => implode(",", $booking_ids),
                                    'action' => "Unassigned Bookings [".implode(",", $booking_uuids)."] from vehicle [".$registration_number."]",
                                    'user_id' => $this->getUserId()
                                );
                                if ($this->_post->check('date') && $this->_post->toString('date') != '') {
                                    $data_log['booking_date'] = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
                                }
                                pjLogModel::factory()->setAttributes($data_log)->insert();
                            }
                        } else {
                            pjBookingModel::factory()->set('id', $this->_post->toInt('booking_id'))->modify(array('is_manual' => $is_manual, 'vehicle_id' => $this->_post->toInt('vehicle_id'), 'vehicle_order' => $this->_post->toInt('vehicle_order')));
                        }
                        
                        pjAppController::jsonResponse(array('status' => 'OK', 'vehicle_ids' => implode("-", $vehicle_ids), 'text' => implode("<br/>", $msg_arr)));
                        break;
                    case 'assign_driver':
                        $pjDriverVehicleModel = pjDriverVehicleModel::factory();
                        $date = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
                        
                        $vehicle_arr = pjVehicleModel::factory()->find($this->_post->toInt('vehicle_id'))->getData();
                        
                         $pjDriverVehicleModel
                        ->where('vehicle_id', $this->_post->toInt('vehicle_id'))
                        ->where('date', $date)
                        ->where('`order`', $this->_post->toInt('order'))
                        ->eraseAll();
                        if ($this->_post->toInt('driver_id') > 0) {
                            $pjDriverVehicleModel->reset()
                            ->setAttributes(array(
                                'driver_id' => $this->_post->toInt('driver_id'),
                                'vehicle_id' => $this->_post->toInt('vehicle_id'),
                                'date' => $date,
                                'order' => $this->_post->toInt('order')
                            ))->insert();
                            pjBookingModel::factory()->where('vehicle_id', $this->_post->toInt('vehicle_id'))
                            ->where('DATE(booking_date)', $date)
                            ->where('vehicle_order', $this->_post->toInt('order'))
                            ->modifyAll(array('app_driver_id' => $this->_post->toInt('driver_id')));
                            
                            $driver_arr = pjMainDriverModel::factory()->find($this->_post->toInt('driver_id'))->getData();
                            $data_log = array(
                                'action' => "Assign driver [".@$driver_arr['name']."] to vehicle [".@$vehicle_arr['registration_number']."] on [".$this->_post->toString('date')."]",
                                'user_id' => $this->getUserId(),
                                'booking_date' => $date
                            );
                            pjLogModel::factory()->setAttributes($data_log)->insert();
                        } else {
                            $data = pjBookingModel::factory()->reset()->select('t1.*, t2.name AS driver_name')
                            ->join('pjMainDriver', 't2.id=t1.app_driver_id', 'left outer')
                            ->where('vehicle_id', $this->_post->toInt('vehicle_id'))
                            ->where('DATE(booking_date)', $date)
                            ->where('vehicle_order', $this->_post->toInt('order'))
                            ->limit(1)->findAll()->getDataIndex(0);
                            
                            pjBookingModel::factory()->reset()->where('vehicle_id', $this->_post->toInt('vehicle_id'))
                            ->where('DATE(booking_date)', $date)
                            ->where('vehicle_order', $this->_post->toInt('order'))
                            ->modifyAll(array('app_driver_id' => 0));
                            
                            $driver_arr = pjMainDriverModel::factory()->find($this->_post->toInt('driver_id'))->getData();
                            
                            $data_log = array(
                                'action' => "Unassign driver [".@$data['driver_name']."] from vehicle [".@$vehicle_arr["registration_number"]."] on [".$this->_post->toString('date')."]",
                                'user_id' => $this->getUserId(),
                                'booking_date' => $date
                            );
                            pjLogModel::factory()->setAttributes($data_log)->insert();
                        }
                        
                        $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
                        ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
                        ->where('t1.status', 'T')
                        ->orderBy('t1.order ASC, t2.content ASC')
                        ->findAll()
                        ->getData();
                        
                        $driver_arr = pjMainDriverModel::factory()->select('t1.id, t1.name, t1.email, t1.phone')
                        ->where('t1.status', 'T')
                        ->where('t1.role_id', 3)
                        ->orderBy('t1.name ASC')
                        ->findAll()
                        ->getData();
                        
                        $assigned_driver_arr = array();
                        $pjDriverVehicleModel = pjDriverVehicleModel::factory()
                        ->where('t1.date', $date);
                        $dv_arr = $pjDriverVehicleModel->findAll()->getData();
                        $driver_vehicle_arr = array();
                        foreach ($dv_arr as $val) {
                            $driver_vehicle_arr[$val['vehicle_id']][$val['order']] = $val['driver_id'];
                            $assigned_driver_arr[$val['order']][] = $val['driver_id'];
                        }
                        $this->set('vehicle_arr', $vehicle_arr)
                        ->set('driver_arr', $driver_arr)
                        ->set('driver_vehicle_arr', $driver_vehicle_arr)
                        ->set('assigned_driver_arr', $assigned_driver_arr);
                        break;
                    case 'delete':
                        $arr = pjBookingModel::factory()->find($this->_post->toInt('booking_id'))->getData();
                        if (!$this->isDriver()) {
                            pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('booking_id'))->modify(array('admin_confirm_cancelled' => 1));
                            $data_log = array(
                                'booking_id' => $this->_post->toInt('booking_id'),
                                'action' => "Admin confirmed cancellation for Booking [".$arr['uuid']."]",
                                'user_id' => $this->getUserId(),
                                'booking_date' => date('Y-m-d', strtotime($arr['booking_date']))
                            );
                            pjLogModel::factory()->setAttributes($data_log)->insert();
                        } else {
                            pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('booking_id'))->erase();
                            pjBookingExtraModel::factory()->where('booking_id', $this->_post->toInt('booking_id'))->eraseAll();
                            pjBookingPaymentModel::factory()->where('booking_id', $this->_post->toInt('booking_id'))->eraseAll();
                            
                            $data_log = array(
                                'booking_id' => $this->_post->toInt('booking_id'),
                                'action' => "Deleted Booking [".$arr['uuid']."]",
                                'user_id' => $this->getUserId(),
                                'booking_date' => date('Y-m-d', strtotime($arr['booking_date']))
                            );
                            pjLogModel::factory()->setAttributes($data_log)->insert();
                        }
                        
                        // If admin/driver deletes cancelled booking then locked its status in Booking System
                        $pjHttp = new pjHttp();
                        $booking_id = $arr['external_id'];
                        $result = $pjHttp->curlRequest($arr['domain'].'/index.php?controller=pjApiSync&action=pjActionLockBookingStatus&booking_id='.$booking_id);
                        $resp = $result->getResponse();
                        $resp = json_decode($resp, true);
                        
                        pjAppController::jsonResponse(array('status' => 'OK'));
                        break;
                }
            }
        }
    }
    
    public function pjActionSms()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if ($this->_post->check('send_sms') && $this->_post->check('to') && $this->_post->toString('to') != '' && $this->_post->toString('message') != '')
        {
            $result = pjAppController::messagebirdSendSMS(array($this->_post->toString('to')), $this->_post->toString('message'), $this->option_arr);
            if ($result['status'] == 'OK')
            {
                self::jsonResponse(array('status' => 'OK', 'text' => 'SMS has been sent.'));
            }
            self::jsonResponse(array('status' => 'ERR', 'text' => 'SMS failed to send.'));
        }
        
        if (self::isGet() && $this->_get->check('driver_id') && $this->_get->toInt('driver_id') > 0 && $this->_get->check('vehicle_id') && $this->_get->toInt('vehicle_id') > 0)
        {
            $driver_arr = pjAuthUserModel::factory()->find($this->_get->toInt('driver_id'))->getData();
            $vehicle_arr = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
            ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
            ->find($this->_get->check('vehicle_id'))
            ->getData();
            
            $notification = pjNotificationModel::factory()->where('recipient', 'driver')->where('transport', 'sms')->where('variant', 'assign_vehicle')->findAll()->getDataIndex(0);
            $field = $notification['variant'] . '_sms_' . $notification['recipient'];
            $lang_message = pjMultiLangModel::factory()
            ->reset()
            ->select('t1.*')
            ->where('t1.foreign_id', $this->getForeignId())
            ->where('t1.model','pjOption')
            ->where('t1.locale', $this->getLocaleId())
            ->where('t1.field', $field)
            ->limit(0, 1)
            ->findAll()
            ->getData();
            $search = array('{DriverName}','{DriverEmail}','{DriverPhone}','{Date}','{VehicleName}','{VehicleRegNo}','{VehicleSeats}','{VehicleOrder}');
            $replace = array($driver_arr['name'], $driver_arr['email'], $driver_arr['phone'], $this->_get->toString('date'), $vehicle_arr['name'], $vehicle_arr['registration_number'], $vehicle_arr['seats'], $this->_get->toInt('order'));
            $message = $lang_message ? $lang_message[0]['content'] : '';
            $message = str_replace($search, $replace, $message);
            $this->set('arr', array(
                'phone' => $driver_arr['phone'],
                'message' => $message
            ));
        } else {
            exit;
        }
    }
    
    public function pjActionSyncAllData()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        $provider_arr = pjProviderModel::factory()->orderBy('t1.name ASC')->findAll()->getData();
        $this->set('provider_arr', $provider_arr);
        
        $this->appendJs('pjAdminSchedule.js');
    }
    
    public function pjActionGetInfoSync()
    {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('do_sync')) {
                set_time_limit(0);
                $provider_id = $this->_post->toInt('provider_id');
                $type = $this->_post->toString('type');
                
                $params = array(
                    'type' => $type,
                    'provider_id' => $provider_id,
                    'is_count_page' => 1,
                    'row_count' => 100
                );
                $resp = pjApiSync::pjActionPullAllData($params);
                $resp = array_merge($params, $resp);
                pjAppController::jsonResponse($resp);
            } else {
                pjAppController::jsonResponse(array('status' => 'ERR'));
            }
        }
    }
    
    public function pjActionDoSyncData()
    {
        $this->setAjax(true);
        if ($this->isXHR()) {
            set_time_limit(0);
            $type = $this->_get->toString('type');
            $provider_id = $this->_get->toInt('provider_id');
            $page = 1;
            if ($this->_get->check('page') && $this->_get->toInt('page') > 0) {
                $page = $this->_get->toInt('page');
            }
            
            $params = array(
                'type' => $type,
                'provider_id' => $provider_id,
                'page' => $page,
                'row_count' => 100
            );
            $resp = pjApiSync::pjActionPullAllData($params);
            $resp['page'] = $page;
            $resp['next_page'] = $page + 1;
            pjAppController::jsonResponse($resp);
        }
    }
    
    public function pjActionSyncGeneralData()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        set_time_limit(0);
        pjApiSync::pjActionPullGeneralData($this->option_arr);
        $date = $this->_get->check('date') ? $this->_get->toString('date') : date($this->option_arr['o_date_format']);
        pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSchedule&action=pjActionIndex&date=" . $date);
    }
    
    private function getDriverOrders($post=array()) {
        $driver_arr = pjMainDriverModel::factory()->find($this->getUserId())->getData();
        
        $pjBookingModel = pjBookingModel::factory()
        ->select("t1.*, t2.content as fleet,
        (IF (t1.return_id != '', (SELECT `uuid` FROM `".pjBookingModel::factory()->getTable()."` WHERE `id`=t1.return_id LIMIT 1), '')) AS return_uuid,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
		t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
		t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
		t6.title, t6.fname, t6.lname, t6.email,t6.phone, t10.content AS c_country_title, t11.color AS location_color")
		->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjBooking', "t5.id=t1.return_id", 'left outer')
		->join('pjClient', "t6.id=t1.client_id", 'left')
		->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
		->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
		->join('pjLocation', 't11.id=t1.location_id', 'left outer')
		->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))');
		$date = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
		$pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')");
		$pjBookingModel->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `driver_id`='.$this->getUserId().' AND `date`="'.$date.'" AND t1.vehicle_order=`order`)');
		
		if ($driver_arr['type_of_driver'] == 'own' && $this->option_arr['o_driver_visibility_mode'] == 'scheduled') {
		    $o_release_days_offset = (int)$this->option_arr['o_release_days_offset'];
		    $o_release_time_threshold = $this->option_arr['o_release_time_threshold'];
		    $pjBookingModel->where('NOW() >= TIMESTAMP(DATE_SUB(DATE(t1.booking_date), INTERVAL '.$o_release_days_offset.' DAY), "'.$o_release_time_threshold.'")');
		}
		
		$order_arr = $pjBookingModel->orderBy("t1.booking_date ASC")
		->findAll()
		->getData();
		$booking_ids_arr = $booking_extras_arr = array();
		foreach ($order_arr as $val) {
		    $booking_ids_arr[] = $val['id'];
		}
		if ($booking_ids_arr) {
		    $be_arr = pjBookingExtraModel::factory()->select('t1.*, t2.content AS name')
		    ->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
		    ->whereIn('t1.booking_id', $booking_ids_arr)
		    ->orderBy('t2.content ASC')
		    ->findAll()
		    ->getData();
		    foreach ($be_arr as $val) {
		        $booking_extras_arr[$val['booking_id']][] = $val;
		    }
		}
		foreach ($order_arr as $i => $val) {
		    $order_arr[$i]['extra_arr'] = isset($booking_extras_arr[$val['id']]) ? $booking_extras_arr[$val['id']] : array();
		}
		
		$vehicle_arr = pjVehicleModel::factory()->select('t1.*, t3.content AS name')
		->join('pjDriverVehicle', 't2.vehicle_id=t1.id', 'left outer')
		->join('pjMultiLang', "t3.model='pjVehicle' AND t3.foreign_id=t1.id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
		->where('t2.driver_id', $this->getUserId())
		->where('t2.date', $date)
		->limit(1)
		->findAll()
		->getDataIndex(0);
		
		return compact('order_arr', 'vehicle_arr');
    }
    
    
    public function pjActionGetDriverSchedule() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $post = $this->_post->raw();
            $date = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
            $arr = $this->getDriverOrders($post);
            $this->set('order_arr', $arr['order_arr'])
            ->set('vehicle_arr', $arr['vehicle_arr'])
            ->set('date', $date);
        }
    }
    
    public function pjActionViewOrder() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $arr = pjBookingModel::factory()
            ->select("t1.*, t2.content as fleet,
            (IF (t1.return_id != '', (SELECT `uuid` FROM `".pjBookingModel::factory()->getTable()."` WHERE `id`=t1.return_id LIMIT 1), '')) AS return_uuid,
			IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
			IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
			t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2, t5.c_address AS c_address2, t5.c_destination_address AS c_destination_address2, t5.c_hotel as c_hotel2,
			IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
			IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
			t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
			t6.title, t6.fname, t6.lname, t6.email, t6.phone, t10.content AS c_country_title, t11.price AS duplicate_price")
			->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjBooking', "t5.id=t1.return_id", 'left outer')
			->join('pjClient', "t6.id=t1.client_id", 'left')
			->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
			->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjBooking', "t11.external_id=t1.external_id", 'left outer')
			->find($this->_post->toInt('id'))
			->getData();
			$arr['extra_arr'] = pjBookingExtraModel::factory()->select('t1.*, t2.content AS name, t3.domain, t3.image_path')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->join('pjExtra', 't3.id=t1.extra_id', 'left outer')
			->where('t1.booking_id', $arr['id'])
			->orderBy('t2.content ASC')
			->findAll()
			->getData();
			$this->set('arr', $arr);
			
			$pjWhatsappMessageModel = pjWhatsappMessageModel::factory();
			if ($this->isDriver()) {
			    $pjWhatsappMessageModel->whereIn('t1.available_for', array('driver','both'));
			} else {
			    $pjWhatsappMessageModel->whereIn('t1.available_for', array('admin','both'));
			}
			$cnt_whatsapp_message = $pjWhatsappMessageModel->where('t1.status', 'T')->findCount()->getData();
			$this->set('cnt_whatsapp_message', $cnt_whatsapp_message);
			
			$second_driver_arr = $ref_booking_arr = array();
			if ($arr['ref_id'] == "") {
			     $ref_booking_arr = pjBookingModel::factory()->reset()->where('t1.ref_id', $arr['id'])->limit(1)->findAll()->getDataIndex(0);
			} elseif ($arr['ref_id'] != "") {
			    $ref_booking_arr = pjBookingModel::factory()->reset()->where('t1.id', $arr['ref_id'])->limit(1)->findAll()->getDataIndex(0);
			}
			if ($ref_booking_arr) {
			    $second_driver_arr = pjDriverVehicleModel::factory()->select('t1.*, t2.name, t2.phone')
			    ->join('pjMainDriver', 't2.id=t1.driver_id', 'inner')
			    ->where('t1.vehicle_id', $ref_booking_arr['vehicle_id'])
			    ->where('t1.order', $ref_booking_arr['vehicle_order'])
			    ->where('t1.date', date('Y-m-d', strtotime($ref_booking_arr['booking_date'])))
			    ->limit(1)->findAll()->getDataIndex(0);
			}
			$this->set('second_driver_arr', $second_driver_arr);
        }
    }
    
    public function pjActionNameSign()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        $this->setLayout('pjActionEmpty');
        if ($this->_get->check('hash')) {
            $arr = pjBookingModel::factory()->select('t1.*, t2.title, t2.fname, t2.lname')
            ->join('pjClient', "t2.id=t1.client_id", 'left')
            ->where(sprintf("SHA1(CONCAT(t1.id, '%s')) = ", PJ_SALT), $this->_get->toString('hash'))
            ->limit(1)
            ->findAll()
            ->getDataIndex(0);
            if ($arr) {
                $provider_arr = pjProviderModel::factory()->where('t1.url', $arr['domain'])->limit(1)->findAll()->getDataIndex(0);
                $this->set('provider_arr', $provider_arr);
                $this->set('arr', $arr);
            } else {
                $this->set('status', 2);
            }
        } else {
            $this->set('status', 1);
        }
    }
    
    public function pjActionUpdatePaymentStatus() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('update_payment_status')) {
                $pjBookingModel = pjBookingModel::factory();
                $arr = $pjBookingModel->find($this->_post->toInt('id'))->getData();
                $data = array();
                if ($this->_post->toInt('payment_status') > 0) {
                    $data['driver_payment_status'] = $this->_post->toInt('payment_status');
                } else {
                    $data['driver_payment_status'] = ':NULL';
                }
                $data['is_enter_hale_cash_register'] = $this->_post->check('is_enter_hale_cash_register') ? $this->_post->toInt('is_enter_hale_cash_register') : 0; 
                $pjBookingModel->reset()->set('id', $this->_post->toInt('id'))->modify($data);
                
                $_driver_payment_status = __('_driver_payment_status', true);
                $data_log = array(
                    'booking_id' => $arr['id'],
                    'action' => "Booking [".$arr['uuid']."]: Changed payment from [".@$_driver_payment_status[$arr['driver_payment_status']]."] to [".@$_driver_payment_status[$data['driver_payment_status']]."]",
                    'user_id' => $this->getUserId(),
                    'booking_date' => date('Y-m-d', strtotime($arr['booking_date']))
                );
                pjLogModel::factory()->setAttributes($data_log)->insert();
                
                if (in_array($this->_post->toInt('payment_status'), array(3,4,5,6))) {
                    $arr = $pjBookingModel->reset()
                    ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address) AS dropoff,
					t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2, t5.c_address AS c_address2, t5.c_destination_address AS c_destination_address2, t5.c_hotel as c_hotel2,
					IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address) AS location2, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
					t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
					t6.title, t6.fname, t6.lname, t6.email, t6.phone, t10.content AS c_country_title, t11.price AS duplicate_price")
					->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjBooking', "t5.id=t1.return_id", 'left outer')
					->join('pjClient', "t6.id=t1.client_id", 'left')
					->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
					->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
					->join('pjBooking', "t11.external_id=t1.external_id", 'left outer')
					->find($this->_post->toInt('id'))
					->getData();
					$driver_arr = pjMainDriverModel::factory()->find($this->getUserId())->getData();
					
					$pjNotificationModel = pjNotificationModel::factory();
					$Email = self::getMailer($this->option_arr);
					//$admin_emails = pjAppController::getAllAdminEmails();
					
					$to_email = $this->option_arr['o_admin_change_payment_status_email'];
					$notification = $pjNotificationModel->reset()->where('recipient', 'admin')->where('transport', 'email')->where('variant', 'change_payment_status')->limit(1)->findAll()->getDataIndex(0);
					if((int) $notification['id'] > 0 && $notification['is_active'] == 1 && !empty($to_email))
					{
					    $driver_payment_status = sprintf(@$_driver_payment_status[$arr['driver_payment_status']], pjCurrency::formatPrice($arr['price'] + $arr['duplicate_price']));
					    $resp = pjAppController::pjActionGetSubjectMessage($notification, $this->getLocaleId(), $this->getForeignId());
					    $lang_message = $resp['lang_message'];
					    $lang_subject = $resp['lang_subject'];
					    if (count($lang_message) === 1 && count($lang_subject) === 1 && !empty($lang_subject[0]['content'])) {
					        $search = array('{DriverName}','{CustomerName}','{Date}','{PaymentStatus}','{ReferenceID}');
					        $replace = array(
					            $driver_arr['name'],
					            $arr['fname'].' '.$arr['lname'],
					            date($this->option_arr['o_date_format'], strtotime($arr['booking_date'])).', '.date($this->option_arr['o_time_format'], strtotime($arr['booking_date'])),
					            $driver_payment_status,
					            !empty($arr['uuid2']) ? $arr['uuid2'] : $arr['uuid']
					        );
					        $subject = str_replace($search, $replace, $lang_subject[0]['content']);
					        $message = str_replace($search, $replace, $lang_message[0]['content']);
					        if (!empty($subject) && !empty($message))
					        {
					            $message = pjUtil::textToHtml($message);
					            $Email
					            ->setTo($to_email)
					            ->setSubject($subject)
					            ->send($message);
					        }
					    }
					}
                }
            }
        }
        pjAppController::jsonResponse(array('status' => 'OK'));
    }
    
    public function pjActionUpdateBookingStatus() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('update_driver_status')) {
                $arr = pjBookingModel::factory()->find($this->_post->toInt('id'))->getData();
                pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('id'))->modify(array('driver_status' => $this->_post->toInt('driver_status')));
                
                $_booking_driver_statuses = __('_booking_driver_statuses', true);
                $data_log = array(
                    'booking_id' => $arr['id'],
                    'action' => "Booking [".$arr['uuid']."]: Changed booking status from [".@$_booking_driver_statuses[$arr['driver_status']]."] to [".@$_booking_driver_statuses[$this->_post->toInt('driver_status')]."]",
                    'user_id' => $this->getUserId(),
                    'booking_date' => date('Y-m-d', strtotime($arr['booking_date']))
                );
                pjLogModel::factory()->setAttributes($data_log)->insert();
            }
        }
        pjAppController::jsonResponse(array('status' => 'OK'));
    }
    
    public function pjActionTurnover()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (self::isGet() && $this->_get->check('vehicle_id') && $this->_get->toInt('vehicle_id') > 0)
        {
            $date = pjDateTime::formatDate($this->_get->toString('date'), $this->option_arr['o_date_format']);
            $arr = pjBookingModel::factory()->select('t1.*')
            ->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')")
            ->where('t1.vehicle_id', $this->_get->toInt('vehicle_id'))
            ->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `driver_id`='.$this->_get->toInt('driver_id').' AND `date`="'.$date.'")')
            ->where('t1.status !=', 'cancelled')
            ->whereNotIn('t1.driver_status', array(4,5))
            ->findAll()
            ->getData();
            $turnover_arr = array();
            $total_cash = $total_credit_card = $total_prepaid = 0;
            foreach ($arr as $val) {
                if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
                    $total_cash += $val['price'];
                } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
                    $total_credit_card += $val['price'];
                } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
                    $total_prepaid += $val['price'];
                } elseif ($val['payment_method'] == 'cash'){
                    $total_cash += $val['price'];
                } elseif ($val['payment_method'] == 'creditcard_later'){
                    $total_credit_card += $val['price'];
                } else {
                    $total_prepaid += $val['price'];
                }
            }
            $total = $total_cash + $total_credit_card + $total_prepaid;
            $this->set('total_cash', $total_cash)
            ->set('total_credit_card', $total_credit_card)
            ->set('total_prepaid', $total_prepaid)
            ->set('total', $total);
        } else {
            exit;
        }
    }
    
    public function pjActionRemoveDriverStatus() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
                pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('id'))->modify(array('driver_status' => ':NULL'));
            }
            pjAppController::jsonResponse(array('status' => 'OK'));
        }
    }
    
    public function pjActionCountOrders() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $post = $this->_post->raw();
            $pjBookingModel = pjBookingModel::factory()
            ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))');
            $date = date('Y-m-d');
            if (isset($post['date']) && !empty($post['date']))
            {
                $date = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
            }
            $pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')");
            if (!$this->isDriver()) {
                $pjBookingModel->where('t1.admin_confirm_cancelled', 0);
            } else {
                $pjBookingModel->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `driver_id`='.$this->getUserId().' AND `date`="'.$date.'" AND t1.vehicle_order=`order`)');
            }
            $cnt = $pjBookingModel->findCount()->getData();
            $this->set('cnt', $cnt);
        }
    }
    
    public function pjActionDriverAddNotes() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('id') && $this->_post->toInt('id') > 0) {
                pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('id'))->modify(array('notes_from_driver' => $this->_post->toString('notes')));
            }
            pjAppController::jsonResponse(array('status' => 'OK'));
        }
    }
    
    public function pjActionAddNotesForDriver()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if ($this->_post->check('add_notes_for_driver') && $this->_post->check('id') && $this->_post->toInt('id') > 0)
        {
            pjBookingModel::factory()->set('id', $this->_post->toInt('id'))->modify(array('notes_from_office' => $this->_post->toString('notes_from_office')));
            self::jsonResponse(array('status' => 'OK', 'text' => 'Notes for driver has been added!'));
        }
        
        if (self::isGet() && $this->_get->check('id') && $this->_get->toInt('id') > 0)
        {
            $arr = pjBookingModel::factory()->find($this->_get->toInt('id'))->getData();
            $this->set('arr', $arr);
        } else {
            exit;
        }
    }
    
    public function pjActionClosePopUp()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if ($this->_post->check('close_popup'))
        {
            if ($this->isDriver()) {
                pjDriverPopupModel::factory()->where('driver_id', $this->getUserId())->modifyAll(array('is_displayed' => 1));
            }
        }
        
        self::jsonResponse(array('status' => 'OK', 'text' => 'Popup closed'));
    }
    
    public function pjActionSyncAllDataManually()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        set_time_limit(0);
        $type = $this->_get->toString('type');
        $page = 1;
        if ($this->_get->check('page') && $this->_get->toInt('page') > 0) {
            $page = $this->_get->toInt('page');
        }
        $params = array(
            'type' => $type,
            'page' => $page
        );
        $resp = pjApiSync::pjActionPullAllData($params);
        $this->set('arr', $resp);
        $this->set('page', $page);
    }
    
    public function pjActionDeletePendingBookings()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        set_time_limit(0);
        
        $pjBookingModel = pjBookingModel::factory();
        $pjBookingExtraModel = pjBookingExtraModel::factory();
        $pjBookingPaymentModel = pjBookingPaymentModel::factory();
        
        $ids_arr = $pjBookingModel->whereIn('t1.status', array('pending','passed_on'))->where('t1.domain!=""')->findAll()->getDataPair(null, 'id');
        if ($ids_arr) {
            $pjBookingModel->reset()->whereIn('id', $ids_arr)->eraseAll();
            $pjBookingExtraModel->whereIn('booking_id', $ids_arr)->eraseAll();
            $pjBookingPaymentModel->whereIn('booking_id', $ids_arr)->eraseAll();
        }
        
        echo 'Pending bookings are deleted!';
        exit;
    }
    
    public function pjActionWhatsAppMessages() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $pjWhatsappMessageModel = pjWhatsappMessageModel::factory()
            ->join('pjMultiLang', "t2.model='pjWhatsappMessage' AND t2.foreign_id=t1.id AND t2.field='subject' AND t2.locale='".$this->getLocaleId()."'", 'left outer');
            if ($this->isDriver()) {
                $pjWhatsappMessageModel->whereIn('t1.available_for', array('driver','both'));
            } else {
                $pjWhatsappMessageModel->whereIn('t1.available_for', array('admin','both'));
            }
            $messages_arr = $pjWhatsappMessageModel->select('t1.*, t2.content AS `subject`')->orderBy('t2.content ASC')->findAll()->getData();
            $this->set('messages_arr', $messages_arr);
        }
    }
    
    public function pjActionGetWhatsAppMessage() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            $message = '';
            if ($this->_get->toInt('id') > 0) {
                $arr = pjBookingModel::factory()
                ->select("t1.*, t2.content as fleet, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address) AS dropoff,
				t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2, t5.c_address AS c_address2, t5.c_destination_address AS c_destination_address2, t5.c_hotel as c_hotel2,
				IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address) AS location2, IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
				t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
				t6.title, t6.fname, t6.lname, t6.email, t6.phone, t10.content AS c_country_title, t11.price AS duplicate_price, t12.name AS driver_name")
				->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjBooking', "t5.id=t1.return_id", 'left outer')
				->join('pjClient', "t6.id=t1.client_id", 'left')
				->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
				->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
				->join('pjBooking', "t11.external_id=t1.external_id", 'left outer')
				->join('pjMainDriver', "t12.id=t1.app_driver_id", 'left outer')
				->find($this->_get->toInt('booking_id'))
				->getData();
				
				$message_arr = pjWhatsappMessageModel::factory()
				->join('pjMultiLang', "t2.model='pjWhatsappMessage' AND t2.foreign_id=t1.id AND t2.field='message' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->select('t1.*, t2.content AS `message`')
				->find($this->_get->toInt('id'))
				->getData();
				if ($message_arr) {
				    $_driver_payment_status = __('_driver_payment_status', true);
				    $driver_payment_status = sprintf(@$_driver_payment_status[$arr['driver_payment_status']], pjCurrency::formatPrice($arr['price'] + $arr['duplicate_price']));
				    $url_tracking = $this->option_arr['o_customer_tracking_url'].'?hash='.sha1($arr['id'].PJ_SALT);
				    $search = array('{DriverName}','{CustomerName}','{Date}','{PaymentStatus}','{ReferenceID}','{URLTracking}');
				    $replace = array(
				        $arr['name'],
				        $arr['fname'].' '.$arr['lname'],
				        date($this->option_arr['o_date_format'], strtotime($arr['booking_date'])).', '.date($this->option_arr['o_time_format'], strtotime($arr['booking_date'])),
				        $driver_payment_status,
				        !empty($arr['uuid2']) ? $arr['uuid2'] : $arr['uuid'],
				        $url_tracking
				    );
				    
				    $message = str_replace($search, $replace, $message_arr['message']);
				}
            }
            $this->set('message', $message);
            $this->set('arr', $arr);
        }
    }
    
    public function pjActionChangePickupTime()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if ($this->_post->check('confirm_change') && $this->_post->check('id') && $this->_post->toInt('id') > 0)
        {
            $arr = pjBookingModel::factory()->find($this->_post->toInt('id'))->getData();
            $booking_date = date('Y-m-d', strtotime($arr['booking_date'])).' '.pjDateTime::formatTime($this->_post->toString('new_pickup_time'), $this->option_arr['o_time_format'], 'H:i:s');
            pjBookingModel::factory()->reset()->set('id', $this->_post->toInt('id'))->modify(array('prev_booking_date' => $arr['booking_date'], 'booking_date' => $booking_date));
            
            $data_log = array(
                'booking_id' => $arr['id'],
                'action' => "Booking [".$arr['uuid']."]: Changed Pick-up time from [".date('H:i', strtotime($arr['booking_date']))."] to [".date('H:i', strtotime($booking_date))."]",
                'user_id' => $this->getUserId(),
                'booking_date' => date('Y-m-d', strtotime($arr['booking_date']))
            );
            pjLogModel::factory()->setAttributes($data_log)->insert();
            
            self::jsonResponse(array('status' => 'OK', 'text' => 'Pick-up time has been updated!'));
        }
        
        if (self::isGet() && $this->_get->check('id') && $this->_get->toInt('id') > 0)
        {
            $arr = pjBookingModel::factory()->find($this->_get->toInt('id'))->getData();
            $this->set('arr', $arr);
        } else {
            exit;
        }
    }
    
    public function pjActionGetOrdersToAssign()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        $pjBookingModel = pjBookingModel::factory()
        ->join('pjMultiLang', "t2.model='pjFleet' AND t2.foreign_id=t1.fleet_id AND t2.field='fleet' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.location_id AND t3.field='pickup_location' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t4.model='pjDropoff' AND t4.foreign_id=t1.dropoff_id AND t4.field='location' AND t4.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjBooking', "t5.id=t1.return_id", 'left outer')
        ->join('pjClient', "t6.id=t1.client_id", 'left')
        ->join('pjMultiLang', "t7.model='pjAreaCoord' AND t7.foreign_id=t1.dropoff_place_id AND t7.field='place_name' AND t7.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjAreaCoord', "t8.id=t1.dropoff_place_id", 'left')
        ->join('pjMultiLang', "t9.model='pjArea' AND t9.foreign_id=t8.area_id AND t9.field='name' AND t9.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjMultiLang', "t10.model='pjBaseCountry' AND t10.foreign_id=t1.c_country AND t10.field='name' AND t10.locale='".$this->getLocaleId()."'", 'left outer')
        ->join('pjLocation', 't11.id=t1.location_id', 'left outer')
        ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))');
        //$pjBookingModel->where('t1.vehicle_id', 0);
        if (!$this->isDriver()) {
            $pjBookingModel->where('t1.admin_confirm_cancelled', 0);
        }
        if ($this->_get->check('q') && $this->_get->toString('q') != '')
        {
            $q = pjObject::escapeString($this->_get->toString('q'));
            $pjBookingModel->where("(t1.uuid LIKE '%$q%' OR t1.passengers LIKE '%$q%' OR t2.content LIKE '%$q%' OR t3.content LIKE '%$q%' OR t7.content LIKE '%$q%' OR t9.content LIKE '%$q%' OR t6.fname LIKE '%$q%' OR t6.lname LIKE '%$q%' OR t6.email LIKE '%$q%')");
        }
        if ($this->_get->check('date') && $this->_get->toString('date') != '')
        {
            $date = pjDateTime::formatDate($this->_get->toString('date'), $this->option_arr['o_date_format']);
            $pjBookingModel->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')");
        }
        
        $column = 'booking_date';
        $direction = 'ASC';
        if ($this->_get->toString('column') && in_array(strtoupper($this->_get->toString('direction')), array('ASC', 'DESC')))
        {
            $column = $this->_get->toString('column');
            $direction = strtoupper($this->_get->toString('direction'));
        }
        
        $total = $pjBookingModel->findCount()->getData();
        $rowCount = $this->_get->toInt('rowCount') ? $this->_get->toInt('rowCount') : 50;
        $pages = ceil($total / $rowCount);
        $page = $this->_get->toInt('page') ? $this->_get->toInt('page') : 1;
        $offset = ((int) $page - 1) * $rowCount;
        if ($page > $pages)
        {
            $page = $pages;
        }
        $data = $pjBookingModel
        ->select("t1.*, t2.content as fleet,
	    (IF (t1.return_id != '', (SELECT `uuid` FROM `".pjBookingModel::factory()->getTable()."` WHERE `id`=t1.return_id LIMIT 1), '')) AS return_uuid,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS location,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS dropoff,
		t5.uuid as uuid2, t5.dropoff_id as location_id2, t5.location_id AS dropoff_id2, t5.id as id2,
		IF(t1.platform='oldsystem', t4.content, IF(t1.dropoff_type='server', CONCAT(t9.content,' - ', t7.content), t1.dropoff_address)) AS location2,
		IF (t1.pickup_type='server', t3.content, t1.pickup_address) AS dropoff2,
		t1.duration as duration2, t1.pickup_is_airport as return_pickup_is_airport, t1.dropoff_is_airport as return_dropoff_is_airport,
		t6.title, t6.fname, t6.lname, t6.email,t6.phone, t10.content AS c_country_title, t11.color AS location_color")
		->orderBy("$column $direction")
		->limit($rowCount, $offset)
		->findAll()
		->getData();
		
		$booking_ids_arr = $booking_extras_arr = array();
		foreach ($data as $val) {
		    $booking_ids_arr[] = $val['id'];
		}
		if ($booking_ids_arr) {
		    $be_arr = pjBookingExtraModel::factory()->select('t1.*, t2.domain, t2.image_path, t3.content AS name')
		    ->join('pjExtra', 't2.id=t1.extra_id', 'left outer')
		    ->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t1.extra_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
		    ->whereIn('t1.booking_id', $booking_ids_arr)
		    ->orderBy('t3.content ASC')
		    ->findAll()
		    ->getData();
		    foreach ($be_arr as $val) {
		        $booking_extras_arr[$val['booking_id']][] = $val;
		    }
		}
		
		foreach ($data as $i => $order) {
		    $data[$i]['extra_arr'] = isset($booking_extras_arr[$order['id']]) ? $booking_extras_arr[$order['id']] : array();
		    $is_airport_to_city = false;
		    if ((int)$order['return_id'] > 0 && (int)$order['return_pickup_is_airport'] == 1 && (int)$order['return_dropoff_is_airport'] == 0) {
		        $is_airport_to_city = true;
		    } else if ((int)$order['pickup_is_airport'] == 1 && (int)$order['dropoff_is_airport'] == 0) {
		        $is_airport_to_city = true;
		    }
		    if(!empty($order['return_id'])) {
		        $data[$i]['from_to'] = pjSanitize::html($order['location2']).'<br/>'.pjSanitize::html($order['dropoff2']);
		        $data[$i]['order_id'] = pjSanitize::html($order['return_uuid']);
		    } else {
		        $data[$i]['from_to'] = pjSanitize::html($order['location']).'<br/>'.pjSanitize::html($order['dropoff']);
		        $data[$i]['order_id'] = pjSanitize::html($order['uuid']);
		    }
		    $data[$i]['client_name'] = pjSanitize::html($order['c_fname'].' '.$order['c_lname']);
		    $data[$i]['total'] = pjCurrency::formatPriceOnly($order['price']);
		    $data[$i]['transfer_time'] = date($this->option_arr['o_time_format'], strtotime($order['booking_date']));
		}
		
		self::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
    }
    
    public function pjActionAssignOrders() {
        $this->setAjax(true);
        if ($this->isXHR()) {
            if ($this->_post->check('assign_orders')) {
                if ($this->_post->toString('order_ids') != '') {
                    $order_ids = explode('-', $this->_post->toString('order_ids'));
                    if ($order_ids) {
                        pjBookingModel::factory()
                        ->whereIn('id', $order_ids)
                        ->modifyAll(array('vehicle_id' => $this->_post->toInt('vehicle_id'), 'vehicle_order' => $this->_post->toInt('vehicle_order')));
                        pjAppController::jsonResponse(array('status' => 'OK', 'text' => __('lblAssignOrdersSuccess', true)));
                    }
                }
            }
            pjAppController::jsonResponse(array('status' => 'ERR', 'text' => ''));
        }
    }
    
    
    /** Tính khoảng cách đường chim bay (km) - Dùng cho lọc sơ bộ */
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
    
    /** * Lấy thời gian di chuyển thực tế (bằng giây) qua Google Maps API (có Cache).
     * Đây là hàm gọi API thực tế.
     */
    protected function getActualTravelTime($lat1, $lon1, $lat2, $lon2) {
        
        // 1. Lọc sơ bộ bằng Haversine
        $distanceKm = $this->haversineGreatCircleDistance($lat1, $lon1, $lat2, $lon2);
        
        // Nếu khoảng cách quá xa (ví dụ > 100km), trả về ước tính tối thiểu và thoát
        if ($distanceKm > $this->max_distance_km) {
            // Giả sử tốc độ trung bình 36km/h (100s/km)
            return round($distanceKm * 100);
        }
        
        // 2. Kiểm tra Cache
        $hashKey = hash('sha256', "{$lat1},{$lon1},{$lat2},{$lon2}");
        $cached = pjApiCacheDistanceModel::factory()->reset()->where('t1.hash_key', $hashKey)->limit(1)->findAll()->getDataIndex(0);
        if ($cached) {
            return (int)$cached['duration_sec']; // Đã tìm thấy trong cache!
        }
        
        // 3. Gọi API (Nếu không có trong cache)
        $origin = "{$lat1},{$lon1}";
        $destination = "{$lat2},{$lon2}";
        
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?" .
            "origins=" . urlencode($origin) .
            "&destinations=" . urlencode($destination) .
            "&key=" . $this->option_arr['o_google_api_key'] .
            "&mode=driving" .
            "&departure_time=now"; // Tính toán giao thông hiện tại
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Tối đa 5 giây chờ
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Xử lý lỗi API
        if ($http_code !== 200 || $response === false) {
            // Trả về ước tính dựa trên khoảng cách nếu API lỗi
            return round($distanceKm * 120);
        }
        
        $data = json_decode($response, true);
        
        // Kiểm tra kết quả hợp lệ
        if (isset($data['rows'][0]['elements'][0]['duration']['value']) &&
            $data['rows'][0]['elements'][0]['status'] === 'OK')
        {
            $actualDuration = (int)$data['rows'][0]['elements'][0]['duration']['value'];
        } else {
            // Nếu API trả về STATUS khác OK (ZERO_RESULTS, v.v.)
            return round($distanceKm * 120);
        }
        
        // 4. Lưu vào Cache
        pjApiCacheDistanceModel::factory()->reset()->setAttributes(array('hash_key' => $hashKey, 'duration_sec' => $actualDuration))->insert();
        
        return $actualDuration;
    }
    
    // =================================================================
    // 3. HÀM LOGIC XỬ LÝ CHI PHÍ (COST CALCULATION)
    // =================================================================
    
    /** Tính chi phí (Thời gian chờ - T_wait) để CHÈN booking MỚI VÀO CUỐI CHUỖI. */
    protected function calculateTripCostToEnd($lastTrip, $newBooking) {
        $isStartingFromBase = ($lastTrip === null);
        
        if ($isStartingFromBase) {
            $startLat = $this->vehicle_base_lat; 
            $startLng = $this->vehicle_base_lng;
            $vehicleReadyTimestamp = 0;
        } else {
            $startLat = $lastTrip['dropoff_lat']; $startLng = $lastTrip['dropoff_lng'];
            $vehicleReadyTimestamp = strtotime($lastTrip['booking_date']) + ($lastTrip['duration'] * 60) + $this->buffer_time_seconds;
        }
        
        $travelTimeSeconds = $this->getActualTravelTime($startLat, $startLng, $newBooking['pickup_lat'], $newBooking['pickup_lng']);
        if ($travelTimeSeconds === null) return null;
        
        $driverArrivalTimeTimestamp = $vehicleReadyTimestamp + $travelTimeSeconds;
        $newPickupTimestamp = strtotime($newBooking['booking_date']);
        $waitTimeSeconds = $newPickupTimestamp - $driverArrivalTimeTimestamp;
        
        // Kiểm tra Khả thi (Feasibility Check)
        if ($waitTimeSeconds < 0) { // Đến muộn (Luôn loại bỏ)
            return PHP_INT_MAX;
        }
        
        // Kiểm tra Giới hạn Tối đa (Max Wait Time Check)
        if (!$isStartingFromBase && $waitTimeSeconds > $this->max_wait_time_seconds) {
            return PHP_INT_MAX;
        }
        
        return $waitTimeSeconds;
    }
    
    /** Tính chi phí chèn (T_wait) của Booking B vào giữa A và C. */
    protected function calculateInsertionCost($tripA, $tripC, $tripB) {
        $ATimeEnd = strtotime($tripA['booking_date']) + ($tripA['duration'] * 60);
        $CTimeStart = strtotime($tripC['booking_date']);
        
        // Lọc khoảng trống nhỏ hơn ngưỡng tối thiểu
        $gapTime = $CTimeStart - $ATimeEnd;
        if ($gapTime < $this->min_gap_fill_seconds) {
            return PHP_INT_MAX;
        }
        
        $travelTimeA_B = $this->getActualTravelTime($tripA['dropoff_lat'], $tripA['dropoff_lng'], $tripB['pickup_lat'], $tripB['pickup_lng']);
        $travelTimeB_C = $this->getActualTravelTime($tripB['dropoff_lat'], $tripB['dropoff_lng'], $tripC['pickup_lat'], $tripC['pickup_lng']);
        $tripBDuration = $tripB['duration'] * 60;
        
        if ($travelTimeA_B === null || $travelTimeB_C === null) return null;
        
        // Tổng thời gian cần thiết (bao gồm 2 buffer)
        $totalTimeNeeded = $travelTimeA_B + $this->buffer_time_seconds + $tripBDuration + $this->buffer_time_seconds + $travelTimeB_C;
        
        // Kiểm tra tính khả thi
        if ($totalTimeNeeded > $gapTime) {
            return PHP_INT_MAX;
        }
        
        // Tính T_wait (A -> B)
        $vehicleLeaveTime = $ATimeEnd + $this->buffer_time_seconds;
        $driverArrivalTimeB = $vehicleLeaveTime + $travelTimeA_B;
        $waitTimeA_B = strtotime($tripB['booking_date']) - $driverArrivalTimeB;
        
        // Kiểm tra T_wait (A -> B)
        if ($waitTimeA_B < 0 || $waitTimeA_B > $this->max_wait_time_seconds) {
            return PHP_INT_MAX;
        }
        
        return $waitTimeA_B;
    }
    
    
    // =================================================================
    // 4. HÀM TRUY VẤN VÀ GÁN XE CHÍNH (VỚI VÒNG LẶP PHỤ)
    // =================================================================
    
    protected function getDataForDay($date) {
        // Lấy tất cả bookings ngày hôm nay
        $allBookings = pjBookingModel::factory()->where('DATE(t1.booking_date)', $date)->orderBy('t1.booking_date ASC')->findAll()->getData();
        
        // Lấy tất cả xe
        $vehicles = pjVehicleModel::factory()->select('t1.*, t2.content AS name')
        ->join('pjMultiLang', "t2.model='pjVehicle' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
        ->where('t1.seats <=', 8)
        ->where('t1.status', 'T')
        ->where('t1.type', 'own')
        ->orderBy('t1.seats ASC, t1.order ASC, t2.content ASC')
        ->findAll()
        ->getData();
        
        // Lấy chuyến cuối cùng của mỗi xe TỪ HÔM TRƯỚC (vị trí khởi điểm)
        $vehicleLastTrips = [];
        if ($vehicles) {
            $vehicle_ids_arr = array();
            foreach ($vehicles as $vehicle) {
                $vehicle_ids_arr[] = $vehicle['id'];
            }
            $vehicle_booking_last_trip_arr = pjBookingModel::factory()->reset()
            ->whereIn('t1.vehicle_id', $vehicle_ids_arr)
            ->where('t1.booking_date', $date)
            ->orderBy('t1.booking_date DESC')
            ->findAll()->getData();
            foreach ($vehicle_booking_last_trip_arr as $vb) {
                $vehicleLastTrips[$vb['vehicle_id']][] = $vb;
            }
        }
        
        return [
            'all_bookings' => $allBookings,
            'vehicles' => $vehicles,
            'vehicle_initial_trip' => $vehicleLastTrips
        ];
    }
    
    
    public function pjActionCaptcha()
    {
        $this->setAjax(true);
        
        header("Cache-Control: max-age=3600, private");
        $rand = $this->_get->toString('rand') ? $this->_get->toString('rand') : null;
        $patterns = null;
        if(!empty($this->option_arr['o_captcha_background']) && $this->option_arr['o_captcha_background'] != 'plain')
        {
            $patterns = PJ_INSTALL_PATH . $this->getConst('PLUGIN_IMG_PATH') . 'captcha_patterns/' . $this->option_arr['o_captcha_background'];
        }
        $Captcha = new pjCaptcha(PJ_INSTALL_PATH . $this->getConst('PLUGIN_WEB_PATH') . 'obj/arialbd.ttf', $this->defaultCaptcha, (int) $this->option_arr['o_captcha_length']);
        $Captcha->setImage($patterns)->setMode($this->option_arr['o_captcha_mode'])->init($rand);
        exit;
    }
    
    public function pjActionCheckCaptcha()
    {
        $this->setAjax(true);
        
        if (!$this->_post->toString('ai_process_captcha') || !pjCaptcha::validate($this->_post->toString('ai_process_captcha'), $this->session->getData($this->defaultCaptcha))){
            echo 'ERR';
        }else{
            echo 'OK';
        }
        exit;
    }
    
    public function getVehiclesFromAPI() {
        // Cấu hình
        $api_token = $this->option_arr['o_infleet_api_token']; // Thay thế bằng API Token thực của bạn
        // === ĐÃ THAY ĐỔI ENDPOINT TẠI ĐÂY ===
        $api_url = 'https://api.bornemann.net/data/assets/all';
        //$api_url = 'https://api.bornemann.net/data/hardware/find?make=Mercedes';
        
        // Thiết lập Headers cho API Call
        $headers = [
            'Authorization: Bearer ' . $api_token,
            'Accept: application/json'
        ];
        
        // Khởi tạo cURL
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Thực thi API Call
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Kiểm tra lỗi cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            /* http_response_code(500);
            die(json_encode(['error' => 'cURL error: ' . $error_msg])); */
            return array();
        }
        
        curl_close($ch);
        
        // Kiểm tra HTTP Status Code
        if ($http_code != 200) {
            /* http_response_code($http_code);
            die(json_encode(['error' => 'API returned non-200 status code: ' . $http_code, 'response' => $response])); */
            return array();
        }
        
        // Chuyển đổi JSON response thành mảng PHP
        $arr = json_decode($response, true);
        $data = array();
        foreach ($arr as $val) {
            $is_valid = true;
            if (isset($val['child']) && $val['child']['type'] == 'vehicle' && !empty($val['child']['licensePlate'])) {
                $data[$val['child']['licensePlate']] = $val;
            }
        }
        
        return $data;
    }
    
    public function getVehicleFromAPI() {
        // Cấu hình
        $vehicle_id = $this->_get->toString('vehicle_id');
        $api_token = $this->option_arr['o_infleet_api_token']; // Thay thế bằng API Token thực của bạn
        $api_url = 'https://api.bornemann.net/data/assets/'.$vehicle_id;
        
        // Thiết lập Headers cho API Call
        $headers = [
            'Authorization: Bearer ' . $api_token,
            'Accept: application/json'
        ];
        
        // Khởi tạo cURL
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Thực thi API Call
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Kiểm tra lỗi cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            http_response_code(500);
            echo json_encode(['error' => 'cURL error: ' . $error_msg]);
            exit;
        }
        
        curl_close($ch);
        
        // Kiểm tra HTTP Status Code
        if ($http_code != 200) {
            http_response_code($http_code);
            echo json_encode(['error' => 'API returned non-200 status code: ' . $http_code, 'response' => $response]);
            exit;
        }
        
        // Chuyển đổi JSON response thành mảng PHP
        $data = json_decode($response, true);
        
        // Định dạng lại dữ liệu và gửi về Frontend (JavaScript)
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public function pjActionCheckVehiclesStatus() {
        $resp = $this->getVehiclesFromAPI();
        $data = array();
        foreach ($resp as $val) {
            $isMoving = isset($val['logLast']['isMoving']) ? (int)$val['logLast']['isMoving'] : '';
            $speed = isset($val['logLast']['speed']) ? (int)$val['logLast']['speed'] : 0;
            if ($isMoving || $speed > 0) {
                $isMoving = 1;
            }
            $data[] = array(
                'id' => $val['_id'],
                'isMoving' => $isMoving
            );
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public function getFlightNumbers() {
        $this->setAjax(true);
        
        $date = date('Y-m-d');
        if ($this->_get->check('date') && $this->_get->toString('date') != '')
        {
            $date = pjDateTime::formatDate($this->_get->toString('date'), $this->option_arr['o_date_format']);
        }
        $pjBookingModel = pjBookingModel::factory()
        ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))')
        ->where('t1.pickup_is_airport', 1)
        ->where('t1.c_flight_number!=""')
        ->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')")
        ->where('t1.booking_date >= NOW()');
        $booking_arr = $pjBookingModel->orderBy('t1.c_flight_time ASC, t1.booking_date ASC')->findAll()->getData();
        $listFlights = array();
        $tmp = array();
        foreach ($booking_arr as $booking) {
            $c_flight_number = preg_replace('/\s+/', '', $booking['c_flight_number']);
            if (!empty($c_flight_number)) {
                if (!in_array(strtoupper($c_flight_number), $tmp)) {
                    $listFlights[] = $c_flight_number;
                }
                $tmp[] = strtoupper($c_flight_number);
            }
        }
        $listFlights = array_unique($listFlights);
        //$listFlights = ['Ba686', 'HV6604', 'HV6833', 'HV6681', 'TB2063'];
        pjAppController::jsonResponse($listFlights);
    }
    
    public function pjActionCheckFlights() {
        $this->setAjax(true);
        
        $date = date('Y-m-d');
        if ($this->_get->check('date') && $this->_get->toString('date') != '')
        {
            $date = pjDateTime::formatDate($this->_get->toString('date'), $this->option_arr['o_date_format']);
        }
        $listFlights = array();
        $cnt = 0;
        if ($this->_get->check('flight_number') && $this->_get->toString('flight_number') != '') {
            $listFlights[] = $this->_get->toString('flight_number');
            $cnt = pjBookingModel::factory()
            ->where('t1.c_flight_number', $this->_get->toString('flight_number'))
            ->where("(DATE_FORMAT(t1.booking_date, '%Y-%m-%d')='$date')")
            ->findCount()->getData();
        }
        $arr = pjAppController::getMultipleFlights($listFlights, $date, $this->option_arr);
        $this->set('arr', $arr);
        $this->set('cnt', $cnt);
    }
    
    public function pjActionSendWhatsapp()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if ($this->_post->check('send_whatsapp'))
        {
            $post = $this->_post->raw();
            $message = $post['whatsapp_message'];
            $provider_id = $post['provider_id'];
            $driver_id = $post['driver_id'];
            //$provider_id = 4;
            $provider_arr = pjProviderModel::factory()->find($provider_id)->getData();
            $driver_arr = pjMainDriverModel::factory()->find($driver_id)->getData();
            
            $date = isset($post['date']) ? $post['date'] : date($this->option_arr['o_date_format']);
            $driver_name = @$driver_arr['name'];
            
            $accessToken = $provider_arr['whatsapp_permanent_access_token'];
            $phoneNumberId = $provider_arr['whatsapp_phone_number_id'];
            $arr = pjMainDriverModel::factory()->find($post['driver_id'])->getData();
            if ($arr) {
                $url = "https://graph.facebook.com/v18.0/$phoneNumberId/messages";
                $driver_id = $arr['id'];
                $driver_phone = $arr['phone'];
                $driver_phone = ltrim($driver_phone, '0+');
                if (!empty($post['whatsapp_template'])) {
                    list($name, $lang) = explode('~:~', $post['whatsapp_template']);
                    $data_replace = [
                        'driver_name' => $driver_name,
                        'date'     => $date
                    ];
                    $mappedParams = pjAppController::getWhatsappTemplateParameters($name, $data_replace);
                    $data = [
                        "messaging_product" => "whatsapp",
                        "to" => $driver_phone,
                        "type" => "template",
                        "template" => [
                            "name" => $name,
                            "language" => [ "code" => $lang ],
                            "components" => [
                                [
                                    "type" => "body",
                                    "parameters" => $mappedParams
                                ]
                            ]
                        ]
                    ];
                    
                    $search = array('{{driver_name}}', '{{drivername}}', '{{date}}');
                    $replace = array($driver_name, $driver_name, $date);
                    $message = str_replace($search, $replace, $message);
                } else {
                    $data = [
                        "messaging_product" => "whatsapp",
                        "to" => $driver_phone,
                        "type" => "text",
                        "text" => ["body" => $message]
                    ];
                }
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer $accessToken",
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $res = curl_exec($ch);
                curl_close($ch);
                $res = json_decode($res, true);
                if (isset($res['messages'][0]['id'])) {
                    $data_insert = array(
                        'wa_message_id' => $res['messages'][0]['id'],
                        'provider_id' => $provider_id,
                        'driver_phone' => $driver_phone,
                        'direction' => 'sent',
                        'content' => $message
                    );
                    pjWhatsappChatHistoryModel::factory()->reset()->setAttributes($data_insert)->insert();
                    // success
                } else {
                    // failed
                }
            }
            pjAppController::jsonResponse(array('status' => 'OK', 'text' => __('dash_message_sent_successfully', true)));
        }
        
        if (self::isGet() && $this->_get->check('driver_id') && $this->_get->toInt('driver_id') > 0 && $this->_get->check('vehicle_id') && $this->_get->toInt('vehicle_id') > 0)
        {
            $driver_arr = pjAuthUserModel::factory()->find($this->_get->toInt('driver_id'))->getData();
            $this->set('driver_arr', $driver_arr);
            
            $provider_arr = pjProviderModel::factory()->where('t1.status', 'T')->orderBy('t1.whatsapp_name ASC')->findAll()->getData();
            $this->set('provider_arr', $provider_arr);
        } else {
            exit;
        }
    }
    
    public function pjActionAddNotesForVehicle()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if ($this->_post->check('add_note'))
        {
            $post = $this->_post->raw();
            $data = array();
            $data['status'] = 'T';
            if (isset($post['date']) && !empty($post['date']))
            {
                $data['date'] = pjDateTime::formatDate($post['date'], $this->option_arr['o_date_format']);
            }
            $note_id = pjNoteModel::factory(array_merge($post, $data))->insert()->getInsertId();
            if ($note_id !== false && (int) $note_id > 0)
            {
                self::jsonResponse(array('status' => 'OK', 'text' => nl2br($post['notes'])));
            }
            pjAppController::jsonResponse(array('status' => 'ERR'));
        }
        pjAppController::jsonResponse(array('status' => 'ERR'));
    }
    
    public function pjActionGetTemplates() {
        $this->setAjax(true);
        $provider_id = $this->_get->toInt('provider_id');
        //$provider_id = 4;
        $provider_arr = pjProviderModel::factory()->find($provider_id)->getData();
        $token = $provider_arr['whatsapp_permanent_access_token'];
        $wabaId = $this->option_arr['o_whatsapp_business_account_id'];
        
        $url = "https://graph.facebook.com/v18.0/$wabaId/message_templates?limit=100";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        
        $templates = [];
        if (isset($data['data'])) {
            foreach ($data['data'] as $tpl) {
                if ($tpl['status'] === 'APPROVED') {
                    $bodyText = "";
                    // Meta trả về components là một mảng, phải tìm đúng type = BODY
                    foreach ($tpl['components'] as $component) {
                        if ($component['type'] === 'BODY') {
                            $bodyText = $component['text'];
                            break;
                        }
                    }
                    $templates[] = [
                        'name' => $tpl['name'],
                        'value' => $tpl['name'].'~:~'.$tpl['language'],
                        'language' => $tpl['language'],
                        'body' => $bodyText
                    ];
                }
            }
        }
        pjAppController::jsonResponse($templates);
    }
    
    public function pjActionUpdateBookingDateInLog(){
        $arr = pjLogModel::factory()->findAll()->getData();
        foreach ($arr as $val) {
            pjLogModel::factory()->reset()->set('id', $val['id'])->modify(array('booking_date' => date('Y-m-d', strtotime($val['created']))));
        }
        echo "updated!!!";
        exit;
    }
    
    public function pjActionChat(){
        
    }
    
    public function testBookings(){
        $arr = pjBookingModel::factory()->whereIn('t1.status', array('in_progress','pending'))->findAll()->getData();
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
        exxit;
    }
}
?>