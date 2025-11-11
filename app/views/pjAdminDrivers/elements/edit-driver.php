<div class="panel no-borders">
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoUpdateDriverTitle')?></p>
	</div><!-- /.panel-heading -->

	<div class="panel-body">
		<form action="" method="post" id="frmUpdate">
			<input type="hidden" name="update_driver" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverName');?>:</label>			
				<input type="text" class="form-control required" name="name" value="<?php echo pjSanitize::html($tpl['arr']['name']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>	
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverEmail');?>:</label>			
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-at"></i></span>
					<input type="text" name="email" id="email" value="<?php echo pjSanitize::html($tpl['arr']['email']);?>" class="form-control required email" maxlength="255" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-email="<?php __('plugin_base_email_invalid', false, true);?>" data-msg-remote="<?php __('plugin_base_email_in_used', false, true);?>">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverPassword');?>:</label>			
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-lock"></i></span> 
					<input type="text" name="password" id="password" value="<?php echo pjSanitize::html($tpl['arr']['password']);?>" class="form-control required" maxlength="100" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverPhone');?>:</label>			
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-phone"></i></span> 
					<input type="text" name="phone" id="phone" value="<?php echo pjSanitize::html($tpl['arr']['phone']);?>" class="form-control required" maxlength="100" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
				</div>
			</div>	
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverLanguage');?>:</label>			
				<select name="locale_id" class="select-item form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
					<option value="">-- <?php __('lblChoose');?> --</option>
					<?php
					foreach ($tpl['locale_arr'] as $loc)
					{
						?><option value="<?php echo $loc['id']; ?>" <?php echo $loc['id'] == $tpl['arr']['locale_id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($loc['name']); ?></option><?php
					}
					?>
				</select>
			</div>	
			
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverType');?>:</label>
				<div class="form-group">			
    				<?php foreach (__('_types_of_drive', true) as $k => $v) { ?>
    					<div class="radio radio-inline radio-primary">
    						<input type="radio" id="type_of_driver_<?php echo $k;?>" name="type_of_driver" value="<?php echo $k;?>"<?php echo $k == $tpl['arr']['type_of_driver'] ? ' checked' : NULL; ?>>
    						<label for="type_of_driver_<?php echo $k;?>"><?php echo $v; ?></label>
    					</div>
				<?php } ?>
				</div>
			</div>	
			<div class="form-group">
				<label class="control-label"><?php __('lblDriverGeneralInfo');?>:</label>			
				<textarea rows="3" class="form-control" name="general_info_for_driver"><?php echo stripslashes($tpl['arr']['general_info_for_driver']);?></textarea>
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
				<button type="button" class="btn btn-white btn-lg pull-right pjBtnCancelUpdateDriver"><?php __('btnCancel'); ?></button>
			</div><!-- /.m-b-lg -->
		</form>
	</div><!-- /.panel-body -->
</div><!-- /.panel panel-primary -->