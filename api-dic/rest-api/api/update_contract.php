<?php
//  api-dic/rest-api/api/update_contract.php
include_once "../config/database.php";
include_once "../class/contract.php";

$db = (new Database())->getConnection();
$contract = new Contract($db);

$data = json_decode(file_get_contents("php://input"), true);
$data['updated_at'] = date('Y-m-d H:i:s');

if($contract->update($data)){
    echo json_encode(['status' => 'success',"message" => "Contract updated successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to update contract"]);
}
?>