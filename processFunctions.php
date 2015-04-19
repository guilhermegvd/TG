<?php
function docx2text($filename) {
	$xml = readZipFile($filename, "word/document.xml");
	$content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $xml);
    $content = str_replace('</w:r></w:p>', " ", $content);
	$content = str_replace('<w:br/>', " ", $content);
	$content = str_replace('</w:p>', " ", $content);
    $striped_content = strip_tags($content);
    return $striped_content;
}

function odt2text($filename) {
	$xml = readZipFile($filename, "content.xml");
	preg_match_all('|<text:[ph].*>(.*)</text:[ph]>|U', $xml, $stream, PREG_PATTERN_ORDER);  
	foreach($stream[1] as $texto)
		$saida.= $texto . ' ';
	return strip_tags($saida);
}

function readZipFile($filename, $dataType) {
	
	$zip = new ZipArchive;
	if (true === $zip->open($filename)) {
    	if (($index = $zip->locateName($dataType)) !== false) {
        	$data = $zip->getFromIndex($index);
        	$zip->close();
        	$xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
			return $xml->saveXML();
    	}
    	$zip->close();
	}
	return "";
}

function indexar($palavra, $filename) {
			
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Erro alpha de conexao!');
	mysqli_query($dbc, "SET NAMES 'utf8'");
    mysqli_query($dbc, 'SET character_set_connection=utf8');
    mysqli_query($dbc, 'SET character_set_client=utf8');
    mysqli_query($dbc, 'SET character_set_results=utf8');
	
	$query = "SELECT id from TDOCUMENTO WHERE caminho='" . $filename . "'";
	$data = mysqli_query($dbc, $query);
	$row = mysqli_fetch_array($data);
	$idTDOCUMENTO = $row[0];
	
	$query = "SELECT id from TOCORRENCIA WHERE cdocumento=" . $idTDOCUMENTO . " LIMIT 1";
	$data = mysqli_query($dbc, $query);
	if($data) {
		if (mysqli_num_rows($data)==0) {
			
			foreach($palavra[1] as $p)
			if(trim($p)=='')
				continue;
			else
				echo $p . '<br />';
				
				$sqlString .= '(' . $idTDOCUMENTO . ', "' . $p . '"),';
				
	
			$sqlString = substr($sqlString, 0, -1);
	
			desativaIndices($dbc);
			$dbc->autocommit(FALSE);

			$query = "INSERT IGNORE INTO TOCORRENCIA (cdocumento, palavra) VALUES " . $sqlString;
    		$response = mysqli_query($dbc, $query);
			//echo $query;
			if($response) {
				$query = "UPDATE TDOCUMENTO SET statu = 1 WHERE id = " . $idTDOCUMENTO;
				$response = mysqli_query($dbc, $query);
				if($response) {
					$dbc->commit();
					echo 'A indexacao deste documento foi realizada com sucesso!';
				}
				else {
					$dbc->rollback();
				}
			}
			else {
				$dbc->rollback();
			}
	
			reativaIndices($dbc);
		}
		else echo 'Ja existe este documento na base de ocorrencias';
	}
	mysqli_close($dbc);
}

function desativaIndices(&$conexao) {
	mysqli_query($conexao, "ALTER TABLE 'TOCORRENCIA' DISABLE KEYS;");
	mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 0');
	mysqli_query($conexao, 'SET UNIQUE_CHECKS = 0');
}

function reativaIndices(&$conexao) {
	mysqli_query($conexao, 'SET UNIQUE_CHECKS = 1');
	mysqli_query($conexao, 'SET FOREIGN_KEY_CHECKS = 1');
	mysqli_query($conexao, "ALTER TABLE 'TOCORRENCIA' ENABLE KEYS;");
}

?>