<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <h2><?php __('infoAddPartnerTitle');?></h2>
                <ol class="breadcrumb">
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminPartners&amp;action=pjActionIndex"><?php __('MenuPartners'); ?></a></li>
					<li class="active">
						<strong><?php __('infoAddPartnerTitle');?></strong>
					</li>
				</ol>
            </div>
        </div>

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoAddPartnerBody'); ?></p>
    </div>
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminPartners&amp;action=pjActionCreate" method="post" id="frmCreate" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="action_add" value="1" />
				<button id="hidden-browse-button" style="display:none;"></button>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php __('lblPartnerVehicles');?></label>
                                <select name="vehicle_ids[]" class="form-control select-item required" multiple data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                	<?php foreach ($tpl['vehicle_arr'] as $vel) { ?>
                                		<option value="<?php echo $vel['id'];?>"><?php echo pjSanitize::html($vel['name']);?> | <?php echo pjSanitize::html($vel['registration_number']);?></option>
                                	<?php } ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label><?php __('lblPartnerCommissionIn');?> %</label>
                                <input type="text" name="commission_pct" class="form-control number" placeholder="0">
                            </div>
                            
                            <div class="form-group">
                                <label><?php __('lblContractTheme');?></label>
                                <select name="contract_theme" class="form-control select-item required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                	<option value="">-- <?php __('lblChoose');?> --</option>
                                	<?php foreach ($tpl['contract_theme_arr'] as $val) { ?>
                                		<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['name']);?></option>
                                	<?php } ?>
                                </select>
                            </div>
                            
                        </div>
    
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerPartnerName');?></label>
                                    <input type="text" name="name" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerPhone');?></label>
                                    <div class="input-group">
                    					<span class="input-group-addon"><i class="fa fa-phone"></i></span> 
                    					<input type="text" name="phone" id="phone" class="form-control required" maxlength="100" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                    				</div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerMail');?></label>
                                    <div class="input-group">
                    					<span class="input-group-addon"><i class="fa fa-at"></i></span>
                    					<input type="text" name="email" id="email" class="form-control required email" maxlength="255" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" data-msg-email="<?php __('plugin_base_email_invalid', false, true);?>" data-msg-remote="<?php __('plugin_base_email_in_used', false, true);?>">
                    				</div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerCompanyName');?></label>
                                    <input type="text" name="company_name" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerAddress');?></label>
                                    <input type="text" name="address" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerTaxNumber');?></label>
                                    <input type="text" name="tax_number" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerCompanyNumber');?></label>
                                    <input type="text" name="company_number" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerIban');?></label>
                                    <input type="text" name="iban" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label><?php __('lblPartnerBic');?></label>
                                    <input type="text" name="bic" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label><?php __('lblPartnerNotes');?></label>
                                    <textarea name="notes" class="form-control" rows="6"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
   					<div class="hr-line-dashed"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="clearfix">
        						<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
        							<span class="ladda-label"><?php __('btnGeneratePartnerContract', false, true); ?></span>
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


<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.choose = "<?php __('lblChoose', false, true); ?>";
myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;	
myLabel.alert_delete_file_title = <?php x__encode('infoDeleteFileTitle');?>;
myLabel.alert_delete_file_text = <?php x__encode('infoDeleteFileBody');?>;
myLabel.alert_delete_record_title = <?php x__encode('infoDeleteRecordTitle');?>;
myLabel.alert_delete_record_text = <?php x__encode('infoDeleteRecordBody');?>;
</script>