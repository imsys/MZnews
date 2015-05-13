<?php /*

  ============================== Atenção ==============================
  Esta página é apenas um exemplo  de como o  MZn² pode funcionar.  Ela
  mostra  o  que  o  usuário  selecionar. Lembre-se que o sistema não é
  capaz  de  'adivinhar' o que você quer com  um  pequeno código,  essa
  seqüência  de  IFs  é  necessária.  Se você quer alterar  e  não sabe
  como, procure informações sobre a linguagem PHP.
  =====================================================================

*/ ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title>Imprimir notícia</title>
<style type="text/css">
<!--
a       {color:#000000; text-decoration:none; }
a:hover {color:#000000; text-decoration:underline; }

body        {color:#000000; background-color:#FFFFFF; }
body, table {font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; }
-->
</style>
	</head>
	<body onload="window.print(); ">
		
<?php

$mzn_path = dirname(__FILE__); require_once($mzn_path ."/mzn2.php");

$mzn2 = new MZn2_Noticias;
$mzn2->categoria = "principal";

$mzn2->noticia = $s->req['id'];
$mzn2->mostrar_noticia_para_impressao();

echo "\n"; ?>
		
	</body>
</html>
