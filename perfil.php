<?php
// Definir cabeçalhos para permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

// Verificar se o método da requisição é GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Verificar se foi fornecido um ID de usuário
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Conectar ao banco de dados
        $con = new mysqli('localhost', 'root', '', 'e-studo');
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        // Consultar o banco de dados para obter os dados do perfil
        $sql = $con->prepare("SELECT id, nome, email, telefone, data_nasc, genero, imagem FROM cliente WHERE id = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $perfil = array(
                "id" => $row['id'],
                "nome" => $row['nome'],
                "email" => $row['email'],
                "telefone" => $row['telefone'],
                "data_nasc" => $row['data_nasc'],
                "genero" => $row['genero'],
                "imagem" => $row['imagem']
            );
            echo json_encode($perfil);
        } else {
            echo json_encode(array("status" => "error", "message" => "Usuário não encontrado."));
        }

        $sql->close();
        $con->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "ID do usuário não foi fornecido."));
    }
}

// Verificar se o método da requisição é PUT (atualização de perfil)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Ler os dados da requisição
    $data = json_decode(file_get_contents("php://input"), true);

    // Conectar ao banco de dados
    $con = new mysqli('localhost', 'root', '', 'e-studo');
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Atualizar os dados do perfil
    $sql = $con->prepare("UPDATE cliente SET nome = ?, email = ?, telefone = ?, data_nasc = ?, genero = ? WHERE id = ?");
    $sql->bind_param("sssssi", $data['nome'], $data['email'], $data['telefone'], $data['data_nasc'], $data['genero'], $data['id']);

    if ($sql->execute()) {
        echo json_encode(array("status" => "success", "message" => "Perfil atualizado com sucesso."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Erro ao atualizar perfil: " . $con->error));
    }

    $sql->close();
    $con->close();
}

// Verificar se o método da requisição é POST (upload de imagem)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se foi fornecido um ID de usuário e um arquivo
    if (isset($_GET['action']) && $_GET['action'] === 'uploadImage' && isset($_GET['id']) && isset($_FILES['file'])) {
        $id = $_GET['id'];
        $file = $_FILES['file'];

        // Pasta onde será feito o upload
        $uploadDir = "./uploads/";

        // Caminho completo do arquivo
        $uploadPath = $uploadDir . basename($file['name']);

        // Verificar se o arquivo é uma imagem
        $imageFileType = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($imageFileType, $allowedTypes)) {
            // Tentar mover o arquivo para o diretório de uploads
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Conectar ao banco de dados
                $con = new mysqli('localhost', 'root', '', 'e-studo');
                if ($con->connect_error) {
                    die("Connection failed: " . $con->connect_error);
                }

                // Atualizar o campo de imagem do cliente
                $sql = $con->prepare("UPDATE cliente SET imagem = ? WHERE id = ?");
                $sql->bind_param("si", $uploadPath, $id);

                if ($sql->execute()) {
                    echo json_encode(array("status" => "success", "message" => "Imagem do perfil atualizada com sucesso."));
                } else {
                    echo json_encode(array("status" => "error", "message" => "Erro ao atualizar imagem do perfil: " . $con->error));
                }

                $sql->close();
                $con->close();
            } else {
                echo json_encode(array("status" => "error", "message" => "Falha ao fazer upload do arquivo."));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos."));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Dados inválidos para upload de imagem."));
    }
}
?>
