<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-12">
				<h2><?php __('infoWhatsappChatTitle');?></h2>
			</div>
		</div>
		<p class="m-b-none">
			<i class="fa fa-info-circle"></i><?php __('infoWhatsappChatBody');?>
		</p>
	</div>
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		
        <style>
            :root { --alpstria-green: #1a4d2e; }
            .whatsapp-wrapper { display: flex; height: 80vh; background: #fff; border: 1px solid #ddd; margin-top: 10px; }
            .sidebar { width: 30%; border-right: 1px solid #ddd; display: flex; flex-direction: column; }
            .driver-list { overflow-y: auto; flex: 1; border-top: 1px solid #e5e6e7;}
            .driver-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
            .driver-item.active { background: #e9edef; border-left: 5px solid var(--alpstria-green); }
            .chat-main { width: 70%; display: flex; flex-direction: column; background: #e5ddd5; }
            #chat-window { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; }
            .message { margin-bottom: 10px; padding: 10px; border-radius: 8px; max-width: 70%; }
            .sent { align-self: flex-end; background: #dcf8c6; }
            .received { align-self: flex-start; background: #fff; }
            .chat-footer { padding: 15px; background: #f0f0f0; }
            
            #chat-window {
                height: 500px;
                overflow-y: auto;
                padding: 15px;
                background-color: #e5ddd5; /* Màu nền WhatsApp */
            	background-image: url('<?php echo PJ_INSTALL_URL;?>app/web/img/backend/bg_whatsapp.png'); /* Pattern doodle */
                background-repeat: repeat;
                display: flex;
                flex-direction: column;
            }
            .msg {
                margin-bottom: 10px;
                padding: 10px 15px;
                border-radius: 10px;
                max-width: 80%;
                position: relative;
                line-height: 1.5;
            }
            .sent { align-self: flex-end; background-color: #dcf8c6; border-bottom-right-radius: 2px; }
            .received { align-self: flex-start; background-color: #ffffff; border-bottom-left-radius: 2px; }
            /* Spinner chỉnh lại vị trí */
            #chat-loading {
                border-radius: 5px;
            }
            .whatsapp-wrapper .btn-group .btn.active{
                box-shadow: inset 0 3px 5px rgba(0,0,0,.1);
                background: #fc801c;
            }
           .whatsapp-wrapper .btn-group .btn{
                background: rgb(245, 246, 247);
            }
             .whatsapp-wrapper .btn-group{
                margin: 5px 10px;
            }
            .d-none {
                display: none;
            }
        	.unread-badge { background: #25d366; color: white; border-radius: 50%; padding: 2px 8px; font-size: 12px; font-weight: bold; float: right;}
        </style>
        
        <div class="whatsapp-wrapper">
            <div class="sidebar">
                <div class="sidebar-header" style="padding:15px; background:#ededed;"><b>DRIVERS</b></div>
                
                <div class="p-3">
                    <input type="text" id="search-driver" class="form-control mb-2" placeholder="Search by name or phone...">
                    
                    <div class="btn-group btn-group-toggle w-100 mb-3" data-toggle="buttons">
                        <label class="btn btn-outline-secondary btn-sm active">
                            <input type="radio" name="filter-type" value="all" checked> All
                        </label>
                        <label class="btn btn-outline-secondary btn-sm">
                            <input type="radio" name="filter-type" value="own"> Own driver
                        </label>
                        <label class="btn btn-outline-secondary btn-sm">
                            <input type="radio" name="filter-type" value="partner"> Partner driver
                        </label>
                    </div>
                </div>
    
    
            
                <div class="driver-list">
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
                </div>
            </div>
    
            <div class="chat-main">
                <div class="chat-header" style="padding:10px; background:#ededed;">
                	<select id="provider-select" class="form-control" style="margin-bottom:10px;">
                		<?php foreach ($tpl['provider_arr'] as $provider) { ?>
                        	<option value="<?php echo $provider['id'];?>"><?php echo pjSanitize::html($provider['whatsapp_name']);?></option>
                        <?php } ?>
                    </select>
                    <?php __('lblChat')?>: <span id="current-name"><?php __('lblSelectADriver');?></span>
                </div>
                <div id="chat-window"></div>
                <div class="chat-footer">
                    <select id="tpl-select" class="form-control" style="margin-bottom:10px;">
                        <option value="">-- <?php __('lblSelectTemplate');?> --</option>
                    </select>
                    <div class="input-group">
                        <textarea id="msg-input" class="form-control" rows="3" placeholder="Type message..."></textarea>
                        <span class="input-group-btn">
                            <button id="btn-send" class="btn btn-success" style="height:72px; background:var(--alpstria-green);">SEND</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    
	</div>
</div>
<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.pusher_key = "<?php echo $tpl['option_arr']['o_pusher_key'];?>";
	myLabel.pusher_cluster = "<?php echo $tpl['option_arr']['o_pusher_cluster'];?>";
	myLabel.select_template = "-- <?php __('lblSelectTemplate');?> --";
</script>
	