<form action="" method="get" class="pjSbScheduleForm" >
	<div class="row">
		<div class="col-lg-3 col-md-4 col-sm-5">
			<div class="form-group">
				<button class="btn btn-primary btn-outline btn-print btnFilterOrder" data-date="<?php echo date($tpl['option_arr']['o_date_format']);?>" type="button"><i class="fa fa-calendar m-r-xs"></i><?php __('btn_today'); ?></button>&nbsp;&nbsp;
				<button class="btn btn-primary btn-outline btn-print btnFilterOrder" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime('+1 day'));?>" type="button"><i class="fa fa-calendar m-r-xs"></i><?php __('btn_tomorrow'); ?></button>
							</div>
						</div>
						<div class="col-lg-3 col-md-4 col-sm-4 col-xs-7">
							<div class="form-group">
								<div class="input-group date"
                                     data-provide="datepicker"
                                     data-date-autoclose="true"
                                     data-date-format="<?php echo $jqDateFormat ?>"
                                     data-date-week-start="<?php echo (int) $tpl['option_arr']['o_week_start'] ?>">
					<input type="text" name="date" id="date" class="form-control" value="<?php echo date($tpl['option_arr']['o_date_format']); ?>" autocomplete="off">
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>
		</div>
		<div class="col-lg-2 col-md-4 col-sm-3 col-xs-5">
			<div class="form-group"><a href="javascript:void(0);" class="pjCntOrders"></a></div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 text-right hidden-md hidden-sm hidden-xs">
			<div class="form-group">
				<a class="btn btn-primary 1" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionSyncGeneralData"><?php __('btnUpdateBookings');?></a>
				<?php 
				$last_update = '';
				if (!empty($tpl['option_arr']['o_last_update_data'])) { 
					if (date('Ymd', strtotime($tpl['option_arr']['o_last_update_data'])) == date('Ymd')) {
						$last_update = __('lblToday', true).', '.date($tpl['option_arr']['o_time_format'], strtotime($tpl['option_arr']['o_last_update_data']));
					} else {
						$last_update = date($tpl['option_arr']['o_date_format'].', '.$tpl['option_arr']['o_time_format'], strtotime($tpl['option_arr']['o_last_update_data']));
					}
				}
				echo __('lblLastUpdate', true).': '.$last_update;
				?> 
			</div>
		</div>
		<div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 text-center visible-md visible-sm visible-xs">
			<div class="form-group pjSbLastUpdate">
				<?php 
				$last_update = '';
				if (!empty($tpl['option_arr']['o_last_update_data'])) { 
					if (date('Ymd', strtotime($tpl['option_arr']['o_last_update_data'])) == date('Ymd')) {
						$last_update = __('lblToday', true).', '.date($tpl['option_arr']['o_time_format'], strtotime($tpl['option_arr']['o_last_update_data']));
					} else {
						$last_update = date($tpl['option_arr']['o_date_format'].', '.$tpl['option_arr']['o_time_format'], strtotime($tpl['option_arr']['o_last_update_data']));
					}
				}
				echo __('lblLastUpdate', true).': '.$last_update;
				?> 
			</div>
			<div class="form-group">
				<a class="btn btn-primary 2" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionSyncGeneralData"><?php __('btnUpdateBookings');?></a>
			</div>
		</div>
	</div>
</form>
<div class="row">
	<div class="col-xs-12 pj-loader-outer">
		<div class="pj-loader"></div>
		<div class="pjSbVehicles pjSbDriverSchedule">
			
		</div>
	</div>
</div>