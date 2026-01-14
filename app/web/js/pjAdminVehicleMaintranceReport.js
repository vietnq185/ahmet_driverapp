var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmReport = $("#frmReport"),
			datepicker = ($.fn.datepicker !== undefined),
			select2 = ($.fn.select2 !== undefined),
			validate = ($.fn.validate !== undefined),
			validator;
		
		if ($(".select-item").length && select2) {
            $(".select-item").select2({
                placeholder: myLabel.choose,
                allowClear: true
            });
        }
		
		if ($('#datePickerOptions').length) {
        	$.fn.datepicker.dates['en'] = {
        		days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    		    daysMin: $('#datePickerOptions').data('days').split("_"),
    		    daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    		    months: $('#datePickerOptions').data('months').split("_"),
    		    monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    		    format: $('#datePickerOptions').data('format'),
            	weekStart: parseInt($('#datePickerOptions').data('wstart'), 10),
    		};
        	
        	var $fromDate = $('#from_date');
        	var $toDate = $('#to_date');
        	$('.datepick').datepicker({
                autoclose: true
            });
        	
        	$fromDate.datepicker().on('changeDate', function(e) {
                var selectedDate = e.date;
                $toDate.datepicker('setStartDate', selectedDate);
                
                if ($toDate.datepicker('getDate') < selectedDate) {
                    $toDate.datepicker('setDate', '');
                }
            });
        	
        	$toDate.datepicker().on('changeDate', function(e) {
                var selectedDate = e.date;
                $fromDate.datepicker('setEndDate', selectedDate);
                
                if ($fromDate.datepicker('getDate') > selectedDate) {
                    $fromDate.datepicker('setDate', '');
                }
            });
        };
		
		if ($frmReport.length > 0 && validate) {
			$frmReport.validate({
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					
					$.post("index.php?controller=pjAdminVehicleMaintranceReport&action=pjActionGetReport", $frmReport.serialize()).done(function (data) {
						$('#reportDetails').html(data);
			        	l.stop();
					}).fail(function(xhr) {
						l.stop();
				    });
					return false;
				}
			});
		}

		$(document).on("click", "#btnRunReport", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			
		});
	});
})(jQuery);