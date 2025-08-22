<?php
// api-dic/rest-api/api/read_contract.php
include_once "../config/database.php";
include_once "../class/contract.php";

$db = (new Database())->getConnection();
$contract = new Contract($db);

$stmt = $contract->read();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>