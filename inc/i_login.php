<?php $p['tit'] = "login"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

$act = $s->req['act']; if (!$act) {$act = "index"; }
if ($s->usr['data'] && $act != "exit" && !preg_match("/^popup/i", $act)) {$m->location(); }

//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") { ?>
<b>Bem vindo ao MZn² 2.0!</b><br />
<br />
Digite abaixo os seus dados<br />
para entrar no sistema.<br />
<br />
<form name="formCenter" action="index.php" method="post" autocomplete="off" onsubmit="disableButtons('formCenter'); <?php if ($s->req['SAFELOGIN'] != "off") { ?>this.elements['pwd'].value = md5('WsSys SAFELOGIN '+ unique +' '+ md5(this.elements['pwd'].value)); <?php } ?>">
	<input type="hidden" name="s" value="<?php echo $s->req['s']; ?>" />
	<input type="hidden" name="sec" value="login" />
	<input type="hidden" name="act" value="check" />
<?php if ($s->req['SAFELOGIN'] != "off") { ?>	<input type="hidden" name="SAFELOGIN" value="on" /><?php } ?>
	<table cellpadding="0" cellspacing="1" border="0" align="center">
		<tr><td align="right"><b>Login:</b></td><td><input type="text" name="user" class="small" tabindex="1"<?php if ($s->cfg['ver']['demo']) { ?> value="demo" readonly="1"<?php } ?> /></td></tr>
		<tr><td align="right"><b>Senha:</b></td><td><input type="password" name="pwd" class="small" tabindex="1"<?php if ($s->cfg['ver']['demo']) { ?> value="demo" readonly="1"<?php } ?> /></td></tr>
		<tr><td colspan="2" align="right"><button type="submit" class="submit" tabindex="1" accesskey="e"><u>E</u>ntrar</button></td></tr>
	</table>
<?php if ($s->sys['lostpwd'] && !$s->cfg['ver']['demo']) { ?>	<br />
	<a href="index.php?s={session}&amp;sec=login&amp;act=lostpwd" class="small"><b>Esqueceu sua senha?</b><br />Clique aqui para pegar outra!<br /></a><?php echo "\n"; } ?>
	<script language="JavaScript" type="text/javascript"> document.forms['formCenter'].elements['user'].focus(); </script>
</form>
<?php if ($s->req['SAFELOGIN'] != "off") { ?><div><b>WsSys SAFELOGIN ativado.</b></div>
<div><span class="small"><b>Seu login está protegido.</b></span></div>
<div><span class="small"><a href="index.php?s={session}&amp;sec=login&amp;SAFELOGIN=off">Clique aqui se você está tendo problemas.</a></span></div><?php }
else { ?><div class="important">WsSys SAFELOGIN desativado.</div>
<div class="important"><span class="small">Seu login <b>NÃO</b> está protegido.</span></div>
<div class="important"><span class="small"><a href="index.php?s={session}&amp;sec=login&amp;SAFELOGIN=on" class="important">Clique aqui para ativá-lo.</a></span></div><?php } ?>
<?php }


//-----------------------------------------------------------------------------
// Act check
//-----------------------------------------------------------------------------
else if ($act == "check") {
	function test_login ($v1, $v2) {
		global $s;
		if ($s->req['SAFELOGIN'] == "on") {
			if (md5("WsSys SAFELOGIN ". $s->usr['unique'] ." ". $v1) == $v2) {return true; }
			else {return false; }
		}
		else {
			if ($v1 == md5($v2)) {return true; }
			else {return false; }
		}
	}
	$loginOk = 0; $savePwd = "";
	if (!$s->req['user'] || !$s->req['pwd'] || !$s->users[$s->req['user']]['active']) {$m->error_redir("logininvalid"); }
	
	if ($s->users[$s->req['user']]['newpwd'] && $s->sys['lostpwd']) {
		if (test_login($s->users[$s->req['user']]['newpwd'], $s->req['pwd']) || test_login($s->users[$s->req['user']]['pwd'], $s->req['pwd'])) {
			
		if (test_login($s->users[$s->req['user']]['newpwd'], $s->req['pwd'])) {$savePwd = $s->users[$s->req['user']]['newpwd']; }
			else {$savePwd = $s->users[$s->req['user']]['pwd']; }
			
			$loginOk = 1;
		}
	}
	else {
		if (test_login($s->users[$s->req['user']]['pwd'], $s->req['pwd'])) {
			$loginOk = 1;
			$savePwd = $s->users[$s->req['user']]['pwd'];
		}
	}
	
	if ($loginOk) {
		$s->usr['user'] = $s->req['user']; $s->usr['auth'] = md5("WsSys LOGIN ". $savePwd);
		if (!$s->cfg['ver']['demo']) {
			$db = $s->db_table_open($s->cfg['file']['users'], $s->cfg['header']['users']);
			foreach ($db['data'] as $k => $v) {
				if ($v['user'] == $s->req['user']) {
					$dbN = $v;
					if ($savePwd) {
						$dbN['pwd'] = $savePwd;
						unset($dbN['data']['newpwd']);
					}
					$dbN['data']['lastlogin'] = $s->cfg['time'];
					$db['data'][$k] = $dbN;
				}
			}
			$s->db_table_save($s->cfg['file']['users'], $db);
		}
		$m->location();
	}
	else {$m->error_redir("logininvalid"); }
}


