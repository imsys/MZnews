<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title>MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a       {color:#000000; text-decoration:none; }
a:hover {color:#000000; text-decoration:underline; }

body        {color:#000000; background-color:#FFFFFF; }
body, table {font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; }
-->
</style>
	</head>
	<body>
		
<?php

// Define as variáveis iniciais e chama a classe de exibição do MZn²
$mzn_path = dirname(__FILE__); $mzn_path = str_replace("/classic:EOF:", "", $mzn_path .":EOF:"); $mzn_path = str_replace("\\classic:EOF:", "", $mzn_path .":EOF:"); $mzn_path = str_replace(":EOF:", "", $mzn_path); require_once($mzn_path ."/mzn2.php"); $mzn_selfpage = $s->req['PHP_SELF'];

// Define $mzn2 como a classe com todas as funções de exibição do MZn²
$mzn2 = new MZn2_Noticias;

// Define a categoria que será usada
$mzn2->categoria = "principal";

// Define $acao como o campo 'acao' da query string
$act = $s->req['act'];

// Se não tem $type
if (!$type) {
	echo "Erro - \$type não definido.";
}

// Ou se $type é igual a "news"
else if ($type == "news") {
	$mzn2->noticia = $s->req['id'];
	$mzn2->mostrar_noticia();
	
	echo "<div align=\"center\"><a href=\"#\" onclick=\"window.close(); return false; \">Fechar janela</a></div>";
}

// Ou se $type é igual a "fnews"
else if ($type == "fnews") {
	$mzn2->noticia = $s->req['id'];
	$mzn2->mostrar_noticia_completa();
	
	echo "<div align=\"center\"><a href=\"#\" onclick=\"window.close(); return false; \">Fechar janela</a></div>";
}

?>
		
	</body>
</html>
