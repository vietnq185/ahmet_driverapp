<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('lblInstallJs1_title'); ?></h2>
            </div>
        </div>

        <p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('lblInstallJs1_body'); ?></p>
    </div>
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<form action="" method="get" class="form-horizontal">
					<div style="display: <?php echo $tpl['is_flag_ready'] ? null : 'none';?>">
						<div class="m-b-lg">
							<h2 class="no-margins"><?php __('lblInstallLanguageConfig'); ?></h2>
						</div>
						<div class="row">
							<div class="col-lg-8">
								<div class="form-group">
									<label class="col-lg-3 col-md-4 control-label"><?php __('lblInstallConfigLocale'); ?></label>
									<div class="col-lg-5 col-md-8">
										<select name="install_locale" id="install_locale" class="form-control">
											<option value="">-- <?php __('lblChoose'); ?> --</option>
											<?php
											foreach ($tpl['locale_arr'] as $locale)
											{
												?><option value="<?php echo $locale['id']; ?>"><?php echo pjSanitize::html($locale['title']); ?></option><?php
											}
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="hr-line-dashed"></div>
					</div>
					<p class="alert alert-info alert-with-icon m-t-xs">
						<i class="fa fa-info-circle"></i><?php __('lblInstallCodeStep1'); ?>
					</p>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="col-xs-12">
									<textarea class="form-control textarea_install" rows="5">
&lt;meta http-equiv="X-UA-Compatible" content="IE=edge" /&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;</textarea>
								</div>
							</div>
						</div>
					</div>
					
					<p class="alert alert-info alert-with-icon m-t-xs">
						<i class="fa fa-info-circle"></i><?php __('lblInstallCodeStep2'); ?>
					</p>
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="col-xs-12">
									<textarea class="form-control textarea_install" id="install_code" rows="5">
&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad"&gt;&lt;/script&gt;</textarea>
								</div>
							</div>
						</div>
						<div style="display:none" id="hidden_code">&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadJS"&gt;&lt;/script&gt;</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>