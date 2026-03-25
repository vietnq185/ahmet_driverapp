<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_INSTALL_PATH. 'whatsapp/vendor/autoload.php';
class pjFront extends pjAppController
{
	public $defaultLocale = 'SbsDriver_LocaleId';
	
	public function __construct()
	{
		$this->setLayout('pjActionEmpty');
		
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
				$this->setLocaleId($locale_arr[0]['id']);
			}
		}
		return parent::beforeFilter();
	}

	public function beforeRender()
	{
		
	}
	
	static protected function allowCORS()
	{
		if (!isset($_SERVER['HTTP_ORIGIN']))
		{
			return;
		}
		
		header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
		{
			exit;
		}
	}
	
	public function pjActionConfirm()
	{
	    $this->setAjax(true);
	    
	    $input = $_REQUEST;
	    //pjAppController::writeLog(date("Y-m-d H:i:s"), "chat.log");
	    //pjAppController::writeLog(serialize($input), "chat.log");
	    
	    $pusher = new Pusher\Pusher($this->option_arr['o_pusher_key'], $this->option_arr['o_pusher_secret'], $this->option_arr['o_pusher_app_ai'], ['cluster' => $this->option_arr['o_pusher_cluster'], 'useTLS' => true]);
	    
	    // Verify Webhook (chỉ dùng khi Meta yêu cầu xác thực lần đầu)
	    if (isset($input['hub_mode']) && $input['hub_mode'] == 'subscribe') {
	        echo $input['hub_challenge'];
	        exit;
	    }
	    
	    if (isset($input['entry'][0]['changes'][0]['value']['messages'][0])) {
	        $msg = $input['entry'][0]['changes'][0]['value']['messages'][0];
	        $fromPhone = $msg['from'];
	        $content = $msg['text']['body'];
	        $recipientId = $input['entry'][0]['changes'][0]['value']['metadata']['phone_number_id'];
	        
	        $provider_arr = pjProviderModel::factory()->where('t1.whatsapp_phone_number_id', $recipientId)->limit(1)->findAll()->getDataIndex(0);
	        if ($provider_arr) {
	            $data = array(
	                'wa_message_id' => $msg['id'],
	                'provider_id' => $provider_arr['id'],
	                'driver_phone' => $fromPhone,
	                'direction' => 'received',
	                'content' => $content
	            );
	            pjWhatsappChatHistoryModel::factory()->setAttributes($data)->insert();
	            
	            $driver_arr = pjMainDriverModel::factory()->where("TRIM(LEADING '0' FROM 
                        TRIM(LEADING '+' FROM 
                            TRIM(BOTH ' ' FROM `phone`)
                        )
                    )='".$fromPhone."'")->limit(1)->findAll()->getDataIndex(0);
	            $driver_id = 0;
	            $provider_id = $provider_arr['id'];
	            if ($driver_arr) {
	                $driver_id = $driver_arr['id'];
    	            $data_update = array(
    	                'unread_count' => (int)$driver_arr['unread_count'] + 1,
    	                'last_message_at' => date('Y-m-d H:i:s'),
    	                'last_provider_id' => $provider_id
    	            );
    	            pjMainDriverModel::factory()->reset()->set('id', $driver_id)->modify($data_update);
	            }

	            $sql = "INSERT INTO `".pjWhatsappDriverProviderStatusModel::factory()->getTable()."` (`driver_id`, `provider_id`, `unread_count`, `last_message_at`)
	            VALUES ($driver_id, $provider_id, 1, NOW())
	            ON DUPLICATE KEY UPDATE
	            `unread_count` = `unread_count` + 1,
	            `last_message_at` = NOW()";
	            pjWhatsappDriverProviderStatusModel::factory()->prepare($sql)->exec();
	            
	            $pusher->trigger('chat-' . $provider_id, 'new-message', [
    	            'provider_id' => $provider_id,
    	            'driver_id' => $driver_id,
    	            'phone' => $fromPhone,
    	            'content' => $content,
    	            'direction' => 'received',
    	            'time' => date('H:i')
    	        ]);
	        }
	    }
	    exit;
	}
}
?>