//-----------------------------------------------------------------------------
// Act exit
//-----------------------------------------------------------------------------
else if ($act == "exit") {
	unset($s->usr['user']); unset($s->usr['auth']);
	$m->location();
}


//-----------------------------------------------------------------------------
// Act lostpwd
//-----------------------------------------------------------------------------
else if ($act == "lostpwd") {
	if (!$s->sys['lostpwd']) {$m->error_redir("lostpwd_disabled"); }
	?>
Preenchendo o formulário abaixo, você receberá uma<br />
nova senha por e-mail. Se a sua senha antiga for usada,<br />
a nova será removida, e vice-versa.<br />
<br />
<?php $l->form("login", "lostpwd_send"); ?>
	<table cellpadding="0" cellspacing="1" border="0">
		<tr><td align="right"><b>Login:</b></td><td><input type="text" name="user" class="small" tabindex="1" /></td></tr>
		<tr><td align="right"><b>E-mail:</b></td><td><input type="text" name="mail" class="small" tabindex="1" /></td></tr>
		<tr><td colspan="2" align="right"><button type="submit" class="submit" tabindex="1" accesskey="e"><u>E</u>nviar</button></td></tr>
	</table>
	<script language="JavaScript" type="text/javascript"> document.forms['formCenter'].elements['user'].focus(); </script>
<?php $l->form_end(); ?>
<?php }


//-----------------------------------------------------------------------------
// Act lostpwd_send
//-----------------------------------------------------------------------------
else if ($act == "lostpwd_send") {
	if (!$s->sys['lostpwd']) {$m->error_redir("lostpwd_disabled"); }
	$m->req('user', 'mail');
	
	$eID = 1; $newPwd = substr(md5(time()*rand()), 0, 10); $replaceBody = array(); $emailTo = "";
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['users'], $s->cfg['header']['users']);
		foreach ($db['data'] as $k => $v) {
			if ($v['user'] == $s->req['user'] && $v['data']['mail'] == $s->req['mail']) {
				$v['data']['newpwd'] = md5($newPwd);
				$db['data'][$k] = $v; $eID = 0;
			}
			else if ($v['user'] == $s->req['user'] && !$v['data']['mail']) {$eID = 2; }
		}
		
		if ($eID == 0) {
			$replaceBody = array('site:name' => $s->sys['site']['name'], 'user:login' => $s->req['user'], 'user:pwd' => $newPwd);
			$sendMail = @mail($s->req['mail'], $s->cfg['mail']['lostpwd']['s'], $s->replace_vars($s->cfg['mail']['lostpwd']['b'], $replaceBody), "From: Sistema MZn² <sistemamzn2@naoresponda.com.br>\nContent-type: text/html");
			if ($sendMail) {
				$s->db_table_save($s->cfg['file']['users'], $db);
				$m->location("sec=login", "A sua nova senha foi enviada para o seu e-mail!");
			}
			else {$m->error_redir("lostpwd_notsent"); }
		}
		else if ($eID == 1) {$m->error_redir("lostpwd_emailmismatch"); }
		else if ($eID == 2) {$m->error_redir("lostpwd_noemail"); }
	}
	else {$m->error_redir("demo"); }
}


