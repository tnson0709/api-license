<?php
// api-dic/rest-api/api/read_room.php
include_once "../config/database.php";
include_once "../class/room.php";

$db = (new Database())->getConnection();
$room = new Room($db);

$stmt = $room->read();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>