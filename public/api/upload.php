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
$filename = $input['filename'] ?? 'image.jpg';
$base64Data = $input['base64Data'] ?? '';

if (empty($base64Data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data gambar tidak valid']);
    exit;
}

if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
    $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
    $type = strtolower($type[1]);
    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg+xml'])) {
        $type = 'jpg';
    }
} else {
    $type = pathinfo($filename, PATHINFO_EXTENSION) ?: 'jpg';
}

$data = base64_decode($base64Data);
if ($data === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Gagal mendecode base64 gambar']);
    exit;
}

$cleanName = 'upload-' . time() . '-' . rand(100, 999) . '.' . ($type === 'svg+xml' ? 'svg' : $type);
$targetDir = __DIR__ . '/../assets/images/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

file_put_contents($targetDir . $cleanName, $data);

echo json_encode([
    'success' => true,
    'url' => 'assets/images/' . $cleanName,
    'message' => 'Gambar berhasil diunggah'
]);
