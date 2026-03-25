<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWhatsappChatHistoryModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'whatsapp_chat_history';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'provider_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'wa_message_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'driver_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'direction', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'content', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'created_at', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
		return new pjWhatsappChatHistoryModel($attr);
	}
}
?>