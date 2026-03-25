<?php 
$_schedule_pm = __('_schedule_pm', true);
$_booking_driver_statuses = __('_booking_driver_statuses', true);
$_driver_payment_status = __('_driver_payment_status', true);
$_extras_info = __('_extras_info', true); 
$personal_titles = __('personal_titles', true);
$_transfer_types = __('_transfer_types', true);
$show_change_time = $show_change_passengers = false;
if (!empty($tpl['arr']['prev_booking_date']) && date('His', strtotime($tpl['arr']['prev_booking_date'])) != date('His', strtotime($tpl['arr']['booking_date']))) {
	$show_change_time = true;
}
if (!empty($tpl['arr']['prev_passengers']) && $tpl['arr']['prev_passengers'] != $tpl['arr']['passengers']) {
	$show_change_passengers = true;
}
?>
<div class="pjSbBlock">
	<?php if ($show_change_time || $show_change_passengers) { ?>
		<div class="alert alert-warning">
			<button type="button" class="close pjSbCloseInfoChangeTime" data-id="<?php echo $tpl['arr']['id'];?>" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
			<i class="fa fa-info-circle"></i>
			<div class="pjSbPickupTime">
				<?php if ($show_change_time) { ?>
					<div><?php __('lblOrderBookingUpdate');?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['prev_booking_date']));?></div>
					<div><?php echo $is_airport_to_city ? __('lblOrderNewArrivalTime', true) : __('lblOrderNewPcikupTime', true);?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date']));?></div>
				<?php } ?>
				<?php if ($show_change_passengers) { ?>
					<div><?php __('lblOrderNumUpdated');?>: <?php echo $tpl['arr']['prev_passengers'];?></div>
					<div><?php __('lblOrderNewNumPassengers');?>: <?php echo $tpl['arr']['passengers'];?></div>
				<?php } ?>
			</div>
			<br class="pjSbClearBoth"/>
		</div>
	<?php } ?>
	<div class="pjSbBlockInner">
		<div class="pull-left pjSbSign">
			<i class="fa fa-clock-o" aria-hidden="true"></i>
		</div>
		<div class="pull-left pjSbOrderInfo">
			<h3><?php __('lblPickupTime');?></h3>
			<?php if (!$controller->isDriver()) { ?>
				<h3><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date']));?>  <a href="javascript: void(0);" class="pjSbBtnChangePickupTime btn btn-primary btn-outline btn-sm" data-id="<?php echo $tpl['arr']['id'];?>"><?php __('btnChange');?></a></h3>
			<?php } else { ?>
				<h3><?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date']));?></h3>
			<?php } ?>
			<?php if (!empty($tpl['arr']['c_departure_flight_time'])) { ?>
				<p><?php __('lblFlightDeparture');?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['c_departure_flight_time'].':00'));?></p>
			<?php } ?>
		</div>
		<br class="pjSbClearBoth"/>
	</div>
</div>
<div class="pjSbBlock">
	<div class="pjSbBlockInner">
		<div class="pull-left pjSbSign">
			<i class="fa fa-map-marker" aria-hidden="true"></i>
		</div>
		<div class="pull-left pjSbOrderInfo">
			<h3><?php __('lblPickupLocation');?></h3>
			<p><?php echo (int)$tpl['arr']['return_id'] > 0 ? pjSanitize::html($tpl['arr']['location2']) : pjSanitize::html($tpl['arr']['location']);?></p>
			<?php if (!empty($tpl['arr']['c_address'])) { ?>
				<p><?php echo pjSanitize::html($tpl['arr']['c_address']);?></p>
			<?php } ?>
			<?php if (!empty($tpl['arr']['pickup_google_map_link'])) { ?>
				<p><a class="btn btn-primary btn-block" href="<?php echo pjSanitize::html($tpl['arr']['pickup_google_map_link']);?>" target="_blank"><?php __('btnOpenGoogleMaps');?></a></p>
			<?php } ?>
		</div>
		<br class="pjSbClearBoth"/>
	</div>
