var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validator,
			validate = ($.fn.validate !== undefined),
			$modalSms = $("#modalSms"),
			$frmCreate = $("#frmCreate"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined);
		
		if ($(".select-item").length) {
            $(".select-item").select2({
                placeholder: myLabel.choose,
                allowClear: true
            });
        };
        
		if ($frmCreate.length > 0 && validate) {
			$frmCreate.validate({
				rules:{
					"email": {
						required: true,
						email: true,
						remote: "index.php?controller=pjAdminDrivers&action=pjActionCheckEmail"
					}
				},
				messages: {
					"email": {
						remote: myLabel.driver_same_email
					}
				},
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					$.post('index.php?controller=pjBaseUsers&action=pjActionCheckPassword', $(form).serialize()).done(function (data) {
	        			if(data.status == 'OK')
	    				{
	        				$.ajaxSetup({async:false});
	    					var formData = new FormData(form);
	    					$.ajax({
	    			            url: "index.php?controller=pjAdminDrivers&action=pjActionAddDriver",
	    			            type: 'post',
	    			            data: formData,
	    			            dataType: 'html',
	    			            async: true,
	    			            processData: false,
	    			            contentType: false,
	    			            success : function(data) {
	    			            	var content = $grid.datagrid("option", "content");
	    							$grid.datagrid("load", "index.php?controller=pjAdminDrivers&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
	    							$(form)[0].reset();
	    							l.stop();
	    			            },
	    			            error : function(request) {
	    			            	l.stop();
	    			            }
	    			        });
	    				}else{
	    					l.stop();
	    					swal({
	    		                title: myLabel.invalid_password_title,
	    		                type: "warning",
	    		                text: data.text,
	    		                showCancelButton: false,
	    		                confirmButtonColor: "#11511a",
	    		                confirmButtonText: myLabel.btn_ok,
	    		                closeOnConfirm: true,
	    		            });
	    				}
	        		});
					return false;
				}
			});
		}

		if ($("#grid").length > 0 && datagrid) {
			var buttons = [];
			buttons.push({type: "phone", url: "index.php?controller=pjAdminDrivers&action=pjActionSms&driver_id={:id}"});
			if (pjGrid.hasAccessUpdate) {
				buttons.push({type: "edit", url: "index.php?controller=pjAdminDrivers&action=pjActionUpdate&id={:id}"});
			}
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminDrivers&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminDrivers&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
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
							{text: myLabel.driver_name, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.driver_email, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.driver_phone, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.driver_language, type: "text", sortable: true, editable: false},
							{text: myLabel.driver_status, type: "toggle", sortable: true, editable: pjGrid.hasAccessUpdate, positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"}
				          ],
				dataUrl: "index.php?controller=pjAdminDrivers&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'email', 'phone', 'locale', 'status'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminDrivers&action=pjActionSave&id={:id}",
				select: select,
				onRender: function(){
					$grid.find('.pj-table-icon-edit').each(function() {
						var $this = $(this),
							$tr = $(this).closest('tr'),
							$data_id = $tr.attr('data-id'),
							$arr = $data_id.split('_');
						if ($arr[1] == parseInt(myLabel.selected_driver_id, 10)) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminDrivers&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminDrivers&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminDrivers&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".pj-table-icon-edit", function (e) {
			var $url = $(this).attr("href");
			$.get($url).done(function (data) {
				$(".boxFormDriver").html(data); 
				if ($(".select-item").length) {
		            $(".select-item").select2({
		                placeholder: myLabel.choose,
		                allowClear: true
		            });
		        };
				$("#frmUpdate").validate({
					rules:{
						"email": {
							required: true,
							email: true,
							remote: "index.php?controller=pjAdminDrivers&action=pjActionCheckEmail&id=" + $("#frmUpdate").find("input[name='id']").val()
						}
					},
					messages: {
						"email": {
							remote: myLabel.driver_same_email
						}
					},
    				onkeyup: false,
    				submitHandler: function (form) {
    					var l = Ladda.create( $(form).find(":submit").get(0) );
    					l.start();
    					$.post('index.php?controller=pjBaseUsers&action=pjActionCheckPassword', $(form).serialize()).done(function (data) {
    	        			if(data.status == 'OK')
    	    				{
    	        				$.ajaxSetup({async:false});
    							var formData = new FormData(form);
    							$.ajax({
    					            url: "index.php?controller=pjAdminDrivers&action=pjActionUpdate",
    					            type: 'post',
    					            data: formData,
    					            dataType: 'html',
    					            async: true,
    					            processData: false,
    					            contentType: false,
    					            success : function(data) {
    					            	var content = $grid.datagrid("option", "content");
    		    						$grid.datagrid("load", "index.php?controller=pjAdminDrivers&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
    		    						if(pjGrid.hasAccessCreate == true)
    		    						{
    		    							$(".pjBtnCancelUpdateDriver").trigger("click");
    		    						}else{
    		    							$(".boxFormDriver").html("");
    		    						}
    		    						l.stop();
    					            },
    					            error : function(request) {
    					            	l.stop();
    					            }
    					        });
    	    				}else{
    	    					l.stop();
    	    					swal({
    	    		                title: myLabel.invalid_password_title,
    	    		                type: "warning",
    	    		                text: data.text,
    	    		                showCancelButton: false,
    	    		                confirmButtonColor: "#11511a",
    	    		                confirmButtonText: myLabel.btn_ok,
    	    		                closeOnConfirm: true,
    	    		            });
    	    				}
    	        		});
    					return false;
    				}
    			});
			});
			return false;
		}).on("click", ".pjBtnCancelUpdateDriver", function (e) {
			if(pjGrid.hasAccessCreate == true)
			{
				var $url = 'index.php?controller=pjAdminDrivers&action=pjActionCreate';
				$.get($url).done(function (data) {
					$(".boxFormDriver").html(data); 
					if ($(".select-item").length) {
			            $(".select-item").select2({
			                placeholder: myLabel.choose,
			                allowClear: true
			            });
			        };
					$("#frmCreate").validate({
						rules:{
							"email": {
								required: true,
								email: true,
								remote: "index.php?controller=pjAdminDrivers&action=pjActionCheckEmail"
							}
						},
						messages: {
							"email": {
								remote: myLabel.driver_same_email
							}
						},
						onkeyup: false,
						submitHandler: function (form) {
							var l = Ladda.create( $(form).find(":submit").get(0) );
							l.start();
							
							$.ajaxSetup({async:false});
							var formData = new FormData(form);
							$.ajax({
					            url: "index.php?controller=pjAdminDrivers&action=pjActionAddDriver",
					            type: 'post',
					            data: formData,
					            dataType: 'html',
					            async: true,
					            processData: false,
					            contentType: false,
					            success : function(data) {
					            	var content = $grid.datagrid("option", "content");
									$grid.datagrid("load", "index.php?controller=pjAdminDrivers&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
									$(form)[0].reset();
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
			}else{
				$(".boxFormDriver").html("");
			}
			return false;
		}).on("click", ".pj-table-icon-phone", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $href = $(this).attr('href');
			$.get($href).done(function (data) {
				$modalSms.find(".modal-content").html(data);
				$modalSms.modal('show');
				validator = $modalSms.find("form").validate();
			});
			return false;
		}).on("click", ".btnSendSms", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			if ($modalSms.find("form").valid()) {
				$.post("index.php?controller=pjAdminDrivers&action=pjActionSms", $modalSms.find("form").serialize()).done(function (data) {
					$modalSms.modal('hide');
				});
			}
		});
	});
})(jQuery);