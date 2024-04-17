<?php
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
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");

function verifyRecaptcha($response) {
    global $secret;
    $apiUrl = "https://www.google.com/recaptcha/api/siteverify";
    
    $postData = http_build_query([
        "secret" => $secret,
        "response" => $response
    ]);

    $options = [
        "http" => [
            "method" => "POST",
            "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
            "content" => $postData
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($apiUrl, false, $context);
    if ($result === FALSE) {
        return false;
    }
    
    $responseData = json_decode($result);
    return $responseData->success;
}

function recaptchaHandler() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit();
    }

    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $success = verifyRecaptcha($recaptchaResponse);
    
    $response = [
        "success" => $success,
        "message" => $success ? "reCAPTCHA verified successfully" : "reCAPTCHA verification failed"
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

recaptchaHandler();
?>
