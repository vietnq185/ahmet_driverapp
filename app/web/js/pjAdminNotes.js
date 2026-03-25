var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreate = $("#frmCreate"),
			datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			multilang = ($.fn.multilang !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
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

		if ($frmCreate.length > 0 && validate) {
			$frmCreate.validate({
				onkeyup: false,
				submitHandler: function (form) {
					var l = Ladda.create( $(form).find(":submit").get(0) );
					l.start();
					
					$.ajaxSetup({async:false});
					var formData = new FormData(form);
					$.ajax({
			            url: "index.php?controller=pjAdminNotes&action=pjActionAdd",
			            type: 'post',
			            data: formData,
			            dataType: 'html',
			            async: true,
			            processData: false,
			            contentType: false,
			            success : function(data) {
			            	var content = $grid.datagrid("option", "content");
							$grid.datagrid("load", "index.php?controller=pjAdminNotes&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
							$(form)[0].reset();
							l.stop();
							$('#vehicle_id').val(null).trigger('change');
			            },
			            error : function(request) {
			            	l.stop();
			            }
			        });
					return false;
				}
			});
		}

		if ($("#grid").length > 0 && datagrid) {
			var buttons = [];
			if (pjGrid.hasAccessUpdate) {
				buttons.push({type: "edit", url: "index.php?controller=pjAdminNotes&action=pjActionUpdate&id={:id}"});
			}
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminNotes&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminNotes&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
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
							{text: myLabel.note_vehicle_name, type: "text", sortable: true, editable: false},
							{text: myLabel.shift, type: "text", sortable: true, editable: false},
							{text: myLabel.note_date, type: "text", sortable: true, editable: false},
							{text: myLabel.note_notes, type: "text", sortable: true, editable: pjGrid.hasAccessUpdate},
							{text: myLabel.note_status, type: "toggle", sortable: true, editable: pjGrid.hasAccessUpdate, positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"}
				          ],
				dataUrl: "index.php?controller=pjAdminNotes&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['vehicle_name', 'vehicle_order', 'date', 'notes', 'status'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminNotes&action=pjActionSave&id={:id}",
				select: select,
				onRender: function(){
					$grid.find('.pj-table-icon-edit').each(function() {
						var $this = $(this),
							$tr = $(this).closest('tr'),
							$data_id = $tr.attr('data-id'),
							$arr = $data_id.split('_');
						if ($arr[1] == parseInt(myLabel.selected_note_id, 10)) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminNotes&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminNotes&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminNotes&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".pj-table-icon-edit", function (e) {
			var $url = $(this).attr("href");
			$.get($url).done(function (data) {
				$(".boxFormNote").html(data);
				if ($(".select-item").length) {
		            $(".select-item").select2({
		                placeholder: myLabel.choose,
		                allowClear: true
		            });
		        }
				$("#frmUpdate").validate({
					onkeyup: false,
    				submitHandler: function (form) {
    					var l = Ladda.create( $(form).find(":submit").get(0) );
    					l.start();
    					
    					$.ajaxSetup({async:false});
						var formData = new FormData(form);
						$.ajax({
				            url: "index.php?controller=pjAdminNotes&action=pjActionUpdate",
				            type: 'post',
				            data: formData,
				            dataType: 'html',
				            async: true,
				            processData: false,
				            contentType: false,
				            success : function(data) {
				            	var content = $grid.datagrid("option", "content");
	    						$grid.datagrid("load", "index.php?controller=pjAdminNotes&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
	    						if(pjGrid.hasAccessCreate == true)
	    						{
	    							$(".pjBtnCancelUpdateNote").trigger("click");
	    						}else{
	    							$(".boxFormNote").html("");
	    						}
	    						l.stop();
	    						$('#vehicle_id').val(null).trigger('change');
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
		}).on("click", ".pjBtnCancelUpdateNote", function (e) {
			if(pjGrid.hasAccessCreate == true)
			{
				var $url = 'index.php?controller=pjAdminNotes&action=pjActionCreate';
				$.get($url).done(function (data) {
					$(".boxFormNote").html(data);
					if ($(".select-item").length) {
			            $(".select-item").select2({
			                placeholder: myLabel.choose,
			                allowClear: true
			            });
			        }
					$("#frmCreate").validate({
						onkeyup: false,
						submitHandler: function (form) {
							var l = Ladda.create( $(form).find(":submit").get(0) );
							l.start();
							
							$.ajaxSetup({async:false});
							var formData = new FormData(form);
							$.ajax({
					            url: "index.php?controller=pjAdminNotes&action=pjActionAdd",
					            type: 'post',
					            data: formData,
					            dataType: 'html',
					            async: true,
					            processData: false,
					            contentType: false,
					            success : function(data) {
					            	var content = $grid.datagrid("option", "content");
									$grid.datagrid("load", "index.php?controller=pjAdminNotes&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
									$(form)[0].reset();
									$('#vehicle_id').val(null).trigger('change');
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
				$(".boxFormNote").html("");
			}
			return false;
		});
		
		if ($('.date').length > 0 && datepicker) {
			$('.date').datepicker();
		}
		
		if ($(".select-item").length) {
            $(".select-item").select2({
                placeholder: myLabel.choose,
                allowClear: true
            });
        }
		
	});
})(jQuery);