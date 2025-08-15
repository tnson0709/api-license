<?php
header('Content-Type: application/json');
// Secret key dùng để tạo HMAC, giữ bí mật
define('SECRET_KEY', 'jms-secret-key-7');
// Thư mục lưu cache key đã kích hoạt
define('CACHE_DIR', __DIR__ . '/cache/');
// Tạo thư mục cache nếu chưa tồn tại
if (!is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}
// Hàm sinh license key
function generateLicenseKey( $userId, $productId) {
    $data = $userId . ':' . $productId . ':' . time();
    return hash_hmac('sha256', $data, SECRET_KEY);
}
// Hàm kích hoạt key và lưu cache (7 ngày)
function activateLicenseKey( $key) {
    $file = CACHE_DIR . md5( $key) . '.txt';
    $weekInSeconds = 7 * 24 * 3600;
    if (file_exists( $file)) {
        $mtime = filemtime( $file);
        if ((time() - $mtime) < $weekInSeconds) {
            return ['success' => false, 'message' => 'Key đã được kích hoạt trước đó'];
        }
    }
    file_put_contents( $file, 'active');
    return ['success' => true, 'message' => 'Kích hoạt thành công'];
}
// Hàm kiểm tra key đã kích hoạt chưa
function checkLicenseKey( $key) {
    $file = CACHE_DIR . md5( $key) . '.txt';
    $weekInSeconds = 7 * 24 * 3600;
    if (file_exists( $file) && (time() - filemtime( $file)) < $weekInSeconds) {
        return ['valid' => true, 'message' => 'Key hợp lệ và đang kích hoạt'];
    }
    return ['valid' => false, 'message' => 'Key không hợp lệ hoặc hết hạn kích hoạt'];
}
// Xử lý API theo method & param POST/GET
$action = $_REQUEST['action'] ?? '';
$userId = $_REQUEST['userId'] ?? '';
$productId = $_REQUEST['productId'] ?? '';
$key = $_REQUEST['key'] ?? '';
switch( $action) {
    case 'generate':
        if (!$userId || !$productId) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu thông tin userId hoặc productId']);
            exit;
        }
        $licenseKey = generateLicenseKey( $userId, $productId);
        echo json_encode(['licenseKey' => $licenseKey]);
        break;
    case 'activate':
        if (!$key) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu parameter key']);
            exit;
        }
        $result = activateLicenseKey( $key);
        echo json_encode( $result);
        break;
    case 'check':
        if (!$key) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu parameter key']);
            exit;
        }
        $result = checkLicenseKey( $key);
        echo json_encode( $result);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action không hợp lệ. Sử dụng action=generate|activate|

check']);
        break;
}

