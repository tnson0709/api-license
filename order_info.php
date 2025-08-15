<?php
// ...existing code...

// Hàm lấy orderno tự tăng từ order_request
function getNextOrderNo(PDO $pdo) {
    $pdo->exec("INSERT INTO order_request VALUES (NULL)");
    return $pdo->lastInsertId();
}

// Thêm order_info mới
function addOrderInfo(PDO $pdo, $data) {
    $order_info_Id = bin2hex(random_bytes(18));
    $orderno = getNextOrderNo($pdo);
    $stmt = $pdo->prepare("INSERT INTO order_info 
        (order_info_Id, orderno, productId, packcode, licenseInfo, customer_name, customer_address, taxcode, tel, email, note, amount, payment_status, resource_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ok = $stmt->execute([
        $order_info_Id,
        $orderno,
        $data['productId'],
        $data['packcode'],
        $data['licenseInfo'],
        $data['customer_name'] ?? '',
        $data['customer_address'] ?? '',
        $data['taxcode'] ?? '',
        $data['tel'] ?? '',
        $data['email'] ?? '',
        $data['note'] ?? '',
        $data['amount'] ?? 0,
        $data['payment_status'] ?? 0,
        $data['resource_status'] ?? 0
    ]);
    return $ok ? ['success' => true, 'order_info_Id' => $order_info_Id, 'orderno' => $orderno] : ['success' => false];
}

// Nhân bản order_info
function cloneOrderInfo(PDO $pdo, $order_info_Id) {
    $stmt = $pdo->prepare("SELECT * FROM order_info WHERE order_info_Id = ?");
    $stmt->execute([$order_info_Id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$order) return ['success' => false, 'message' => 'Không tìm thấy order'];
    unset($order['order_info_Id'], $order['orderno']);
    $order['order_info_Id'] = bin2hex(random_bytes(18));
    $order['orderno'] = getNextOrderNo($pdo);
    $stmt2 = $pdo->prepare("INSERT INTO order_info 
        (order_info_Id, orderno, productId, packcode, licenseInfo, customer_name, customer_address, taxcode, tel, email, note, amount, payment_status, resource_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $ok = $stmt2->execute([
        $order['order_info_Id'],
        $order['orderno'],
        $order['productId'],
        $order['packcode'],
        $order['licenseInfo'],
        $order['customer_name'],
        $order['customer_address'],
        $order['taxcode'],
        $order['tel'],
        $order['email'],
        $order['note'],
        $order['amount'],
        $order['payment_status'],
        $order['resource_status']
    ]);
    return $ok ? ['success' => true, 'order_info_Id' => $order['order_info_Id'], 'orderno' => $order['orderno']] : ['success' => false];
}

// Sửa order_info
function updateOrderInfo(PDO $pdo, $order_info_Id, $data) {
    $fields = [];
    $params = [];
    foreach ($data as $k => $v) {
        $fields[] = "$k = ?";
        $params[] = $v;
    }
    $params[] = $order_info_Id;
    $sql = "UPDATE order_info SET " . implode(', ', $fields) . " WHERE order_info_Id = ?";
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute($params);
    return $ok ? ['success' => true] : ['success' => false];
}

// Xóa order_info
function deleteOrderInfo(PDO $pdo, $order_info_Id) {
    $stmt = $pdo->prepare("DELETE FROM order_info WHERE order_info_Id = ?");
    $ok = $stmt->execute([$order_info_Id]);
    return $ok ? ['success' => true] : ['success' => false];
}

// Xác nhận thanh toán
function confirmPayment(PDO $pdo, $order_info_Id, $status) {
    $stmt = $pdo->prepare("UPDATE order_info SET payment_status = ? WHERE order_info_Id = ?");
    $ok = $stmt->execute([$status, $order_info_Id]);
    return $ok ? ['success' => true] : ['success' => false];
}

// ...existing code...

switch($action) {
    // ...existing cases...
    case 'add_order':
        $data = $_REQUEST;
        if (!isset($data['productId'], $data['packcode'], $data['licenseInfo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu productId, packcode, licenseInfo']);
            exit;
        }
        if (json_decode($data['licenseInfo']) === null) {
            http_response_code(400);
            echo json_encode(['error' => 'licenseInfo phải là chuỗi JSON hợp lệ']);
            exit;
        }
        echo json_encode(addOrderInfo($pdo, $data));
        break;
    case 'clone_order':
        $order_info_Id = $_REQUEST['order_info_Id'] ?? '';
        if (!$order_info_Id) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu order_info_Id']);
            exit;
        }
        echo json_encode(cloneOrderInfo($pdo, $order_info_Id));
        break;
    case 'update_order':
        $order_info_Id = $_REQUEST['order_info_Id'] ?? '';
        if (!$order_info_Id) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu order_info_Id']);
            exit;
        }
        $data = $_REQUEST;
        unset($data['action'], $data['order_info_Id']);
        echo json_encode(updateOrderInfo($pdo, $order_info_Id, $data));
        break;
    case 'delete_order':
        $order_info_Id = $_REQUEST['order_info_Id'] ?? '';
        if (!$order_info_Id) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu order_info_Id']);
            exit;
        }
        echo json_encode(deleteOrderInfo($pdo, $order_info_Id));
        break;
    case 'confirm_payment':
        $order_info_Id = $_REQUEST['order_info_Id'] ?? '';
        $status = $_REQUEST['status'] ?? '';
        if (!$order_info_Id || $status === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu order_info_Id hoặc status']);
            exit;
        }
        echo json_encode(confirmPayment($pdo, $order_info_Id, $status));
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action không hợp lệ']);
        break;
}

// ...existing code...
?>