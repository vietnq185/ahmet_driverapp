<?php foreach ($tpl['driver_arr'] as $driver) {
    $driver_phone = ltrim($driver['phone'], '0+');
    $driver_phone_formatted = '+' . substr($driver_phone, 0, 3) . ' ' . substr($driver_phone, 3, 3) . ' ' . substr($driver_phone, 6);
    ?>
    <div class="driver-item" 
    	data-id="<?php echo $driver['id'];?>"
         data-phone="<?php echo pjSanitize::html($driver_phone);?>" 
         data-type="<?php echo pjSanitize::html($driver['type_of_driver']);?>"
         data-name="<?php echo pjSanitize::html($driver['name']);?>">
        <div class="d-flex justify-content-between">
            <strong><?php echo pjSanitize::html($driver['name']);?></strong>
            <span class="unread-badge <?php echo $driver['unread_count'] == 0 ? 'd-none' : '' ?>"><?php echo $driver['unread_count'] ?></span>
        </div>
        <small class="text-muted"><?php echo pjSanitize::html($driver_phone_formatted);?></small>
    </div>
<?php } ?>