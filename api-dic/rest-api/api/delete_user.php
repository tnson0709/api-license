<?php
// api-dic/rest-api/api/delete_user.php
include_once "../config/database.php";
include_once "../class/user.php";

$db = (new Database())->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"), true);

if($user->delete($data['user_id'])){
    echo json_encode(['status' => 'success',"message" => "User deleted successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to delete user"]);
}
?>