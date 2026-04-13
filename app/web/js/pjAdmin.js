var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmLoginAdmin = $("#frmLoginAdmin"),
			$frmForgotAdmin = $("#frmForgotAdmin"),
			$frmUpdateProfile = $("#frmUpdateProfile"),
			validate = ($.fn.validate !== undefined);
		
		if ($frmLoginAdmin.length > 0 && validate) {
			$frmLoginAdmin.validate({
				rules: {
					login_email: {
						required: true,
						email: true
					},
					login_password: "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmForgotAdmin.length > 0 && validate) {
			$frmForgotAdmin.validate({
				rules: {
					forgot_email: {
						required: true,
						email: true
					}
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		if ($frmUpdateProfile.length > 0 && validate) {
			$frmUpdateProfile.validate({
				rules: {
					"email": {
						required: true,
						email: true
					},
					"password": "required",
					"name": "required"
				},
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em"
			});
		}
		
		$(document).ready(function() {
			if ($('.metric-card').length > 0) {
				$.get('index.php?controller=pjAdmin&action=pjActionGetMetric', function(data) {
					var top_driver = Object.keys(data.top_driver_arr).length;
					if (top_driver > 0) {
						$('#op-driver-id').html(data.top_driver_arr.driver_name);
						$('#op-driver-rev').html(data.top_driver_arr.total_revenue);
					} else {
						$('#op-driver-id').html('-');
						$('#op-driver-rev').html(0);
					}
					
					if (parseInt(data.total_bookings) > 0) {
						$('#op-total-km').html(data.total_distance);
						$('#op-total-km-bookings').html(data.total_bookings);
					} else {
						$('#op-total-km').html(0);
						$('#op-total-km-bookings').html(0);
					}
					
					var top_vehicle = Object.keys(data.max_vehicle).length;
					if (top_vehicle > 0) {
						$('#op-veh-id').html(data.max_vehicle.vehicle_name);
						$('#op-veh-km').html(data.max_vehicle.total_driven_km);
					} else {
						$('#op-veh-id').html('-');
						$('#op-veh-km').html(0);
					}
					
					var top_destination = Object.keys(data.top_destination_arr).length;
					if (top_destination > 0) {
						$('#op-top-dest').html(data.top_destination_arr.destination);
						$('#op-top-dest-bookings').html(data.top_destination_arr.cnt_bookings);
					} else {
						$('#op-top-dest').html('-');
						$('#op-top-dest-bookings').html(0);
					}
				});
			}
		});
	});
})(jQuery);