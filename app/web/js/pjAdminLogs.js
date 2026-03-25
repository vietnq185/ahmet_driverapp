var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			datepicker = ($.fn.datepicker !== undefined);
		
		if (datepicker) {
			$.fn.datepicker.dates['en'] = {
	        	days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	        	daysMin: myLabel.days.split("_"),
	        	daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
	        	months: myLabel.months.split("_"),
	        	monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    		}
			
			$('.date', $('.frm-filter')).datepicker().on('changeDate', function(e) {
	        	if (e && e.preventDefault) {
					e.preventDefault();
				}

				$(".frm-filter").trigger("submit");
	        });
		}
		
		if ($("#grid").length > 0 && datagrid) {
			var buttons = [];
			if (pjGrid.hasAccessDeleteSingle) {
				buttons.push({type: "delete", url: "index.php?controller=pjAdminLogs&action=pjActionDelete&id={:id}"});
			}
			var actions = [];
			if (pjGrid.hasAccessDeleteMulti) {
				actions.push({text: myLabel.delete_selected, url: "index.php?controller=pjAdminLogs&action=pjActionDeleteBulk", render: true, confirmation: myLabel.delete_confirmation});
			}
			var select = false;
			if (actions.length) {
				select = {
					field: "id",
					name: "record[]"
				};
			}
			
			function formatCreatedBy(str, obj) {
				return obj.created_by;
			}
			function formatCreated(str, obj) {
				return obj.created;
			}
			function formatDate(str, obj) {
				return obj.date;
			}
			var $grid = $("#grid").datagrid({
				buttons: buttons,
		          columns: [
							{text: myLabel.log_content, type: "text", sortable: true, editable: false},
							{text: myLabel.log_by, type: "text", sortable: true, editable: false, renderer: formatCreatedBy},
							{text: myLabel.log_created, type: "text", sortable: true, editable: false, renderer: formatDate}
				          ],
				dataUrl: "index.php?controller=pjAdminLogs&action=pjActionGet" + pjGrid.queryString,
				dataType: "json",
				fields: ['action', 'user_id', 'created'],
				paginator: {
					actions: actions,
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminLogs&action=pjActionSave&id={:id}",
				select: select
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
				date: $this.find("input[name='date']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminLogs&action=pjActionGet", content.column, content.direction, content.page, content.rowCount);
			return false;
		});
		
	});
})(jQuery);