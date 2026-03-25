<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPartnerModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'partners';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'company_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'address', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'tax_number', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'company_number', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'iban', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'bic', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'notes', 'type' => 'text', 'default' => ':NULL'),
	    array('name' => 'commission_pct', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'contract_theme', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
	    return new pjPartnerModel($attr);
	}
}
?>