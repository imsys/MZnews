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
		<title>Notícias MZn²</title>
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
$mzn_path = dirname(__FILE__); require_once($mzn_path ."/mzn2.php"); $mzn_selfpage = $s->req['PHP_SELF'];

// Define $mzn2 como a classe com todas as funções de exibição do MZn²
$mzn2 = new MZn2_Noticias;

// Define a categoria que será usada
$mzn2->categoria = "principal";

// Define $mostrar como o campo 'mostrar' da query string
$mostrar = $s->req['mostrar'];

// Se não tem $mostrar
if (!$mostrar) {
	$mzn2->data = $s->req['mzn_data'];
	$mzn2->usuario = $s->req['mzn_usuario'];
	$mzn2->busca = $s->req['mzn_busca'];
	
	$mzn2->pagina = ($s->req['mzn_pg'] != 1)? ($s->req['mzn_pg'] * 2) : 1;
	$mzn2->porpagina = 5;
	echo "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Headlines</b></div>\n</div></div>";
	echo "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; \">\n<div style=\"text-align:left; \">";
	$mzn2->mostrar_manchetes();
	echo "</div></div>";
	
	echo "<br />";
	
	$mzn2->pagina = $s->req['mzn_pg'];
	$mzn2->porpagina = 10;
	$mzn2->mostrar_noticias();
	
	echo "<div align=\"center\">";
	$mzn2->mostrar_paginacao($mzn_selfpage ."?mzn_pg={pagina}", 1);
	echo"<br /><form style=\"margin-top:0px; margin-bottom:0px; \" action=\"". $mzn_selfpage ."\" method=\"get\"><input type=\"text\" name=\"mzn_busca\" value=\"". $s->quote_safe($s->req['mzn_busca']) ."\" size=\"20\" style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; \" />&nbsp;<input type=\"submit\" value=\"Buscar\" style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; font-weight:bold; \" />&nbsp;&middot;&nbsp;<a href=\"". $mzn_selfpage ."?mostrar=arquivo\">Mostrar arquivo</a></form></div>";
}

// Ou se $mostrar é igual a "noticia"
else if ($mostrar == "noticia") {
	$mzn2->noticia = $s->req['id'];
	$mzn2->mostrar_noticia();
	
	echo "<div align=\"center\"><a href=\"#\" onclick=\"history.back(); return false; \">Voltar</a></div>";
}

// Ou se $mostrar é igual a "noticiacompleta"
else if ($mostrar == "noticiacompleta") {
	$mzn2->noticia = $s->req['id'];
	$mzn2->mostrar_noticia_completa();
	
	echo "<div align=\"center\"><a href=\"#\" onclick=\"history.back(); return false; \">Voltar</a></div>";
}

// Ou se $mostrar é igual a "comentários"
else if ($mostrar == "comentarios") {
	
	// Define $acao como o campo 'acao' da query string
	$acao = $s->req['acao'];
	
	// Se não tem $acao
	if (!$acao) {
		echo "<div align=\"center\">";
		$mzn2->porpagina = 0;
		$mzn2->noticia = $s->req['id'];
		$mzn2->mostrar_noticia();
		
		echo "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Comentários</b></div>\n</div></div>";
		$mzn2->ordem = "crescente";
		$mzn2->mostrar_comentarios();
		
		echo "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Adicione o seu!</b></div>\n</div></div>";
		$mzn2->mostrar_formulario_comentario("mostrar=comentarios&acao=adicionar");
		
		echo "<div align=\"center\"><a href=\"#\" onclick=\"history.back(); return false; \">Voltar</a></div>";
		echo "</div>";
	}
	
	// Ou se $acao é igual a "adicionar"
	else if ($acao == "adicionar") {
		$mzn2->adicionar_comentario();
	}
}

// Ou se $mostrar é igual a "arquivo"
else if ($mostrar == "arquivo") {
	echo "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; text-align:left; \">\n\t<div style=\"font-size:8pt; text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Arquivo</b></div>";
	$mzn2->mostrar_arquivo($mzn_selfpage ."?mzn_data={data}");
	echo "</div></div>";
}

echo "\n"; ?>
		
	</body>
</html>
