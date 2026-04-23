<?php 
$total_bookings = $total_amount = $total_paid = $total_cc = $total_cash = $total_paysafe = 0;
foreach ($tpl['data']['report_arr'] as $val) {
    $total_bookings++;
    $price = $val['price'];
    if (isset($tpl['custom_amount_arr'][$val['id']]) && $tpl['custom_amount_arr'][$val['id']]) {
        $total_cash += $tpl['custom_amount_arr'][$val['id']]['total_cash'];
        $total_cc += $tpl['custom_amount_arr'][$val['id']]['total_cc'];
        $total_paysafe += $tpl['custom_amount_arr'][$val['id']]['total_paysafe'];
        $total_paid += $tpl['custom_amount_arr'][$val['id']]['total_paid'];
        $price = (float)$tpl['custom_amount_arr'][$val['id']]['total_cash'] + (float)$tpl['custom_amount_arr'][$val['id']]['total_cc'] + (float)$tpl['custom_amount_arr'][$val['id']]['total_paysafe'] + (float)$tpl['custom_amount_arr'][$val['id']]['total_paid'];
    } else {
        if (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(1,5))) {
            $total_cash += $val['price'];
        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(2,6))){
            $total_cc += $val['price'];
        } elseif (in_array($val['payment_method'], array('cash','creditcard_later')) && !empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
            $total_paysafe += $val['price'];
        } elseif (!empty($val['driver_payment_status']) && in_array($val['driver_payment_status'], array(8))){
            $total_paid += $val['price'];
        } elseif ($val['payment_method'] == 'cash'){
            $total_cash += $val['price'];
        } elseif ($val['payment_method'] == 'creditcard_later'){
            $total_cc += $val['price'];
        } else {
            $total_paid += $val['price'];
        }
    }
    $total_amount += $price;
}
$commission_pct = (float)$tpl['data']['partner_arr']['commission_pct'];
$commission = ($total_amount*$commission_pct)/100;
$paid_bookings_we_made = (float)$tpl['arr']['paid_bookings_we_made'];
//Billing Amount=(Paid by partner)−(Commission) - (Paid bookings we made)
$billing_amount = $total_paid + $total_paysafe - $commission - $paid_bookings_we_made;
?>
<div class="billing-summary-box">
    <h3 class="text-warning"><?php __('lblReportBillingTotal');?></h3>
    <div class="row text-center grey-box">
        <div class="col-md-2"><strong><?php __('lblReportBillingTotalBookings');?>:</strong><br><?php echo $total_bookings;?></div>
        <div class="col-md-2"><strong><?php __('lblReportBillingTotalAmount');?>:</strong><br><?php echo pjCurrency::formatPrice($total_amount);?></div>
        <div class="col-md-2"><strong><?php __('lblReportBillingPaid');?>:</strong><br><?php echo pjCurrency::formatPrice($total_paid);?></div>
        <div class="col-md-2"><strong><?php __('lblReportBillingCreditCard');?>:</strong><br><?php echo pjCurrency::formatPrice($total_cc);?></div>
        <div class="col-md-2"><strong>Paysafe QR Code:</strong><br><?php echo pjCurrency::formatPrice($total_paysafe);?></div>
        <div class="col-md-2"><strong><?php __('lblReportBillingCash');?>:</strong><br><?php echo pjCurrency::formatPrice($total_cash);?></div>
    </div>
</div>

<div class="commission-banner grey-box">
    <?php __('lblReportBillingCommission')?> <?php echo $commission_pct;?>%: <strong><?php echo pjCurrency::formatPrice($commission);?></strong>
</div>

