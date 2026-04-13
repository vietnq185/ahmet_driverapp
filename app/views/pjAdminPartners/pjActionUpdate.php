<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$show_period = 'false';
if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
{
    $show_period = 'true';
}
?>
<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <h2><?php __('infoUpdatePartnerTitle');?></h2>
                <ol class="breadcrumb">
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminPartners&amp;action=pjActionIndex"><?php __('MenuPartners');?></a></li>
					<li class="active">
						<strong><?php __('infoUpdatePartnerTitle');?></strong>
					</li>
				</ol>
            </div>
        </div>

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoUpdatePartnerBody');?></p>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<?php
	$error_code = $controller->_get->toString('err');
	if (!empty($error_code))
    {
    	switch (true)
    	{
    		case in_array($error_code, array('APAN01', 'APAN03')):
    			?>
    			<div class="alert alert-success">
    				<i class="fa fa-check m-r-xs"></i>
    				<strong><?php echo @$titles[$error_code]; ?></strong>
    				<?php echo @$bodies[$error_code]; ?>
    			</div>
    			<?php 
    			break;
            case in_array($error_code, array('APAN04', 'APAN08')):	
    			?>
    			<div class="alert alert-danger">
    				<i class="fa fa-exclamation-triangle m-r-xs"></i>
    				<strong><?php echo @$titles[$error_code]; ?></strong>
    				<?php echo @$bodies[$error_code]; ?>
    			</div>
    			<?php
    			break;
    	}
    }
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
    			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminPartners&amp;action=pjActionUpdate" method="post" id="frmUpdate" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
    				<input type="hidden" name="action_update" value="1" />
    				<input type="hidden" name="id" value="<?php echo pjSanitize::html($tpl['arr']['id']);?>" />
    				<input type="hidden" name="foreign_id" value="<?php echo pjSanitize::html($tpl['arr']['id']);?>" />
    				<input type="hidden" name="update_type" id="update_type" value="1" />
    				<button id="hidden-browse-button" style="display:none;"></button>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><?php __('lblPartnerVehicles');?></label>
                                    <select name="vehicle_ids[]" class="form-control select-item required" multiple data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    	<?php foreach ($tpl['vehicle_arr'] as $vel) { ?>
                                    		<option value="<?php echo $vel['id'];?>" <?php echo in_array($vel['id'], $tpl['partner_vehicle_arr']) ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($vel['name']);?> | <?php echo pjSanitize::html($vel['registration_number']);?></option>
                                    	<?php } ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label><?php __('lblPartnerCommissionIn');?> %</label>
                                    <input type="text" name="commission_pct" value="<?php echo pjSanitize::html($tpl['arr']['commission_pct']);?>" class="form-control number" placeholder="0">
                                </div>
                                
                                <div class="form-group">
                                    <label><?php __('lblContractTheme');?></label>
                                    <select name="contract_theme" class="form-control select-item required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    	<option value="">-- <?php __('lblChoose');?> --</option>
                                    	<?php foreach ($tpl['contract_theme_arr'] as $val) { ?>
                                    		<option value="<?php echo $val['id'];?>" <?php echo $tpl['arr']['contract_theme'] == $val['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($val['name']);?></option>
                                    	<?php } ?>
                                    </select>
                                </div>
                                
                            </div>
        
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerPartnerName');?></label>
                                        <input type="text" name="name" value="<?php echo pjSanitize::html($tpl['arr']['name']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerPhone');?></label>
                                        <div class="input-group">
                        					<span class="input-group-addon"><i class="fa fa-phone"></i></span> 
                        					<input type="text" name="phone" id="phone" value="<?php echo pjSanitize::html($tpl['arr']['phone']);?>" class="form-control required" maxlength="100" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                        				</div>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerMail');?></label>
                                        <div class="input-group">
                        					<span class="input-group-addon"><i class="fa fa-at"></i></span>
                        					<input type="text" name="email" id="email" value="<?php echo pjSanitize::html($tpl['arr']['email']);?>" class="form-control required email" maxlength="255" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-email="<?php __('plugin_base_email_invalid', false, true);?>" data-msg-remote="<?php __('plugin_base_email_in_used', false, true);?>">
                        				</div>
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerCompanyName');?></label>
                                        <input type="text" name="company_name" value="<?php echo pjSanitize::html($tpl['arr']['company_name']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerAddress');?></label>
                                        <input type="text" name="address" value="<?php echo pjSanitize::html($tpl['arr']['address']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerTaxNumber');?></label>
                                        <input type="text" name="tax_number" value="<?php echo pjSanitize::html($tpl['arr']['tax_number']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerCompanyNumber');?></label>
                                        <input type="text" name="company_number" value="<?php echo pjSanitize::html($tpl['arr']['company_number']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerIban');?></label>
                                        <input type="text" name="iban" value="<?php echo pjSanitize::html($tpl['arr']['iban']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label><?php __('lblPartnerBic');?></label>
                                        <input type="text" name="bic" value="<?php echo pjSanitize::html($tpl['arr']['bic']);?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                    </div>
                                </div>
        
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label><?php __('lblPartnerNotes');?></label>
                                        <textarea name="notes" class="form-control" rows="6"><?php echo pjSanitize::html($tpl['arr']['notes']);?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                		<div class="grey-box">
                            <div class="row row-flex-center">
                                <div class="col-md-9">
                                    <div class="file-list" id="contract_documents">
                                    	
                                        <div class="file-item empty"><?php __('lblNoDocuments');?></div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right">
                                    <button class="btn btn-default btn-upload btn-block btn-outline btn-primary btn-sm" type="button" data-foreign_id="<?php echo pjSanitize::html($tpl['arr']['id']);?>"><?php __('btnAddContractDocuments');?></button>
                                </div>
                            </div>
            			</div>
            			<br/>
                        <div class="row align-items-center">
                        	<div class="col-md-12" align="center">
                                <button type="button" class="btn btn-info btn-add-report">+ <?php __('btnAddReport');?></button>
                            </div>
                        </div>
                        <br/>
                        <div class="table-responsive" id="report_list">
                            
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="clearfix">
            						<button type="button" name="button_action" data-type="1" class="ladda-button btnUpdatePartner btn btn-primary btn-lg btn-phpjabbers-loader pull-left" style="margin-right: 20px;" data-style="zoom-in">
            							<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
            							<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            						</button>
            						
            						<button type="button" name="button_action" data-type="2" class="ladda-button btnUpdatePartner btn btn-secondary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
            							<span class="ladda-label"><?php __('btnSaveGeneratePartnerContract', false, true); ?></span>
            							<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            						</button>
            	
            						<button type="button" class="btn btn-white btn-lg pull-right" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminPartners&action=pjActionIndex';"><?php __('btnCancel'); ?></button>
            					</div>
                            </div>
                        </div>
            		</div>
                </form>
            </div>
        </div>
   </div>
</div>


<!-- Form Add Report -->
<div class="modal fade" id="modalAddReport" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1000px;">
        <div class="modal-content">
            
        </div>
    </div>
</div>
<!-- End Form Add Report -->


<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.choose = "<?php __('lblChoose', false, true); ?>";
myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;	
myLabel.alert_delete_file_title = <?php x__encode('infoDeleteFileTitle');?>;
myLabel.alert_delete_file_text = <?php x__encode('infoDeleteFileBody');?>;
myLabel.alert_delete_record_title = <?php x__encode('infoDeleteRecordTitle');?>;
myLabel.alert_delete_record_text = <?php x__encode('infoDeleteRecordBody');?>;
myLabel.showperiod = <?php echo $show_period; ?>;
</script>