<?php 
	require_once($_SERVER['DOCUMENT_ROOT'] . '/intrafatec/backup/scripts/connection.php');
	require_once('searchFunctions.php');

	if(isset($_GET['q'])) {
		$termos = strip_tags($_GET['q']);
		$result = strtolower($termos);
		$result .= ' ';
	
		$result = preg_replace("[(\s[ao][s]?\s)|(\sde\s)|(\sante\s)|(\spara\s)|(\scontra\s)|(\spor\s)|(\sob\s)|(\sapós\s)|(\sperante\s)|(\sentre\s)|(\scom\s)|(\sdesde\s)|(\sem\s)|(\ssem\s)|(\sob\s)|(\strás\s)|(\saté\s)|(\sper\s)|(\sd[ao][s]?\s)|(\sé\s)|(\se\s)|(\s-\s)|(\sn[oa][s]?\s)|(\sque\s)|(-{2,})|([\.:;,!?\"\[\]\(\)\{\}_])]i", " ", $result);
		$result .= ' ';
		
		preg_match_all('|(.*)\s|U', $result, $palavras, PREG_PATTERN_ORDER);  
		$resultados = buscar($palavras);
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Intrafatec - Serviço de inclusão e pesquisa de TG</title>
</head>
<body>

<form method="get" action="search.php">
  <label>Palavras-chave:</label>
  <input type="text" name="q" value="<?php echo $termos; ?>" />
  <input type="submit" value="Pesquisar" />
</form>

<?php
	if(isset($_GET['q'])) {
		while ($row = mysqli_fetch_array($resultados))
			echo '<h3><a href="docs/' . $row[2] . '">' . $row[1] . '</a></h3>';
	}
?>

</body>
</html>