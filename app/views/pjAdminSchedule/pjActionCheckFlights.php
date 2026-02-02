<?php foreach ($tpl['arr'] as $flightNo => $flights) {
    if (!isset($flights[0]) || !isset($flights[0]['status'])) {
        continue;
    }
    $f = $flights[0];
    $status = $f['status'];
    switch ($status) {
        case 'Boarding':
        case 'EnRoute':
        case 'Approaching':
        case 'Arrived':
            $badgeClass = 'st-green'; // Hoạt động tích cực/Thành công
            break;
        case 'Expected':
        case 'CheckIn':
            $badgeClass = 'st-blue';  // Đang chuẩn bị
            break;
        case 'GateClosed':
        case 'Departed':
            $badgeClass = 'st-gray';  // Đã hoàn tất thủ tục
            break;
        case 'Delayed':
        case 'Canceled':
        case 'Diverted':
            $badgeClass = 'st-red';   // Cảnh báo/Sự cố
            break;
        case 'Unknown':
        case 'CanceledUncertain':
        default:
            $badgeClass = 'st-orange'; // Dữ liệu không rõ ràng
            break;
    }
    // Time Processing
    $schedArr = strtotime($f['arrival']['scheduledTime']['local']);
    $predArr = isset($f['arrival']['predictedTime']['local']) ? strtotime($f['arrival']['predictedTime']['local']) : $schedArr;
    $reviArr = isset($f['arrival']['revisedTime']['local']) ? strtotime($f['arrival']['revisedTime']['local']) : $schedArr;
    $delayMin = ($reviArr - $schedArr) / 60;
    $from = $f['departure']['airport']['name'];
    $to = $f['arrival']['airport']['name'];
    ?>
    <tr class="trFlightInfo">
        <td class="ps-4" data-title="<?php __('lblFlightAirline');?>">
            <div class="text-flight">(<?php echo $tpl['cnt'];?>) <?php echo pjSanitize::html($f['number']);?>&nbsp;</div>
            <div class="text-airline"><?php echo pjSanitize::html(strtoupper($f['airline']['name']));?>&nbsp;</div>
            <div class="from-to-location"><?php echo pjSanitize::html($from);?> <i class="fa fa-long-arrow-right" aria-hidden="true"></i> <?php echo pjSanitize::html($to);?></div>
        </td>
        <td data-title="<?php __('lblFlightTrackDeparture');?>">
            <div class="time-main"><?php echo date('H:i', strtotime($f['departure']['scheduledTime']['local']));?>&nbsp;</div>
            <small class="text-muted">Scheduled</small>
        </td>
        <td data-title="<?php __('lblFlightTrackArrival');?>">
            <?php if ($delayMin > 0) { ?>
                <span class="time-strikethrough"><?php echo date('H:i', $schedArr);?>&nbsp;</span><br>
                <span class="time-main text-danger"><?php echo date('H:i', $reviArr);?>&nbsp;</span>
            <?php } else { ?>
                <div class="time-main text-success"><?php echo date('H:i', $schedArr);?>&nbsp;</div>
            <?php } ?>
        </td>
        <td data-title="<?php __('lblFlightStatus');?>">
            <span class="badge badge-status <?php echo $badgeClass;?>"><?php echo strtoupper($status);?>&nbsp;</span>
        </td>
        <td class="text-center" data-title="<?php __('lblFlightRemarks');?>">
            <?php if ($delayMin > 0) { ?>
                <span class="delay-tag"><i class="fa fa-clock-o"></i> <?php __('lblFlightDelay');?> <?= $delayMin ?>M</span>
            <?php } else { ?>
                <span class="text-success fw-bold"><span class="fa-double-check">
                    <i class="fa fa-check fa-first"></i>
                    <i class="fa fa-check fa-second"></i>
                </span> <?php __('lblFlightOnTime');?></span>
            <?php } ?>
        </td>
    </tr>
<?php } ?>