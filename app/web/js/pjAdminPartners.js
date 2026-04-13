var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreate = $("#frmCreate"),
			$frmUpdate = $("#frmUpdate"),
			select2 = ($.fn.select2 !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$modalAddReport = $('#modalAddReport'),
			validator,
			currentUploadForeignID = "";
		
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		
		if ($("#grid").length > 0 && datagrid) {
			function formatLastservice(str, obj) {
				return obj.last_service;
			}
			function formatVehicles(str, obj) {
				return obj.vehicles;
			}
			function formatLastBilling(str, obj) {
				return obj.last_billing_formated;
			}
			function formatStatusLastBilling(str, obj) {
				return '<span class="label label-status status-'+obj.status_last_billing+'">'+obj.status_last_billing_formated+'</span>';
			}
			
			function formatContract(str, obj) {
				return '<a href="index.php?controller=pjAdminPartners&action=pjActionDownloadContract&id='+obj.id+'"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
			}
			
			var buttons = [];
			if (pjGrid.hasAccessUpdate) {
				buttons.push({type: "edit", url: "index.php?controller=pjAdminPartners&action=pjActionUpdate&id={:id}"});
			}
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminPartners&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminPartners&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			var select = false;
			if (actions.length) {
				select = {
					field: "id",
					name: "record[]"
				};
			}
			var $grid = $("#grid").datagrid({
				buttons: buttons,
		          columns: [
							{text: myLabel.name, type: "text", sortable: true, editable: false},
							{text: myLabel.vehicles, type: "text", sortable: true, editable: false, renderer: formatVehicles},
							{text: myLabel.last_billing, type: "text", sortable: true, editable: false, renderer: formatLastBilling},
							{text: myLabel.status_last_billing, type: "text", sortable: true, editable: false, renderer: formatStatusLastBilling},
							{text: myLabel.contract, type: "text", sortable: false, editable: false, renderer: formatContract, align: "center"}
				          ],
				dataUrl: "index.php?controller=pjAdminPartners&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'id', 'last_billing', 'status_last_billing', 'id'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminPartners&action=pjActionSave&id={:id}",
				select: select,
				onRender: function(){
					
				}
			});
		}
		
		
		$(document).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminPartners&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on('click', '.btn-delete-file', function(e) {
	    	if (e && e.preventDefault) {
				e.preventDefault();
			}
	    	var $this = $(this),
	    		$id = $this.attr('data-id');
	    	swal({
				title: myLabel.alert_delete_file_title,
				text: myLabel.alert_delete_file_text,
				html: true,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: myLabel.btn_delete,
				cancelButtonText: myLabel.btn_cancel,
				closeOnConfirm: true,
				showLoaderOnConfirm: true
			}, function (res) {
				if (res) {
					$.post("index.php?controller=pjAdminPartners&action=pjActionDeleteFile", {id: $id}, function(response) {
						$this.closest('.file-column').remove();
			        }).fail(function(xhr) {
				        console.error("Error:", xhr.responseText);
				    });
				}
			});
	    }).on("click", ".btn-add-report", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
	    		params = {},
	    		$form = $this.closest('form'),
	    		$partner_id = $form.find('input[name="foreign_id"]').val();
	    	params.partner_id = $partner_id;
			$.get("index.php?controller=pjAdminPartners&action=pjActionReportForm", params).done(function (data) {
				$modalAddReport.find(".modal-content").html(data);
				$modalAddReport.modal('show');
				validator = $modalAddReport.find("form").validate();
			});
		}).on("input", ".calc-trigger", function (e) {
			//Billing Amount=(Paid by partner)−(Commission) - (Paid bookings we made)
			
			// 1. Lấy giá trị từ các trường đầu vào
	        let totalBookings = parseFloat($('input[name="total_bookings_amount"]').val(), 10) || 0;
	        let paidByPartner = parseFloat($('input[name="paid_by_partner_amount"]').val(), 10) || 0;
	        let paidWeMade = parseFloat($('input[name="paid_bookings_we_made"]').val(), 10) || 0;
	        
	        // 2. Tính hoa hồng (Commission 10%)
	        let commissionPct = parseFloat($('input[name="commission_pct"]').val(), 10) || 0;
	        let commissionAmount = (totalBookings * commissionPct)/100;
	        
	        // Hiển thị số tiền hoa hồng (thường là số âm trong bảng tính)
	        $('input[name="commission_amount"]').val(parseFloat(commissionAmount).toFixed(2));

	        // 3. Công thức tính Billing Amount
	        // Billing = Paid_By_Partner - Commission + Paid_We_Made
	        let billingAmount = paidByPartner - commissionAmount - paidWeMade;

	        // 4. Cập nhật kết quả vào trường Billing amount
	        $('input[name="billing_amount"]').val(parseFloat(billingAmount).toFixed(2));
		}).on("click", ".btnGenerateReportBilling", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminPartners&action=pjActionReportForm", $modalAddReport.find("form").serialize()).done(function (data) {
				if (data.status == 'OK') {
					$modalAddReport.modal('hide');
					getReport(data.partner_id);
				}
			});
		}).on("click", ".btnDownloadReportBilling", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminPartners&action=pjActionDownloadReportBilling", $modalAddReport.find("form").serialize()).done(function (data) {
				window.open(data.pdf_file, '_blank');
			});
		}).on('click', '.btn-delete-report', function(e) {
	    	if (e && e.preventDefault) {
				e.preventDefault();
			}
	    	var $this = $(this),
	    		$id = $this.attr('data-id'),
	    		$form = $this.closest('form'),
	    		$partner_id = $form.find('input[name="foreign_id"]').val();
	    	swal({
				title: myLabel.alert_delete_record_title,
				text: myLabel.alert_delete_record_text,
				html: true,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: myLabel.btn_delete,
				cancelButtonText: myLabel.btn_cancel,
				closeOnConfirm: true,
				showLoaderOnConfirm: true
			}, function (res) {
				if (res) {
					$.post("index.php?controller=pjAdminPartners&action=pjActionDeleteReport", {id: $id}, function(response) {
						getReport($partner_id);
			        }).fail(function(xhr) {
				        console.error("Error:", xhr.responseText);
				    });
				}
			});
	    }).on("click", ".btn-edit-report", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
	    		params = {},
	    		$form = $this.closest('form'),
	    		$id = $this.attr('data-id'),
	    		$partner_id = $form.find('input[name="foreign_id"]').val();
	    	params.id = $id;
	    	params.partner_id = $partner_id;
			$.get("index.php?controller=pjAdminPartners&action=pjActionReportForm", params).done(function (data) {
				$modalAddReport.find(".modal-content").html(data);
				$modalAddReport.modal('show');
				validator = $modalAddReport.find("form").validate();
			});
		}).on("click", ".btnUpdatePartner", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $type = $(this).attr('data-type');
			$('#update_type').val($type);
			if ($('#frmUpdate').valid()) {
				var l = Ladda.create($(".btnUpdatePartner").get(0));
				l.start();
				$('#frmUpdate').submit();
			}
		}).on("change", ".save-price-trigger", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				$partner_id = $modalAddReport.find('input[name="partner_id"]').val(),
				$report_id = $modalAddReport.find('input[name="id"]').val(),
				$tmp_hash = $modalAddReport.find('input[name="tmp_hash"]').val(),
				$booking_id = $this.attr('data-booking_id'),
				$column = $this.attr('data-name'),
				$price = $this.val();
			$.post("index.php?controller=pjAdminPartners&action=pjActionSaveCustomPrice", {
				save_custom_price: 1, 
				partner_id: $partner_id, 
				report_id: $report_id, 
				tmp_hash: $tmp_hash, 
				booking_id: $booking_id, 
				column: $column, 
				price: $price
			}).done(function (data) {
				generateBilling();
			});
		});
		
		$modalAddReport.on('shown.bs.modal', function (e) {
	    	$('.datepicker').datepicker({
                autoclose: true
            });
	    	
	    	var $from = $modalAddReport.find('form');
	    	var $date_from = $('#date_from');
        	var $date_to = $('#date_to');
        	
        	$date_from.datepicker().on('changeDate', function(e) {
                var selectedDate = e.date;
                $date_to.datepicker('setStartDate', selectedDate);
                
                if ($date_to.datepicker('getDate') < selectedDate) {
                    $date_to.datepicker('setDate', '');
                }
                
                generateBilling();
            });
        	
        	$date_to.datepicker().on('changeDate', function(e) {
                var selectedDate = e.date;
                $date_from.datepicker('setEndDate', selectedDate);
                
                if ($date_from.datepicker('getDate') > selectedDate) {
                    $date_from.datepicker('setDate', '');
                }
                
                generateBilling();
            });
        	
        	generateBilling();
	    });
		
		function getFiles($foreign_id) {
	    	$.get("index.php?controller=pjAdminPartners&action=pjActionGetFiles&foreign_id=" + $foreign_id, function(data) {
	    		var $obj = $('#contract_documents');
	    		$obj.html(data);
			});
	    }

		function getReport($foreign_id) {
	    	$.get("index.php?controller=pjAdminPartners&action=pjActionGetReport&foreign_id=" + $foreign_id, function(data) {
	    		var $obj = $('#report_list');
	    		$obj.html(data);
			});
	    }
		
		function generateBilling() {
			$('.pj-loader-modal').show();
	    	$.post("index.php?controller=pjAdminPartners&action=pjActionGenerateBilling", $modalAddReport.find("form").serialize()).done(function (data) {
	    		$modalAddReport.find('.report-billing-data').html(data);
	    		$('.pj-loader-modal').hide();
			});
	    }
		
		$(document).ready(function() {
			if ($(".select-item").length && select2) {
	            $(".select-item").select2({
	                placeholder: myLabel.choose,
	                allowClear: true
	            });
	        }
			
			if ($frmUpdate.length > 0) {
	        	var uploader = new plupload.Uploader({
	        		browse_button: 'hidden-browse-button',
					multi_selection: true,
					url: "index.php?controller=pjAdminPartners&action=pjActionUploadFiles"
				});
				
				uploader.init();
				
				$(document).on('click', '.btn-upload', function(e) {
				    e.preventDefault();
				    currentUploadForeignID = $(this).attr('data-foreign_id'); 
				    $('#hidden-browse-button').click();
				});
				
				uploader.bind('FilesAdded', function(up, files){
					uploader.start();
				});
				
				uploader.bind('BeforeUpload', function (up, file) {
				    up.setOption('multipart_params', {
				        'foreign_id': currentUploadForeignID 
				    });
				});
				
				uploader.bind('FileUploaded', function(up, file, data) {
					if (data.response == 'OK'){
						getFiles(currentUploadForeignID);
					}
				});
				
				if (validate) {
					$frmUpdate.validate({
						onkeyup: false
					});
				}
				
				currentUploadForeignID = $frmUpdate.find('input[name="foreign_id"]').val();
				getFiles(currentUploadForeignID);
				getReport(currentUploadForeignID);
	        }
			
			if ($frmCreate.length > 0 && validate) {
				$frmCreate.validate({
					onkeyup: false
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
	        };
		});
	});
})(jQuery);