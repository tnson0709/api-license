<?php
// api-dic/rest-api/api/delete_room.php
include_once "../config/database.php";
include_once "../class/room.php";

$db = (new Database())->getConnection();
$room = new Room($db);

$data = json_decode(file_get_contents("php://input"), true);

if($room->delete($data['room_id'])){
    echo json_encode(['status' => 'success',"message" => "Room deleted successfully"]);
} else {
    echo json_encode(['status' => 'error',"message" => "Unable to delete room"]);
}
?>