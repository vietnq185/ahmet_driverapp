<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceServicesTypesModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicle_maintrance_services_types';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'service_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'type_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceServicesTypesModel($attr);
	}
}
?>