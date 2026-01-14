<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjVehicleMaintranceFileModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vehicles_maintrance_files';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'tmp_hash', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'foreign_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'vehicle_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'filename', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'small_path', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'source_path', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
	    array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	protected $validate = array(
	    
	);
	
	public $i18n = array('name');
	
	public static function factory($attr=array())
	{
	    return new pjVehicleMaintranceFileModel($attr);
	}
}
?>