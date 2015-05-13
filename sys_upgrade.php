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

if (@file_exists($s->cfg['file']['mzn2_safe'])) {header("Location: index.php"); exit; }
if (!@file_exists($s->cfg['path']['data'] ."/config.php")) {header("Location: sys_install.php"); exit; }

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

function db_vars_old ($file) {
	global $s;
	if (!file_exists($file)) {return array(); }
	$res = array();
	$tits = array();
	$contents = $s->file_read($file);
	$contents = explode("\n", $contents);
	foreach ($contents as $line) {
		$line = trim($line); if ($line == "") {continue; }
		if (preg_match("/^<\/[^>]+>$/", $line)) {
			$tit = preg_replace("/<\/([^>]+)>/i", "\\1", $line);
			if (preg_match("/(.*)\_(.*)/i", $tits[(count($tits)-1)]) && $tit == preg_replace("/\_(.*)$/i", "", $tits[(count($tits)-1)])) {unset($tits[(count($tits)-1)]); }
			else if ($tit == $tits[(count($tits)-1)]) {unset($tits[(count($tits)-1)]); }
		}
		else if (preg_match("/^<[^>]+>$/i", $line)) {$tits[count($tits)] = preg_replace("/<([^>]+)>/i", "\\1", $line); }
		else {
			$var = "\$res";
			foreach ($tits as $tit) {$var .= "['". $tit ."']"; }
			list ($name, $value) = explode("=", $line, 2);
			$name = trim($name); $value = trim($value);
			$name = preg_replace("/^\$/i", "", $name); $name = addslashes($name); $name = str_replace("\$", "\\$", $name);
			$value = addslashes($value); $value = str_replace("\\'", "'", $value); $value = str_replace("\$", "\\$", $value); $value = str_replace("\\\\n", "\\n", $value); $value = str_replace("\\\\t", "\\t", $value);
			eval($var ."['". $name ."'] = \"". $value ."\"; ");
		}
	}
	return $res;
}
function db_table_old ($file) {
	global $s;
	if (!file_exists($file)) {return array(); }
	$res = array();
	$db = trim($s->file_read($file, 1)); $db = explode("\n", $db);
	$i = 0; foreach ($db as $row) {
		$row = explode("|", $row);
		foreach ($row as $col) {
			$col = str_replace("¦", "|", $col); $col = str_replace("\\n", "\n", $col); $col = str_replace("\\t", "\t", $col); $res[$i][] = $col;
		}
	$i++; }
	return $res;
}
function db_rem ($file) {
	if (@file_exists($file)) {@unlink($file); }
}

$needChanges = 0;
$db = db_table_old($s->cfg['file']['users']);
foreach ($db as $k => $v) {
	if (!preg_match("/^[0-9a-z_]+$/", $v[0])) {$needChanges = 1; break; }
}

$noDBs = 0;
if (!file_exists($s->cfg['path']['data'] ."/config.php") || !file_exists($s->cfg['path']['data'] ."/database.php") || !file_exists($s->cfg['path']['data'] ."/users.php")) {$noDBs = 1; }

$noPerms = 0;
if (!is_writable($s->cfg['path']['data'])) {$noPerms = 1; }


// Código de atualização
$act = $s->req['act']; if (!$act) {$act = "index"; }

ob_start();


if ($noDBs) {
	$l->table(505);
	$l->tb_custom("<span class=\"important\"><b>Erro - Arquivos faltando</b><br />O MZn² não localizou os bancos de dados da versão anterior do MZn²!<br /><img src=\"img/_blank.gif\" width=\"1\" height=\"10\" border=\"0\" alt=\"\" /><br /><a href=\"sys_install.php\">Tente efetuar uma nova instalação.</a></span>", 505, "center");
	$l->table_end();
}


else if ($noPerms) {
	$l->table(505);
	$l->tb_custom("<span class=\"important\"><b>Erro - Sem as permissões necessárias</b><br />O MZn² não pode ser atualizado pois não consegue escrever no diretório de dados. Altere as permissões do diretório <b>data</b> para Leitura, Escrita e Execução (CHMOD 777)</span>", 505, "center");
	$l->table_end();
}


