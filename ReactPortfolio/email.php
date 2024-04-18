<?php

// お問い合わせ送信

require './config/env.php';

if (isset($_SERVER['HTTP_ORIGIN'])) {
    if ($_SERVER['HTTP_ORIGIN'] == $origin_url) {
        header("Access-Control-Allow-Origin: $origin_url");
    } else {
        header("HTTP/1.1 403 Access Forbidden");
        exit('This service is not available for your origin.');
    }
} else {
    header("HTTP/1.1 403 Access Forbidden");
    exit('No Origin header received.');
}
header('Access-Control-Allow-Headers: Content-Type');

$postData = json_decode(file_get_contents('php://input'), true);

if (!empty($postData)) {
    $to = $postData['email'] . ',' . $email_to;

    $subject = 'お問い合わせありがとうございます';

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . 'RealPortfolio' . "\r\n";

    $message = "<p>以下の内容でお問い合わせを承りました。</p>";
    $message = "<p>数日以内にご連絡いたしますので、しばらくお待ちください。</p>";
    $message .= "<p style='padding: 12px; border-left: 4px solid #d0d0d0; font-style: italic;'>氏名: " . $postData['sei'] . "</p>";
    $message .= "<p style='padding: 12px; border-left: 4px solid #d0d0d0; font-style: italic;'>題名: " . $postData['mei'] . "</p>";
    $message .= "<p style='padding: 12px; border-left: 4px solid #d0d0d0; font-style: italic;'>メールアドレス: " . $postData['email'] . "</p>";
    $message .= "<p style='padding: 12px; border-left: 4px solid #d0d0d0; font-style: italic;'>お問い合わせ内容: " . $postData['message'] . "</p>";

    if(mail($to, $subject, $message, $headers)){
        echo json_encode(array("status" => "success"));
    } else{
        echo json_encode(array("status" => "fail"));
    }
} else {
    echo json_encode(array("status" => "no data received"));
}
?>
