<?php

if(isset($_POST['submit']))
{

    include_once('conexao.php');

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $telefone = $_POST['telefone'];
   

    $result = mysqli_query($conexao, "INSERT INTO usuarios(nome,email,senha,telefone) VALUES ('$nome','$email','$senha','$telefone')");
}

?>
