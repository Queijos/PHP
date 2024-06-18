<?php
// Definir cabeçalhos para permitir CORS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

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
        $sql = $con->prepare("SELECT id, nome, senha FROM cliente WHERE email=?");
        $sql->bind_param("s", $email);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $senha_hash = $row['senha'];

            if (password_verify($senha, $senha_hash)) {
                // Sucesso no login
                exit(json_encode(array('success' => true, 'id' => $row['id'], 'nome' => $row['nome'])));
            } else {
                // Falha no login
                exit(json_encode(array('success' => false, 'message' => 'Email ou senha incorretos')));
            }
        } else {
            exit(json_encode(array('success' => false, 'message' => 'Email não encontrado')));
        }
    } else {
        exit(json_encode(array('success' => false, 'message' => 'Por favor, forneça email e senha')));
    }
} else {
    exit(json_encode(array('success' => false, 'message' => 'Método não permitido')));
}
?>
