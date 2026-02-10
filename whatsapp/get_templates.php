<?php
// Viet's API
$token = "EAFwqWSVozegBQoZC1ow2uG3fygTZCuXZBqStwlovNZCr9aveZCZA0ZBNH43bWdLqQ1FIOtu7wOqA2EfvryfRWHoSHj38YZAoSH69NJVdAtQghmHycysCx0ZCUX3trzSKWCx6ekn24S2UF2ZCJixKO0TW0kdpTN3zHK08pcnt9HBJXSIVepnMwISPqqX6RNtuVOxu8tJgZDZD";
$wabaId = "340520532472949";

// Ahmet's API
//$token = "EAAMuA2p8b1cBQsFdgjirBXKV6ZAZBSCVliv43pcmxd5LZBiuRSUrSFIzaLCorI2GWcZBye82ZCdwCDEzfKZBKtZBoCUQaQG5xHxFDYVDZAGz949agfr0TX1FjPL1wJyky1b5hdIvrdoSFUZB0HsfVuopSvvzuRp0rYyAVj3cR2zkTl8Hd7NRT3ygvciZCWhoNKdPlz5gZDZD";
//$wabaId = "1239756145002451";

$url = "https://graph.facebook.com/v18.0/$wabaId/message_templates?limit=100";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
$response = curl_exec($ch);
$data = json_decode($response, true);

$approved = [];
if (isset($data['data'])) {
    foreach ($data['data'] as $tpl) {
        if ($tpl['status'] === 'APPROVED') {
            $bodyText = "";
            // Meta trả về components là một mảng, phải tìm đúng type = BODY
            foreach ($tpl['components'] as $component) {
                if ($component['type'] === 'BODY') {
                    $bodyText = $component['text'];
                    break;
                }
            }
            $templates[] = [
                'name' => $tpl['name'],
                'value' => $tpl['name'].'~:~'.$tpl['language'],
                'language' => $tpl['language'],
                'body' => $bodyText // Đảm bảo body không bị undefined
            ];
        }
    }
}
header('Content-Type: application/json');
echo json_encode($templates);
