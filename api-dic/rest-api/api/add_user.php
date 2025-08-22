<?php
// api-dic/rest-api/api/add_user.php
include_once "../config/database.php";
include_once "../class/user.php";

$db = (new Database())->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"), true);
$data['user_id'] = uniqid(); // hoặc dùng thư viện UUID
$data['created_at'] = date('Y-m-d H:i:s');
$data['updated_at'] = date('Y-m-d H:i:s');

if($user->create($data)){
    echo json_encode(['status' => 'success',"message" => "User created successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to create user"]);
}
?>