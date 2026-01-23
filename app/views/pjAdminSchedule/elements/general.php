<form action="" method="get" class="pjSbScheduleForm" >
	<input type="hidden" name="lock_orders" id="lock_orders" value="0" />
	<input type="hidden" class="assign_sel_date" name="selected_date" value="<?php echo date('Y-m-d'); ?>">
	<div class="row">
		<div class="col-lg-7 col-md-5 col-sm-6">
			<div class="form-group">
				<button class="btn btn-primary btn-outline btn-print btnFilterOrder" data-date="<?php echo date($tpl['option_arr']['o_date_format']);?>" type="button"><i class="fa fa-calendar m-r-xs"></i><?php __('btn_today'); ?></button>&nbsp;
				<button class="btn btn-primary btn-outline btn-print btnFilterOrder" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime('+1 day'));?>" type="button"><i class="fa fa-calendar m-r-xs"></i><?php __('btn_tomorrow'); ?></button>&nbsp;
				<button class="btn btn-primary btn-outline btn-print btnLockOrder" type="button"><i class="fa fa-lock m-r-xs"></i><?php __('btnLock'); ?></button>
				<button class="btn btn-primary btn-outline btn-print btnUnlockOrder" type="button" style="display: none;"><i class="fa fa-unlock m-r-xs"></i><?php __('btnUnlock'); ?></button>&nbsp;
				<?php if ($tpl['option_arr']['o_enable_assign_unassign_orders_button'] == 'Yes') { ?>
					<button class="btn btn-primary btn-outline btn-print btnAssignOrders" type="button"><i class="fa fa-car m-r-xs"></i> <?php __('btnAssignOrders'); ?></button>&nbsp;
				<?php } ?>
				<button class="btn btn-primary btn-outline btn-print assign_with_ai" type="button"><i class="fa fa-rocket m-r-xs"></i> <?php __('btnAssignWithAI'); ?></button>
				<?php if ($tpl['option_arr']['o_enable_assign_unassign_orders_button'] == 'Yes') { ?>
					<button class="btn btn-primary btn-outline btn-print reset_assign_with_ai" type="button"><i class="fa fa-refresh m-r-xs"></i> Unassign</button>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-3 col-md-4 col-sm-3 col-xs-7">
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
		<div class="col-lg-2 col-md-3 col-sm-3 col-xs-5">
			<div class="form-group"><a href="javascript:void(0);" class="pjCntOrders"></a></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6 col-xs-12">
			<button class="btn btn-primary btn-outline btnSelectMultipleOrders" type="button"><?php __('btnSelectMultipleOrders'); ?></button>
			<span class="txtAssignOrders text-danger"></span>
			<input type="hidden" name="selected_order_ids" id="selected_order_ids" />
			
			<button class="btn btn-primary btn-outline btnCheckFlights" type="button"><?php __('btnCheckFlights'); ?></button>
		</div>
		<div class="col-sm-6 col-xs-12 text-right hidden-md hidden-sm hidden-xs">
			<div class="form-group">
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
				<a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionSyncAllData"><?php __('btnSynchronizeData');?></a>
				<a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionSyncGeneralData"><?php __('btnUpdateBookings');?></a>
			</div>
		</div>
		<div class="col-xs-12 text-center visible-md visible-sm visible-xs">
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
				<a class="btn btn-primary 2" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionSyncAllData"><?php __('btnSynchronizeData');?></a>
				<a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionSyncGeneralData"><?php __('btnUpdateBookings');?></a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-3 col-md-4 col-xs-12">
			<div class="input-group">
				<input type="text" name="q" placeholder="<?php __('btnSearch', false, true); ?>" class="form-control">
				<div class="input-group-btn">
					<button class="btn btn-primary" type="submit">
						<i class="fa fa-search"></i>
					</button>
				</div>
			</div>
			<div class="pjSbScheduleRow">
			<ol class="pjSbOrdersList pjSbOrders list-unstyled" data-vehicle_id="0">
				
			</ol>
			</div>
		</div>
		<div class="col-lg-9 col-md-8 col-xs-12 pj-loader-outer">
			<div class="pj-loader"></div>
			<div class="pjSbVehicles">
				
			</div>
		</div>
	</div>
</form>