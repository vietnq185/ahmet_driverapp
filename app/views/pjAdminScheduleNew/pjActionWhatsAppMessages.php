<form action="" method="post" class="form pj-form">
	<input type="hidden" name="send_sms" value="1" />
	<input type="hidden" name="booking_id" value="<?php echo $controller->_get->toInt('id');?>" />
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>

		<h3 class="modal-title" id="myModalWMLabel"><?php __('btnSendWS'); ?></h3>
	</div>
	
	<div class="container-fluid">
		<div class="row m-t-sm">
			<div class="col-sm-12">
				<div class="form-group">
					<label class="control-label"><?php __('lblWMSubject'); ?></label>
					<select class="form-control required selectWhatsappMessage" name="subject" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
						<option value="">-- <?php __('lblChoose');?> --</option>
						<?php foreach ($tpl['messages_arr'] as $val) { ?>
							<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['subject']);?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		
		<div class="row m-t-sm">
			<div class="col-sm-12 WhatsappMessageContainer">
				<div class="form-group">
					<label class="control-label"><?php __('lblMessage'); ?></label>
					<textarea name="message" id="message" class="form-control required" rows="5" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"></textarea>
				</div>
			</div>
		</div>
		
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btnSendWhatsappSms"><?php __('btnSend'); ?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></button>
		</div>
	</div>
</form>