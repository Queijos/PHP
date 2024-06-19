<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "e-studo";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Verifica se foi enviado um arquivo e o ID do cliente
        if (isset($_FILES['file']) && isset($_POST['clienteId'])) {
            $file = $_FILES['file'];
            $clienteId = $_POST['clienteId'];

            // Diretório onde a imagem será salva
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($file["name"]);

            // Move o arquivo para o diretório de destino
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                // Atualiza o campo imagem na tabela cliente
                $sql = "UPDATE cliente SET imagem = '$target_file' WHERE id = '$clienteId'";
                if ($conn->query($sql) === TRUE) {
                    $response = array(
                        'success' => true,
                        'imagem' => $target_file
                    );
                    echo json_encode($response);
                } else {
                    $response = array(
                        'success' => false,
                        'message' => "Erro ao atualizar a imagem: " . $conn->error
                    );
                    echo json_encode($response);
                }
            } else {
                $response = array(
                    'success' => false,
                    'message' => "Erro ao fazer upload da imagem."
                );
                echo json_encode($response);
            }
        } else {
            $response = array(
                'success' => false,
                'message' => "Nenhum arquivo enviado ou ID de cliente não fornecido."
            );
            echo json_encode($response);
        }
        break;

    case 'GET':
        // Obtém o caminho da imagem do cliente
        if (isset($_GET['id'])) {
            $clienteId = $_GET['id'];

            // Busca o caminho da imagem do cliente no banco de dados
            $sql = "SELECT imagem FROM cliente WHERE id = '$clienteId'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $imagem = $row['imagem'];

                // Retorna a URL da imagem
                $response = array(
                    'success' => true,
                    'imagem' => $imagem
                );
                echo json_encode($response);
            } else {
                // Se não houver imagem, retorna um placeholder ou uma mensagem de erro
                $response = array(
                    'success' => false,
                    'message' => "Nenhuma imagem encontrada para o cliente com ID $clienteId"
                );
                echo json_encode($response);
            }
        } else {
            $response = array(
                'success' => false,
                'message' => "ID de cliente não fornecido."
            );
            echo json_encode($response);
        }
        break;

    default:
        echo json_encode(array('status' => 'Erro: Método não suportado'));
        break;
}

// Fecha a conexão com o banco de dados
$conn->close();
?>