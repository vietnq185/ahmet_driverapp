<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDriverJobStatusModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'driver_job_status';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'driver_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
	    array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjDriverJobStatusModel($attr);
	}
}
?>