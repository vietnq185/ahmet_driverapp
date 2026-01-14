<div class="modal-header">
    <h4 class="modal-title text-muted" id="serviceModalLabel"><?php __('lblAddService');?></h4>
  </div>
  <div class="modal-body">
    <form id="formService">
    	<input type="hidden" name="save_service" value="1" />
    	<input type="hidden" name="maintrance_id" id="maintrance_id" value="<?php echo $controller->_get->toString('maintrance_id');?>" />
    	<input type="hidden" name="service_id" id="service_id" value="<?php echo $tpl['service_id'];?>" />
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label class="text-blue"><?php __('lblSelectServiceType');?></label>
            <select name="service_type_id[]" multiple="multiple" class="form-control select-item required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" style="height: 40px; border-radius: 8px;">
            	<option value=""><?php __('lblSelectServiceType');?></option>
            	<?php foreach ($tpl['service_type_arr'] as $val) { ?>
            		<option value="<?php echo $val['id'];?>" <?php echo isset($tpl['service_type_ids_arr']) && in_array($val['id'], $tpl['service_type_ids_arr']) ? 'selected="selected"' : '';?>><?php echo pjSanitize::html($val['name']);?></option>
            	<?php } ?>
            </select>
          </div>
        </div>
      </div>

      <div class="light-green-box">
        <div class="row">
          <div class="col-md-4 form-group">
            <label class="text-blue"><?php __('lblServiceKm');?></label>
            <input type="text" name="km" value="<?php echo isset($tpl['arr']['km']) ? pjSanitize::html($tpl['arr']['km']) : '';?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" >
          </div>
          <div class="col-md-4 form-group">
            <label class="text-blue"><?php __('lblServiceDate');?></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
                <input type="text" name="date" value="<?php echo isset($tpl['arr']['date']) ? date($tpl['option_arr']['o_date_format'], strtotime($tpl['arr']['date'])) : '';?>" class="form-control datepick required" readonly data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
            </div>
          </div>
          <div class="col-md-4 form-group">
            <label class="text-blue"><?php __('lblServiceCost');?></label>
            <div class="input-group">
				<input type="text" name="cost" id="cost" value="<?php echo isset($tpl['arr']['cost']) ? pjSanitize::html($tpl['arr']['cost']) : '';?>" class="form-control number text-right" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>" />
				<span class="input-group-addon"><?php echo pjCurrency::getCurrencySign($tpl['option_arr']['o_currency'], false) ?></span>
			</div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5 form-group">
            <label class="text-blue"><?php __('lblServiceStation');?></label>
            <input type="text" name="service_station" value="<?php echo isset($tpl['arr']['service_station']) ? pjSanitize::html($tpl['arr']['service_station']) : '';?>" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
          </div>
        </div>
      </div>

	<div class="light-green-box">
          <div class="row row-flex-center">
            <div class="col-md-9">
            	<div class="file-list" id="service_invoice">
            		<div class="photo-item empty"><?php __('lblNoDocuments');?></div>
                </div>
               
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-default btn-upload btn-block btn-outline btn-primary btn-sm"" data-foreign_id="<?php echo $tpl['service_id'];?>" data-type="service_invoice">
                <?php __('btnAddServiceInvoice');?>
              </button>
            </div>
          </div>
      </div>
      
    </form>
  </div>
  <div class="modal-footer" style="border-top: none; text-align: center;">
    <button type="button" class="btn btn-primary btn-lg btnSaveService" style="min-width: 150px;"><?php __('btnSave');?></button>
    <button type="button" class="btn btn-secondary btn-lg" style="min-width: 150px;" data-dismiss="modal"><?php __('btnCancel');?></button>
  </div>