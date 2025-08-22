<?php
// api-dic/rest-api/api/update_room.php
include_once "../config/database.php";
include_once "../class/room.php";

$db = (new Database())->getConnection();
$room = new Room($db);

$data = json_decode(file_get_contents("php://input"), true);
$data['updated_at'] = date('Y-m-d H:i:s');

if($room->update($data)){
    echo json_encode(['status' => 'success',"message" => "Room updated successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to update room"]);
}
?>