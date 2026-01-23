
<div class="card flight-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4"><?php __('lblFlightAirline');?></th>
                    <th><?php __('lblFlightTrackDeparture')?></th>
                    <th><?php __('lblFlightTrackArrival');?></th>
                    <th><?php __('lblFlightStatus');?></th>
                    <th class="text-center"><?php __('lblFlightRemarks');?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tpl['arr'] as $flightNo => $flights): 
                    $f = $flights[0];
                    $status = $f['status'];
                    
                    // Time Processing
                    $schedArr = strtotime($f['arrival']['scheduledTime']['local']);
                    $predArr = isset($f['arrival']['predictedTime']['local']) ? strtotime($f['arrival']['predictedTime']['local']) : $schedArr;
                    $delayMin = ($predArr - $schedArr) / 60;
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="text-flight"><?= $f['number'] ?></div>
                        <div class="text-airline"><?= strtoupper($f['airline']['name']) ?></div>
                    </td>
                    <td>
                        <div class="time-main"><?= date('H:i', strtotime($f['departure']['scheduledTime']['local'])) ?></div>
                        <small class="text-muted">Scheduled</small>
                    </td>
                    <td>
                        <?php if ($delayMin > 0): ?>
                            <span class="time-strikethrough"><?= date('H:i', $schedArr) ?></span><br>
                            <span class="time-main text-danger"><?= date('H:i', $predArr) ?></span>
                        <?php else: ?>
                            <div class="time-main text-success"><?= date('H:i', $schedArr) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                            $badgeClass = 'bg-light text-dark';
                            if ($status == 'Expected') $badgeClass = 'bg-primary text-white';
                            if ($status == 'Arrived' || $status == 'Landed') $badgeClass = 'bg-success text-white';
                            if ($status == 'Cancelled') $badgeClass = 'bg-danger text-white';
                        ?>
                        <span class="badge badge-status <?= $badgeClass ?>"><?= strtoupper($status) ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($delayMin > 0): ?>
                            <span class="delay-tag"><i class="fa fa-clock-o"></i> <?php __('lblFlightDelay');?> <?= $delayMin ?>M</span>
                        <?php else: ?>
                            <span class="text-success fw-bold"><span class="fa-double-check">
    <i class="fa fa-check fa-first"></i>
    <i class="fa fa-check fa-second"></i>
</span> <?php __('lblFlightOnTime');?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

  