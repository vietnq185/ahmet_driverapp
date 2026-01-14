<div class="gallery-container">
    <?php if ($tpl['arr']) {
        $map_type_arr = array(
            'photo' => 'vehicle_photos',
            'document' => 'vehicle_documents',
            'accident' => 'vehicle_accidents',
            'service_invoice' => 'service_invoice'
        );
        ?>
        <?php foreach ($tpl['arr'] as $val) { 
            if ((int)$val['foreign_id'] > 0) {
                $foreign_id = (int)$val['foreign_id'];
            } else {
                $foreign_id = $val['tmp_hash'];
            }
            
            $extension = strtolower(pathinfo($val['filename'], PATHINFO_EXTENSION));
            $is_image = in_array($val['type'], array('photo')) || in_array($extension, array('jpg', 'jpeg', 'png', 'gif'));
            $download_url = $_SERVER['PHP_SELF'] . "?controller=pjAdminVehiclesMaintrance&amp;action=pjActionDownloadFile&amp;id=" . $val['id'];
            $source_url = PJ_INSTALL_URL . $val['source_path'];
            ?>
    
        	<?php if ($is_image) { ?>
                <div class="photo-column">
                    <div class="photo-item" title="<?php echo pjSanitize::html($val['filename']);?>">
                        <a href="<?php echo $source_url; ?>" class="gallery-item" title="<?php echo pjSanitize::html($val['filename']);?>">
                            <img src="<?php echo $source_url; ?>" />
                        </a>
                    </div>
                    <div class="delete-action">
                    	<a href="<?php echo $download_url; ?>" class="btn-action btn-download" title="Download"><i class="fa fa-download"></i></a>&nbsp;&nbsp;
                        <a href="javascript:void(0);" class="text-danger btn-delete-file" data-id="<?php echo $val['id'];?>" data-foreign_id="<?php echo $foreign_id;?>" data-type="<?php echo @$map_type_arr[$val['type']];?>" title="<?php __('btnDelete');?>"><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
        	<?php } else { ?>
                <div class="file-column">
            			<?php 
                        $target = "";
                        $class = "file-item";
                        $icon = "fa-file-o";
                        $link = $download_url;
    
                        if ($extension == 'pdf') {
                            $class .= " file-pdf";
                            $icon = "fa-file-pdf-o";
                            $target = 'target="_blank"'; // Mở tab mới cho PDF
                            $link = $source_url; // Link trực tiếp để trình duyệt đọc PDF
                        } elseif (in_array($extension, array('doc','docx'))) {
                            $class .= " file-doc";
                            $icon = "fa-file-word-o";
                        } elseif (in_array($extension, array('xlsx','xls'))) {
                            $class .= " file-excel";
                            $icon = "fa-file-excel-o";
                        } elseif ($extension == 'txt') {
                            $class .= " file-txt";
                            $icon = "fa-file-text-o";
                        }
                        ?>
                        
                        <div class="<?php echo $class; ?>" title="<?php echo pjSanitize::html($val['filename']);?>">
                            <a href="<?php echo $link; ?>" <?php echo $target; ?> title="<?php echo pjSanitize::html($val['filename']);?>">
                                <i class="fa <?php echo $icon; ?>"></i>
                            </a>
                        </div>
    
                        <div class="delete-action">
                        	<a href="<?php echo $download_url; ?>" class="btn-action btn-download" title="Download"><i class="fa fa-download"></i></a>&nbsp;&nbsp;
                            <a href="javascript:void(0);" class="text-danger btn-delete-file" data-foreign_id="<?php echo $foreign_id;?>" data-id="<?php echo $val['id'];?>" data-type="<?php echo @$map_type_arr[$val['type']];?>" title="<?php __('btnDelete');?>"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </div>
            	<?php } ?>
        <?php } ?>
    <?php } else { ?>
    	<div class="photo-item empty"><?php __('lblNoPhotos');?></div>
    <?php } ?>
</div>