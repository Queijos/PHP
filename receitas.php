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
$con = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Verifica o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // PEGANDO AS INFORMAÇÕES DAS RECEITAS DO BANCO DE DADOS
        if (isset($_GET['receitaid'])) {
            $id = $_GET['receitaid'];
            $sql = $con->query("SELECT r.*, c.nome AS usuario_nome FROM receitas r JOIN cliente c ON r.usuario_id = c.id WHERE r.receitaid='$id'");
            $data = $sql->fetch_assoc();
        } else {
            $data = array();
            $sql = $con->query("SELECT r.*, c.nome AS usuario_nome FROM receitas r JOIN cliente c ON r.usuario_id = c.id");
            while ($d = $sql->fetch_assoc()) {
                $data[] = $d;
            }
        }
        exit(json_encode($data));
        break;

    case 'PUT':
        // ALTERAR INFORMAÇÕES DA RECEITA
        parse_str(file_get_contents("php://input"), $put_vars);

        if (isset($put_vars['receitaid'])) {
            $id = $put_vars['receitaid'];
            $titulo = $put_vars['titulo'];
            $descricao = $put_vars['descricao'];
            $imagem = $put_vars['imagem'];

            $sql = $con->prepare("UPDATE receitas SET titulo=?, descricao=?, imagem=? WHERE receitaid=?");
            $sql->bind_param("sssi", $titulo, $descricao, $imagem, $id);
            $result = $sql->execute();
            if ($result) {
                exit(json_encode(array('status'=> 'success')));
            } else {
                exit(json_encode(array('status'=> 'Não Funcionou')));
            }
        }
        break;

    case 'POST':
        // GRAVAR INFORMAÇÕES DA RECEITA
        if (isset($_FILES['imagem'])) {
            $usuario_id = $_POST['usuario_id'];
            $titulo = $_POST['titulo'];
            $descricao = $_POST['descricao'];
            
            // Diretório onde a imagem será salva
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES["imagem"]["name"]);

            // Move o arquivo para o diretório de destino
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                $sql = $con->prepare("INSERT INTO receitas(usuario_id, titulo, descricao, imagem) VALUES (?, ?, ?, ?)");
                $sql->bind_param("isss", $usuario_id, $titulo, $descricao, $target_file);
                $result = $sql->execute();
                if ($result) {
                    $data = array(
                        "receitaid" => $con->insert_id,
                        "usuario_id" => $usuario_id,
                        "titulo" => $titulo,
                        "descricao" => $descricao,
                        "imagem" => $target_file
                    );
                    exit(json_encode($data));
                } else {
                    exit(json_encode(array("status" => "Não Funcionou", "error" => $con->error)));
                }
            } else {
                exit(json_encode(array("status" => "Erro ao fazer upload da imagem")));
            }
        } else {
            exit(json_encode(array("status" => "Erro: Nenhuma imagem enviada")));
        }
        break;

    case 'DELETE':
        // APAGAR INFORMAÇÕES DA RECEITA DO BANCO
        parse_str(file_get_contents("php://input"), $delete_vars);

        if (isset($delete_vars['receitaid'])) {
            $id = $delete_vars['receitaid'];
            $sql = $con->prepare("DELETE FROM receitas WHERE receitaid=?");
            $sql->bind_param("i", $id);
            $result = $sql->execute();
            if ($result) {
                exit(json_encode(array('status'=> 'success')));
            } else {
                exit(json_encode(array('status'=> 'Não Funcionou')));
            }
        }
        break;

    default:
        echo json_encode(array('status' => 'Erro: Método não suportado'));
        break;
}

// Fecha a conexão com o banco de dados
$con->close();
?>
