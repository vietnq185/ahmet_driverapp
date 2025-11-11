<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
    $jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="update_service" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
		<input type="hidden" name="vehicle_id" value="<?php echo $tpl['arr']['vehicle_id'];?>" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>

			<h3 class="modal-title" id="myModalLabel"><?php __('infoUpdateServiceRepair'); ?></h3>
		</div>
		
		<div class="container-fluid">
			<div class="row m-t-sm">
				<div class="col-sm-12">
					<div class="text-right">
						<div class="btn-group-languages">
                            <?php if ($tpl['is_flag_ready']) : ?>
                    		<div class="multilang"></div>
                    		<?php endif; ?>    
                    	</div>
					</div>
					<div class="form-group">
        				<label class="control-label"><?php __('lblVehicleServiceDate');?>:</label>			
        				<div class="input-group date"
                             data-provide="datepicker"
                             data-date-autoclose="true"
                             data-date-format="<?php echo $jqDateFormat ?>"
                             data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
        					<input type="text" name="date" id="date" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date']));?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" autocomplete="off">
        					<span class="input-group-addon">
        						<span class="glyphicon glyphicon-calendar"></span>
        					</span>
        				</div>
        			</div>
        			<div class="form-group">
        				<label class="control-label"><?php __('lblVehicleServiceKm');?>:</label>			
        				<input type="text" class="form-control required" name="km" value="<?php echo pjSanitize::html($tpl['arr']['km']);?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
        			</div>
        			
        			<div class="form-group">
        				<label class="control-label"><?php __('lblVehicleServiceRepair');?>:</label>			
        				<?php
        				foreach ($tpl['lp_arr'] as $v)
        				{
        					?>
        					<div class="<?php echo $tpl['is_flag_ready'] ? 'input-group' : '';?> pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 1 ? NULL : 'none'; ?>">
        						<input type="text" class="form-control<?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>" name="i18n[<?php echo $v['id']; ?>][service]" value="<?php echo pjSanitize::html(@$tpl['arr']['i18n'][$v['id']]['service']); ?>" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">	
        						<?php if ($tpl['is_flag_ready']) : ?>
        						<span class="input-group-addon pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="<?php echo pjSanitize::html($v['name']); ?>"></span>
        						<?php endif; ?>
        					</div>
        					<?php 
        				}
        				?>
        			</div>
        			
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btnConfirmUpdateServiceRepair"><?php __('btnSave'); ?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></button>
		</div>
	</form>
	<?php
}
?>