<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
if ($controller->_get->check('date') && $controller->_get->toString('date') != "") {
    $date = $controller->_get->toString('date');
} else {
    $date = date($tpl['option_arr']['o_date_format']);
}
?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<h2><?php __('infoLogsTitle'); ?></h2>
				</div>
			</div>
			<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoLogsBody'); ?></p>
		</div>
	</div>
	
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<div class="row">
						<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
							<form action="" method="get" class="frm-filter">
								<div class="form-group">
                                    <div class="input-group date"
                                         data-provide="datepicker"
                                         data-date-autoclose="true"
                                         data-date-format="<?php echo $jqDateFormat ?>"
                                         data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
                    					<input type="text" name="date" id="date" class="form-control" value="<?php echo $date;?>" autocomplete="off">
                    					<span class="input-group-addon">
                    						<span class="glyphicon glyphicon-calendar"></span>
                    					</span>
                    				</div>
                    			</div>
                            </form>
						</div><!-- /.col-lg-6 -->
					</div><!-- /.row -->
					<div id="grid"></div>
				</div>
			</div>
		</div><!-- /.col-lg-8 -->
	
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	pjGrid.hasAccessDeleteSingle = <?php echo pjAuth::factory('pjAdminLogs', 'pjActionDelete')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessDeleteMulti = <?php echo pjAuth::factory('pjAdminLogs', 'pjActionDeleteBulk')->hasAccess() ? 'true' : 'false';?>;
	
	myLabel.log_content = "<?php __('lblLogContent'); ?>";
	myLabel.log_by = "<?php __('lblLogBy'); ?>";
	myLabel.log_created = "<?php __('lblLogCreated'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.months = "<?php echo implode("_", $months);?>";
	myLabel.days = "<?php echo implode("_", $short_days);?>";
	</script>