<style>
    /* Container chung cho Grid */
.grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

/* Base Card Style */
.metric-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px; /* Tăng padding để box thoáng hơn */
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.2s ease;
    border-left: 6px solid #ccc; 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 140px; /* Tăng chiều cao tối thiểu */
}

.metric-card:hover {
    transform: translateY(-5px);
}

/* Định dạng tiêu đề nhỏ phía trên */
.m-title {
    font-size: 1.2rem; /* Tăng từ 0.75rem */
    font-weight: 800;  /* Tăng độ đậm */
    color: #4a5568;    /* Màu đậm hơn (Slate Gray) */
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 12px;
}

/* Định dạng giá trị chính (Số lớn) */
.m-value {
    font-size: 2rem;
    font-weight: 900;
    color: #1a202c;
}

.m-value small {
    font-size: 1.1rem;
    color: #718096;
    margin-left: 6px;
    font-weight: 600;
}

/* Định dạng dòng chú thích phía dưới */
.m-comp {
    font-size: 1.5rem;
    color: #4a5568;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 2px solid #edf2f7; /* Đường kẻ rõ hơn */
    font-weight: 500;
}

.m-comp span {
    font-weight: 700;
    color: #2d3748; 
}
#op-top-dest {
    font-size: 1.2rem; /* Size vừa phải cho địa chỉ dài */
    font-weight: 700;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Giới hạn tối đa 2 dòng */
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom Colors cho từng loại Box */
.border-driver { border-left-color: #2ecc71; } /* Xanh lá - Tài xế */
.border-km { border-left-color: #3498db; }     /* Xanh dương - KM */
.border-vehicle { border-left-color: #9b59b6; } /* Tím - Xe */
.border-dest { border-left-color: #e74c3c; }    /* Đỏ - Điểm đến */

/* Utility class cho Grid */
.span-3 { grid-column: span 3; }

.m-list-container {
    margin-top: 10px;
    min-height: 80px; /* Đảm bảo các card cao bằng nhau */
}
.m-item-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    margin-bottom: 4px;
    border-bottom: 1px dashed #eee;
    padding-bottom: 2px;
}
.m-item-row:last-child { border-bottom: none; }
.m-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
    font-weight: 500;
}
.m-sub-value {
    color: #555;
    font-weight: bold;
}
.m-item-row-dest {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    line-height: 1.4;
    margin-bottom: 6px;
    padding-left: 8px;
    border-left: 3px solid #e74c3c;
    background: #fdf2f1; /* Thêm nền nhẹ để phân biệt các dòng địa chỉ dài */
    padding-right: 5px;
}

.dest-name {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 10px;
}

.dest-count {
    background: #e74c3c;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 20px;
    text-align: center;
}

.m-value-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-end; /* Căn lề dưới cho các con số trông đều nhau */
    padding: 15px 0;
    margin-bottom: 10px;
}

.m-value-item {
    flex: 1;
}

.m-separator {
    width: 1px;
    height: 30px;
    background: #eee;
    margin: 0 15px;
    align-self: center;
}

/* Đảm bảo font size không bị quá to khi có 2 cột */
#op-total-km, #op-total-fuel-cost {
    font-weight: bold;
    letter-spacing: -1px;
}

