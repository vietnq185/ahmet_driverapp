<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFleetModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'fleets';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'external_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'min_passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'return_discount_1', 'type' => 'decimal', 'default' => ':NULL'), // Monday
		array('name' => 'return_discount_2', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_3', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_4', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_5', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_6', 'type' => 'decimal', 'default' => ':NULL'), // .
		array('name' => 'return_discount_7', 'type' => 'decimal', 'default' => ':NULL'), // Sunday
		array('name' => 'luggage', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'is_crossedout_price', 'type' => 'tinyint', 'default' => 0),
		array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'thumb_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'image_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'order_index', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'domain', 'type' => 'varchar', 'default' => ':NULL')
	);
	
	public $i18n = array('fleet', 'description');
	
	public static function factory($attr=array())
	{
		return new pjFleetModel($attr);
	}
}
?>