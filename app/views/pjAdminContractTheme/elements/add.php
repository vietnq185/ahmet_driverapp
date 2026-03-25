<div class="panel no-borders">
	<div class="panel-heading bg-completed">
		<p class="lead m-n"><?php __('infoAddContractThemeTitle')?></p>
	</div><!-- /.panel-heading -->
 
	<div class="panel-body">
		<div class="alert alert-info"><?php __('lblContractThemeNameTokens');?></div>
		<form action="" method="post" id="frmCreate">
			<input type="hidden" name="action_add" value="1" />
			<div class="form-group">
				<label class="control-label"><?php __('lblContractThemeName');?>:</label>	
				<input type="text" name="name" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" />
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblContractThemeContent');?>:</label>	
				<textarea name="content" class="form-control mceEditor"></textarea>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblStatus');?>:</label>			
				<div class="clearfix">
					<div class="switch onoffswitch-data pull-left">
						<div class="onoffswitch">
							<input type="checkbox" value="1" class="onoffswitch-checkbox" id="status" name="status" checked>
							<label class="onoffswitch-label" for="status">
								<span class="onoffswitch-inner" data-on="<?php __('filter_ARRAY_active', false, true); ?>" data-off="<?php __('filter_ARRAY_inactive', false, true); ?>"></span>
								<span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="m-t-lg">
				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
					<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
				</button>
			</div><!-- /.m-b-lg -->
		</form>
	</div><!-- /.panel-body -->
</div><!-- /.panel panel-primary -->