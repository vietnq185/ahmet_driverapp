<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceAttributeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicle_maintrance_attributes';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'attribute_type_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'content', 'type' => 'int', 'default' => ':NULL')
	);
	
	protected $validate = array(
	    
	);
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceAttributeModel($attr);
	}
}
?>