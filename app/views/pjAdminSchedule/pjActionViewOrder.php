<?php 
$is_airport_to_city = $is_city_to_airport = $is_city_to_city = false;
if ((int)$tpl['arr']['return_id'] > 0) {
	if ((int)$tpl['arr']['return_pickup_is_airport'] == 1 && (int)$tpl['arr']['return_dropoff_is_airport'] == 0) {
		$is_airport_to_city = true;
	} elseif ((int)$tpl['arr']['return_pickup_is_airport'] == 0 && (int)$tpl['arr']['return_dropoff_is_airport'] == 1) {
		$is_city_to_airport = true;
	} else {
		$is_city_to_city = true;
	}
} else {
	if ((int)$tpl['arr']['pickup_is_airport'] == 1 && (int)$tpl['arr']['dropoff_is_airport'] == 0) {
		$is_airport_to_city = true;
	} elseif ((int)$tpl['arr']['pickup_is_airport'] == 0 && (int)$tpl['arr']['dropoff_is_airport'] == 1) {
		$is_city_to_airport = true;
	} else {
		$is_city_to_city = true;
	}
}
?>
<input type="hidden" name="booking_id" id="booking_id" value="<?php echo $tpl['arr']['id'];?>" />
<?php if (!$controller->isDriver()) { ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only"><?php __('btnClose');?></span></button>
</div>
<div class="pjSbDriverSchedule">
<?php } ?>
<div class="row">
	<div class="col-xs-12 text-center">
		<?php if ($controller->isDriver()) { ?>
			<a href="javascript: void(0);" class="pjSbDriverViewSchedule pull-left" data-date="<?php echo date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['booking_date']));?>"><i class="fa fa-long-arrow-left "></i></a>
		<?php } ?>
		<span class="pjSbTransfersDetailsTitle"><?php __('lblTransfersDetails');?></span>
	</div>
</div>
<?php if (!$controller->isDriver()) { ?>
	<div class="text-center"><a href="javascript: void(0);" class="pjSbLnkAddNotesForDriver btn btn-primary btn-outline btn-sm" data-id="<?php echo $tpl['arr']['id'];?>"><?php __('btnAddNotesForDriver');?></a></div>
<?php } ?>
<h2 class="text-center"><?php __('lblReferenceId');?>: <?php echo !empty($tpl['arr']['return_uuid']) ? pjSanitize::html($tpl['arr']['return_uuid']) : pjSanitize::html($tpl['arr']['uuid']);?></h2>
<div class="row">
	<div class="<?php echo $controller->isDriver() ? 'col-md-6 col-md-offset-3' : 'col-md-10 col-md-offset-1'?> pjSbViewOrderDetails">
		<?php if (!empty($tpl['arr']['internal_notes'])) { ?>
			<div class="pjSbDriverInterNotes">
				<?php __('lblOrderInternalNotes');?>:<br/>
				<?php echo !empty($tpl['arr']['internal_notes']) ? nl2br($tpl['arr']['internal_notes']) : '';?>
			</div>
		<?php } 
		if ($is_airport_to_city) {
			include PJ_VIEWS_PATH.'pjAdminSchedule/elements/airport_to_city.php';
		} elseif ($is_city_to_airport) {
			include PJ_VIEWS_PATH.'pjAdminSchedule/elements/city_to_airport.php';
		} else {
			include PJ_VIEWS_PATH.'pjAdminSchedule/elements/city_to_city.php';
		}
		?>		
	</div>
</div>
<?php if (!$controller->isDriver()) { ?>
</div>
<?php } ?>