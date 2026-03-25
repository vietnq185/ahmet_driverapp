<?php
if (isset($tpl['driver_arr']) && !empty($tpl['driver_arr']))
{
    $driver_phone = ltrim($tpl['driver_arr']['phone'], '0+');
    $date = $controller->_get->check('date') && $controller->_get->toString('date') != '' ? $controller->_get->toString('date') : date($tpl['option_arr']['o_date_format']);
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="send_whatsapp" value="1" />
		<input type="hidden" name="to" value="<?php echo pjSanitize::html($driver_phone);?>" />
		<input type="hidden" name="driver_id" value="<?php echo $tpl['driver_arr']['id'];?>" />
		<input type="hidden" name="date" value="<?php echo $date;?>" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>

			<h3 class="modal-title" id="myModalLabel"><?php __('driver_send_whatsapp_title'); ?></h3>
		</div>
		
		<div class="container-fluid">
			<div class="row m-t-sm" style="display: none;">
				<div class="col-sm-12">
        			<div class="form-group">
        				<label class="control-label"><?php __('dash_whatsapp_provider');?>:</label>			
        				<select id="provider_id" name="provider_id" class="form-control">
        					<?php foreach ($tpl['provider_arr'] as $k => $val) { ?>
                            	<option value="<?php echo $val['id'];?>" <?php echo $k == 0 ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($val['whatsapp_name']);?></option>
                            <?php } ?>
                        </select>
        			</div>
        		</div>
        	</div>
			
			<div class="row m-t-sm">
				<div class="col-sm-12">
        			<div class="form-group">
        				<label class="control-label"><?php __('dash_whatsapp_templates');?>:</label>			
        				<select id="whatsapp_template" name="whatsapp_template" class="form-control msg-group">
                            <option value="">-- <?php __('lblSelectTemplate');?> --</option>
                        </select>
        			</div>
        		</div>
        	</div>
			<div class="row m-t-sm">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label"><?php __('lblMessage'); ?></label>
						<textarea rows="5" name="whatsapp_message" id="whatsapp_message" class="form-control msg-group" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"></textarea>
					</div>
				</div>
			</div>
			
			<div class="pjSbSendWhatsappMsg" style="display: nones;">
				<div class="alert"></div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btnSendWhatsappMsg"><?php __('btnSend'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></button>
			</div>
		</div>
	</form>
	<?php
}
?>