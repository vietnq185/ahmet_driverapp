<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="send_sms" value="1" />
		<input type="hidden" name="to" value="<?php echo $tpl['arr']['phone'];?>" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>

			<h3 class="modal-title" id="myModalLabel"><?php __('driver_sms_title'); ?></h3>
		</div>
		
		<div class="container-fluid">
			<div class="row m-t-sm">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label"><?php __('lblMessage'); ?></label>
						<textarea name="message" id="message" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><?php echo stripslashes(str_replace(array('\r\n', '\n'), '&#10;', $tpl['arr']['message'])); ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btnSendSms"><?php __('btnSend'); ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></button>
			</div>
		</div>
	</form>
	<?php
}
?>