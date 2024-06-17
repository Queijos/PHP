<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$con = new mysqli('localhost', 'root', '', 'e-studo');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    $id = $_GET['id'];
    $sql = $con->query("UPDATE cliente SET
        nome = '".$data->nome."',
        email = '".$data->email."',
        telefone = '".$data->telefone."',
        data_nasc = '".$data->data_nasc."',
        genero = '".$data->genero."',
        senha = '".$data->senha."'
        WHERE id = '$id'");
    if ($sql) {
        echo json_encode(array('status'=> 'success'));
    } else {
        echo json_encode(array('status'=> 'error'));
    }
    exit;
}
?>
