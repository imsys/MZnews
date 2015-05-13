<?php $p['tit'] = "smilies"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

$act = $s->req['act']; if (!$act) {$act = "index"; }


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") { $m->req_perms("smilies"); ?>
<script type="text/javascript" language="JavaScript">
<!-- ;

var last_sel = null;

function smileAdd() {
	var win = window.open('index.php?s={session}&sec=smilies&act=new', '_blank', 'width=406,height=210');
	win.resizeTo(410, 210);
}

function smileEdit(f) {
	var id = last_sel;
	if (!id) {return; }
	var win = window.open('index.php?s={session}&sec=smilies&act=edit&id='+ id, '_blank', 'width=406,height=210');
	win.resizeTo(410, 210);
}

function smileClick(f, id) {
	var obj1 = document.getElementById('smile_'+ id), obj2 = document.forms[f].elements['sel['+ id +']'];
	if (!obj1 || !obj2) {return; }
	if (obj1.className == 'listItem') {last_sel = id; obj1.className = 'listItemSelected'; obj2.value = 1; }
	else {obj1.className = 'listItem'; obj2.value = 0; }
}

// -->
</script>
<form name="formCenter" action="index.php" method="post" autocomplete="off">
	<input type="hidden" name="s" value="{session}" />
	<input type="hidden" name="sec" value="smilies" />
	<input type="hidden" name="act" value="remove" />
	<table width="505" align="center" cellpadding="0" cellspacing="0" border="0" style="text-align:left; ">
		<tr><td valign="top">
			<div align="center" style="width:505px; height:250px; overflow:auto; "><table cellpadding="2" cellspacing="10" width="460">
<?php

echo "<tr>";
$db = $s->vars_import($s->sys['sm']['list']);
$packs = $s->vars_import($s->sys['sm']['packs']);
$packs['local'] = $s->cfg['dir']['smilies'];
$i = 0; $cMax = 5; $c = 1; foreach ($db as $sm => $img) {
	$img = $s->replace_vars($img, $packs);
	echo "<td width=\"". intval(100 / $cMax) ."%\" id=\"smile_". $i ."\" align=\"center\" valign=\"bottom\" class=\"listItem\" style=\"cursor:hand; \" onclick=\"smileClick('formCenter', '". $i ."'); \"><input type=\"hidden\" name=\"sel[". $i ."]\" value=\"0\"><a href=\"#\" onclick=\"return false; \"><img src=\"". $img ."\" alt=\"\" border=\"0\" /><br /><b>". $sm ."</b></a></td>";
	if ($c >= $cMax) {echo "</tr>\n<tr>"; $c = 1; } else {$c++; } $i++;
}
echo "</tr>";

?>
			</table></div>
		</td></tr>
		<tr><td><hr color="#000000" noshade size="1" /></td></tr>
		<tr><td valign="top"><table cellpadding="0" cellspacing="0" width="100%"><tr class="listFooter"><td nowrap="1" align="left"><a href="index.php?s={session}&amp;sec=smilies&amp;act=addpack"><b>Adicionar um pacote</b></a><?php if (count($packs) > 1) { ?>&nbsp;&middot;&nbsp;<a href="index.php?s={session}&amp;sec=smilies&amp;act=rempack"><b>Remover um pacote</b></a><?php } ?></td><td nowrap="1" align="right"><a href="#" onclick="smileAdd(); return false; "><b>Adicionar</b></a>&nbsp;&middot;&nbsp;<a href="#" onclick="smileEdit('formCenter'); return false; "><b>Editar</b></a>&nbsp;&middot;&nbsp;<a href="#" onclick="if (confirm('Você está prestes a remover os itens selecionados.\nTodos os registros que são dependentes destes itens\ntambém serão removidos!\n\nDeseja continuar?')) {document.forms['formCenter'].submit(); return false; } "><b>Remover</b></a></td></tr></table></td></tr>
	</table>
</form>
<?php }


