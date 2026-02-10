<?php

// Sử dụng thư viện Pusher cho PHP 7.4
require __DIR__ . '/vendor/autoload.php';

// Thông tin cấu hình WhatsApp API
// Viet's API
$accessToken = 'EAFwqWSVozegBQoZC1ow2uG3fygTZCuXZBqStwlovNZCr9aveZCZA0ZBNH43bWdLqQ1FIOtu7wOqA2EfvryfRWHoSHj38YZAoSH69NJVdAtQghmHycysCx0ZCUX3trzSKWCx6ekn24S2UF2ZCJixKO0TW0kdpTN3zHK08pcnt9HBJXSIVepnMwISPqqX6RNtuVOxu8tJgZDZD';
$phoneNumberId = '327635860430454';

// Ahmet's API
//$accessToken = 'EAAMuA2p8b1cBQsFdgjirBXKV6ZAZBSCVliv43pcmxd5LZBiuRSUrSFIzaLCorI2GWcZBye82ZCdwCDEzfKZBKtZBoCUQaQG5xHxFDYVDZAGz949agfr0TX1FjPL1wJyky1b5hdIvrdoSFUZB0HsfVuopSvvzuRp0rYyAVj3cR2zkTl8Hd7NRT3ygvciZCWhoNKdPlz5gZDZD';
//$phoneNumberId = '926885857180507';

$phone = $_POST['phone'];
$message = $_POST['message'];

if ($phone && $message) {
    $url = "https://graph.facebook.com/v18.0/$phoneNumberId/messages";
    if (!empty($_POST['template'])) {
        list($name, $lang) = explode('~:~', $_POST['template']);
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $phone,
            "type" => "template",
            "template" => [
                "name" => $name, // Tên template đã duyệt
                "language" => [ "code" => $lang ],
                /*"components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $driverName], // Tương ứng {{1}}
                            ["type" => "text", "text" => $time]        // Tương ứng {{2}}
                        ]
                    ]
                ] */
            ]
        ];
    } else {
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $phone,
            "type" => "text",
            "text" => ["body" => $message]
        ];
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    curl_close($ch);
    echo $result;
}
