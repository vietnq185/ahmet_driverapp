<?php $report_selector = $controller->_post->toString('report_selector');?>
<?php if ($report_selector == 'destinations') { ?>
	<div class="table-responsive">
        <table class="table table-bordered table-destinations">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Destination Address</th>
                    <th class="text-center">Total Bookings</th>
                    <th class="text-center">Market Share (%)</th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($tpl['top_destination_arr'] as $rank => $val) { ?>
                    <tr>
                        <td><?php echo ($rank+1);?></td>
                        <td><?php echo pjSanitize::html($val['destination']);?></td>
                        <td class="text-center"><?php echo (int)$val['cnt_bookings'];?></td>
                        <td class="text-center"><?php echo round((float)($val['cnt_bookings']/$tpl['total_bookings'])*100, 2);?>%</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } elseif ($report_selector == 'vehicles') { 
    $total_distance = $total_fuel_cost = 0;
    foreach ($tpl['top_vehicle_arr'] as $val) {
        $total_distance += (int)$val['total_driven_km'];
        $total_fuel_cost += (float)$val['total_fuel_cost'];
    }
    ?>
	<div class="row stats-overview" style="margin-bottom: 20px;">
        <div class="col-md-3">
            <div class="stat-box shadow-sm">
                <div class="stat-icon bg-info-light">
                    <i class="fa fa-road text-info"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Distance</div>
                    <div class="stat-value"><span id="summary-total-km"><?php echo $total_distance;?></span> <small>KM</small></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-box shadow-sm">
                <div class="stat-icon bg-warning-light">
                    <i class="fa fa-tint text-warning"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Fuel Cost</div>
                    <div class="stat-value"><span id="summary-total-cost" class="text-warning"><?php echo round($total_fuel_cost, 2);?></span> <small><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></small></div>
                </div>
            </div>
        </div>
    </div>

	<div class="table-responsive">
        <table class="table table-bordered table-vehicles">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Vehicle Info (Plate | Model)</th>
                    <th class="text-center">Total Distance</th>
                    <th class="text-right">Fuel Cost</th>
                    <th class="text-center">Consumption Rate</th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($tpl['top_vehicle_arr'] as $rank => $val) { ?>
                    <tr>
                        <td><?php echo ($rank+1);?></td>
                        <td><strong><?php echo pjSanitize::html($val['vehicle_name']);?></strong></td>
                        <td class="text-center"><?php echo (int)$val['total_driven_km'];?> KM</td>
                        <td class="text-right"><?php echo pjCurrency::formatPrice($val['total_fuel_cost']);?></td>
                        <td class="text-center"><?php echo (float)$val['fuel_consumption'];?> L / 100km</td>
                    </tr>
            	<?php } ?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
	<div class="table-responsive">
        <table class="table table-bordered table-drivers">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Driver Name</th>
                    <th class="text-center">Total Bookings</th>
                    <th class="text-right">Total Revenue</th>
                    <th class="text-right">Avg. / Booking</th>
                </tr>
            </thead>
            <tbody>
            	<?php foreach ($tpl['top_driver_arr'] as $rank => $val) { ?>
                    <tr>
                        <td><?php echo ($rank+1);?></td>
                        <td><strong><?php echo pjSanitize::html($val['driver_name']);?></strong></td>
                        <td class="text-center"><?php echo (int)$val['total_bookings'];?></td>
                        <td class="text-right"><?php echo pjCurrency::formatPrice((float)$val['total_revenue']);?></td>
                        <td class="text-right"><?php echo pjCurrency::formatPrice((float)$val['total_revenue']/(int)$val['total_bookings']);?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>