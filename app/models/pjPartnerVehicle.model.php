<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPartnerVehicleModel extends pjAppModel
{
	protected $primaryKey = null;
	
	protected $table = 'partner_vehicles';
	
	protected $schema = array(
		array('name' => 'partner_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'vehicle_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
	    return new pjPartnerVehicleModel($attr);
	}
}
?>