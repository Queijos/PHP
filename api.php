<?php
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
	header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

	$con = new mysqli('localhost','root', '','e-studo');

	if($_SERVER['REQUEST_METHOD'] === 'GET'){
	//PEGANDO AS INFORMAÇÕES DO BANCO DE DADOS
	if(isset($_GET['id'])){
	// Este If é usado, caso passagem de id
	$id = $_GET['id'];
	$sql = $con->query("select * from cliente where id='$id'");
	$data = $sql->fetch_assoc();
	
	}else{
	//Entra nesse, caso não tenha passagem de ID via "get"
	$data = array();
	$sql = $con->query("select * from cliente");
	while($d = $sql->fetch_assoc()){
		$data[] = $d;
	}
}
	exit(json_encode($data));
}
	if($_SERVER['REQUEST_METHOD'] === 'PUT'){	
		//ALTERAR INFORMAÇÕES
		if(isset($_GET['id']))
		$id = $_GET['id'];
		$data = json_decode(file_get_contents("php://input"));
		$sql = $con->query("update cliente set
		nome = '".$data->nome."',
		email = '".$data->email."',
		telefone = '".$data->telefone."',
		senha = '".$data->senha."'
		where id = '$id'");
		if($sql){
			exit(json_encode(array('status'=> 'successo')));
		}else{
			exit(json_encode(array('status'=> 'Não Funcionou')));
		}


	}

	if($_SERVER['REQUEST_METHOD'] === 'POST'){
	// GRAVAR INFORMAÇÕES
	$data = json_decode(file_get_contents("php://input"));
	$sql = $con->query("insert into cliente(nome, email, telefone, senha) values ('".$data->nome."','".$data->email."','".$data->telefone."','".$data->senha."')");
	if($sql){
		$data->id = $con->insert_id;
		exit(json_encode($data));
	}else{
		exit(json_encode(array("status"=> "Não Funcionou")));
	}
}
	
	if($_SERVER["REQUEST_METHOD"] === 'DELETE'){
		//APAGAR INFORMAÇÕES DO BANCO
		if(isset($_GET['id'])){
			$id = $_GET['id'];
			$sql = $con->query("delete from cliente where id='$id'");
			if($sql){
				exit(json_encode(array('status'=> 'Sucesso')));
			}else{
				exit(json_encode(array('status'=> 'Não Funcionou')));
			}
		}
	}
?>
