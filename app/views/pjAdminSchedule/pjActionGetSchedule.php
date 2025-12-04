<!-- First Driver -->
<?php if (isset($tpl['vehicle_arr']) && $tpl['vehicle_arr']) { 
	$_schedule_pm = __('_schedule_pm', true);
	$_booking_driver_statuses = __('_booking_driver_statuses', true);
	$_driver_payment_status = __('_driver_payment_status', true);
	$_extras_info = __('_extras_info', true);
	?>
	<div class="table-responsive">
		<table class="table pjTblVehicles" width="100%">
			<thead>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $veh) { ?>
						<th><?php echo pjSanitize::html($veh['name']);?><br/><?php echo pjSanitize::html($veh['registration_number']);?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $i => $veh) { ?>
						<td class="<?php echo $i%2 == 0 ? 'odd' : 'even';?> <?php echo $veh['schedule_status'] == 'T' ? ' pjSbOrdersVehicleActive' : ' pjSbOrdersVehicleInactive';?>" align="center">
							<h3 class="driverName" id="driver_name_<?php echo $veh['id'];?>_1"><?php echo isset($tpl['assigned_driver_name_arr'][$veh['id']][1]) ? pjSanitize::html($tpl['assigned_driver_name_arr'][$veh['id']][1]) : __('lblNoSelected', true);?></h3>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $i => $veh) { ?>
						<td class="<?php echo $i%2 == 0 ? 'odd' : 'even';?>">
							<ol class="pjSbOrders list-unstyled" data-vehicle_id="<?php echo $veh['id'];?>" data-vehicle_order="1">
							<?php if (isset($tpl['schedule_arr'][$veh['id']]) && count($tpl['schedule_arr'][$veh['id']]) > 0) { ?>
								<?php foreach ($tpl['schedule_arr'][$veh['id']] as $order) { 
									if ($order['vehicle_order'] != 1) {
										continue;	
									}
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
										<div class="pjSbOrderInner <?php echo empty($order['location_color']) || strtolower($order['location_color']) == '#ffffff' || !empty($order['return_id']) ? ($is_airport_to_city ? 'pjSbOrderInnerAirport' : 'pjSbOrderInnerLocation') : '';?> <?php echo $order['status'] == 'cancelled' ? 'pjSbOrderCancelled' : '';?>" style="<?php echo !empty($order['location_color']) && strtolower($order['location_color']) != '#ffffff' && empty($order['return_id']) ? ('border-left: 10px solid '.$order['location_color']) : '';?>">
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
												<?php if (!empty($order['notes_from_office'])) { ?>
													<span style="display: block; padding-top: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat-square-fill" viewBox="0 0 16 16"><path d="M2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.533a1 1 0 0 0 1.6 0l1.9-2.533a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/></svg></span>
												<?php } ?>
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
													$additional_info = '';
													if (isset($_extras_info[$ex['extra_id']]) && !empty($_extras_info[$ex['extra_id']])) {
														$additional_info = ' ('.$_extras_info[$ex['extra_id']].')';
													}
													foreach ($order['extra_arr'] as $ex) {
														if (!empty($ex['image_path'])) {
															$extra_arr[] = '<img class="img-responsive" src="'.$ex['domain'] . $ex['image_path'] .'" /> '.$ex['quantity'].' x '.$ex['name']. $additional_info;
														} else {
															$extra_arr[] = $ex['quantity'].' x '.$ex['name'] . $additional_info;
														}
													}
													?>
													<div><?php __('lblOrderExtras');?>: <span><?php echo implode(', ', $extra_arr);?></span></div>
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
												<?php if (!empty($order['notes_from_driver'])) { ?>
													<div class="pjSbNotesFromDriver">
														<div class="alert alert-warning"><strong><?php __('lblOrderNotesFromDriver');?>:</strong><div><?php echo nl2br($order['notes_from_driver']);?></div></div>
													</div>
												<?php } ?>
												<?php if (!empty($order['region'])) { ?>
                            						<div><small style="font-size: 10px;"><?php __('lblPickupRegion');?>: <?php echo pjSanitize::html($order['region']);?></small></div>
                            					<?php } ?>
                            					<?php if (!empty($order['dropoff_region'])) { ?>
                            						<div><small style="font-size: 10px;"><?php __('lblDropoffRegion');?>: <?php echo pjSanitize::html($order['dropoff_region']);?></small></div>
                            					<?php } ?>
                            					
                            					<?php /*
                            					<div class="text-danger">Booking date: <?php echo pjSanitize::html($order['booking_date']);?></div>
                            					<div class="text-danger">Pickup Lat: <?php echo pjSanitize::html($order['pickup_lat']);?></div>
                            					<div class="text-danger">Pickup Lng: <?php echo pjSanitize::html($order['pickup_lng']);?></div>
                            					<div class="text-danger">Dropoff Lat: <?php echo pjSanitize::html($order['dropoff_lat']);?></div>
                            					<div class="text-danger">Dropoff Lng: <?php echo pjSanitize::html($order['dropoff_lng']);?></div>
                            					<div class="text-danger">Duration: <?php echo pjSanitize::html($order['duration']);?></div>
                            					<div class="text-danger">Distance: <?php echo pjSanitize::html($order['distance']);?></div>
                            					<div class="text-danger">Platform: <?php echo pjSanitize::html($order['platform']);?></div>
                            					*/?>
                            					
                            					<?php /*if (!empty($order['empty_travel_start_time'])) { ?>
                            						<div class="text-danger">Start Time to Next Pick-up: <?php echo pjSanitize::html($order['empty_travel_start_time']);?></div>
                            					<?php } ?>
                            					<?php if (!empty($order['empty_travel_arrival_time'])) { ?>
                            						<div class="text-danger">Arrival Time at Pick-up: <?php echo pjSanitize::html($order['empty_travel_arrival_time']);?></div>
                            					<?php }*/ ?>
                            					
											</div>
											<br class="pjSbClearBoth"/>
										</div>
									</li>
								<?php } ?>
							<?php } ?>
							</ol>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $i => $veh) { ?>
						<td class="<?php echo $i%2 == 0 ? 'odd' : 'even';?>">
							<label class="coltrol-label"><?php __('lblChooseDriver');?></label>
							<div class="pjSbDriverContainer pjSbDriverContainer-<?php echo $veh['id'];?>-1">
								<select class="form-control select-item pjSbDriverSelector" name="driver_id[1][<?php echo $veh['id'];?>]" data-vehicle_id="<?php echo $veh['id'];?>" data-order="1">
									<option value="" data-driver_name="<?php __('lblNoSelected', true);?>"><?php __('lblSelect');?></option>
									<?php foreach ($tpl['driver_arr'] as $driver) { 
									    if (!isset($tpl['assigned_driver_arr'][1]) || (isset($tpl['assigned_driver_arr'][1]) && !in_array($driver['id'], $tpl['assigned_driver_arr'][1])) || (isset($tpl['driver_vehicle_arr'][$veh['id']][1]) && $tpl['driver_vehicle_arr'][$veh['id']][1] == $driver['id']))
										{
										?>
										<option value="<?php echo $driver['id'];?>" data-driver_name="<?php echo pjSanitize::html($driver['name']);?>" <?php echo isset($tpl['driver_vehicle_arr'][$veh['id']][1]) && $tpl['driver_vehicle_arr'][$veh['id']][1] == $driver['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($driver['name']);?></option>
									<?php } 
									}
									?>
								</select>
							</div>
							<div class="text-center"><button class="btn btn-primary pjSbSendSmsToDriver" type="button" data-vehicle_id="<?php echo $veh['id'];?>" data-order="1"><?php __('btnSendSmsToDriver');?></button></div>
							<div class="text-center"><button class="btn btn-primary pjSbViewTurnover" type="button" data-vehicle_id="<?php echo $veh['id'];?>" data-order="1"><?php __('btnViewTurnover');?></button></div>
						</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- Second Driver -->
	<hr/>
	<div class="table-responsive">
		<table class="table pjTblVehicles" width="100%">
			<thead>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $veh) { ?>
						<th><?php echo pjSanitize::html($veh['name']);?><br/><?php echo pjSanitize::html($veh['registration_number']);?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $i => $veh) { ?>
						<td class="<?php echo $i%2 == 0 ? 'odd' : 'even';?>  <?php echo $veh['schedule_status'] == 'T' ? ' pjSbOrdersVehicleActive' : ' pjSbOrdersVehicleInactive';?>" align="center">
							<h3 class="driverName" id="driver_name_<?php echo $veh['id'];?>_2"><?php echo isset($tpl['assigned_driver_name_arr'][$veh['id']][2]) ? pjSanitize::html($tpl['assigned_driver_name_arr'][$veh['id']][2]) : __('lblNoSelected', true);?></h3>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $i => $veh) { ?>
						<td class="<?php echo $i%2 == 0 ? 'odd' : 'even';?>">
							<ol class="pjSbOrders list-unstyled" data-vehicle_id="<?php echo $veh['id'];?>"  data-vehicle_order="2">
							<?php if (isset($tpl['schedule_arr'][$veh['id']]) && count($tpl['schedule_arr'][$veh['id']]) > 0) { ?>
								<?php foreach ($tpl['schedule_arr'][$veh['id']] as $order) { 
									if ($order['vehicle_order'] != 2) {
										continue;	
									}
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
														<?php __('lblOrderBookingUpdate');?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($order['prev_booking_date']));?><br/>
														<?php echo $is_airport_to_city ? __('lblOrderNewArrivalTime', true) : __('lblOrderNewPcikupTime', true);?>: <?php echo date($tpl['option_arr']['o_time_format'], strtotime($order['booking_date']));?>
													<?php } ?>
													<?php if ($show_change_passengers) { ?>
														<?php __('lblOrderNumUpdated');?>: <?php echo $order['prev_passengers'];?><br/>
														<?php __('lblOrderNewNumPassengers');?>: <?php echo $order['passengers'];?>
													<?php } ?>
												</div>
												<br class="pjSbClearBoth"/>
											</div>
										<?php } ?>
										<div class="pjSbOrderInner <?php echo empty($order['location_color']) || strtolower($order['location_color']) == '#ffffff' ? ($is_airport_to_city ? 'pjSbOrderInnerAirport' : 'pjSbOrderInnerLocation') : '';?>  <?php echo $order['status'] == 'cancelled' ? 'pjSbOrderCancelled' : '';?>" style="<?php echo !empty($order['location_color']) && strtolower($order['location_color']) != '#ffffff' ? ('border-left: 10px solid '.$order['location_color']) : '';?>">
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
														$extra_arr[] = $ex['quantity'].'x'.$ex['name'];
													}
													?>
													<div><?php __('lblOrderExtras');?>: <span><?php echo implode(', ', $extra_arr);?></span></div>
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
												<?php if (!empty($order['notes_from_driver'])) { ?>
													<div class="pjSbNotesFromDriver">
														<div class="alert alert-warning"><strong><?php __('lblOrderNotesFromDriver');?>:</strong><div><?php echo nl2br($order['notes_from_driver']);?></div></div>
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
								<?php } ?>
							<?php } ?>
							</ol>
						</td>
					<?php } ?>
				</tr>
				<tr>
					<?php foreach ($tpl['vehicle_arr'] as $i => $veh) { ?>
						<td class="<?php echo $i%2 == 0 ? 'odd' : 'even';?>">
							<label class="coltrol-label"><?php __('lblChooseDriver');?></label>
							<div class="pjSbDriverContainer pjSbDriverContainer-<?php echo $veh['id'];?>-2">
								<select class="form-control select-item pjSbDriverSelector" name="driver_id[2][<?php echo $veh['id'];?>]" data-vehicle_id="<?php echo $veh['id'];?>" data-order="2">
									<option value="" data-driver_name="<?php __('lblNoSelected', true);?>"><?php __('lblSelect');?></option>
									<?php foreach ($tpl['driver_arr'] as $driver) { 
									    if (!isset($tpl['assigned_driver_arr'][2]) || (isset($tpl['assigned_driver_arr'][2]) && !in_array($driver['id'], $tpl['assigned_driver_arr'][2])) || (isset($tpl['driver_vehicle_arr'][$veh['id']][2]) && $tpl['driver_vehicle_arr'][$veh['id']][2] == $driver['id']))
										{ ?>
										<option value="<?php echo $driver['id'];?>" data-driver_name="<?php echo pjSanitize::html($driver['name']);?>" <?php echo isset($tpl['driver_vehicle_arr'][$veh['id']][2]) && $tpl['driver_vehicle_arr'][$veh['id']][2] == $driver['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($driver['name']);?></option>
									<?php } 
									}
									?>
								</select>
							</div>
							<div class="text-center"><button class="btn btn-primary pjSbSendSmsToDriver" type="button" data-vehicle_id="<?php echo $veh['id'];?>" data-order="2"><?php __('btnSendSmsToDriver');?></button></div>
							<div class="text-center"><button class="btn btn-primary pjSbViewTurnover" type="button" data-vehicle_id="<?php echo $veh['id'];?>" data-order="2"><?php __('btnViewTurnover');?></button></div>
						</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
	</div>
<?php } ?>