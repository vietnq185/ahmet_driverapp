<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminSchedule extends pjAdmin
{	
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
		
		if ($this->isDriver()) {
		    $popup_message = pjDriverPopupModel::factory()->where('t1.driver_id', $this->getUserId())->where('t1.is_displayed', 0)->findAll()->getDataPair(null, 'message');
		    $this->set('popup_message', $popup_message);
		}
		
		$this->set('date', date('Y-m-d'));
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
			
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendCss('clockpicker.css', PJ_THIRD_PARTY_PATH . 'clockpicker/');
		$this->appendJs('clockpicker.js');
		
		$this->appendJs('jquery-sortable.js');
		$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/')
			->appendJs('pjAdminSchedule.js');
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
			$pjBookingModel->where("(t1.uuid LIKE '%$q%' OR t1.passengers LIKE '%$q%' OR t2.content LIKE '%$q%' OR t3.content LIKE '%$q%' OR t7.content LIKE '%$q%' OR t9.content LIKE '%$q%' OR t6.fname LIKE '%$q%' OR t6.lname LIKE '%$q%' OR t6.email LIKE '%$q%')");
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
					t6.title, t6.fname, t6.lname, t6.email,t6.phone, t10.content AS c_country_title, t11.color AS location_color")
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
		
		return array(
			'booking_arr' => $booking_arr,
			'vehicle_arr' => $vehicle_arr,
			'driver_arr' => $driver_arr,
			'driver_vehicle_arr' => $driver_vehicle_arr,
		    'assigned_driver_name_arr' => $assigned_driver_name_arr,
			'date' => $date,
			'assigned_driver_arr' => $assigned_driver_arr
		);
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
			    ->set('assigned_driver_name_arr', $schedule_arr['assigned_driver_name_arr']);
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
						pjBookingModel::factory()->set('id', $this->_post->toInt('booking_id'))->modify(array('vehicle_id' => $this->_post->toInt('vehicle_id'), 'vehicle_order' => $this->_post->toInt('vehicle_order')));
						pjAppController::jsonResponse(array('status' => 'OK'));		
						break;
					case 'assign_driver':
						$pjDriverVehicleModel = pjDriverVehicleModel::factory();
						$date = pjDateTime::formatDate($this->_post->toString('date'), $this->option_arr['o_date_format']);
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
						} else {
							pjBookingModel::factory()->where('vehicle_id', $this->_post->toInt('vehicle_id'))
								->where('DATE(booking_date)', $date)
								->where('vehicle_order', $this->_post->toInt('order'))
								->modifyAll(array('app_driver_id' => 0));
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
						if (!$this->isDriver()) {
							pjBookingModel::factory()->set('id', $this->_post->toInt('booking_id'))->modify(array('admin_confirm_cancelled' => 1));
						} else {
							pjBookingModel::factory()->set('id', $this->_post->toInt('booking_id'))->erase();
							pjBookingExtraModel::factory()->where('booking_id', $this->_post->toInt('booking_id'))->eraseAll();
							pjBookingPaymentModel::factory()->where('booking_id', $this->_post->toInt('booking_id'))->eraseAll();
						}
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
		pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminSchedule&action=pjActionIndex");
	}
	
	private function getDriverOrders($post=array()) {
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
				$data = array();
				if ($this->_post->toInt('payment_status') > 0) {
				    $data['driver_payment_status'] = $this->_post->toInt('payment_status');
				} else {
				    $data['driver_payment_status'] = ':NULL';
				}
				$pjBookingModel->set('id', $this->_post->toInt('id'))->modify($data);
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
			        $admin_emails = pjAppController::getAllAdminEmails();
			        
					$notification = $pjNotificationModel->reset()->where('recipient', 'admin')->where('transport', 'email')->where('variant', 'change_payment_status')->limit(1)->findAll()->getDataIndex(0);
			        if((int) $notification['id'] > 0 && $notification['is_active'] == 1)
			        {
			        	$_driver_payment_status = __('_driver_payment_status', true);
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
								foreach($admin_emails as $email)
								{
									$Email
										->setTo($email)
										->setSubject($subject)
										->send($message);
								}
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
				pjBookingModel::factory()->set('id', $this->_post->toInt('id'))->modify(array('driver_status' => $this->_post->toInt('driver_status')));
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
	                
	                $search = array('{DriverName}','{CustomerName}','{Date}','{PaymentStatus}','{ReferenceID}');
	                $replace = array(
	                    $arr['name'],
	                    $arr['fname'].' '.$arr['lname'],
	                    date($this->option_arr['o_date_format'], strtotime($arr['booking_date'])).', '.date($this->option_arr['o_time_format'], strtotime($arr['booking_date'])),
	                    $driver_payment_status,
	                    !empty($arr['uuid2']) ? $arr['uuid2'] : $arr['uuid']
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
}
?>