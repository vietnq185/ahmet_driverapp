<div class="form-group">
	<label class="control-label"><?php __('lblMessage'); ?></label>
	<input type="hidden" name="customer_phone" value="<?php echo pjSanitize::html($tpl['arr']['c_dialing_code']);?><?php echo pjSanitize::html($tpl['arr']['c_phone']);?>" />
	<textarea name="message" id="message" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><?php echo stripslashes(str_replace(array('\r\n', '\n'), '&#10;', $tpl['message'])); ?></textarea>
</div>