</div>
<div class="pjSbBlock">
	<div class="pjSbBlockInner">
		<div class="pull-left pjSbSign">
			<i class="fa fa-map-marker" aria-hidden="true"></i>
		</div>
		<div class="pull-left pjSbOrderInfo">
			<h3><?php __('lblDropoffLocation');?></h3>
			<p><?php echo (int)$tpl['arr']['return_id'] > 0 ? pjSanitize::html($tpl['arr']['dropoff2']) : pjSanitize::html($tpl['arr']['dropoff']);?></p>			
			<?php if (!empty($tpl['arr']['c_destination_address'])) { ?>
				<p><?php echo pjSanitize::html($tpl['arr']['c_destination_address']);?></p>
			<?php } ?>
			<?php if (!empty($tpl['arr']['dropoff_google_map_link'])) { ?>
				<p><a class="btn btn-primary btn-block" href="<?php echo pjSanitize::html($tpl['arr']['dropoff_google_map_link']);?>" target="_blank"><?php __('btnOpenGoogleMaps');?></a></p>
			<?php } ?>
		</div>
		<br class="pjSbClearBoth"/>
	</div>
</div>
<div class="pjSbBlock">
	<div class="pjSbBlockInner">
		<div class="pull-left pjSbSign">
			<i class="fa fa-user" aria-hidden="true"></i>
		</div>
		<div class="pull-left pjSbOrderInfo">
			<?php 
			$name_arr = array();
			if (!empty($tpl['arr']['c_title'])) {
				$name_arr[] = @$personal_titles[$tpl['arr']['c_title']];
			}
			if (!empty($tpl['arr']['c_fname'])) {
				$name_arr[] = pjSanitize::html($tpl['arr']['c_fname']);
			}
			if (!empty($tpl['arr']['c_lname'])) {
				$name_arr[] = pjSanitize::html($tpl['arr']['c_lname']);
			}
			?>
			<h3><?php echo implode(" ", $name_arr);?></h3>
			<?php if (!empty($tpl['arr']['c_phone'])) { ?>
				<p><?php __('lblPhoneNumber');?>: <?php echo pjSanitize::html($tpl['arr']['c_dialing_code']);?> <?php echo pjSanitize::html($tpl['arr']['c_phone']);?></p>
			<?php } ?>
			
			<?php if ((int)$tpl['cnt_whatsapp_message'] > 0) { ?>
				<p><a class="btn btn-primary btn-block btnWhatsappMessage" data-id="<?php echo $tpl['arr']['id'];?>" href="javascript:void(0);"><?php __('btnWhatsApp');?></a></p>
			<?php } ?>

			<?php if (!empty($tpl['arr']['c_country_title'])) { ?>
				<p><?php __('lblCountry');?>: <?php echo pjSanitize::html($tpl['arr']['c_country_title']);?></p>
			<?php } ?>
			
			<?php 
			$name_sign_url = PJ_INSTALL_URL.'index.php?controller=pjAdminSchedule&amp;action=pjActionNameSign&amp;hash='.sha1($tpl['arr']['id'].PJ_SALT);
			if (!empty($tpl['arr']['name_sign'])) {
			    $name_sign_url = PJ_INSTALL_URL.$tpl['arr']['name_sign'];
			}
			?>
			<p><a class="btn btn-primary btn-block" href="<?php echo $name_sign_url; ?>" target="_blank"><?php __('btnOpenNameSign');?></a></p>
		</div>
		<br class="pjSbClearBoth"/>
	</div>
</div>
<div class="pjSbBlock">
	<div class="pjSbBlockInner">
		<div class="pull-left pjSbSign">
			&nbsp;
		</div>
		<div class="pull-left pjSbOrderInfo">
			<p><?php __('lblOrderPassengers');?>: <?php echo (int)$tpl['arr']['passengers'];?></p>
			<?php if (isset($tpl['arr']['extra_arr']) && count($tpl['arr']['extra_arr']) > 0) {
				$extra_arr = array();
				foreach ($tpl['arr']['extra_arr'] as $ex) {
				    $additional_info = '';
				    if (isset($_extras_info[$ex['extra_id']]) && !empty($_extras_info[$ex['extra_id']])) {
				        $additional_info = ' ('.$_extras_info[$ex['extra_id']].')';
				    }
					if (!empty($ex['image_path'])) {
						$extra_arr[] = '<img class="img-responsive" src="'.$ex['domain'] . $ex['image_path'] .'" /> '.$ex['quantity'].' x '.$ex['name']. $additional_info;
					} else {
						$extra_arr[] = $ex['quantity'].' x '.$ex['name'] . $additional_info;
					}
				}
				?>
				<p><?php __('lblOrderExtras');?>: <?php echo implode(', ', $extra_arr);?></p>
			<?php } ?>
		</div>
		<br class="pjSbClearBoth"/>
	</div>
