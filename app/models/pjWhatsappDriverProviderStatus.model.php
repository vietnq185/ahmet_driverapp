<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWhatsappDriverProviderStatusModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'whatsapp_driver_provider_status';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'provider_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'driver_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'unread_count', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'last_message_at', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjWhatsappDriverProviderStatusModel($attr);
	}
}
?>