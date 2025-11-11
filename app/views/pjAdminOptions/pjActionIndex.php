<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h2><?php echo __('infoGeneralOptionsTitle', true); ?></h2>
            </div>
        </div>

        <p class="m-b-none"><i class="fa fa-info-circle"></i><?php echo __('infoGeneralOptionsDesc', true); ?></p>
    </div>
</div>

<div class="row wrapper wrapper-content animated fadeInRight">
    <?php
	$error_code = $controller->_get->toString('err');
	if (!empty($error_code))
    {
    	switch (true)
    	{
    		case in_array($error_code, array('AO02','AO06')):
    			?>
    			<div class="alert alert-success">
    				<i class="fa fa-check m-r-xs"></i>
    				<strong><?php echo @$titles[$error_code]; ?></strong>
    				<?php echo @$bodies[$error_code]?>
    			</div>
    			<?php
    			break;
    		case in_array($error_code, array('')):
    			?>
    			<div class="alert alert-danger">
    				<i class="fa fa-exclamation-triangle m-r-xs"></i>
    				<strong><?php echo @$titles[$error_code]; ?></strong>
    				<?php echo @$bodies[$error_code]?>
    			</div>
    			<?php
    			break;
    	}
    }
    ?>
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <?php
    				if (isset($tpl['arr']) && is_array($tpl['arr']) && !empty($tpl['arr']))
    				{
                    ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form-horizontal" id="frmUpdateOptions" enctype="multipart/form-data">
                        <input type="hidden" name="options_update" value="1" />
                        <input type="hidden" name="tab" value="1" />
                        <input type="hidden" name="next_action" value="pjActionIndex" />
                        <?php
                        foreach ($tpl['arr'] as $option)
                        {
                        	if ((int) $option['is_visible'] === 0 || $option['key'] == 'o_layout' || $option['key'] == 'o_theme') continue;
                            if(in_array($option['key'], array('o_allow_bank', 'o_allow_cash', 'o_allow_creditcard', 'o_bank_account'))) continue; // These will be managed from Payments menu
	                        $rowClass = NULL;
							$rowStyle = NULL;
                            ?>
                            <div class="form-group <?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">

                                <label class="col-sm-3 control-label"><?php __('opt_' . $option['key']); ?></label>
                                <div class="col-lg-7 col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <?php
                                            switch ($option['type'])
                                            {
                                                case 'string':
                                                	if ($option['key'] == 'o_name_sign_logo')
                                                	{
                                                		?>
                                                		<?php
														if (!empty($tpl['option_arr']['o_name_sign_logo']) && is_file($tpl['option_arr']['o_name_sign_logo']))
														{
															?>
															<div class="pj-logo-container">
																<p class="m-b-md">
																	<img src="<?php echo PJ_INSTALL_URL . $tpl['option_arr']['o_name_sign_logo'];?>" alt="" class="pj-scale">
																</p>
																<p class="m-b-md">
																	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionDeleteLogo" class="btn btn-xs btn-danger btn-outline btn-file pj-delete-logo"><i class="fa fa-trash"></i> <?php __('btn_delete_logo');?></a>
																</p>
															</div>
															<?php
														}
														?>
                                                		<div class="form-group">
	                                                		<div class="fileinput fileinput-new" data-provides="fileinput">
																<span class="btn btn-primary btn-outline btn-file">
																	<span class="fileinput-new"><i class="fa fa-upload m-r-xs"></i> <?php __('btn_select_image'); ?></span>
																	<span class="fileinput-exists"><i class="fa fa-upload m-r-xs"></i> <?php __('btn_change_image'); ?></span>
																	<input type="file" name="name_sign_logo">
																</span>
																<span class="fileinput-filename"></span>
																<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
															</div>
														</div>
													<?php } else {
                                                		?>
                                                    	<input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control" value="<?php echo pjSanitize::html($option['value']); ?>"><?php
                                                	}
                                                    break;
                                                case 'text':
                                                    ?><textarea name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control"><?php echo pjSanitize::html($option['value']); ?></textarea><?php
                                                    break;
                                                case 'int':
                                                	if (in_array($option['key'], array('o_deposit_payment','o_tax_payment','o_insurance_payment'))) { 
                                                		$map = array(
                                                			'o_deposit_payment' => 'o_deposit_type',
	                                                		'o_tax_payment' => 'o_tax_type',
	                                                		'o_insurance_payment' => 'o_insurance_payment_type'
                                                		);
                                                    ?>
                                                    	<div class="row">
                                                    		<div class="col-md-6">
                                                    			<input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control field-float number" value="<?php echo (int) $option['value']; ?>" />
                                                    		</div>
                                                    		<div class="col-md-6">
																<select name="value-enum-<?php echo $map[$option['key']];?>" class="form-control">
																	<?php
																	$default = explode("::", $tpl['o_arr'][$map[$option['key']]]['value']);
																	$enum = explode("|", $default[0]);
		                                                    											
																	$enumLabels = array();
																	if (!empty($tpl['o_arr'][$map[$option['key']]]['label']) && strpos($tpl['o_arr'][$map[$option['key']]]['label'], "|") !== false)
																	{
																		$enumLabels = explode("|", $tpl['o_arr'][$map[$option['key']]]['label']);
																	}
																	$enum_arr = __('enum_'.$option['key'].'_arr', true);
																	foreach ($enum as $k => $el)
																	{
																		?><option value="<?php echo $default[0].'::'.$el; ?>"<?php echo $default[1] == $el ? ' selected="selected"' : NULL; ?>><?php echo isset($enum_arr[$el]) ? $enum_arr[$el] : (array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el)); ?></option><?php
																	}
																	?>
																</select>
															</div>
														</div>
                                                    <?php } else { ?>
	                                                    <input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control field-int" value="<?php echo pjSanitize::html($option['value']); ?>">
                                                    <?php
                                                    }
                                                    break;
                                                case 'float':
                                                    if(in_array($option['key'], array('o_security_payment'))) {
                                                        ?>
                                                        <div class="input-group">
                                                            <input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control decimal number text-right" value="<?php echo $option['value']; ?>" data-msg-number="<?php __('pj_please_enter_valid_number', false, true);?>">

                                                            <span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>
                                                        </div>
                                                        <?php
													} else {
                                                        ?><input type="text" name="value-<?php echo $option['type']; ?>-<?php echo $option['key']; ?>" class="form-control field-float number" value="<?php echo $option['value']; ?>"><?php
                                                    }
                                                    break;
                                                case 'enum':
													include dirname(__FILE__) . '/elements/enum.php';
                                                    break;
												case 'bool':
													include dirname(__FILE__) . '/elements/switch.php';
													break;
                                            }
                                            $desc = __("opt_{$option['key']}_text", true);
                                           if (!empty($desc)): ?>
												<small><?php echo $desc;?></small>
											<?php endif; ?>
                                        </div>

                                        <?php if (in_array($option['key'], array('o_booking_pending','o_new_day_per_day'))): ?>
                                            <p class="m-t-xs"><?php __('lblHours'); ?></p>
                                        <?php endif; ?>

                        				<?php if($option['key'] == 'o_min_hour'){
											if ($tpl['option_arr']['o_booking_periods'] == 'both'){
												?>
												<p class="boxMinimumBoth m-t-xs" style="display: inline-block;"><?php __('lblHours'); ?></p>
												<p class="boxMinimumDay m-t-xs" style="display: none;"><?php __('lblDays'); ?></p>
												<?php
											}else{
												?>
												<p class="boxMinimumBoth m-t-xs" style="display: none;"><?php __('lblHours'); ?></p>
												<p class="boxMinimumDay m-t-xs" style="display: inline-block;"><?php __('lblDays'); ?></p>
												<?php
											}
										} ?>													
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="hr-line-dashed"></div>

                        <div class="clearfix">
                            <button class="ladda-button btn btn-primary btn-lg pull-left btn-phpjabbers-loader" data-style="zoom-in">
                                <span class="ladda-label"><?php __('plugin_base_btn_save'); ?></span>
                                <?php include $controller->getConstant('pjBase', 'PLUGIN_VIEWS_PATH') . 'pjLayouts/elements/button-animation.php'; ?>
                            </button>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    </div><!-- /.col-lg-12 -->
</div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.alert_title = "<?php __('logo_image_dtitle');?>";
myLabel.alert_text = "<?php __('logo_image_dbody');?>";
myLabel.btn_delete = "<?php __('btnDelete'); ?>";
myLabel.btn_cancel = "<?php __('btnCancel'); ?>";
</script>