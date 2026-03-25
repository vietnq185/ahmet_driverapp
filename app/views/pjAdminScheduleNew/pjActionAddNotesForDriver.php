<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="add_notes_for_driver" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id'];?>" />
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>

			<h3 class="modal-title" id="myModalLabel"><?php __('lblOrderNotesForDriver'); ?></h3>
		</div>
		
		<div class="container-fluid">
			<div class="row m-t-sm">
				<div class="col-sm-12">
					<div class="form-group">
						<textarea name="notes_from_office" id="notes_from_office" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"><?php echo stripslashes($tpl['arr']['notes_from_office']); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary btnConfirmAddNotesForDriver"><?php __('btnSave'); ?></button>
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnCancel'); ?></button>
		</div>
	</form>
	<?php
}
?>