/* Responsive cho Mobile */
@media (max-width: 768px) {
    .span-3 { grid-column: span 12; }
}
</style>
<?php
$today = pjDateTime::formatDate(date('Y-m-d'), 'Y-m-d', $tpl['option_arr']['o_date_format']);
?>
<div class="wrapper wrapper-content animated fadeInRight">
	
	<div class="row">
    	<div class="col-xs-12">
    		<div class="grid grid-metric" style="margin-top: 20px;">
    			<div class="card span-3 metric-card" style="border-left-color: #3498db;">
                    <div class="m-title">Total Distance (Own)</div>
                    <div class="m-value-container">
                        <div class="m-value-item">
                            <span id="op-total-km" style="font-size: 24px;">0</span> 
                            <small style="font-size: 14px; color: #999;">KM</small>
                        </div>
                        
                        <div class="m-separator"></div>
                
                        <div class="m-value-item" style="text-align: right;">
                            <span id="op-total-fuel-cost" style="font-size: 24px; color: #e67e22;">0</span> 
                            <small style="font-size: 14px; color: #999;">€</small>
                            <div style="font-size: 10px; color: #bcc3c7; margin-top: -5px;">Fuel Cost</div>
                        </div>
                    </div>
                    <div class="m-comp">Bookings: <span id="op-total-km-bookings"><?php echo (int)$tpl['data']['total_bookings'];?></span></div>
                </div>
            
                <div class="card span-3 metric-card" style="border-left-color: #2ecc71;">
                    <div class="m-title">Top 3 Drivers Today</div>
                    <div id="op-driver-container" class="m-list-container">
                        <div class="m-item-row">
                            <span class="m-label">-</span>
                            <span class="m-sub-value"><?php echo pjCurrency::formatPrice(0);?></span>
                        </div>
                        <div class="m-item-row">
                            <span class="m-label">-</span>
                            <span class="m-sub-value"><?php echo pjCurrency::formatPrice(0);?></span>
                        </div>
                        <div class="m-item-row">
                            <span class="m-label">-</span>
                            <span class="m-sub-value"><?php echo pjCurrency::formatPrice(0);?></span>
                        </div>
                    </div>
                    <div class="m-comp">Revenue Metrics</div>
                </div>
            
                <div class="card span-3 metric-card" style="border-left-color: #9b59b6;">
                    <div class="m-title">Top 3 Used Vehicles</div>
                    <div id="op-veh-container" class="m-list-container">
                        <div class="m-item-row"><span class="m-label">-</span><span class="m-sub-value">0 KM</span></div>
                        <div class="m-item-row"><span class="m-label">-</span><span class="m-sub-value">0 KM</span></div>
                        <div class="m-item-row"><span class="m-label">-</span><span class="m-sub-value">0 KM</span></div>
                    </div>
                    <div class="m-comp">Usage Statistics</div>
                </div>
            
                <div class="card span-3 metric-card" style="border-left-color: #e74c3c;">
                    <div class="m-title">Top 3 Destinations</div>
                    <div id="op-dest-container" class="m-list-container">
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
                    </div>
                    <div class="m-comp">Popular Routes</div>
                </div>

            </div>
    	</div>
    </div>
    <div class="hr-line-dashed"></div>
    
    <div class="row">
         <div class="col-lg-3 col-sm-6">
         	<div class="ibox float-e-margins">
         		<div class="ibox-content">
         			<p class="clearfix"><span class="pull-left"><?php __('dash_total_bookings_today'); ?></span><span class="pull-right"><?php echo (int) $tpl['cnt_bookings_today'];?></span></p>
         			<p class="clearfix"><span class="pull-left"><?php __('dash_total_bookings_tomorrow'); ?></span><span class="pull-right"><?php echo (int) $tpl['cnt_bookings_tomorrow'];?></span></p>
         			<hr />         			
         			<p class="h4"><?php __('dash_total_amount_today'); ?></p>         			
         			<p class="clearfix"><span class="pull-left"><?php __('dash_total_bookings_today'); ?></span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_amount_today']);?></span></p>
         			<p class="clearfix"><span class="pull-left padding-sm"><?php __('dash_total_bookings_own_vehicles'); ?></span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_own_amount_today']);?></span></p>
         			<p class="clearfix"><span class="pull-left padding-sm"><?php __('dash_total_bookings_partner_vehicles'); ?></span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_partner_amount_today']);?></span></p>
         			<hr />   
         			<p class="clearfix"><span class="pull-left"><?php __('dash_paid'); ?></span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_paid_today']);?></span></p>
         			<p class="clearfix"><span class="pull-left padding-sm"><?php __('dash_credit_card'); ?></span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_cc_today']);?></span></p>
         			<p class="clearfix"><span class="pull-left padding-sm">Paysafe QR Code</span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_paysafe_today']);?></span></p>
         			<p class="clearfix"><span class="pull-left padding-sm"><?php __('dash_cash'); ?></span><span class="pull-right"><?php echo pjCurrency::formatPrice((float)$tpl['total_cash_today']);?></span></p>
         			<?php if(pjAuth::factory('pjAdminSchedule', 'pjActionIndex')->hasAccess()) { ?>
             			<p>
             				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminSchedule&amp;action=pjActionIndex" class="btn btn-primary btn-block" target="_blank"><?php __('btnSchedule');?></a>
             			</p>
         			<?php } ?>
         		</div><!-- /.ibox-content -->
         	</div><!-- /.ibox float-e-margins -->
         </div><!-- /.col-lg-3 col-sm-6 -->
         <div class="col-lg-5 col-sm-6">
         	<div class="tabs-container tabs-reservations m-b-lg">
             	<ul class="nav nav-tabs" role="tablist">
             		<li role="presentation" class="active"><a class="nav-tab-sms" href="#tab-sms" aria-controls="sms" role="tab" data-toggle="tab"><?php __('dash_sms');?></a></li>
    				<li role="presentation"><a class="nav-tab-popup" href="#tab-popup" aria-controls="popup" role="tab" data-toggle="tab"><?php __('dash_popup');?></a></li>
    				<?php if ($tpl['option_arr']['o_enable_whatsapp_fearure'] == 'Yes') { ?>
    					<li role="presentation"><a class="nav-tab-popup" href="#tab-whatsapp" aria-controls="whatsapp" role="tab" data-toggle="tab"><?php __('dash_whatsapp');?></a></li>
    				<?php } ?>
    			</ul>
    			<div class="tab-content">
    				<div role="tabpanel" class="tab-pane active" id="tab-sms">
    					<div class="panel-body">
    						<h3 class="text-center"><?php __('dash_send_sms');?></h3>
    						<form action="" method="post" id="frmSendSms">
    							<input type="hidden" name="send_sms" value="1" />
    							<div class="form-group">
                    				<label class="control-label"><?php __('dash_choose_driver');?>:</label>			
                    				<select name="driver_id" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                    					<option value="">-- <?php __('lblChoose');?> --</option>
                    					<option value="own_drivers_today"><?php __('dash_sms_own_drivers_today');?></option>
                    					<option value="own_drivers_tomorrow"><?php __('dash_sms_own_drivers_tomorrow');?></option>
                    					<?php foreach ($tpl['driver_arr'] as $val) { ?>
                    						<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['name']);?></option>
                    					<?php } ?>
                    				</select>
                    			</div>
                    			<div class="form-group">
                    				<label class="control-label"><?php __('dash_message');?>:</label>			
                    				<textarea rows="5" name="message" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"></textarea>
                    			</div>
                    			
                    			<div class="pjSbSendSmsMsg" style="display: none;">
                    				<div class="alert"></div>
                    			</div>
                    			
                    			<div class="m-t-lg">
                    				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
                    					<span class="ladda-label"><?php __('btnSend', false, true); ?></span>
                    					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                    				</button>
                    			</div><!-- /.m-b-lg -->
    						</form>
    					</div>
    				</div>
    				<div role="tabpanel" class="tab-pane" id="tab-popup">
    					<div class="panel-body">
    						<h3 class="text-center"><?php __('dash_send_popup');?></h3>
    						<form action="" method="post" id="frmSendPopup">
    							<input type="hidden" name="send_popup" value="1" />
    							<div class="form-group">
                    				<label class="control-label"><?php __('dash_choose_driver');?>:</label>			
                    				<select name="driver_id" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                    					<option value="">-- <?php __('lblChoose');?> --</option>
                    					<option value="own_drivers_today"><?php __('dash_sms_own_drivers_today');?></option>
                    					<option value="own_drivers_tomorrow"><?php __('dash_sms_own_drivers_tomorrow');?></option>
                    					<?php foreach ($tpl['driver_arr'] as $val) { ?>
                    						<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['name']);?></option>
                    					<?php } ?>
                    				</select>
                    			</div>
                    			<div class="form-group">
                    				<label class="control-label"><?php __('dash_message');?>:</label>			
                    				<textarea rows="5" name="message" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"></textarea>
                    			</div>
                    			<div class="pjSbSendSmsPopUp" style="display: none;">
                    				<div class="alert"></div>
                    			</div>
                    			<div class="m-t-lg">
                    				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
                    					<span class="ladda-label"><?php __('btnSend', false, true); ?></span>
                    					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                    				</button>
                    			</div><!-- /.m-b-lg -->
    						</form>
    					</div>
    				</div>
    				<?php if ($tpl['option_arr']['o_enable_whatsapp_fearure'] == 'Yes') { ?>
        				<div role="tabpanel" class="tab-pane" id="tab-whatsapp">
        					<div class="panel-body">
        						<h3 class="text-center"><?php __('dash_send_whatsapp_message');?></h3>
        						<form action="" method="post" id="frmSendWhatsapp">
        							<input type="hidden" name="send_whatsapp" value="1" />
        							<div class="form-group" style="display: none;">
                        				<label class="control-label"><?php __('dash_whatsapp_provider');?>:</label>			
                        				<select id="provider_id" name="provider_id" class="form-control">
                        					<?php foreach ($tpl['provider_arr'] as $k => $val) { ?>
                                            	<option value="<?php echo $val['id'];?>" <?php echo $k == 0 ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($val['whatsapp_name']);?></option>
                                            <?php } ?>
                                        </select>
                        			</div>
                        			
                        			<div class="form-group">
                        				<label class="control-label"><?php __('dash_choose_driver');?>:</label>			
                        				<select name="driver_id" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                        					<option value="">-- <?php __('lblChoose');?> --</option>
                        					<option value="own_drivers_today"><?php __('dash_sms_own_drivers_today');?></option>
                        					<option value="own_drivers_tomorrow"><?php __('dash_sms_own_drivers_tomorrow');?></option>
                        					<?php foreach ($tpl['driver_arr'] as $val) { ?>
                        						<option value="<?php echo $val['id'];?>"><?php echo pjSanitize::html($val['name']);?></option>
                        					<?php } ?>
                        				</select>
                        			</div>
                        			
                        			<div class="form-group">
                        				<label class="control-label"><?php __('dash_whatsapp_templates');?>:</label>			
                        				<select id="whatsapp_template" name="whatsapp_template" class="form-control msg-group">
                                            <option value="">-- <?php __('lblSelectTemplate');?> --</option>
                                        </select>
                        			</div>
                        			
                        			<div class="form-group">
                        				<label class="control-label"><?php __('dash_message');?>:</label>			
                        				<textarea rows="5" name="whatsapp_message" id="whatsapp_message" class="form-control msg-group" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>"></textarea>
                        			</div>
                        			
                        			<div class="pjSbSendWhatsappMsg" style="display: none;">
                        				<div class="alert"></div>
                        			</div>
                        			
                        			<div class="m-t-lg">
                        				<button type="submit" class="ladda-button btn btn-primary btn-lg btn-phpjabbers-loader pull-left" data-style="zoom-in">
                        					<span class="ladda-label"><?php __('btnSend', false, true); ?></span>
                        					<?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                        				</button>
                        			</div><!-- /.m-b-lg -->
        						</form>
        					</div>
        				</div>
    				<?php } ?>
    			</div><!-- /.tab-content -->
    		</div><!-- /.tabs-container tabs-reservations m-b-lg -->
         </div><!-- /.col-lg-3 col-sm-6 -->
         <div class="col-lg-4 col-sm-12">
         	<div class="tabs-container tabs-reservations m-b-lg">
             	<ul class="nav nav-tabs" role="tablist">
             		<li role="presentation" class="active"><a class="nav-tab-charts-1" href="#tab-charts-1" aria-controls="charts-1" role="tab" data-toggle="tab"><?php __('dash_tab_upcoming');?></a></li>
    			</ul>
    			<div class="tab-content">
    				<div role="tabpanel" class="tab-pane active" id="tab-charts-1">
    					<div class="panel-body">
    						<div class="row">
    							<div class="col-xs-12 text-right">
    								<a href="javascript:void(0);" class="navUpcomingBookings navPrevUpcomingBookings" data-type="prev" data-step="<?php echo (7 * 24 *3600);?>" data-ts="<?php echo strtotime('-7 days');?>"><i class="fa fa-caret-left"></i></a>
    								<a href="javascript:void(0);" class="navUpcomingBookings navNextUpcomingBookings" data-type="next" data-step="<?php echo (7 * 24 *3600);?>" data-ts="<?php echo strtotime('+7 days');?>"><i class="fa fa-caret-right"></i></a>
    							</div>
    						</div>
    						<input type="hidden" name="today_ts" id="today_ts" value="<?php echo time();?>" />
    						<div id="chart-1" style="height: 290px"></div>
    					</div>
    				</div>
    			</div><!-- /.tab-content -->
    		</div><!-- /.tabs-container tabs-reservations m-b-lg -->
         </div><!-- /.col-lg-6 col-sm-12 -->
    </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content -->

<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.select_template = "-- <?php __('lblSelectTemplate');?> --";
myLabel.dash_select_template_or_enter_message = "<?php __('dash_select_template_or_enter_message');?>";
</script>