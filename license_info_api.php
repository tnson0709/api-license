<?php
header('Content-Type: application/json');
define('SECRET_KEY', 'jms-secret-key');
$dsn = 'mysql:host=localhost;dbname=demo;charset=utf8mb4';
$dbUser = 'root';
$dbPass = 'root';
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection error']);
    exit;
}

// Helper: Generate UUID v4
function uuidv4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Parse URL
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$matches = [];
preg_match('#/licenses(?:/([^/]+))?(?:/activate)?#', $path, $matches);
$id = $matches[1] ?? null;
$isActivate = (strpos($path, '/activate') !== false);

// GET /licenses?search=...&sort=...&page=...&limit=...
if ($method === 'GET' && preg_match('#^/licenses#', $path)) {
    $where = [];
    $params = [];
    // Filtering
    if (!empty($_GET['search'])) {
        $where[] = '(customer_name LIKE ? OR customer_code LIKE ? OR email LIKE ?)';
        $search = '%' . $_GET['search'] . '%';
        $params = array_merge($params, [$search, $search, $search]);
    }
    // Paging
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = max(1, intval($_GET['limit'] ?? 10));
    $offset = ($page - 1) * $limit;
    // Sorting
    $sort = $_GET['sort'] ?? 'created_date DESC';
    $sql = "SELECT * FROM license_info";
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " ORDER BY $sort LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($data);
    exit;
}

// POST /licenses — Thêm mới
if ($method === 'POST' && $id === null) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['customerId'], $input['productId'], $input['licenseKey'], $input['licenseInfo'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    // Validate licenseInfo JSON
    if (json_decode($input['licenseInfo']) === null) {
        http_response_code(400);
        echo json_encode(['error' => 'licenseInfo must be valid JSON']);
        exit;
    }
    $license_customer_Id = uuidv4();
    $fields = [
        'license_customer_Id', 'customerId', 'productId', 'licenseKey', 'licenseInfo',
        'customer_name', 'customer_code', 'taxcode', 'tel', 'email', 'customer_address',
        'note', 'start_date', 'expiry_date', 'created_date', 'amount'
    ];
    $values = [];
    foreach ($fields as $f) $values[$f] = $input[$f] ?? null;
    $sql = "INSERT INTO license_info (" . implode(',', $fields) . ") VALUES (" .
        implode(',', array_fill(0, count($fields), '?')) . ")";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute(array_values($values));
    echo json_encode(['success' => $ok, 'license_customer_Id' => $license_customer_Id]);
    exit;
}

// PUT /licenses/:id — Sửa
if ($method === 'PUT' && $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing input']);
        exit;
    }
    $fields = [
        'customerId', 'productId', 'licenseKey', 'licenseInfo',
        'customer_name', 'customer_code', 'taxcode', 'tel', 'email', 'customer_address',
        'note', 'start_date', 'expiry_date', 'created_date', 'amount'
    ];
    $set = [];
    $params = [];
    foreach ($fields as $f) {
        if (isset($input[$f])) {
            $set[] = "$f = ?";
            $params[] = $input[$f];
        }
    }
    if (!$set) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }
    $params[] = $id;
    $sql = "UPDATE license_info SET " . implode(',', $set) . " WHERE license_customer_Id = ?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute($params);
    echo json_encode(['success' => $ok]);
    exit;
}

// POST /licenses/:id/activate — Cấp phép
if ($method === 'POST' && $id && $isActivate) {
    $sql = "UPDATE license_info SET activatedAt = NOW() WHERE license_customer_Id = ?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$id]);
    echo json_encode(['success' => $ok]);
    exit;
}

// DELETE /licenses/:id — Xóa
if ($method === 'DELETE' && $id) {
    $sql = "DELETE FROM license_info WHERE license_customer_Id = ?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([$id]);
    echo json_encode(['success' => $ok]);
    exit;
}

// Nếu không khớp endpoint
http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
// End of script
?>
