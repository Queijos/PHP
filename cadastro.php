<?php
// Definir cabeçalhos para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

// Verificar se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receber os dados do cliente
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar se todos os campos necessários estão presentes
    if (isset($data['nome']) && isset($data['email']) && isset($data['telefone']) && isset($data['data_nasc']) && isset($data['genero']) && isset($data['senha'])) {
        $nome = $data['nome'];
        $email = $data['email'];
        $telefone = $data['telefone'];
        $data_nasc = $data['data_nasc'];
        $genero = $data['genero'];
        $senha = $data['senha'];

        // Hash da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir no banco de dados
        $con = new mysqli('localhost', 'root', '', 'e-studo');
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        $sql = $con->prepare("INSERT INTO cliente (nome, email, telefone, data_nasc, genero, senha) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssssss", $nome, $email, $telefone, $data_nasc, $genero, $senha_hash);

        if ($sql->execute()) {
            echo json_encode(array("status" => "success", "message" => "Cadastro realizado com sucesso."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Erro ao cadastrar cliente: " . $sql->error));
        }

        $sql->close();
        $con->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "Parâmetros inválidos."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Método não permitido."));
}
?>
