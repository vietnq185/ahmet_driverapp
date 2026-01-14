<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicle_maintrance';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'vehicle_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'make', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'model', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'model_year', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'vin', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'net_price', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'buy_date', 'type' => 'date', 'default' => ':NULL'),
	    array('name' => 'buyed_in_km', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'tuv', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'internet', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'toll_brenner', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'austria_vignette', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'gps', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'toll_arlberg', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'telepass', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'swiss_vignette', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceModel($attr);
	}
}
?>