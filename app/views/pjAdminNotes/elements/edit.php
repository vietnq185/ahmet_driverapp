<?php 
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
?>
<div class="panel no-borders">
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoUpdateNoteTitle')?></p>
	</div><!-- /.panel-heading -->
 
	<div class="panel-body">
		<form action="" method="post" id="frmUpdate">
			<input type="hidden" name="update_note" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			
			<div class="form-group">
				<label class="control-label"><?php __('lblNoteVehicle');?>:</label>			
				<select name="vehicle_id" id="vehicle_id" class="form-control required select-item" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php foreach ($tpl['vehicle_arr'] as $veh) { ?>
						<option value="<?php echo $veh['id'];?>" <?php echo $veh['id'] == $tpl['arr']['vehicle_id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($veh['name']);?> | <?php echo pjSanitize::html($veh['registration_number']);?></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblOrderShift')?>:</label>	
				<select name="vehicle_order" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
					<?php foreach (__('_order_shift', true) as $k => $v) { ?>
						<option value="<?php echo $k;?>" <?php echo $tpl['arr']['vehicle_order'] == $k ? 'selected="selected"' : '';?>><?php echo $v;?></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblNoteDate');?>:</label>			
				<div class="input-group date"
                     data-provide="datepicker"
                     data-date-autoclose="true"
                     data-date-format="<?php echo $jqDateFormat ?>"
                     data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
					<input type="text" name="date" id="date" value="<?php echo !empty($tpl['arr']['date']) ? date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date'])) : '';?>" class="form-control" autocomplete="off">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblNoteNotes');?>:</label>	
				<textarea rows="5" name="notes" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><?php echo pjSanitize::html($tpl['arr']['notes']);?></textarea>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblStatus');?>:</label>			
				<div class="clearfix">
					<div class="switch onoffswitch-data pull-left">
						<div class="onoffswitch">
							<input type="checkbox" value="1" class="onoffswitch-checkbox" id="status" name="status" <?php echo $tpl['arr']['status'] == 'T' ? 'checked="checked"' : '';?>>
							<label class="onoffswitch-label" for="status">
								<span class="onoffswitch-inner" data-on="<?php __('filter_ARRAY_active', false, true); ?>" data-off="<?php __('filter_ARRAY_inactive', false, true); ?>"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="m-t-sm">
				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
					<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
				</button>
				<button type="button" class="btn btn-white btn-lg pull-right pjBtnCancelUpdateNote"><?php __('btnCancel'); ?></button>
			</div><!-- /.m-b-lg -->
		</form>
	</div><!-- /.panel-body -->
</div><!-- /.panel panel-primary -->