<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-lg-9 col-md-8 col-sm-6">
					<h2><?php __('infoVehiclesTitle'); ?></h2>
				</div>
				<div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                    <?php if ($tpl['is_flag_ready']) : ?>
					<div class="multilang"></div>
					<?php endif; ?>    
            	</div>
			</div>
			<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoVehiclesBody'); ?></p>
		</div>
	</div>
	 
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-9">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<div class="row">
						<div class="col-lg-6">
							<form action="" method="get" class="form-horizontal frm-filter">
                                <div class="input-group">
									<input type="text" name="q" placeholder="<?php __('btnSearch', false, true); ?>" class="form-control">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
						</div><!-- /.col-lg-6 -->
					</div><!-- /.row -->
					<div id="grid"></div>
				</div>
			</div>
		</div><!-- /.col-lg-8 -->
	
		<div class="col-lg-3">
			<div class="panel no-borders boxFormVehicle">
				<?php 
				if(pjAuth::factory('pjAdminVehicles', 'pjActionCreate')->hasAccess())
				{
				    include_once dirname(__FILE__) . '/elements/add-vehicle.php';
				}
				?>
			</div><!-- /.panel panel-primary -->
		</div><!-- /.col-lg-4 -->
	</div>
	
	<div class="modal inmodal fade" id="modalAddServiceRepair" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	<div class="modal inmodal fade" id="modalUpdateServiceRepair" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	myLabel.selected_vehicle_id = 0;
	<?php if (isset($get['id']) && (int)$get['id'] > 0)
	{
		?>
		myLabel.selected_vehicle_id = <?php echo (int)$get['id'];?>;
		<?php 
	}
	?>
	pjGrid.hasAccessCreate = <?php echo pjAuth::factory('pjAdminVehicles', 'pjActionCreate')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessUpdate = <?php echo pjAuth::factory('pjAdminVehicles', 'pjActionUpdate')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessDeleteSingle = <?php echo pjAuth::factory('pjAdminVehicles', 'pjActionDelete')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessDeleteMulti = <?php echo pjAuth::factory('pjAdminVehicles', 'pjActionDeleteBulk')->hasAccess() ? 'true' : 'false';?>;
	
	myLabel.vehicle_name = "<?php __('lblVehicleName'); ?>";
	myLabel.vehicle_reg_num = "<?php __('lblVehicleRegistrationNumber'); ?>";
	myLabel.vehicle_seats = "<?php __('lblVehicleSeats'); ?>";
	myLabel.vehicle_order = "<?php __('lblVehicleOrder'); ?>";
	myLabel.vehicle_status = "<?php __('lblStatus'); ?>";
	myLabel.active = "<?php __('filter_ARRAY_active'); ?>";
	myLabel.inactive = "<?php __('filter_ARRAY_inactive'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.isFlagReady = "<?php echo $tpl['is_flag_ready'] ? 1 : 0;?>";
	myLabel.choose = "<?php __('lblChoose', false, true); ?>";
	myLabel.vehicle_same_reg = "<?php __('vehicle_same_reg'); ?>";
	myLabel.months = "<?php echo implode("_", $months);?>";
	myLabel.days = "<?php echo implode("_", $short_days);?>";
	myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
	myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;	
	<?php if ($tpl['is_flag_ready']) : ?>
		var pjLocale = pjLocale || {};
		pjLocale.langs = <?php echo $tpl['locale_str']; ?>;
		pjLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
	<?php endif; ?>
	</script>