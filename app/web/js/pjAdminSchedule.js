var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			validate = ($.fn.validate !== undefined),
			datepicker = ($.fn.datepicker !== undefined),
			sortable = ($.fn.sortable !== undefined),	
			datagrid = ($.fn.datagrid !== undefined),
			$modalSms = $("#modalSms"),
			$modalTurnover = $("#modalTurnover"),
			$modalViewOrder = $("#modalViewOrder"),
			$modalWhatsappSms = $("#modalWhatsappSms"),
			$modalAddNotesForDriver = $("#modalAddNotesForDriver"),
			$modalChangePickupTime = $("#modalChangePickupTime"),
			$frmSyncData = $("#frmSyncData"),
			$adjustment,
			$grid_orders,
			$filterTimer = null, 
			$delayTime = 10000,
			$currentlyTrackingId = null;
		
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

				var selectedDate = e.date;

			    // ? Fix timezone shift � format using local date parts
			    var formattedDate = selectedDate.getFullYear() + '-' +
			        String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' +
			        String(selectedDate.getDate()).padStart(2, '0');

			    //console.log("Selected date:", formattedDate);
			    if(formattedDate){
			    	$('.assign_sel_date').val(formattedDate);
			    }

	        	if (myLabel.isDriver) {
					getDriverSchedule($('.pjSbScheduleForm'));
				} else {
					getSchedule($('.pjSbScheduleForm'), 1);
				}
	        });

			function refreshCaptcha() {
			    var captchaImage = document.getElementById('captchaImage');
			    if (captchaImage) {
			        captchaImage.src = myLabel.install_url + 'index.php?controller=pjAdminSchedule&action=pjActionCaptcha&rand=' + Math.ceil(Math.random() * 999999);
			    }
			}
			
			$('.assign_with_ai').on('click', function(e) {
	        	var selectedDate = $('.assign_sel_date').val();
	        	refreshCaptcha();
	        	swal({
					title: myLabel.alert_assign_order_with_ai_title,
					text: myLabel.alert_assign_order_with_ai_text + $('#captchaContainer').html(),
					html: true,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_yes,
					cancelButtonText: myLabel.btn_no,
					closeOnConfirm: false,
					showLoaderOnConfirm: false
				}, function (res) {
					if (res) {
						var $container = $('.sweet-alert'),
			        		$form_group = $container.find('.form-group'),
			        		$input = $container.find('input[name="ai_process_captcha"]'),
			        		$error_container = $container.find('.ai_process_captcha_err'),
			        		$msg_required = $input.attr('data-msg-required'),
			        		$msg_remote = $input.attr('data-msg-remote'),
			        		$btnConfirm = $container.find('.confirm');
						
			        	$form_group.removeClass('has-error');
			        	$error_container.removeClass('help-block');
			        	$error_container.html('');
						if ($input.val() == '' || $input.length <= 0) {
							$form_group.addClass('has-error');
							$error_container.addClass('help-block');
							$error_container.html($msg_required);
						} else {
							$.post(
						        "index.php?controller=pjAdminSchedule&action=pjActionCheckCaptcha",
						        { ai_process_captcha: $input.val() },
						        function(response) {
						            if (response == 'ERR') {
						            	$form_group.addClass('has-error');
										$error_container.addClass('help-block');
										$error_container.html($msg_remote);
						            } else {
						            	$btnConfirm.prop('disabled', true);
						            	$.post(
									        "index.php?controller=pjAdminAISchedule&action=pjActionIndex",
									        { selected_date: selectedDate },
									        function(response) {
									            if(response.status == "OK") {
									            	getSchedule($('.pjSbScheduleForm'), 1);
									            }
									            swal.close();
									            $btnConfirm.prop('disabled', false);
									        }
									    ).fail(function(xhr) {
									    	$btnConfirm.prop('disabled', false);
									        console.error("Error:", xhr.responseText);
									    });
						            }
						        }
						    ).fail(function(xhr) {
						    	$btnConfirm.prop('disabled', false);
						        console.error("Error:", xhr.responseText);
						    });
						}
					}
				});
	        });
	        
	        $('.reset_assign_with_ai').on('click', function(e) {
	        	var selectedDate = $('.assign_sel_date').val();
	        	refreshCaptcha();
	        	swal({
					title: myLabel.alert_unassign_order_with_ai_title,
					text: myLabel.alert_assign_order_with_ai_text + $('#captchaContainer').html(),
					html: true,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_yes,
					cancelButtonText: myLabel.btn_no,
					closeOnConfirm: false,
					showLoaderOnConfirm: false
				}, function (res) {
					if (res) {
						var $container = $('.sweet-alert'),
			        		$form_group = $container.find('.form-group'),
			        		$input = $container.find('input[name="ai_process_captcha"]'),
			        		$error_container = $container.find('.ai_process_captcha_err'),
			        		$msg_required = $input.attr('data-msg-required'),
			        		$msg_remote = $input.attr('data-msg-remote'),
			        		$btnConfirm = $container.find('.confirm');
						
			        	$form_group.removeClass('has-error');
			        	$error_container.removeClass('help-block');
			        	$error_container.html('');
						if ($input.val() == '' || $input.length <= 0) {
							$form_group.addClass('has-error');
							$error_container.addClass('help-block');
							$error_container.html($msg_required);
						} else {
							$.post(
						        "index.php?controller=pjAdminSchedule&action=pjActionCheckCaptcha",
						        { ai_process_captcha: $input.val() },
						        function(response) {
						            if (response == 'ERR') {
						            	$form_group.addClass('has-error');
										$error_container.addClass('help-block');
										$error_container.html($msg_remote);
						            } else {
						            	$btnConfirm.prop('disabled', true);
						            	$.post(
									        "index.php?controller=pjAdminAISchedule&action=pjActionIndex&type=reset",
									        { selected_date: selectedDate },
									        function(response) {
									            if(response.status == "OK") {
									            	getSchedule($('.pjSbScheduleForm'), 1);
									            }
									            swal.close();
									            $btnConfirm.prop('disabled', false);
									        }
									    ).fail(function(xhr) {
									    	$btnConfirm.prop('disabled', false);
									        console.error("Error:", xhr.responseText);
									    });
						            }
						        }
						    ).fail(function(xhr) {
						    	$btnConfirm.prop('disabled', false);
						        console.error("Error:", xhr.responseText);
						    });
						}
					}
				});
	        });
		}
		
		function getOrders($form) {
			$('.pj-loader').show();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionGetOrders", $form.serialize()).done(function (data) {
				$('.pjSbOrdersList').html(data)
				$('.pj-loader').hide();
			});
		}
		
		function getSchedule($form, $show_loader) {
			$("ol.pjSbOrders").sortable("destroy");
			if ($show_loader == 1) {
				$('.pj-loader').show();
			}
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
			if ($payment_status == 2) {
				swal({
					title: myLabel.alert_driver_payment_cc_title,
					text: myLabel.alert_driver_payment_cc_text,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: myLabel.btn_yes,
					cancelButtonText: myLabel.btn_no,
					closeOnConfirm: true,
					showLoaderOnConfirm: true
				}, function (res) {
					console.log(res)
					if (res) {
						var $is_enter_hale_cash_register = 1;
					} else {
						var $is_enter_hale_cash_register = 0;
					}
					$.post("index.php?controller=pjAdminSchedule&action=pjActionUpdatePaymentStatus", {update_payment_status: 1, id: $booking_id, payment_status: $payment_status, is_enter_hale_cash_register: $is_enter_hale_cash_register}).done(function (data) {
						/* TO DO */
					});
				});
			} else {
				$.post("index.php?controller=pjAdminSchedule&action=pjActionUpdatePaymentStatus", {update_payment_status: 1, id: $booking_id, payment_status: $payment_status}).done(function (data) {
					/* TO DO */
				});
			}
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
		}).on("click", ".btnAssignOrders", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$('#modalAssignOrders').modal('show');
			return false;
		}).on("click", "img.captcha", function () {
        	var $this = $(this);
			$this.attr("src", $this.attr("src").replace(/(&?rand=)\d+/, "$1" + Math.ceil(Math.random() * 999999)));
        });
		
		$("#modalAddNotesForDriver").on('hide.bs.modal', function(){
			var $form = $modalAddNotesForDriver.find("form"),
				$order_id = $form.find('input[name="id"]').val();
			$.post("index.php?controller=pjAdminSchedule&action=pjActionViewOrder", {id: $order_id}).done(function (data) {
				$modalViewOrder.find(".modal-content").html(data);
				$modalViewOrder.modal('show');
			});
		});
		
		$("#modalChangePickupTime").on('shown.bs.modal', function(){
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
					getSchedule($('.pjSbScheduleForm'), 1);
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
		
		$(document).ready(function() {
			$.fn.modal.Constructor.prototype.enforceFocus = function() {};

			$(document).on('focusin', function(e) {
			    if ($(e.target).closest(".select2-dropdown").length || 
			        $(e.target).closest(".select2-search__field").length) {
			        return true;
			    }
			    
			    if (!$(e.target).closest('.modal').length) {
			        return;
			    }
			}).on("click", ".btnLocateVehicelOnMap", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if ($filterTimer !== null) {
        	        clearTimeout($filterTimer);
        	    }
				var $vehicle_id = $(this).attr('data-id');
				$currentlyTrackingId = $vehicle_id;
				loadVehicle($vehicle_id);
		        $('#modalLocateVehicleOnMap').modal('show');
			});
			var map;
	        var roadmap;
	        var satellite;
	        var hybrid;
	        var terrain;
	        var baseLayers;
	        var vehicleMarkersMap = {};
	        var vehicleMarkers;
	        var IdleIcon;
	        var MovingIcon;
			$('#modalLocateVehicleOnMap').on('shown.bs.modal', function (e) {
				map = L.map('map-on-popup', {
		            zoomControl: false 
		        }).setView([47.2576489, 11.3513075], 13);
				// Lấy ngôn ngữ ưu tiên của trình duyệt (ví dụ: 'en-US', 'vi-VN')
		        const clientLanguage = navigator.language || navigator.userLanguage || 'en';
		        
		        // Chỉ lấy mã ngôn ngữ cơ bản (ví dụ: 'en', 'vi', 'de')
		        // Dùng slice(0, 2) để cắt lấy 2 ký tự đầu tiên
		        const languageCode = clientLanguage.slice(0, 2).toLowerCase(); 
		        
		        const langParam = `&hl=${languageCode}`;
				// --- 1. ĐỊNH NGHĨA CÁC LỚP BẢN ĐỒ (TILE LAYERS) ---

		        // A. Roadmap (Mặc định)
		        roadmap = L.tileLayer('http://{s}.google.com/vt/lyrs=m'+langParam+'&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3'],
		            attribution: 'Map data &copy; Google'
		        }).addTo(map); // Thêm Roadmap làm lớp mặc định

		        // B. Satellite
		        satellite = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3']
		        });

		        // C. Hybrid (Kết hợp Roadmap và Satellite)
		        hybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3']
		        });
		        
		        // D. Terrain/Tôp địa hình (Thường dùng lyrs=p hoặc lyrs=t)
		        terrain = L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',{
		            maxZoom: 20,
		            subdomains:['mt0','mt1','mt2','mt3']
		        });
		        
		        L.control.zoom({
		            position: 'topright' // Đặt nút thu phóng ở vị trí mong muốn
		        }).addTo(map);
		        
		     // --- 2. THÊM CÔNG CỤ ĐIỀU KHIỂN CHỌN LAYER ---
		        baseLayers = {
		            "Roadmap": roadmap,
		            "Satellite": satellite,
		            "Hybrid": hybrid,
		            "Terrain": terrain
		        };
		        
		        L.control.layers(baseLayers, null, { collapsed: true, position: 'bottomright'}).addTo(map);
		        
		        
		        vehicleMarkersMap = {};
		        vehicleMarkers = L.featureGroup().addTo(map); // Nhóm chứa tất cả các marker

		        IdleIcon = L.divIcon({
		        	className: 'custom-vehicle-icon',
		            html: '<i class="fa fa-car"></i>', 
		            iconSize: [34, 34], // Điều chỉnh kích thước lớn hơn một chút để chứa nền
		            iconAnchor: [17, 34], // Căn giữa
		            popupAnchor: [0, -34]
		        });
		        
		        MovingIcon = L.divIcon({
		            className: 'moving-vehicle-icon', // Sử dụng CSS mới (màu xanh lá)
		            html: '<i class="fa fa-car"></i>', 
		            iconSize: [34, 34], 
		            iconAnchor: [17, 34], 
		            popupAnchor: [0, -34] 
		        });
			}).on('hidden.bs.modal', function (e) {
				if ($filterTimer !== null) {
        	        clearTimeout($filterTimer);
        	    }
				if (map !== null) {
			        // Hủy bỏ (Destroy) bản đồ hiện tại
			        map.remove(); 
			        map = null; // Reset biến về null
			    }
				$currentlyTrackingId = null;
			})
			
	        // HÀM XỬ LÝ HIGHLIGHT MARKER TRÊN BẢN ĐỒ
	        function highlightMarker(vehicleId, highlight) {
	            var marker = vehicleMarkersMap[vehicleId];
	            if (marker && marker._icon) {
	                if (highlight) {
	                    // Thêm class highlight
	                    L.DomUtil.addClass(marker._icon, 'highlight-marker');
	                    marker.openPopup(); // Mở popup khi highlight (như hover)
	                } else {
	                    // Xóa class highlight
	                    L.DomUtil.removeClass(marker._icon, 'highlight-marker');
	                    marker.closePopup(); // Đóng popup khi hết highlight
	                }
	            }
	        }

	        // HÀM XỬ LÝ TRACKING XE TRÊN BẢN ĐỒ
	        function trackVehicle(vehicleId) {
	        	var marker = vehicleMarkersMap[vehicleId];
	            
	            // 1. Nếu đang tracking chính chiếc xe này, hãy TẮT tracking
	            if ($currentlyTrackingId === vehicleId) {
	                $currentlyTrackingId = null; 
	                console.log(`Stop tracking vehicle: ${vehicleId}`);
	                return false; // Trả về false để biết đã tắt
	            } 
	            
	            // 2. Nếu đang tracking xe khác hoặc chưa tracking, hãy BẬT tracking xe mới
	            $currentlyTrackingId = vehicleId; 
	            console.log(`Start tracking vehicle: ${vehicleId}`);

	            if (marker) {
	                // Lần đầu tiên, dùng flyTo để di chuyển mượt mà và zoom vào
	                var newZoom = map.getZoom() < 15 ? 15 : map.getZoom(); 
	                map.flyTo(marker.getLatLng(), newZoom, { duration: 1.5 });
	            }
	            return true; // Trả về true để biết đã bật
	        }

	        function bindHoverPopup(marker) {
	            marker.on('mouseover', function (e) {
	                this.openPopup();
	            });
	            marker.on('mouseout', function (e) {
	                this.closePopup();
	            });
	        }
			
			// Hàm Tải dữ liệu và Cập nhật bản đồ
	        function loadVehicle($vehicle_id) {
	        	$.ajax({
	                url: 'index.php?controller=pjAdminSchedule&action=getVehicleFromAPI&vehicle_id=' + $vehicle_id, 
	                type: 'GET',
	                dataType: 'json',
	                success: function(vehicle) {
	                    // Xóa tất cả marker cũ
	                    vehicleMarkers.clearLayers(); 

	                  //var position = vehicle.logLast.lonlat;
                        const position = vehicle.logLast?.lonlat;
                        // Đảm bảo có tọa độ để vẽ
                        if (position && position[0] && position[1]) {
                            var lat = position[1];
                            var lng = position[0];
                            var currentSpeed = vehicle.logLast?.speed;
                            var isMoving = vehicle.logLast.isMoving !== undefined ? parseInt(vehicle.logLast.isMoving, 10) : 0;
                            var selectedIcon;
                            var tooltipClassName;
                            var vehicleId = vehicle._id;
                            if (isMoving == 1 || parseInt(currentSpeed, 10) > 0) {
                                selectedIcon = MovingIcon;
                                tooltipClassName = 'vehicle-label-moving';
                            } else {
                                selectedIcon = IdleIcon;
                                tooltipClassName = 'vehicle-label';
                            }
                            var popupContent = `
                                <b>${vehicle.name || 'N/A'}</b><br>
                                Tốc độ: ${currentSpeed} km/h<br>
                                Cập nhật: ${new Date(position.timestamp * 1000).toLocaleTimeString()}
                            `;
                            
                            var marker = L.marker([lat, lng], {
                                icon: selectedIcon // Dùng icon đã định nghĩa
                            })/*.bindPopup(popupContent, { 
                                closeButton: false, 
                                autoClose: false 
                            })*/.bindTooltip(vehicle.name, {
                            	permanent: true,
                                direction: 'top',   // <--- ĐÃ THAY ĐỔI TẠI ĐÂY
                                offset: [0, -25],   // Điều chỉnh vị trí (0, -25) để nhãn cao hơn icon
                                className: tooltipClassName
                            });
                            
                            bindHoverPopup(marker);
                            
                            // 🔑 LƯU TRỮ MARKER VÀ ID
                            vehicleMarkersMap[vehicleId] = marker;
                            marker.vehicleId = vehicleId;
                            
                            vehicleMarkers.addLayer(marker);
                        }
	                    
                        // --- LOGIC TRACKING REALTIME ---
	                    if ($currentlyTrackingId) {
	                        const trackedMarker = vehicleMarkersMap[$currentlyTrackingId];
	                        if (trackedMarker) {
	                            const newLatlng = trackedMarker.getLatLng();
	                            
	                            // Sử dụng panTo để di chuyển bản đồ đến vị trí mới MƯỢT MÀ
	                            map.panTo(newLatlng, { animate: true, duration: 1 }); 
	                            
	                            // Cập nhật lại highlight trên danh sách (đề phòng)
	                            const trackingItem = document.querySelector(`.vehicle-item[data-vehicle-id="${$currentlyTrackingId}"]`);
	                            if (trackingItem) {
	                                document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));
	                                trackingItem.classList.add('is-tracking');
	                            }
	                        } else {
	                            // Nếu xe đang tracking không còn dữ liệu (mất kết nối), dừng tracking
	                            $currentlyTrackingId = null;
	                            document.querySelectorAll('.vehicle-item.is-tracking').forEach(el => el.classList.remove('is-tracking'));
	                        }
	                    } else if (vehicleMarkers.getLayers().length > 0) {
	                         // Nếu KHÔNG có xe nào đang được tracking, fitbounds để bao quát tất cả
	                    	if (map !== null) {
	                         map.invalidateSize(); 
		                         map.fitBounds(vehicleMarkers.getBounds(), { 
		                             padding: [50, 50, 50, 380] // Đã sửa padding
		                         }); 
	                    	}
	                    }
	                },
	                error: function(xhr, status, error) {
	                    console.error("Lỗi tải dữ liệu phương tiện: " + error);
	                }
	            });
	        	
	        	// TỰ ĐỘNG CẬP NHẬT (LIVE TRACKING): Cứ sau 15 giây sẽ tải lại dữ liệu
	        	$filterTimer = setTimeout(function() {
	        		loadVehicle($vehicle_id);
	            }, $delayTime);
	        }
	        
	     // Hàm Tải dữ liệu và Cập nhật bản đồ
	        function checkVehiclesStatus() {
	        	$.ajax({
	                url: 'index.php?controller=pjAdminSchedule&action=pjActionCheckVehiclesStatus', 
	                type: 'GET',
	                dataType: 'json',
	                success: function(vehicles) {
	                	vehicles.forEach(vehicle => {
	                		$('.vehicleFromApiID-' + vehicle.id).removeClass('btnVehiclMoving');
	                		if (vehicle.isMoving == 1) {
	                			$('.vehicleFromApiID-' + vehicle.id).addClass('btnVehiclMoving');
	                		}
	                	});
	                },
	                error: function(xhr, status, error) {
	                    console.error("Lỗi tải dữ liệu phương tiện: " + error);
	                }
	            });
	        	
	        	// TỰ ĐỘNG CẬP NHẬT (LIVE TRACKING): Cứ sau 15 giây sẽ tải lại dữ liệu
	        	$filterTimer = setTimeout(function() {
	        		checkVehiclesStatus();
	            }, $delayTime);
	        }
	        
	        checkVehiclesStatus();
			
			if ($(".select-vehicle").length) {
	            $(".select-vehicle").select2({
	                placeholder: myLabel.choose,
	                allowClear: true,
	                dropdownParent: $('#modalAssignOrders')
	            });
	        }
			
			if ($("#grid_orders").length > 0 && datagrid) {
				function formatFromTo(str, obj) {
					return obj.from_to;
				}
				function formatClient(str, obj) {
					return obj.client_name;
				}
				function formatTotal(str, obj) {
					return obj.total;
				}
				function formatTransferTime(str, obj) {
					return obj.transfer_time;
				}
				var $date = $('.pjSbScheduleForm').find('input[name="date"]').val();
				var buttons = [];
				var actions = [];
				var select = select = {
					field: "id",
					name: "record[]"
				};
				var $grid_orders = $("#grid_orders").datagrid({
					buttons: buttons,
			          columns: [
			        	  		{text: myLabel.order_transfer_time, type: "text", sortable: true, editable: false, renderer: formatTransferTime},
			        	  		{text: myLabel.order_transfer_destinations, type: "text", sortable: true, editable: false, renderer: formatFromTo},
								{text: myLabel.order_client, type: "text", sortable: true, editable: false, renderer: formatClient},
								{text: myLabel.order_vehicle, type: "text", sortable: true, editable: false},
								{text: myLabel.order_passengers, type: "text", sortable: true, editable: false},
								{text: myLabel.order_total, type: "text", sortable: true, editable: false, renderer: formatTotal}
					          ],
					dataUrl: null,
					dataType: "json",
					fields: ['booking_date', 'id', 'id', 'fleet', 'passengers', 'id'],
					paginator: {
						actions: actions,
						gotoPage: true,
						paginate: true,
						total: true,
						rowCount: true
					},
					saveUrl: null,
					select: select,
					onRender: function(){
						$grid_orders.find('.pj-table-icon-edit').each(function() {
							var $this = $(this),
								$tr = $(this).closest('tr'),
								$data_id = $tr.attr('data-id'),
								$arr = $data_id.split('_');
						});
					}
				});
			}
			
			$('#modalAssignOrders').on('shown.bs.modal', function (e) {
				var content = $grid_orders.datagrid("option", "content"),
					cache = $grid_orders.datagrid("option", "cache"),
					$date = $('.pjSbScheduleForm').find('input[name="date"]').val();
				$.extend(cache, {
					q: ''
				});
				$grid_orders.datagrid("option", "cache", cache);
				$grid_orders.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetOrdersToAssign&date=" + $date, "booking_date", "ASC", content.page, content.rowCount);
			}).on('hidden.bs.modal', function (e) {
				$('#frmAssignMultiOrders').get(0).reset();
				$('#order_ids').val('');
				$('#vehicle_id').val('').trigger('change');
				formValidator.resetForm();
				var content = $grid_orders.datagrid("option", "content"),
					cache = $grid_orders.datagrid("option", "cache"),
					$date = $('.pjSbScheduleForm').find('input[name="date"]').val();
				$.extend(cache, {
					q: ''
				});
				$grid_orders.datagrid("option", "cache", cache);
				$grid_orders.datagrid("load", "", "booking_date", "ASC", content.page, content.rowCount);
				$('#modalAssignOrders').find('.alert').html('').hide();
			}).on("submit", ".frm-filter-orders", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = $(this),
					content = $grid_orders.datagrid("option", "content"),
					cache = $grid_orders.datagrid("option", "cache"),
					$date = $('.pjSbScheduleForm').find('input[name="date"]').val();
				$.extend(cache, {
					q: $this.find("input[name='q']").val(),
					date: $date,
				});
				$grid_orders.datagrid("option", "cache", cache);
				$grid_orders.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetOrdersToAssign", "booking_date", "ASC", content.page, content.rowCount);
				return false;
			}).on("ifChanged", ".pj-table-select-row", function (e) {
				var $order_ids = [];
				$('.pj-table-select-row').each(function() {
					if (this.checked) {
						$order_ids.push(this.value);
					}
				});
				$('#order_ids').val($order_ids.join('-'));
			});
			
			var $frmAssignMultiOrders = $('#frmAssignMultiOrders');
			var formValidator = $frmAssignMultiOrders.validate({
				onkeyup: false,
				ignore: "",
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					$.post('index.php?controller=pjAdminSchedule&action=pjActionAssignOrders', $(form).serialize()).done(function (data) {
						if (data.status == 'OK') {
							$('#modalAssignOrders').find('.alert').html(data.text).show();
							getSchedule($('.pjSbScheduleForm'), 0);
							
							var currentTime = Date.now();
							var content = $grid_orders.datagrid("option", "content"),
								cache = $grid_orders.datagrid("option", "cache"),
								$date = $('.pjSbScheduleForm').find('input[name="date"]').val(),
								$q = $('.frm-filter-orders').find("input[name='q']").val();
							$.extend(cache, {
								q: $q,
								date: $date,
							});
							$grid_orders.datagrid("option", "cache", cache);
							$grid_orders.datagrid("load", "index.php?controller=pjAdminSchedule&action=pjActionGetOrdersToAssign", "booking_date", "ASC", content.page, content.rowCount);
							setTimeout(function() {
								$('#modalAssignOrders').find('.alert').html('').hide();
								$('#vehicle_id').val('').trigger('change');
								$('#order_ids').val('');
								$('#frmAssignMultiOrders').get(0).reset();
							}, 3000);
						}
						l.stop();
	        		});
					l.stop();
					return false;
				}
			});
		});
	});
})(jQuery);