<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

$validUser = getenv('ADMIN_USER') ?: 'admin';
$validPass = getenv('ADMIN_PASS') ?: 'ciptacoding2026';

if ($username === $validUser && $password === $validPass) {
    echo json_encode([
        'success' => true,
        'message' => 'Login berhasil',
        'token' => 'ciptacoding-adm-token-' . time()
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Username atau password salah!'
    ]);
}