else if ($act == "index") {
	$db = db_table_old($s->cfg['file']['users']); $users = array(); $list = "";
	foreach ($db as $k => $v) {
		$v[2] = $s->vars_import($v[2]);
		if (!preg_match("/^[0-9a-z_]+$/", $v[0])) {$i = count($users); $users[$i] = $v; $users[$i]['k'] = $k; if ($list) {$list .= ", "; } $list .= "'user[". $k ."]:sensitive'"; }
	}
	
	$l->form("", "upgrade", array(), "post", "formCenter", "onsubmit=\"if (!checkFields(this, ". $list .")) {alert('O login de um dos usuários contém caracteres inválidos!\\nUtilize apenas letras MINÚSCULAS, números e _ (underscore).'); return false; } disableButtons('formCenter'); \"", "sys_upgrade.php"); $l->table(505);
	$num = "";
	if ($needChanges) {
		$num = 2;
		$l->tb_group("Mudança nos nomes de usuário");
				$l->tb_custom("<b>Atenção 1</b><br />No MZn² 2.0 os logins (nomes de usuário) só poderão conter letras <b>minúsculas</b>, números e _ (underscore). Os usuários abaixo só poderão ser adicionados ao MZn² 2.0 se seus logins forem corrigidos.", 505);
			$l->tb_nextrow();
			foreach ($users as $k => $v) {
				if (!$v[2]['email']) {$v[2]['email'] = "(sem e-mail)"; }
				$l->tb_input("text", "user[". $v['k'] ."]", "<b>". $v[2]['name'] ."</b> - E-mail: ". $v[2]['email'], $v[0], 505);
				$l->tb_nextrow();
			}
	}
	$l->tb_group("Avatares");
			$l->tb_custom("<b>Atenção ". $num ."</b><br />No MZn² 2.0 não há mais o sistema de avatares como na versão 1.0. Se você usa avatares e deseja mantê-los, marque a caixa abaixo. Observe que o terceiro campo personalizado dos usuários será perdido por causa disso, e usuários sem avatar ficarão com uma imagem quebrada.", 505);
		$l->tb_nextrow();
			$l->tb_check("checkbox", "keep_avatars", "<b>Manter os avatares dos usuários</b>", "1=Alterar as configurações para manter os avatares dos usuários", "", 505);
	$l->tb_group("Estrutura do sistema");
			$l->tb_custom("<b>Observações</b><br />No MZn² 2.0 não há mais arquivos gerados (como o xNews.txt). Agora todas as notícias são exibidas através de includes em PHP. Se você tem dificuldade com includes em PHP, utilize a segunda opção (estrutura clássica), assim você poderá utilizar o MZn² 2.0 de uma maneira muito semelhante ao 1.0, bastando incluir o PHP <i>mznews/classic/news.php</i> por exemplo. Maiores informações visite o <a href=\"http://www.mznews.kit.net\" target=\"_blank\"><u>site do MZn²</u></a>.", 505);
		$l->tb_nextrow();
			$l->tb_select("c[tpl]", "<b>Que tipo de estrutura deseja utilizar na adaptação dos links?</b>", "1=Estrutura nova (usando o noticias.php, totalmente diferente do MZn² 1.0)|2=Estrutura clássica, semelhante à do MZn² 1.0 (arquivos na pasta mznews/classic)", "1", 505);
	if ($needChanges) {$l->tb_caption("Altere todos os nomes de usuário"); }
	$l->tb_button("submit", "Atualizar", array("accesskey" => "a"));
	$l->table_end(); $l->form_end();
}


