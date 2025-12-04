<?php if (isset($tpl['order_arr']) && $tpl['order_arr']) { 
	$_schedule_pm = __('_schedule_pm', true);
	$_booking_driver_statuses = __('_booking_driver_statuses', true);
	$_driver_payment_status = __('_driver_payment_status', true);
	foreach ($tpl['order_arr'] as $order) { 
		$is_airport_to_city = false;
		if ((int)$order['return_id'] > 0 && (int)$order['return_pickup_is_airport'] == 1 && (int)$order['return_dropoff_is_airport'] == 0) {
			$is_airport_to_city = true;
		} else if ((int)$order['pickup_is_airport'] == 1 && (int)$order['dropoff_is_airport'] == 0) {
			$is_airport_to_city = true;
		}
		$show_change_time = $show_change_passengers = false;
		if (!empty($order['prev_booking_date']) && date('His', strtotime($order['prev_booking_date'])) != date('His', strtotime($order['booking_date']))) {
			$show_change_time = true;
		}
		if (!empty($order['prev_passengers']) && $order['prev_passengers'] != $order['passengers']) {
			$show_change_passengers = true;
		}
		?>
		<li class="pjSbOrder" data-booking_id="<?php echo $order['id'];?>">
			<?php if ($show_change_time || $show_change_passengers) { ?>
				<div class="alert alert-warning">
					<button type="button" class="close pjSbCloseInfoChangeTime" data-id="<?php echo $order['id'];?>" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
					<i class="fa fa-info-circle"></i>
					<div class="pjSbPickupTime">
						<?php if ($show_change_time) { ?>
							<div><?php __('lblOrderBookingUpdate');?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($order['prev_booking_date']));?></div>
							<div><?php echo $is_airport_to_city ? __('lblOrderNewArrivalTime', true) : __('lblOrderNewPcikupTime', true);?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($order['booking_date']));?></div>
						<?php } ?>
						<?php if ($show_change_passengers) { ?>
							<div><?php __('lblOrderNumUpdated');?>: <?php echo $order['prev_passengers'];?></div>
							<div><?php __('lblOrderNewNumPassengers');?>: <?php echo $order['passengers'];?></div>
						<?php } ?>
					</div>
					<br class="pjSbClearBoth"/>
				</div>
			<?php } ?>
			<div class="pjSbOrderInner <?php echo empty($order['location_color']) || strtolower($order['location_color']) == '#ffffff' || !empty($order['return_id']) ? ($is_airport_to_city ? 'pjSbOrderInnerAirport' : 'pjSbOrderInnerLocation') : '';?>  <?php echo $order['status'] == 'cancelled' ? 'pjSbOrderCancelled' : '';?>" style="<?php echo !empty($order['location_color']) && strtolower($order['location_color']) != '#ffffff' && empty($order['return_id']) ? ('border-left: 10px solid '.$order['location_color']) : '';?>">
				<?php if ($order['status'] == 'cancelled') { ?>
					<div class="pjSbOrderCancelledInner">
						<a href="javascript:void(0);" data-id="<?php echo $order['id'];?>" class="pjSbBtnRemoveBooking"><?php __('btnCancelledOK');?></a>
					</div>
				<?php } ?>
				<div class="pull-left pjSbOrderSign">
					<?php if ($is_airport_to_city) { ?>
					<i class="fa fa-plane" aria-hidden="true"></i>                                						
					<?php } else { ?>
						<i class="fa fa-map-marker" aria-hidden="true"></i>
					<?php } ?>
					<a href="javascript:void(0);" data-id="<?php echo $order['id'];?>" class="pjSbViewOrder"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
				</div>
				<div class="pull-left pjSbOrderDetails"> 
					<?php if ((int)$order['driver_status'] > 0) { ?>
						<div class="pjSbOrderDriverStatus pjSbOrderDriverStatus<?php echo $order['driver_status'];?>">
							<?php echo @$_booking_driver_statuses[$order['driver_status']];?>
						</div>
					<?php } ?>                               						
					<div><span><?php echo date($tpl['option_arr']['o_time_format'], strtotime($order['booking_date']));?></span></div>
					<?php if ($is_airport_to_city) { ?>
						<div><?php __('lblOrderFlight');?>: <span><?php echo stripslashes($order['c_flight_number']);?></span></div>
					<?php } ?>
					<?php if(!empty($order['return_id'])) { ?>
						<div><?php __('lblOrderFrom');?>: <span><?php echo stripslashes($order['location2']);?></span></div>
						<div><?php __('lblOrderTo');?>: <span><?php echo stripslashes($order['dropoff2']);?></span></div>
					<?php } else { ?>
						<div><?php __('lblOrderFrom');?>: <span><?php echo stripslashes($order['location']);?></span></div>
						<div><?php __('lblOrderTo');?>: <span><?php echo stripslashes($order['dropoff']);?></span></div>
					<?php } ?>
					<div><?php __('lblOrderClient');?>: <span><?php echo pjSanitize::html($order['c_fname'].' '.$order['c_lname']);?></span></div>
					<div><?php __('lblOrderVehicle');?>: <span><?php echo pjSanitize::html($order['fleet']);?></span></div>
					<div><?php __('lblOrderPassengers');?>: <span><?php echo pjSanitize::html($order['passengers']);?></span></div>
					<?php if (isset($order['extra_arr']) && count($order['extra_arr']) > 0) { 
						$extra_arr = array();
						foreach ($order['extra_arr'] as $ex) {
						    if (!empty($ex['image_path'])) {
						        $extra_arr[] = $ex['quantity'].'x'.$ex['name'] . ' <img src="'.$ex['domain'] . $ex['image_path'].'" />';
						    } else {
                                $extra_arr[] = $ex['quantity'].'x'.$ex['name'];
						    }
						}
						?>
						<div class="pjSbOrderExtras"><?php __('lblOrderExtras');?>: <span><?php echo implode(', ', $extra_arr);?></span></div>
					<?php } ?>
					<div>
						<?php echo $tpl['option_arr']['o_currency'];?>: <span><?php echo pjCurrency::formatPriceOnly($order['price']);?> <?php echo @$_schedule_pm[$order['payment_method']];?></span>
						<?php if (in_array($order['payment_method'], array('creditcard','bank','saferpay'))) { ?>
							<span class="driver-status"><?php __('lblPaid');?></span>
						<?php } elseif (!empty($order['driver_payment_status'])) { ?>
							<span class="driver-status"><?php echo @$_driver_payment_status[$order['driver_payment_status']];?></span>
						<?php } ?>
					</div>
					<div><?php __('lblOrderInternalNotes');?>: <span><?php echo !empty($order['internal_notes']) ? nl2br($order['internal_notes']) : '';?></span></div>
					<div><?php __('lblOrderID');?>: <span><?php echo !empty($order['return_uuid']) ? pjSanitize::html($order['return_uuid']) : pjSanitize::html($order['uuid']);?></span></div>
					<?php if (!empty($order['region'])) { ?>
						<div><small style="font-size: 10px;"><?php __('lblPickupRegion');?>: <?php echo pjSanitize::html($order['region']);?></small></div>
					<?php } ?>
					<?php if (!empty($order['dropoff_region'])) { ?>
						<div><small style="font-size: 10px;"><?php __('lblDropoffRegion');?>: <?php echo pjSanitize::html($order['dropoff_region']);?></small></div>
					<?php } ?>
					
					<?php /*
					<div><small style="font-size: 10px;">Booking date: <?php echo pjSanitize::html($order['booking_date']);?></small></div>
					<div><small style="font-size: 10px;">Pickup Lat: <?php echo pjSanitize::html($order['pickup_lat']);?></small></div>
					<div><small style="font-size: 10px;">Pickup Lng: <?php echo pjSanitize::html($order['pickup_lng']);?></small></div>
					<div><small style="font-size: 10px;">Dropoff Lat: <?php echo pjSanitize::html($order['dropoff_lat']);?></small></div>
					<div><small style="font-size: 10px;">Dropoff Lng: <?php echo pjSanitize::html($order['dropoff_lng']);?></small></div>
					<div><small style="font-size: 10px;">Duration: <?php echo pjSanitize::html($order['duration']);?></small></div>
					<div><small style="font-size: 10px;">Distance: <?php echo pjSanitize::html($order['distance']);?></small></div>
					<div><small style="font-size: 10px;">Platform: <?php echo pjSanitize::html($order['platform']);?></small></div>
					<div><small style="font-size: 10px;">External ID: <?php echo pjSanitize::html($order['external_id']);?></small></div>
					<div><small style="font-size: 10px;">Ref ID: <?php echo pjSanitize::html($order['ref_id']);?></small></div>
					*/ ?>
				</div>
				<br class="pjSbClearBoth"/>
			</div>
		</li>
	<?php } ?>
<?php } ?>