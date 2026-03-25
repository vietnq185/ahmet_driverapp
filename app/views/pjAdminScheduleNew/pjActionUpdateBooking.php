<?php foreach ($tpl['vehicle_arr'] as $k => $veh) { ?>
	<select class="form-control select-item pjSbDriverSelector" name="driver_id[1][<?php echo $veh['id'];?>]" data-vehicle_id="<?php echo $veh['id'];?>" data-order="1">
		<option value="" data-driver_name="<?php __('lblNoSelected', true);?>"><?php __('lblSelect');?></option>
		<?php foreach ($tpl['driver_arr'] as $driver) { 
			if (!in_array($driver['id'], @$tpl['assigned_driver_arr'][1]) || (isset($tpl['driver_vehicle_arr'][$veh['id']][1]) && $tpl['driver_vehicle_arr'][$veh['id']][1] == $driver['id']))
			{
			?>
			<option value="<?php echo $driver['id'];?>" data-driver_name="<?php echo pjSanitize::html($driver['name']);?>" <?php echo isset($tpl['driver_vehicle_arr'][$veh['id']][1]) && $tpl['driver_vehicle_arr'][$veh['id']][1] == $driver['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($driver['name']);?></option>
		<?php } 
		}
		?>
	</select>
	--LIMITER--
<?php } ?>
<?php foreach ($tpl['vehicle_arr'] as $k => $veh) { ?>
	<select class="form-control select-item pjSbDriverSelector" name="driver_id[2][<?php echo $veh['id'];?>]" data-vehicle_id="<?php echo $veh['id'];?>" data-order="2">
		<option value="" data-driver_name="<?php __('lblNoSelected', true);?>"><?php __('lblSelect');?></option>
		<?php foreach ($tpl['driver_arr'] as $driver) { 
			if (!in_array($driver['id'], @$tpl['assigned_driver_arr'][2]) || (isset($tpl['driver_vehicle_arr'][$veh['id']][2]) && $tpl['driver_vehicle_arr'][$veh['id']][2] == $driver['id']))
			{ ?>
			<option value="<?php echo $driver['id'];?>" data-driver_name="<?php echo pjSanitize::html($driver['name']);?>" <?php echo isset($tpl['driver_vehicle_arr'][$veh['id']][2]) && $tpl['driver_vehicle_arr'][$veh['id']][2] == $driver['id'] ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($driver['name']);?></option>
		<?php } 
		}
		?>
	</select>
	--LIMITER--
<?php } ?>