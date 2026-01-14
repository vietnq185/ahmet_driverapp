var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreate = $("#frmCreate"),
			$frmUpdate = $("#frmUpdate"),
			select2 = ($.fn.select2 !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$modalAddAccident = $('#modalAddAccident'),
			$modalAddService = $('#modalAddService'),
			validator,
			currentUploadType = "",
			currentUploadForeignID = "";
		
		$.fn.modal.Constructor.prototype.enforceFocus = function() {};
		
		if ($("#grid").length > 0 && datagrid) {
			function formatLastservice(str, obj) {
				return obj.last_service;
			}
			var buttons = [];
			if (pjGrid.hasAccessUpdate) {
				buttons.push({type: "edit", url: "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionUpdate&id={:id}"});
			}
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
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
							{text: myLabel.registration_number, type: "text", sortable: true, editable: false},
							{text: myLabel.last_service, type: "text", sortable: true, editable: false, renderer: formatLastservice},
							{text: myLabel.tuv, type: "text", sortable: true, editable: false}
				          ],
				dataUrl: "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['vehicle_name', 'registration_number', 'id', 'tuv'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionSave&id={:id}",
				select: select,
				onRender: function(){
					
				}
			});
		}
		
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("btn-primary active").removeClass("btn-default")
			.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("btn-primary active").removeClass("btn-default")
			.siblings(".btn").removeClass("btn-primary active").addClass("btn-default");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("submit", ".frm-filter", function (e) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		});
		
		$(document).ready(function() {
		    // 1. Gỡ bỏ mọi sự kiện click cũ để tránh trùng lặp
		    $(document).off('click', 'a.gallery-item');

		    // 2. Lắng nghe sự kiện click thủ công
		    $(document).on('click', 'a.gallery-item', function(e) {
		        e.preventDefault();
		        
		        var $this = $(this);
		        // Xác định container chứa ảnh (Trang Edit hoặc Modal)
		        var $container = $this.closest('.gallery-container');
		        
		        // Nếu không có container, lấy ảnh trong khối cha gần nhất
		        if ($container.length === 0) {
		            $container = $this.parent().parent(); 
		        }

		        var items = [];
		        // Chỉ lấy những thẻ <a> có class gallery-item trong container này
		        var $links = $container.find('a.gallery-item');

		        $links.each(function() {
		            items.push({
		                src: $(this).attr('href'),
		                type: 'image'
		            });
		        });

		        // 3. Mở Gallery bằng API thủ công (Tuyệt đối không đệ quy)
		        $.magnificPopup.open({
		            items: items,
		            gallery: {
		                enabled: true,
		                tCounter: ''
		            },
		            type: 'image',
		            mainClass: 'mfp-with-zoom mfp-fade',
		            index: $links.index($this), // Mở đúng ảnh vừa click
		            image: {
		                titleSrc: function() { return ''; }
		            },
		            callbacks: {
		                open: function() {
		                    // Đảm bảo z-index cao hơn Bootstrap Modal (1050)
		                    $('.mfp-bg').css('z-index', 10000);
		                    $('.mfp-wrap').css('z-index', 10001);
		                }
		            }
		        });
		    });
		});
		
		$(document).ready(function() {
			
			function initGallery() {
				$('body').magnificPopup({
					delegate: 'a.gallery-item',
				    type: 'image',
				    gallery: {
				        enabled: true,
				        tCounter: ''
				    },
				    image: {
				        titleSrc: function(item) {
				            return '';
				        }
				    },
				    mainClass: 'mfp-with-zoom mfp-fade',
				    zoom: {
				        enabled: true, 
				        duration: 300, 
				        opener: function(openerElement) {
				            return openerElement.is('img') ? openerElement : openerElement.find('img');
				        }
				    },
				    callbacks: {
				        open: function() {
				            $('.mfp-bg').css('z-index', 3000);
				            $('.mfp-wrap').css('z-index', 3001);
				        }
				    }
			    });
			}
			
			//initGallery();
			
		    $(document).on('click', '#add-custom-field', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var fieldHTML = $('#customFieldClone').html(),
		    		index = Math.ceil(Math.random() * 999999);
		    	fieldHTML = fieldHTML.replace(/\{INDEX\}/g, 'new_' + index);
		        $("#dynamic-fields-container").append(fieldHTML);
		    }).on('change', '.field-type-select', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		        if ($(this).val() === 'other') {
		            $(this).next('.custom-label-input').show().focus();
		        } else {
		            $(this).next('.custom-label-input').hide();
		        }
		    }).on('click', '.remove-field', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		        $(this).closest('.custom-field-row').remove();
		    }).on('click', '.btn-add-accident', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $this = $(this),
		    		params = {},
		    		$form = $(this).closest('form'),
		    		$maintrance_id = $form.find('input[name="foreign_id"]').val();
		    	params.maintrance_id = $maintrance_id;
				$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionAccidentForm", params).done(function (data) {
					$modalAddAccident.find(".modal-content").html(data);
					$modalAddAccident.modal('show');
					validator = $modalAddAccident.find("form").validate();
				});
		    }).on('click', '.btn-edit-accident', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $this = $(this),
		    		params = {},
		    		$form = $(this).closest('form'),
		    		$maintrance_id = $form.find('input[name="foreign_id"]').val();
		    	params.maintrance_id = $maintrance_id;
		    	params.accident_id = $this.attr('data-id');
				$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionAccidentForm", params).done(function (data) {
					$modalAddAccident.find(".modal-content").html(data);
					$modalAddAccident.modal('show');
					validator = $modalAddAccident.find("form").validate();
					if(parseInt(params.accident_id, 10) > 0) {
						getFiles(params.accident_id, 'vehicle_accidents');
					}
				});
		    }).on('change', '#chkSecondVehicle', function(e) {
		    	if ($(this).is(':checked')) {
		            $('#second-vehicle-info').slideDown(300);
		            $('#checkIcon').css('color', '#337ab7').removeClass('fa-check-square-o').addClass('fa-check-square');
		        } else {
		            $('#second-vehicle-info').slideUp(300);
		            $('#checkIcon').css('color', '#ccc').removeClass('fa-check-square').addClass('fa-check-square-o');
		            
		            $('#second-vehicle-info input').val('');
		        }
		    }).on("click", ".btnSaveAccident", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $form = $modalAddAccident.find("form"),
					$foreign_id = $form.find('input[name="maintrance_id"]').val();
				if ($form.valid()) {
					$.post("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionAccidentForm", $modalAddAccident.find("form").serialize()).done(function (data) {
						$modalAddAccident.modal('hide');
						getMaintranceAccidents($foreign_id);
					});
				}
			}).on('click', '.btn-add-service', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $this = $(this),
		    		params = {},
		    		$form = $(this).closest('form'),
		    		$maintrance_id = $form.find('input[name="foreign_id"]').val();
		    	params.maintrance_id = $maintrance_id;
				$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionServiceForm", params).done(function (data) {
					$modalAddService.find(".modal-content").html(data);
					$modalAddService.modal('show');
					validator = $modalAddService.find("form").validate();
					if ($(".select-item").length && select2) {
			            $(".select-item").select2({
			                placeholder: myLabel.choose,
			                allowClear: true
			            });
			        }
				});
		    }).on('click', '.btn-edit-service', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $this = $(this),
		    		params = {},
		    		$form = $(this).closest('form'),
		    		$maintrance_id = $form.find('input[name="foreign_id"]').val();
		    	params.maintrance_id = $maintrance_id;
		    	params.service_id = $this.attr('data-id');
				$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionServiceForm", params).done(function (data) {
					$modalAddService.find(".modal-content").html(data);
					$modalAddService.modal('show');
					validator = $modalAddService.find("form").validate();
					if ($(".select-item").length && select2) {
			            $(".select-item").select2({
			                placeholder: myLabel.choose,
			                allowClear: true
			            });
			        }
					if(parseInt(params.service_id, 10) > 0) {
						getFiles(params.service_id, 'service_invoice');
					}
				});
		    }).on("click", ".btnSaveService", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $form = $modalAddService.find("form"),
					$foreign_id = $form.find('input[name="maintrance_id"]').val();
				if ($form.valid()) {
					$.post("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionServiceForm", $modalAddService.find("form").serialize()).done(function (data) {
						$modalAddService.modal('hide');
						getMaintranceServices($foreign_id);
					});
				}
			}).on('click', '.btn-delete-file', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $id = $(this).attr('data-id'),
		    		$type = $(this).attr('data-type'),
		    		$foreign_id = $(this).attr('data-foreign_id');
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
						$.post("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionDeleteFile", {id: $id}, function(response) {
							getFiles($foreign_id, $type);
				        }).fail(function(xhr) {
					        console.error("Error:", xhr.responseText);
					    });
					}
				});
		    }).on('click', '.btn-delete-accident', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $id = $(this).attr('data-id'),
		    		$form = $(this).closest('form'),
		    		$foreign_id = $form.find('input[name="foreign_id"]').val();
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
						$.post("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionDeleteAccident", {id: $id}, function(response) {
							getMaintranceAccidents($foreign_id);
				        }).fail(function(xhr) {
					        console.error("Error:", xhr.responseText);
					    });
					}
				});
		    }).on('click', '.btn-delete-service', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $id = $(this).attr('data-id'),
		    		$form = $(this).closest('form'),
		    		$foreign_id = $form.find('input[name="foreign_id"]').val();
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
						$.post("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionDeleteService", {id: $id}, function(response) {
							getMaintranceServices($foreign_id);
				        }).fail(function(xhr) {
					        console.error("Error:", xhr.responseText);
					    });
					}
				});
		    }).on('click', '.btn-delete-attribute', function(e) {
		    	if (e && e.preventDefault) {
					e.preventDefault();
				}
		    	var $this = $(this),
		    		$id = $(this).attr('data-id');
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
						$.post("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionDeleteAttribute", {id: $id}, function(response) {
							$this.closest('.custom-field-row').remove();
				        }).fail(function(xhr) {
					        console.error("Error:", xhr.responseText);
					    });
					}
				});
		    });
		    
		    $('#modalAddAccident').on('shown.bs.modal', function (e) {
		    	$('.datepick').datepicker({
	                autoclose: true
	            });
		    	
		    	$('.clockpicker').clockpicker({
	                twelvehour: myLabel.showperiod,
	                placement: 'bottom',
	                align: 'left',
	                container: '#modalAddAccident',
	                autoclose: true
	            });
		    });
		    
		    $('#modalAddService').on('shown.bs.modal', function (e) {
		    	$('.datepick').datepicker({
	                autoclose: true
	            });
		    });
		    
		    function getFiles($foreign_id, $type) {
		    	$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGetFiles&foreign_id=" + $foreign_id + "&type=" + $type, function(data) {
		    		var $obj = $('#'+$type);
		    		$obj.html(data);
		    		
		    		//initGallery($obj);
				});
		    }

		    function getMaintranceAccidents($foreign_id) {
		    	$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGetAccidents&foreign_id=" + $foreign_id, function(data) {
		    		$('#vehicleAccidents').html(data);
				});
		    }

		    function getMaintranceServices($foreign_id) {
		    	$.get("index.php?controller=pjAdminVehiclesMaintrance&action=pjActionGetServices&foreign_id=" + $foreign_id, function(data) {
		    		$('#vehicleServices').html(data);
				});
		    }
		    
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
	        	$('.datepick').datepicker({
	                autoclose: true
	            });
	        };
	        
	        if ($frmCreate.length > 0 || $frmUpdate.length > 0) {
	        	var uploader = new plupload.Uploader({
	        		browse_button: 'hidden-browse-button',
					multi_selection: true,
					url: "index.php?controller=pjAdminVehiclesMaintrance&action=pjActionUploadFiles"
				});
				
				uploader.init();
				
				$(document).on('click', '.btn-upload', function(e) {
				    e.preventDefault();
				    currentUploadType = $(this).attr('data-type'); 
				    currentUploadForeignID = $(this).attr('data-foreign_id'); 
				    $('#hidden-browse-button').click();
				});
				
				uploader.bind('FilesAdded', function(up, files){
					uploader.start();
				});
				
				uploader.bind('BeforeUpload', function (up, file) {
				    up.setOption('multipart_params', {
				        'upload_type': currentUploadType,
				        'foreign_id': currentUploadForeignID 
				    });
				});
				
				uploader.bind('FileUploaded', function(up, file, data) {
					if (data.response == 'OK'){
						getFiles(currentUploadForeignID, currentUploadType);
					}
				});
	        }
	        
	        if ($frmCreate.length > 0 && validate) {
				$frmCreate.validate({
					onkeyup: false
				});
			}
			
			if ($frmUpdate.length > 0) {
				$frmUpdate.validate({
					onkeyup: false
				});
				
				var $foreign_id = $frmUpdate.find('input[name="id"]').val();
				getFiles($foreign_id, 'vehicle_photos');
				getFiles($foreign_id, 'vehicle_documents');
				getMaintranceAccidents($foreign_id);
				getMaintranceServices($foreign_id);
			}
		});
	});
})(jQuery);