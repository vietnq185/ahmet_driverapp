<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjLogModel extends pjAppModel
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
	var $table = 'logs';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'user_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'action', 'type' => 'text', 'default' => ':NULL'),
	    array('name' => 'booking_date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	
	public static function factory($attr=array())
	{
		return new pjLogModel($attr);
	}
}
?>
