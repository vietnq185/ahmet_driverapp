<?php 
$front_tracking_statuses = __('front_tracking_statuses', true);
if (isset($tpl['arr']) && $tpl['arr']) { 
    $start_time = strtotime($tpl['arr']['booking_date']);
    $end_time = strtotime($tpl['arr']['booking_date']) + ($tpl['arr']['duration']*60);
    $allow_tracking_from = $start_time - (2 * 60 * 60); //before 15 minutes
    $end_tracking_at = $end_time + (2 * 60 * 60); //after 15 minutes
    ?>
    <?php if ((isset($tpl['arr']['vehicle_data']) && $tpl['arr']['vehicle_data'] && time() >= $allow_tracking_from && time() < $end_tracking_at) || time() < $allow_tracking_from) { ?>
    	<input type="hidden" name="vehicle_id_from_api" id="vehicle_id_from_api" value="<?php echo $tpl['arr']['vehicle_data']['_id'];?>" />
    <?php } ?>
	<?php if (isset($tpl['arr']['vehicle_data']) && $tpl['arr']['vehicle_data'] && time() >= $allow_tracking_from && time() < $end_tracking_at) { 
	    $in_trip = time() >= $start_time && time() < $end_time ? true : false;
	    ?>
        <div align="center" class="pjTripInfo <?php echo $in_trip ? 'text-success' : 'text-warning';?>">
			<div><strong><?php __('front_your_vehicle');?>: <?php echo pjSanitize::html($tpl['arr']['vehicle_name']);?></strong></div>
			<div><strong><?php __('front_scheduled_start_time');?>: <?php echo date($tpl['option_arr']['o_time_format'], $start_time);?></strong></div>
			<div><strong><?php __('front_scheduled_end_time');?>: <?php echo date($tpl['option_arr']['o_time_format'], $end_time);?></strong></div>
			<div class="pjVehicleSpeed"></div>
		</div><br/>
        <div id="main-container">
            <div id="map-panel">
                <div id="map-tracking"></div>
            </div>
        </div>
	<?php } else { ?>
    	<br/>
    	<div class="container">
    		<div class="row">
        		<div class="col-xs-12">
        			<div class="alert alert-info">
        				<?php 
        				    if ((int)$tpl['arr']['vehicle_id'] <= 0 || !isset($tpl['arr']['vehicle_data']) || (isset($tpl['arr']['vehicle_data']) && !$tpl['arr']['vehicle_data'])) {
        				        echo $front_tracking_statuses[2];
        				    } elseif (time() < $allow_tracking_from) {
        				        echo sprintf($front_tracking_statuses[3], date($tpl['option_arr']['o_time_format'], $start_time));
        				    } elseif ($end_tracking_at <= time()) {
        				        echo $front_tracking_statuses[5];
        				    }
        				?>
        			</div>
        		</div>
        	</div>
    	</div>
	<?php } ?>
<?php } else { ?>
	<br/>
	<div class="container">
		<div class="row">
    		<div class="col-xs-12">
    			<div class="alert alert-info"><?php echo $front_tracking_statuses[1];?></div>
    		</div>
    	</div>
	</div>
<?php } ?>