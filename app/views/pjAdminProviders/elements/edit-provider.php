<div class="panel no-borders">
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoUpdateProviderTitle')?></p>
	</div><!-- /.panel-heading -->

	<div class="panel-body">
		<form action="" method="post" id="frmUpdate" enctype="multipart/form-data">
			<input type="hidden" name="update_provider" value="1" />
			<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
			<div class="form-group">
				<label class="control-label"><?php __('lblProviderName');?>:</label>			
				<input type="text" class="form-control required" name="name" value="<?php echo pjSanitize::html($tpl['arr']['name']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblProviderURL');?>:</label>			
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-globe"></i></span> 
					<input type="text" name="url" id="url" value="<?php echo pjSanitize::html($tpl['arr']['url']);?>" class="form-control required" maxlength="100" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
				</div>
			</div>	
			
			<div class="form-group">
				<label class="control-label"><?php __('lblWhatsAppName');?>:</label>			
				<input type="text" class="form-control" name="whatsapp_name" value="<?php echo pjSanitize::html($tpl['arr']['whatsapp_name']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblWhatsAppPhoneNumberID');?>:</label>			
				<input type="text" class="form-control" name="whatsapp_phone_number_id" value="<?php echo pjSanitize::html($tpl['arr']['whatsapp_phone_number_id']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblWhatsAppPermanentAccessToken');?>:</label>			
				<input type="text" class="form-control" name="whatsapp_permanent_access_token" value="<?php echo pjSanitize::html($tpl['arr']['whatsapp_permanent_access_token']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
			</div>
			
			<div class="form-group">
				<label class="control-label"><?php __('lblProviderNameSignLogo');?></label><br/>
				<?php
				if (!empty($tpl['arr']['name_sign_logo']) && is_file($tpl['arr']['name_sign_logo']))
				{
					?>
					<div class="provider-logo">
						<p class="m-b-md">
							<img src="<?php echo PJ_INSTALL_URL . $tpl['arr']['name_sign_logo'];?>" alt="" class="img-responsive">
						</p>
						<p class="m-b-md">
							<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminProviders&amp;action=pjActionDeleteImage&amp;id=<?php echo $tpl['arr']['id'];?>" rev="<?php echo $tpl['arr']['id']; ?>" class="btn btn-xs btn-danger btn-outline btn-file btnDeleteImage"><i class="fa fa-trash"></i> <?php __('btn_delete_logo');?></a>
						</p>
					</div>
					<?php
				}
				?>
        		<div class="fileinput fileinput-new" data-provides="fileinput">
					<span class="btn btn-primary btn-outline btn-file">
						<span class="fileinput-new"><i class="fa fa-upload m-r-xs"></i> <?php __('btn_select_image'); ?></span>
						<span class="fileinput-exists"><i class="fa fa-upload m-r-xs"></i> <?php __('btn_change_image'); ?></span>
						<input type="file" name="name_sign_logo">
					</span>
					<span class="fileinput-filename"></span>
					<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">x</a>
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
			<div class="m-t-sm">
				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
					<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
				</button>
				<button type="button" class="btn btn-white btn-lg pull-right pjBtnCancelUpdateProvider"><?php __('btnCancel'); ?></button>
			</div><!-- /.m-b-lg -->
		</form>
	</div><!-- /.panel-body -->
</div><!-- /.panel panel-primary -->