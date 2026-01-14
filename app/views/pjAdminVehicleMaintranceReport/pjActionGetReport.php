<?php 
$total_cost = 0;
foreach ($tpl['arr'] as $val) {
    $total_cost += $val['cost'];
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="well text-center" style="background: #fff; border: 2px solid #337ab7;">
            <h4 class="text-uppercase" style="color: #777;"><?php __('lblTotalServiceCost');?></h4>
            <h1 id="displayTotalCost" style="color: #337ab7; font-weight: bold; margin-top: 10px;"><?php echo pjCurrency::formatPrice($total_cost);?></h1>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-hover" id="reportTable">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th><?php __('lblReportServiceDate');?></th>
                    <th><?php __('lblReportServiceType');?></th>
                    <th><?php __('lblReportServiceKilometers');?></th>
                    <th><?php __('lblReportServiceStation');?></th>
                    <th class="text-right"><?php __('lblReportCost')?></th>
                </tr>
            </thead>
            <tbody>
            	<?php if ($tpl['arr']) { ?>
            		<?php foreach ($tpl['arr'] as $val) { ?>
            			<tr>
                            <td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($val['date']));?></td>
                            <td><?php echo pjSanitize::html($val['service_type']);?></td>
                            <td><?php echo pjSanitize::html($val['km']);?> km</td>
                            <td><?php echo pjSanitize::html($val['service_station']);?></td>
                            <td class="text-right"><?php echo pjCurrency::formatPrice($val['cost']);?></td>
                         </tr>
            		<?php } ?>
            	<?php } else { ?>
                	<tr><td colspan="5" class="text-center"><?php __('lblReportEmpty');?></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>