<?php
// api-dic/rest-api/api/delete_contract.php
include_once "../config/database.php";
include_once "../class/contract.php";

$db = (new Database())->getConnection();
$contract = new Contract($db);

$data = json_decode(file_get_contents("php://input"), true);

if($contract->delete($data['contract_id'])){
    echo json_encode(['status' => 'success',"message" => "Contract deleted successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to delete contract"]);
}
?>