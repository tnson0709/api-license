<?php
// api-dic/rest-api/api/read_user.php
include_once "../config/database.php";
include_once "../class/user.php";

$db = (new Database())->getConnection();
$user = new User($db);

$stmt = $user->read();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>