<div class="table-responsive margin-top-20">
    <table class="table table-bordered table-condensed billing-table">
        <thead>
            <tr>
                <th><?php __('lblReportBillingDate');?></th>
                <th><?php __('lblReportBillingFromTo');?></th>
                <th><?php __('lblReportBillingPaid');?></th>
                <th><?php __('lblReportBillingCreditCard');?></th>
                <th>Paysafe QR Code</th>
                <th><?php __('lblReportBillingCash');?></th>
            </tr>
        </thead>
        <tbody>
        	<?php foreach ($tpl['data']['report_arr'] as $order) { 
        	    $paid = $cc = $cash = $paysafe = 0;
        	    if (isset($tpl['custom_amount_arr'][$order['id']]) && $tpl['custom_amount_arr'][$order['id']]) {
        	        $cash = $tpl['custom_amount_arr'][$order['id']]['total_cash'];
        	        $cc = $tpl['custom_amount_arr'][$order['id']]['total_cc'];
        	        $paysafe = $tpl['custom_amount_arr'][$order['id']]['total_paysafe'];
        	        $paid = $tpl['custom_amount_arr'][$order['id']]['total_paid'];
        	    } else {
        	        if (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(1,5))) {
        	            $cash = $order['price'];
        	        } elseif (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(2,6))){
        	            $cc = $order['price'];
        	        } elseif (in_array($order['payment_method'], array('cash','creditcard_later')) && !empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(8))){
        	            $paysafe = $order['price'];
        	        } elseif (!empty($order['driver_payment_status']) && in_array($order['driver_payment_status'], array(8))){
        	            $paid = $order['price'];
        	        } elseif ($order['payment_method'] == 'cash'){
        	            $cash = $order['price'];
        	        } elseif ($order['payment_method'] == 'creditcard_later'){
        	            $cc = $order['price'];
        	        } else {
        	            $paid = $order['price'];
        	        }
        	    }
        	    ?>
                <tr>
                    <td><?php echo date($tpl['option_arr']['o_date_format'], strtotime($order['booking_date']));?></td>
                    <td>
                    	<?php if(!empty($order['return_id'])) {
                    	    echo pjSanitize::html($order['location2'].' - '.$order['dropoff2']);
                    	} else { 
                    	    echo pjSanitize::html($order['location'].' - '.$order['dropoff']);
                    	} ?>
                    </td>
                    <td>
						<div class="input-group width-150">
                            <input type="text" class="form-control text-right save-price-trigger" data-booking_id="<?php echo $order['id'];?>" data-name="total_paid" name="total_paid[<?php echo $order['id'];?>]" value="<?php echo $paid;?>">
                            <span class="input-group-addon">€</span>
                        </div>
					</td>
                    <td>
						<div class="input-group width-150">
                            <input type="text" class="form-control text-right save-price-trigger" data-booking_id="<?php echo $order['id'];?>" data-name="total_cc" name="total_cc[<?php echo $order['id'];?>]" value="<?php echo $cc;?>">
                            <span class="input-group-addon">€</span>
                        </div>
					</td>
					<td>
						<div class="input-group width-150">
                            <input type="text" class="form-control text-right save-price-trigger" data-booking_id="<?php echo $order['id'];?>" data-name="total_paysafe" name="total_paysafe[<?php echo $order['id'];?>]" value="<?php echo $paysafe;?>">
                            <span class="input-group-addon">€</span>
                        </div>
					</td>
                    <td>
						<div class="input-group width-150">
                            <input type="text" class="form-control text-right save-price-trigger" data-booking_id="<?php echo $order['id'];?>" data-name="total_cash" name="total_cash[<?php echo $order['id'];?>]" value="<?php echo $cash;?>">
                            <span class="input-group-addon">€</span>
                        </div>
					</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="calculation-section row margin-top-20">
	<input type="hidden" name="commission_pct" value="<?php echo $commission_pct;?>" />
    <div class="col-lg-6 col-md-7">
        <div class="calc-row">
            <span><?php __('lblReportBillingTotalBookingsMadeByPartner');?>:</span>
            <div class="input-group width-150">
                <input type="text" class="form-control text-right calc-trigger" name="total_bookings_amount" value="<?php echo number_format($total_amount, 2, '.', '');?>">
                <span class="input-group-addon">€</span>
            </div>
        </div>
        <div class="calc-row">
            <span><?php __('lblReportBillingPaidBookingsMadeByPartner');?>:</span>
            <div class="input-group width-150">
                <input type="text" class="form-control text-right calc-trigger" name="paid_by_partner_amount" value="<?php echo number_format($total_paid + $total_paysafe, 2, '.', '');?>">
                <span class="input-group-addon">€</span>
            </div>
        </div>
        <div class="calc-row text-primary">
            <span><?php __('lblReportBillingCommission');?> <?php echo (float)$commission_pct;?>%:</span>
            <div class="input-group width-150">
                <input type="text" class="form-control text-right" name="commission_amount" readonly="readonly" value="<?php echo number_format($commission, 2, '.', '');?>">
                <span class="input-group-addon">€</span>
            </div>
        </div>
        <div class="calc-row text-primary">
            <span><?php __('lblReportBillingPaidBookingsFromPartnerWemade');?>:</span>
            <div class="input-group width-150">
                <input type="text" class="form-control text-right calc-trigger" name="paid_bookings_we_made" value="<?php echo number_format($paid_bookings_we_made, 2, '.', '');?>">
                <span class="input-group-addon">€</span>
            </div>
        </div>
        <div class="calc-row border-top">
            <strong><?php __('lblReportBillingBillingAmount');?>:</strong>
            <div class="input-group width-150">
                <input type="text" class="form-control text-right font-bold" name="billing_amount" readonly="readonly" value="<?php echo number_format($billing_amount, 2, '.', '');?>">
                <span class="input-group-addon">€</span>
            </div>
        </div>
        
        <div class="calc-row border-top">
            <strong><?php __('lblReportBillingStatus');?>:</strong>
            <div class="width-150">
            	<select name="status" class="form-control">
            		<?php foreach (__('report_billing_statuses', true) as $k => $v) { ?>
            			<option value="<?php echo $k;?>" <?php echo isset($tpl['arr']['status']) && $tpl['arr']['status'] == $k ? 'selected="selected"' : '';?>><?php echo $v;?></option>
            		<?php } ?>
            	</select>
            </div>
        </div>
    </div>
</div>