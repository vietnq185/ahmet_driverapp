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
	    $pjBookingModel->where(sprintf("(DATE(t1.booking_date) BETWEEN '%1\$s' AND '%2\$s')", $date_from, $date_to));
	    if($driver_id > 0)
	    {
	        $pjBookingModel->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `driver_id`='.$driver_id.' AND `date` BETWEEN "'.$date_from.'" AND "'.$date_to.'")');
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
}
?>