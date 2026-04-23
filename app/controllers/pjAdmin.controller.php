<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	protected $extensions = array('gif', 'png', 'jpg', 'jpeg');
	
	protected $mimeTypes = array('image/gif', 'image/png', 'image/jpg', 'image/jpeg', 'image/pjpeg');
	
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public $vehicle_base_lat = '47.2576489';
	public $vehicle_base_lng = '11.3513075';
	public $threshold = 1; // Ngưỡng 1000 mét
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && $this->_get !=  null && !in_array(@$this->_get->toString('action'), array('pjActionLogin', 'pjActionForgot', 'pjActionValidate', 'pjActionExportFeed')))
			{
				if (!$this->isXHR())
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjBase&action=pjActionLogin");
				} else {
					header('HTTP/1.1 401 Unauthorized');
					exit;
				}
			}
		}
		
		$ref_inherits_arr = array();
		if ($this->isXHR() && isset($_SERVER['HTTP_REFERER'])) {
			$http_refer_arr = parse_url($_SERVER['HTTP_REFERER']);
			parse_str($http_refer_arr['query'], $arr);
			if (isset($arr['controller']) && isset($arr['action'])) {
				parse_str($_SERVER['QUERY_STRING'], $query_string_arr);
				$key = $query_string_arr['controller'].'_'.$query_string_arr['action'];
				$cnt = pjAuthPermissionModel::factory()->where('`key`', $key)->findCount()->getData();
				if ($cnt <= 0) {
					$ref_inherits_arr[$query_string_arr['controller'].'::'.$query_string_arr['action']] = $arr['controller'].'::'.$arr['action'];
				}
			}
		}
		
		$inherits_arr = array(
			'pjAdminOptions::pjActionUpdate' => 'pjAdminOptions::pjActionNotifications',
			'pjAdminOptions::pjActionNotificationsSetContent' => 'pjAdminOptions::pjActionNotifications',
			'pjAdminOptions::pjActionNotificationsGetContent' => 'pjAdminOptions::pjActionNotifications',
			'pjAdminOptions::pjActionNotificationsGetMetaData' => 'pjAdminOptions::pjActionNotifications',
			'pjAdminSchedule::pjActionSyncAllData' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::pjActionSyncAllDataManually' => 'pjAdminSchedule::pjActionIndex',
			'pjAdminSchedule::pjActionPullGeneralData' => 'pjAdminSchedule::pjActionIndex',
			'pjAdminSchedule::pjActionNameSign' => 'pjAdminSchedule::pjActionIndex',
			'pjAdminReports::pjActionPrint' => 'pjAdminReports::pjActionIndex',
		    
		    'pjAdminVehicles::pjActionGetServices' => 'pjAdminVehicles::pjActionUpdate',
		    'pjAdminVehicles::pjActionAddService' => 'pjAdminVehicles::pjActionUpdate',
		    'pjAdminVehicles::pjActionUpdateService' => 'pjAdminVehicles::pjActionUpdate',
		    'pjAdminVehicles::pjActionDeleteService' => 'pjAdminVehicles::pjActionUpdate',
		    'pjAdminSchedule::pjActionSyncGeneralData' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminAISchedule::pjActionIndex' => 'pjAdminSchedule::pjActionIndex',
		    
		    'pjAdminSchedule::pjActionCaptcha' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::pjActionCheckCaptcha' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::getVehiclesFromAPI' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::getVehicleFromAPI' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::pjActionCheckVehiclesStatus' => 'pjAdminSchedule::pjActionIndex',
		    
		    'pjAdminTracking::pjActionGetVehicles' => 'pjAdminTracking::pjActionIndex',
		    
		    'pjAdminWhatsappChat::pjActionGetHistory' => 'pjAdminWhatsappChat::pjActionIndex',
		    'pjAdminWhatsappChat::pjActionSend' => 'pjAdminWhatsappChat::pjActionIndex',
		    'pjAdminWhatsappChat::pjActionGetTemplates' => 'pjAdminWhatsappChat::pjActionIndex',
		    'pjAdminWhatsappChat::pjActionMarkAsRead' => 'pjAdminWhatsappChat::pjActionIndex',
		    'pjAdminWhatsappChat::pjActionGetDrivers' => 'pjAdminWhatsappChat::pjActionIndex',
		    
		    'pjAdmin::pjActionGetTemplates' => 'pjAdmin::pjActionIndex',
		    'pjAdmin::pjActionSendWhatsapp' => 'pjAdmin::pjActionIndex',
		    'pjAdmin::pjActionGetTemplates' => 'pjAdminSchedule::pjActionIndex',
		    
		    'pjAdminLogs::pjActionGet' => 'pjAdminLogs::pjActionIndex',
		    
		    'pjAdminPartners::pjActionDownloadFile' => 'pjAdminPartners::pjActionIndex',
		    'pjAdminPartners::pjActionDownloadReport' => 'pjAdminPartners::pjActionIndex',
		    'pjAdminPartners::pjActionDownloadContract' => 'pjAdminPartners::pjActionIndex',
		    
		    'pjAdminPartners::pjActionGetReport' => 'pjAdminPartners::pjActionIndex',
		    'pjAdminPartners::pjActionReportForm' => 'pjAdminPartners::pjActionIndex',
		    'pjAdminPartners::pjActionGenerateBilling' => 'pjAdminPartners::pjActionIndex',
		    'pjAdminPartners::pjActionSaveCustomPrice' => 'pjAdminPartners::pjActionUpdate',
		    'pjAdminSchedule::pjActionDriverConfirmedJobs' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::pjActionCheckDriverConfirmedJobs' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminSchedule::pjActionGetDriverJobStatus' => 'pjAdminSchedule::pjActionIndex',
		    'pjAdminProviders::pjActionDeleteImage' => 'pjAdminProviders::pjActionIndex'
		);
		if ($_REQUEST['controller'] == 'pjAdminOptions' && isset($_REQUEST['next_action'])) {
			$inherits_arr['pjAdminOptions::pjActionUpdate'] = 'pjAdminOptions::'.$_REQUEST['next_action'];
		}
		$inherits_arr = array_merge($inherits_arr, $ref_inherits_arr);
		pjRegistry::getInstance()->set('inherits', $inherits_arr);
	}
	
	public function beforeFilter()
	{
		parent::beforeFilter();
		
		if (!pjAuth::factory()->hasAccess())
		{
			if (!$this->isXHR())
			{
				$this->sendForbidden();
				return false;
			} else {
				self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Access denied.'));
			}
		}
		
		return true;
	}
	
	public function afterFilter()
	{
		parent::afterFilter();
		
		$this->appendJs('index.php?controller=pjBase&action=pjActionMessages', PJ_INSTALL_URL, true);
	}
	
	public function beforeRender()
	{
		
	}
		
	public function setLocalesData()
    {
        $locale_arr = pjLocaleModel::factory()
            ->select('t1.*, t2.file')
            ->join('pjBaseLocaleLanguage', 't2.iso=t1.language_iso', 'left')
            ->where('t2.file IS NOT NULL')
            ->orderBy('t1.sort ASC')->findAll()->getData();

        $lp_arr = array();
        foreach ($locale_arr as $item)
        {
            $lp_arr[$item['id']."_"] = $item['file'];
        }
        $this->set('lp_arr', $locale_arr);
        $this->set('locale_str', pjAppController::jsonEncode($lp_arr));
        $this->set('is_flag_ready', $this->requestAction(array('controller' => 'pjBaseLocale', 'action' => 'pjActionIsFlagReady'), array('return')));
    }
	
	public function pjActionVerifyAPIKey()
    {
        $this->setAjax(true);

        if ($this->isXHR())
        {
            if (!self::isPost())
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method is not allowed.'));
            }

            $option_key = $this->_post->toString('key');
            if (!array_key_exists($option_key, $this->option_arr))
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Option cannot be found.'));
            }

            $option_value = $this->_post->toString('value');
            if(empty($option_value))
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'API key is empty.'));
            }

            $html = '';
            $isValid = false;
            switch ($option_key)
            {
                case 'o_google_maps_api_key':
                case 'o_google_geocoding_api_key':
                    $address = preg_replace('/\s+/', '+', $this->option_arr['o_timezone']);
                    $api_key_str = $option_value;
                    $gfile = "https://maps.googleapis.com/maps/api/geocode/json?key=".$api_key_str."&address=".$address;
                    $Http = new pjHttp();
                    $response = $Http->request($gfile)->getResponse();
                    $geoObj = pjAppController::jsonDecode($response);
                    $geoArr = (array) $geoObj;
                    if ($geoArr['status'] == 'OK')
                    {
                        $isValid = true;
                    }
                    break;
                default:
                    // API key for an unknown service. We can't verify it so we assume it's correct.
                    $isValid = true;
            }

            if ($isValid)
            {
                self::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Key is correct!', 'html' => $html));
            }
            else
            {
                self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Key is not correct!', 'html' => $html));
            }
        }
        exit;
    } 
    
    public function pjActionIndex()
    {
        $this->checkLogin();
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        
        $pjBookingModel = pjBookingModel::factory();       
        
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        
        $cnt_bookings_today = $pjBookingModel->where('DATE(t1.booking_date)', $today)->where('t1.status !=', 'cancelled')->findCount()->getData();
        $cnt_bookings_tomorrow = $pjBookingModel->reset()->where('DATE(t1.booking_date)', $tomorrow)->where('t1.status !=', 'cancelled')->findCount()->getData();
        $amount_today_arr = $pjBookingModel->reset()->select('SUM(`price`) AS `total_amount`')->where('DATE(t1.booking_date)', $today)->where('t1.status !=', 'cancelled')->limit(1)->findAll()->getDataIndex(0);
        $amount_own_vehicles_arr = $pjBookingModel->reset()->select('SUM(`price`) AS `total_amount`')
        ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
        ->where('t2.type', 'own')
        ->where('DATE(t1.booking_date)', $today)
        ->where('t1.status !=', 'cancelled')->limit(1)->findAll()->getDataIndex(0);
        $amount_partner_vehicles_arr = $pjBookingModel->reset()->select('SUM(`price`) AS `total_amount`')
        ->select('SUM(`price`) AS `total_amount`')
        ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
        ->where('t2.type', 'partner')
        ->where('DATE(t1.booking_date)', $today)->where('t1.status !=', 'cancelled')->limit(1)->findAll()->getDataIndex(0);
        
        $today_booking_arr = $pjBookingModel->reset()
            ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
            ->where('DATE(t1.booking_date)', $today)
            ->where('t2.type', 'own')
            ->where('t1.status !=', 'cancelled')
            ->whereNotIn('t1.driver_status', array(4,5))
            ->findAll()->getData();
        $total_paid_today = $total_cc_today = $total_paysafe_today = $total_cash_today = 0;
        foreach ($today_booking_arr as $val) {
            if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
                $total_cash_today += $val['price'];
            } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
                $total_cc_today += $val['price'];
            } elseif (in_array($val['payment_method'], array('cash','creditcard_later')) && !empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
                $total_paysafe_today += $val['price'];
            } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
                $total_paid_today += $val['price'];
            } elseif ($val['payment_method'] == 'cash'){
                $total_cash_today += $val['price'];
            } elseif ($val['payment_method'] == 'creditcard_later'){
                $total_cc_today += $val['price'];
            } else {
                $total_paid_today += $val['price'];
            }
        }
        
        $this->set('cnt_bookings_today', $cnt_bookings_today)
        ->set('cnt_bookings_tomorrow', $cnt_bookings_tomorrow)
        ->set('total_amount_today', $amount_today_arr['total_amount'])
        ->set('total_own_amount_today', $amount_own_vehicles_arr['total_amount'])
        ->set('total_partner_amount_today', $amount_partner_vehicles_arr['total_amount'])    
        ->set('total_paid_today', $total_paid_today)
        ->set('total_cc_today', $total_cc_today)
        ->set('total_cash_today', $total_cash_today)
        ->set('total_paysafe_today', $total_paysafe_today);
        
        $driver_arr = pjMainDriverModel::factory()->select('t1.id, t1.name, t1.email, t1.phone')
        ->where('t1.status', 'T')
        ->where('t1.role_id', 3)
        ->where('t1.type_of_driver', 'own')
        ->orderBy('t1.name ASC')
        ->findAll()
        ->getData();
        $this->set('driver_arr', $driver_arr);
        
        $provider_arr = pjProviderModel::factory()->where('t1.status', 'T')->orderBy('t1.whatsapp_name ASC')->findAll()->getData();
        $this->set('provider_arr', $provider_arr);
        
        $this->appendJs('jsapi', 'https://www.google.com/', TRUE);
        $this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
        $this->appendJs('additional-methods.js', PJ_THIRD_PARTY_PATH . 'validate/');
        $this->appendJs('pjAdmin.js');
        $this->appendJs('pjAdminDashboard.js');
    }
    
    public function pjActionGetMetric() {
        $this->setAjax(true);
        
        $today = date('Y-m-d');
        
        $top_driver_arr = pjBookingModel::factory()->reset()->select('t1.app_driver_id, t2.name AS driver_name, SUM(t1.price) as total_revenue')
        ->join('pjMainDriver', 't2.id=t1.app_driver_id', 'inner')
        ->join('pjVehicle', 't3.id=t1.vehicle_id', 'inner')
        ->where('t3.type', 'own')
        ->where('DATE(t1.booking_date)', $today)
        ->where('t1.status !=', 'cancelled')
        ->groupBy('t1.app_driver_id')
        ->orderBy('total_revenue DESC')
        ->limit(3)
        ->findAll()
        ->getData();
        
        $cnt_bookings_arr = pjBookingModel::factory()->reset()->select('COUNT(*) AS cnt_bookings')
        ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
        ->where('t2.type', 'own')
        ->where('DATE(t1.booking_date)', $today)
        ->where('t1.status !=', 'cancelled')
        ->limit(1)
        ->findAll()
        ->getDataIndex(0);
        
        $top_destination_arr = pjBookingModel::factory()->reset()->select("
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
        ->where('t9.type', 'own')
        ->where('DATE(t1.booking_date)', $today)
        ->where('t1.status !=', 'cancelled')
        ->groupBy('1')
        ->orderBy('2 DESC')
        ->limit(3)
        ->findAll()->getData();
        
        $arr = pjBookingModel::factory()->reset()
        ->select("t1.vehicle_id, t1.distance, t1.pickup_lat, t1.pickup_lng, t1.dropoff_lat, t1.dropoff_lng, t2.registration_number, t2.fuel_consumption")
        ->join('pjVehicle', 't2.id=t1.vehicle_id', 'inner')
        ->where("DATE(t1.booking_date)='".$today."'")
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
        
        $total_distance = $total_fuel_cost = 0;
        $max_vehicle = array();
        if ($results) {
            uasort($results, function($a, $b) {
                return $b['total_driven_km'] <=> $a['total_driven_km'];
            });
            $idx = 0;
            foreach ($results as $vehId => $val) {
                $total_distance += $val['total_driven_km'];
                
                if ((float)$this->option_arr['o_fuel_price'] > 0 && (float)$val['fuel_consumption'] > 0) {
                    $cost_per_km = ((float)$val['fuel_consumption']/100)*(float)$this->option_arr['o_fuel_price'];
                    $total_fuel_cost += round($val['total_driven_km'] * $cost_per_km, 2);
                }
                
                if ($idx < 3) {
                    $val['total_driven_km'] = round($val['total_driven_km']);
                    $max_vehicle[] = $val;
                }
                $idx++;
            }
            $total_distance = round($total_distance);
        }
        
        $data = array(
            'top_driver_arr' => $top_driver_arr ? $top_driver_arr : array(),
            'total_bookings' => $cnt_bookings_arr ? $cnt_bookings_arr['cnt_bookings'] : 0,
            'top_destination_arr' => $top_destination_arr ? $top_destination_arr : array(),
            'total_distance' => $total_distance,
            'total_fuel_cost' => $total_fuel_cost,
            'max_vehicle' => $max_vehicle
        );
        
        $this->set('data', $data);
    }
    
    public function pjActionSendSms()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method is not allowed.'));
        }
        
        if ($this->_post->check('send_sms'))
        {
            $pjMainDriverModel = pjMainDriverModel::factory();
            
            if ($this->_post->toString('driver_id') == 'own_drivers_today') {
                $today = date('Y-m-d');
                $pjMainDriverModel->where('t1.type_of_driver', 'own')->where('t1.id IN (SELECT `driver_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `date`="'.$today.'")');
            } elseif ($this->_post->toString('driver_id') == 'own_drivers_tomorrow') {
                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                $pjMainDriverModel->where('t1.type_of_driver', 'own')->where('t1.id IN (SELECT `driver_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `date`="'.$tomorrow.'")');
            } else {
                $pjMainDriverModel->where('t1.id', $this->_post->toInt('driver_id'));
            }
            $arr = $pjMainDriverModel->where('t1.phone != ""')
            ->findAll()->getDataPair(null, 'phone');
            if ($arr) {
                $result = pjAppController::messagebirdSendSMS($arr, $this->_post->toString('message'), $this->option_arr);
                if ($result['status'] == 'OK')
                {
                    self::jsonResponse(array('status' => 'OK', 'text' => __('dash_sms_sent_success', true)));
                }
                self::jsonResponse(array('status' => 'ERR', 'text' => __('dash_sms_sent_failed', true)));
            }
            self::jsonResponse(array('status' => 'ERR', 'text' => __('dash_sms_sent_failed', true)));
        }
        exit;
    }
    
    public function pjActionSendPopUp()
    {
        $this->setAjax(true);
        
        if (!$this->isXHR())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Missing headers.'));
        }
        
        if (!self::isPost())
        {
            self::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method is not allowed.'));
        }
        
        if ($this->_post->check('send_popup'))
        {
            $pjMainDriverModel = pjMainDriverModel::factory();
            
            if ($this->_post->toString('driver_id') == 'own_drivers_today') {
                $today = date('Y-m-d');
                $pjMainDriverModel->where('t1.type_of_driver', 'own')->where('t1.id IN (SELECT `driver_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `date`="'.$today.'")');
            } elseif ($this->_post->toString('driver_id') == 'own_drivers_tomorrow') {
                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                $pjMainDriverModel->where('t1.type_of_driver', 'own')->where('t1.id IN (SELECT `driver_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `date`="'.$tomorrow.'")');
            } else {
                $pjMainDriverModel->where('t1.id', $this->_post->toInt('driver_id'));
            }
            $arr = $pjMainDriverModel->findAll()->getDataPair(null, 'id');
            if ($arr) {
                $pjDriverPopupModel = pjDriverPopupModel::factory();
                foreach ($arr as $val)
                {
                    $pjDriverPopupModel->addBatchRow(array($val, $this->_post->toString('message'), 0));
                }
                $pjDriverPopupModel
                ->setBatchFields(array('driver_id', 'message', 'is_displayed'))
                ->insertBatch();
            }
            self::jsonResponse(array('status' => 'OK', 'text' => __('dash_popup_sent_success', true)));
        }
        exit;
    }
    
    public function pjActionChartGet()
    {
        $this->setAjax(true);
        
        
        $min = 1;
        $max = 7;
        if ($this->_get->check('type'))
        {
            switch ($this->_get->toInt('type'))
            {
                case 2:
                    $min = -7;
                    $max = -1;
                    break;
                case 1:
                default:
                    $min = 0;
                    $max = 6;
                    break;
            }
        }
        if ($this->_get->check('ts') && $this->_get->toInt('ts') > 0) {
            $iso_date = date("Y-n-j", $this->_get->toInt('ts'));
        } else {
            $iso_date = date("Y-n-j");
        }
        list($y, $m, $d) = explode("-", $iso_date);
        
        $bookings = array();
        
        $pjBookingModel = pjBookingModel::factory();
        foreach (range($min, $max) as $i)
        {
            $time = mktime(0, 0, 0, $m, $d+$i, $y);
            $date = date("Y-m-d", $time);
            
            $bookings[$date] = $pjBookingModel
            ->reset()
            ->where('t1.status !=', 'cancelled')
            ->where('DATE(t1.booking_date)', $pjBookingModel->escapeStr($date))
            ->findCount()->getData();
        }
        
        $result = array('cols' => array(), 'rows' => array());
        $result['cols'][] = array(
            'id' => 0,
            'label' => 'Date',
            'type' => 'string'
        );
        $result['cols'][] = array(
            'id' => 1,
            'label' => __('dash_transfers', true),
            'type' => 'number'
        );
        $i = 0;
        foreach ($bookings as $date => $cnt)
        {
            $result['rows'][$i] = array('c' => array());
            foreach ($result['cols'] as $c => $col)
            {
                if ($c == 0)
                {
                    $result['rows'][$i]['c'][0] = array('v' => $date, 'f' => pjDateTime::formatDate($date, 'Y-m-d', 'd/m'));
                } else {
                    $result['rows'][$i]['c'][$c] = array('v' => $cnt);
                    
                }
            }
            $i += 1;
        }
        
        pjAppController::jsonResponse($result);
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
    
    public function pjActionSendWhatsapp() {
        $this->setAjax(true);
        
        $post = $this->_post->raw();
        $message = $post['whatsapp_message'];
        $provider_id = $post['provider_id'];
        //$provider_id = 4;
        $provider_arr = pjProviderModel::factory()->find($provider_id)->getData();
        
        $accessToken = $provider_arr['whatsapp_permanent_access_token'];
        $phoneNumberId = $provider_arr['whatsapp_phone_number_id'];
        
        $pjMainDriverModel = pjMainDriverModel::factory();
        
        if ($this->_post->toString('driver_id') == 'own_drivers_today') {
            $today = date('Y-m-d');
            $pjMainDriverModel->where('t1.type_of_driver', 'own')->where('t1.id IN (SELECT `driver_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `date`="'.$today.'")');
        } elseif ($this->_post->toString('driver_id') == 'own_drivers_tomorrow') {
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $pjMainDriverModel->where('t1.type_of_driver', 'own')->where('t1.id IN (SELECT `driver_id` FROM `'.pjDriverVehicleModel::factory()->getTable().'` WHERE `date`="'.$tomorrow.'")');
        } else {
            $pjMainDriverModel->where('t1.id', $this->_post->toInt('driver_id'));
        }
        $arr = $pjMainDriverModel->where('t1.phone != ""')->findAll()->getData();
        if ($arr) {
            $url = "https://graph.facebook.com/v18.0/$phoneNumberId/messages";
            foreach ($arr as $val) {
                $driver_id = $val['id'];
                $driver_phone = $val['phone'];
                $driver_phone = ltrim($driver_phone, '0+');
                if (!empty($post['whatsapp_template'])) {
                    list($name, $lang) = explode('~:~', $post['whatsapp_template']);
                    
                    $date = date($this->option_arr['o_date_format']);
                    $driver_name = $val['name'];
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
        }
        pjAppController::jsonResponse(array('status' => 'OK', 'text' => __('dash_message_sent_successfully', true)));
    }
}
?>