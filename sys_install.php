<?php

// Verificações de versão
if (false) { ?><!-- MZn2 - Sem suporte --><div style="font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; color:#CC0000; background-color:#FFFFFF; padding:8px; padding-left:10px; margin:5px; margin-top:10px; "><h4 style="margin:0px; ">MZn&sup2; - Sem suporte a PHP</h4>O MZn² detectou que o host para o qual que você enviou o sistema não oferece suporte à tecnologia PHP.<br />O MZn² só funciona em hosts com suporte à PHP 4.2 no mínimo.</div><noframes><?php exit; }
$ver = str_replace(".", "", phpversion()); if ($ver < 420) { ?><!-- MZn2 - Versão incompatível --><div style="font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; color:#CC0000; background-color:#FFFFFF; padding:8px; padding-left:10px; margin:5px; margin-top:10px; "><h4 style="margin:0px; ">MZn&sup2; - Versão incompatível</h4>O MZn² detectou que o host para o qual que você enviou o sistema está usando uma versão não suportada da tecnologia PHP.<br />O MZn² só funciona em hosts com suporte à PHP 4.2 no mínimo.</div><?php exit; }

error_reporting(7); set_magic_quotes_runtime(0);

define("WsSys_Token", 1);

unset($s); unset($m); unset($p);

$AbsPath = dirname(__FILE__);
require_once $AbsPath ."/inc/g_global.php";
require_once $AbsPath ."/inc/g_config.php";
	$s = new WsSys;
	$s->cfg = $c;
	$s->debug = 1;

$old = 0;
if (@file_exists($s->cfg['file']['mzn2_safe'])) {header("Location: index.php"); exit; }
if (@file_exists($s->cfg['path']['data'] ."/config.php")) {$old = 1; }

require_once $AbsPath ."/inc/g_mzn2.php";
	$m = new MZn2;

require_once $AbsPath ."/inc/g_layout.php";
	$l = new Layout;


