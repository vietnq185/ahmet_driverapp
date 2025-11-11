<?php if ($tpl['arr']) { ?>
	<?php foreach ($tpl['arr'] as $sr) { ?>
	<div class="pjSbVehicleServiceRepairItem">
		<div class="m-t-xs text-right">
			<a href="javascript:void(0);" class="btn btn-primary btn-outline btn-xs m-l-xs btnUpdateServiceRepair" data-id="<?php echo $sr['id'];?>"><i class="fa fa-pencil"></i></a>
			<a href="javascript:void(0);" class="btn btn-danger btn-outline btn-xs m-l-xs btnDeleteServiceRepair" data-vehicle_id="<?php echo $sr['vehicle_id'];?>" data-id="<?php echo $sr['id'];?>"><i class="fa fa-trash"></i></a>
		</div>
		<div><strong><?php __('lblVehicleServiceDate');?>:</strong> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($sr['date']));?></div>
		<div><strong><?php __('lblVehicleServiceKm');?>:</strong> <?php echo pjSanitize::html($sr['km']);?></div>
		<div><strong><?php __('lblVehicleServiceRepair');?>:</strong> <?php echo pjSanitize::html($sr['service_repair']);?></div>
	</div>
	<?php } ?>
<?php } ?>