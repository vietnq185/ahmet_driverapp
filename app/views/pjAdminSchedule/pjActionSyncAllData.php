<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$_synch_types = __('_synch_types', true);
$types = array(
    'client',
    'driver',
    'fleet',
    'extra',
    'area',
    'station',
    'location',
    'price',
    'voucher',
    'booking'
);
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-12">
				<h2><?php __('infoSyncDataTitle'); ?></h2>
			</div>
		</div>
		<p class="m-b-none"><i class="fa fa-info-circle"></i><?php __('infoSyncDataBody'); ?></p>
	</div>
</div>
<div class="row wrapper wrapper-content animated fadeInRight">
	<div class="col-lg-12">
		<div class="ibox float-e-margins">
			<div class="ibox-content">
				<form action="" method="post" id="frmSyncData">
					<input type="hidden" name="do_sync" value="1" />
					<div class="row">
						<div class="col-md-3 col-sm-6">
							<div class="form-group">
                				<label class="control-label block"><?php __('lblSyncProvider');?>:</label>
                				<select name="provider_id" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                					<option value="">-- <?php __('lblChoose');?> --</option>
                					<?php
                					foreach ($tpl['provider_arr'] as $val)
                					{
                					    ?><option value="<?php echo $val['id']; ?>"><?php echo pjSanitize::html($val['name']); ?></option><?php
                					}
                					?>
                				</select>
                			</div>	
						</div>
						<div class="col-md-3 col-sm-6">
							<div class="form-group">
                				<label class="control-label block"><?php __('lblSyncType');?>:</label>
                				<select name="type" class="form-control required" data-msg-required="<?php __('plugin_base_this_field_is_required', false, true);?>">
                					<option value="">-- <?php __('lblChoose');?> --</option>
                					<?php
                					foreach ($types as $k)
                					{
                					    ?><option value="<?php echo $k; ?>"><?php echo pjSanitize::html($_synch_types[$k]); ?></option><?php
                					}
                					?>
                				</select>
                			</div>	
						</div>
						<div class="col-md-3 col-sm-6">
							<div class="form-group">
    							<label class="control-label block">&nbsp;</label>
    							<button type="submit" class="btn btn-primary"><?php __('btnSyncUpdate');?></button>
                			</div>
						</div>
					</div>
				</form>
				<br/>
				<h2 class="syncDataMsg text-warning"></h2>
				<div class="syncDataResults" style="display: none;">
    				<h3 class="text-success">Total pages (100 items per page): <span class="total_pages"></span></h3>
    				<h3 class="text-success">Total pages updated: <span class="total_pages_updated"></span></h3>
    				<h3 class="text-success totalRecordsUpdatedWrap" style="display: none;">Total records updated: <span class="total_records_updated"></span></h3>
				</div>
			</div>
		</div>
	</div><!-- /.col-lg-8 -->
</div>
<script type="text/javascript">
var myLabel = myLabel || {};
myLabel.show_popup = "<?php echo isset($tpl['popup_message']) && !empty($tpl['popup_message']) ? 1 : 0; ?>";
</script>