<?php 
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
<h2 class="text-warning"><?php __('lblReportTotal');?></h2>
<div class="row">
    <div class="col-lg-3 col-md-3 col-xs-6">
        <div class="form-group">
            <label class="control-label"><?php __('lblTotalBookings'); ?>:</label>

            <div><?php echo count($tpl['order_arr']);?></div>
        </div><!-- /.form-group -->
    </div>

    <div class="col-lg-3 col-md-3 col-xs-6">
        <div class="form-group">
            <label class="control-label"><?php __('lblTotalAmount'); ?>:</label>

            <div><?php echo pjCurrency::formatPrice($total_amount);?></div>
        </div><!-- /.form-group -->
    </div>

    <div class="col-lg-2 col-md-2 col-xs-6">
        <div class="form-group">
            <label class="control-label"><?php __('report_paid'); ?>:</label>

            <div><?php echo pjCurrency::formatPrice($total_paid);?></div>
        </div><!-- /.form-group -->
    </div>

    <div class="col-lg-2 col-md-2 col-xs-6">
        <div class="form-group">
            <label class="control-label"><?php __('report_creditcard'); ?>:</label>

            <div><?php echo pjCurrency::formatPrice($total_cc);?></div>
        </div><!-- /.form-group -->
    </div>
    
    <div class="col-lg-2 col-md-2 col-xs-6">
        <div class="form-group">
            <label class="control-label"><?php __('report_cash'); ?>:</label>

            <div><?php echo pjCurrency::formatPrice($total_cash);?></div>
        </div><!-- /.form-group -->
    </div>
</div><!-- /.row -->

<?php foreach ($tpl['provider_arr'] as $pro) { ?>
	<div class="hr-line-dashed"></div>
	<h2 class="text-warning"><?php echo __('lblReportProvider', true).' '.pjSanitize::html($pro['name']);?></h2>
    <div class="row">
        <div class="col-lg-3 col-md-3 col-xs-6">
            <div class="form-group">
                <label class="control-label"><?php __('lblTotalBookings'); ?>:</label>
    
                <div><?php echo $sum_report_per_provider[$pro['url']]['total_booking'];?></div>
            </div><!-- /.form-group -->
        </div>
    
        <div class="col-lg-3 col-md-3 col-xs-6">
            <div class="form-group">
                <label class="control-label"><?php __('lblTotalAmount'); ?>:</label>
    
                <div><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_amount']);?></div>
            </div><!-- /.form-group -->
        </div>
    
        <div class="col-lg-2 col-md-2 col-xs-6">
            <div class="form-group">
                <label class="control-label"><?php __('report_paid'); ?>:</label>
    
                <div><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_paid']);?></div>
            </div><!-- /.form-group -->
        </div>
    
        <div class="col-lg-2 col-md-2 col-xs-6">
            <div class="form-group">
                <label class="control-label"><?php __('report_creditcard'); ?>:</label>
    
                <div><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_cc']);?></div>
            </div><!-- /.form-group -->
        </div>
        
        <div class="col-lg-2 col-md-2 col-xs-6">
            <div class="form-group">
                <label class="control-label"><?php __('report_cash'); ?>:</label>
    
                <div><?php echo pjCurrency::formatPrice($sum_report_per_provider[$pro['url']]['total_cash']);?></div>
            </div><!-- /.form-group -->
        </div>
    </div><!-- /.row -->
<?php } ?>

<?php if (isset($tpl['vehicle_id']) && (int)$tpl['vehicle_id'] > 0) { ?>
    <div class="hr-line-dashed"></div>
    
    <div class="table-responsive table-responsive-secondary">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><?php __('report_date');?></th>
                    <th><?php __('report_from_to');?></th>
                    <th class="text-right"><?php __('report_paid');?></th>
                    <th class="text-right"><?php __('report_creditcard');?></th>
    				<th class="text-right"><?php __('report_cash');?></th>
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
    </div>
<?php } ?>