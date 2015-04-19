<?php
	
function buscar($palavra) {
	//$starttime = microtime(true);
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Erro alpha de conexao!');
	mysqli_query($dbc, "SET NAMES 'utf8'");
    mysqli_query($dbc, 'SET character_set_connection=utf8');
    mysqli_query($dbc, 'SET character_set_client=utf8');
    mysqli_query($dbc, 'SET character_set_results=utf8');
	
	foreach($palavra[1] as $p)
	if($p=='')
		continue;
	else {
		$sqlString .= 'palavra like "' . $p . '%" or ';
		$sqlTabelas .= "TOCORRENCIA ";
	}
	$sqlString = substr($sqlString, 0, -4);
	
	$query = "SELECT distinct(A.cdocumento), B.nome, B.caminho from TOCORRENCIA A inner join TDOCUMENTO B on A.cdocumento = B.id WHERE " . $sqlString;
	$data = mysqli_query($dbc, $query);
	return $data;
			
	mysqli_close($dbc);
	//$endtime = microtime(true);
	//echo ' Duracao: ' . date("H:i:s",$endtime-$starttime);
}
?>