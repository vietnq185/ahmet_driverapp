<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
    ?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="confirm_change" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>

			<h3 class="modal-title" id="myModalLabel"><?php __('lblChangeTime'); ?></h3>
		</div>
		
		<div class="container-fluid">
			<div class="row m-t-sm">
				<div class="col-sm-12">
					<div class="form-group">
						<div class="input-group clockpicker">
        					<input type="text" name="new_pickup_time" value="<?php echo date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['booking_date']));?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" readonly>
        			
        					<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
        				</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btnConfirmChangePickupTime"><?php __('btnSave'); ?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></button>
		</div>
	</form>
	<?php
}
?>