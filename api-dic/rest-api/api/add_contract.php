<?php
// api-dic/rest-api/api/add_contract.php
include_once "../config/database.php";
include_once "../class/contract.php";

$db = (new Database())->getConnection();
$contract = new Contract($db);

$data = json_decode(file_get_contents("php://input"), true);
$data['contract_id'] = uniqid();
$data['contract_create_date'] = date('Y-m-d H:i:s');
$data['updated_at'] = date('Y-m-d H:i:s');

if($contract->create($data)){
    echo json_encode(['status' => 'success',"message" => "Contract created successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to create contract"]);
}
?>