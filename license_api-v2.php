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
/// Hàm sinh license key (có tham số licenseInfo)
function generateLicenseKey( $userId, $productId, $licenseInfo = '') {
    // Kết hợp các thông tin userId, productId, licenseInfo và thời gian hiện tại
    $data = $userId . ':' . $productId . ':' . $licenseInfo . ':' . time();
    return hash_hmac('sha256', $data, SECRET_KEY);
}
// Hàm kích hoạt key và lưu cache (7 ngày)
// Hàm kích hoạt key và lưu cache (7 ngày) kèm licenseInfo
function activateLicenseKey( $key, $licenseInfo = '') {
    $file = CACHE_DIR . md5( $key) . '.json';
    $weekInSeconds = 7 * 24 * 3600;
    if (file_exists( $file)) {
        $mtime = filemtime( $file);
        if ((time() - $mtime) < $weekInSeconds) {
            return ['success' => false, 'message' => 'Key đã được kích hoạt trước đó'];
        }
    }
    // Lưu trạng thái kích hoạt với licenseInfo
    $data = json_encode([
        'status' => 'active',
        'licenseInfo' => $licenseInfo,
        'activatedAt' => time()
    ]);
    file_put_contents( $file, $data);
    return ['success' => true, 'message' => 'Kích hoạt thành công'];
}
// Hàm kiểm tra key đã kích hoạt chưa, trả licenseInfo khi valid
function checkLicenseKey( $key) {
    $file = CACHE_DIR . md5( $key) . '.json';
    $weekInSeconds = 7 * 24 * 3600;
    if (file_exists( $file) && (time() - filemtime( $file)) < $weekInSeconds) {
        $data = json_decode(file_get_contents( $file), true);
        if (!empty( $data['status']) && $data['status'] === 'active') {
            return [
                'valid' => true,
                'message' => 'Key hợp lệ và đang kích hoạt',
                'licenseInfo' => $data['licenseInfo'] ?? ''
            ];
        }
    }
    return ['valid' => false, 'message' => 'Key không hợp lệ hoặc hết hạn kích hoạt'];
}

// Xử lý API theo method & param POST/GET
$action = $_REQUEST['action'] ?? '';
$userId = $_REQUEST['userId'] ?? '';
$productId = $_REQUEST['productId'] ?? '';
$licenseInfo = $_REQUEST['licenseInfo'] ?? '';
$key = $_REQUEST['key'] ?? '';
switch( $action) {
    case 'generate':
        if (!$userId || !$productId) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu thông tin userId hoặc productId']);
            exit;
        }
        // Truyền thêm tham số licenseInfo
        $licenseKey = generateLicenseKey( $userId, $productId, $licenseInfo);
        echo json_encode(['licenseKey' => $licenseKey]);
        break;
    case 'activate':
	    if (!$key) {
		http_response_code(400);
		echo json_encode(['error' => 'Thiếu parameter key']);
		exit;
	    }
	    // Lấy licenseInfo nếu gửi kèm khi activate
	    $licenseInfo = $_REQUEST['licenseInfo'] ?? '';
	    $result = activateLicenseKey( $key, $licenseInfo);
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
        echo json_encode(['error' => 'Action không hợp lệ. Sử dụng action=generate|activate|check']);
        break;
}
   