//-----------------------------------------------------------------------------
// Act popup
//-----------------------------------------------------------------------------
else if ($act == "popup") {
	if (!$s->req['form'] || !$s->req['ufield'] || !$s->req['pfield']) {exit; }
	$designActive = 0;
	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Login - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script type="text/javascript" language="JavaScript" src="mzn2.js"></script>
	</head>
	<body onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<form name="formCenter" action="index.php" method="post" onsubmit="this.elements['pwd'].value = md5('WsSys SAFELOGIN <?php echo $s->usr['unique']; ?> '+ md5(this.elements['pwd'].value)); " autocomplete="off">
		<input type="hidden" name="s" value="<?php echo $s->req['s']; ?>">
		<input type="hidden" name="sec" value="login">
		<input type="hidden" name="act" value="popup_check">
		<input type="hidden" name="form" value="<?php echo $s->quote_safe($s->req['form']); ?>">
		<input type="hidden" name="ufield" value="<?php echo $s->quote_safe($s->req['ufield']); ?>">
		<input type="hidden" name="pfield" value="<?php echo $s->quote_safe($s->req['pfield']); ?>">
		
		<img src="img/{skin}/popup_login.jpg" width="400" height="70"><br>
		<table align="center" cellpadding="3" cellspacing="0">
			<tr><td align="right"><b>Usuário:</b></td><td><input type="text" name="user" style="font:8pt verdana; width:130px; "></td></tr>
			<tr><td align="right"><b>Senha:</b></td><td><input type="password" name="pwd" style="font:8pt verdana; width:130px; "></td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr><td colspan="2" align="center">
				<button type="submit" style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button type="button" onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button>
			</td></tr>
		</table>
		<script language="JavaScript" type="text/javascript"> document.forms['formCenter'].elements['user'].focus(); </script>
		
		</form>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act popup_check
//-----------------------------------------------------------------------------
else if ($act == "popup_check") {
	if (!$s->req['form'] || !$s->req['ufield'] || !$s->req['pfield']) {exit; }
	$designActive = 0;
	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Login - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script type="text/javascript" language="JavaScript" src="mzn2.js"></script>
	</head>
	<body onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_login.jpg" width="400" height="70"><br>
<?php

function test_login ($v1, $v2) {
	global $s;
	if (md5("WsSys SAFELOGIN ". $s->usr['unique'] ." ". $v1) == $v2) {return true; }
	else {return false; }
}
$loginOk = 0; $savePwd = "";
if (!$s->req['user'] || !$s->req['pwd'] || !$s->users[$s->req['user']]['active']) {}
else if ($s->users[$s->req['user']]['newpwd'] && $s->sys['lostpwd']) {
	if (test_login($s->users[$s->req['user']]['newpwd'], $s->req['pwd']) || test_login($s->users[$s->req['user']]['pwd'], $s->req['pwd'])) {
		
		if (test_login($s->users[$s->req['user']]['newpwd'], $s->req['pwd'])) {$savePwd = $s->users[$s->req['user']]['newpwd']; }
		else {$savePwd = $s->users[$s->req['user']]['pwd']; }
		
		$loginOk = 1;
	}
}
else {
	if (test_login($s->users[$s->req['user']]['pwd'], $s->req['pwd'])) {
		$loginOk = 1;
		$savePwd = $s->users[$s->req['user']]['pwd'];
	}
}

if ($loginOk) {
	$s->usr['user'] = $s->req['user']; $s->usr['auth'] = md5("WsSys LOGIN ". $savePwd);
	$db = $s->db_table_open($s->cfg['file']['users'], $s->cfg['header']['users']);
	foreach ($db['data'] as $k => $v) {
		if ($v['user'] == $s->req['user']) {
			$dbN = $v;
			if ($savePwd) {
				$dbN['pwd'] = $savePwd;
				unset($dbN['data']['newpwd']);
			}
			$dbN['data']['lastlogin'] = $s->cfg['time'];
			$db['data'][$k] = $dbN;
		}
	}
	$s->db_table_save($s->cfg['file']['users'], $db);
	echo "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\"> if (!window.opener) {window.close(); } else {var oForm = window.opener.document.forms[\"". addslashes($s->req['form']) ."\"]; if (oForm) {if (oForm.elements[\"". addslashes($s->req['ufield']) ."\"]) {oForm.elements[\"". addslashes($s->req['ufield']) ."\"].value = \"". addslashes($s->req['user']) ."\"; } if (oForm.elements[\"". addslashes($s->req['pfield']) ."\"]) {oForm.elements[\"". addslashes($s->req['pfield']) ."\"].value = \"". addslashes($s->users[$s->req['user']]['pwd']) ."\"; } oForm.submit(); window.close(); } } </scr"."ipt>";
}
else {echo "<br /><br /><div align=\"center\"><b>Nome de usuário ou senha inválidos!</b><br /><a href=\"#\" onclick=\"history.back(); return false; \">Clique aqui para voltar.</a></div>"; }

echo "\n"; ?>
		
	</body>
</html><?php }


?>
