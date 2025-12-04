<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjApiCacheDistanceModel extends pjAppModel
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
	var $table = 'api_cache_distances';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'hash_key', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'duration_sec', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'distance_meters', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created_at', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public $i18n = array('service');
	
	public static function factory($attr=array())
	{
		return new pjApiCacheDistanceModel($attr);
	}

}
?>
