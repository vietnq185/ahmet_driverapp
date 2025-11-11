<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
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
}
?>