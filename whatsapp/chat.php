<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Alpstria WhatsApp Chat</title>
    <link rel="stylesheet" href="http://localhost/Ahmet/driverapp/third-party/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/Ahmet/driverapp/third-party/font_awesome/4.7.0/css/font-awesome.min.css">
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
</head>
<body>
<?php 
/* $accessToken = 'EAAMuA2p8b1cBQsFdgjirBXKV6ZAZBSCVliv43pcmxd5LZBiuRSUrSFIzaLCorI2GWcZBye82ZCdwCDEzfKZBKtZBoCUQaQG5xHxFDYVDZAGz949agfr0TX1FjPL1wJyky1b5hdIvrdoSFUZB0HsfVuopSvvzuRp0rYyAVj3cR2zkTl8Hd7NRT3ygvciZCWhoNKdPlz5gZDZD';
$phoneNumberId = '926885857180507';
$url = "https://graph.facebook.com/v18.0/$phoneNumberId";

$ch = curl_init($url);
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);
echo "<pre>";
print_r($result);
echo "</pre>"; */
?>
<div class="container-fluid">
    <div class="whatsapp-wrapper">
        <div class="sidebar">
            <div class="sidebar-header" style="padding:15px; background:#ededed;"><b>DRIVERS</b></div>
            <div class="driver-list">
                <div class="driver-item" data-phone="84901234567" data-name="Taner Vural">
                    <strong>Driver A</strong><br><small>+84 123 456 789</small>
                </div>
                <div class="driver-item" data-phone="84901234567" data-name="Taner Vural">
                    <strong>Driver B</strong><br><small>+84 901 234 666</small>
                </div>
                <div class="driver-item" data-phone="84901234567" data-name="Taner Vural">
                    <strong>Driver C</strong><br><small>+84 901 234 777</small>
                </div>
                <div class="driver-item" data-phone="84815566166" data-name="Ken Nguyen">
                    <strong>Driver C</strong><br><small>+84 815 566 166</small>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>

<script>
    console.log("555 - JavaScript Ready");

    // --- CẤU HÌNH PUSHER ---
    const PUSHER_KEY = '47facf3eb2b6de698e86';
    const PUSHER_CLUSTER = 'ap1';

    var pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_CLUSTER,
        forceTLS: true
    });

    var currentChannel = null;

    
    let currentPhone = '';
    
    // Dữ liệu giả định để thay thế vào Template
    let bookingData = {
        driver_name: "Taner Vural",
        pickup_time: "15:45",
        car_plate: "AL-9988",
        customer_name: "Mr. Patrick"
    };

    // DỮ LIỆU HISTORY GIẢ ĐỊNH (Thay thế việc gọi database tạm thời)
    const mockHistory = {
        "84901234567": [
            { type: 'received', content: 'Chào công ty, tôi đã nhận được lịch chạy ngày mai.', time: '08:00' },
            { type: 'sent', content: 'Vâng anh Taner, nhớ kiểm tra xe kỹ trước khi đón khách nhé.', time: '08:05' },
            { type: 'received', content: 'Ok, tôi đã rõ. Xe đã được rửa sạch sẽ.', time: '08:10' },
            { type: 'sent', content: 'Tốt lắm, khách này rất quan trọng, hãy chú ý đúng giờ.', time: '09:00' },
            { type: 'received', content: 'Tôi đang trên đường đến điểm đón.', time: '14:20' }
        ]
    };

    $(document).ready(function() {
        loadTemplates();

        // 1. Khi chọn Driver từ danh sách
        $(document).on('click', '.driver-item', function() {
            $('.driver-item').removeClass('active');
            $(this).addClass('active');
            
            currentPhone = $(this).data('phone').toString();
            $('#current-name').text($(this).data('name'));
            
            // Nếu đang ở kênh cũ, hãy hủy đăng ký trước khi sang tài xế mới
            if (currentChannel) {
                pusher.unsubscribe('chat-' + currentChannel);
            }

            // Đăng ký kênh riêng cho số điện thoại này
            currentChannel = currentPhone;
            var channel = pusher.subscribe('chat-' + currentChannel);

            // Lắng nghe sự kiện tin nhắn mới từ Webhook
            channel.bind('new-message', function(data) {
                appendMessage(data.content, 'received', data.time);
                scrollBottom();
            });
            
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
        	let template_id = $('#tpl-select').val();
            let msg = $('#msg-input').val();
            if(!currentPhone || !msg) return;

            appendMessage(msg, 'sent', new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
            
            $.post('send_whatsapp.php', {
                phone: currentPhone,
                message: msg,
                template: template_id
            }, function(response) {
                console.log("Meta Response:", response);
            });

            $('#msg-input').val('');
            scrollBottom();
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
                $('#tpl-select').append(`<option value="${t.value}" data-body="${t.body}">${t.name}</option>`);
            });
        });
    }

    // Khởi tạo Pusher
    var pusher = new Pusher('47facf3eb2b6de698e86', {
        cluster: 'ap1'
    });

    // Đăng ký kênh chat (nên dùng tên kênh chung hoặc kênh riêng cho từng driver)
    var channel = pusher.subscribe('driverapp-whatsapp-chat');

    // Lắng nghe sự kiện 'new-message' từ server
    channel.bind('new-message', function(data) {
        // Kiểm tra nếu tin nhắn nhận được thuộc về tài xế đang được chọn
        if (currentPhone === data.phone) {
            appendMessage(data.text, 'received', data.time);
            scrollBottom();
        } else {
            // (Tùy chọn) Hiển thị thông báo hoặc đổi màu driver-item trong danh sách để báo có tin nhắn mới
            $(`.driver-item[data-phone="${data.phone}"]`).css('background-color', '#fff3cd');
        }
    });

    
</script>

</body>
</html>