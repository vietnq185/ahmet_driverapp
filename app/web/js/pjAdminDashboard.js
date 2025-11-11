function drawCurveTypes(jsonData1, jsonData2) {
	var options = {
			series: {
				0: {pointShape: "circle"},
				1: {pointShape: "triangle"},
				2: {pointShape: "square"},
				3: {pointShape: "diamond"},
				4: {pointShape: "star"},
				5: {pointShape: "polygon"}
			},
			legend: {
				alignment: 'start',
				position: 'bottom'
			},
			pointSize: 7,
			chartArea: {
				left: 30,
				width: "100%",
				top: 30
			},
			vAxis: {
				gridlines: {
					count: -1
				},
				viewWindow: {
					min: 0
				}
			},
			hAxis: {
				viewWindow: {
					min: 0
				},
				textStyle: {
					color: '#2d2d2d'
				}
			}
	};
	
	function getMeta(jsonData) {
		var i, iCnt, j, jCnt, v, step,
			min = null, 
			max = null,
			ticks = [0],
			maxGgridlines = 4,
			data = JSON.parse(jsonData);
		for (i = 0, iCnt = data.rows.length; i < iCnt; i += 1) {
			for (j = 1, jCnt = data.rows[i].c.length; j < jCnt; j += 1) {
				v = parseInt(data.rows[i].c[j].v, 10);
				if (min === null || v < min) {
					min = v;
				}
				if (max === null || v > max) {
					max = v;
				}
			}
		}
		
		if (min === null) {
			min = 0;
		}
		if (max === null) {
			max = 0;
		}
		
		step = Math.floor(max / maxGgridlines);
		
		iCnt = max;
		if (step <= 0) {
			step = 1;
		}
		for (i = 0; i < iCnt; i += step) {
			ticks.push( i + step );
		}
		
		if (ticks.length <= maxGgridlines) {
			for (i = ticks.length; i <= maxGgridlines; i += 1) {
				ticks.push(i);
			}
		}
		
		return {
			"min": min, 
			"max": max,
			"gridlinesCount": ticks.length,
			"ticks": ticks
		};
	}
	
	var handler = function(e, data) {
		
	};
	
	var o, data1, data2, chart1, chart2;
	
	// Chart 1
	o = getMeta(jsonData1);
	options.vAxis.gridlines.count = o.gridlinesCount;
	options.vAxis.ticks = o.ticks;

	data1 = new google.visualization.DataTable(jsonData1);
	
	chart1 = new google.visualization.LineChart(document.getElementById('chart-1'));
	chart1.draw(data1, options);
	
	google.visualization.events.addListener(chart1, 'click', function (e) {
		handler.call(null, e, data1);
	});
}

google.load('visualization', '1', {packages: ['corechart', 'line']});

(function ($, undefined) {
	$(function () {
		"use strict";
	
		var tabs = ($.fn.tabs !== undefined),
			validate = ($.fn.validate !== undefined),
			$charts = $("#charts"),
			$frmSendSms = $("#frmSendSms"),
			$frmSendPopup = $("#frmSendPopup");
		
		if ($frmSendSms.length > 0 && validate) {
			$frmSendSms.validate({
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					
					$.post("index.php?controller=pjAdmin&action=pjActionSendSms", $(form).serialize()).done(function (data) {
						if (data.status == 'OK') {
		            		$('.pjSbSendSmsMsg').find('.alert').html(data.text).removeClass('alert-danger').addClass('alert-success');
		            		$(form)[0].reset();
		            	} else {
		            		$('.pjSbSendSmsMsg').find('.alert').html(data.text).removeClass('alert-success').addClass('alert-danger');
		            	}
		            	$('.pjSbSendSmsMsg').show();
						l.stop();
					});
					return false;
				}
			});
		}
		
		if ($frmSendPopup.length > 0 && validate) {
			$frmSendPopup.validate({
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					
					$.post("index.php?controller=pjAdmin&action=pjActionSendPopUp", $(form).serialize()).done(function (data) {
						if (data.status == 'OK') {
		            		$('.pjSbSendSmsPopUp').find('.alert').removeClass('alert-danger').addClass('alert-success').html(data.text);
		            		$(form)[0].reset();
		            	} else {
		            		$('.pjSbSendSmsPopUp').find('.alert').removeClass('alert-success').addClass('alert-danger').html(data.text);
		            	}
		            	$('.pjSbSendSmsPopUp').show();
						l.stop();
					});
					return false;
				}
			});
		}
		
		if ($charts.length > 0 && tabs) {
			$charts.tabs({
				disabled: true
			});
		}
		
		function drawChart($ts) {
			$.when($.get('index.php?controller=pjAdmin&action=pjActionChartGet&type=1&ts=' + $ts), $.get('index.php?controller=pjAdmin&action=pjActionChartGet&type=2&ts=' + $ts)).done(function (a1, a2) {
				google.setOnLoadCallback(drawCurveTypes(a1[2].responseText, a2[2].responseText));
				
				if ($charts.length > 0 && tabs) {
					$charts.tabs("enable");
				}
			});
		}
		
		$(document).ready(function() {
			var $today_ts = $('#today_ts').val();
			drawChart($today_ts);
		});
		
		$(document).on("click", ".navUpcomingBookings", function (e) {
			var $this = $(this),
				$step = parseInt($this.attr('data-step'), 10),
				$prev_ts = parseInt($('.navPrevUpcomingBookings').attr('data-ts'), 10),
				$next_ts = parseInt($('.navNextUpcomingBookings').attr('data-ts'), 10),
				$type = $this.attr('data-type');
			if ($type == 'prev') {
				drawChart($prev_ts);
				$('.navPrevUpcomingBookings').attr('data-ts', $prev_ts - $step);
				$('.navNextUpcomingBookings').attr('data-ts', $prev_ts + $step);
			} else {
				drawChart($next_ts);
				$('.navPrevUpcomingBookings').attr('data-ts', $next_ts - $step);
				$('.navNextUpcomingBookings').attr('data-ts', $next_ts + $step);
			}
		});
		/*window.setTimeout(function () {
			window.location.reload();
		}, 1000 * 60 * 2);*/
	});
})(jQuery);