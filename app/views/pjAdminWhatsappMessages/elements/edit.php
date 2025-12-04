<?php 
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
?>
<div class="panel no-borders">
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoUpdateWhatsappMessageTitle')?></p>
	</div><!-- /.panel-heading -->
 
	<div class="panel-body">
		<div class="alert alert-info"><?php __('infoAddUpdateWhatsappMessageToken');?></div>
		<form action="" method="post" id="frmUpdate">
			<input type="hidden" name="update_wm" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			<div class="form-group">
				<label class="control-label"><?php __('lblWMSubject');?>:</label>			
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?> pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
						<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][subject]" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['subject']); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
						<?php if ($tpl['is_flag_ready']) : ?>
						<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
						<?php endif; ?>
					</div>
					<?php 
				}
				?>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblWMMessage');?>:</label>			
				<?php
				foreach ($tpl['lp_arr'] as $v)
				{
					?>
					<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?> pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
						<textarea rows="5" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][message]" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><?php echo @$tpl['arr']['i18n'][$v['id']]['message'];?></textarea>
						<?php if ($tpl['is_flag_ready']) : ?>
						<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
						<?php endif; ?>
					</div>
					<?php 
				}
				?>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblWMAvailableFor');?>:</label>			
				<select name="available_for" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php foreach (__('wm_available_for', true) as $k => $v) { ?>
						<option value="<?php echo $k;?>" <?php echo $tpl['arr']['available_for'] == $k ? 'selected="selected"' : '';?>><?php echo $v;?></option>
					<?php } ?>
				</select>
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblVehicleOrder');?>:</label>			
				<input type="text" class="form-control touchspin3" name="order" value="<?php echo pjSanitize::html($tpl['arr']['order']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblWMStatus');?>:</label>			
				<div class="clearfix">
					<div class="switch onoffswitch-data pull-left">
						<div class="onoffswitch">
							<input type="checkbox" value="1" class="onoffswitch-checkbox" id="status" name="status" <?php echo $tpl['arr']['status'] == 'T' ? 'checked="checked"' : '';?>>
							<label class="onoffswitch-label" for="status">
								<span class="onoffswitch-inner" data-on="<?php __('wm_statuses_ARRAY_T', false, true); ?>" data-off="<?php __('wm_statuses_ARRAY_F', false, true); ?>"></span>
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
				<button type="button" class="btn btn-white btn-lg pull-right pjBtnCancelUpdateWM"><?php __('btnCancel'); ?></button>
			</div><!-- /.m-b-lg -->
		</form>
	</div><!-- /.panel-body -->
</div><!-- /.panel panel-primary -->