<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$show_period = 'false';
if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
{
    $show_period = 'true';
}
?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<h2>
						<?php 
						if ($controller->isDriver()) {
							__('infoDriverScheduleTitle');
						} else {
							__('infoScheduleTitle');	
						} 
						?>
					</h2>
				</div>
			</div>
			<p class="m-b-none">
				<i class="fa fa-info-circle"></i>
				<?php 
				if ($controller->isDriver()) {
					__('infoDriverScheduleBody');
				} else {
					__('infoScheduleBody');	
				} 
				?>
			</p>
		</div>
	</div>
	
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">
			<?php if (!empty($tpl['driver_arr']['general_info_for_driver'])) { ?>
				<div class="alert alert-warning generalInfoForDriver"><?php echo nl2br($tpl['driver_arr']['general_info_for_driver']);?></div>
			<?php } ?>
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<?php
					if ($controller->isDriver()) {
						include PJ_VIEWS_PATH.'pjAdminSchedule/elements/driver.php';
					} else {
						include PJ_VIEWS_PATH.'pjAdminSchedule/elements/general.php';
					}
					?>
				</div>
			</div>
		</div><!-- /.col-lg-8 -->
	</div>
	
	<div class="modal inmodal fade" id="modalSms" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalTurnover" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalViewOrder" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalAddNotesForDriver" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalWhatsappSms" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalChangePickupTime" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog modal-sm">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalAssignOrders" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	        	<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>
					<h3 class="modal-title"><?php __('btnAssignOrders'); ?></h3>
				</div>
	        	<div class="row">
	        		<div class="col-md-9 col-sm-12 col-xs-12">
	        			<div class="ibox">
            	        	<div class="ibox-content">
            	        		<div class="row">
            						<div class="col-lg-6">
            							<form action="" method="get" class="form-horizontal frm-filter-orders">
                                            <div class="input-group">
            									<input type="text" name="q" placeholder="<?php __('btnSearch', false, true); ?>" class="form-control">
                                                <div class="input-group-btn">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
            						</div><!-- /.col-lg-6 -->
            					</div><!-- /.row -->
            	        		<div id="grid_orders"></div>
            	        	</div>
            	        </div>
	        		</div>
	        		<div class="col-md-3 col-sm-13 col-xs-12">
	        			<div class="selecCarsContainer">
	        				<form action="" method="post" class="" id="frmAssignMultiOrders">
	        					<input type="hidden" name="assign_orders" value="1" />
    	        				<div class="form-group">
                    				<label class="control-label"><?php __('lblOrderCar')?>:</label>	
                    				<select name="vehicle_id" id="vehicle_id" class="form-control select-vehicle required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                    					<option value="">-- <?php __('lblChoose');?> --</option>
                    					<?php foreach ($tpl['vehicle_arr'] as $veh) { ?>
                    						<option value="<?php echo $veh['id'];?>"><?php echo pjSanitize::html($veh['name']);?> | <?php echo pjSanitize::html($veh['registration_number']);?></option>
                    					<?php } ?>
                    				</select>
                    			</div>
                    			
                    			<div class="form-group">
                    				<label class="control-label"><?php __('lblOrderShift')?>:</label>	
                    				<select name="vehicle_order" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                    					<?php foreach (__('_order_shift', true) as $k => $v) { ?>
                    						<option value="<?php echo $k;?>"><?php echo $v;?></option>
                    					<?php } ?>
                    				</select>
                    			</div>
                    			
                    			<div class="form-group">
                    				<input type="hidden" name="order_ids" id="order_ids" class="required" data-msg-required="<?php __('lblAssignOrderError', false, true);?>" />
                    			</div>
                			
                    			<div class="form-group">
                    				<button class="btn btn-primary" type="submit"><?php __('btnAssign');?></button>&nbsp;&nbsp;&nbsp;&nbsp;
                    				<button class="btn btn-default" type="button" data-dismiss="modal"><?php __('btnClose');?></button>
                    			</div>
                    			
                    			<div class="alert alert-success" style="display: none;">
                    			
                    			</div>
                			</form>
	        			</div>
	        		</div>
	        	</div>
	        </div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalLocateVehicleOnMap" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog modal-lg">
	        <div class="modal-content">
	        	<div class="modal-body">
                	<div id="map-panel">
                        <div id="map"></div>
                    </div>
              	</div>
              
              	<div class="modal-footer">
              		<button class="btn btn-default" type="button" data-dismiss="modal"><?php __('btnClose');?></button>
              	</div>
	        </div>
	    </div>
	</div>
	
	<div id="captchaContainer" style="display: none;">
        <div class="aiWithCaptcha">
        	<div class="form-group">
                <div class="input-group input-group-captcha">
                    <input type="text" name="ai_process_captcha" class="form-control form-control-lg required" placeholder="<?php __('plugin_base_login_captcha', false, true); ?>" autocomplete="off" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-remote="<?php __('plugin_base_captcha_incorrect', false, true);?>">
    
                    <span class="input-group-addon">
                        <img id="captchaImage" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminSchedule&amp;action=pjActionCaptcha&amp;rand=<?php echo rand(1, 99999); ?>" alt="Captcha" class="captcha captchaImage" title="<?php __('plugin_base_captcha_reload', false, true); ?>">
                    </span>
                </div>
                <span class="ai_process_captcha_err"></span>
            </div><!-- /.form-group -->
        </div>
    </div>
                
	<div id="popupMessage" style="display: none;"><?php echo isset($tpl['popup_message']) ? implode('<br/>', $tpl['popup_message']) : '';?></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	var myLabel = myLabel || {};
	myLabel.choose = "<?php __('lblChoose');?>";
	myLabel.months = "<?php echo implode("_", $months);?>";
	myLabel.days = "<?php echo implode("_", $short_days);?>";
	myLabel.alert_title = "<?php echo __('del_booking_title');?>";
	myLabel.alert_text = "<?php echo __('del_booking_body');?>";
	myLabel.btn_delete = "<?php echo __('btnDelete');?>";
	myLabel.btn_cancel = "<?php echo __('btnCancel');?>";
	myLabel.isDriver = <?php echo $controller->isDriver() ? 'true' : 'false';?>;
	myLabel.show_popup = "<?php echo isset($tpl['popup_message']) && !empty($tpl['popup_message']) ? 1 : 0; ?>";
	myLabel.showperiod = <?php echo $show_period; ?>;

	myLabel.order_transfer_time = "<?php __('lblScheduleTransferTime');?>";
	myLabel.order_client = "<?php __('lblScheduleOrderClient');?>";
	myLabel.order_transfer_destinations = "<?php __('lblScheduleOrderTransferDestinations');?>";
	myLabel.order_vehicle = "<?php __('lblScheduleOrderVehicle');?>";
	myLabel.order_passengers = "<?php __('lblScheduleOrderPassengers');?>";
	myLabel.order_order_id = "<?php __('lblScheduleOrderOrderID');?>";
	myLabel.order_total = "<?php __('lblScheduleOrderTotal');?>";

	myLabel.alert_assign_order_with_ai_title = <?php x__encode('infoAssignOrdersWithAITitle');?>;
	myLabel.alert_assign_order_with_ai_text = <?php x__encode('infoAssignOrdersWithAIDesc');?>;

	myLabel.alert_driver_payment_cc_title = <?php x__encode('infoDriverPaymentCreditCardTitle');?>;
	myLabel.alert_driver_payment_cc_text = <?php x__encode('infoDriverPaymentCreditCardBody');?>;

	myLabel.alert_unassign_order_with_ai_title = <?php x__encode('infoUnassignOrdersTitle');?>;
	myLabel.alert_unassign_order_with_ai_text = <?php x__encode('infoUnassignOrdersDesc');?>;

	myLabel.btn_yes = "<?php __('btnYes');?>";
	myLabel.btn_no = "<?php __('btnNo');?>";

	myLabel.install_url = "<?php echo PJ_INSTALL_URL;?>";
	</script>