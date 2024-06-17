<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$con = new mysqli('localhost', 'root', '', 'e-studo');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $sql = $con->query("INSERT INTO cliente(nome, email, telefone, data_nasc, genero, senha) VALUES (
        '".$data->nome."', '".$data->email."', '".$data->telefone."', '".$data->data_nasc."', '".$data->genero."', '".$data->senha."')");
    if ($sql) {
        $data->id = $con->insert_id;
        echo json_encode($data);
    } else {
        echo json_encode(array("status" => "error"));
    }
    exit;
}
?>
