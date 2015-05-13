<?php

/*

== Observações ==
Para trocar o número de headlines ou notícias exibidas,
altere o valor de $mzn2->porpagina para o número desejado
na sua respectiva linha (headlines: 48, notícias: 56).

*/

// Define as variáveis iniciais e chama a classe de exibição do MZn²
$mzn_path = dirname(__FILE__); $mzn_path = str_replace("/classic:EOF:", "", $mzn_path .":EOF:"); $mzn_path = str_replace("\\classic:EOF:", "", $mzn_path .":EOF:"); $mzn_path = str_replace(":EOF:", "", $mzn_path); require_once($mzn_path ."/mzn2.php"); $mzn_selfpage = $s->req['PHP_SELF'];

// Define $mzn2 como a classe com todas as funções de exibição do MZn²
$mzn2 = new MZn2_Noticias;

// Define a categoria que será usada
$mzn2->categoria = "principal";

// Redireciona caso esteja usando a estrutura nova
if (isset($s->req['mostrar'])) {header("Location: ../noticias.php?". $s->req['QUERY_STRING']); exit; }


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title>MZn² 2.0 ADV - Notícias</title>
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

// == Observações ==
// Para trocar o número de headlines
// exibidas, altere o valor abaixo.
$mzn2->porpagina = 6;

// Mostra as headlines
$mzn2->mostrar_headlines();

// == Observações ==
// Para trocar o número de notícias
// exibidas, altere o valor abaixo.
$mzn2->porpagina = 10;

// Mostra as headlines
$mzn2->mostrar_noticias();

echo "\n"; ?>
		
	</body>
</html>
