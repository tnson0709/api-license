<?php
// api-dic/rest-api/api/add_customer.php
include_once "../config/database.php";
include_once "../class/customer.php";

$db = (new Database())->getConnection();
$customer = new Customer($db);

$data = json_decode(file_get_contents("php://input"), true);
$data['customer_id'] = uniqid();
$data['created_at'] = date('Y-m-d H:i:s');
$data['updated_at'] = date('Y-m-d H:i:s');

if($customer->create($data)){
    echo json_encode(['status' => 'success',"message" => "Customer created successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to create customer"]);
}
?>