<div class="row">
	<div class="col-xs-12 pjSbDriverSchedueTitle">
		<?php echo ($tpl['date'] == date('Y-m-d') ? __('lblToday', true) : __('lblScheduleDate', true)).': '.date($tpl['option_arr']['o_date_format'], strtotime($tpl['date']));?>
	</div>
	<div class="col-xs-12 pjSbDriverSchedueTitle">
		<?php echo __('lblScheduleVehicle', true).': '.((isset($tpl['vehicle_arr']) && $tpl['vehicle_arr']) ? pjSanitize::html(@$tpl['vehicle_arr']['name']) : __('lblNotAssigned', true));?>
	</div>
</div>
<ul class="pjSbOrdersList list-unstyled row">
	<?php if ($tpl['order_arr']) { 
		$_schedule_pm = __('_schedule_pm', true);
		$_booking_driver_statuses = __('_booking_driver_statuses', true);
		$_driver_payment_status = __('_driver_payment_status', true);
		$_extras_info = __('_extras_info', true); 
		foreach ($tpl['order_arr'] as $k => $order) { 
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
			<li class="pjSbOrder col-sm-12 col-md-6 col-lg-4" data-booking_id="<?php echo $order['id'];?>">
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
					<div class="pjSbLinkViewOrder" data-id="<?php echo $order['id'];?>">
						<a href="javascript:void(0);"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
					</div>
					<div class="pull-left pjSbOrderSign">
						<?php if ($is_airport_to_city) { ?>
						<i class="fa fa-plane" aria-hidden="true"></i>                                						
						<?php } else { ?>
							<i class="fa fa-map-marker" aria-hidden="true"></i>
						<?php } ?>
					</div>
					<div class="pull-left pjSbOrderDetails"> 
						<?php if ((int)$order['driver_status'] > 0) { ?>
							<div class="pjSbOrderDriverStatus pjSbOrderDriverStatus<?php echo $order['driver_status'];?>">
								<?php echo @$_booking_driver_statuses[$order['driver_status']];?>
								<a href="javascript:void(0);" title="<?php __('btnRemoveDriverStatus');?>" data-id="<?php echo $order['id'];?>" class="pjSbOrderRemoveDriverStatus"><span aria-hidden="true">&times;</span></a>
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
						<div><?php __('lblOrderClient');?>: <span><?php echo pjSanitize::html($order['fname'].' '.$order['lname']);?></span></div>
						<div><?php __('lblOrderVehicle');?>: <span><?php echo pjSanitize::html($order['fleet']);?></span></div>
						<div><?php __('lblOrderPassengers');?>: <span><?php echo pjSanitize::html($order['passengers']);?></span></div>
						<?php if (isset($order['extra_arr']) && count($order['extra_arr']) > 0) { 
							$extra_arr = array();
							foreach ($order['extra_arr'] as $ex) {
								if (in_array($ex['extra_id'], array(2,3,4))) {
									$extra_arr[] = $ex['quantity'].'x'.$ex['name'].' ('.@$_extras_info[$ex['extra_id']].' <img src="'.PJ_IMG_PATH.'backend/extra-'.$ex['extra_id'].'.png" />)';
								} else {
									$extra_arr[] = $ex['quantity'].'x'.$ex['name'];
								}
							}
							?>
							<div><?php __('lblOrderExtras');?>: <span><?php echo implode(', ', $extra_arr);?></span></div>
						<?php } ?>

						<div class="pjSbOrderPaymentStatus">
							<?php if ((int)$order['driver_payment_status'] == 2) { ?>
    							<span class="pjSbOrderPaymentHaleCash pjSbOrderPaymentHaleCashRegister<?php echo (int)$order['is_enter_hale_cash_register'];?>"><i class="fa fa-tablet"></i></span>
    						<?php } ?>
							<?php echo $tpl['option_arr']['o_currency'];?>: <span><?php echo !in_array($order['payment_method'], array('creditcard','bank','saferpay')) ? pjCurrency::formatPriceOnly($order['price']) : '';?> <?php echo @$_schedule_pm[$order['payment_method']];?></span>
    						<?php if (in_array($order['payment_method'], array('creditcard','bank','saferpay'))) { ?>
    							<span class="driver-status"><?php __('lblPaid');?></span>
    						<?php } elseif (!empty($order['driver_payment_status'])) { ?>
    							<span class="driver-status"><?php echo @$_driver_payment_status[$order['driver_payment_status']];?></span>
    						<?php } ?>
						</div>						

						<div><?php __('lblOrderInternalNotes');?>: <span><?php echo !empty($order['internal_notes']) ? nl2br($order['internal_notes']) : '';?></span></div>
						<div><?php __('lblOrderID');?>: <span><?php echo !empty($order['return_uuid']) ? pjSanitize::html($order['return_uuid']) : pjSanitize::html($order['uuid']);?></span></div>
						<?php if (!empty($order['notes_from_office'])) { ?>
							<div class="pjSbNotesForDriver">
								<div class="alert alert-warning"><strong><?php __('lblOrderImportantNotesFromOffice');?>:</strong><div><?php echo nl2br($order['notes_from_office']);?></div></div>
							</div>
						<?php } ?>
						<?php if (!empty($order['region'])) { ?>
    						<div><small style="font-size: 10px;"><?php __('lblPickupRegion');?>: <?php echo pjSanitize::html($order['region']);?></small></div>
    					<?php } ?>
    					<?php if (!empty($order['dropoff_region'])) { ?>
    						<div><small style="font-size: 10px;"><?php __('lblDropoffRegion');?>: <?php echo pjSanitize::html($order['dropoff_region']);?></small></div>
    					<?php } ?>
					</div>
					<br class="pjSbClearBoth"/>
				</div>
			</li>
			<?php if ($k > 0 && $k%2 == 0) { ?>
				<li class="clearfix visible-lg"></li>
			<?php } ?>
			<?php if ($k > 0 && $k%2 == 1) { ?>
				<li class="clearfix visible-md"></li>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</ul>