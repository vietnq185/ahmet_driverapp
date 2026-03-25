var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreate = $("#frmCreate"),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			validator;
		
function myTinyMceDestroy() {
			
			if (window.tinymce === undefined) {
				return;
			}
			
			var iCnt = tinymce.editors.length;
			
			if (!iCnt) {
				return;
			}
			
			for (var i = 0; i < iCnt; i++) {
				tinymce.remove(tinymce.editors[i]);
			}
		}
		
		function myTinyMceInit(pSelector) {
			
			if (window.tinymce === undefined) {
				return;
			}
			
			tinymce.init({
				relative_urls : false,
				remove_script_host : false,
				convert_urls : true,
				browser_spellcheck : true,
			    contextmenu: false,
			    selector: pSelector,
			    theme: "modern",
			    height: 480,
			    plugins: [
			         "advlist autolink link image lists charmap print preview hr anchor pagebreak",
			         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			         "save table contextmenu directionality emoticons template paste textcolor"
			    ],
			    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
			    image_advtab: true,
			    menubar: "file edit insert view table tools",
			    setup: function (editor) {
			    	editor.on('change', function (e) {
			    		editor.editorManager.triggerSave();
			    	});
			    }
			});
		}
		
		if ($('.mceEditor').length > 0) {
			myTinyMceDestroy.call(null);
			myTinyMceInit.call(null, 'textarea.mceEditor');
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
			            url: "index.php?controller=pjAdminContractTheme&action=pjActionAdd",
			            type: 'post',
			            data: formData,
			            dataType: 'html',
			            async: true,
			            processData: false,
			            contentType: false,
			            success : function(data) {
			            	var content = $grid.datagrid("option", "content");
							$grid.datagrid("load", "index.php?controller=pjAdminContractTheme&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
		}

		if ($("#grid").length > 0 && datagrid) {
			$.datagrid.messages.choose_action = myLabel.action;
			
			var buttons = [];
			if (pjGrid.hasAccessUpdate) {
				buttons.push({type: "edit", url: "index.php?controller=pjAdminContractTheme&action=pjActionUpdate&id={:id}"});
			}
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminContractTheme&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminContractTheme&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
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
							{text: myLabel.status, type: "toggle", sortable: true, editable: pjGrid.hasAccessUpdate, positiveLabel: myLabel.active, positiveValue: "T", negativeLabel: myLabel.inactive, negativeValue: "F"}
				          ],
				dataUrl: "index.php?controller=pjAdminContractTheme&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['name', 'status'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminContractTheme&action=pjActionSave&id={:id}",
				select: select,
				onRender: function(){
					$grid.find('.pj-table-icon-edit').each(function() {
						var $this = $(this),
							$tr = $(this).closest('tr'),
							$data_id = $tr.attr('data-id'),
							$arr = $data_id.split('_');
						if ($arr[1] == parseInt(myLabel.selected_id, 10)) {
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
			$grid.datagrid("load", "index.php?controller=pjAdminContractTheme&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminContractTheme&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminContractTheme&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		}).on("click", ".pj-table-icon-edit", function (e) {
			var $url = $(this).attr("href");
			$.get($url).done(function (data) {
				$(".boxFormWrapper").html(data);
				if ($('.mceEditor').length > 0) {
					myTinyMceDestroy.call(null);
					myTinyMceInit.call(null, 'textarea.mceEditor');
		        }
				$("#frmUpdate").validate({
					onkeyup: false,
    				submitHandler: function (form) {
    					var l = Ladda.create( $(form).find(":submit").get(0) );
    					l.start();
    					
    					$.ajaxSetup({async:false});
						var formData = new FormData(form);
						$.ajax({
				            url: "index.php?controller=pjAdminContractTheme&action=pjActionUpdate",
				            type: 'post',
				            data: formData,
				            dataType: 'html',
				            async: true,
				            processData: false,
				            contentType: false,
				            success : function(data) {
				            	var content = $grid.datagrid("option", "content");
	    						$grid.datagrid("load", "index.php?controller=pjAdminContractTheme&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
	    						if(pjGrid.hasAccessCreate == true)
	    						{
	    							$(".pjBtnCancelUpdate").trigger("click");
	    						}else{
	    							$(".boxFormWrapper").html("");
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
		}).on("click", ".pjBtnCancelUpdate", function (e) {
			if(pjGrid.hasAccessCreate == true)
			{
				var $url = 'index.php?controller=pjAdminContractTheme&action=pjActionCreate';
				$.get($url).done(function (data) {
					$(".boxFormWrapper").html(data);
					if ($('.mceEditor').length > 0) {
						myTinyMceDestroy.call(null);
						myTinyMceInit.call(null, 'textarea.mceEditor');
			        }
					$("#frmCreate").validate({
						onkeyup: false,
						submitHandler: function (form) {
							var l = Ladda.create( $(form).find(":submit").get(0) );
							l.start();
							
							$.ajaxSetup({async:false});
							var formData = new FormData(form);
							$.ajax({
					            url: "index.php?controller=pjAdminContractTheme&action=pjActionAdd",
					            type: 'post',
					            data: formData,
					            dataType: 'html',
					            async: true,
					            processData: false,
					            contentType: false,
					            success : function(data) {
					            	var content = $grid.datagrid("option", "content");
									$grid.datagrid("load", "index.php?controller=pjAdminContractTheme&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
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
				$(".boxFormWrapper").html("");
			}
			return false;
		});
	});
})(jQuery);