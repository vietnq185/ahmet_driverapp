<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminPartnerReport extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$partner_arr = pjPartnerModel::factory()
		->orderBy('t1.name ASC')
		->findAll()
		->getData();
		$this->set('partner_arr', $partner_arr);
		
		$this->appendCss('css/select2.min.css', PJ_THIRD_PARTY_PATH . 'select2/');
		$this->appendJs('js/select2.full.min.js', PJ_THIRD_PARTY_PATH . 'select2/');
		
		$this->appendCss('datepicker.css', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendJs('bootstrap-datepicker.js', PJ_THIRD_PARTY_PATH . 'bootstrap_datepicker/');
		$this->appendCss('pjAdminPartners.css');
		$this->appendJs('pjAdminPartnerReport.js');
	}
	
	public function pjActionGetReport() {
	    $this->setAjax(true);
	    $pjPartnerReportModel = pjPartnerReportModel::factory()
	       ->join('pjPartner', 't2.id=t1.partner_id', 'inner');
	    
       $date_from = date('Y-m-d');
       $date_to = date('Y-m-d');
	    if ($this->_post->toString('from_date') != '')
	    {
	        $date_from = pjDateTime::formatDate($this->_post->toString('from_date'), $this->option_arr['o_date_format']);
	    }
	    if ($this->_post->toString('to_date') != '')
	    {
	        $date_to = pjDateTime::formatDate($this->_post->toString('to_date'), $this->option_arr['o_date_format']);
	    }
	    
	    $partner_ids = array();
	    if ($this->_post->check('partner_id') && $this->_post->toInt('partner_id') > 0) {
	        $partner_id = $this->_post->toInt('partner_id');
	        $partner_ids[] = $partner_id;
	        $partner = pjPartnerModel::factory()->find($partner_id)->getData();
	        $partner_arr[$partner_id] = $partner;
	    } else {
	        $partner_ids = pjPartnerModel::factory()->findAll()->getDataPair(null, 'id');
	        $partner_arr = pjPartnerModel::factory()->findAll()->getDataPair('id', null);
	    }
	    
	    $total_bookings = $total_amount = $total_paid = $total_cc = $total_cash = $total_comm = 0;
	    if ($partner_ids) {
	        $tblPartnerVehicle = pjPartnerVehicleModel::factory()->getTable();
	        $pjPartnerReportBookingAmountModel = pjPartnerReportBookingAmountModel::factory();
	        
	        foreach ($partner_arr as $partner_id => $partner) { 
    	        $pjBookingModel = pjBookingModel::factory()->reset()
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
        	        ->where('t1.location_id!="" AND ((t1.dropoff_type="server" AND t1.dropoff_id!="") OR (t1.dropoff_type="google" AND t1.dropoff_place_id!=""))')
        	        ->where('t1.admin_confirm_cancelled', 0)
        	        ->where('t1.vehicle_id IN (SELECT `vehicle_id` FROM `'.$tblPartnerVehicle.'` WHERE `partner_id`='.$partner_id.')')
        	        ->where('DATE(t1.booking_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
        	        ->whereNotIn('t1.driver_status', array(4,5));
        	        
    	        $report_arr = $pjBookingModel
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

            		$custom_amount_arr = $pjPartnerReportBookingAmountModel->reset()
            		->where('t1.partner_id', $partner_id)
            		->findAll()->getDataPair('booking_id', null);
            		
    		      if (!isset($partner_arr[$partner_id]['total_bookings'])) {
    		          $partner_arr[$partner_id]['total_bookings'] = 0;
    		      }
    		      if (!isset($partner_arr[$partner_id]['total_amount'])) {
    		          $partner_arr[$partner_id]['total_amount'] = 0;
    		      }
    		      if (!isset($partner_arr[$partner_id]['total_paid'])) {
    		          $partner_arr[$partner_id]['total_paid'] = 0;
    		      }
    		      if (!isset($partner_arr[$partner_id]['total_cc'])) {
    		          $partner_arr[$partner_id]['total_cc'] = 0;
    		      }
    		      if (!isset($partner_arr[$partner_id]['total_cash'])) {
    		          $partner_arr[$partner_id]['total_cash'] = 0;
    		      }
    		      if (!isset($partner_arr[$partner_id]['total_comm'])) {
    		          $partner_arr[$partner_id]['total_comm'] = 0;
    		      }
    		      foreach ($report_arr as $val) {
    		          $total_bookings++;
    		          $price = $val['price'];
    		          
    		          $partner_arr[$partner_id]['total_bookings'] += 1;
    		          
    		          if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
    		              if (isset($custom_amount_arr[$val['id']]['total_cash']) && (float)$custom_amount_arr[$val['id']]['total_cash'] > 0) {
    		                  $price = $custom_amount_arr[$val['id']]['total_cash'];
    		                  $total_cash += $price;
    		                  $partner_arr[$partner_id]['total_cash'] += $price;
    		              } else {
    		                  $total_cash += $val['price'];
    		                  $partner_arr[$partner_id]['total_cash'] += $val['price'];
    		              }
    		          } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
    		              if (isset($custom_amount_arr[$val['id']]['total_cc']) && (float)$custom_amount_arr[$val['id']]['total_cc'] > 0) {
    		                  $price = $custom_amount_arr[$val['id']]['total_cc'];
    		                  $total_cc += $price;
    		                  $partner_arr[$partner_id]['total_cc'] += $price;
    		              } else {
    		                  $total_cc += $val['price'];
    		                  $partner_arr[$partner_id]['total_cc'] += $val['price'];
    		              }
    		          } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
    		              if (isset($custom_amount_arr[$val['id']]['total_paid']) && (float)$custom_amount_arr[$val['id']]['total_paid'] > 0) {
    		                  $price = $custom_amount_arr[$val['id']]['total_paid'];
    		                  $total_paid += $price;
    		                  $partner_arr[$partner_id]['total_paid'] += $price;
    		              } else {
    		                  $total_paid += $val['price'];
    		                  $partner_arr[$partner_id]['total_paid'] += $val['price'];
    		              }
    		          } elseif ($val['payment_method'] == 'cash'){
    		              if (isset($custom_amount_arr[$val['id']]['total_cash']) && (float)$custom_amount_arr[$val['id']]['total_cash'] > 0) {
    		                  $price = $custom_amount_arr[$val['id']]['total_cash'];
    		                  $total_cash += $price;
    		                  $partner_arr[$partner_id]['total_cash'] += $price;
    		              } else {
    		                  $total_cash += $val['price'];
    		                  $partner_arr[$partner_id]['total_cash'] += $val['price'];
    		              }
    		          } elseif ($val['payment_method'] == 'creditcard_later'){
    		              if (isset($custom_amount_arr[$val['id']]['total_cc']) && (float)$custom_amount_arr[$val['id']]['total_cc'] > 0) {
    		                  $price = $custom_amount_arr[$val['id']]['total_cc'];
    		                  $total_cc += $price;
    		                  $partner_arr[$partner_id]['total_cc'] += $price;
    		              } else {
    		                  $total_cc += $val['price'];
    		                  $partner_arr[$partner_id]['total_cc'] += $val['price'];
    		              }
    		          } else {
    		              if (isset($custom_amount_arr[$val['id']]['total_paid']) && (float)$custom_amount_arr[$val['id']]['total_paid'] > 0) {
    		                  $price = $custom_amount_arr[$val['id']]['total_paid'];
    		                  $total_paid += $price;
    		                  $partner_arr[$partner_id]['total_paid'] += $price;
    		              } else {
    		                  $total_paid += $val['price'];
    		                  $partner_arr[$partner_id]['total_paid'] += $val['price'];
    		              }
    		          }
    		          
    		          $total_amount += $price;
    		          $partner_arr[$partner_id]['total_amount'] += $price;
    		      }
    		      
    		      $commission_pct = (float)$partner['commission_pct'];
    		      $commission = ($partner_arr[$partner_id]['total_amount']*$commission_pct)/100;
    		      $partner_arr[$partner_id]['total_comm'] = $commission;
    		      
    		      $total_comm += $commission;
	          }
	    }
	    
	    usort($partner_arr, function($a, $b) {
	        return $b['total_bookings'] <=> $a['total_bookings'];
	    });
	    
	   $this->set('partner_arr', $partner_arr)
	    ->set('total_bookings', $total_bookings)
	    ->set('total_amount', $total_amount)
	    ->set('total_paid', $total_paid)
	    ->set('total_cc', $total_cc)
	    ->set('total_cash', $total_cash)
	    ->set('total_comm', $total_comm);
	}
}
?>