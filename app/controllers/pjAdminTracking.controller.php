<?php
if (!defined("ROOT_PATH"))
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}
class pjAdminTracking extends pjAdmin
{
    public function pjActionIndex()
    {
        $this->checkLogin();
        
        if (!pjAuth::factory()->hasAccess())
        {
            $this->sendForbidden();
            return;
        }
        $this->appendCss('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', '', true, false);
        $this->appendJs('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', '', true, false);
        $this->appendJs('pjAdminTracking.js');
    }
    
    public function pjActionGetVehicles() {
        $this->setAjax(true);
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
            http_response_code(500);
            die(json_encode(['error' => 'cURL error: ' . $error_msg]));
        }
        
        curl_close($ch);
        
        // Kiểm tra HTTP Status Code
        if ($http_code != 200) {
            http_response_code($http_code);
            die(json_encode(['error' => 'API returned non-200 status code: ' . $http_code, 'response' => $response]));
        }
        
        // Chuyển đổi JSON response thành mảng PHP
        $arr = json_decode($response, true);
        $data = array();
        foreach ($arr as $val) {
            $is_valid = true;
            if ($val['child']['type'] != 'vehicle') {
                $is_valid = false;
            }
            if ($q = $this->_get->toString('q'))
            {
                $q = strtolower(trim($q));
                $make = strtolower(trim($val['child']['make']) ?? '');
                $model = strtolower(trim($val['child']['model']) ?? '');
                $licensePlate = strtolower(trim($val['child']['licensePlate']) ?? '');
                $name = strtolower(trim($vehicle['name']) ?? '');
                
                if (strpos($make, $q) === false && 
                    strpos($model, $q) === false && 
                    strpos($licensePlate, $q) === false && 
                    strpos($name, $q) === false) {
                    $is_valid = false;
                }
            }
            
            $isMoving = isset($val['logLast']['isMoving']) ? (int)$val['logLast']['isMoving'] : '';
            $speed = isset($val['logLast']['speed']) ? (int)$val['logLast']['speed'] : 0;
            $val['speed'] = $speed;
            if ($this->_get->check('status') && in_array($this->_get->toInt('status'), array(0,1)) && $this->_get->toInt('status') != $isMoving && $speed <= 0) {
                $is_valid = false;
            }
            if ($is_valid) {
                $data[] = $val;
            }
        }
        
        $sortBy = 'name';
        $direction = 'asc';
        if ($this->_get->toString('column') && in_array($this->_get->toString('direction'), array('asc', 'desc')))
        {
            $sortBy = $this->_get->toString('column');
            $direction = $this->_get->toString('direction');
        }
        
        usort($data, function($a, $b) use ($sortBy, $direction) {
            if ($direction == 'asc') {
                return $a[$sortBy] <=> $b[$sortBy];
            } else {
                return $b[$sortBy] <=> $a[$sortBy];
            }
        });
        
        // Định dạng lại dữ liệu và gửi về Frontend (JavaScript)
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>