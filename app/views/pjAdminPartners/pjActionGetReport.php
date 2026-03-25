<?php 
$report_billing_statuses = __('report_billing_statuses', true);
?>
<table class="table table-hover table-striped" id="tablePartnerReport" style="background: white; border-radius: 8px; overflow: hidden;">
    <thead>
        <tr style="background: #f1f4f7; color: #337ab7;">
            <th><?php __('lblPartnerReportPeriod');?></th>
            <th><?php __('lblPartnerReportCreated');?></th>
            <th><?php __('lblPartnerReportReportFile');?></th>
            <th class="text-center"><?php __('lblPartnerReportStatus');?></th>
            <th class="text-center"><?php __('lblPartnerReportAction');?></th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach ($tpl['arr'] as $val) { 
    	    $download_url = $_SERVER['PHP_SELF'] . "?controller=pjAdminPartners&amp;action=pjActionDownloadReport&amp;id=" . $val['id'];
    	    ?>
            <tr>
            	<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date_from']))." - ".date($tpl['option_arr']['o_date_format'], strtotime($val['date_to']));?></td>
                <td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['created']));?></td>
                <td><a href="<?php echo $download_url; ?>" title="<?php echo pjSanitize::html(basename($val['pdf_path']));?>"><?php echo pjSanitize::html(basename($val['pdf_path']));?></a></td>
                <td class="text-center"><span class="label label-status status-<?php echo $val['status'];?>"><?php echo $report_billing_statuses[$val['status']];?></span></td>
                <td class="text-center">
                    <button class="btn btn-link btn-xs text-danger btn-edit-report" data-id="<?php echo $val['id'];?>" title="<?php __('btnEdit');?>">
                        <i class="fa fa-edit fa-lg"></i>
                    </button>
                    <button class="btn btn-link btn-xs text-danger btn-delete-report" data-id="<?php echo $val['id'];?>" title="<?php __('btnDelete');?>">
                        <i class="fa fa-trash-o fa-lg"></i>
                    </button>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>