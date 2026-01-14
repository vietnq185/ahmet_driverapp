<div class="modal-header">
    <h4 class="modal-title text-muted" id="serviceModalLabel"><?php __('lblAddAccident');?></h4>
  </div>
  <div class="modal-body">
    <form id="formAccident">
    	<input type="hidden" name="save_accident" value="1" />
    	<input type="hidden" name="maintrance_id" id="maintrance_id" value="<?php echo $controller->_get->toString('maintrance_id');?>" />
    	<input type="hidden" name="accident_id" id="accident_id" value="<?php echo $tpl['accident_id'];?>" />
      <div class="row">
        <div class="col-md-4 form-group">
          <label class="text-blue"><?php __('lblAccidentDate');?></label>
          <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                <input type="text" name="date" value="<?php echo isset($tpl['arr']) ? date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date'])) : '';?>" class="form-control datepick required" readonly data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
            </div>
        </div>
        <div class="col-md-4 form-group">
          <label class="text-blue"><?php __('lblAccidentTime');?></label>
          <div class="input-group clockpicker">
    			<input type="text" name="time" value="<?php echo isset($tpl['arr']) ? date($tpl['option_arr']['o_time_format'], strtotime($tpl['arr']['time'])) : '';?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" readonly>
    			<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
    		</div>
        </div>
        <div class="col-md-4 form-group">
          <label class="text-blue"><?php __('lblAccidentDriverName');?></label>
          <input type="text" name="driver_name" value="<?php echo isset($tpl['arr']['driver_name']) ? pjSanitize::html($tpl['arr']['driver_name']) : '';?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 form-group">
          <label class="text-blue"><?php __('lblAccidentLocationAccident');?></label>
          <input type="text" name="location_accident" value="<?php echo isset($tpl['arr']['location_accident']) ? pjSanitize::html($tpl['arr']['location_accident']) : '';?>" class="form-control">
        </div>
        <div class="col-md-4 form-group">
          <label class="text-blue"><?php __('lblAccidentInstanceNumber');?></label>
          <input type="text" name="instance_number" value="<?php echo isset($tpl['arr']['instance_number']) ? pjSanitize::html($tpl['arr']['instance_number']) : '';?>" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <div class="checkbox">
            <label class="text-blue" style="font-weight: bold; cursor: pointer;">
                <input type="checkbox" name="is_second_vehicle_involved" <?php echo isset($tpl['arr']['is_second_vehicle_involved']) && (int)$tpl['arr']['is_second_vehicle_involved'] == 1 ? 'checked="checked"' : '';?> id="chkSecondVehicle"> <?php __('lblAccidentASecondVehicleInvolved');?>
            </label>
        </div>
    </div>
    
    <div id="second-vehicle-info" style="display: <?php echo isset($tpl['arr']['is_second_vehicle_involved']) && (int)$tpl['arr']['is_second_vehicle_involved'] == 1 ? '' : 'none';?>; border-left: 3px solid #337ab7; padding-left: 15px; margin-bottom: 20px;">
        <div class="row">
            <div class="col-md-4 form-group">
                <label class="text-blue"><?php __('lblAccidentDriverName');?></label>
                <input type="text" class="form-control" name="second_driver_name" value="<?php echo isset($tpl['arr']['second_driver_name']) ? pjSanitize::html($tpl['arr']['second_driver_name']) : '';?>">
            </div>
            <div class="col-md-4 form-group">
                <label class="text-blue"><?php __('lblAccidentLicencePlateNumber');?></label>
                <input type="text" class="form-control" name="second_licence_plate_number" value="<?php echo isset($tpl['arr']['second_licence_plate_number']) ? pjSanitize::html($tpl['arr']['second_licence_plate_number']) : '';?>">
            </div>
            <div class="col-md-4 form-group">
                <label class="text-blue"><?php __('lblAccidentInstanceCompanyNumber')?></label>
                <input type="text" class="form-control" name="second_instance_number" value="<?php echo isset($tpl['arr']['second_instance_number']) ? pjSanitize::html($tpl['arr']['second_instance_number']) : '';?>">
            </div>
        </div>
    </div>

      <div class="form-group">
        <label class="text-blue"><?php __('lblAccidentNotes')?></label>
        <textarea class="form-control" name="notes" rows="4"><?php echo isset($tpl['arr']['notes']) ? pjSanitize::html($tpl['arr']['notes']) : '';?></textarea>
      </div>

	<div class="light-green-box">
          <div class="row row-flex-center">
            <div class="col-md-9">
            	<div class="file-list" id="vehicle_accidents">
            		<div class="photo-item empty"><?php __('lblNoPhotos');?></div>
                </div>
               
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-default btn-upload btn-block btn-outline btn-primary btn-sm"" data-foreign_id="<?php echo $tpl['accident_id'];?>" data-type="vehicle_accidents">
                <?php __('btnAddPhotosProtocol');?>
              </button>
            </div>
          </div>
      </div>
    </form>
  </div>
  <div class="modal-footer" style="border-top: none; text-align: center;">
    <button type="button" class="btn btn-primary btn-lg btnSaveAccident" style="min-width: 150px;"><?php __('btnSave');?></button>
    <button type="button" class="btn btn-secondary btn-lg" style="min-width: 150px;" data-dismiss="modal"><?php __('btnCancel');?></button>
  </div>