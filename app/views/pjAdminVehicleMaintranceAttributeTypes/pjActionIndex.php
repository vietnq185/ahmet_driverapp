<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-lg-9 col-md-8 col-sm-6">
					<h2><?php __('infoVehicleMaintranceAttributeTypesTitle'); ?></h2>
				</div>
				<div class="col-lg-3 col-md-4 col-sm-6 btn-group-languages">
                    <?php if ($tpl['is_flag_ready']) : ?>
					<div class="multilang"></div>
					<?php endif; ?>    
            	</div>
			</div>
			<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoVehicleMaintranceAttributeTypesBody'); ?></p>
		</div>
	</div>
	 
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-8">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<div class="row">
						<div class="col-lg-6">
							<form action="" method="get" class="form-horizontal frm-filter">
                                <div class="input-group">
									<input type="text" name="q" placeholder="<?php __('btnSearch', false, true); ?>" class="form-control">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
						</div><!-- /.col-lg-6 -->
					</div><!-- /.row -->
					<div id="grid"></div>
				</div>
			</div>
		</div><!-- /.col-lg-8 -->
	
		<div class="col-lg-4">
			<div class="panel no-borders boxFormWrapper">
				<?php 
				if(pjAuth::factory('pjAdminVehicleMaintranceAttributeTypes', 'pjActionCreate')->hasAccess())
				{
				    include_once dirname(__FILE__) . '/elements/add.php';
				}
				?>
			</div><!-- /.panel panel-primary -->
		</div><!-- /.col-lg-4 -->
	</div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	var pjGrid = pjGrid || {};
	pjGrid.queryString = "";
	myLabel.selected_record_id = 0;
	<?php if (isset($get['id']) && (int)$get['id'] > 0)
	{
		?>
		myLabel.selected_record_id = <?php echo (int)$get['id'];?>;
		<?php 
	}
	?>
	pjGrid.hasAccessCreate = <?php echo pjAuth::factory('pjAdminVehicleMaintranceAttributeTypes', 'pjActionCreate')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessUpdate = <?php echo pjAuth::factory('pjAdminVehicleMaintranceAttributeTypes', 'pjActionUpdate')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessDeleteSingle = <?php echo pjAuth::factory('pjAdminVehicleMaintranceAttributeTypes', 'pjActionDelete')->hasAccess() ? 'true' : 'false';?>;
	pjGrid.hasAccessDeleteMulti = <?php echo pjAuth::factory('pjAdminVehicleMaintranceAttributeTypes', 'pjActionDeleteBulk')->hasAccess() ? 'true' : 'false';?>;
	
	myLabel.name = "<?php __('lblAttributeTypeName'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.active = "<?php __('statuses_ARRAY_T'); ?>";
	myLabel.inactive = "<?php __('statuses_ARRAY_F'); ?>";
	myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
	myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
	myLabel.isFlagReady = "<?php echo $tpl['is_flag_ready'] ? 1 : 0;?>";
	myLabel.choose = "<?php __('lblChoose', false, true); ?>";
	myLabel.btn_delete = <?php x__encode('btnDelete'); ?>;
	myLabel.btn_cancel = <?php x__encode('btnCancel'); ?>;	
    <?php if ($tpl['is_flag_ready']) : ?>
    var pjCmsLocale = pjCmsLocale || {};
    pjCmsLocale.langs = <?php echo $tpl['locale_str']; ?>;
    pjCmsLocale.flagPath = "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/";
    <?php endif; ?>
	</script>