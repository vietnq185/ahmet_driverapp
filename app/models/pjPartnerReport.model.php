<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPartnerReportModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'partner_reports';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'partner_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'total_bookings_count', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'total_bookings_amount', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'paid_by_partner_amount', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'commission_pct', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'commission_amount', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'paid_bookings_we_made', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'billing_amount', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'status', 'type' => 'enum', 'default' => 'open'),
	    array('name' => 'pdf_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
	    return new pjPartnerReportModel($attr);
	}
}
?>