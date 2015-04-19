<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/intrafatec/tg/scripts/connection.php');
	//unset($_SERVER['PHP_AUTH_USER']);
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
    	header('WWW-Authenticate: Basic realm="Intrafatec Upload Service Authentication"');
    	header('HTTP/1.0 401 Unauthorized');
    	echo 'Acesso Negado';
    	exit;
  	} else {
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Erro alpha de conexao!');
		$query = "SELECT A.id FROM TUSUARIO A inner join TSENHA B on A.id = B.id WHERE A.id=" . mysqli_real_escape_string($dbc, $_SERVER['PHP_AUTH_USER']) . " and B.senha='" . mysqli_real_escape_string($dbc, sha1(utf8_encode($_SERVER['PHP_AUTH_PW'])))  . "'";
		$data = mysqli_query($dbc, $query);
		$row = mysqli_fetch_array($data);
		mysqli_close($dbc);
		if(mysqli_num_rows($data) == 1)
		{
			// Acesso permitido
			$id_usr = $row['id'];
		}
		else
		{
			echo 'Credenciais invalidas';
			exit;	
  		}
  	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Intrafatec - Serviço de inclusão e pesquisa de TG</title>
</head>

<body>
<?php
	//Validacao de nome do trabalho
		if(isset($_POST["nome"]))
		{
			if($_POST["nome"] == '') 
			{
				echo '<h3>Informe o nome do trabalho!</h3>';
			}
			else
			{
				$nome = strip_tags($_POST["nome"]);
				//Validacao de documento        
       			if($_FILES["file"]["name"] != '')
        		{
        			$allowedExts = array("docx", "odt");
					$extension = end(explode(".", $_FILES["file"]["name"]));
					if (in_array($extension, $allowedExts))
  					{
						if ($_FILES["file"]["error"] == 0)
    					{
                			if($_FILES["file"]["size"] >= 1 && $_FILES["file"]["size"] <= (50*1048576))
                			{
                    			move_uploaded_file($_FILES["file"]["tmp_name"], "docs/" . sha1("file_" . $_FILES["file"]["name"] . "_usr_" . $id_usr . "_time_" . date('U')) . "." . $extension);
						
								$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Erro alpha de conexao!');
								mysqli_query($dbc, "SET NAMES 'utf8'");
     							mysqli_query($dbc, 'SET character_set_connection=utf8');
     							mysqli_query($dbc, 'SET character_set_client=utf8');
     							mysqli_query($dbc, 'SET character_set_results=utf8');
    							$query = "INSERT INTO TDOCUMENTO (nome, caminho, cusuar_incl) VALUES('" . mysqli_real_escape_string($dbc, strip_tags($_POST["nome"])) . "', '" . sha1("file_" . $_FILES["file"]["name"] . "_usr_" . $id_usr . "_time_" . date('U')) . "." . $extension . "', " . $id_usr . ")";
    							$response = mysqli_query($dbc, $query);
								if($response) { 
										echo '<h3>Upload efetuado com sucesso!</h3>';
										$nome = '';
									}
								else
									echo '<h3>Erro ao registrar o arquivo!</h3>';
				
							}
        	        		else
            	    			echo '<h3>O tamanho do arquivo deve estar entre 1B e 50 MiB!</h3>';
                		}
                	    else
                   	 		echo '<h3>Ocorreu um erro com o arquivo selecionado!</h3>';                 
            		
        			}
            		else
            			echo '<h3>O arquivo deve ter formato DOCX ou ODT!</h3>';
        		} 
				else
					echo '<h3>Informe o arquivo de TG a ser incluído!</h3>';
			}
		}
?>
<form method="post" action="upload.php" enctype="multipart/form-data">
  <label>Nome do trabalho:</label>
  <input type="text" name="nome" value="<?php echo $nome; ?>" />
  <label>Arquivo:</label>
  <input type="file" name="file" id="file" />
  <input type="submit" value="Incluir trabalho" />
</form>

<?php

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Erro alpha de conexao!');
	mysqli_query($dbc, "SET NAMES 'utf8'");
    mysqli_query($dbc, 'SET character_set_connection=utf8');
    mysqli_query($dbc, 'SET character_set_client=utf8');
    mysqli_query($dbc, 'SET character_set_results=utf8');
	$query = "SELECT nome, caminho, statu from TDOCUMENTO ORDER BY id ASC";
	$data = mysqli_query($dbc, $query);
	
	echo "<ul>";
	while ($row = mysqli_fetch_array($data)) {
    	echo '<li><a href="docs/' . $row[1] . '">' . $row[0] . ' - Statu: ' . $row[2] . '</a>' . ($row[2] != 1 ? '<a href="scripts/process.php?tg=' . $row[1] . '"><button type="button">Indexar</button></a>' : '') . '</li>';
	}
	echo "</ul>";
	mysqli_close($dbc);
	
?>

</body>
</html>