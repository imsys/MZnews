<?php $p['tit'] = "index"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
if (!$s->usr['data']) {$m->location("sec=login"); }

$act = $s->req['act']; if (!$act) {$act = "index"; }

//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	$msg = "Há itens na fila de moderação!\\n"; $show = 0; $news = 0; $comments = 0;
	$db = $s->db_table_open($s->cfg['file']['news']);
	foreach ($db['data'] as $k => $v) {
		if ($v['data']['q']) {$news++; }
	}
	if ($news > 0) {$msg .= "\\nNotícias: ". $news; $show = 1; }
	$db = $s->db_table_open($s->cfg['file']['comments']);
	foreach ($db['data'] as $k => $v) {
		if ($v['data']['q']) {$comments++; $show = 1; }
	}
	if ($comments > 0) {$msg .= "\\nComentários: ". $comments; $show = 1; }
	if (!$s->sys['queue']['popup']) {$show = 0; }
	if ($show) {echo "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\">alert(\"". $msg ."\"); </scr"."ipt>"; }
	?><iframe src="index.php?s={session}&amp;sec=index&amp;act=welcome" width="100%" height="400" frameborder="no"></iframe><?php
}


//-----------------------------------------------------------------------------
// Act welcome
//-----------------------------------------------------------------------------
else if ($act == "welcome") { $designActive = 0; ?><!DOCTYPE html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>Bem vindo ao MZn²</title>
<style type="text/css">
<!--
a       {color:#000000; text-decoration:none; }
a:hover {color:#000000; text-decoration:underline; }

body        {color:#000000; background-color:#FFFFFF; }
body, table {font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; }

h1 {font-size:18pt; margin:0px; }
h2 {font-size:16pt; margin:0px; }
-->
</style>
<script type="text/javascript" language="JavaScript">
<!-- ;

function load () {
	var loc = location.href.toString(); loc = loc.substring(0, loc.lastIndexOf('/'));
	location.href = <?php echo "'http://". substr(md5(rand()*time()), 0, 5) .".mznews.kit.net/ext/welcome_20.html?siteName=". urlencode($s->sys['site']['name']) ."&siteURL=". urlencode($s->sys['site']['url']) ."&mznURL='+ loc +'&version=". urlencode($s->cfg['ver']['system']) ."&user=". urlencode($s->usr['data']['name']) ."'"; ?>;
}
function error () {
	var obj = document.getElementById('contents');
	obj.innerHTML = "<h2>Erro</h2><br /><b>Não foi possível localizar o servidor remoto!</b><br /><br />Ele pode estar offline ou você<br />não está conectado à Internet.";
}

// -->
</script>
	</head>
	<body>
		
		<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center" valign="middle" id="contents"><h1>Carregando...</h1></td></tr></table>
		<img src="http://www.mznews.kit.net/check.gif" onload="load(); " onerror="error(); " style="position:absolute; top:-10; left:-10; display:none; visibility:hidden; ">
		
	</body>
</html><?php }

