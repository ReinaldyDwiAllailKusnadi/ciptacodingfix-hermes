<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dataFile = __DIR__ . '/../data/portofolio.json';

function getProjects($file) {
    if (!file_exists($file)) {
        return [];
    }
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}

function saveProjects($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];
$pathInfo = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : '';
$id = $pathInfo !== '' ? $pathInfo : (isset($_GET['id']) ? $_GET['id'] : null);

if ($method === 'GET') {
    $projects = getProjects($dataFile);
    if ($id) {
        foreach ($projects as $p) {
            if ((string)$p['id'] === (string)$id) {
                echo json_encode(['success' => true, 'data' => $p]);
                exit;
            }
        }
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Proyek tidak ditemukan']);
        exit;
    }
    echo json_encode(['success' => true, 'data' => $projects]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];

if ($method === 'POST') {
    if (empty($input['title'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Judul proyek wajib diisi']);
        exit;
    }

    $projects = getProjects($dataFile);
    $maxId = 0;
    foreach ($projects as $p) {
        $pId = (int)$p['id'];
        if ($pId > $maxId) $maxId = $pId;
    }
    $newId = $maxId + 1;

    $newProject = [
        'id' => $newId,
        'code' => !empty($input['code']) ? $input['code'] : 'PRJ-' . str_pad($newId, 2, '0', STR_PAD_LEFT),
        'title' => $input['title'],
        'categories' => is_array($input['categories'] ?? null) ? $input['categories'] : ['bisnis'],
        'categoryLabel' => $input['categoryLabel'] ?? 'Sistem Digital',
        'description' => $input['description'] ?? '',
        'image' => $input['image'] ?? 'assets/images/pos-erp-multi-outlet.svg',
        'badgeTop' => $input['badgeTop'] ?? ['icon' => '', 'dot' => true, 'text' => 'Live Project'],
        'badgeBottom' => $input['badgeBottom'] ?? 'Digital Solution',
        'techStack' => is_array($input['techStack'] ?? null) ? $input['techStack'] : [],
        'highlight' => $input['highlight'] ?? 'Siap Digunakan',
        'highlightIcon' => $input['highlightIcon'] ?? 'check_circle',
        'ctaText' => $input['ctaText'] ?? 'Konsultasi',
        'whatsappText' => $input['whatsappText'] ?? ('Halo CiptaCoding, saya tertarik dengan proyek ' . $input['title']),
        'order' => isset($input['order']) ? (int)$input['order'] : $newId
    ];

    $projects[] = $newProject;
    saveProjects($dataFile, $projects);

    http_response_code(201);
    echo json_encode(['success' => true, 'data' => $newProject, 'message' => 'Proyek berhasil ditambahkan']);
    exit;
}

if ($method === 'PUT') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID proyek dibutuhkan']);
        exit;
    }

    $projects = getProjects($dataFile);
    $found = false;

    foreach ($projects as $index => $p) {
        if ((string)$p['id'] === (string)$id) {
            $input['id'] = $p['id']; // protect id
            $projects[$index] = array_merge($p, $input);
            $found = true;
            $updatedProject = $projects[$index];
            break;
        }
    }

    if (!$found) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Proyek tidak ditemukan']);
        exit;
    }

    saveProjects($dataFile, $projects);
    echo json_encode(['success' => true, 'data' => $updatedProject, 'message' => 'Proyek berhasil diperbarui']);
    exit;
}

if ($method === 'DELETE') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID proyek dibutuhkan']);
        exit;
    }

    $projects = getProjects($dataFile);
    $filtered = [];
    $found = false;

    foreach ($projects as $p) {
        if ((string)$p['id'] === (string)$id) {
            $found = true;
        } else {
            $filtered[] = $p;
        }
    }

    if (!$found) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Proyek tidak ditemukan']);
        exit;
    }

    saveProjects($dataFile, $filtered);
    echo json_encode(['success' => true, 'message' => 'Proyek berhasil dihapus']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
