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
		<title>Enviar notícia por e-mail</title>
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

$mzn_path = dirname(__FILE__); require_once($mzn_path ."/mzn2.php");

$mzn2 = new MZn2_Noticias;
$mzn2->categoria = "principal";

$acao = $s->req['acao'];

if (!$acao) {
	echo "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Enviar notícia por e-mail</b></div>\n</div>";
	$mzn2->noticia = $s->req['id'];
	$mzn2->mostrar_formulario_email("acao=enviar", "voltar");
	echo "</div>";
}
else if ($acao == "enviar") {
	echo "<div align=\"center\"><b>";
	$mzn2->enviar_email();
	echo "</b></div><div>&nbsp;</div><div align=\"center\"><a href=\"#\" onclick=\"history.go(-1); return false; \">Voltar ao formulário</a>&nbsp;&middot;&nbsp;<a href=\"#\" onclick=\"history.go(-2); return false; \">Voltar à notícia</a></div>";
}

echo "\n"; ?>
		
	</body>
</html>
