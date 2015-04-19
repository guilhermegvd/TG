<?php
	set_time_limit(0); 
	require_once($_SERVER['DOCUMENT_ROOT'] . '/intrafatec/tg/scripts/connection.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/intrafatec/tg/scripts/processFunctions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Intrafatec - Serviço de indexação de TG</title>
</head>

<body>

<?php
	$filename = strip_tags($_GET["tg"]);
	$caminhoArquivo = $_SERVER['DOCUMENT_ROOT'] . '/intrafatec/tg/docs/' . $filename;
	//echo $caminhoArquivo;
	preg_match("#\.(.*)#", $caminhoArquivo, $extensao);
    switch ($extensao[1]) {
		//case "doc":
		//	$result = doc2text($caminhoArquivo); break;
		case "docx":
			$result = docx2text($caminhoArquivo); break;
		case "odt":
			$result = odt2text($caminhoArquivo); break;
		//case "pdf":
		//	$result = pdf2text($caminhoArquivo); break;
		//case "zip":
		//	$result = pages2text($caminhoArquivo); break;
	}
	
	$result= strtolower($result);
	$result .= ' ';
	
	$result = preg_replace("[(\s[ao][s]?\s)|(\sde\s)|(\s[ao][s]\s)|(\sante\s)|(\spara\s)|(\scontra\s)|(\spor\s)|(\ssob\s)|(\sapós\s)|(\sperante\s)|(\sentre\s)|(\scom\s)|(\sdesde\s)|(\sem\s)|(\ssem\s)|(\sob\s)|(\strás\s)|(\saté\s)|(\sper\s)|(\sd[ao][s]?\s)|(\sé\s)|(\se\s)|(\s-\s)|(\sn[oa][s]?\s)|(\sque\s)|(-{2,})|([\.:;,!?\"\[\]\(\)\{\}_])]i", " ", $result);
	
	//$result = $str = implode(' ',array_unique(explode(' ', $result)));
	$result;

	//preg_match_all('|(.*^\s)\s|U', $result, $palavra, PREG_PATTERN_ORDER);  
	//print_r(array_values ($palavra));
	
	$palavra = explode(" ", $result);	
			
	indexar($palavra, $filename);
	
?>

</body>
</html>
