<?php
// api-dic/rest-api/api/delete_customer.php
include_once "../config/database.php";
include_once "../class/customer.php";

$db = (new Database())->getConnection();
$customer = new Customer($db);

$data = json_decode(file_get_contents("php://input"), true);

if($customer->delete($data['customer_id'])){
    echo json_encode(['status' => 'success',"message" => "Customer deleted successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to delete customer"]);
}
?>