<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDriverVehicleModel extends pjAppModel
{
/**
 * The name of table's primary key. If PK is over 2 or more columns set this to boolean null
 *
 * @var string
 * @access public
 */
	var $primaryKey = 'id';
/**
 * The name of table associate with current model
 *
 * @var string
 * @access protected
 */
	var $table = 'drivers_vehicles';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'driver_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'vehicle_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'order', 'type' => 'smallint', 'default' => ':NULL')
	);
	
	protected $validate = array(
	
	);
	
	public static function factory($attr=array())
	{
		return new pjDriverVehicleModel($attr);
	}
}
?>