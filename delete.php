<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$con = new mysqli('localhost', 'root', '', 'e-studo');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = $con->query("DELETE FROM cliente WHERE id='$id'");
    if ($sql) {
        echo json_encode(array('status'=> 'success'));
    } else {
        echo json_encode(array('status'=> 'error'));
    }
    exit;
}
?>
