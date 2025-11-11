<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleModel extends pjAppModel
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
	var $table = 'vehicles';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'registration_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'seats', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'order', 'type' => 'int', 'default' => ':NULL'),	    
	    array('name' => 'type', 'type' => 'enum', 'default' => 'own'),
	    array('name' => 'maker_modell', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'vin', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'model_year', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'tuv', 'type' => 'date', 'default' => ':NULL'),	    
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $validate = array(
	
	);
	
	public $i18n = array('name');

	public static function factory($attr=array())
	{
		return new pjVehicleModel($attr);
	}
}
?>