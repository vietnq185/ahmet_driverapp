<?php
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$bodies = str_replace("{SIZE}", ini_get('post_max_size'), $bodies);
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-10">
                <h2><?php __('infoVehiclesMaintranceTitle'); ?></h2>
            </div>
        </div><!-- /.row -->

        <p class="m-b-none"><i class="fa fa-info-circle"></i> <?php __('infoVehiclesMaintranceBody');?></p>
    </div><!-- /.col-md-12 -->
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<?php
	$error_code = $controller->_get->toString('err');
	if (!empty($error_code))
    {
    	switch (true)
    	{
    		case in_array($error_code, array('AVM01', 'AVM03')):
    			?>
    			<div class="alert alert-success">
    				<i class="fa fa-check m-r-xs"></i>
    				<strong><?php echo @$titles[$error_code]; ?></strong>
    				<?php echo @$bodies[$error_code]; ?>
    			</div>
    			<?php 
    			break;
            case in_array($error_code, array('AVM04', 'AVM08')):	
    			?>
    			<div class="alert alert-danger">
    				<i class="fa fa-exclamation-triangle m-r-xs"></i>
    				<strong><?php echo @$titles[$error_code]; ?></strong>
    				<?php echo @$bodies[$error_code]; ?>
    			</div>
    			<?php
    			break;
    	}
    }
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="row m-b-md">
                        <div class="col-md-4 col-sm-4">
                        <?php 
                        if ($tpl['has_create'])
                        {
                        	?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminVehiclesMaintrance&amp;action=pjActionCreate" class="btn btn-primary"><i class="fa fa-plus"></i> <?php __('btnAddVehicleMaintrance');?></a>
                            <?php 
                        }
                        ?>
                        </div><!-- /.col-md-6 -->
    
                        <div class="col-md-4 col-sm-8">
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
                        </div><!-- /.col-md-3 -->
                    </div>
                    <div id="grid"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var pjGrid = pjGrid || {};
pjGrid.hasAccessUpdate = <?php echo pjAuth::factory('pjAdminVehiclesMaintrance', 'pjActionUpdate')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasAccessDeleteSingle = <?php echo pjAuth::factory('pjAdminVehiclesMaintrance', 'pjActionDelete')->hasAccess() ? 'true' : 'false';?>;
pjGrid.hasAccessDeleteMulti = <?php echo pjAuth::factory('pjAdminVehiclesMaintrance', 'pjActionDeleteBulk')->hasAccess() ? 'true' : 'false';?>;
pjGrid.queryString = "";
var myLabel = myLabel || {};
myLabel.name = "<?php __('lblVehicleName', false, true); ?>";
myLabel.registration_number = "<?php __('lblVehicleRegistrationNumber', false, true); ?>";
myLabel.last_service = "<?php __('lblLastService', false, true); ?>";
myLabel.tuv = "<?php __('lblMaintranceTuv', false, true); ?>";
myLabel.delete_selected = "<?php __('delete_selected', false, true); ?>";
myLabel.delete_confirmation = "<?php __('delete_confirmation', false, true); ?>";
</script>