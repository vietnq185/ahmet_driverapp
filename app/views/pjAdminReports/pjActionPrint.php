<p><?php __('lblDate'); ?>: <strong><?php echo $controller->_get->toString('date_from');?></strong> <?php __('lblTo'); ?> <strong><?php echo $controller->_get->toString('date_to');?></strong> </p>
<?php
if(isset($tpl['driver']))
{
    ?><p><?php __('report_driver');?>: <strong><?php echo pjSanitize::html($tpl['driver']['name']);?></strong></p><?php
}
if ($tpl['vehicle_id'] == 'own_vehicles') {
    ?><p><?php __('report_vehicle');?>: <strong><?php __('lblReportFilterByOwnVehicles');?></strong></p><?php
} elseif(isset($tpl['vehicle'])) {
    ?><p><?php __('report_vehicle');?>: <strong><?php echo pjSanitize::html($tpl['vehicle']['name']);?></strong></p><?php
}

$sum_report_per_provider = array();
foreach ($tpl['provider_arr'] as $pro) {
    $sum_report_per_provider[$pro['url']]['total_booking'] = 0;
    $sum_report_per_provider[$pro['url']]['total_amount'] = 0;
    $sum_report_per_provider[$pro['url']]['total_cash'] = 0;
    $sum_report_per_provider[$pro['url']]['total_cc'] = 0;
    $sum_report_per_provider[$pro['url']]['total_paid'] = 0;
}
$total_amount = $total_paid = $total_cc = $total_cash = 0;
foreach ($tpl['order_arr'] as $val) {
    $total_amount += $val['price'];
    $sum_report_per_provider[$val['domain']]['total_amount'] += $val['price'];
    $sum_report_per_provider[$val['domain']]['total_booking'] += 1;
    if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
        $total_cash += $val['price'];
        $sum_report_per_provider[$val['domain']]['total_cash'] += $val['price'];
    } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
        $total_cc += $val['price'];
        $sum_report_per_provider[$val['domain']]['total_cc'] += $val['price'];
    } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
        $total_paid += $val['price'];
        $sum_report_per_provider[$val['domain']]['total_paid'] += $val['price'];
    } elseif ($val['payment_method'] == 'cash'){
        $total_cash += $val['price'];
        $sum_report_per_provider[$val['domain']]['total_cash'] += $val['price'];
    } elseif ($val['payment_method'] == 'creditcard_later'){
        $total_cc += $val['price'];
        $sum_report_per_provider[$val['domain']]['total_cc'] += $val['price'];
    } else {
        $total_paid += $val['price'];
        $sum_report_per_provider[$val['domain']]['total_paid'] += $val['price'];
    }
}
?>
<h3><?php __('lblReportTotal');?></h3>
<table class="table">
	<tr>
		<th><?php __('lblTotalBookings'); ?></th>
		<th><?php __('lblTotalAmount'); ?></th>
		<th><?php __('report_paid'); ?></th>
		<th><?php __('report_creditcard'); ?></th>
		<th><?php __('report_cash'); ?></th>
	</tr>
	<tr>
		<td><?php echo count($tpl['order_arr']);?></td>
		<td><?php echo pjCurrency::formatPrice($total_amount);?></td>
		<td><?php echo pjCurrency::formatPrice($total_paid);?></td>
		<td><?php echo pjCurrency::formatPrice($total_cc);?></td>
		<td><?php echo pjCurrency::formatPrice($total_cash);?></td>
	</tr>
</table>
<br/> 

<?php foreach ($tpl['provider_arr'] as $pro) { ?>
	<br/> 
	<h3><?php echo __('lblReportProvider', true).' '.pjSanitize::html($pro['name']);?></h3>
    <table class="table">
    	<tr>
    		<th><?php __('lblTotalBookings'); ?></th>
    		<th><?php __('lblTotalAmount'); ?></th>
    		<th><?php __('report_paid'); ?></th>
    		<th><?php __('report_creditcard'); ?></th>
    		<th><?php __('report_cash'); ?></th>
    	</tr>
    	<tr>
    		<td><?php echo $sum_report_per_provider[$pro['url']]['total_booking'];?></td>
    		<td><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_amount']);?></td>
    		<td><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_paid']);?></td>
    		<td><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_cc']);?></td>
    		<td><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_cash']);?></td>
    	</tr>
    </table>
<?php } ?>

<?php if (isset($tpl['vehicle_id']) && (int)$tpl['vehicle_id'] > 0) { ?>
    <table class="table">
        <thead>
    		<tr>
                <tr>
                    <th><?php __('report_date');?></th>
                    <th><?php __('report_from_to');?></th>
                    <th class="text-right"><?php __('report_paid');?></th>
                    <th class="text-right"><?php __('report_creditcard');?></th>
    				<th class="text-right"><?php __('report_cash');?></th>
                </tr>
    		</tr>
    	</thead>
    	<tbody>
    		<?php
			foreach($tpl['order_arr'] as $v)
			{
			    $paid = $cc = $cash = 0;
			    if (!empty($v['driver_payment_status']) && in_array($v['driver_payment_status'], array(1,5))) {
			        $cash = $v['price'];
			    } elseif (!empty($v['driver_payment_status']) && in_array($v['driver_payment_status'], array(2,6))){
			        $cc = $v['price'];
			    } elseif (!empty($v['driver_payment_status']) && in_array($v['driver_payment_status'], array(8))){
			        $paid = $v['price'];
			    } elseif ($v['payment_method'] == 'cash'){
			        $cash = $v['price'];
			    } elseif ($v['payment_method'] == 'creditcard_later'){
			        $cc = $v['price'];
			    } else {
			        $paid = $v['price'];
			    }
				?>
				<tr>
					<td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['booking_date']));?></td>
					<td><?php echo (int)$v['return_id'] > 0 ? pjSanitize::html($v['location2']) : pjSanitize::html($v['location']);?> - <?php echo (int)$v['return_id'] > 0 ? pjSanitize::html($v['dropoff2']) : pjSanitize::html($v['dropoff']);?></td>
					<td class="text-right"><?php echo pjCurrency::formatPrice($paid);?></td>
					<td class="text-right"><?php echo pjCurrency::formatPrice($cc);?></td>
					<td class="text-right"><?php echo pjCurrency::formatPrice($cash);?></td>
				</tr>
				<?php
			} 
			?>
    	</tbody>
    </table>
<?php } ?>