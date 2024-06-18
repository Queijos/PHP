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
        echo json_encode(array("status" => "error", "message" => "ID de usuário não fornecido."));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Receber os dados do cliente
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar se todos os campos necessários estão presentes
    if (isset($data['id']) && isset($data['nome']) && isset($data['email']) && isset($data['telefone']) && isset($data['data_nasc']) && isset($data['genero'])) {
        $id = $data['id'];
        $nome = $data['nome'];
        $email = $data['email'];
        $telefone = $data['telefone'];
        $data_nasc = $data['data_nasc'];
        $genero = $data['genero'];

        // Verificar se há um arquivo de imagem
        $target_file = null;
        if (isset($_FILES['imagem'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES['imagem']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Verificar se é uma imagem real
            $check = getimagesize($_FILES['imagem']['tmp_name']);
            if ($check === false) {
                $uploadOk = 0;
            }

            // Limitar tamanho do arquivo
            if ($_FILES['imagem']['size'] > 500000) {
                $uploadOk = 0;
            }

            // Permitir apenas certos formatos de arquivo
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo json_encode(array("status" => "error", "message" => "O arquivo não pôde ser enviado."));
                exit;
            } else {
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $target_file)) {
                    // Imagem movida com sucesso
                } else {
                    echo json_encode(array("status" => "error", "message" => "Ocorreu um erro ao enviar o arquivo."));
                    exit;
                }
            }
        }

        // Atualizar no banco de dados
        $con = new mysqli('localhost', 'root', '', 'e-studo');
        $sql = $con->prepare("UPDATE cliente SET nome=?, email=?, telefone=?, data_nasc=?, genero=?, imagem=? WHERE id=?");
        $sql->bind_param("ssssssi", $nome, $email, $telefone, $data_nasc, $genero, $target_file, $id);

        if ($sql->execute()) {
            echo json_encode(array("status" => "success", "message" => "Perfil atualizado com sucesso."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Erro ao atualizar perfil."));
        }

        $sql->close();
        $con->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "Parâmetros inválidos."));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Receber o ID do perfil a ser deletado
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        // Conectar ao banco de dados
        $con = new mysqli('localhost', 'root', '', 'e-studo');
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        // Deletar perfil do banco de dados
        $sql = $con->prepare("DELETE FROM cliente WHERE id=?");
        $sql->bind_param("i", $id);

        if ($sql->execute()) {
            echo json_encode(array("status" => "success", "message" => "Perfil deletado com sucesso."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Erro ao deletar perfil."));
        }

        $sql->close();
        $con->close();
    } else {
        echo json_encode(array("status" => "error", "message" => "ID de perfil não fornecido."));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Método não permitido."));
}
?>
