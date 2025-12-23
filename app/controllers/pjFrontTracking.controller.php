<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFrontTracking extends pjAppController
{
	public $defaultLocale = 'SbsTracking_LocaleId';
	
	public function __construct()
	{
	    $this->setLayout('pjActionFront');
		self::allowCORS();
	}
	
	public function afterFilter()
	{
		
	}
	
	public function beforeFilter()
	{
		$cid = $this->getForeignId();
        $this->models['Option'] = pjBaseOptionModel::factory();
	    $base_option_arr = $this->models['Option']->getPairs($cid);
	    $script_option_arr = pjOptionModel::factory()->getPairs($cid);
	    $this->option_arr = array_merge($base_option_arr, $script_option_arr);
	    $this->set('option_arr', $this->option_arr);
		
		if (!isset($_SESSION[$this->defaultLocale]))
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
			    $this->pjActionSetLocale($locale_arr[0]['id']);
			}
		}
		return parent::beforeFilter();
	}

	public function beforeRender()
	{
		
	}
	
	public function pjActionSetLocale($locale)
	{
	    if ((int) $locale > 0)
	    {
	        $_SESSION[$this->defaultLocale] = (int) $locale;
	    }
	    return $this;
	}
	
	public function pjActionGetLocale()
	{
	    return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : FALSE;
	}
	
	public function isXHR()
	{
	    // CORS
	    return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	protected static function allowCORS()
	{
	    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
	    header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
	    header("Access-Control-Allow-Origin: $origin");
	    header("Access-Control-Allow-Credentials: true");
	    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
	    header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
	
	
	public function pjActionLoadCss()
	{
	    $dm = new pjDependencyManager(null, PJ_THIRD_PARTY_PATH);
	    $dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	    $theme = 'theme1';
	    $get = $this->_get->raw();
	    $arr = array(
	        array('file' => 'font-awesome.min.css', 'path' => $dm->getPath('font_awesome') . 'css/'),
	        //array('file' => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', 'path' => ''),
	        array('file' => 'front_tracking.css', 'path' => PJ_CSS_PATH)
	    );
	    header("Content-Type: text/css; charset=utf-8");
	    foreach ($arr as $item)
	    {
	        ob_start();
	        @readfile($item['path'] . $item['file']);
	        $string = ob_get_contents();
	        ob_end_clean();
	        
	        if ($string !== FALSE)
	        {
	            echo str_replace(
	                array( '../fonts/fontawesome', "pjWrapper"),
	                array(
	                    PJ_INSTALL_URL . $dm->getPath('font_awesome') . 'fonts/fontawesome',
	                    "pjWrapperTracking"
	                ),
	                $string
	                ) . "\n";
	        }
	    }
	    exit;
	}
	
	public function pjActionLoad()
	{
	    $this->setAjax(false);
	    $this->setLayout('pjActionFront');
	    ob_start();
	    header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionTracking() {
	    $this->setAjax(true);
	    if ($this->isXHR()) {
	        if ($this->_get->check('hash') && $this->_get->toString('hash') != '') {
	            $arr = pjBookingModel::factory()->select('t1.*, t2.registration_number, t3.content AS vehicle_name')
	               ->join('pjVehicle', 't2.id=t1.vehicle_id', 'left outer')
	               ->join('pjMultiLang', "t3.model='pjVehicle' AND t3.foreign_id=t1.vehicle_id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left outer')
	               ->where("SHA1(CONCAT(t1.id, '".PJ_SALT."'))='".$this->_get->toString('hash')."'")
	               ->limit(1)->findAll()->getDataIndex(0);
               if ($arr) {
                   $resp = $this->getVehiclesFromAPI();
                   $arr['vehicle_data'] = isset($resp[$arr['registration_number']]) ? $resp[$arr['registration_number']] : array();
               }
               $this->set('arr', $arr);
	        }
	    }
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
}
?>