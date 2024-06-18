<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$con = new mysqli('localhost', 'root', '', 'e-studo');

// Verifica a conexão
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // PEGANDO AS INFORMAÇÕES DO BANCO DE DADOS
    if (isset($_GET['id'])) {
        // Este If é usado, caso passagem de id
        $id = $_GET['id'];
        $sql = $con->query("SELECT * FROM cliente WHERE id='$id'");
        $data = $sql->fetch_assoc();
    } else {
        // Entra nesse, caso não tenha passagem de ID via "get"
        $data = array();
        $sql = $con->query("SELECT * FROM cliente");
        while ($d = $sql->fetch_assoc()) {
            $data[] = $d;
        }
    }
    exit(json_encode($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // ALTERAR INFORMAÇÕES
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $data = json_decode(file_get_contents("php://input"));
        $sql = $con->query("UPDATE cliente SET
            nome = '".$data->nome."',
            email = '".$data->email."',
            telefone = '".$data->telefone."',
            data_nasc = '".$data->data_nasc."',
            genero = '".$data->genero."',
            senha = '".$data->senha."'
            WHERE id = '$id'");
        if ($sql) {
            exit(json_encode(array('status'=> 'successo')));
        } else {
            exit(json_encode(array('status'=> 'Não Funcionou')));
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Esta é a nova rota de login
    if (isset($_GET['login'])) {
        $data = json_decode(file_get_contents("php://input"));
        $email = $data->email;
        $senha = $data->senha;
        $sql = $con->query("SELECT * FROM cliente WHERE email='$email'");
        if ($sql) {
            $user = $sql->fetch_assoc();
            if ($user && $user['senha'] == $senha) {
                // Sucesso no login
                exit(json_encode(array('success' => true)));
            } else {
                // Falha no login
                exit(json_encode(array('success' => false, 'message' => 'Email ou senha incorretos')));
            }
        } else {
            exit(json_encode(array('success' => false, 'message' => 'Usuário não encontrado')));
        }
    } else {
        // GRAVAR INFORMAÇÕES
        $data = json_decode(file_get_contents("php://input"));
        $sql = $con->query("INSERT INTO cliente(nome, email, telefone, data_nasc, genero, senha) VALUES (
            '".$data->nome."', '".$data->email."', '".$data->telefone."', '".$data->data_nasc."', '".$data->genero."', '".$data->senha."')");
        if ($sql) {
            $data->id = $con->insert_id;
            exit(json_encode($data));
        } else {
            exit(json_encode(array("status" => "Não Funcionou")));
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // APAGAR INFORMAÇÕES DO BANCO
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = $con->query("DELETE FROM cliente WHERE id='$id'");
        if ($sql) {
            exit(json_encode(array('status'=> 'Sucesso')));
        } else {
            exit(json_encode(array('status'=> 'Não Funcionou')));
        }
    }
}
?>