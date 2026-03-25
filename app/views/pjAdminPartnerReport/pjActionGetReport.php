<div class="row text-center">
    <div class="col-md-4">
        <div class="report-stat-box bg-info">
            <h4><?php __('lblPartnerReportTotalCommissions');?></h4>
            <div class="stat-number"><?php echo pjCurrency::formatPrice($tpl['total_comm']);?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="report-stat-box bg-success">
            <h4><?php __('lblPartnerReportTotalBookingsByPartners');?></h4>
            <div class="stat-number"><?php echo $tpl['total_bookings'];?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="report-stat-box bg-warning">
            <h4><?php __('lblPartnerReportTotalRevenue');?></h4>
            <div class="stat-number"><?php echo pjCurrency::formatPrice($tpl['total_amount']);?></div>
        </div>
    </div>
</div>

<div class="row margin-top-30">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php __('lblPartnerReportBestPartner');?></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php __('lblPartnerReportPartnerName');?></th>
                            <th><?php __('lblPartnerReportBookings');?></th>
                            <th class="text-right"><?php __('lblPartnerReportRevenue');?></th>
                            <th class="text-right"><?php __('lblPartnerReportCommissions');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php 
                    	$idx = 1;
                    	foreach ($tpl['partner_arr'] as $partner_id => $partner) { ?>
                        <tr>
                            <td><?php echo $idx;?></td>
                            <td><strong><?php echo pjSanitize::html($partner['name']);?></strong></td>
                            <td><?php echo $partner['total_bookings'];?></td>
                            <td class="text-right"><?php echo pjCurrency::formatPrice($partner['total_amount']);?></td>
                            <td class="text-right"><?php echo pjCurrency::formatPrice($partner['total_comm']);?></td>
                        </tr>
                        <?php $idx++;} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>