// Funções
function installLayout ($body) {
	global $s, $m, $l; $body = trim($body);
	$bdReplace = array();
	
	$html  = "PCFET0NUWVBFIGh0bWwgUFVCTElDICItLy9XM0MvL0RURCBYSFRNTCAxLjAgVHJhbnNpdGlvbmFsLy9FTiINCgkiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQoNCjxodG1sIHhtbG5zPSJodHRwOi8vd3d3";
	$html .= "LnczLm9yZy8xOTk5L3hodG1sIiB4bWw6bGFuZz0icHQiIGxhbmc9InB0Ij4NCgk8aGVhZD4NCgkJPHRpdGxlPk1abrIgMi4wIEFEViAtIEluc3RhbGHn4288L3RpdGxlPg0KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCjwhLS0NCkBpbXBvcnQgdXJsKCJpbWcve3Nr";
	$html .= "aW59L3N0eWxlLmNzcyIpOw0KLS0+DQo8L3N0eWxlPg0KPHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiIGxhbmd1YWdlPSJKYXZhU2NyaXB0IiBzcmM9Im16bjIuanMiPjwvc2NyaXB0Pg0KCTwvaGVhZD4NCgk8Ym9keSBvbmxvYWQ9ImxvYWRQYWdlKCk7ICIg";
	$html .= "bGVmdG1hcmdpbj0iMCIgcmlnaHRtYXJnaW49IjAiIHRvcG1hcmdpbj0iMCIgYm90dG9tbWFyZ2luPSIwIiBtYXJnaW53aWR0aD0iMCIgbWFyZ2luaGVpZ2h0PSIwIj4NCgkJDQoJCTxkaXYgaWQ9ImNvbnRhaW5lciI+DQoJCQ0KCQk8ZGl2IGlkPSJjb250ZW50cyI+";
	$html .= "DQoJCQ0KCQk8ZGl2IGlkPSJsb2dvIiBvbmNsaWNrPSJ3aW5kb3cub3BlbignaHR0cDovL3d3dy5tem5ld3Mua2l0Lm5ldCcsICdfYmxhbmsnKTsgIj4NCgkJCTxpZnJhbWUgaWQ9Im16bkJhbm5lciIgbmFtZT0ibXpuQmFubmVyIiBzcmM9ImFib3V0OmJsYW5rIiBm";
	$html .= "cmFtZWJvcmRlcj0iMCIgc2Nyb2xsaW5nPSJubyIgd2lkdGg9IjQ2OCIgaGVpZ2h0PSI2MCIgbWFyZ2lud2lkdGg9IjAiIG1hcmdpbmhlaWdodD0iMCI+PC9pZnJhbWU+DQoJCTwvZGl2Pg0KCQkNCgkJPGRpdiBpZD0ibWVudSI+DQoJCQk8ZGl2IGNsYXNzPSJtZW51";
	$html .= "Rmlyc3QiPjwvZGl2Pg0KCQkJPGRpdiBjbGFzcz0ibWVudUxhc3QiPjwvZGl2Pg0KCQk8L2Rpdj4NCgkJDQoJCTxkaXYgaWQ9InRpdGxlIiBzdHlsZT0iYmFja2dyb3VuZC1pbWFnZTp1cmwoaW1nL3tza2lufS90aXRfaW5zdGFsbC5qcGcpOyAiPjwvZGl2Pg0KCQkN";
	$html .= "CgkJPGRpdiBpZD0ic2l0ZSI+DQoJCQ0KPCEtLSBDb250ZfpkbyAtLT4NCntib2R5fQ0KPCEtLSBGaW0gY29udGX6ZG8gLS0+DQoJCQ0KCQk8L2Rpdj4NCgkJDQoJCTxkaXYgaWQ9ImJvdFR4dCI+DQoJCQk8Yj5TaXN0ZW1hIHByb2R1emlkbyBwb3IgPGEgaHJlZj0i";
	$html .= "aHR0cDovL3d3dy53c3RlYy52bzYubmV0LyIgdGFyZ2V0PSJfYmxhbmsiPldzVGVjPC9hPiAtIENvcHlyaWdodCCpIDIwMDMtMjAwNCA8YSBocmVmPSJodHRwOi8vd3d3Lm11bmRvLWRiei5jb20uYnIvIiB0YXJnZXQ9Il9ibGFuayI+TXVuZG8gREJaPC9hPjwvYj4N";
	$html .= "CgkJPC9kaXY+DQoJCTxkaXYgaWQ9ImJvdCIgb25jbGljaz0id2luZG93Lm9wZW4oJ2h0dHA6Ly93d3cubXpuZXdzLmtpdC5uZXQnLCAnX2JsYW5rJyk7ICI+PC9kaXY+DQoJCQ0KCQk8L2Rpdj4NCgkJDQoJCTwvZGl2Pg0KCQkNCgk8L2JvZHk+DQo8L2h0bWw+";
	$html = base64_decode($html);
	
	$instFinal = str_replace("{body}", $body, $html);
	
	$bdReplace["session"] = $s->req['s']; $bdReplace["skin"] = $s->cfg['skin'];
	$instFinal = $s->replace_vars($instFinal, $bdReplace);
	$instFinal = str_replace("<br>", "<br />", $instFinal);
	echo $instFinal; exit;
}

function installError ($msg) {
	global $s, $m, $l;
	$msg = $s->escape($msg);
	echo "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\"> alert(\"". $msg ."\"); history.back(); </scr"."ipt>"; exit;
}

$noPerms = 0;
if (!is_writable($s->cfg['path']['data'])) {$noPerms = 1; }


// Código de instalação
$act = $s->req['act'];

ob_start();


if ($noPerms) {
	$l->table(505);
	$l->tb_custom("<span class=\"important\"><b>Erro - Sem as permissões necessárias</b><br />O MZn² não pode ser atualizado pois não consegue escrever no diretório de dados. Altere as permissões do diretório <b>data</b> para Leitura, Escrita e Execução (CHMOD 777)</span>", 505, "center");
	$l->table_end();
}


