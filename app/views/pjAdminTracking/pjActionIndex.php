<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-12">
				<h2><?php __('infoTrackingTitle'); ?></h2>
			</div>
		</div>
		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoTrackingBody'); ?></p>
	</div>
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-xs-12">
		
        <div id="main-container">
        	<div class="btn-toggle-container">
        		<button type="button" id="btnOpenVehiclePanel" class="btn-toggle">
                    <i class="fa fa-chevron-right"></i>
                </button>
                <button type="button" id="btnCloseVehiclePanel" class="btn-toggle">
                    <i class="fa fa-chevron-left"></i>
                </button>
        	</div>
        	<div id="vehicle-list-panel">
        		<div class="filter-vehicles-container">
        			<form action="" method="get" class="frm-filter">
        				<input type="hidden" name="column" value="name" />
        				<input type="hidden" name="direction" value="asc" />
                    	<div class="row">
                    		<div class="col-md-8 col-sm-12">
                    			<input name="q" id="specific-search-box" placeholder="<?php __('lblTrackingSearchPlaceholder');?>" class="form-control" />
                    		</div>
                    		<div class="col-md-4 col-sm-12">
                    			<select name="status" id="specific-filter-box" class="form-control">
                        			<?php foreach (__('tracking_vehicle_statuses', true) as $k => $v) { ?>
                        				<option value="<?php echo $k;?>" <?php echo $k == 2 ? 'selected="selected"' : '';?>><?php echo $v;?></option>
                        			<?php } ?>
                    			</select>
                    		</div>
                    	</div>
                    </form>
            	</div>
                
                <div id="list-header">
                    <h3><?php __('lblTrackingVehicles');?></h3>
                    <div class="row">
                        <div class="col-xs-8">
                            <a href="javascript:void(0);" class="sort-link sort-vehicle-name asc" data-sort="name">
                                <strong><?php __('lblTrackingSortName');?></strong> <i class="fa fa-sort-asc"></i>
                            </a>
                        </div>
                        <div class="col-xs-4 text-right">
                            <a href="javascript:void(0);" class="sort-link" data-sort="speed">
                                <strong><?php __('lblTrackingSortSpeed');?></strong> <i class="fa fa-sort"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div id="vehicle-list-content" class="pj-loader-outer">
                	<div class="pj-loader"></div>
                    <div id="vehicle-list"></div>
                </div>
            </div>
            
            <div id="map-panel">
                <div id="map"></div>
            </div>
        </div>
   </div>
</div>

<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.make = "<?php __('lblTrackingVehicleMake'); ?>";
	myLabel.model = "<?php __('lblTrackingVehicleModel'); ?>";
	myLabel.license_plate = "<?php __('lblTrackingVehicleLicensePlate'); ?>";
	myLabel.seats = "<?php __('lblTrackingVehicleSeats'); ?>";
	myLabel.click_to_toggle = "<?php __('lblTrackingClickToToggleTracking'); ?>";
</script>