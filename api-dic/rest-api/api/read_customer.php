<?php
// api-dic/rest-api/api/read_customer.php
include_once "../config/database.php";
include_once "../class/customer.php";

$db = (new Database())->getConnection();
$customer = new Customer($db);

$stmt = $customer->read();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>