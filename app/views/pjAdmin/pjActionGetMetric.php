	<div class="card span-3 metric-card" style="border-left-color: #3498db;">
        <div class="m-title">Total Distance (Own)</div>
        <div class="m-value-container">
            <div class="m-value-item">
                <span id="op-total-km" style="font-size: 24px;"><?php echo (int)$tpl['data']['total_distance'];?></span> 
                <small style="font-size: 14px; color: #999;">KM</small>
            </div>
            
            <div class="m-separator"></div>
    
            <div class="m-value-item" style="text-align: right;">
                <span id="op-total-fuel-cost" style="font-size: 24px; color: #e67e22;"><?php echo round($tpl['data']['total_fuel_cost'], 2);?></span> 
                <small style="font-size: 14px; color: #999;"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false);?></small>
                <div style="font-size: 10px; color: #bcc3c7; margin-top: -5px;">Fuel Cost</div>
            </div>
        </div>
        <div class="m-comp">Bookings: <span id="op-total-km-bookings"><?php echo (int)$tpl['data']['total_bookings'];?></span></div>
    </div>

    <div class="card span-3 metric-card" style="border-left-color: #2ecc71;">
        <div class="m-title">Top 3 Drivers Today</div>
        <div id="op-driver-container" class="m-list-container">
        	<?php if ($tpl['data']['top_driver_arr']) { ?>
        		<?php foreach ($tpl['data']['top_driver_arr'] as $val) { ?>
        			<div class="m-item-row">
                        <span class="m-label"><?php echo pjSanitize::html($val['driver_name']);?></span>
                        <span class="m-sub-value"><?php echo pjCurrency::formatPrice($val['total_revenue']);?></span>
                    </div>
        		<?php } ?>
        	<?php } else { ?>
                <div class="m-item-row">
                    <span class="m-label">-</span>
                    <span class="m-sub-value">0</span>
                </div>
                <div class="m-item-row">
                    <span class="m-label">-</span>
                    <span class="m-sub-value">0</span>
                </div>
                <div class="m-item-row">
                    <span class="m-label">-</span>
                    <span class="m-sub-value">0</span>
                </div>
        	<?php } ?>
        </div>
        <div class="m-comp">Revenue Metrics</div>
        <?php if ($tpl['data']['top_driver_arr']) { ?>
        	<div align="center" style="margin-top: 10px;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex&amp;tab=performance&amp;type=drivers" class="btn btn-primary btn-link btn-sm btn-outline">See more</a></div>
        <?php } ?>
    </div>

    <div class="card span-3 metric-card" style="border-left-color: #9b59b6;">
        <div class="m-title">Top 3 Used Vehicles</div>
        <div id="op-veh-container" class="m-list-container">
        	<?php if ($tpl['data']['max_vehicle']) { ?>
        		<?php foreach ($tpl['data']['max_vehicle'] as $val) { ?>
        			<div class="m-item-row"><span class="m-label"><?php echo pjSanitize::html($val['vehicle_name']);?></span><span class="m-sub-value"><?php echo (int)$val['total_driven_km'];?> KM</span></div>
        		<?php } ?>
        	<?php } else { ?>
                <div class="m-item-row"><span class="m-label">-</span><span class="m-sub-value">0 KM</span></div>
                <div class="m-item-row"><span class="m-label">-</span><span class="m-sub-value">0 KM</span></div>
                <div class="m-item-row"><span class="m-label">-</span><span class="m-sub-value">0 KM</span></div>
        	<?php } ?>
        </div>
        <div class="m-comp">Usage Statistics</div>
        <?php if ($tpl['data']['max_vehicle']) { ?>
        	<div align="center" style="margin-top: 10px;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex&amp;tab=performance&amp;type=vehicles" class="btn btn-primary btn-link btn-sm btn-outline">See more</a></div>
        <?php } ?>
    </div>

    <div class="card span-3 metric-card" style="border-left-color: #e74c3c;">
        <div class="m-title">Top 3 Destinations</div>
        <div id="op-dest-container" class="m-list-container">
        	<?php if ($tpl['data']['top_destination_arr']) { ?>
        		<?php foreach ($tpl['data']['top_destination_arr'] as $val) { ?>
        			<div class="m-item-row-dest">
                        <span class="dest-name"><?php echo pjSanitize::html($val['destination']);?></span>
                        <span class="dest-count"><?php echo (int)$val['cnt_bookings'];?></span>
                    </div>
        		<?php } ?>
        	<?php } else { ?>
                <div class="m-item-row-dest">
                    <span class="dest-name">-</span>
                    <span class="dest-count">0</span>
                </div>
                <div class="m-item-row-dest">
                    <span class="dest-name">-</span>
                    <span class="dest-count">0</span>
                </div>
                <div class="m-item-row-dest">
                    <span class="dest-name">-</span>
                    <span class="dest-count">0</span>
                </div>
        	<?php } ?>
        </div>
        <div class="m-comp">Popular Routes</div>
        <?php if ($tpl['data']['top_destination_arr']) { ?>
        	<div align="center" style="margin-top: 10px;"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminReports&amp;action=pjActionIndex&amp;tab=performance&amp;type=destinations" class="btn btn-primary btn-link btn-sm btn-outline">See more</a></div>
        <?php } ?>
    </div>