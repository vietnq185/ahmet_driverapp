<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$show_period = 'false';
if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
{
    $show_period = 'true';
}
?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<h2>
						<?php 
						if ($controller->isDriver()) {
							__('infoDriverScheduleTitle');
						} else {
							__('infoScheduleTitle');	
						} 
						?>
					</h2>
				</div>
			</div>
			<p class="m-b-none">
				<i class="fa fa-info-circle"></i>
				<?php 
				if ($controller->isDriver()) {
					__('infoDriverScheduleBody');
				} else {
					__('infoScheduleBody');	
				} 
				?>
			</p>
		</div>
	</div>
	
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">
			<?php if (!empty($tpl['driver_arr']['general_info_for_driver'])) { ?>
				<div class="alert alert-warning generalInfoForDriver"><?php echo nl2br($tpl['driver_arr']['general_info_for_driver']);?></div>
			<?php } ?>
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<?php
					if ($controller->isDriver()) {
						include PJ_VIEWS_PATH.'pjAdminSchedule/elements/driver.php';
					} else {
						include PJ_VIEWS_PATH.'pjAdminSchedule/elements/general.php';
					}
					?>
				</div>
			</div>
		</div><!-- /.col-lg-8 -->
	</div>
	
	<div class="modal inmodal fade" id="modalSms" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalTurnover" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalViewOrder" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalAddNotesForDriver" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalWhatsappSms" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div class="modal inmodal fade" id="modalChangePickupTime" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog modal-sm">
	        <div class="modal-content"></div>
	    </div>
	</div>
	
	<div id="popupMessage" style="display: none;"><?php echo isset($tpl['popup_message']) ? implode('<br/>', $tpl['popup_message']) : '';?></div>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.choose = "<?php __('lblChoose');?>";
	myLabel.months = "<?php echo implode("_", $months);?>";
	myLabel.days = "<?php echo implode("_", $short_days);?>";
	myLabel.alert_title = "<?php echo __('del_booking_title');?>";
	myLabel.alert_text = "<?php echo __('del_booking_body');?>";
	myLabel.btn_delete = "<?php echo __('btnDelete');?>";
	myLabel.btn_cancel = "<?php echo __('btnCancel');?>";
	myLabel.isDriver = <?php echo $controller->isDriver() ? 'true' : 'false';?>;
	myLabel.show_popup = "<?php echo isset($tpl['popup_message']) && !empty($tpl['popup_message']) ? 1 : 0; ?>";
	myLabel.showperiod = <?php echo $show_period; ?>;
	</script>