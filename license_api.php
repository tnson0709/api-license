<?php
header('Content-Type: application/json');
define('SECRET_KEY', 'jms-secret-key');
// Cấu hình DB MySQL
$dsn = 'mysql:host=localhost;dbname=demo;charset=utf8mb4';
$dbUser = 'root';
$dbPass = 'root';
try {
    $pdo = new PDO( $dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Lỗi kết nối database']);
    exit;
}
function generateLicenseKey( $userId, $productId, $licenseInfoJson) {
    $data = $userId . ':' . $productId . ':' . $licenseInfoJson . ':' . time();
    return hash_hmac('sha256', $data, SECRET_KEY);
}
// Lưu license mới vào DB
function saveLicense(PDO $pdo, $userId, $productId, $licenseKey, $licenseInfoJson) {
    // Kiểm tra tồn tại key (để tránh trùng)
    $stmtCheck = $pdo->prepare("SELECT 1 FROM license WHERE licenseKey = ?");
    $stmtCheck->execute([$licenseKey]);
    if ( $stmtCheck->fetch()) {
        return false; // đã tồn tại
    }
    // Lưu license chưa kích hoạt (activatedAt = NULL)
    $stmt = $pdo->prepare("INSERT INTO license (userId, productId, licenseKey, licenseInfo) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$userId, $productId, $licenseKey, $licenseInfoJson]);
}
// Kích hoạt license: cập nhật activatedAt nếu chưa kích hoạt hoặc hết hạn
function activateLicense(PDO $pdo, $licenseKey) {
    // Lấy license
    $stmt = $pdo->prepare("SELECT * FROM license WHERE licenseKey = ?");
    $stmt->execute([$licenseKey]);
    $license = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$license) {
        return ['success' => false, 'message' => 'Key không tồn tại'];
    }
    // Kiểm tra ngày hết hạn licenseInfo
    $info = json_decode( $license['licenseInfo'], true);
    $now = date('Y-m-d');
    if (isset( $info['endate']) && $now > $info['endate']) {
        return ['success' => false, 'message' => 'Key đã hết hạn'];
    }
    // Kiểm tra đã kích hoạt trong 7 ngày chưa
    if ( $license['activatedAt']) {
        $activatedAt = strtotime( $license['activatedAt']);
        if ((time() - $activatedAt) < 7 * 24 * 3600) {
            return ['success' => false, 'message' => 'Key đã được kích hoạt trước đó'];
        }
    }
    // Cập nhật activatedAt mới
    $stmtUpdate = $pdo->prepare("UPDATE license SET activatedAt = NOW() WHERE licenseKey = ?");
    $stmtUpdate->execute([$licenseKey]);
    return ['success' => true, 'message' => 'Kích hoạt thành công'];
}
// Kiểm tra license hợp lệ và đang kích hoạt
function checkLicense(PDO $pdo, $licenseKey) {
    $stmt = $pdo->prepare("SELECT * FROM license WHERE licenseKey = ?");
    $stmt->execute([$licenseKey]);
    $license = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$license) {
        return ['valid' => false, 'message' => 'Key không tồn tại'];
    }
    $info = json_decode( $license['licenseInfo'], true);
    $now = date('Y-m-d');
    if (!$license['activatedAt']) {
        return ['valid' => false, 'message' => 'Key chưa được kích hoạt'];
    }
    $activatedAt = strtotime( $license['activatedAt']);
    // Kiểm tra hạn kích hoạt trong 7 ngày
    if ((time() - $activatedAt) > 7 * 24 * 3600) {
        return ['valid' => false, 'message' => 'Key đã hết hạn kích hoạt'];
    }
    // Kiểm tra ngày hết hạn licenseInfo (nếu có)
    if (isset( $info['endate']) && $now > $info['endate']) {
        return ['valid' => false, 'message' => 'Key đã hết hạn theo licenseInfo'];
    }
    return [
        'valid' => true,
        'message' => 'Key hợp lệ và đang kích hoạt',
        'licenseInfo' => $info
    ];
}
// Xử lý API
$action = $_REQUEST['action'] ?? '';
$userId = $_REQUEST['userId'] ?? '';
$productId = $_REQUEST['productId'] ?? '';
$licenseInfo = $_REQUEST['licenseInfo'] ?? '';  // chuỗi JSON string
$key = $_REQUEST['key'] ?? '';
switch( $action) {
    case 'generate':
        if (!$userId || !$productId || !$licenseInfo) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu userId, productId hoặc licenseInfo']);
            exit;
        }
        // Validate licenseInfo là JSON hợp lệ
        if (json_decode( $licenseInfo) === null) {
            http_response_code(400);
            echo json_encode(['error' => 'licenseInfo phải là chuỗi JSON hợp lệ']);
            exit;
        }
        $licenseKey = generateLicenseKey( $userId, $productId, $licenseInfo);
        $saved = saveLicense( $pdo, $userId, $productId, $licenseKey, $licenseInfo);
        if (!$saved) {
            http_response_code(409);
            echo json_encode(['error' => 'Key đã tồn tại hoặc lỗi lưu']);
            exit;
        }
        echo json_encode(['licenseKey' => $licenseKey]);
        break;
    case 'activate':
        if (!$key) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu parameter key']);
            exit;
        }
        $result = activateLicense( $pdo, $key);
        echo json_encode( $result);
        break;
    case 'check':
        if (!$key) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu parameter key']);
            exit;
        }
        $result = checkLicense( $pdo, $key);
        echo json_encode( $result);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action không hợp lệ. Sử dụng action=generate|activate|check']);
        break;
}

