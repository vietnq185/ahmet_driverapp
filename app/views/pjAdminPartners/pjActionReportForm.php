<?php 
if (isset($tpl['arr']) && $tpl['arr']) {
    $id = $tpl['arr']['id'];
    $from = date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date_from']));
    $to = date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date_to']));
} else {
    $id = '';
    $startOfMonth = new DateTime('first day of this month');
    $from = $startOfMonth->format($tpl['option_arr']['o_date_format']);
    $endOfMonth = new DateTime('last day of this month');
    $to = $endOfMonth->format($tpl['option_arr']['o_date_format']); 
}
?>
<div class="pj-loader-outer">
	<div class="pj-loader-modal"></div>
    <form method="post">
    	<input type="hidden" name="action_generate_billing" value="1" />
    	<input type="hidden" name="partner_id" value="<?php echo $controller->_get->toInt('partner_id');?>" />
    	<input type="hidden" name="id" value="<?php echo $id;?>" />
    	<input type="hidden" name="tmp_hash" value="<?php echo pjSanitize::html($tpl['tmp_hash']);?>" />
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?php __('infoAddReportBillingTitle');?></h4>
        </div>
        <div class="modal-body pjSbModalBilling">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" name="date_from" id="date_from" class="form-control datepicker" value="<?php echo pjSanitize::html($from);?>">
                        <span class="input-group-addon"><?php __('lblReportBillingTo');?></span>
                        <input type="text" name="date_to" id="date_to" class="form-control datepicker" value="<?php echo pjSanitize::html($to);?>">
                    </div>
                </div>
            </div>
        
        	<div class="report-billing-data">
                
           </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary btnGenerateReportBilling"><?php __('btnGenerateReportBilling');?></button>
            <button type="button" class="btn btn-default btn-outline btnDownloadReportBilling"><?php __('btnDownloadReportBillingPdf');?></button>
        </div>
    </form>
</div>