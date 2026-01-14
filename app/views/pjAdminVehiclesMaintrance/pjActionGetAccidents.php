<table class="table table-hover table-striped" id="tableAccidents" style="background: white; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background: #f1f4f7; color: #337ab7;">
            <th><?php __('lblAccidentDate');?></th>
            <th><?php __('lblAccidentDriver');?></th>
            <th><?php __('lblAccidentLocation');?></th>
            <th class="text-center"><?php __('lblAccidentAction');?></th>
        </tr>
    </thead>
    <tbody>
    	<?php if ($tpl['arr']) { ?>
    		<?php foreach ($tpl['arr'] as $val) { ?>
    			<tr>
                    <td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date'])).', '.date($tpl['option_arr']['o_time_format'], strtotime($val['time']))?></td>
                    <td><?php echo pjSanitize::html($val['driver_name']);?></td>
                    <td><?php echo pjSanitize::html($val['location_accident']);?></td>
                    <td class="text-center">
                    	<button class="btn btn-link btn-xs text-danger btn-edit-accident" data-id="<?php echo $val['id'];?>" title="<?php __('btnEdit');?>">
                            <i class="fa fa-edit fa-lg"></i>
                        </button>
                        <button class="btn btn-link btn-xs text-danger btn-delete-accident" data-id="<?php echo $val['id'];?>" title="<?php __('btnDelete');?>">
                            <i class="fa fa-trash-o fa-lg"></i>
                        </button>
                    </td>
                </tr>
    		<?php } ?>
        <?php } else { ?>
        	<tr>
        		<td colspan="4" align="center"><?php __('lblRecentAccidentsEmpty');?></td>
        	</tr>
        <?php } ?>
    </tbody>
</table>