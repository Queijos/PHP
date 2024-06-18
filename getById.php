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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = $con->query("SELECT * FROM cliente WHERE id='$id'");
    if ($sql) {
        $data = $sql->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(array('status'=> 'error', 'message' => 'Erro ao buscar cliente.'));
    }
    exit;
} else {
    echo json_encode(array('status'=> 'error', 'message' => 'ID não fornecido ou método não permitido.'));
}
?>
