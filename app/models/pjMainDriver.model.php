<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjMainDriverModel extends pjAuthUserModel
{
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'role_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'password', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'type_of_driver', 'type' => 'enum', 'default' => 'own'),
	    array('name' => 'general_info_for_driver', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'last_login', 'type' => 'datetime', 'default' => ':NULL'),
    	array('name' => 'pswd_modified', 'type' => 'datetime', 'default' => ':NOW()'),
        array('name' => 'status', 'type' => 'enum', 'default' => 'T'),
        array('name' => 'is_active', 'type' => 'enum', 'default' => 'T'),
        array('name' => 'locked', 'type' => 'enum', 'default' => 'F'),
        array('name' => 'login_token', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'locale_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	protected $validate = array(
		'rules' => array(
			'phone' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'email' => array(
				'pjActionEmail' => true,
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
			'password' => array(
				'pjActionRequired' => true,
				'pjActionNotEmpty' => true
			),
		)
	);
	
	public static function factory($attr=array())
	{
		return new pjMainDriverModel($attr);
	}
}
?>