<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceAccidentModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicle_maintrance_accidents';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'tmp_hash', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'driver_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'location_accident', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'instance_number', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'is_second_vehicle_involved', 'type' => 'tinyint', 'default' => '0'),
	    array('name' => 'second_driver_name', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'second_licence_plate_number', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'second_instance_number', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceAccidentModel($attr);
	}
}
?>