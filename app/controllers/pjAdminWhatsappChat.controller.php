<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_INSTALL_PATH. 'whatsapp/vendor/autoload.php';
class pjAdminWhatsappChat extends pjAdmin
{	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if (!pjAuth::factory()->hasAccess())
		{
			$this->sendForbidden();
			return;
		}
		
		$provider_arr = pjProviderModel::factory()->where('t1.status', 'T')->orderBy('t1.whatsapp_name ASC')->findAll()->getData();
		$this->set('provider_arr', $provider_arr);
		
		$provider_id = $provider_arr ? $provider_arr[0]['id'] : 0;
		$driver_arr = pjMainDriverModel::factory()
		->select('t1.*, COALESCE(t2.unread_count, 0) as unread_count')
		->join('pjWhatsappDriverProviderStatus', 't1.id=t2.driver_id AND t2.provider_id='.$provider_id, 'left')
		->where('t1.role_id', 3)
		->where('t1.status', 'T')
		->orderBy('t2.last_message_at DESC, t1.name ASC')->findAll()->getData();
		$this->set('driver_arr', $driver_arr);
		
		$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
		$this->appendJs('https://js.pusher.com/8.0.1/pusher.min.js', '', true);
		$this->appendJs('pjAdminWhatsappChat.js');
	}

	public function pjActionGetHistory() {
	    $this->setAjax(true);
	    $provider_id = $this->_get->toInt('provider_id');
	    $phone = $this->_get->toString('phone');
	    $limit = $this->_get->toInt('limit') ? $this->_get->toInt('limit') : 20;
	    $offset = $this->_get->toInt('offset') ? $this->_get->toInt('offset') : 0;
	    
	    $messages = pjWhatsappChatHistoryModel::factory()
       ->select("t1.content, t1.direction, DATE_FORMAT(t1.created_at, '%H:%i') as time")
       ->where("t1.driver_phone", $phone)
       ->where('t1.provider_id', $provider_id)
	    ->orderBy("t1.created_at ASC")
	    ->limit($limit, $offset)
	    ->findAll()
	    ->getData();
	    pjAppController::jsonResponse(array_reverse($messages));
	}
	
	public function pjActionSend() {
	    $this->setAjax(true);
	    
	    $post = $this->_post->raw();
	    $phone = $post['phone'];
	    $message = $post['message'];
	    $provider_id = $post['provider_id'];
	    $driver_id = $post['driver_id'];
	    $provider_arr = pjProviderModel::factory()->find($provider_id)->getData();
	    $driver_arr = pjMainDriverModel::factory()->find($driver_id)->getData();
	    $date = date($this->option_arr['o_date_format']);
	    $driver_name = @$driver_arr['name'];
	    
	    $accessToken = $provider_arr['whatsapp_permanent_access_token'];
	    $phoneNumberId = $provider_arr['whatsapp_phone_number_id'];
	    if ($phone && $message) {
	        $url = "https://graph.facebook.com/v18.0/$phoneNumberId/messages";
	        if (!empty($post['template'])) {
	            list($name, $lang) = explode('~:~', $post['template']);
	            $data_replace = [
	                'driver_name' => $driver_name,
	                'date'     => $date
	            ];
	            $mappedParams = pjAppController::getWhatsappTemplateParameters($name, $data_replace);
	            
	            $data = [
	                "messaging_product" => "whatsapp",
	                "to" => $phone,
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
	                "to" => $phone,
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
	            $data = array(
	                'wa_message_id' => $res['messages'][0]['id'],
	                'provider_id' => $provider_id,
	                'driver_phone' => $phone,
	                'direction' => 'sent',
	                'content' => $message
	            );
	            pjWhatsappChatHistoryModel::factory()->setAttributes($data)->insert();
	            pjAppController::jsonResponse(array('status' => 'success', 'message' => $message));
	        } else {
	            pjAppController::jsonResponse(array('status' => 'failed'));
	        }
	    }
	}
	
	public function pjActionGetTemplates() {
	    $this->setAjax(true);
	    $provider_id = $this->_get->toInt('provider_id');
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
	
	public function pjActionMarkAsRead() {
	    $this->setAjax(true);
	    if ($this->_post->check('mark_as_read')) {
	        $provider_id = $this->_post->toInt('provider_id');
	        $driver_id = $this->_post->toInt('driver_id');
	        pjWhatsappDriverProviderStatusModel::factory()
	        ->where('provider_id', $provider_id)
	        ->where('driver_id', $driver_id)
	        ->limit(1)->modifyAll(array('unread_count' => 0));
	    }
	   
	    pjAppController::jsonResponse(array('status' => 'OK'));
	}
	
	public function pjActionGetDrivers()
	{
	    $this->setAjax(true);
	    
	    $provider_id = $this->_get->toInt('provider_id');
	    $driver_arr = pjMainDriverModel::factory()
	    ->select('t1.*, COALESCE(t2.unread_count, 0) as unread_count')
	    ->join('pjWhatsappDriverProviderStatus', 't1.id=t2.driver_id AND t2.provider_id='.$provider_id, 'left')
	    ->where('t1.role_id', 3)
	    ->where('t1.status', 'T')
	    ->orderBy('t2.last_message_at DESC, t1.name ASC')->findAll()->getData();
	    $this->set('driver_arr', $driver_arr);
	}
}
?>