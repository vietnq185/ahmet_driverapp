<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
?>
<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
                <h2><?php __('infoVehiclesMaintenanceReportTitle');?></h2>
                <ol class="breadcrumb">
					<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehiclesMaintrance&amp;action=pjActionIndex"><?php __('MenuVehiclesMaintraince'); ?></a></li>
					<li class="active">
						<strong><?php __('infoVehiclesMaintenanceReportTitle');?></strong>
					</li>
				</ol>
            </div>
        </div>

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoVehiclesMaintenanceReportBody'); ?></p>
    </div>
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
			<div class="container-fluid">
                <div class="panel panel-default" style="border-top: 3px solid #337ab7;">
                    <div class="panel-body">
                        <form id="frmReport">
                        	<div class="row">
                                <div class="col-md-4">
                                	<div class="form-group">
                                        <label><?php __('lblSelectVehicle');?></label>
                                        <select name="vehicle_id" id="vehicle_id" class="form-control select-item required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                            <option value="">-- <?php __('lblChooseVehicle');?> --</option>
                                            <?php foreach ($tpl['vehicle_arr'] as $val) { ?>
                                            	<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['name']);?> | <?php echo pjSanitize::html($val['registration_number']);?></option>
                                            <?php } ?>
                                        </select>
                                   </div>
                                </div>
                                <div class="col-md-3">
                                	<div class="form-group">
                                        <label><?php __('lblReportFromDate');?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="from_date" id="from_date" class="form-control datepick required" readonly data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                	<div class="form-group">
                                        <label><?php __('lblReportToDate');?></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                                            <input type="text" name="to_date" id="to_date" class="form-control datepick required" readonly data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                                        </div>
                                   </div>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="ladda-button btn btn-primary btn-block btn-phpjabbers-loader pull-left" data-style="zoom-in">
            							<span class="ladda-label"><?php __('btnShowReport', false, true); ?></span>
            							<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
            						</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

				<div id="reportDetails">
					<div class="row">
                        <div class="col-md-12">
                            <div class="well text-center" style="background: #fff; border: 2px solid #337ab7;">
                                <h4 class="text-uppercase" style="color: #777;"><?php __('lblTotalServiceCost');?></h4>
                                <h1 id="displayTotalCost" style="color: #337ab7; font-weight: bold; margin-top: 10px;"><?php echo pjCurrency::formatPrice(0);?></h1>
                            </div>
                        </div>
                    </div>
    
                    <div class="panel panel-default">
                        <div class="table-responsive">
                            <table class="table table-hover" id="reportTable">
                                <thead>
                                    <tr style="background: #f5f5f5;">
                                        <th><?php __('lblReportServiceDate');?></th>
                                        <th><?php __('lblReportServiceType');?></th>
                                        <th><?php __('lblReportServiceKilometers');?></th>
                                        <th><?php __('lblReportServiceStation');?></th>
                                        <th class="text-right"><?php __('lblReportCost')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="5" class="text-center"><?php __('lblReportEmpty');?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
				</div>
			</div>
        </div>
    </div>
</div>


<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.choose = "<?php __('lblChoose', false, true); ?>";
</script>