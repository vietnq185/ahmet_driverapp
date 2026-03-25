<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPartnerReportBookingAmountModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'partner_reports_bookings_amount';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'tmp_hash', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'partner_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'report_id', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'total_cash', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'total_cc', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'total_paid', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()')
	);
	
	public static function factory($attr=array())
	{
	    return new pjPartnerReportBookingAmountModel($attr);
	}
}
?>