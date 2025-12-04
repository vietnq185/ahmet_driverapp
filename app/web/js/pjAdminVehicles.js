var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreate = $("#frmCreate"),
			datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			multilang = ($.fn.multilang !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			$modalAddServiceRepair = $("#modalAddServiceRepair"),
			$modalUpdateServiceRepair = $("#modalUpdateServiceRepair"),
			validator;
		
		if (datepicker) {
			$.fn.datepicker.dates['en'] = {
	        	days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	        	daysMin: myLabel.days.split("_"),
	        	daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
	        	months: myLabel.months.split("_"),
	        	monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    		}
		}
		
		if (multilang && 'pjCmsLocale' in window) {
			$(".multilang").multilang({
				langs: pjCmsLocale.langs,
				flagPath: pjCmsLocale.flagPath,
				tooltip: "",
				select: function (event, ui) {
					$("input[name='locale_id']").val(ui.index);
				}
			});
		}

		if($(".touchspin3").length > 0)
		{
			$(".touchspin3").TouchSpin({
				min: 0,
				max: 4294967295,
				step: 1,
				verticalbuttons: true,
	            buttondown_class: 'btn btn-white',
	            buttonup_class: 'btn btn-white'
	        });
		}

		if ($frmCreate.length > 0 && validate) {
			$frmCreate.validate({
				rules:{
					"registration_number": {
						required: true,
						remote: "index.php?controller=pjAdminVehicles&action=pjActionCheckRegistrationNumber"
					}
				},
				messages: {
					"registration_number": {
						remote: myLabel.vehicle_same_reg
					}
				},
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					
					$.ajaxSetup({async:false});
					var formData = new FormData(form);
					$.ajax({
			            url: "index.php?controller=pjAdminVehicles&action=pjActionAddVehicle",
			            type: 'post',
			            data: formData,
			            dataType: 'html',
			            async: true,
			            processData: false,
			            contentType: false,
			            success : function(data) {
			            	var content = $grid.datagrid("option", "content");
							$grid.datagrid("load", "index.php?controller=pjAdminVehicles&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
							$(form)[0].reset();
							l.stop();
							if (multilang && myLabel.isFlagReady == 1) {
					        	$('.pj-form-langbar-item').first().trigger('click');
							}
			            },
			            error : function(request) {
			            	l.stop();
			            }
			        });
					return false;
				}
			});
			$('[data-toggle="tooltip"]').tooltip(); 
		}

		if ($("#grid").length > 0 && datagrid) {
			var buttons = [];
			if (pjGrid.hasAccessUpdate) {
				buttons.push({type: "edit", url: "index.php?controller=pjAdminVehicles&action=pjActionUpdate&id={:id}"});
			}
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminVehicles&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminVehicles&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
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
							{text: myLabel.vehicle_name, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.vehicle_reg_num, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.vehicle_seats, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.vehicle_order, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.vehicle_status, type: "toggle", sortable: true, editable: pjGrid.hasAccessUpdate, positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"}
				          ],
				dataUrl: "index.php?controller=pjAdminVehicles&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'registration_number', 'seats', 'order', 'status'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminVehicles&action=pjActionSave&id={:id}",
				select: select,
				onRender: function(){
					$grid.find('.pj-table-icon-edit').each(function() {
						var $this = $(this),
							$tr = $(this).closest('tr'),
							$data_id = $tr.attr('data-id'),
							$arr = $data_id.split('_');
						if ($arr[1] == parseInt(myLabel.selected_vehicle_id, 10)) {
							$this.trigger('click');
							return;
						}
					});
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
			$grid.datagrid("load", "index.php?controller=pjAdminVehicles&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminVehicles&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminVehicles&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".pj-table-icon-edit", function (e) {
			var $url = $(this).attr("href");
			$.get($url).done(function (data) {
				$(".boxFormVehicle").html(data);
				$('[data-toggle="tooltip"]').tooltip(); 
				if (multilang && myLabel.isFlagReady == 1) {
		        	$('.pj-form-langbar-item').first().trigger('click');
				}
				
				var $id = parseInt($("#frmUpdate").find("input[name='id']").val(), 10) || 0;
				getVehicleServices($id);
		        
				$("#frmUpdate").validate({
					rules:{
						"registration_number": {
							required: true,
							remote: "index.php?controller=pjAdminVehicles&action=pjActionCheckRegistrationNumber&id=" + $id
						}
					},
					messages: {
						"registration_number": {
							remote: myLabel.vehicle_same_reg
						}
					},
    				onkeyup: false,
    				submitHandler: function (form) {
    					var l = Ladda.create( $(form).find(":submit").get(0) );
    					l.start();
    					
    					$.ajaxSetup({async:false});
						var formData = new FormData(form);
						$.ajax({
				            url: "index.php?controller=pjAdminVehicles&action=pjActionUpdate",
				            type: 'post',
				            data: formData,
				            dataType: 'html',
				            async: true,
				            processData: false,
				            contentType: false,
				            success : function(data) {
				            	var content = $grid.datagrid("option", "content");
	    						$grid.datagrid("load", "index.php?controller=pjAdminVehicles&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
	    						if(pjGrid.hasAccessCreate == true)
	    						{
	    							$(".pjBtnCancelUpdateVehicle").trigger("click");
	    						}else{
	    							$(".boxFormVehicle").html("");
	    						}
	    						l.stop();
				            },
				            error : function(request) {
				            	l.stop();
				            }
				        });
    					return false;
    				}
    			});
			});
			return false;
		}).on("click", ".pjBtnCancelUpdateVehicle", function (e) {
			if(pjGrid.hasAccessCreate == true)
			{
				var $url = 'index.php?controller=pjAdminVehicles&action=pjActionCreate';
				$.get($url).done(function (data) {
					$(".boxFormVehicle").html(data);
					$('[data-toggle="tooltip"]').tooltip(); 
			        
			        if (multilang && myLabel.isFlagReady == 1) {
			        	$('.pj-form-langbar-item').first().trigger('click');
					}
			        
					$("#frmCreate").validate({
						rules:{
							"registration_number": {
								required: true,
								remote: "index.php?controller=pjAdminVehicles&action=pjActionCheckRegistrationNumber"
							}
						},
						messages: {
							"registration_number": {
								remote: myLabel.vehicle_same_reg
							}
						},
						onkeyup: false,
						submitHandler: function (form) {
							var l = Ladda.create( $(form).find(":submit").get(0) );
							l.start();
							
							$.ajaxSetup({async:false});
							var formData = new FormData(form);
							$.ajax({
					            url: "index.php?controller=pjAdminVehicles&action=pjActionAddVehicle",
					            type: 'post',
					            data: formData,
					            dataType: 'html',
					            async: true,
					            processData: false,
					            contentType: false,
					            success : function(data) {
					            	var content = $grid.datagrid("option", "content");
									$grid.datagrid("load", "index.php?controller=pjAdminVehicles&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
									$(form)[0].reset();
									l.stop();
									if (multilang && myLabel.isFlagReady == 1) {
							        	$('.pj-form-langbar-item').first().trigger('click');
									}
					            },
					            error : function(request) {
					            	l.stop();
					            }
					        });
							return false;
						}
					});
				});
			}else{
				$(".boxFormVehicle").html("");
			}
			return false;
		}).on("click", ".btnAddServiceRepair", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $vehicle_id = $(this).attr('data-id');
			$.get("index.php?controller=pjAdminVehicles&action=pjActionAddService", {
				"vehicle_id": $vehicle_id
			}).done(function (data) {
				$modalAddServiceRepair.find(".modal-content").html(data);
				$modalAddServiceRepair.modal('show');
				validator = $modalAddServiceRepair.find("form").validate();
				
				if (multilang && myLabel.isFlagReady == 1) {
					$(".multilang").multilang({
						langs: pjCmsLocale.langs,
						flagPath: pjCmsLocale.flagPath,
						tooltip: "",
						select: function (event, ui) {
							$("input[name='locale_id']").val(ui.index);					
						}
					});
				}
			});
		}).on("click", ".btnConfirmAddServiceRepair", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalAddServiceRepair.find("form").valid()) {
				var $form = $modalAddServiceRepair.find("form"),
					$vehicle_id = parseInt($form.find('input[name="vehicle_id"]').val(), 10) || 0;
				$.post("index.php?controller=pjAdminVehicles&action=pjActionAddService", $form.serialize()).done(function (data) {
					$modalAddServiceRepair.modal('hide');
					getVehicleServices($vehicle_id)
				});
			}
		}).on("click", ".btnUpdateServiceRepair", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $id = $(this).attr('data-id');
			$.get("index.php?controller=pjAdminVehicles&action=pjActionUpdateService", {
				"id": $id
			}).done(function (data) {
				$modalUpdateServiceRepair.find(".modal-content").html(data);
				$modalUpdateServiceRepair.modal('show');
				validator = $modalUpdateServiceRepair.find("form").validate();
				
				if (multilang && myLabel.isFlagReady == 1) {
					$(".multilang").multilang({
						langs: pjCmsLocale.langs,
						flagPath: pjCmsLocale.flagPath,
						tooltip: "",
						select: function (event, ui) {
							$("input[name='locale_id']").val(ui.index);					
						}
					});
				}
			});
		}).on("click", ".btnConfirmUpdateServiceRepair", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalUpdateServiceRepair.find("form").valid()) {
				var $form = $modalUpdateServiceRepair.find("form"),
					$vehicle_id = parseInt($form.find('input[name="vehicle_id"]').val(), 10) || 0;
				$.post("index.php?controller=pjAdminVehicles&action=pjActionUpdateService", $form.serialize()).done(function (data) {
					$modalUpdateServiceRepair.modal('hide');
					getVehicleServices($vehicle_id)
				});
			}
		}).on("click", ".btnDeleteServiceRepair", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				$id = $this.attr('data-id'),
				$vehicle_id = $this.attr('data-vehicle_id');
			swal({
				title: myLabel.delete_selected,
				text: myLabel.delete_confirmation,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: myLabel.btn_delete,
				cancelButtonText: myLabel.btn_cancel,
				closeOnConfirm: false,
				showLoaderOnConfirm: true
			}, function () {
				$.post("index.php?controller=pjAdminVehicles&action=pjActionDeleteService", {id: $id}).done(function (data) {
					if (!(data && data.status)) {
						
					}
					switch (data.status) {
					case "OK":
						swal.close();
						getVehicleServices($vehicle_id)
						break;
					}
				});
			});
		});
		
		if ($('.date').length > 0 && datepicker) {
			$('.date').datepicker();
		}
		
		function getVehicleServices($vehicle_id) {
			$.get("index.php?controller=pjAdminVehicles&action=pjActionGetServices", {
				"vehicle_id": $vehicle_id
			}).done(function (data) {
				$('.pjSbVehicleServiceRepair').html(data);
			});
		}
		
		$("#modalAddServiceRepair").on('hide.bs.modal', function(){
			$('.ibox-content').removeClass('sk-loading');
		});
		
		$("#modalUpdateServiceRepair").on('hide.bs.modal', function(){
			$('.ibox-content').removeClass('sk-loading');
		});
		
	});
})(jQuery);