</div>
<div class="pjSbBlock">
	<div class="pjSbBlockInner">
		<div class="pull-left pjSbSign">
			&nbsp;
		</div>
		<div class="pull-left pjSbOrderInfo">
			<?php if ($controller->isDriver() && (in_array($tpl['arr']['payment_method'], array('creditcard','bank','saferpay')))) { ?>
				<h3><?php echo __('lblPaid', true);?></h3>
			<?php } else { ?>
				<h3><?php echo $tpl['option_arr']['o_currency'];?> <?php echo pjCurrency::formatPriceOnly($tpl['arr']['price']).' '.@$_schedule_pm[$tpl['arr']['payment_method']];?> <?php echo in_array($tpl['arr']['payment_method'], array('creditcard','bank','saferpay')) ? __('lblPaid', true) : '';?></h3>
			<?php } ?>
		</div>
		<br class="pjSbClearBoth"/>
	</div>
</div>
<?php if (!empty($tpl['arr']['c_notes'])) { ?>
	<div class="pjSbBlock">
		<div class="pjSbBlockInner">
			<div class="pull-left pjSbSign">
				&nbsp;
			</div>
			<div class="pull-left pjSbOrderInfo">
				<p><?php __('lblFurtherInformation');?>:</p>
				<p><?php echo nl2br($tpl['arr']['c_notes']);?></p>
			</div>
			<br class="pjSbClearBoth"/>
		</div>
	</div>
<?php } ?>
<?php if (!in_array($tpl['arr']['payment_method'], array('creditcard','bank','saferpay'))) { ?>
	<div class="row form-group">
		<div class="col-xs-12">
			<label class="control-label"><?php __('lblPayment');?></label>
			<select class="form-control pjSbDriverSelectPaymentStatus" name="driver_payment_status">
				<option value="" data-booking_id="<?php echo $tpl['arr']['id'];?>"><?php __('lblSelect');?></option>
				<?php foreach (__('_driver_payment_status', true) as $k => $v) { 
					if (((empty($tpl['arr']['return_date']) && (int)$tpl['arr']['return_id'] <= 0) || (int)$tpl['arr']['return_id'] > 0) && in_array($k, array(3,5,6))) {
						continue;	
					}
					?>
					<option value="<?php echo $k;?>" <?php echo $tpl['arr']['driver_payment_status'] == $k ? 'selected="selected"' : '';?> data-booking_id="<?php echo $tpl['arr']['id'];?>"><?php echo sprintf($v, pjCurrency::formatPrice($tpl['arr']['price'] + $tpl['arr']['duplicate_price']));?></option>
				<?php } ?>
			</select>
		</div>
	</div>
<?php } ?>
<div class="row form-group">
	<div class="col-xs-12">
		<label class="control-label"><?php __('lblBookingStatus');?></label>
		<select class="form-control pjSbDriverSelectBookingStatus" name="driver_status">
			<option value=""><?php __('lblSelect');?></option>
			<?php foreach (__('_booking_driver_statuses', true) as $k => $v) { ?>
				<option value="<?php echo $k;?>" <?php echo $tpl['arr']['driver_status'] == $k ? 'selected="selected"' : '';?> data-booking_id="<?php echo $tpl['arr']['id'];?>"><?php echo $v;?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if ($controller->isDriver()) { ?>
<div class="row form-group">
	<div class="col-xs-12">
		<label class="control-label"><?php __('lblOrderNotesFromDriver');?></label>
		<textarea name="notes_from_driver" id="notes_from_driver" class="form-control pjSbDriverAddNotes" rows="4"><?php echo stripslashes($tpl['arr']['notes_from_driver']); ?></textarea>
	</div>
</div>
<?php } ?>
<br/><br/>
<div class="row">
	<div class="col-xs-12 text-center"><?php __('lblTransferType');?>: <?php echo (!empty($tpl['arr']['return_date']) || (int)$tpl['arr']['return_id'] > 0) ? $_transfer_types[2] : $_transfer_types[1];?></div>
</div>