<table class="table table-hover table-striped" id="tableServices" style="background: white; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background: #f1f4f7; color: #337ab7;">
            <th><?php __('lblServiceDate');?></th>
            <th><?php __('lblServiceType');?></th>
            <th><?php __('lblServiceKm');?></th>
            <th><?php __('lblServiceCost');?></th>
            <th class="text-center"><?php __('lblServiceAction');?></th>
        </tr>
    </thead>
    <tbody>
    	<?php if ($tpl['arr']) { ?>
    		<?php foreach ($tpl['arr'] as $val) { ?>
                <tr>
                	<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date']));?></td>
                    <td><?php echo $val['service_types'];?></td>
                    <td><?php echo pjSanitize::html($val['km']);?> km</td>
                    <td><?php echo pjCurrency::formatPrice($val['cost']);?></td>
                    <td class="text-center">
                        <button class="btn btn-link btn-xs text-danger btn-edit-service" data-id="<?php echo $val['id'];?>" title="<?php __('btnEdit');?>">
                            <i class="fa fa-edit fa-lg"></i>
                        </button>
                        <button class="btn btn-link btn-xs text-danger btn-delete-service" data-id="<?php echo $val['id'];?>" title="<?php __('btnDelete');?>">
                            <i class="fa fa-trash-o fa-lg"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
        	<tr>
        		<td colspan="5" align="center"><?php __('lblRecentServicesEmpty');?></td>
        	</tr>
        <?php } ?>
    </tbody>
</table>