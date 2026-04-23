<?php
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$tab = $controller->_get->check('tab') ? $controller->_get->toString('tab') : 'financial';
$type = $controller->_get->check('type') ? $controller->_get->toString('type') : 'drivers';
?>
<style>
#tab-daily-performance-analytics .table-drivers thead th {
    background-color: #2ecc71; /* Màu xanh lá của Driver */
    color: #fff !important;
}

#tab-daily-performance-analytics .table-vehicles thead th {
    background-color: #9b59b6; /* Màu tím của Vehicle */
    color: #fff !important;
}

#tab-daily-performance-analytics .table-destinations thead th {
    background-color: #e74c3c; /* Màu đỏ của Destination */
    color: #fff !important;
}
.stat-box {
    display: flex;
    align-items: center;
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #eee;
    transition: transform 0.2s;
}

.stat-box:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 15px;
    font-size: 20px;
}

/* Màu nền nhẹ cho Icon */
.bg-info-light { background-color: rgba(52, 152, 219, 0.1); }
.bg-warning-light { background-color: rgba(230, 126, 34, 0.1); }

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 12px;
    color: #7f8c8d;
    text-transform: uppercase;
    font-weight: bold;
    margin-bottom: 2px;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
}

.stat-value small {
    font-size: 13px;
    color: #95a5a6;
    font-weight: normal;
}
</style>
<div id="datePickerOptions" style="display:none;" data-wstart="<?php echo (int) $tpl['option_arr']['o_week_start']; ?>" data-format="<?php echo pjUtil::toBootstrapDate($tpl['option_arr']['o_date_format']); ?>" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>"></div>
<div class="row border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoReportsTitle', false, true);?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoReportsDesc', false, true);?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
        	<div class="tabs-container tabs-reservations m-b-lg">
        		<ul class="nav nav-tabs" role="tablist">
             		<li role="presentation" class="<?php echo $tab == 'financial' ? 'active' : '';?>"><a class="nav-tab-financial-overview" href="#tab-financial-overview" aria-controls="financial-overview" role="tab" data-toggle="tab">Financial Overview</a></li>
    				<li role="presentation" class="<?php echo $tab == 'performance' ? 'active' : '';?>"><a class="nav-tab-daily-performance-analytics" href="#tab-daily-performance-analytics" aria-controls="daily-performance-analytics" role="tab" data-toggle="tab">Daily Performance Analytics</a></li>
    				<li role="presentation" class="<?php echo $tab == 'visuals' ? 'active' : '';?>"><a class="nav-tab-visuals" href="#tab-visuals" aria-controls="visuals" role="tab" data-toggle="tab">Visuals</a></li>
    			</ul>
    			<div class="tab-content">
    				<div role="tabpanel" class="tab-pane <?php echo $tab == 'financial' ? 'active' : '';?>" id="tab-financial-overview">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                            	<div class="sk-spinner sk-spinner-double-bounce"><div class="sk-double-bounce1"></div><div class="sk-double-bounce2"></div></div>
                            	<form action="" method="post" id="frmReport" autocomplete="off">
                            		<input type="hidden" name="generate_report" value="1" />
                            		<div class="row m-b-md">
                                        <div class="col-md-3">
                                            <label><?php __('lblFilterByDriver'); ?></label>
                
                                            <select name="driver_id" id="driver_id" class="form-control">
                            					<option value="">-- <?php __('lblAll');?> --</option>
                            					<?php
                            					foreach ($tpl['driver_arr'] as $k => $v)
                            					{
                            						?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html($v['name']); ?></option><?php
                            					}
                            					?>
                            				</select>
                                        </div><!-- /.col-sm-3 -->
                						<div class="col-md-3">
                                            <label><?php __('lblFilterByVehicle'); ?></label>
                
                                            <select name="vehicle_id" id="vehicle_id" class="form-control">
                            					<option value="">-- <?php __('lblAll');?> --</option>
                            					<option value="own_vehicles"><?php __('lblReportFilterByOwnVehicles');?></option>
                            					<?php
                            					foreach ($tpl['vehicle_arr'] as $k => $v)
                            					{
                            					    ?><option value="<?php echo $v['id']; ?>"><?php echo pjSanitize::html($v['name']); ?> (<?php echo pjSanitize::html($v['registration_number']);?>)</option><?php
                            					}
                            					?>
                            				</select>
                                        </div><!-- /.col-sm-3 -->
                                        <div class="col-md-4">
                                            <label><?php __('lblDate'); ?></label>
                
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                
                                                    <input type="text" name="date_from" id="date_from" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date_from']));?>" class="form-control" readonly>
                
                                                    <span class="input-group-addon"><?php __('lblTo'); ?></span>
                
                                                    <input type="text" name="date_to" id="date_to" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['date_to']));?>" class="form-control" readonly>
                                                </div>
                                            </div><!-- /.form-group -->
                                        </div>
                						<div class="col-md-2">
                							<label>&nbsp;</label>
                							<div class="form-group m-b-md">
                								<a id="pjFdPrintReprot" href="#" data-href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionPrint" target="_blank" class="btn btn-primary btn-outline"><i class="fa fa-print"></i> <?php __('btnPrint');?></a>
                							</div>
                						</div>
                                    </div><!-- /.row -->
                                </form>
            
                                <div class="hr-line-dashed"></div>
                                
            					<div id="pjFdReportContent">
                                    
                                </div><!-- /#pjFdReportContent -->
                            </div>
                        </div>
                 	</div>
                 	
                 	<div role="tabpanel" class="tab-pane <?php echo $tab == 'performance' ? 'active' : '';?>" id="tab-daily-performance-analytics">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                            	<div class="sk-spinner sk-spinner-double-bounce"><div class="sk-double-bounce1"></div><div class="sk-double-bounce2"></div></div>
                            	<form action="" method="post" id="frmDailyPerformanceReport" autocomplete="off">
                            		<input type="hidden" name="performance_report" value="1" />
                                    <div class="row m-b-md">
                                        <div class="col-md-3 col-sm-6">
                                            <label>Select Report View</label>
                
                                            <select name="report_selector" id="report_selector" class="form-control">
                            					<option value="drivers" <?php echo $type == 'drivers' ? 'selected="selected"' : '';?>>Driver Rankings</option>
                                                <option value="vehicles" <?php echo $type == 'vehicles' ? 'selected="selected"' : '';?>>Vehicle Statistics</option>
                                                <option value="destinations" <?php echo $type == 'destinations' ? 'selected="selected"' : '';?>>Destination Top List</option>
                            				</select>
                                        </div><!-- /.col-sm-3 -->
                						
                                        <div class="col-md-3 col-sm-6">
                                            <label><?php __('lblDate'); ?></label>
                
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" name="date" value="<?php echo date($tpl['option_arr']['o_date_format']);?>" class="form-control" readonly>
                									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div><!-- /.form-group -->
                                        </div>
                                    </div><!-- /.row -->
                                </form>
            
                                <div class="hr-line-dashed"></div>
                                
            					<div id="pjFdDailyPerformanceReport">
                                    
                                </div><!-- /#pjFdReportContent -->
                            </div>
                        </div>
                 	</div>
                 	<div role="tabpanel" class="tab-pane <?php echo $tab == 'visuals' ? 'active' : '';?>" id="tab-visuals">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                            	<div class="sk-spinner sk-spinner-double-bounce"><div class="sk-double-bounce1"></div><div class="sk-double-bounce2"></div></div>
                            	<form action="" method="post" id="frmVisualsReport" autocomplete="off">
                            		<input type="hidden" name="visuals_report" value="1" />
                                    <div class="row m-b-md">
                                        <div class="col-md-4 col-sm-6">
                                            <label><?php __('lblDate'); ?></label>
                							<div class="form-group">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                
                                                    <input type="text" name="visual_date_from" id="visual_date_from" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['first_daye_of_month']));?>" class="form-control" readonly>
                
                                                    <span class="input-group-addon"><?php __('lblTo'); ?></span>
                
                                                    <input type="text" name="visual_date_to" id="visual_date_to" value="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['last_daye_of_month']));?>" class="form-control" readonly>
                                                </div>
                                            </div><!-- /.form-group -->
                                        </div>
                                        
                                        <div class="col-md-2">
                							<label>&nbsp;</label>
                							<div class="form-group m-b-md">
                								<a href="javascript:void(0);" class="btn btn-primary btn-outline btnGenerateVisualReport">Generate Report</a>
                							</div>
                						</div>
                						
                                    </div><!-- /.row -->
                                </form>
            
                                <div class="hr-line-dashed"></div>
                                
            					<div id="pjFdVisualsReport">
                                    <div class="" style="background: #f4f7f6; padding: 20px;">
                                        <div class="row">
                                        	<div class="col-md-6">
                                                <div class="card shadow-sm mb-4" style="background: white; border-radius: 8px; padding: 15px;">
                                                    <canvas id="chartDrivers" height="250"></canvas>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card shadow-sm mb-4" style="background: white; border-radius: 8px; padding: 15px;">
                                                    <canvas id="chartFleet" height="250"></canvas>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                        <br/>
                                        <div class="row">
                                        	<div class="col-md-6">
                                                <div class="card shadow-sm mb-4" style="background: white; border-radius: 8px; padding: 15px;">
                                                    <canvas id="chartDestinations" height="250"></canvas>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="card shadow-sm mb-4" style="background: white; border-radius: 8px; padding: 15px;">
                                                    <canvas id="chartAirport" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card shadow-sm mb-4" style="background: white; border-radius: 8px; padding: 15px;">
                                                    <canvas id="chartProvider" height="250"></canvas>
                                                </div>
                                            </div>
                                    
                                            <div class="col-md-6">
                                                <div class="card shadow-sm mb-4" style="background: white; border-radius: 8px; padding: 15px;">
                                                    <canvas id="chartPayment" height="250"></canvas>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                </div><!-- /#pjFdReportContent -->
                            </div>
                        </div>
                 	</div>
            	</div>
        	</div>
        </div>
    </div>
</div>