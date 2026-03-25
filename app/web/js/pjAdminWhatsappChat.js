var jQuery = jQuery || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var validate = ($.fn.validate !== undefined),
			PUSHER_KEY = myLabel.pusher_key,
			PUSHER_CLUSTER = myLabel.pusher_cluster,
	    	pusher = new Pusher(PUSHER_KEY, {
		        cluster: PUSHER_CLUSTER,
		        forceTLS: true
		    }),
	    	currentOffset = 0,
	    	isLoading = false,
	    	allLoaded = false,
	    	limit = 20,
	    	currentDriverId = null,
	    	currentPhone = null,
	    	currentProvider = null,
	    	channel;
		
		$(document).ready(function() {
			currentProvider = $('#provider-select').val();
			initPusher(currentProvider);
			loadTemplates();
			
			function filterDrivers() {console.log(333)
		        let searchTerm = $('#search-driver').val().toLowerCase();
		        let selectedType = $('input[name="filter-type"]:checked').val();

		        $('.driver-item').each(function() {
		            let name = $(this).find('strong').text().toLowerCase();
		            let phone = $(this).data('phone').toString();
		            let type = $(this).data('type'); // Giả sử bạn render data-type="owner/partner"

		            let matchesSearch = name.includes(searchTerm) || phone.includes(searchTerm);
		            let matchesType = (selectedType === 'all') || (type === selectedType);

		            if (matchesSearch && matchesType) {
		                $(this).show();
		            } else {
		                $(this).hide();
		            }
		        });
		    }

		    $('#search-driver').on('input', filterDrivers);
		    $('input[name="filter-type"]').on('change', filterDrivers);
		});
		
		function loadDrivers() {
	        $.get('index.php?controller=pjAdminWhatsappChat&action=pjActionGetDrivers', { provider_id: currentProvider }, function(html) {
	            $('.driver-list').html(html);
	            //if (currentDriverId) $(`.driver-item[data-id="${currentDriverId}"]`).addClass('active');
	        });
	    }
		
		// 2. Lắng nghe Real-time theo Channel Provider
	    function initPusher(pId) {
	        if (channel) pusher.unsubscribe('chat-' + currentProvider);
	        currentProvider = pId;
	        channel = pusher.subscribe('chat-' + currentProvider);

	        channel.bind('new-message', function(data) {
	            let item = $(`.driver-item[data-id="${data.driver_id}"]`);
	            
	            // ĐẨY LÊN ĐẦU: Di chuyển element lên đầu danh sách
	            item.prependTo('.driver-list'); 

	            if (currentDriverId == data.driver_id) {
	                // Đang mở chat -> Hiện tin nhắn + Reset DB đã đọc
	            	appendMessage(data.content, 'received', data.time);
	                $.post('index.php?controller=pjAdminWhatsappChat&action=pjActionMarkAsRead', {mark_as_read: 1, driver_id: data.driver_id, provider_id: currentProvider });
	            } else {
	                // Không mở chat -> Hiện/Tăng số Badge
	                let badge = item.find('.unread-badge');
	                let count = parseInt(badge.text() || 0) + 1;
	                badge.text(count).removeClass('d-none');
	            }
	        });
	    }
		
		$(document).on("click", ".driver-item", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			currentPhone = $(this).data('phone').toString();
			currentDriverId = $(this).data('id');
			$('#current-name').text($(this).data('name'));
	        $('.driver-item').removeClass('active');
	        $(this).addClass('active').find('.unread-badge').addClass('d-none').text('0');

	     // Reset và Load lịch sử
		    allLoaded = false;
		    isLoading = false;
		    currentOffset = 0;
		    $('#chat-window').empty();
		    loadHistory(false);
	        
	        // Đánh dấu đã đọc
	        $.post('index.php?controller=pjAdminWhatsappChat&action=pjActionMarkAsRead', {mark_as_read: 1, driver_id: currentDriverId, provider_id: currentProvider });
		}).on("click", "#btn-send", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			let msg = $('#msg-input').val();
			var $template = $("#tpl-select").val();
		    $.post('index.php?controller=pjAdminWhatsappChat&action=pjActionSend', { phone: currentPhone, message: msg, provider_id: currentProvider, driver_id: currentDriverId, template: $template}, function(data) {
		        //appendMessage(msg, 'sent', 'Just now');
		    	appendMessage(data.message, 'sent', new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
		        $('#msg-input').val('');
		        $("#tpl-select").val('');
		        scrollBottom();
		    });
		}).on("change", "#tpl-select", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			let body = $(this).find(':selected').data('body');
            if(body) {
                // Thay thế các biến {{name}}, {{time}}...
                /*let finalContent = body.replace(/{{(\w+)}}/g, function(match, key) {
                    return bookingData[key] || match; 
                });*/

                // Đổ vào textarea
                $('#msg-input').val(body);
                
                // Tự động focus và cuộn textarea lên đầu nội dung
                $('#msg-input').focus();
            } else {
                $('#msg-input').val('');
            }
		}).on("change", "#provider-select", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			currentProvider = $(this).val();
			initPusher(currentProvider);
	        currentDriverId = null;
	        
			// Reset và Load lịch sử
			
		    allLoaded = false;
		    isLoading = false;
		    currentOffset = 0;
		    $('#chat-window').empty();
		    loadDrivers();
		    loadTemplates();
		    $('#msg-input').val('')
		});
		
		function scrollBottom() {
	        $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
	    }
		
		function appendMessage(txt, type, time) {
	        let msgHtml = `
	            <div class="msg ${type}">
	                ${txt.replace(/\n/g, '<br>')}
	                <div style="font-size:9px; opacity:0.5; text-align:right; margin-top:5px;">${time}</div>
	            </div>`;
	        $('#chat-window').append(msgHtml);
	    }
		
		function loadTemplates() {
			$('#tpl-select').empty();
			$('#tpl-select').append(`<option value="" data-body="">${myLabel.select_template}</option>`);
	        $.getJSON('index.php?controller=pjAdminWhatsappChat&action=pjActionGetTemplates&provider_id=' + currentProvider, function(data) {
	            data.forEach(t => {
	                $('#tpl-select').append(`<option value="${t.value}" data-body="${t.body}">${t.name}</option>`);
	            });
	        });
	    }
		
		function loadHistory(isLoadMore = false) {
		    if (isLoading || allLoaded) return;
		    isLoading = true;
		    $('#chat-loading').fadeIn(200);
		    $.getJSON('index.php?controller=pjAdminWhatsappChat&action=pjActionGetHistory', {provider_id: currentProvider, phone: currentPhone, offset: currentOffset, limit: limit }, function(data) {
		        if (data.length < limit) {
		        	allLoaded = true;
		        	isLoading = false;
		        }
		        if (data.length > 0) {
		            let oldHeight = $('#chat-window')[0].scrollHeight;
		            data.forEach(msg => {
		                let html = `<div class="msg ${msg.direction}">${msg.content}<div class="time">${msg.time}</div></div>`;
		                $('#chat-window').prepend(html); // Prepend để đưa tin cũ lên trên
		            });
		            currentOffset += limit;
		            if (!isLoadMore) scrollBottom();
		            else $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight - oldHeight);
		        }
		        isLoading = false;
		        $('#chat-loading').fadeOut(200);
		    }).fail(function() {
	            $('#chat-loading').fadeOut(200);
	        });
		}
		
		$('#chat-window').on('scroll', function() {
		    if ($(this).scrollTop() === 0 && !allLoaded) loadHistory(true);
		});
		
	});
})(jQuery);