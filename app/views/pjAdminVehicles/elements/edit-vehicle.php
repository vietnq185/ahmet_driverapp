<?php 
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
?>
<div class="panel no-borders">
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoUpdateVehicleTitle')?></p>
	</div><!-- /.panel-heading -->
 
	<div class="panel-body">
		<form action="" method="post" id="frmUpdate">
			<input type="hidden" name="update_vehicle" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleName');?>:</label>			
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?> pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
						<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][name]" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['name']); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
						<?php if ($tpl['is_flag_ready']) : ?>
						<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
						<?php endif; ?>
					</div>
					<?php 
				}
				?>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleRegistrationNumber');?>:</label>			
				<input type="text" class="form-control required" name="registration_number" value="<?php echo pjSanitize::html($tpl['arr']['registration_number']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleSeats');?>:</label>			
				<input type="text" class="form-control touchspin3 required" name="seats" value="<?php echo pjSanitize::html($tpl['arr']['seats']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleOrder');?>:</label>			
				<input type="text" class="form-control touchspin3 required" name="order" value="<?php echo pjSanitize::html($tpl['arr']['order']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleType');?>:</label>			
				<select name="type" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php foreach (__('_vehicle_types', true) as $k => $v) { ?>
						<option value="<?php echo $k;?>" <?php echo $tpl['arr']['type'] == $k ? 'selected="selected"' : '';?>><?php echo $v;?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleMarkerModell');?>:</label>			
				<input type="text" class="form-control" name="maker_modell" value="<?php echo pjSanitize::html($tpl['arr']['maker_modell']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleVin');?>:</label>			
				<input type="text" class="form-control" name="vin" value="<?php echo pjSanitize::html($tpl['arr']['vin']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleModelYear');?>:</label>			
				<input type="text" class="form-control" name="model_year" value="<?php echo pjSanitize::html($tpl['arr']['model_year']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleTuv');?>:</label>			
				<div class="input-group date"
                     data-provide="datepicker"
                     data-date-autoclose="true"
                     data-date-format="<?php echo $jqDateFormat ?>"
                     data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
					<input type="text" name="tuv" id="tuv" value="<?php echo !empty($tpl['arr']['tuv']) ? date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['tuv'])) : '';?>" class="form-control" autocomplete="off">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
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
			<div class="m-t-sm m-b-sm text-center">
				<button type="button" class="btn btn-primary btn-outline btn-sm btnAddServiceRepair" data-id="<?php echo $tpl['arr']['id'];?>"><i class="fa fa-plus"></i> <?php __('btnAddServiceRepair');?></button>
			</div>
			<div class="pjSbVehicleServiceRepair m-b-sm"></div>
			<div class="m-t-sm">
				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
					<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
				</button>
				<button type="button" class="btn btn-white btn-lg pull-right pjBtnCancelUpdateVehicle"><?php __('btnCancel'); ?></button>
			</div><!-- /.m-b-lg -->
		</form>
	</div><!-- /.panel-body -->
</div><!-- /.panel panel-primary -->