var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		var $frmReport = $("#frmReport"),
			$frmDailyPerformanceReport = $("#frmDailyPerformanceReport"),
			$frmVisualsReport = $('#frmVisualsReport'),
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
		
		function generateDailyPerformanceReport() 
		{
			$('.ibox-content').addClass('sk-loading');
			$.post("index.php?controller=pjAdminReports&action=pjActionDailyPerformance", $frmDailyPerformanceReport.serialize()).done(function (data) {
				if (!(data.code != undefined && data.status == 'ERR')) 
				{
					$('#pjFdDailyPerformanceReport').html(data);
				}
				$('.ibox-content').removeClass('sk-loading');
			});
		}
		
		$(document).on("change", "#driver_id, #vehicle_id", function (e) {
			generateReport.call(null);
		}).on("change", "#report_selector", function (e) {
			generateDailyPerformanceReport.call(null);
		});
		
		
		$(document).ready(function() {
			if ($frmReport.length > 0 || $frmDailyPerformanceReport.length > 0) {
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
			}
			
			if ($frmReport.length > 0) 
			{
				generateReport.call(null);
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
			
			if ($frmDailyPerformanceReport.length > 0) {
				generateDailyPerformanceReport.call(null);
				$frmDailyPerformanceReport.find('input[name="date"]').datepicker({
		            autoclose: true
		        }).on('changeDate', function (e) {
		        	generateDailyPerformanceReport.call(null);
				});
			}
			
			if ($frmVisualsReport.length > 0) {
				Chart.register(ChartDataLabels);
				let charts = {};
				
				function renderChart(id, type, labels, datasets, titleText, isHorizontal = false, isCurrency = false) {
				    if (charts[id]) charts[id].destroy();
				    
				    const safeTitle = String(titleText || "").toUpperCase();
				    const isPieDoughnut = (type === 'pie' || type === 'doughnut');
				    const checkIsCurrency = (label) => {
				        const text = String(label || "").toUpperCase();
				        return text.includes("COST") || text.includes("REVENUE") || text.includes("PRICE") || text.includes("€") || isCurrency;
				    };
				    
				    const currencyFormatter = new Intl.NumberFormat('de-AT', { 
				        style: 'currency', 
				        currency: 'EUR',
				        minimumFractionDigits: 2, // Luôn hiển thị ít nhất 2 số (ví dụ: ,00)
				        maximumFractionDigits: 2  // Không hiển thị quá 2 số
				    });
				    
				    charts[id] = new Chart(document.getElementById(id), {
				        type: type,
				        data: { labels: labels, datasets: datasets },
				        options: {
				            indexAxis: isHorizontal ? 'y' : 'x',
				            responsive: true,
				            plugins: {
				                title: { 
				                    display: true, text: safeTitle, 
				                    font: { size: 18, weight: 'bold' }, padding: { bottom: 35 }, color: '#1a2a3a' 
				                },
				                tooltip: {
				                    callbacks: {
				                        // Format số tiền khi di chuột vào
				                    	label: (context) => {
				                    		let val = context.parsed.y !== undefined ? context.parsed.y : context.parsed;
				                    	    if (isHorizontal) val = context.parsed.x;
				                    	    
				                    	    if (checkIsCurrency(context.dataset.label)) {
				                    	    	if (context.dataset.label !== undefined) {
				                    	    		return context.dataset.label + ': ' + new Intl.NumberFormat('de-AT', { 
					                    	            style: 'currency', 
					                    	            currency: 'EUR',
					                    	            minimumFractionDigits: 2,
					                    	            maximumFractionDigits: 2 
					                    	        }).format(val);
				                    	    	} else {
				                    	    		return new Intl.NumberFormat('de-AT', { 
					                    	            style: 'currency', 
					                    	            currency: 'EUR',
					                    	            minimumFractionDigits: 2,
					                    	            maximumFractionDigits: 2 
					                    	        }).format(val);
				                    	    	}
				                    	    }
				                    	    if (context.dataset.label !== undefined) {
				                    	    	return context.dataset.label + ': ' + val.toLocaleString('de-AT');
				                    	    } else {
				                    	    	return val.toLocaleString('de-AT');
				                    	    }
				                        }
				                    }
				                },
				                datalabels: {
				                    anchor: isPieDoughnut ? 'center' : 'end',
				                    align: isPieDoughnut ? 'center' : 'end',
				                    color: isPieDoughnut ? '#fff' : '#444', 
				                    font: { weight: 'bold', size: 12 },
				                    display: function(context) {
				                        var value = context.dataset.data[context.dataIndex];
				                        return value > 0; 
				                    },
				                    formatter: (value, context) => {
				                    	if (value === null || value === undefined) return '';
				                    	if (!value || value <= 0) return '';
				                        
				                        const dsLabel = context.dataset.label;
				                        if (checkIsCurrency(dsLabel)) {
				                            // Sử dụng cấu hình 2 chữ số thập phân
				                            return new Intl.NumberFormat('de-AT', { 
				                                style: 'currency', 
				                                currency: 'EUR',
				                                minimumFractionDigits: 2,
				                                maximumFractionDigits: 2 
				                            }).format(value);
				                        }
				                        // Nếu không phải tiền (ví dụ KM) thì có thể làm tròn 1 số hoặc giữ nguyên
				                        return value.toLocaleString('de-AT', { maximumFractionDigits: 1 });
				                    }
				                },
				                legend: { position: 'bottom' }
				            },
				            layout: { 
				                padding: isPieDoughnut ? 0 : { 
				                    top: 25, 
				                    right: isHorizontal ? 70 : 35, // Tăng lên 70 nếu là biểu đồ ngang để không mất chữ €
				                    left: 10 
				                } 
				            }
				        }
				    });
				}
				
				function getVisualReports() {
					$('.ibox-content').addClass('sk-loading');
					$.post("index.php?controller=pjAdminReports&action=pjActionGetVisualReports", $frmVisualsReport.serialize()).done(function (data) {
						renderChart('chartProvider', 'bar', data.provider_arr.map(i => i.label), [{ label: 'Revenue', data: data.provider_arr.map(i => i.value), backgroundColor: ['#36A2EB','#FF6384','#FFCD56','#4BC0C0', '#e74c3c', '#2ecc71', '#f1c40f'] }], 'Revenue by Provider', true);
						
						renderChart('chartPayment', 'bar', data.payment_method_arr.map(i => i.label), [{ label: 'Revenue', data: data.payment_method_arr.map(i => i.value), backgroundColor: ['#36A2EB','#FF6384','#FFCD56','#4BC0C0', '#e74c3c', '#2ecc71', '#f1c40f'] }], 'Payment Methods', true);
						
						renderChart('chartDrivers', 'bar', data.top_driver_arr.map(i => i.label), [{ label: 'Revenue', data: data.top_driver_arr.map(i => i.value), backgroundColor: '#ff6600' }], 'Top Drivers', true);
						
						renderChart('chartFleet', 'bar', data.top_vehicle_arr.vehicle_name, [
							{
			                    label: 'Distance (KM)',
			                    data: data.top_vehicle_arr.driven_km,
			                    backgroundColor: 'rgba(155, 89, 182, 0.6)',
			                    order: 2
			                },
			                {
			                    label: 'Fuel Cost',
			                    data: data.top_vehicle_arr.fuel_cost,
			                    type: 'line',
			                    borderColor: '#e67e22',
			                    backgroundColor: '#e67e22',
			                    fill: false,
			                    order: 1
			                }
				        ], 'Fleet: Distance vs Fuel Cost');
						
						renderChart('chartDestinations', 'bar', data.top_destination_arr.map(i => i.label), [{ label: 'Bookings', data: data.top_destination_arr.map(i => i.value), backgroundColor: '#36A2EB' }], 'Top Destinations', true);
						
						renderChart('chartAirport', 'bar', data.top_airport_arr.map(i => i.label), [{ label: 'Bookings', data: data.top_airport_arr.map(i => i.value), backgroundColor: '#FFCD56' }], 'Top Airports', true);
						
						$('.ibox-content').removeClass('sk-loading');
					});
				}
				
				getVisualReports.call(null);

				$('#visual_date_from').datepicker({
		            autoclose: true
		        });
		        $('#visual_date_to').datepicker({
		            autoclose: true
		        });
		        
		        $(document).on("click", ".btnGenerateVisualReport", function (e) {
					if (e && e.preventDefault) {
						e.preventDefault();
					}
					getVisualReports.call(null);
				});
			}
			
		});
	});
})(jQuery_1_8_2);