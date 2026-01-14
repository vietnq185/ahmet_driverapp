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
                <h2><?php __('infoAddVehiclesMaintranceTitle');?></h2>
                <ol class="breadcrumb">
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehiclesMaintrance&amp;action=pjActionIndex"><?php __('MenuVehiclesMaintraince'); ?></a></li>
					<li class="active">
						<strong><?php __('infoAddVehiclesMaintranceTitle');?></strong>
					</li>
				</ol>
            </div>
        </div>

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoAddVehiclesMaintranceBody'); ?></p>
    </div>
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehiclesMaintrance&amp;action=pjActionCreate" method="post" id="frmCreate" class="form pj-form" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="action_add" value="1" />
				<input type="hidden" name="foreign_id" id="foreign_id" value="<?php echo pjSanitize::html($tpl['tmp_hash']);?>" />
				<button id="hidden-browse-button" style="display:none;"></button>
            	<div class="ibox-content">
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php __('lblMaintranceSelectVehicle');?></label>
                                <select name="vehicle_id" class="form-control select-item required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                	<option value="">-- <?php __('lblChoose');?> --</option>
                                	<?php foreach ($tpl['vehicle_arr'] as $vel) { ?>
                                		<option value="<?php echo $vel['id'];?>"><?php echo pjSanitize::html($vel['name']);?> | <?php echo pjSanitize::html($vel['registration_number']);?></option>
                                	<?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php __('lblMaintranceMake')?></label>
                                <input type="text" name="make" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                            <div class="form-group">
                                <label><?php __('lblMaintranceVehicleVIN');?></label>
                                <input type="text" name="vin" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                            <div class="form-group">
                                <label><?php __('lblMaintranceBuyedInKm');?></label>
                                <input type="text" name="buyed_in_km" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php __('lblMaintranceModel');?></label>
                                <input type="text" name="model" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                            <div class="form-group">
                                <label><?php __('lblMaintranceNetPrice');?></label>
                                <div class="input-group">
                    				<input type="text" name="net_price" id="net_price" class="form-control number text-right required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" />
                    				<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>
                    			</div>
                            </div>
                            <div class="form-group">
                                <label><?php __('lblMaintranceTuv');?></label>
                                <input type="text" name="tuv" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?php __('lblMaintranceMadeYear');?></label>
                                <input type="text" name="model_year" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                            </div>
                            <div class="form-group">
                                <label><?php __('lblMaintranceBuyDate');?></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                    <input type="text" name="buy_date" id="buy_date" class="form-control datepick required" readonly data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                </div>
                            </div>
                        </div>
                    </div>
        
        			<div class="grey-box">
                        <div class="row row-flex-center">
                            <div class="col-md-9">
                                <div class="photo-list" id="vehicle_photos">
                                	
                                    <div class="photo-item empty"><?php __('lblNoPhotos');?></div>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <button class="btn btn-default btn-upload btn-block btn-outline btn-primary btn-sm"" type="button" data-foreign_id="<?php echo pjSanitize::html($tpl['tmp_hash']);?>" data-type="vehicle_photos"><?php __('btnAddVehiclePhotos');?></button>
                            </div>
                        </div>
        			</div>
        			<div class="grey-box">
                        <div class="row row-flex-center">
                            <div class="col-md-9">
                                <div class="file-list" id="vehicle_documents">
                                	
                                    <div class="file-item empty"><?php __('lblNoDocuments');?></div>
                                </div>
                            </div>
                            <div class="col-md-3 text-right">
                                <button class="btn btn-default btn-upload btn-block btn-outline btn-primary btn-sm"" type="button" data-foreign_id="<?php echo pjSanitize::html($tpl['tmp_hash']);?>" data-type="vehicle_documents"><?php __('btnAddVehicleDocuments');?></button>
                            </div>
                        </div>
        			</div>
                    <hr>
        
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label><?php __('lblInternet');?></label><input type="text" name="internet" class="form-control"></div>
                            <div class="form-group"><label><?php __('lblGPS');?></label><input type="text" name="gps" class="form-control"></div>
                            <div class="form-group"><label><?php __('lblTelepass');?></label><input type="text" name=telepass class="form-control"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label><?php __('lblTollBrenner');?></label><input type="text" name="toll_brenner" class="form-control"></div>
                            <div class="form-group"><label><?php __('lblTollArlberg');?></label><input type="text" name="toll_arlberg" class="form-control"></div>
                            <div class="form-group"><label><?php __('lblSwissVignette');?></label><input type="text" name="swiss_vignette" class="form-control"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label><?php __('lblAustriaVignette');?></label><input type="text" name="austria_vignette" class="form-control"></div>
                        </div>
                    </div>
        
                    <div class="form-group">
                        <label><?php __('lblNotes');?></label>
                        <textarea class="form-control" rows="4" name="notes"></textarea>
                    </div>
                    
                    <div id="dynamic-fields-container" style="margin-top: 15px;">
                        </div>
                    
                    <div style="margin-top: 10px;">
                        <button type="button" class="btn btn-outline btn-primary btn-sm" id="add-custom-field">
                            <i class="fa fa-plus"></i> <?php __('btnAddField');?>
                        </button>
                    </div>
        
                    <div class="text-center" style="margin-top: 20px;">
                        <button class="btn btn-add-accident btn-sm btn-outline btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddAccident');?></button>
                        <button class="btn btn-add-service btn-primary btn-sm btn-outline btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddService');?></button>
                    </div>
                    
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-6">
                            <h5 class="text-blue"><i class="fa fa-exclamation-triangle"></i> <?php __('lblRecentAccidents');?></h5>
                            <div class="table-responsive" id="vehicleAccidents">
                                <table class="table table-hover table-striped" id="tableAccidents" style="background: white; border-radius: 8px; overflow: hidden;">
                                    <thead>
                                        <tr style="background: #f1f4f7; color: #337ab7;">
                                            <th><?php __('lblAccidentDate');?></th>
                                            <th><?php __('lblAccidentDriver');?></th>
                                            <th><?php __('lblAccidentLocation');?></th>
                                            <th class="text-center"><?php __('lblAccidentAction');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" align="center"><?php __('lblRecentAccidentsEmpty');?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <h5 class="text-blue"><i class="fa fa-gears"></i> <?php __('lblRecentServices');?></h5>
                            <div class="table-responsive" id="vehicleServices">
                                <table class="table table-hover table-striped" id="tableServices" style="background: white; border-radius: 8px; overflow: hidden;">
                                    <thead>
                                        <tr style="background: #f1f4f7; color: #337ab7;">
                                            <th><?php __('lblServiceType');?></th>
                                            <th><?php __('lblServiceKm');?></th>
                                            <th><?php __('lblServiceCost');?></th>
                                            <th class="text-center"><?php __('lblServiceAction');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" align="center"><?php __('lblRecentServicesEmpty');?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    
					<div class="clearfix">
						<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
							<span class="ladda-label"><?php __('btnSave', false, true); ?></span>
							<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
						</button>
	
						<button type="button" class="btn btn-white btn-lg pull-right" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminVehiclesMaintrance&action=pjActionIndex';"><?php __('btnCancel'); ?></button>
					</div>
	            </div>
			</form>
        </div>
    </div>
</div>

<!-- Custom field -->
<div id="customFieldClone" style="display: none;">
	<div class="row row-flex-center custom-field-row" style="margin-bottom: 10px; display: flex; align-items: center;">
        <div class="col-md-4">
            <select name="attr_cat[{INDEX}]" class="form-control field-type-select">
                <option value="">-- <?php __('lblChoose');?> --</option>
                <?php foreach ($tpl['attribute_arr'] as $val) { ?>
                	<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['name']);?></option>
                <?php } ?>
                <option value="other"><?php __('lblOtherNewAttribute');?></option>
            </select>
            <input name="new_attr_cat[{INDEX}]" type="text" class="form-control custom-label-input" style="display:none; margin-top:5px;">
        </div>
        <div class="col-md-7">
            <input name="attr_content[{INDEX}]" type="text" class="form-control">
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="btn btn-link text-danger remove-field" title="<?php __('btnRemove');?>">
                <i class="fa fa-trash fa-lg"></i>
            </button>
        </div>
    </div>
</div>
<!-- End custom field -->

<!-- Form accident -->
<div class="modal fade" id="modalAddAccident" data-backdrop="static" data-keyboard="false"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>
<!-- End Form accident -->

<!-- Add service -->
<div class="modal fade" id="modalAddService" data-backdrop="static" data-keyboard="false"  tabindex="-1" role="dialog" aria-labelledby="serviceModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div>
<!-- End add service -->

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