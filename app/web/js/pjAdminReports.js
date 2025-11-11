var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmReport = $("#frmReport"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		function generateReport() 
		{
			var $printUrl = $('#pjFdPrintReprot');
			var href = $printUrl.attr('data-href');
			href = href + "&driver_id=" + $("#driver_id").val();
			href = href + "&vehicle_id=" + $("#vehicle_id").val();
			href = href + "&date_from=" + $("#date_from").val();
			href = href + "&date_to=" + $("#date_to").val();
			$printUrl.attr('href', href);
			$('.ibox-content').addClass('sk-loading');
			$.post("index.php?controller=pjAdminReports&action=pjActionGenerate", $frmReport.serialize()).done(function (data) {
				if (!(data.code != undefined && data.status == 'ERR')) 
				{
					$('#pjFdReportContent').html(data);
				}
				$('.ibox-content').removeClass('sk-loading');
			});
		}
		
		if ($frmReport.length > 0) 
		{
			generateReport.call(null);
			
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
	        };
	        $('#date_from').datepicker({
	            autoclose: true
	        }).on('changeDate', function (e) {
	        	generateReport.call(null);
			});
	        $('#date_to').datepicker({
	            autoclose: true
	        }).on('changeDate', function (e) {
	        	generateReport.call(null);
			});
		}
		
		$(document).on("change", "#driver_id, #vehicle_id", function (e) {
			generateReport.call(null);
		});
		
	});
})(jQuery_1_8_2);