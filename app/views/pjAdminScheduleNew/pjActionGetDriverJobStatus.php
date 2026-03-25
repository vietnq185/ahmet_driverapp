<?php if ($tpl['driver_arr']) { 
    $_driver_job_statuses = __('_driver_job_statuses', true);
    ?>
    <div class="row m-t-sm">
    	<div class="col-sm-12">
    		<div class="form-group">
    			<label class="control-label"><?php __('lblDriverLastLogin'); ?>:</label>
    			<span><?php echo date($tpl['option_arr']['o_date_format'].', '.$tpl['option_arr']['o_time_format'], strtotime($tpl['driver_arr']['last_login']));?></span>
    		</div>
    	</div>
    </div>
    <div class="row m-t-sm">
    	<div class="col-sm-12">
    		<div class="form-group">
    			<label class="control-label"><?php __('lblDriverConfirmationStatus'); ?>:</label>
    			<span><?php echo isset($_driver_job_statuses[@$tpl['job_status_arr']['status']]) ? $_driver_job_statuses[@$tpl['job_status_arr']['status']] : $_driver_job_statuses['F'];?></span>
    		</div>
    	</div>
    </div>
<?php } ?>