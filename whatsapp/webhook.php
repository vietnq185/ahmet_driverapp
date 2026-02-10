<?php 
require __DIR__ . '/vendor/autoload.php';

// Cấu hình Pusher từ thông tin bạn đã lấy
$options = array('cluster' => 'ap1', 'useTLS' => true);
$pusher = new Pusher\Pusher('47facf3eb2b6de698e86', '64120caad769b38a9acc', '2107248', $options);

// 1. Xác thực Webhook với Facebook (Chỉ chạy lần đầu khi thiết lập)
if (isset($_GET['hub_mode']) && $_GET['hub_mode'] == 'subscribe') {
    echo $_GET['hub_challenge'];
    exit;
}

// 2. Nhận dữ liệu tin nhắn từ WhatsApp
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['entry'][0]['changes'][0]['value']['messages'][0])) {
    $message = $input['entry'][0]['changes'][0]['value']['messages'][0];
    $fromPhone = $message['from']; // Số điện thoại tài xế
    $text = $message['text']['body']; // Nội dung tin nhắn
    
    $data = [
        'phone' => $fromPhone,
        'content' => $text,
        'direction' => 'received',
        'time' => date('H:i')
    ];
    
    // Trigger Pusher vào kênh riêng của số điện thoại: chat-8490...
    $pusher->trigger('chat-' . $fromPhone, 'new-message', $data);
    
    // (Tùy chọn) Lưu vào database của bạn tại đây
}
?>