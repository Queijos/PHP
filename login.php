<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Resto do seu código PHP aqui...

// Conectar ao banco de dados
$con = new mysqli('localhost', 'root', '', 'e-studo');

// Verificar a conexão
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receber os dados do body da requisição
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->email) && !empty($data->senha)) {
        $email = $data->email;
        $senha = $data->senha;

        // Consultar o banco de dados para verificar o login
        $sql = $con->query("SELECT * FROM cliente WHERE email='$email'");

        if ($sql) {
            $user = $sql->fetch_assoc();

            if ($user && $user['senha'] == $senha) {
                // Sucesso no login
                exit(json_encode(array('success' => true, 'id' => $user['id'], 'nome' => $user['nome'])));
            } else {
                // Falha no login
                exit(json_encode(array('success' => false, 'message' => 'Email ou senha incorretos')));
            }
        } else {
            exit(json_encode(array('success' => false, 'message' => 'Erro ao consultar o banco de dados')));
        }
    } else {
        exit(json_encode(array('success' => false, 'message' => 'Por favor, forneça email e senha')));
    }
} else {
    exit(json_encode(array('success' => false, 'message' => 'Método não permitido')));
}
?>
