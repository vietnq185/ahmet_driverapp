var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			sortable = ($.fn.sortable !== undefined),			
			$modalSms = $("#modalSms"),
			$modalTurnover = $("#modalTurnover"),
			$modalViewOrder = $("#modalViewOrder"),
			$modalWhatsappSms = $("#modalWhatsappSms"),
			$modalAddNotesForDriver = $("#modalAddNotesForDriver"),
			$modalChangePickupTime = $("#modalChangePickupTime"),
			$frmSyncData = $("#frmSyncData"),
			$adjustment;
		
		if (datepicker) {
			$.fn.datepicker.dates['en'] = {
	        	days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	        	daysMin: myLabel.days.split("_"),
	        	daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
	        	months: myLabel.months.split("_"),
	        	monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    		}
			
			$('.date', $('.pjSbScheduleForm')).datepicker().on('changeDate', function(e) {
	        	if (e && e.preventDefault) {
					e.preventDefault();
				}
	        	if (myLabel.isDriver) {
					getDriverSchedule($('.pjSbScheduleForm'));
				} else {
					getSchedule($('.pjSbScheduleForm'));
				}
	        });
		}
		
		function getOrders($form) {
			$('.pj-loader').show();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionGetOrders", $form.serialize()).done(function (data) {
				$('.pjSbOrdersList').html(data)
				$('.pj-loader').hide();
			});
		}
		
		function getSchedule($form) {
			$("ol.pjSbOrders").sortable("destroy");
			$('.pj-loader').show();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionCountOrders", $form.serialize()).done(function (resp) {
				$('.pjCntOrders').html(resp);
				$.post("index.php?controller=pjAdminSchedule&action=pjActionGetSchedule", $form.serialize()).done(function (data) {
					$('.pjSbVehicles').html(data);
					$('.pjSbScheduleForm').submit();
					initSortable();
					
					var $lock_orders = parseInt($('#lock_orders').val(), 10) || 0;
					if ($lock_orders == 1) {
						$("ol.pjSbOrders").sortable('disable');
						$("ol.pjSbOrders").addClass('disabled');
					} else {
						$("ol.pjSbOrders").sortable('enable');
						$("ol.pjSbOrders").removeClass('disabled');
					}
					$('.pj-loader').hide();
				});
			});        	
		}
		
		function getDriverSchedule($form) {
			$('.pj-loader').show();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionCountOrders", $form.serialize()).done(function (resp) {
				$('.pjCntOrders').html(resp);
	        	$.post("index.php?controller=pjAdminSchedule&action=pjActionGetDriverSchedule", $form.serialize()).done(function (data) {
					$('.pjSbVehicles').html(data);
					$('.pj-loader').hide();
				});
			});
		}
		
		
		$(document).on("submit", ".pjSbScheduleForm", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			getOrders($(this));
			return false;
		}).on("click", ".btnFilterOrder", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $date = $(this).attr('data-date'),
				$form = $(this).closest('form');
			$form.find('input[name="date"]').val($date);
			$('.date', $form).datepicker("setDate", $date);
			return false;
		}).on("click", ".pjSbCloseInfoChangeTime", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$id = $this.attr('data-id');
			$.post("index.php?controller=pjAdminSchedule&action=pjActionConfirmTimeChange", {id: $id}).done(function (data) {
				$this.closest('.alert-warning').remove();
			});
			return false;
		}).on("click", ".pjSbOrderRemoveDriverStatus", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$id = $this.attr('data-id');
			$.post("index.php?controller=pjAdminSchedule&action=pjActionRemoveDriverStatus", {id: $id}).done(function (data) {
				$this.closest('.pjSbOrderDriverStatus').remove();
			});
			return false;
		}).on("change", ".pjSbDriverSelector", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$form = $('.pjSbScheduleForm'),
				$vehicle_id = $this.attr('data-vehicle_id'),
				$driver_name = $this.find('option:selected').attr('data-driver_name'),
				$order = $this.attr('data-order'),
				$driver_id = $this.val(),
				$date = $form.find('input[name="date"]').val();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionUpdateBooking", {
				"type": "assign_driver",
				"vehicle_id": $vehicle_id, 
				"driver_id": $driver_id,
				"date": $date,
				"order": $order
			}).done(function (resp) {
				var $html = resp.split("--LIMITER--");
				$('.pjSbDriverContainer').each(function(idx){
					if ($html[idx] != undefined) {
						$(this).html($html[idx]);
					}
				});
				if ($(".select-item").length) {
		            $(".select-item").select2({
		                placeholder: myLabel.choose,
		                allowClear: true
		            });
		        }
				
				$('#driver_name_' + $vehicle_id + '_' + $order).html($driver_name);
			});
			return false;
		}).on("click", ".pjSbSendSmsToDriver", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$parent = $this.closest('td'),
				$form = $('.pjSbScheduleForm'),
				$vehicle_id = $this.attr('data-vehicle_id'),
				$order = $this.attr('data-order'),
				$driver_id = $parent.find('.pjSbDriverSelector').val(),
				$date = $form.find('input[name="date"]').val();
			if (parseInt($driver_id, 10) > 0) {
				$.get("index.php?controller=pjAdminSchedule&action=pjActionSms", {
					"vehicle_id": $vehicle_id,
					"driver_id": $driver_id,
					"date": $date,
					"order": $order
				}).done(function (data) {
					$modalSms.find(".modal-content").html(data);
					$modalSms.modal('show');
					validator = $modalSms.find("form").validate();
				});
			}
		}).on("click", ".btnSendSms", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalSms.find("form").valid()) {
				$.post("index.php?controller=pjAdminSchedule&action=pjActionSms", $modalSms.find("form").serialize()).done(function (data) {
					$modalSms.modal('hide');
				});
			}
		}).on("click", ".pjSbViewTurnover", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$parent = $this.closest('td'),
				$form = $('.pjSbScheduleForm'),
				$vehicle_id = parseInt($this.attr('data-vehicle_id'), 10) || 0,
				$driver_id = $parent.find('.pjSbDriverSelector').val(),
				$date = $form.find('input[name="date"]').val();
			$.get("index.php?controller=pjAdminSchedule&action=pjActionTurnover", {
				"vehicle_id": $vehicle_id,
				"driver_id": $driver_id,
				"date": $date
			}).done(function (data) {
				$modalTurnover.find(".modal-content").html(data);
				$modalTurnover.modal('show');
			});
		}).on("click", ".pjSbBtnRemoveBooking", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			
			var $this = $(this),
				$booking_id = $this.attr('data-id');
			swal({
				title: myLabel.alert_title,
				text: myLabel.alert_text,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: myLabel.btn_delete,
				cancelButtonText: myLabel.btn_cancel,
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			}, function () {
				$.post('index.php?controller=pjAdminSchedule&action=pjActionUpdateBooking', {type: 'delete', booking_id: $booking_id}).done(function (data) {
					if (!(data && data.status)) {
						
					}
					switch (data.status) {
					case "OK":
						swal.close();
						$this.closest('.pjSbOrder').remove();
						break;
					}
				});
			});
		}).on("click", ".pjSbLinkViewOrder", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$id = $this.attr('data-id');
			$('.pj-loader').show();
			$('.pjSbScheduleForm').hide();
			$('.generalInfoForDriver').hide();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionViewOrder", {id: $id}).done(function (data) {
				$('.pjSbDriverSchedule').html(data);
				if ($(".select-item").length) {
		            $(".select-item").select2({
		                placeholder: myLabel.choose,
		                allowClear: true
		            });
		        }
				$('.pj-loader').hide();
			});
			return false;
		}).on("click", ".pjSbDriverViewSchedule", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $date = $(this).attr('data-date'),
				$form = $('.pjSbScheduleForm');
			$form.find('input[name="date"]').val($date);
			$('.date', $form).datepicker("setDate", $date);
			getDriverSchedule($form);
			$('.pjSbScheduleForm').show();
			$('.generalInfoForDriver').show();
			return false;
		}).on("change", ".pjSbDriverSelectPaymentStatus", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $payment_status = $(this).val(),
				$booking_id = $('option:selected', this).attr('data-booking_id');
			$.post("index.php?controller=pjAdminSchedule&action=pjActionUpdatePaymentStatus", {update_payment_status: 1, id: $booking_id, payment_status: $payment_status}).done(function (data) {
				/* TO DO */
			});
			return false;
		}).on("change", ".pjSbDriverSelectBookingStatus", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $driver_status = $(this).val(),
				$booking_id = $('option:selected', this).attr('data-booking_id');
			$.post("index.php?controller=pjAdminSchedule&action=pjActionUpdateBookingStatus", {update_driver_status: 1, id: $booking_id, driver_status: $driver_status}).done(function (data) {
				/* TO DO */
			});
			return false;
		}).on("click tap touchstart", ".pjSbViewOrder", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $order_id = $(this).attr('data-id');
			$.post("index.php?controller=pjAdminSchedule&action=pjActionViewOrder", {id: $order_id}).done(function (data) {
				$modalViewOrder.find(".modal-content").html(data);
				$modalViewOrder.modal('show');
			});
		}).on("click", ".btnLockOrder", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('#lock_orders').val(1);
			$(this).hide();
			$('.btnUnlockOrder').show();
			$("ol.pjSbOrders").sortable('disable');
			$("ol.pjSbOrders").addClass('disabled');
			return false;
		}).on("click", ".btnUnlockOrder", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('#lock_orders').val(0);
			$(this).hide();
			$('.btnLockOrder').show();
			$("ol.pjSbOrders").sortable('enable');
			$("ol.pjSbOrders").removeClass('disabled');
			return false;
		}).on("change", ".pjSbDriverAddNotes", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $notes = $(this).val(),
				$id = $('#booking_id').val();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionDriverAddNotes", {driver_add_notes: 1, 'id': $id, notes: $notes}).done(function (data) {
				/* TO DO */
			});
			return false;
		}).on("click", ".pjSbLnkAddNotesForDriver", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $id = $(this).attr('data-id');
			$.get("index.php?controller=pjAdminSchedule&action=pjActionAddNotesForDriver", {
				"id": $id
			}).done(function (data) {
				$modalAddNotesForDriver.find(".modal-content").html(data);
				$modalAddNotesForDriver.modal('show');
				$modalViewOrder.modal('hide');
				validator = $modalAddNotesForDriver.find("form").validate();
			});
		}).on("click", ".btnConfirmAddNotesForDriver", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalAddNotesForDriver.find("form").valid()) {
				$.post("index.php?controller=pjAdminSchedule&action=pjActionAddNotesForDriver", $modalAddNotesForDriver.find("form").serialize()).done(function (data) {
					$modalAddNotesForDriver.modal('hide');
				});
			}
		}).on("click", ".btnWhatsappMessage", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this), 
				$id = $this.attr('data-id');
			if (parseInt($id, 10) > 0) {
				$.get("index.php?controller=pjAdminSchedule&action=pjActionWhatsAppMessages", {
					"id": $id
				}).done(function (data) {
					$modalWhatsappSms.find(".modal-content").html(data);
					$modalWhatsappSms.modal('show');
					validator = $modalWhatsappSms.find("form").validate();
				});
			}
		}).on("change", ".selectWhatsappMessage", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				$form = $this.closest('form'),
				$booking_id = $form.find('input[name="booking_id"]').val(),
				$id = $this.val();
			if (parseInt($id, 10) > 0) {
				$.get("index.php?controller=pjAdminSchedule&action=pjActionGetWhatsAppMessage", {
					"id": $id,
					"booking_id": $booking_id
				}).done(function (data) {
					$modalWhatsappSms.find(".WhatsappMessageContainer").html(data);
				});
			}
		}).on("click", ".btnSendWhatsappSms", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalWhatsappSms.find("form").valid()) {
				var $form = $modalWhatsappSms.find("form"),
				$c_phone = $form.find('input[name="customer_phone"]').val(),
				$text = $form.find('textarea[name="message"]').val();
				$.post("index.php?controller=pjAdminSchedule&action=pjActionWhatsAppMessages", $form.serialize()).done(function (data) {
					$modalWhatsappSms.modal('hide');
					var $href = 'https://wa.me/'+$c_phone+'?text='+$text;
					window.open($href, '_blank');
				});
			}
		}).on("click", ".pjSbBtnChangePickupTime", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $id = $(this).attr('data-id');
			$.get("index.php?controller=pjAdminSchedule&action=pjActionChangePickupTime", {
				"id": $id
			}).done(function (data) {
				$modalChangePickupTime.find(".modal-content").html(data);
				$modalChangePickupTime.modal('show');
				$modalViewOrder.modal('hide');
				validator = $modalChangePickupTime.find("form").validate();
			});
		}).on("click", ".btnConfirmChangePickupTime", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalChangePickupTime.find("form").valid()) {
				$.post("index.php?controller=pjAdminSchedule&action=pjActionChangePickupTime", $modalChangePickupTime.find("form").serialize()).done(function (data) {
					$modalChangePickupTime.modal('hide');
				});
			}
		});
		
		$("#modalAddNotesForDriver").on('hide.bs.modal', function(){
			var $form = $modalAddNotesForDriver.find("form"),
				$order_id = $form.find('input[name="id"]').val();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionViewOrder", {id: $order_id}).done(function (data) {
				$modalViewOrder.find(".modal-content").html(data);
				$modalViewOrder.modal('show');
			});
		});
		
		$("#modalChangePickupTime").on('shown.bs.modal', function(){console.log(111)
			if ($('.clockpicker').length) {
	        	$('.clockpicker').clockpicker({
	                twelvehour: myLabel.showperiod,
	                placement: 'bottom',
	                align: 'left',
	                container: '#modalChangePickupTime',
	                autoclose: true
	            });
	        };
		}).on('hide.bs.modal', function(){
			var $form = $modalChangePickupTime.find("form"),
				$order_id = $form.find('input[name="id"]').val();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionViewOrder", {id: $order_id}).done(function (data) {
				$modalViewOrder.find(".modal-content").html(data);
				$modalViewOrder.modal('show');
			});
	});
		
		if (sortable) {
			initSortable();
		}
		
		function initSortable() {
			if ($(".select-item").length) {
	            $(".select-item").select2({
	                placeholder: myLabel.choose,
	                allowClear: true
	            });
	        }
			$("ol.pjSbOrders").sortable({
			    group: 'pjSbOrders',
			    pullPlaceholder: false,
			    onDrop: function($item, container, _super) {
			    	var $clonedItem = $('<li/>').css({
			            height: 0
			        });
			        $item.before($clonedItem);
			        $clonedItem.animate({
			            'height': $item.height()
			        });

			        $item.animate($clonedItem.position(), function() {
			            $clonedItem.detach();
			            _super($item, container);
			        });
			        
			        var $vehicle_id = $item.closest('.pjSbOrders').attr('data-vehicle_id'),
			        	$vehicle_order = $item.closest('.pjSbOrders').attr('data-vehicle_order'),
			        	$booking_id = $item.attr('data-booking_id');
			        $.post("index.php?controller=pjAdminSchedule&action=pjActionUpdateBooking", {type: 'assign_vehicle', vehicle_id: $vehicle_id, vehicle_order: $vehicle_order, booking_id: $booking_id}).done(function (data) {
						//getSchedule($('.pjSbScheduleForm'));
					});
			    },
			    onDragStart: function($item, container, _super) {
			        var offset = $item.offset(),
			            pointer = container.rootGroup.pointer;
			        $adjustment = {
			            left: pointer.left - offset.left,
			            top: pointer.top - offset.top
			        };
			        _super($item, container);
			    },
			    onDrag: function($item, position) {
			        $item.css({
			            left: position.left - $adjustment.left,
			            top: position.top - $adjustment.top
			        });
			    }
			});
		}
		
		$(document).ready(function() {
			if ($('.pjSbScheduleForm').length > 0) {
				if (myLabel.isDriver) {
					getDriverSchedule($('.pjSbScheduleForm'));
				} else {
					getSchedule($('.pjSbScheduleForm'));
				}
			}
			
			if (myLabel.show_popup != undefined && parseInt(myLabel.show_popup, 10) == 1) {
				swal({
					title: '',
					text: $('#popupMessage').html(),
					type: "warning",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.alert_btn_close,
					closeOnConfirm: false,
					showLoaderOnConfirm: true
				}, function () {
					$.post('index.php?controller=pjAdminSchedule&action=pjActionClosePopUp', {close_popup: 1}).done(function (data) {
						if (!(data && data.status)) {
							
						}
						switch (data.status) {
						case "OK":
							swal.close();
							break;
						}
					});
				});
			}
		});
		
		if ($frmSyncData.length > 0 && validate) {
			var $page = 1,
				$total_pages = 0;
			$frmSyncData.validate({
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					$('.syncDataResults').hide();
					$('.total_pages').html('');
					$('.total_pages_updated').html('');
					$('.total_records_updated').html('');
					$('.totalRecordsUpdatedWrap').hide();
					$.post('index.php?controller=pjAdminSchedule&action=pjActionGetInfoSync', $(form).serialize()).done(function (data) {
						$total_pages = parseInt(data.total_pages, 10);
						if ($total_pages > 0) {
							$('.syncDataResults').show();
							syncData(l, data.type, data.provider_id, 1);
						} else {
							$('.syncDataMsg').html('No Data found.');
							l.stop();
						}
	        		});
					return false;
				}
			});
			
			function syncData(l, $type, $provider_id, $page) {
				$('.syncDataMsg').html('Please wait while synchronizing ....Do not refresh page.');
				$.get('index.php?controller=pjAdminSchedule&action=pjActionDoSyncData&type=' + $type + "&provider_id=" + $provider_id + "&page=" + $page, function(data) {
					$('.total_pages').html($total_pages);
					$('.total_pages_updated').html($page);
					if (data.next_page <= $total_pages) {
						syncData(l, $type, $provider_id, data.next_page);
					} else {
						$('.total_records_updated').html(data.total_records_updated);
						$('.totalRecordsUpdatedWrap').hide();
						$('.syncDataMsg').html('Data has been synchronized.');
						l.stop();
					}
				});
			}
		}
	});
})(jQuery);