//-----------------------------------------------------------------------------
// Act new
//-----------------------------------------------------------------------------
else if ($act == "new") {
	$m->req_perms("smilies");
	$db = $s->vars_import($s->sys['sm']['list']);
	$packs = $s->vars_import($s->sys['sm']['packs']);
	$used = array();
	
	$i = 0; foreach ($db as $sm => $img) {
		if (preg_match("/^{local}\//", $img)) {$used[preg_replace("/^{local}\//", "", $img)] = 1; }
		$i++;
	}
	
	$designActive = 0; ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Adicionar Smile - MZn² - A nova geração</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var smile_from = 'local';
var smile_image = '';

function init() {
	if (!window.opener) {window.close(); }
	if (smile_image) {document.getElementById('preview').src = '<?php echo $s->cfg['dir']['smilies']; ?>/'+ smile_image; }
}

function smile_select (x) {
	smile_image = x;
	document.getElementById('preview').src = "<?php echo $s->cfg['dir']['smilies']; ?>/"+ x;
}

function Save() {
	var obj = document.forms['formEdit'].elements;
	obj['act'].value = 'new_save';
	obj['from'].value = smile_from;
	obj['img'].value = (smile_from == "local")? smile_image : document.getElementById('smile_url').value;
	obj['text'].value = document.getElementById('smile_text').value;
	obj['_keep'].value = (document.getElementById('_keep').checked)? 1 : 0;
	document.forms['formEdit'].submit();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_smilies.jpg" width="400" height="70"><br>
		<table width="400" align="center" cellpadding="2" cellspacing="2">
			<tr><td width="60" align="left"><input type="radio" name="from" value="local" id="from_local" onclick="smile_from = 'local'; " checked><b><label for="from_local">Local:</label></b></td><td width="240"><?php

$res = ""; $sel = "";
$dir = @opendir($s->cfg['path']['smilies']);
if ($dir) {
	$first = "";
	while (($file = @readdir($dir)) !== false) {
		if (preg_match("/^\./", $file) || is_dir($s->cfg['path']['smilies'] ."/". $file) || !preg_match("/\.(gif|jpg|jpeg|bmp|png)$/", $file) || $used[$file]) {continue; }
		$res .= "<option value=\"". $file ."\">". $file ."</option>";
		if (!$first) {$first = $file; }
	}
	@closedir($dir);
}
if ($res) {?><select onfocus="document.getElementById('from_local').click(); document.getElementById('preview').src = '<?php echo $s->cfg['dir']['smilies']; ?>/'+ smile_image; " onchange="smile_select(this.options[this.selectedIndex].value); " style="font:8pt verdana; width:240px; "><?php echo $res; ?></select><?php $sel = "local"; }
else {?><select style="font:8pt verdana; width:240px; " disabled><option value="">Não há imagens na pasta de smilies</option></select><?php $sel = "remote"; }

?></td><td width="80" rowspan="3" align="center" valign="top"><b>Vis</b><br /><img src="<?php echo $s->cfg['dir']['smilies'] ."/". $first; ?>" id="preview"></td></tr>
			<tr><td width="60" align="left"><input type="radio" name="from" value="url" id="from_url" onclick="smile_from = 'url'; "><b><label for="from_url">URL:</label></b></td><td width="240"><input type="text" id="smile_url" value="<?php echo $s->quote_safe($url); ?>" onfocus="document.getElementById('from_url').click(); document.getElementById('preview').src = 'img/_blank.gif'; " onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:240px; "></td></tr>
			<tr><td width="60" align="left"><b>Texto:</b></td><td width="240"><input type="text" id="smile_text" value="<?php echo $s->quote_safe($text); ?>" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:240px; "></td></tr>
			<tr><td colspan="3" height="5"></td></tr>
			<tr><td colspan="3" align="center"><table cellpadding="0" cellspacing="0" width="100%"><tr>
				<td align="left"><input type="checkbox" id="_keep"<?php if ($s->req['_keep']) { echo " checked"; } ?>><label for="_keep">Continuar adicionando</label></td>
				<td align="right"><button type="button" onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button type="button" onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button></td>
			</tr></table></td></tr>
		</table>
		<script> smile_image = '<?php echo $first; ?>'; <?php if ($sel == "remote") { ?>document.getElementById('from_local').disabled = 1; if (!smile_image) {document.getElementById('from_url').click(); document.getElementById('smile_url').focus(); document.getElementById('preview').src = 'img/_blank.gif'; } <?php } ?></script>
		<form name="formEdit" action="index.php" method="post" autocomplete="off">
			<input type="hidden" name="s" value="{session}" />
			<input type="hidden" name="sec" value="smilies" />
			<input type="hidden" name="act" value="new_save" />
			<input type="hidden" name="from" value="" />
			<input type="hidden" name="img" value="" />
			<input type="hidden" name="text" value="" />
			<input type="hidden" name="_keep" value="" />
		</form>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act edit
//-----------------------------------------------------------------------------
else if ($act == "edit") {
	$m->req_perms("smilies");
	if (!isset($s->req['id']) || $s->req['id'] == "") {echo "<scr"."ipt> alert(\"Selecione um smile!\"); window.close(); </scr"."ipt>"; exit; }
	
	$db = $s->vars_import($s->sys['sm']['list']);
	$packs = $s->vars_import($s->sys['sm']['packs']);
	$used = array();
	
	$i = 0; foreach ($db as $sm => $img) {
		if ($i == $s->req['id']) {
			if (preg_match("/^{local}\//", $img)) {$from = "local"; $sel = preg_replace("/^{local}\//", "", $img); }
			else {$from = "url"; $url = $s->replace_vars($img, $packs); }
			$text = $sm;
		}
		else if (preg_match("/^{local}\//", $img)) {$used[preg_replace("/^{local}\//", "", $img)] = 1; }
		$i++;
	}
	
	$designActive = 0; ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Alterar Smile - MZn² - A nova geração</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var smile_from = '<?php echo $from; ?>';
var smile_image = '<?php echo $sel; ?>';

function init() {
	if (!window.opener) {window.close(); }
	if (smile_image) {document.getElementById('preview').src = '<?php echo $s->cfg['dir']['smilies']; ?>/'+ smile_image; }
	else {smile_image = smile_first; }
}

function smile_select (x) {
	smile_image = x;
	document.getElementById('preview').src = "<?php echo $s->cfg['dir']['smilies']; ?>/"+ x;
}

function Save() {
	var obj = document.forms['formEdit'].elements;
	obj['id'].value = '<?php echo $s->req['id']; ?>';
	obj['from'].value = smile_from;
	obj['img'].value = (smile_from == "local")? smile_image : document.getElementById('smile_url').value;
	obj['text'].value = document.getElementById('smile_text').value;
	document.forms['formEdit'].submit();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_smilies.jpg" width="400" height="70"><br>
		<table width="400" align="center" cellpadding="2" cellspacing="2">
			<tr><td width="60" align="left"><input type="radio" name="from" value="local" id="from_local" onclick="smile_from = 'local'; "<?php if ($from == "local") {echo " checked"; } ?>><b><label for="from_local">Local:</label></b></td><td width="240"><?php

$res = ""; $sel = "";
$dir = @opendir($s->cfg['path']['smilies']);
if ($dir) {
	$first = "";
	while (($file = @readdir($dir)) !== false) {
		if (preg_match("/^\./", $file) || is_dir($s->cfg['path']['smilies'] ."/". $file) || !preg_match("/\.(gif|jpg|jpeg|bmp|png)$/", $file) || $used[$file]) {continue; }
		$res .= "<option value=\"". $file ."\""; if ($sel == $file) {$res .= " selected"; } $res .= ">". $file ."</option>";
		if (!$first) {$first = $file; }
	}
	@closedir($dir);
}
if ($res) {?><select onfocus="document.getElementById('from_local').click(); document.getElementById('preview').src = '<?php echo $s->cfg['dir']['smilies']; ?>/'+ smile_image; " onchange="smile_select(this.options[this.selectedIndex].value); " style="font:8pt verdana; width:240px; "><?php echo $res; ?></select><?php $sel = "local"; }
else {?><select style="font:8pt verdana; width:240px; " disabled><option value="">Não há imagens na pasta de smilies</option></select><?php $sel = "remote"; }

?></td><td width="80" rowspan="3" align="center" valign="top"><b>Vis</b><br /><img src="img/_blank.gif" id="preview"></td></tr>
			<tr><td width="60" align="left"><input type="radio" name="from" value="url" id="from_url" onclick="smile_from = 'url'; "<?php if ($from == "url") {echo " checked"; } ?>><b><label for="from_url">URL:</label></b></td><td width="240"><input type="text" id="smile_url" value="<?php echo $s->quote_safe($url); ?>" onfocus="document.getElementById('from_url').click(); document.getElementById('preview').src = 'img/_blank.gif'; " onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:240px; "></td></tr>
			<tr><td width="60" align="left"><b>Texto:</b></td><td width="240"><input type="text" id="smile_text" value="<?php echo $s->quote_safe($text); ?>" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:240px; "></td></tr>
			<tr><td colspan="3" height="5"></td></tr>
			<tr><td colspan="3" align="center">
				<button type="button" onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button type="button" onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button>
			</td></tr>
		</table>
		<script> smile_image = '<?php echo $first; ?>'; <?php if ($sel == "remote") { ?>document.getElementById('from_local').disabled = 1; if (!smile_image) {document.getElementById('from_url').click(); document.getElementById('smile_url').focus(); document.getElementById('preview').src = 'img/_blank.gif'; } <?php } ?></script>
		<form name="formEdit" action="index.php" method="post" autocomplete="off">
			<input type="hidden" name="s" value="{session}" />
			<input type="hidden" name="sec" value="smilies" />
			<input type="hidden" name="act" value="edit_save" />
			<input type="hidden" name="id" value="" />
			<input type="hidden" name="from" value="" />
			<input type="hidden" name="img" value="" />
			<input type="hidden" name="text" value="" />
		</form>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act new_save
//-----------------------------------------------------------------------------
else if ($act == "new_save") {
	$m->req_perms("smilies");
	if (!preg_match("/(\/|)[^\/]+\.[^\.\/]{3,4}$/i", $s->req['img'])) {echo "<scr"."ipt> alert(\"Imagem inválida!\"); history.back(); </scr"."ipt>"; exit; }
	if (!isset($s->req['text']) || $s->req['text'] == "") {echo "<scr"."ipt> alert(\"Texto inválido!\"); history.back(); </scr"."ipt>"; exit; }
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->vars_import($s->sys['sm']['list']);
		$packs = $s->vars_import($s->sys['sm']['packs']);
		
		if ($s->req['from'] == "local") {$use = "local"; $img = $s->req['img']; }
		else {
			$use = "";
			$url = substr($s->req['img'], 0, strrpos($s->req['img'], "/"));
			$img = substr($s->req['img'], strrpos($s->req['img'], "/")+1, strlen($s->req['img']) - strrpos($s->req['img'], "/"));
			foreach ($packs as $id => $pack) {if ($url == $pack) {$use = $id; break; } }
			if (!$use) {$use = substr(md5(time()*rand()), 0, 5); $packs[$use] = $url; }
		}
		$db[$s->req['text']] = "{". $use ."}/". $img;
		
		$p_use = array();
		foreach ($db as $k => $v) {if (preg_match("/^\{[^\}]+\}/", $v) || !preg_match("/^\{local\}/", $v)) {$p_use[preg_replace("/^\{([^\}]+)\}(.*)/", "\\1", $v)] = 1; } }
		foreach ($packs as $k => $v) {if (!$p_use[$k]) {unset($packs[$k]); } }
		
		$s->sys['sm']['list'] = $s->vars_export($db);
		$s->sys['sm']['packs'] = $s->vars_export($packs);
	}
	$designActive = 0;
	if ($s->req['_keep']) {echo "<script> if (window.opener) {window.opener.location.href = window.opener.location.href; } location.href = 'index.php?s={session}&sec=smilies&act=new&_keep=true'; </script>"; }
	else {echo "<script> if (window.opener) {window.opener.location.href = window.opener.location.href; } window.close(); </script>"; }
}


//-----------------------------------------------------------------------------
// Act edit_save
//-----------------------------------------------------------------------------
else if ($act == "edit_save") {
	$m->req_perms("smilies");
	if (!isset($s->req['id']) || $s->req['id'] == "") {echo "<scr"."ipt> alert(\"Nenhum smile selecionado!\"); window.close(); </scr"."ipt>"; exit; }
	if (!preg_match("/(\/|)[^\/]+\.[^\.\/]{3,4}$/i", $s->req['img'])) {echo "<scr"."ipt> alert(\"Imagem inválida!\"); history.back(); </scr"."ipt>"; exit; }
	if (!isset($s->req['text']) || $s->req['text'] == "") {echo "<scr"."ipt> alert(\"Texto inválido!\"); history.back(); </scr"."ipt>"; exit; }
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->vars_import($s->sys['sm']['list']);
		$packs = $s->vars_import($s->sys['sm']['packs']);
		
		$i = 0; foreach ($db as $sm => $img) {
			if ($i == $s->req['id']) {
				if ($s->req['from'] == "local") {$use = "local"; $img = $s->req['img']; }
				else {
					$use = "";
					$url = substr($s->req['img'], 0, strrpos($s->req['img'], "/"));
					$img = substr($s->req['img'], strrpos($s->req['img'], "/")+1, strlen($s->req['img']) - strrpos($s->req['img'], "/"));
					foreach ($packs as $id => $pack) {if ($url == $pack) {$use = $id; break; } }
					if (!$use) {$use = substr(md5(time()*rand()), 0, 5); $packs[$use] = $url; }
				}
				unset($db[$sm]); $db[$s->req['text']] = "{". $use ."}/". $img;
				break;
			}
			$i++;
		}
		
		$p_use = array();
		foreach ($db as $k => $v) {if (preg_match("/^\{[^\}]+\}/", $v) || !preg_match("/^\{local\}/", $v)) {$p_use[preg_replace("/^\{([^\}]+)\}(.*)/", "\\1", $v)] = 1; } }
		foreach ($packs as $k => $v) {if (!$p_use[$k]) {unset($packs[$k]); } }
		
		$s->sys['sm']['list'] = $s->vars_export($db);
		$s->sys['sm']['packs'] = $s->vars_export($packs);
	}
	$designActive = 0;
	echo "<script> if (window.opener) {window.opener.location.href = window.opener.location.href; } window.close(); </script>";
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	$m->req_perms("smilies");
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->vars_import($s->sys['sm']['list']);
		$packs = $s->vars_import($s->sys['sm']['packs']);
		$rem = 0;
		
		$i = 0; foreach ($db as $sm => $img) {
			if ($s->req['sel'][$i] == 1) {unset($db[$sm]); $rem++; }
			$i++;
		}
		
		$p_use = array();
		foreach ($db as $k => $v) {if (preg_match("/^\{[^\}]+\}/", $v) || !preg_match("/^\{local\}/", $v)) {$p_use[preg_replace("/^\{([^\}]+)\}(.*)/", "\\1", $v)] = 1; } }
		foreach ($packs as $k => $v) {if (!$p_use[$k]) {unset($packs[$k]); } }
		
		$s->sys['sm']['list'] = $s->vars_export($db);
		$s->sys['sm']['packs'] = $s->vars_export($packs);
	}
	$designActive = 0;
	$msg = "Smile removido"; if ($rem > 1) {$msg = $rem ." smilies removidos"; }
	echo "<script> alert(\"". $msg ."\"); location.href = 'index.php?s={session}&sec=smilies'; </script>";
}

//-----------------------------------------------------------------------------
// Act addpack
//-----------------------------------------------------------------------------
else if ($act == "addpack") {
	$m->req_perms("smilies");
	echo "<span class=\"important\"><b>Atenção</b><br />É possível que o seu host bloqueie o acesso do PHP a arquivos<br />externos. Neste caso este sistema não funcionará!</span><br /><br />";
	$l->form("smilies", "addpack_import"); $l->table(505);
	
	$l->tb_group("Pacote externo");
			$l->tb_input("text", "url", "<b>URL do pacote</b>", "http://");
	
	$l->tb_button("submit", "Importar", array("accesskey" => "i"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=smilies"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act addpack_import
//-----------------------------------------------------------------------------
else if ($act == "addpack_import") {
	$m->req_perms("smilies");
	$m->req('url');
	$url = $s->req['url'];
	$url = preg_replace("/\/$/i", "", $url);
	$use = $url; $count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$u = @parse_url($url);
		$cScheme = $u['scheme'];
		$cHost = $u['host'];
		if ($u['user']) {$cAuth = $u['user'] .":". $u['pass']; }
		$cPort = $u['port']; if (!$cPort) {$cPort = 80; }
		$cAbsPath = $u['path']; if (!$cAbsPath) {$cAbsPath = "/"; }
		$cPath = $u['path']; if (!$cPath) {$cPath = "/"; } else if (preg_match("/\/[^\/]+\.[^\/\.]+$/", $cPath)) {$cPath = preg_replace("/\/[^\/]+\.[^\/\.]+$/", "/", $cPath); }
		if (!preg_match("/\/$/", $cPath)) {$cPath .= "/"; }
		$cPath .= "pack.txt";
		if ($u['query']) {$cPath .= "?". $u['query']; }
		
		$cUAgent = $s->req['HTTP_USER_AGENT'];
		$cRef = $cScheme ."://". $cHost . $cPath;
		
		if ($cScheme != "http" || !$cHost) {$m->error_redir("pack_invalidurl"); }
		
		$cArgs = "Host: $cHost";
		$cArgs .= "\r\nUser-Agent: $cUAgent";
		if ($cRef) {$cArgs .= "\r\nReferer: $cRef"; }
		if ($cAuth) {$cArg .= "\r\nAuthorization: Basic ". base64_encode($cAuth); }
		
		$fHeaderSent = 0; $fHeader = ""; $fContent = "";
		
		$sk = @fsockopen($cHost, $cPort, $errno, $errstr, 30);
		if (!$sk) {$m->error_redir("pack_nosocket"); }
		fwrite($sk, "GET ". $cPath ." HTTP/1.0\r\n". $cArgs ."\r\n\r\n");
		
		while (!feof($sk)) {$x = fgets($sk, 2048); if (!$fHeaderSent && trim($x) == "") {$fHeaderSent = 1; break; } else {$fHeader .= $x; } }
		
		$fConType = "";
		$fHeader = str_replace("\r\n", "\n", $fHeader); $fHeader = explode("\n", $fHeader); $valid = 0;
		foreach ($fHeader as $head) {
			if (preg_match("/^HTTP(.*)404/i", $head)) {$m->error_redir("pack_notfound"); }
			else if (preg_match("/^HTTP(.*)401/i", $head) || preg_match("/^HTTP(.*)403/i", $head)) {$m->error_redir("pack_noaccess"); }
			else if (preg_match("/^HTTP(.*)301/i", $head) || preg_match("/^HTTP(.*)302/i", $head)) {$m->error_redir("pack_redir"); }
			else if (preg_match("/^HTTP(.*)200/i", $head)) {$valid = 1; }
		}
		if (!$valid) {$m->error_redir("pack_not200"); }
		
		$cont = array();
		while (!feof($sk)) {
			$cont[] = trim(fgets($sk, 2048));
		}
		fclose($sk);
		
		if (!preg_match("/#!\/MZN2PACK\/1\.0/", $cont[0])) {$m->error_redir("pack_invalid"); }
		
		$db = $s->vars_import($s->sys['sm']['list']);
		$packs = $s->vars_import($s->sys['sm']['packs']);
		
		$p_id = substr(md5(time()*rand()), 0, 5);
		$first = 1; $count = 0;
		foreach ($cont as $v) {
			if (!$v) {continue; }
			if ($first) {$first = 0; continue; } else {$count++; }
			list($sm, $img) = explode("|", $v);
			$db[$sm] = "{". $p_id ."}/". $img;
		}
		$packs[$p_id] = $use;
		
		$p_use = array();
		foreach ($db as $k => $v) {if (preg_match("/^\{[^\}]+\}/", $v) || !preg_match("/^\{local\}/", $v)) {$p_use[preg_replace("/^\{([^\}]+)\}(.*)/", "\\1", $v)] = 1; } }
		foreach ($packs as $k => $v) {if (!$p_use[$k]) {unset($packs[$k]); } }
		
		$s->sys['sm']['list'] = $s->vars_export($db);
		$s->sys['sm']['packs'] = $s->vars_export($packs);
	}
	
	$msg = "Apenas um smile adicionado"; if ($count > 1) {$msg = $count ." smilies adicionados"; }
	$m->location("sec=smilies", "Pacote importado - ". $msg);
}


//-----------------------------------------------------------------------------
// Act rempack
//-----------------------------------------------------------------------------
else if ($act == "rempack") {
	$m->req_perms("smilies");
	$packs = $s->vars_import($s->sys['sm']['packs']);
	$list = ""; foreach ($packs as $k => $v) {if ($list) {$list .= "|"; } $list .= $k ."=". $v; }
	$l->form("smilies", "rempack_do"); $l->table(505);
	
	$l->tb_group("Pacote");
			$l->tb_select("id", "<b>URL do pacote a ser removido</b>", $list);
	
	$l->tb_button("submit", "Remover", array("accesskey" => "r"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=smilies"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act rempack_do
//-----------------------------------------------------------------------------
else if ($act == "rempack_do") {
	$m->req_perms("smilies");
	$m->req('id');
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->vars_import($s->sys['sm']['list']);
		$packs = $s->vars_import($s->sys['sm']['packs']);
		
		$count = 0;
		foreach ($db as $k => $v) {
			if (preg_match("/^\{". $s->req['id'] ."\}/", $v)) {
				unset($db[$k]);
				$count++;
			}
		}
		unset($packs[$s->req['id']]);
		
		$s->sys['sm']['list'] = $s->vars_export($db);
		$s->sys['sm']['packs'] = $s->vars_export($packs);
	}
	
	$msg = "Apenas um smile removido"; if ($count > 1) {$msg = $count ." smilies removidos"; }
	$m->location("sec=smilies", "Pacote removido - ". $msg);
}


//-----------------------------------------------------------------------------
// Act list
//-----------------------------------------------------------------------------
else if ($act == "list") {
	$db = $s->vars_import($s->sys['sm']['list']);
	$packs = $s->vars_import($s->sys['sm']['packs']);
	$used = array();
	
	$i = 0; foreach ($db as $sm => $img) {
		if ($i == $s->req['id']) {
			if (preg_match("/^{local}\//", $img)) {$from = "local"; $sel = preg_replace("/^{local}\//", "", $img); }
			else {$from = "url"; $url = $s->replace_vars($img, $packs); }
			$text = $sm;
		}
		else if (preg_match("/^{local}\//", $img)) {$used[preg_replace("/^{local}\//", "", $img)] = 1; }
		$i++;
	}
	
	$designActive = 0; ?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Inserir um Smile - MZn² - A nova geração</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

function smileClick (sm, url) {
	window.opener.MZn2_addSmile(sm, url);
}

function quit() {
	window.opener.smWindow = null;
}

document.onkeypress = new Function ("if (event.keyCode == 27) {quit(); window.close(); } ");

// -->
</script>
	</head>
	<body onbeforeunload="quit(); " onunload="quit(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_smilies.jpg" width="400" height="70"><br>
		<table width="400" align="center" cellpadding="2" cellspacing="2">
			<tr><td>
				<div align="center" style="border:1px solid #808080; width:390px; height:200px; overflow:auto; "><table cellpadding="2" cellspacing="2" width="100%">
<?php

echo "<tr>";
$db = $s->vars_import($s->sys['sm']['list']);
$packs = $s->vars_import($s->sys['sm']['packs']);
$packs['local'] = $s->cfg['dir']['smilies'];
$cMax = 3; $c = 1; foreach ($db as $sm => $img) {
	$img = $s->replace_vars($img, $packs);
	echo "<td width=\"". intval(100 / $cMax) ."%\" align=\"center\" valign=\"middle\" style=\"cursor:hand; \" onclick=\"smileClick('". $sm ."', '". $img ."'); \"><a href=\"#\" onclick=\"return false; \"><img src=\"". $img ."\" alt=\"\" border=\"0\" style=\"cursor:hand; \" /></a></td>";
	if ($c >= $cMax) {echo "</tr>\n<tr>"; $c = 1; } else {$c++; }
}
echo "</tr>";

?>
				</table></div>
			</td></tr>
			<tr><td height="1"></td></tr>
			<tr><td align="center">
				<button type="button" onclick="quit(); window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Fechar</b></button>
			</td></tr>
		</table>
		
	</body>
</html><?php }


?>
