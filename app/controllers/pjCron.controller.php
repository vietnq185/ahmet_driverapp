<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCron extends pjAppController
{
	public function __construct()
	{
		$this->setLayout('pjActionEmpty');
	}
	
	public function pjActionSyncGeneralData()
	{
		$this->setLayout('pjActionEmpty');
		
		set_time_limit(0);
		pjApiSync::pjActionPullAllGeneralData();
		
		return "Data has been synchronized!";
	}
}
?>