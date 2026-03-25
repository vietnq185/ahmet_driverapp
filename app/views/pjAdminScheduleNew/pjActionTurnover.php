<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>
	<h3 class="modal-title" id="myModalLabel"><?php __('lblViewTurnoverTitle'); ?></h3>
</div>
<div class="container-fluid">
	<div class="row m-t-sm">
		<div class="col-sm-12">
			<div class="form-group">
				<label class="control-label"><?php __('lblTurnoverCash'); ?>:</label>
				<?php echo pjCurrency::formatPrice($tpl['total_cash']);?>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblTurnoverCreditCard'); ?>:</label>
				<?php echo pjCurrency::formatPrice($tpl['total_credit_card']);?>
			</div>
			<div class="form-group">
				<label class="control-label"><?php __('lblTurnoverPrepaid'); ?>:</label>
				<?php echo pjCurrency::formatPrice($tpl['total_prepaid']);?>
			</div>
			<div class="form-group col-12">
            	<hr class="style1">
		   </div>
			<div class="form-group">
				<label class="control-label"><?php __('lblTurnoverTotal'); ?>:</label>
				<?php echo pjCurrency::formatPrice($tpl['total']);?>
			</div>
		</div>
	</div>
	
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal"><?php __('btnClose'); ?></button>
	</div>
</div>