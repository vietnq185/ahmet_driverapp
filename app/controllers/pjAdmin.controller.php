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
		    'pjAdminAISchedule::pjActionIndex' => 'pjAdminSchedule::pjActionIndex'
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
        $total_paid_today = $total_cc_today = $total_cash_today = 0;
        foreach ($today_booking_arr as $val) {
            if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
                $total_cash_today += $val['price'];
            } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
                $total_cc_today += $val['price'];
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
        
        ->set('total_paid_toay', $total_paid_today)
        ->set('total_cc_toay', $total_cc_today)
        ->set('total_cash_toay', $total_cash_today);
        
        $driver_arr = pjMainDriverModel::factory()->select('t1.id, t1.name, t1.email, t1.phone')
        ->where('t1.status', 'T')
        ->where('t1.role_id', 3)
        ->where('t1.type_of_driver', 'own')
        ->orderBy('t1.name ASC')
        ->findAll()
        ->getData();
        $this->set('driver_arr', $driver_arr);
        
        $this->appendJs('jsapi', 'https://www.google.com/', TRUE);
        $this->appendJs('pjAdmin.js');
        $this->appendJs('pjAdminDashboard.js');
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
}
?>