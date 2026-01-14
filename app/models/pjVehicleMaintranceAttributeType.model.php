<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceAttributeTypeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicle_maintrance_attribute_types';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $validate = array(
	    
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceAttributeTypeModel($attr);
	}
}
?>