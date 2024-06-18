<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$con = new mysqli('localhost', 'root', '', 'e-studo');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = array();
    $sql = $con->query("SELECT * FROM cliente");
    while ($d = $sql->fetch_assoc()) {
        $data[] = $d;
    }
    echo json_encode($data);
    exit;
} else {
    echo json_encode(array('status'=> 'error', 'message' => 'Método não permitido.'));
}
?>
