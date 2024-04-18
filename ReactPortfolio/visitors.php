<?php

//アクセス数カウント
//ローカルストレージで同じ訪問者の回数は除外

header("Access-Control-Allow-Origin: $origin_url");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

require './config/env.php';

try {
    $conn = new PDO($dsn, $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET CHARACTER SET utf8");

    $sql = "UPDATE visitors SET count = count + 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo json_encode(["message" => "Visitor count updated successfully"]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
} finally {
    $conn = null;
}
?>