else if ($act == "upgrade") {
	$have_icq = 0;
	$db = db_table_old($s->cfg['file']['users']); $users = array();
	foreach ($db as $k => $v) {
		$v[2] = $s->vars_import($v[2]);
		if (!preg_match("/^[0-9a-z_]+$/", $v[0])) {if (!preg_match("/^[0-9a-z_]+$/", $s->req['user'][$k])) {installError("O login de um dos usuários contém caracteres inválidos!\nUtilize apenas letras MINÚSCULAS, números e _ (underscore)."); } }
		$users[$v[0]] = $s->req['user'][$k];
	}
	
	// Cria o controle de instalação
	$s->file_write($s->cfg['file']['mzn2_safe'], "O MZn² está instalado neste site.");
	
	// Lê os bancos de dados antigos
	$old_comments = db_table_old($s->cfg['path']['data'] ."/comments.php");
	$o_cfg = db_vars_old($s->cfg['path']['data'] ."/config.php");
	$old_database = db_table_old($s->cfg['path']['data'] ."/database.php");
	$old_smilies = db_table_old($s->cfg['path']['data'] ."/smilies.php");
	$old_uploads = db_table_old($s->cfg['path']['data'] ."/uploads.php");
	$old_users = db_table_old($s->cfg['path']['data'] ."/users.php");
	
	// Remove os bancos de dados obsoletos
	db_rem($s->cfg['path']['data'] ."/comments.php");
	db_rem($s->cfg['path']['data'] ."/config.php");
	db_rem($s->cfg['path']['data'] ."/database.php");
	db_rem($s->cfg['path']['data'] ."/session.php");
	db_rem($s->cfg['path']['data'] ."/smilies.php");
	db_rem($s->cfg['path']['data'] ."/uploads.php");
	db_rem($s->cfg['path']['data'] ."/users.php");
	
	// Cria os bancos de dados
	$s->db_vars_create($s->cfg['file']['categories']);
	$s->db_table_create($s->cfg['file']['comments'], "id|cid|nid|time|title|comment|data:vars", 1);
	$s->db_vars_create($s->cfg['file']['config']);
	$s->db_table_create($s->cfg['file']['news'], "id|cid|time|user|title|news|fnews|data:vars", 1);
	$s->db_table_create($s->cfg['file']['uploads'], "id|name|size|time|user", 1);
	$s->db_table_create($s->cfg['file']['users'], "id|user|pwd|data:vars|perms:vars", 1);
	$s->db_table_create($s->cfg['file']['session'], "session|ip|time|data:vars", 1);
	$s->db_vars_create($s->cfg['file']['skin_cache']);
	
	// Alterar modelos
	function tpl_change ($tpl, $type = "", $avatar = "") {
		global $s, $have_icq;
		$tpl = str_replace("{sys linkExt}", "{system:thispage}?MZn2", $tpl);
		$tpl = str_replace("{news content}", "{news:contents}", $tpl);
		$tpl = str_replace("{news dateFull}", "{news:date}", $tpl);
		$tpl = str_replace("{comments count}", "{news:comments}", $tpl);
		$tpl = str_replace("{comment content}", "{comment:contents}", $tpl);
		$tpl = str_replace("{comment dateFull}", "{comment:date}", $tpl);
		
		if ($avatar && $s->req['keep_avatars']) {
			$tpl = str_replace("{user avatar}", $avatar, $tpl);
			$tpl = str_replace("{avatar src}", "{user:field1}", $tpl);
		}
		else {$tpl = str_replace("{user avatar}", "", $tpl); }
		
		if ($type == "comment") {
			$tpl = str_replace("{user name}", "{comment:name}", $tpl);
			$tpl = str_replace("{user email}", "{comment:mail}", $tpl);
			$tpl = str_replace("{comment contents}", "{comment:field1}", $tpl);
			$tpl = str_replace("{user field1}", "{comment:field1}", $tpl);
			$tpl = str_replace("{user field2}", "{comment:field2}", $tpl);
			$tpl = str_replace("{user field3}", "", $tpl);
		}
		else {
			if ($s->req['c']['tpl'] == 1) {
				$tpl = str_replace("{system:thispage}?MZn2&act=comments&id={news id}", "{system:thispage}?mostrar=comentarios&id={news:id}", $tpl);
				$tpl = str_replace("{system:thispage}?MZn2&act=read_full&id={news id}", "{system:thispage}?mostrar=noticiacompleta&id={news:id}", $tpl);
			}
			else if ($s->req['c']['tpl'] == 2) {
				$tpl = str_replace("{system:thispage}?MZn2&act=comments&id={news id}", "{system:mzn2dir}/classic/comments.php?id={news:id}", $tpl);
				$tpl = str_replace("{system:thispage}?MZn2&act=read_full&id={news id}", "{system:mzn2dir}/classic/view.php?type=fnews&id={news:id}", $tpl);
			}
			$tpl = str_replace("{user user}", "{user:login}", $tpl);
			$tpl = str_replace("{user email}", "{user:mail}", $tpl);
			
			if ($have_icq == 1) {
				$tpl = str_replace("{user field1}", "{user icq}", $tpl);
				$tpl = str_replace("{user field2}", "{user field1}", $tpl);
				$tpl = str_replace("{user field3}", "{user field2}", $tpl);
			}
			else if ($have_icq == 2) {
				$tpl = str_replace("{user field2}", "{user icq}", $tpl);
				$tpl = str_replace("{user field3}", "{user field2}", $tpl);
			}
			else if ($have_icq == 3) {
				$tpl = str_replace("{user field3}", "{user icq}", $tpl);
			}
			
			if ($s->req['keep_avatars']) {
				$tpl = str_replace("{user field3}", "", $tpl);
				$tpl = str_replace("{user field2}", "{user field3}", $tpl);
				$tpl = str_replace("{user field1}", "{user field2}", $tpl);
			}
		}
		
		$tpl = preg_replace("/{([^ }]+) ([^}]+)}/U", "{\\1:\\2}", $tpl);
		return $tpl;
	}
	
	// Verifica se há ICQ e adapta os campos
	if ($o_cfg['pfields']['field1'] == "ICQ") {
		$have_icq = 1;
		$o_cfg['pfields']['field1'] = $o_cfg['pfields']['field2'];
		$o_cfg['pfields']['field2'] = $o_cfg['pfields']['field3'];
		$o_cfg['pfields']['field3'] = "";
	}
	else if ($o_cfg['pfields']['field2'] == "ICQ") {
		$have_icq = 2;
		$o_cfg['pfields']['field2'] = $o_cfg['pfields']['field3'];
		$o_cfg['pfields']['field3'] = "";
	}
	else if ($o_cfg['pfields']['field3'] == "ICQ") {
		$have_icq = 3;
		$o_cfg['pfields']['field3'] = "";
	}
	
	// Cria a categoria principal
	$nl = array();
	$nl['principal']['name'] = "Principal";
	$nl['principal']['headlines']['cut'] = $o_cfg['headlines']['maxchars'];
	$nl['principal']['news']['cut'] = $o_cfg['news']['maxchars'];
	$nl['principal']['news']['limit'] = "0";
	$nl['principal']['news']['default_align'] = "left";
	$nl['principal']['comments']['active'] = $o_cfg['comments']['active'];
	$nl['principal']['comments']['mzncode'] = $o_cfg['comments']['hcode'];
	$nl['principal']['comments']['smilies'] = $o_cfg['comments']['smilies'];
	$nl['principal']['comments']['queue'] = $o_cfg['comments']['queue'];
	$nl['principal']['comments']['field1'] = $o_cfg['comments']['field1'];
	$nl['principal']['comments']['field2'] = $o_cfg['comments']['field2'];
	$nl['principal']['comments']['limit_title'] = "30";
	$nl['principal']['comments']['limit_comment'] = "300";
	$nl['principal']['comments']['req_mail'] = "1";
	$nl['principal']['comments']['req_title'] = "1";
	$nl['principal']['templates']['headlines'] = tpl_change($o_cfg['templates']['headlines'], "headlines", $o_cfg['templates']['avatar']);
	$nl['principal']['templates']['news'] = tpl_change($o_cfg['templates']['news'], "news", $o_cfg['templates']['avatar']);
	$nl['principal']['templates']['fnews'] = tpl_change($o_cfg['templates']['news'], "news", $o_cfg['templates']['avatar']);
	$nl['principal']['templates']['fnews_link'] = tpl_change($o_cfg['templates']['full'], "fnews_link");
	$nl['principal']['templates']['daygroup'] = "{news}";
	$nl['principal']['templates']['comment'] = tpl_change($o_cfg['templates']['comments'], "comment");
	$nl['principal']['templates']['date'] = $o_cfg['templates']['date'];
	$nl['principal']['templates']['print'] = "<div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; \">\n\t<b>{news:title}</b>\n\t<hr size=\"1\" noshade color=\"#000000\" />\n\t{news:contents}\n\t<hr size=\"1\" noshade color=\"#000000\" />\n\tPor {user:name} ({user:mail}) em {news:date}<br />\n</div>\n";
	$nl['principal']['templates']['link'] = "<a href=\"{link:href}\" target=\"{link:target}\">{link:text}</a><br />";
	$nl['principal']['templates']['mailnews'] = "<html>\n\t<head>\n<style type=\"text/css\">\n<!--\na       {color:#000080; text-decoration:none; }\na:hover {color:#0000FF; text-decoration:none; }\n\nbody        {color:#000000; background-color:#FFFFFF; }\nbody, table {font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; }\n-->\n</style>\n\t</head>\n\t<body>\n\t\t\n\t\t<span style=\"font-size:14pt; \">Olá <b>{mail:to_name}</b>!</span><br />\n\t\t<br />\n\t\tSeu amigo(a) <a href=\"mailto:{mail:from_mail}\">{mail:from_name}</a> pensou que v";
	$nl['principal']['templates']['mailnews'] .= "ocê estivesse interessado em ler a seguinte notícia:<br />\n\t\t<br />\n\t\t<br />\n\t\t<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; width:450px; \">\n\t\t\t<div style=\"text-align:left; margin-bottom:2px; padding:2px; padding-left:4px; color:#FFFFFF; background-color:#808080; \"><b>Notícia de {date:%l}, {date:%j} de {date:%F} de {date:%Y}</b></div>\n\t\t</div></div>\n\t\t<div align=\"center\"><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, s";
	$nl['principal']['templates']['mailnews'] .= "ans-serif; font-size:10pt; width:450px; \">\n\t\t\t<div style=\"text-align:left; padding:2px; padding-left:4px; background-color:#EEEEEE; border:1px solid #808080; \" title=\"{news:title:nocut}\"><b>{news:title}</b></div>\n\t\t\t<div style=\"text-align:left; padding:4px; border-left:1px solid #808080; border-right:1px solid #808080; \">{news:contents}</div>\n\t\t\t<div style=\"text-align:left; padding:2px 4px 2px 4px; font-size:7pt; border:1px solid #808080; border-top:0px; \">Por <a href=\"mailto:{user:mail}\">{us";
	$nl['principal']['templates']['mailnews'] .= "er:name}</a> às {date:%G}h{date:%i}</div>\n\t\t</div></div>\n\t\t<br />\n\t\t<br />\n\t\tA resposta deste e-mail será enviada para o seu amigo(a).<br />\n\t\t<br />\n\t\t<br />\n\t\t<a href=\"http://www.mznews.kit.net\">Este e-mail foi gerado pelo MZn²</a><br />\n\t\t\n\t\t\n\t</body>\n</html>";
	$s->db_vars_save($s->cfg['file']['categories'], $nl);
	
	// Define as configurações
	$nl = array();
	$nl['site']['name'] = $o_cfg['site']['name'];
	$nl['site']['url'] = $o_cfg['site']['url'];
	$nl['skin'] = "blackfog";
	$nl['queue']['popup'] = $o_cfg['queue']['popup'];
	$nl['edv'] = "1";
	$nl['lostpwd'] = $o_cfg['lostpwd']['active'];
	$nl['time']['adjust'] = $o_cfg['time']['adjust'];
	$nl['edit']['perpage'] = $o_cfg['newsedit']['perpage'];
	$nl['upload']['maxsize'] = $o_cfg['upload']['maxsize'];
	$nl['upload']['extensions'] = $o_cfg['upload']['extensions'];
	
	if ($s->req['keep_avatars']) {
		$nl['cfield']['field1'] = "Avatar";
		$nl['cfield']['field2'] = $o_cfg['pfields']['field1'];
		$nl['cfield']['field3'] = $o_cfg['pfields']['field2'];
	}
	else {
		$nl['cfield']['field1'] = $o_cfg['pfields']['field1'];
		$nl['cfield']['field2'] = $o_cfg['pfields']['field2'];
		$nl['cfield']['field3'] = $o_cfg['pfields']['field3'];
	}
	
	$nl['filter']['news'] = "0";
	$nl['filter']['comments'] = "1";
	$nl['filter']['list'] = "merd*=CENSURADO\ncaral*=CENSURADO\nfoda*=CENSURADO\nporr*=CENSURADO\nput*=CENSURADO\ncu*=CENSURADO\nbost*=CENSURADO";
	$nl['visitor']['floodint'] = "30";
	$nl['visitor']['blockip'] = "";
	$nl['visitor']['lock'] = "1";
	$nl['visitor']['lock_custom'] = "mzn*";
	$nl['version'] = "2.0.00";
	$sm_list = array(); $sm_packs = array();
	foreach ($old_smilies as $k => $v) {
		$v[2] = $s->vars_import($v[2]); $v[3] = $s->vars_import($v[3]);
		if (!$v[2]['use']) {continue; }
		$id = substr(md5(rand()*time()), 0, 5);
		$sm_packs[$id] = $v[1];
		foreach ($v[3] as $sm => $img) {$sm_list[$sm] = "{". $id ."}/". $img; }
	}
	$nl['sm']['list'] = $s->vars_export($sm_list);
	$nl['sm']['packs'] = $s->vars_export($sm_packs);
	$s->db_vars_save($s->cfg['file']['config'], $nl);
	
	// Atualiza as notícias
	$db = $s->db_table_open($s->cfg['file']['news']);
	foreach ($old_database as $k => $v) {
		$nl = array(); $v[6] = $s->vars_import($v[6]);
		$nl['id'] = $v[0];
		$nl['cid'] = "principal";
		$nl['time'] = $v[1];
		$nl['user'] = $v[2]; if ($users[$v[2]]) {$nl['user'] = $users[$v[2]]; }
		$nl['title'] = $v[3];
		$nl['news'] = $v[4];
		$nl['fnews'] = $v[5];
		$nl['data']['nm'] = "h";
		$nl['data']['fm'] = "h";
		$nl['data']['o'] = "0";
		$nl['data']['b'] = $v[6]['nobr'];
		$nl['data']['c'] = $v[6]['nohcode'];
		$nl['data']['s'] = $v[6]['nosmilies'];
		$nl['data']['nc'] = $v[6]['nocomments'];
		$nl['data']['q'] = $v[6]['queue'];
		$db['data'][count($db['data'])] = $nl;
	}
	$s->db_table_save($s->cfg['file']['news'], $db);
	
	// Atualiza os comentários
	$db = $s->db_table_open($s->cfg['file']['comments']);
	foreach ($old_comments as $k => $v) {
		$nl = array(); $v[5] = $s->vars_import($v[5]);
		$nl['id'] = $v[0];
		$nl['cid'] = "principal";
		$nl['nid'] = $v[1];
		$nl['time'] = $v[2];
		$nl['title'] = $v[3];
		$nl['comment'] = $v[4];
		$nl['data']['n'] = $v[5]['name'];
		$nl['data']['m'] = $v[5]['email'];
		$nl['data']['i'] = $v[5]['ip'];
		$nl['data']['f1'] = $v[5]['field1'];
		$nl['data']['f2'] = $v[5]['field2'];
		$nl['data']['q'] = $v[5]['queue'];
		$db['data'][count($db['data'])] = $nl;
	}
	$s->db_table_save($s->cfg['file']['comments'], $db);
	
	// Atualiza os usuários
	$db = $s->db_table_open($s->cfg['file']['users']);
	foreach ($old_users as $k => $v) {
		$v[2] = $s->vars_import($v[2]); $v[3] = explode(",", $v[3]); foreach ($v[3] as $perm) {$v[3][$perm] = 1; }
		$nl = array();
		$nl['id'] = substr(md5(rand()*time()), 0, 10);
		$nl['user'] = $v[0]; if ($users[$v[0]]) {$nl['user'] = $users[$v[0]]; }
		$nl['pwd'] = $v[1];
		$nl['data']['name'] = $v[2]['name'];
		$nl['data']['mail'] = $v[2]['email'];
		$nl['data']['active'] = $v[2]['active'];
		$nl['data']['posts'] = $v[2]['posts'];
		
		// Permanência do ICQ
		if ($have_icq == 1) {
			$nl['data']['icq'] = $v[2]['field1'];
			$v[2]['field1'] = $v[2]['field2'];
			$v[2]['field2'] = $v[2]['field3'];
			$v[2]['field3'] = "";
		}
		else if ($have_icq == 2) {
			$nl['data']['icq'] = $v[2]['field2'];
			$v[2]['field2'] = $v[2]['field3'];
			$v[2]['field3'] = "";
		}
		else if ($have_icq == 3) {
			$nl['data']['icq'] = $v[2]['field3'];
			$v[2]['field3'] = "";
		}
		else {$nl['data']['icq'] = ""; }
		
		// Permanência do avatar
		if ($s->req['keep_avatars']) {
			$nl['data']['field1'] = $v[2]['avatar'];
			$nl['data']['field2'] = $v[2]['field1'];
			$nl['data']['field3'] = $v[2]['field2'];
		}
		
		// Se não há avatar, apenas traduz as chaves
		else {
			$nl['data']['field1'] = $v[2]['field1'];
			$nl['data']['field2'] = $v[2]['field2'];
			$nl['data']['field3'] = $v[2]['field3'];
		}
		
		$nl['data']['upload_maxsize'] = $v[2]['upload_maxsize'];
		$nl['data']['upload_extensions'] = $v[2]['upload_extensions'];
		$nl['data']['noedv'] = "0";
		$nl['data']['usequeue'] = $v[3]['usequeue'];
		$nl['data']['lastlogin'] = "0";
		$nl['data']['lastpost'] = "0";
		if ($v[3]['post'] && $v[3]['editown'] && $v[3]['editall'] && $v[3]['comments'] && $v[3]['config'] && $v[3]['templates'] && $v[3]['users'] && $v[3]['cgdate'] && $v[3]['usehtml'] && $v[3]['backup'] && $v[3]['smilies'] && $v[3]['upload'] && $v[3]['uplmng'] && $v[3]['editqueue']) {
			$nl['perms']['admin'] = "1";
		}
		else {
			$nl['perms']['all'] = "";
			$nl['perms']['general'] = "";
			if ($v[3]['post']) {$nl['perms']['all'] .= "post,"; }
			if ($v[3]['editown']) {$nl['perms']['all'] .= "editown,"; }
			if ($v[3]['editall']) {$nl['perms']['all'] .= "editall,"; }
			if ($v[3]['comments']) {$nl['perms']['all'] .= "comments,"; }
			if ($v[3]['cgdate']) {$nl['perms']['all'] .= "cgdate,"; }
			if ($v[3]['usehtml']) {$nl['perms']['all'] .= "usehtml,"; }
			if ($v[3]['templates']) {$nl['perms']['general'] .= "categories,"; }
			if ($v[3]['backup']) {$nl['perms']['general'] .= "backup,"; }
			if ($v[3]['config']) {$nl['perms']['general'] .= "config,"; }
			if ($v[3]['users']) {$nl['perms']['general'] .= "users,"; }
			if ($v[3]['smilies']) {$nl['perms']['general'] .= "smilies,"; }
			if ($v[3]['upload']) {$nl['perms']['general'] .= "upload,"; }
			if ($v[3]['uplmng']) {$nl['perms']['general'] .= "uplmng,"; }
			if ($v[3]['editqueue']) {$nl['perms']['general'] .= "editqueue,"; }
			$nl['perms']['all'] = preg_replace("/,$/", "", $nl['perms']['all']);
			$nl['perms']['general'] = preg_replace("/,$/", "", $nl['perms']['general']);
		}
		$db['data'][count($db['data'])] = $nl;
	}
	$s->db_table_save($s->cfg['file']['users'], $db);
	
	// Atualiza os uploads
	$db = $s->db_table_open($s->cfg['file']['uploads']);
	foreach ($old_uploads as $k => $v) {
		$nl = array();
		$nl['id'] = $v[0];
		$nl['name'] = $v[1];
		$nl['size'] = $v[2];
		$nl['time'] = $v[3];
		$nl['user'] = $v[4]; if ($users[$v[4]]) {$nl['user'] = $users[$v[4]]; }
		$db['data'][count($db['data'])] = $nl;
	}
	$s->db_table_save($s->cfg['file']['uploads'], $db);
	
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
	$db['data'][count($db['data'])] = $nl;
	$s->db_table_save($s->cfg['file']['session'], $db);
	
	// Altera os arquivos gerados para alertar o usuário
	$c_html = base64_decode("PCFET0NUWVBFIGh0bWwgUFVCTElDICItLy9XM0MvL0RURCBYSFRNTCAxLjAgVHJhbnNpdGlvbmFsLy9FTiINCgkiaHR0cDovL3d3dy53My5vcmcvVFIveGh0bWwxL0RURC94aHRtbDEtdHJhbnNpdGlvbmFsLmR0ZCI+DQoNCjxodG1sIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hodG1sIiB4bWw6bGFuZz0icHQiIGxhbmc9InB0Ij4NCgk8aGVhZD4NCgkJPHRpdGxlPk1abrIgLSBBdGVu5+NvPC90aXRsZT4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQo8IS0tDQphICAgICAgIHtjb2xvcjojMDAwMDAwOyB0ZXh0LWRlY29yYXRpb246bm9uZTsgfQ0KYTpob3ZlciB7Y29sb3I6IzAwMDAwMDsgdGV4dC1kZWNvcmF0aW9uOnVuZGVybGluZTsgfQ0KDQpib2R5ICAgICAgICB7Y29sb3I6IzAwMDAwMDsgYmFja2dyb3VuZC1jb2xvcjojRkZGRkZGOyB0ZXh0LWFsaWduOmNlbnRlcjsgfQ0KYm9keSwgdGFibGUge2ZvbnQtZmFtaWx5OlRhaG9tYSwgVmVyZGFuYSwgQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZjsgZm9udC1zaXplOjEwcHQ7IH0NCi0tPg0KPC9zdHlsZT4NCgk8L2hlYWQ+DQoJPGJvZHk+DQoJCQ0KCQk8aDM+TVpusiAtIEF0ZW7n4288L2gzPg0KCQk8cD4NCgkJCTxiPk8gTVpusiAyLjAgQURWIG7jbyBpcuEgZ2VyYXIgYXJxdWl2b3MuPC9iPjxiciAvPg0KCQk8L3A+DQoJCTxwPg0KCQkJT3MgbW9kZWxvcyBkbyBzZXUgTVpusiBmb3JhbSBvdGltaXphZG9zIHBhcmEgc2VyZW08YnIgLz4NCgkJCXVzYWRvcyBjb20gbyBFc3F1ZW1hIDEsIGdlcmFkbyBwZWxvICJHZXJhZG9yIFBIUCIsPGJyIC8+DQoJCQluYSBzZefjbyAiRmVycmFtZW50YXMiLjxiciAvPg0KCQk8L3A+DQoJCTxwPg0KCQkJU2Ugdm9j6iB0ZW0gYWxndW1hIGT6dmlkYSwgY29uc3VsdGUgbyBhcnF1aXZvIDx1PmxlaWFtZS5odG1sPC91Pi48YnIgLz4NCgkJPC9wPg0KCQkNCgk8L2JvZHk+DQo8L2h0bWw+");
	$c_inc = base64_decode("PGgzPk1abrIgLSBBdGVu5+NvPC9oMz4NCjxwPg0KCTxiPk8gTVpusiAyLjAgQURWIG7jbyBpcuEgZ2VyYXIgYXJxdWl2b3MuPC9iPjxiciAvPg0KPC9wPg0KPHA+DQoJT3MgbW9kZWxvcyBkbyBzZXUgTVpusiBmb3JhbSBvdGltaXphZG9zIHBhcmEgc2VyZW08YnIgLz4NCgl1c2Fkb3MgY29tIG8gRXNxdWVtYSAxLCBnZXJhZG8gcGVsbyAiR2VyYWRvciBQSFAiLDxiciAvPg0KCW5hIHNl5+NvICJGZXJyYW1lbnRhcyIuPGJyIC8+DQo8L3A+DQo8cD4NCglTZSB2b2PqIHRlbSBhbGd1bWEgZPp2aWRhLCBjb25zdWx0ZSBvIGFycXVpdm8gPHU+bGVpYW1lLmh0bWw8L3U+LjxiciAvPg0KPC9wPg0K");
	$c_js = base64_decode("ZG9jdW1lbnQud3JpdGUoIjxoMz5NWm6yIC0gQXRlbufjbzwvaDM+XG48cD5cblx0PGI+TyBNWm6yIDIuMCBBRFYgbuNvIGly4SBnZXJhciBhcnF1aXZvcy48L2I+PGJyIC8+XG48L3A+XG48cD5cblx0T3MgbW9kZWxvcyBkbyBzZXUgTVpusiBmb3JhbSBvdGltaXphZG9zIHBhcmEgc2VyZW08YnIgLz5cblx0dXNhZG9zIGNvbSBvIEVzcXVlbWEgMSwgZ2VyYWRvIHBlbG8gXCJHZXJhZG9yIFBIUFwiLDxiciAvPlxuXHRuYSBzZefjbyBcIkZlcnJhbWVudGFzXCIuPGJyIC8+XG48L3A+XG48cD5cblx0U2Ugdm9j6iB0ZW0gYWxndW1hIGT6dmlkYSwgY29uc3VsdGUgbyBhcnF1aXZvIDx1PmxlaWFtZS5odG1sPC91Pi48YnIgLz5cbjwvcD4iKTs=");
	
	$s->file_write($s->cfg['path']['data'] ."/xHeadlines.txt", $c_inc);
	$s->file_write($s->cfg['path']['data'] ."/xNews.txt", $c_inc);
	$s->file_write($s->cfg['path']['data'] ."/xAllNews.txt", $c_inc);
	$s->file_write($s->cfg['path']['data'] ."/xHTML.txt", $c_html);
	$s->file_write($s->cfg['path']['data'] ."/xHeadlines.js", $c_js);
	$s->file_write($s->cfg['path']['data'] ."/xNews.js", $c_js);
	
	header("Location: index.php?s=". $session ."&act=login&msg=". urlencode("MZn² atualizado com sucesso, mas você ainda<br />precisa alterar os seus includes!"));
	
}


$contents = ob_get_contents();
ob_end_clean();

installLayout($contents);

?>
