<?php 

require 'database.php';
$query = "select * from users";
if($is_query_run = mysqli_query($conexao, $query)) {
	$userData = [];
	while($query_executed = mysqli_fetch_assoc($is_query_run)){
		$userData[] = $query_executed;
	}
}
else {
	echo "Error";
}
echo json_encode($userData);
?>