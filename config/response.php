<?php
/**
 * response.php
 * Helper supaya format JSON response konsisten di semua endpoint.
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Browser/Android kadang kirim OPTIONS dulu (preflight), langsung balas OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function sendSuccess($data = [], $message = "OK") {
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

function sendError($message = "Terjadi kesalahan", $code = 400) {
    http_response_code($code);
    echo json_encode([
        "success" => false,
        "message" => $message
    ]);
    exit;
}

// Ambil body JSON dari request (dipakai untuk POST/PUT)
function getJsonInput() {
    $input = file_get_contents("php://input");
    return json_decode($input, true) ?? [];
}
