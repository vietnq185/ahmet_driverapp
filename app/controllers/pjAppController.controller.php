<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAppController extends pjBaseAppController
{
	public $models = array();

	private $layoutRange = array(1, 2);
	
    public function pjActionCheckInstall()
    {
        $this->setLayout('pjActionEmpty');

        $result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());
        $folders = array('app/web/upload');
        foreach ($folders as $dir)
        {
            if (!is_writable($dir))
            {
                $result['status'] = 'ERR';
                $result['code'] = 101;
                $result['text'] = 'Permission requirement';
                $result['info'][] = sprintf('Folder \'<span class="bold">%1$s</span>\' is not writable. You need to set write permissions (chmod 777) to directory located at \'<span class="bold">%1$s</span>\'', $dir);
            }
        }

        return $result;
    }

    /**
     * Sets some predefined role permissions and grants full permissions to Admin.
     */
    public function pjActionAfterInstall()
    {
        $this->setLayout('pjActionEmpty');

        $result = array('status' => 'OK', 'code' => 200, 'text' => 'Operation succeeded', 'info' => array());

        $pjAuthRolePermissionModel = pjAuthRolePermissionModel::factory();
        $pjAuthUserPermissionModel = pjAuthUserPermissionModel::factory();

        $permissions = pjAuthPermissionModel::factory()->findAll()->getDataPair('key', 'id');

        $roles = array(1 => 'admin', 2 => 'editor', 3 => 'driver');
        foreach ($roles as $role_id => $role)
        {
            if (isset($GLOBALS['CONFIG'], $GLOBALS['CONFIG']["role_permissions_{$role}"])
                && is_array($GLOBALS['CONFIG']["role_permissions_{$role}"])
                && !empty($GLOBALS['CONFIG']["role_permissions_{$role}"]))
            {
                $pjAuthRolePermissionModel->reset()->where('role_id', $role_id)->eraseAll();

                foreach ($GLOBALS['CONFIG']["role_permissions_{$role}"] as $role_permission)
                {
                    if($role_permission == '*')
                    {
                        // Grant full permissions for the role
                        foreach($permissions as $key => $permission_id)
                        {
                            $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
                        }
                        break;
                    }
                    else
                    {
                        $hasAsterix = strpos($role_permission, '*') !== false;
                        if($hasAsterix)
                        {
                            $role_permission = str_replace('*', '', $role_permission);
                        }

                        foreach($permissions as $key => $permission_id)
                        {
                            if($role_permission == $key || ($hasAsterix && strpos($key, $role_permission) !== false))
                            {
                                $pjAuthRolePermissionModel->setAttributes(compact('role_id', 'permission_id'))->insert();
                            }
                        }
                    }
                }
            }
        }

		// Grant full permissions to Admin
        $user_id = 1; // Admin ID
        $pjAuthUserPermissionModel->reset()->where('user_id', $user_id)->eraseAll();
        foreach($permissions as $key => $permission_id)
        {
            $pjAuthUserPermissionModel->setAttributes(compact('user_id', 'permission_id'))->insert();
        }

        return $result;
    }
	
	public function getLayoutRange()
	{
		return $this->layoutRange;
	}
	
    public function beforeFilter()
    {
        parent::beforeFilter();

        if(!in_array($this->_get->toString('controller'), array('pjApiSync')))
        {
            $this->appendJs('pjAdminCore.js');
            // TODO: DELETE unnecessary files
            #$this->appendCss('reset.css');
            #$this->appendCss('pj-all.css', PJ_FRAMEWORK_LIBS_PATH . 'pj/css/');
            $this->appendCss('admin.css');
            
            /* $this->appendJs('jquery-ui.min.js', PJ_THIRD_PARTY_PATH . 'jquery_ui/');
            $this->appendCss('jquery-ui.min.css', PJ_THIRD_PARTY_PATH . 'jquery_ui/'); */
        }
        
        return true;
    }
    
    public function getForeignId()
    {
    	return 1;
    }
    
	public function isDriver()
    {
    	return (int) $this->getRoleId() === 3;
    }
    
    public function isInvoiceReady()
	{
		return $this->isAdmin();
	}
    
    public function isCountryReady()
    {
    	return $this->isAdmin();
    }
    
    public function isOneAdminReady()
    {
    	return $this->isAdmin();
    }
    
    public function isWebsiteContentReady()
	{
		return $this->isAdmin();
	}
	
	public function isContactFormReady()
	{
		return $this->isAdmin();
	}	
    
    public static function jsonDecode($str)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->decode($str);
	}
	
	public static function jsonEncode($arr)
	{
		$Services_JSON = new pjServices_JSON();
		return $Services_JSON->encode($arr);
	}
	
	public static function jsonResponse($arr)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo pjAppController::jsonEncode($arr);
		exit;
	}

	public function getLocaleId()
	{
		return isset($_SESSION[$this->defaultLocale]) && (int) $_SESSION[$this->defaultLocale] > 0 ? (int) $_SESSION[$this->defaultLocale] : 1;
	}
	
	public function setLocaleId($locale_id)
	{
		$_SESSION[$this->defaultLocale] = (int) $locale_id;
	}
	
	public function setTheme($theme)
	{
		$_SESSION[$this->defaultTheme] = $theme;
	}
	
	public function getTheme()
	{
		return isset($_SESSION[$this->defaultTheme]) && !empty($_SESSION[$this->defaultTheme]) ? $_SESSION[$this->defaultTheme] : false;
	}
	
	public function friendlyURL($str, $divider='-')
	{
		$str = mb_strtolower($str, mb_detect_encoding($str)); // change everything to lowercase
		$str = trim($str); // trim leading and trailing spaces
		$str = preg_replace('/[_|\s]+/', $divider, $str); // change all spaces and underscores to a hyphen
		$str = preg_replace('/\x{00C5}/u', 'AA', $str);
		$str = preg_replace('/\x{00C6}/u', 'AE', $str);
		$str = preg_replace('/\x{00D8}/u', 'OE', $str);
		$str = preg_replace('/\x{00E5}/u', 'aa', $str);
		$str = preg_replace('/\x{00E6}/u', 'ae', $str);
		$str = preg_replace('/\x{00F8}/u', 'oe', $str);
		$str = preg_replace('/[^a-z\x{0400}-\x{04FF}0-9-]+/u', '', $str); // remove all non-cyrillic, non-numeric characters except the hyphen
		$str = preg_replace('/[-]+/', $divider, $str); // replace multiple instances of the hyphen with a single instance
		$str = preg_replace('/^-+|-+$/', '', $str); // trim leading and trailing hyphens
		return $str;
	}
	    
	static public function getFromEmail()
	{
		$arr = pjAuthUserModel::factory()
			->findAll()
			->orderBy("t1.id ASC")
			->limit(1)
			->getData();
		return !empty($arr) ? $arr[0]['email'] : null;
	}
	
	static public function getAdminEmail()
	{
		 $arr = pjAuthUserModel::factory()->select('t1.email')->find(1)->getData();	    
	    return $arr ? $arr['email'] : NULL;
	}
	
	static public function getAllAdminEmails()
	{
		$arr = pjAuthUserModel::factory()->where('t1.role_id', 1)->findAll()->getDataPair(NULL, 'email');	    
	    return $arr;
	}
	
	static public function getAdminPhone()
	{
		$arr = pjAuthUserModel::factory()->select('t1.phone')->find(1)->getData();	    
	    return $arr ? $arr['phone'] : NULL;
	}
	
	static public function pjActionGetSubjectMessage($notification, $locale_id, $calendar_id)
	{
	    $field = $notification['variant'] . '_tokens_' . $notification['recipient'];
	    $field = str_replace('confirmation', 'confirm', $field);
	    $pjMultiLangModel = pjMultiLangModel::factory();
	    $lang_message = $pjMultiLangModel
		    ->reset()
		    ->select('t1.*')
		    ->where('t1.foreign_id', $calendar_id)
		    ->where('t1.model','pjOption')
		    ->where('t1.locale', $locale_id)
		    ->where('t1.field', $field)
		    ->limit(0, 1)
		    ->findAll()
		    ->getData();
	    $field = $notification['variant'] . '_subject_' . $notification['recipient'];
	    $field = str_replace('confirmation', 'confirm', $field);
	    $lang_subject = $pjMultiLangModel
		    ->reset()
		    ->select('t1.*')
		    ->where('t1.foreign_id',  $calendar_id)
		    ->where('t1.model','pjOption')
		    ->where('t1.locale', $locale_id)
		    ->where('t1.field', $field)
		    ->limit(0, 1)
		    ->findAll()
		    ->getData();
	    return compact('lang_message', 'lang_subject');
	}
	
	static public function messagebirdSendSMS($recipients, $body, $option_arr) {
    	require_once(PJ_COMPONENTS_PATH. '/messagebird/autoload.php');
    	
    	$MessageBird = new \MessageBird\Client($option_arr['plugin_sms_message_bird_access_key']);
		$Message             = new \MessageBird\Objects\Message();
		$Message->originator = $option_arr['plugin_sms_message_bird_originator'];
		$Message->recipients = $recipients;
		$Message->body       = $body;
		$Message->datacoding = 'unicode';
		
		try {
		   	$MessageResult = $MessageBird->messages->create($Message);
		   	$pjSmsModel = pjBaseSmsModel::factory();
		   	foreach ($recipients as $number) {
		   		$data = array();
		   		$data['number'] = $number;
		   		$data['text'] = $body;
		   		$data['status'] = 'sent';
		   		$pjSmsModel->reset()->setAttributes($data)->insert();
		   	}
		   	return array(
		   		'status' => 'OK',
		   		'code' => 1
		   	);		
		} catch (\MessageBird\Exceptions\AuthenticateException $e) {
		    // That means that your accessKey is unknown
		    //$this->log('wrong login');	
		    return array(
		   		'status' => 'ERR',
		   		'code' => 4
		   	);	
		} catch (\MessageBird\Exceptions\BalanceException $e) {
		    // That means that you are out of credits, so do something about it.
		    //$this->log('no balance');
		    return array(
		   		'status' => 'ERR',
		   		'code' => 0
		   	);	
		} catch (\Exception $e) {
			//$this->log($e->getMessage());
			return array(
		   		'status' => 'ERR',
		   		'code' => 2
		   	);
		}
    }
    
    static public function createRandomBookingId() {
        mt_srand();
        $uuid = date('y',time()).mt_rand(10000000, 99999999);
        $cnt = pjBookingModel::factory()->reset()->where('t1.uuid', $uuid)->findCount()->getData();
        if ((int)$cnt > 0)
        {
            $this->createRandomBookingId();
        } else {
            return $uuid;
        }
    }
}
?>