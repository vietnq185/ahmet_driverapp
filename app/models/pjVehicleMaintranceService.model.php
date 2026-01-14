<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceServiceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicle_maintrance_services';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'tmp_hash', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'service_type_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'km', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'cost', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'service_station', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceServiceModel($attr);
	}
}
?>