else if (!$act) {
	$l->form("", "install", array(), "post", "formCenter", "onsubmit=\"if (!checkFields(this, 'c[site][name]', 'c[site][url]', 'c[user][login]', 'c[user][name]', 'c[user][mail]', 'c[user][pwd1]', 'c[user][pwd2]')) {alert('Por favor preencha todos os campos do formulário!'); return false; } if (!checkFields(this, 'c[site][url]:url')) {alert('A URL do seu site é inválida!\\nDigite uma URL como http://www.mznews.kit.net'); return false; } if (!checkFields(this, 'c[user][login]:sensitive')) {alert('O campo login contém caracteres inválidos!\\nUtilize apenas letras MINÚSCULAS, números e _ (underscore).'); return false; } if (!checkFields(this, 'c[user][mail]:mail')) {alert('O campo e-mail deve ser preenchido\\ncom um endereço de e-mail válido!'); return false; } if (!checkFields(this, 'c[user][pwd1]=c[user][pwd2]')) {alert('As senhas digitadas não coincidem!\\nPara evitar erros, digite a mesma senha nos dois campos.'); return false; } disableButtons('formCenter'); \"", "sys_install.php"); $l->table(505);
	if ($old) {$l->tb_custom("<span class=\"important\"><b>Atenção - Versão anterior detectada!</b><br />O instalador detectou uma versão anterior do MZn² instalada.<br />Prosseguindo com este instalador todos os seus dados anteriores serão perdidos!<br /><img src=\"img/_blank.gif\" width=\"1\" height=\"10\" border=\"0\" alt=\"\" /><br /><a href=\"sys_upgrade.php\">Para efetuar uma atualização e manter os dados da última versão, clique aqui.</a><br /><img src=\"img/_blank.gif\" width=\"1\" height=\"12\" border=\"0\" alt=\"\" /><br /></span>", 505, "center"); }
	$l->tb_group("Dados do seu site");
			$l->tb_input("text", "c[site][name]", "<b>Nome do site</b>", "", 505);
		$l->tb_nextrow();
			$l->tb_input("text", "c[site][url]", "<b>URL do site</b>", "", 505);
	$l->tb_group("Dados do administrador");
			$l->tb_input("text", "c[user][login]", "<b>Login</b>&nbsp;¹", "", 200);
			$l->tb_input("text", "c[user][name]", "<b>Nome</b>", "", 300);
		$l->tb_nextrow();
			$l->tb_input("text", "c[user][mail]", "<b>E-mail</b>", "", 505);
		$l->tb_nextrow();
			$l->tb_input("password", "c[user][pwd1]", "<b>Senha</b>", "", 250);
			$l->tb_input("password", "c[user][pwd2]", "<b>Confirme a senha</b>", "", 250);
	$l->tb_group("Estrutura do sistema");
			$l->tb_custom("<b>Observações</b><br />No MZn² 2.0 não há mais arquivos gerados (como o xNews.txt). Agora todas as notícias são exibidas através de includes em PHP. Se você tem dificuldade com includes em PHP, utilize a segunda opção (estrutura clássica), assim você poderá utilizar o MZn² 2.0 de uma maneira muito semelhante ao 1.0, bastando incluir o PHP <i>mznews/classic/news.php</i> por exemplo. Maiores informações visite o <a href=\"http://www.mznews.kit.net\" target=\"_blank\"><u>site do MZn²</u></a>.", 505);
		$l->tb_nextrow();
			$l->tb_select("c[tpl]", "<b>Que tipo de estrutura deseja utilizar?</b>", "1=Estrutura nova (usando o noticias.php, totalmente diferente do MZn² 1.0)|2=Estrutura clássica, semelhante à do MZn² 1.0 (arquivos na pasta mznews/classic)", "1", 505);
	$l->tb_caption("Preencha todos os campos do formulário<br />¹: Somente letras <b>minúsculas</b>, números e _ (underscore)");
	$l->tb_button("submit", "Instalar", array("accesskey" => "i"));
	$l->table_end(); $l->form_end();
}


