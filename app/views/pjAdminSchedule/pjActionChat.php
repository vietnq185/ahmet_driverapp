<?php 
$titles = __('error_titles', true);
$bodies = __('error_bodies', true);
$get = $controller->_get->raw();
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::momentJsDateFormat($tpl['option_arr']['o_date_format']);
$months = __('months', true);
ksort($months);
$short_days = __('short_days', true);
$show_period = 'false';
if((strpos($tpl['option_arr']['o_time_format'], 'a') > -1 || strpos($tpl['option_arr']['o_time_format'], 'A') > -1))
{
    $show_period = 'true';
}
?>
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-sm-12">
			<div class="row">
				<div class="col-sm-12">
					<h2>
						Alpstria WhatsApp Chat
					</h2>
				</div>
			</div>
			<p class="m-b-none">
				<i class="fa fa-info-circle"></i>
				Alpstria WhatsApp Chat
			</p>
		</div>
	</div>
	
	<div class="row wrapper wrapper-content animated fadeInRight">
		<div class="col-lg-12">
			<style>
        :root { --alpstria-green: #1a4d2e; }
        .whatsapp-wrapper { display: flex; height: 80vh; background: #fff; border: 1px solid #ddd; margin-top: 10px; }
        .sidebar { width: 30%; border-right: 1px solid #ddd; display: flex; flex-direction: column; }
        .driver-list { overflow-y: auto; flex: 1; }
        .driver-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
        .driver-item.active { background: #e9edef; border-left: 5px solid var(--alpstria-green); }
        .chat-main { width: 70%; display: flex; flex-direction: column; background: #e5ddd5; }
        #chat-window { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; }
        .message { margin-bottom: 10px; padding: 10px; border-radius: 8px; max-width: 70%; }
        .sent { align-self: flex-end; background: #dcf8c6; }
        .received { align-self: flex-start; background: #fff; }
        .chat-footer { padding: 15px; background: #f0f0f0; }

#chat-window {
    height: 500px;
    overflow-y: auto;
    padding: 15px;
    background-color: #e5ddd5; /* Màu nền WhatsApp */
    display: flex;
    flex-direction: column;
}
.msg {
    margin-bottom: 10px;
    padding: 10px 15px;
    border-radius: 10px;
    max-width: 80%;
    position: relative;
    line-height: 1.5;
}
.sent { align-self: flex-end; background-color: #dcf8c6; border-bottom-right-radius: 2px; }
.received { align-self: flex-start; background-color: #ffffff; border-bottom-left-radius: 2px; }
    </style>
    <div class="container-fluid">
    <div class="whatsapp-wrapper">
        <div class="sidebar">
            <div class="sidebar-header" style="padding:15px; background:#ededed;"><b>DRIVERS</b></div>
            <div class="driver-list">
                <div class="driver-item" data-phone="84123456789" data-name="Driver A">
                    <strong>Driver A</strong><br><small>+84 123 456 789</small>
                </div>
                <div class="driver-item" data-phone="84901234666" data-name="Driver B">
                    <strong>Driver B</strong><br><small>+84 901 234 666</small>
                </div>
                <div class="driver-item" data-phone="84901234777" data-name="Driver C">
                    <strong>Driver C</strong><br><small>+84 901 234 777</small>
                </div>
            </div>
        </div>

        <div class="chat-main">
            <div class="chat-header" style="padding:10px; background:#ededed;">
                Chat: <span id="current-name">Select a driver</span>
            </div>
            <div id="chat-window"></div>
            <div class="chat-footer">
                <select id="tpl-select" class="form-control" style="margin-bottom:10px;">
                    <option value="">-- Select Template --</option>
                </select>
                <div class="input-group">
                    <textarea id="msg-input" class="form-control" rows="2" placeholder="Type message..."></textarea>
                    <span class="input-group-btn">
                        <button id="btn-send" class="btn btn-success" style="height:54px; background:var(--alpstria-green);">SEND</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

		</div><!-- /.col-lg-8 -->
	</div>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
	<script>
    console.log("555 - JavaScript Ready");

    let currentPhone = '';
    
    // Dữ liệu giả định để thay thế vào Template
    let bookingData = {
        driver_name: "Driver A",
        pickup_time: "15:45",
        car_plate: "AL-9988",
        customer_name: "Mr. Patrick"
    };

    // DỮ LIỆU HISTORY GIẢ ĐỊNH (Thay thế việc gọi database tạm thời)
    const mockHistory = {
    	    // Driver A: Taner Vural
    	    "84123456789": [
    	        { type: 'received', content: 'Hello, I have received the schedule for tomorrow.', time: '08:00 AM' },
    	        { type: 'sent', content: 'Good morning Taner. Please make sure to check the vehicle condition before picking up the guests.', time: '08:05 AM' },
    	        { type: 'received', content: 'Understood. The car has been cleaned and the fuel tank is full.', time: '08:10 AM' },
    	        { type: 'sent', content: 'Perfect. This is a VIP client, so please be on time.', time: '09:00 AM' },
    	        { type: 'received', content: 'I am on my way to the pickup point now.', time: '02:20 PM' }
    	    ],

    	    // Driver B: Marco Rossi
    	    "84901234666": [
    	        { type: 'sent', content: 'Marco, there is a change in the pickup time for booking #AS102.', time: '10:30 AM' },
    	        { type: 'received', content: 'Received. What is the new time?', time: '10:35 AM' },
    	        { type: 'sent', content: 'The client requested to move it to 11:45 AM.', time: '10:40 AM' },
    	        { type: 'received', content: 'Copy that. I will adjust my route and arrive early.', time: '10:45 AM' }
    	    ],

    	    // Driver C: Hans Müller
    	    "84901234777": [
    	        { type: 'received', content: 'I have successfully dropped off the guests at Innsbruck Airport.', time: '01:15 PM' },
    	        { type: 'sent', content: 'Great job Hans. Did you find any items left behind in the car?', time: '01:20 PM' },
    	        { type: 'received', content: 'No, the car is empty. I am ready for the next assignment.', time: '01:25 PM' },
    	        { type: 'sent', content: 'Okay, wait for a moment. I am assigning a new trip to Selva di Val Gardena for you.', time: '01:30 PM' }
    	    ]
    	};

    $(document).ready(function() {
        loadTemplates();

        // 1. Khi chọn Driver -> Load history giả định
        $(document).on('click', '.driver-item', function() {
            $('.driver-item').removeClass('active');
            $(this).addClass('active');
            
            currentPhone = $(this).data('phone').toString();
            $('#current-name').text($(this).data('name'));
            
            // Hiển thị history giả định
            displayMockHistory(currentPhone);
        });

        // 2. FIXED: Chọn Template -> Hiện nội dung ngay lập tức
        $('#tpl-select').on('change', function() {
            // Lấy content từ data-body của option được chọn
            let body = $(this).find(':selected').data('body');
            
            if(body) {
                console.log("Template Body thô:", body);
                
                // Thay thế các biến {{name}}, {{time}}...
                let finalContent = body.replace(/{{(\w+)}}/g, function(match, key) {
                    return bookingData[key] || match; 
                });

                // Đổ vào textarea
                $('#msg-input').val(finalContent);
                
                // Tự động focus và cuộn textarea lên đầu nội dung
                $('#msg-input').focus();
            } else {
                $('#msg-input').val('');
            }
        });

        // 3. Hàm hiển thị history
        function displayMockHistory(phone) {
            $('#chat-window').empty();
            let messages = mockHistory[phone] || [];
            
            if(messages.length === 0) {
                $('#chat-window').append('<div class="text-center text-muted">No message history yet.</div>');
            } else {
                messages.forEach(msg => {
                    appendMessage(msg.content, msg.type, msg.time);
                });
            }
            scrollBottom();
        }

        // 4. Gửi tin nhắn
        $('#btn-send').on('click', function() {
            let msg = $('#msg-input').val();
            if(!currentPhone || !msg) return;

            appendMessage(msg, 'sent', new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
            $('#msg-input').val('');
            scrollBottom();
            
            // Phần $.post gửi lên server giữ nguyên
        });
    });

    function appendMessage(txt, type, time) {
        let msgHtml = `
            <div class="msg ${type}">
                ${txt.replace(/\n/g, '<br>')}
                <div style="font-size:9px; opacity:0.5; text-align:right; margin-top:5px;">${time}</div>
            </div>`;
        $('#chat-window').append(msgHtml);
    }

    function scrollBottom() {
        $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
    }

    // Đảm bảo get_templates.php trả về đúng data-body
    function loadTemplates() {
        $.getJSON('get_templates.php', function(data) {
            data.forEach(t => {
                $('#tpl-select').append(`<option value="${t.name}" data-body="${t.body}">${t.name}</option>`);
            });
        });
    }
</script>