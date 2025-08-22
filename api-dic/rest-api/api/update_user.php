<?php
// api-dic/rest-api/api/update_user.php
include_once "../config/database.php";
include_once "../class/user.php";

$db = (new Database())->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"), true);
$data['updated_at'] = date('Y-m-d H:i:s');

if($user->update($data)){
    echo json_encode(['status' => 'success',"message" => "User updated successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to update user"]);
}
?>