else if ($act == "install") {
	if (!$s->req['c']['site']['name'] || !$s->req['c']['site']['url'] || !$s->req['c']['user']['login'] || !$s->req['c']['user']['name'] || !$s->req['c']['user']['mail'] || !$s->req['c']['user']['pwd1'] || !$s->req['c']['user']['pwd2']) {installError("Por favor preencha todos os campos do formulário."); }
	if (strpos($s->req['c']['site']['url'], "http://") !== 0) {installError("A URL do seu site é inválida!\nDigite uma URL como http://www.mznews.kit.net"); }
	if (!preg_match("/^[0-9a-z_]+$/", $s->req['c']['user']['login'])) {installError("O campo login contém caracteres inválidos!\nUtilize apenas letras MINÚSCULAS, números e _ (underscore)."); }
	if (strpos($s->req['c']['user']['mail'], "@") === FALSE || strpos($s->req['c']['user']['mail'], ".") === FALSE || strrpos($s->req['c']['user']['mail'], ".") <= strpos($s->req['c']['user']['mail'], "@")) {installError("O campo e-mail deve ser preenchido\ncom um endereço de e-mail válido!"); }
	if ($s->req['c']['user']['pwd1'] != $s->req['c']['user']['pwd2']) {installError("As senhas digitadas não coincidem!\nPara evitar erros, digite a mesma senha nos dois campos."); }
	
	// Cria o controle de instalação
	$s->file_write($s->cfg['file']['mzn2_safe'], "O MZn² está instalado neste site.");
	
	// Cria os bancos de dados
	$s->db_vars_create($s->cfg['file']['categories']);
	$s->db_table_create($s->cfg['file']['comments'], "id|cid|nid|time|title|comment|data:vars", 1);
	$s->db_vars_create($s->cfg['file']['config']);
	$s->db_table_create($s->cfg['file']['news'], "id|cid|time|user|title|news|fnews|data:vars", 1);
	$s->db_table_create($s->cfg['file']['uploads'], "id|name|size|time|user", 1);
	$s->db_table_create($s->cfg['file']['users'], "id|user|pwd|data:vars|perms:vars", 1);
	$s->db_table_create($s->cfg['file']['session'], "session|ip|time|data:vars", 1);
	$s->db_vars_create($s->cfg['file']['skin_cache']);
	
	// Cria a categoria principal
	$nl = array();
	$nl['principal']['name'] = "Principal";
	$nl['principal']['headlines']['cut'] = "40";
	$nl['principal']['headlines']['limit'] = "0";
	$nl['principal']['news']['cut'] = "30";
	$nl['principal']['news']['limit'] = "0";
	$nl['principal']['news']['default_align'] = "left";
	$nl['principal']['comments']['active'] = "1";
	$nl['principal']['comments']['mzncode'] = "0";
	$nl['principal']['comments']['smilies'] = "1";
	$nl['principal']['comments']['queue'] = "0";
	$nl['principal']['comments']['field1'] = "ICQ";
	$nl['principal']['comments']['field2'] = "MSN";
	$nl['principal']['comments']['limit_title'] = "30";
	$nl['principal']['comments']['limit_comment'] = "300";
	$nl['principal']['comments']['req_mail'] = "1";
	$nl['principal']['comments']['req_title'] = "1";
	$nl['principal']['templates']['headlines'] = "[{date:%d}/{date:%m}] <a href=\"#Noticia_{news:id}\" title=\"{news:title:nocut}\"><b>{news:title}</b></a><br />\n";
	$nl['principal']['templates']['news'] = "<a name=\"Noticia_{news:id}\"></a>\n<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; \">\n\t<div style=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \" title=\"{news:title:nocut}\"><b>{news:title}</b></div>\n\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{news:contents}</div>\n\t<div style=\"text-align:left; padding:2px 4px ";
	$nl['principal']['templates']['news'] .= "2px 4px; font-size:7pt; border:1px solid #808080; border-top:0px; \"><div style=\"float:right; \"><a href=\"imprimir.php?id={news:id}\" target=\"_blank\">Imprimir</a>&nbsp;&middot;&nbsp;<a href=\"email.php?id={news:id}\">Enviar por e-mail</a>&nbsp;&middot;&nbsp;<a href=\"{system:thispage}?mostrar=comentarios&id={news:id}\"><b>Comentários:</b> [{news:comments}]</a></div>Por <a href=\"mailto:{user:mail}\">{user:name}</a> às {date:%G}h{date:%i}</div>\n</div></div>\n<br />\n";
	$nl['principal']['templates']['fnews'] = "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Notícia de {date:%l}, {date:%j} de {date:%F} de {date:%Y}</b></div>\n</div></div>\n<a name=\"Noticia_{news:id}\"></a>\n<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; \">\n\t<div styl";
	$nl['principal']['templates']['fnews'] .= "e=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \" title=\"{news:title:nocut}\"><b>{news:title}</b></div>\n\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{news:full}</div>\n\t<div style=\"text-align:left; padding:2px 4px 2px 4px; font-size:7pt; border:1px solid #808080; border-top:0px; \"><div style=\"float:right; \"><a href=\"imprimir.php?id={news:id}\" target=\"_blank\">Imprimir</a>&nbsp;&mi";
	$nl['principal']['templates']['fnews'] .= "ddot;&nbsp;<a href=\"email.php?id={news:id}\">Enviar por e-mail</a>&nbsp;&middot;&nbsp;<a href=\"{system:thispage}?mostrar=comentarios&id={news:id}\"><b>Comentários:</b> [{news:comments}]</a></div>Por <a href=\"mailto:{user:mail}\">{user:name}</a> às {date:%G}h{date:%i}</div>\n</div></div>\n<br />\n";
	$nl['principal']['templates']['fnews_link'] = "<div>&nbsp;</div><div><a href=\"{system:thispage}?mostrar=noticiacompleta&id={news:id}\">&raquo; Ler notícia completa</a></div>";
	$nl['principal']['templates']['daygroup'] = "<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Notícias de {date:%l}, {date:%j} de {date:%F} de {date:%Y}</b></div>\n</div></div>\n{news}";
	$nl['principal']['templates']['comment'] = "<a name=\"Comentario_{comment:id}\"></a>\n<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; \">\n\t<div style=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \"><b>{comment:title}</b></div>\n\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{comment:contents}</div>\n\t<div style=\"text-align:left; padding:2px 4px 2px 4px; font-s";
	$nl['principal']['templates']['comment'] .= "ize:7pt; border:1px solid #808080; border-top:0px; \">Por <a href=\"mailto:{comment:mail}\">{comment:name}</a> em {comment:date}</div>\n</div></div>\n<br />\n";
	$nl['principal']['templates']['date'] = "%j-%m-%Y às %Gh%i";
	$nl['principal']['templates']['print'] = "<div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; \">\n\t<b>{news:title}</b>\n\t<hr size=\"1\" noshade color=\"#000000\" />\n\t{news:contents}\n\t<hr size=\"1\" noshade color=\"#000000\" />\n\tPor {user:name} ({user:mail}) em {news:date}<br />\n</div>\n";
	$nl['principal']['templates']['link'] = "<a href=\"{link:href}\" target=\"{link:target}\">{link:text}</a><br />";
	$nl['principal']['templates']['mailnews'] = "<html>\n\t<head>\n<style type=\"text/css\">\n<!--\na       {color:#000080; text-decoration:none; }\na:hover {color:#0000FF; text-decoration:none; }\n\nbody        {color:#000000; background-color:#FFFFFF; }\nbody, table {font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; }\n-->\n</style>\n\t</head>\n\t<body>\n\t\t\n\t\t<span style=\"font-size:14pt; \">Olá <b>{mail:to_name}</b>!</span><br />\n\t\t<br />\n\t\tSeu amigo(a) <a href=\"mailto:{mail:from_mail}\">{mail:from_name}</a> pensou que v";
	$nl['principal']['templates']['mailnews'] .= "ocê estivesse interessado em ler a seguinte notícia:<br />\n\t\t<br />\n\t\t<br />\n\t\t<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t\t\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Notícia de {date:%l}, {date:%j} de {date:%F} de {date:%Y}</b></div>\n\t\t</div></div>\n\t\t<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, s";
	$nl['principal']['templates']['mailnews'] .= "ans-serif; font-size:10pt; width:450px; \">\n\t\t\t<div style=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \" title=\"{news:title:nocut}\"><b>{news:title}</b></div>\n\t\t\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{news:contents}</div>\n\t\t\t<div style=\"text-align:left; padding:2px 4px 2px 4px; font-size:7pt; border:1px solid #808080; border-top:0px; \">Por <a href=\"mailto:{user:mail}\">{us";
	$nl['principal']['templates']['mailnews'] .= "er:name}</a> às {date:%G}h{date:%i}</div>\n\t\t</div></div>\n\t\t<br />\n\t\t<br />\n\t\tA resposta deste e-mail será enviada para o seu amigo(a).<br />\n\t\t<br />\n\t\t<br />\n\t\t<a href=\"http://www.mznews.kit.net\">Este e-mail foi gerado pelo MZn²</a><br />\n\t\t\n\t\t\n\t</body>\n</html>";
	if ($s->req['c']['tpl'] == 2) {
		$nl['principal']['templates']['news'] = "<a name=\"Noticia_{news:id}\"></a>\n<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; \">\n\t<div style=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \" title=\"{news:title:nocut}\"><b>{news:title}</b></div>\n\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{news:contents}</div>\n\t<div style=\"text-align:left; padding:2px ";
		$nl['principal']['templates']['news'] .= "4px 2px 4px; font-size:7pt; border:1px solid #808080; border-top:0px; \"><div style=\"float:right; \"><a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/print.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Imprimir</a>&nbsp;&middot;&nbsp;<a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/sendmail.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Enviar por e-mail</a>&nbsp;&middot;&nbsp;<a hr";
		$nl['principal']['templates']['news'] .= "ef=\"#\" title=\"{news:title:nocut}\" onclick=\"window.open('{system:mzn2dir}/classic/comments.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \"><b>Comentários:</b> [{news:comments}]</a></div>Por <a href=\"mailto:{user:mail}\">{user:name}</a> em {news:date}</div>\n</div></div>\n<br />\n";
		$nl['principal']['templates']['fnews'] = "<a name=\"Noticia_{news:id}\"></a>\n<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; width:450px; \">\n\t<div style=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \" title=\"{news:title:nocut}\"><b>{news:title}</b></div>\n\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{news:full}</div>\n\t<div style=\"text-align:left; padding:2px 4px ";
		$nl['principal']['templates']['fnews'] .= "2px 4px; font-size:7pt; border:1px solid #808080; border-top:0px; \"><div style=\"float:right; \"><a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/print.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Imprimir</a>&nbsp;&middot;&nbsp;<a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/sendmail.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Enviar por e-mail</a>&nbsp;&middot;&nbsp;<a href=\"";
		$nl['principal']['templates']['fnews'] .= "#\" title=\"{news:title:nocut}\" onclick=\"window.open('{system:mzn2dir}/classic/comments.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \"><b>Comentários:</b> [{news:comments}]</a></div>Por <a href=\"mailto:{user:mail}\">{user:name}</a> em {news:date}</div>\n</div></div>\n<br />\n";
		$nl['principal']['templates']['fnews_link'] = "<div>&nbsp;</div><div><a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/view.php?type=fnews&id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">&raquo; Ler notícia completa</a></div>";
		$nl['principal']['templates']['daygroup'] = "{news}";
	}
	$s->db_vars_save($s->cfg['file']['categories'], $nl);
	
	// Define as configurações
	$nl = array();
	$nl['site']['name'] = $s->req['c']['site']['name'];
	$nl['site']['url'] = $s->req['c']['site']['url'];
	$nl['skin'] = "blackfog";
	$nl['queue']['popup'] = "1";
	$nl['edv'] = "1";
	$nl['lostpwd'] = "1";
	$nl['time']['adjust'] = "0";
	$nl['edit']['perpage'] = "30";
	$nl['upload']['maxsize'] = "40960";
	$nl['upload']['extensions'] = "gif,jpg,jpeg,png,htm,html,txt,ini";
	$nl['cfield']['field1'] = "MSN";
	$nl['cfield']['field2'] = "";
	$nl['cfield']['field3'] = "";
	$nl['filter']['news'] = "0";
	$nl['filter']['comments'] = "1";
	$nl['filter']['list'] = "merd*=CENSURADO\ncaral*=CENSURADO\nfoda*=CENSURADO\nporr*=CENSURADO\nput*=CENSURADO\ncu*=CENSURADO\nbost*=CENSURADO";
	$nl['visitor']['floodint'] = "30";
	$nl['visitor']['blockip'] = "";
	$nl['visitor']['lock'] = "1";
	$nl['visitor']['lock_custom'] = "mzn*";
	$nl['version'] = "2.0.00";
	$nl['sm']['list'] = "%3A-%29:%7B28eca%7D%2Fsmile.gif;%3A-D:%7B28eca%7D%2Fbiggrin.gif;%3B-%29:%7B28eca%7D%2Fwink.gif;8-%29:%7B28eca%7D%2Fcool.gif;%3A-p:%7B28eca%7D%2Fsilly.gif;%3A-%28:%7B28eca%7D%2Ffrown.gif;%3E-%28:%7B28eca%7D%2Fmad.gif;%3A%3F%3F%3F%3A:%7B28eca%7D%2Fconfused.gif;%3Aabraco%3A:%7B28eca%7D%2Fhug.gif;%3Aabracoemgrupo%3A:%7B28eca%7D%2Fgrouphug.gif;%3Aabobora%3A:%7B28eca%7D%2Fpumpkin.gif;%3Aalien%3A:%7B28eca%7D%2Falien.gif;%3Aanjo%3A:%7B28eca%7D%2Fangel.gif;%3Aapaixonados%3A:%7B28eca%7D%2Fcouple.gif;%3Aba";
	$nl['sm']['list'] .= "bando%3A:%7B28eca%7D%2Fdrool.gif;%3Abanana%3A:%7B28eca%7D%2Fbanana.gif;%3Abebado%3A:%7B28eca%7D%2Fdrunk.gif;%3Abeijo%3A:%7B28eca%7D%2Fkiss.gif;%3Abocagrande%3A:%7B28eca%7D%2Fbigmouth.gif;%3Abruxa%3A:%7B28eca%7D%2Fwitch.gif;%3Acaveira%3A:%7B28eca%7D%2Fskull.gif;%3Acabecadeovo%3A:%7B28eca%7D%2Fegghead.gif;%3Acensurado%3A:%7B28eca%7D%2Fcensored.gif;%3Acereja%3A:%7B28eca%7D%2Fcherry.gif;%3Achato%3A:%7B28eca%7D%2Fbored.gif;%3Achorando%3A:%7B28eca%7D%2Fcrying.gif;%3Acongelado%3A:%7B28eca%7D%2Fcold.gif";
	$nl['sm']['list'] .= ";%3Acoracao%3A:%7B28eca%7D%2Fvalentine.gif;%3Acoracaopartido%3A:%7B28eca%7D%2Fbrokenheart.gif;%3Acoracoes%3A:%7B28eca%7D%2Fhearts.gif;%3Acupido%3A:%7B28eca%7D%2Fcupidarrow.gif;%3Ademonio%3A:%7B28eca%7D%2Fdevil.gif;%3Adentrodacaixa%3A:%7B28eca%7D%2Fboxedin.gif;%3Aduh%3A:%7B28eca%7D%2Fboggled.gif;%3Aeca%3A:%7B28eca%7D%2Fyuck.gif;%3Aenfezado%3A:%7B28eca%7D%2Fgrumpy.gif;%3Aenvergonhado%3A:%7B28eca%7D%2Fblush.gif;%3Afantasma%3A:%7B28eca%7D%2Fghost.gif;%3Afeliz%3A:%7B28eca%7D%2Fcheerful.gif;%3Afranken";
	$nl['sm']['list'] .= "stein%3A:%7B28eca%7D%2Ffrankenstein.gif;%3Agasp%3A:%7B28eca%7D%2Fgasp.gif;%3Agatopreto%3A:%7B28eca%7D%2Fblackcat.gif;%3Agravatinha%3A:%7B28eca%7D%2Fbowtie.gif;%3Agrrr%3A:%7B28eca%7D%2Fgnasher.gif;%3Ahumm%3A:%7B28eca%7D%2Ftounge-in-cheek.gif;%3Aladonegro%3A:%7B28eca%7D%2Fdarkside.gif;%3Alimao%3A:%7B28eca%7D%2Flemon.gif;%3Alua%3A:%7B28eca%7D%2Fmoon.gif;%3Amaluco%3A:%7B28eca%7D%2Fcrazy.gif;%3Amumia%3A:%7B28eca%7D%2Fmummy.gif;%3Anuvem9%3A:%7B28eca%7D%2Fcloud9.gif;%3Aolhosgrandes%3A:%7B28eca%7D%2Fbig";
	$nl['sm']['list'] .= "eyes.gif;%3Apao%3A:%7B28eca%7D%2Fbread.gif;%3Apenaboca%3A:%7B28eca%7D%2Ffootinmouth.gif;%3Aquadrado%3A:%7B28eca%7D%2Fblockhead.gif;%3Aqueijo%3A:%7B28eca%7D%2Fcheese.gif;%3Asarcastico%3A:%7B28eca%7D%2Fsarcastic.gif;%3Asemexpressao%3A:%7B28eca%7D%2Fexpressionless.gif;%3Asnif%3A:%7B28eca%7D%2Fashamed.gif;%3Atapandoosolhos%3A:%7B28eca%7D%2Fcovereyes.gif;%3Atomate%3A:%7B28eca%7D%2Ftomato.gif;%3Atorrada%3A:%7B28eca%7D%2Ftoast.gif;%3Atriangulo%3A:%7B28eca%7D%2Fconehead.gif;%3Atumulo%3A:%7B28eca%7D%2Fto";
	$nl['sm']['list'] .= "mbstone.gif;%3Aumolho%3A:%7B28eca%7D%2Fcyclops.gif;%3Auou%3A:%7B28eca%7D%2Feek.gif;%3Avampiro%3A:%7B28eca%7D%2Fvampire.gif;%3Azumbi%3A:%7B28eca%7D%2Fzombie.gif;%3Azzz%3A:%7B28eca%7D%2Fzzz.gif";
	$nl['sm']['packs'] = "28eca:http%3A%2F%2Fwww.mznews.kit.net%2Fsmilies%2Fpack2";
	$s->db_vars_save($s->cfg['file']['config'], $nl);
	
	// Cria a notícia inicial
	$db = $s->db_table_open($s->cfg['file']['news']);
	$nl = array();
	$nl['id'] = substr(md5(rand()*time()), 0, 10);
	$nl['cid'] = "principal";
	$nl['time'] = time();
	$nl['user'] = $s->req['c']['user']['login'];
	$nl['title'] = "Bem vindo ao MZn² 2.0 ADV!";
	$nl['news'] = "[align=left][b][size=3]Parabéns ". $s->req['c']['user']['name'] ."![/size][/b][/align]\n[align=left]&nbsp;[/align]\n[align=left]O [b]MZn²[/b] foi instalado com sucesso no seu site! [img=absMiddle]http://www.mznews.kit.net/smilies/evolution/cool.gif[/img][/align]\n[align=left]&nbsp;[/align]\n[align=left]Visite o nosso site: [url=http://www.mznews.kit.net,self]http://www.mznews.kit.net[/url][/align]";
	$nl['fnews'] = "";
	$nl['data']['nm'] = "h";
	$nl['data']['fm'] = "h";
	$nl['data']['o'] = "0";
	$nl['data']['b'] = "1";
	$nl['data']['c'] = "0";
	$nl['data']['s'] = "1";
	$db['data'][count($db['data'])] = $nl;
	$s->db_table_save($s->cfg['file']['news'], $db);
	
	// Cria a sessão inicial
	$session = md5(rand()*time());
	setcookie("s", $session, time() + 1200);
	
	$db = $s->db_table_open($s->cfg['file']['session']);
	$nl = array();
	$nl['session'] = $session;
	$nl['ip'] = $s->req['REMOTE_ADDR'];
	$nl['time'] = time();
	$nl['data']['unique'] = substr(md5(rand()*time()), 0, 10);
	$nl['data']['cat'] = "principal";
	$nl['data']['user'] = $s->req['c']['user']['login'];
	$nl['data']['auth'] = md5("WsSys LOGIN ". md5($s->req['c']['user']['pwd1']));
	$db['data'][count($db['data'])] = $nl;
	$s->db_table_save($s->cfg['file']['session'], $db);
	
	// Cria o usuário inicial
	$db = $s->db_table_open($s->cfg['file']['users']);
	$nl = array();
	$nl['id'] = substr(md5(rand()*time()), 0, 10);
	$nl['user'] = $s->req['c']['user']['login'];
	$nl['pwd'] = md5($s->req['c']['user']['pwd1']);
	$nl['data']['name'] = $s->req['c']['user']['name'];
	$nl['data']['mail'] = $s->req['c']['user']['mail'];
	$nl['data']['active'] = "1";
	$nl['data']['posts'] = "1";
	$nl['data']['icq'] = "";
	$nl['data']['field1'] = "";
	$nl['data']['field2'] = "";
	$nl['data']['field3'] = "";
	$nl['data']['upload_maxsize'] = "2097152";
	$nl['data']['upload_extensions'] = "*";
	$nl['data']['noedv'] = "0";
	$nl['data']['usequeue'] = "0";
	$nl['data']['lastlogin'] = time();
	$nl['data']['lastpost'] = time();
	$nl['perms']['admin'] = "1";
	$db['data'][count($db['data'])] = $nl;
	$s->db_table_save($s->cfg['file']['users'], $db);
	
	header("Location: index.php?s=". $session ."&msg=". urlencode("MZn² instalado com sucesso!"));
	
}


$contents = ob_get_contents();
ob_end_clean();

installLayout($contents);

?>
