<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'external_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uuid', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'ref_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'client_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'driver_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'fleet_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'accept_shared_trip', 'type' => 'bool', 'default' => '0'),
		array('name' => 'location_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'pickup_type', 'type' => 'enum', 'default' => 'server'),
		array('name' => 'dropoff_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'dropoff_place_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'dropoff_type', 'type' => 'enum', 'default' => 'server'),
		array('name' => 'booking_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'return_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'return_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'luggage', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'credit_card_fee', 'type' => 'decimal', 'default' => ':NULL'),
	    array('name' => 'sub_total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'deposit', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'station_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'station_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'voucher_code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'payment_method', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'last_update', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
		
		array('name' => 'c_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_fname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_lname', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_dialing_code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'c_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_country', 'type' => 'int', 'default' => ':NULL'),
		
		array('name' => 'c_airline_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_airline_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_flight_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_flight_time', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_flight_number', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_departure_flight_time', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_destination_address', 'type' => 'varchar', 'default' => ':NULL'),
        array('name' => 'c_hotel', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_cruise_ship', 'type' => 'int', 'default' => ':NULL'),

		array('name' => 'cc_owner', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_num', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_exp', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_code', 'type' => 'varchar', 'default' => ':NULL'),

		array('name' => 'internal_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'customized_name_plate', 'type' => 'varchar', 'default' => ':NULL'),
	    array('name' => 'name_sign', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'google_map_link', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'prev_booking_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'prev_passengers', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'vehicle_id', 'type' => 'int', 'default' => '0'),
		array('name' => 'vehicle_order', 'type' => 'int', 'default' => '3'),
		array('name' => 'app_driver_id', 'type' => 'int', 'default' => '0'),
		array('name' => 'driver_status', 'type' => 'smallint', 'default' => '0'),
		array('name' => 'domain', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'driver_payment_status', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'admin_confirm_cancelled', 'type' => 'tinyint', 'default' => '0'),
		array('name' => 'pickup_google_map_link', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'dropoff_google_map_link', 'type' => 'text', 'default' => ':NULL'),
		
		array('name' => 'pickup_address', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'pickup_lat', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'pickup_lng', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'pickup_is_airport', 'type' => 'tinyint', 'default' => '0'),
		
		array('name' => 'dropoff_address', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'dropoff_lat', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'dropoff_lng', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'dropoff_is_airport', 'type' => 'tinyint', 'default' => '0'),
		
		array('name' => 'duration', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'distance', 'type' => 'int', 'default' => ':NULL'),
	    array('name' => 'notes_from_driver', 'type' => 'text', 'default' => ':NULL'),
	    array('name' => 'notes_from_office', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'platform', 'type' => 'enum', 'default' => 'newsystem'),
	    array('name' => 'locale_id', 'type' => 'int', 'default' => '1')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingModel($attr);
	}
}
?>