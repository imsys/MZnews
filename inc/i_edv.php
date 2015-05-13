<?php $designActive = 0; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

$act = $s->req['act']; if (!$act) {$act = "index"; }

$msie = 1;
if (!preg_match("/msie (5.5|6)/i", $s->req['HTTP_USER_AGENT']) || preg_match("/opera/i", $s->req['HTTP_USER_AGENT'])) {
	$s->req['only_mznCode'] = 1;
	$msie = 0;
}
if ($s->req['only_mznCode']) {
	if ($act == "index") {$act = "mznCode"; }
}
else {$s->req['only_mznCode'] = 0; }

if ($act == "index" && $s->req['mode'] == "c") {$act = "mznCode"; }

$s->cfg['block:config'] = 1;

//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	$m->req_login();
	
	if ($s->req['news_id']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['news_id']) {continue; }
			$v['news'] = $m->news_parse($v['news'], $v['data']['b'], $v['data']['c'], $v['data']['s']);
			$contents = $m->mznCode_to_html($v['news']);
		}
	}
	else if ($s->req['fnews_id']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['fnews_id']) {continue; }
			$v['fnews'] = $m->news_parse($v['fnews'], $v['data']['b'], $v['data']['c'], $v['data']['s']);
			$contents = $m->mznCode_to_html($v['fnews']);
		}
	}
	else if ($s->req['MZn2_edv_code']) {$contents = $m->mznCode_to_html($s->req['MZn2_edv_code']); }
	else {
		if (!$s->cat[$cat]['news']['default_align']) {$s->cat[$cat]['news']['default_align'] = "left"; }
		$contents = "<div align=\"". $s->cat[$cat]['news']['default_align'] ."\"></div>";
	}
	
	if ($s->req['obj'] == "news") {$obj1 = "news"; $obj2 = "data][nm"; }
	else if ($s->req['obj'] == "fnews") {$obj1 = "fnews"; $obj2 = "data][fm"; }
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title></title>
<style type="text/css">
<!--
@import "img/{skin}/style.css";
-->
</style>
<script type="text/javascript" language="JavaScript">
<!-- ;

var ext_formName     = "formCenter";
var ext_elementCName = "c[<?php echo $obj1; ?>]";
var ext_elementTName = "c[<?php echo $obj2; ?>]";
var smWindow = null;

function MZn2_edv_init (id) {
	document.execCommand('LiveResize', 0, 1);
	MZn2_edv_update(id, 1);
}

function MZn2_edv_quit (id) {
	if (smWindow) {smWindow.close(); }
}

function MZn2_edv_exec (id, exec, value) {
	var obj = document.getElementById("MZn2_"+ id +"_obj");
	if (!obj) {return; }
	obj.focus(); document.selection.createRange().execCommand(exec, 0, value); obj.focus(); 
	MZn2_edv_update(id, 1);
}

function MZn2_addSmile (sm, img) {
	var obj = document.getElementById("MZn2_edv_obj");
	obj.focus();
	document.selection.createRange().pasteHTML('<img src="'+ img +'" align="absmiddle" border="0">');
	obj.focus();
}

function MZn2_edv_ui (id, command) {
	var obj = document.getElementById("MZn2_"+ id +"_obj");
	if (!obj) {return; }
	if (command == "Font") {
		var x = new Array();
		x['font'] = document.selection.createRange().queryCommandValue('FontName');
		x['size'] = document.selection.createRange().queryCommandValue('FontSize');
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_font', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result){
			if (result['font']) {document.selection.createRange().execCommand('FontName', 0, result['font']); }
			if (result['size']) {document.selection.createRange().execCommand('FontSize', 0, result['size']); }
			obj.focus();
		}
	}
	else if (command == "FgColor") {
		var x = new Array();
		x['mode'] = 'fg';
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_color', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (result['color']) {document.selection.createRange().execCommand('ForeColor', 0, result['color']); }
			obj.focus();
		}
	}
	else if (command == "BgColor") {
		var x = new Array();
		x['mode'] = 'bg';
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_color', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (result['color']) {document.selection.createRange().execCommand('BackColor', 0, result['color']); }
			obj.focus();
		}
	}
	else if (command == "Link") {
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_link', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (/^www\./i.test(result['url'])) {result['url'] = "http://"+ result['url']; }
			var link_target = ''; if (result['target'] == "self") {link_target = 'self'; }
			if (result['url']) {document.selection.createRange().execCommand('CreateLink', false, result['url'] +"|"+ link_target); }
			obj.focus();
		}
	}
	else if (command == "Mail") {
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_mail', x, 'dialogWidth:406px; dialogHeight:160px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (result['mail']) {document.selection.createRange().execCommand('CreateLink', false, "mailto:"+ result['mail']); }
			obj.focus();
		}
	}
	else if (command == "Image") {
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_image', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (result['align']) {image_align = result['align']; } else {image_align = 'right'; }
			if (result['url']) {document.selection.createRange().pasteHTML('<img src="'+ result['url'] +'" align="'+ image_align +'" border="0">'); }
			obj.focus();
		}
	}
	else if (command == "Flash") {
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_flash', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (result['width'] && result['height'] && result['url']) {document.selection.createRange().pasteHTML('<img style="WIDTH: '+ result['width'] +'px; HEIGHT: '+ result['height'] +'px" src="img/editor_flash_obj.gif" flash_url="'+ result['url'] +'" width="'+ result['width'] +'" height="'+ result['height'] +'" border="0">'); }
			obj.focus();
		}
	}
	else if (command == "Smile") {
		if (smWindow) {smWindow.focus(); }
		else {
			smWindow = window.open('index.php?s={session}&sec=smilies&act=list', '_blank', 'width=406,height=330,directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no');
			smWindow.resizeTo(410, 330);
		}
	}
	MZn2_edv_update(id);
}

var sel = new Array();
function MZn2_edv_update (id, nc) {
	var x = document.selection.createRange();
	if (x.queryCommandState('Bold') && !sel['Bold']) {MZn2_edv_alterstate(id, 'bold', 'toolSelected'); sel['Bold'] = true; }
	else if (!x.queryCommandState('Bold') && sel['Bold']) {MZn2_edv_alterstate(id, 'bold', 'toolNormal'); sel['Bold'] = false; }
	if (x.queryCommandState('Italic') && !sel['Italic']) {MZn2_edv_alterstate(id, 'italic', 'toolSelected'); sel['Italic'] = true; }
	else if (!x.queryCommandState('Italic') && sel['Italic']) {MZn2_edv_alterstate(id, 'italic', 'toolNormal'); sel['Italic'] = false; }
	if (x.queryCommandState('Underline') && !sel['Underline']) {MZn2_edv_alterstate(id, 'underline', 'toolSelected'); sel['Underline'] = true; }
	else if (!x.queryCommandState('Underline') && sel['Underline']) {MZn2_edv_alterstate(id, 'underline', 'toolNormal'); sel['Underline'] = false; }
	if (x.queryCommandState('JustifyLeft') && !sel['JustifyLeft']) {MZn2_edv_alterstate(id, 'left', 'toolSelected'); sel['JustifyLeft'] = true; }
	else if (!x.queryCommandState('JustifyLeft') && sel['JustifyLeft']) {MZn2_edv_alterstate(id, 'left', 'toolNormal'); sel['JustifyLeft'] = false; }
	if (x.queryCommandState('JustifyCenter') && !sel['JustifyCenter']) {MZn2_edv_alterstate(id, 'center', 'toolSelected'); sel['JustifyCenter'] = true; }
	else if (!x.queryCommandState('JustifyCenter') && sel['JustifyCenter']) {MZn2_edv_alterstate(id, 'center', 'toolNormal'); sel['JustifyCenter'] = false; }
	if (x.queryCommandState('JustifyRight') && !sel['JustifyRight']) {MZn2_edv_alterstate(id, 'right', 'toolSelected'); sel['JustifyRight'] = true; }
	else if (!x.queryCommandState('JustifyRight') && sel['JustifyRight']) {MZn2_edv_alterstate(id, 'right', 'toolNormal'); sel['JustifyRight'] = false; }
	if (x.queryCommandState('JustifyFull') && !sel['JustifyFull']) {MZn2_edv_alterstate(id, 'justify', 'toolSelected'); sel['JustifyFull'] = true; }
	else if (!x.queryCommandState('JustifyFull') && sel['JustifyFull']) {MZn2_edv_alterstate(id, 'justify', 'toolNormal'); sel['JustifyFull'] = false; }
	var obj1 = document.getElementById("MZn2_"+ id +"_obj"), obj2 = document.getElementById("MZn2_"+ id +"_code");
	if (!obj1 || !obj2) {return; }
	obj2.value = obj1.innerHTML;
	if (ext_formName) {
		if (ext_elementCName) {parent.document.forms[ext_formName].elements[ext_elementCName].value = obj2.value; }
		if (ext_elementTName) {parent.document.forms[ext_formName].elements[ext_elementTName].value = "h"; }
		if (!nc) {parent.form_changed = 1; }
	}
}

function MZn2_edv_updateCode (id) {
	var obj1 = document.getElementById("MZn2_"+ id +"_obj"), obj2 = document.getElementById("MZn2_"+ id +"_code");
	if (!obj1 || !obj2) {return; } obj1.innerHTML = obj2.value;
}

function MZn2_edv_alterstate (id, subid, className) {
	var obj = document.getElementById("MZn2_"+ id +"_"+ subid);
	if (!obj) {return; }
	obj.className = className;
}

function MZn2_edv_togglemode (id) {
	var obj1 = document.getElementById("MZn2_"+ id +"_obj"), obj2 = document.getElementById("MZn2_"+ id +"_code"), link = document.getElementById("MZn2_"+ id +"_toggle");
	if (!obj1 || !obj2 || !link) {return; }
	if (obj1.style.display == 'none') {obj1.style.display = 'inline'; obj2.style.display = 'none'; link.innerHTML = 'Editar código'; obj1.focus(); }
	else {obj1.style.display = 'none'; obj2.style.display = 'inline'; link.innerHTML = 'Voltar ao editor HTML'; obj2.focus(); }
}

// -->
</script>
	</head>
	<body onunload="MZn2_edv_quit('edv'); ">
		
<?php $l->tabs = 2; $l->form("edv", "mznCode", array("obj" => $s->req['obj'])); ?>
			<div id="MZn2_edv_toolbar" class="toolbar"><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Font'); return false; "><img src="img/editor_font.gif" width="23" height="22" border="0" alt="Formatar fonte" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'Bold'); return false; "><img id="MZn2_edv_bold" src="img/editor_bold.gif" width="23" height="22" border="0" alt="Texto em negrito (Ctrl+B)" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'Italic'); return false; "><img id="MZn2_edv_italic" src="img/editor_italic.gif" width="23" height="22" border="0" alt="Texto em itálico (Ctrl+I)" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'Underline'); return false; "><img id="MZn2_edv_underline" src="img/editor_under.gif" width="23" height="22" border="0" alt="Texto sublinhado (Ctrl+U)" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'FgColor'); return false; "><img src="img/editor_font_fgcolor.gif" width="23" height="22" border="0" alt="Alterar cor da fonte" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'BgColor'); return false; "><img src="img/editor_font_bgcolor.gif" width="23" height="22" border="0" alt="Alterar cor de fundo" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'JustifyLeft'); return false; "><img id="MZn2_edv_left" src="img/editor_left.gif" width="23" height="22" border="0" alt="Alinhar à esquerda" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'JustifyCenter'); return false; "><img id="MZn2_edv_center" src="img/editor_center.gif" width="23" height="22" border="0" alt="Centralizar" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'JustifyRight'); return false; "><img id="MZn2_edv_right" src="img/editor_right.gif" width="23" height="22" border="0" alt="Alinhar à direita" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'JustifyFull'); return false; "><img id="MZn2_edv_justify" src="img/editor_justify.gif" width="23" height="22" border="0" alt="Justificar" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Image'); return false; "><img src="img/editor_image.gif" width="23" height="22" border="0" alt="Inserir imagem" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Link'); return false; "><img src="img/editor_link.gif" width="23" height="22" border="0" alt="Criar link" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Mail'); return false; "><img src="img/editor_email.gif" width="23" height="22" border="0" alt="Criar link de e-mail" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'Unlink'); return false; "><img src="img/editor_unlink.gif" width="23" height="22" border="0" alt="Remover link" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'InsertUnorderedList'); return false; "><img src="img/editor_list.gif" width="23" height="22" border="0" alt="Inserir / Remover lista" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'InsertOrderedList'); return false; "><img src="img/editor_listnum.gif" width="23" height="22" border="0" alt="Inserir / Remover lista numerada" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'InsertHorizontalRule'); return false; "><img src="img/editor_hr.gif" width="23" height="22" border="0" alt="Inserir linha horizontal" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'InsertMarquee'); return false; "><img src="img/editor_move.gif" width="23" height="22" border="0" alt="Inserir texto em movimento (marquee)" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Flash'); return false; "><img src="img/editor_flash.gif" width="23" height="22" border="0" alt="Inserir objeto flash" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Smile'); return false; "><img src="img/editor_smilies.gif" width="23" height="22" border="0" alt="Inserir smile" /></a></div>
			<div id="MZn2_edv_obj" class="edv" tabindex="1" contentEditable="true" onmouseup="MZn2_edv_update('edv'); " onkeyup="MZn2_edv_update('edv'); " onselect="MZn2_edv_update('edv'); " onclick="MZn2_edv_update('edv'); " onchange="MZn2_edv_update('edv'); "><?php echo $contents; ?></div>
			<textarea id="MZn2_edv_code" name="MZn2_edv_code" class="edvCode" style="display:none; " wrap="off" onkeyup="MZn2_edv_updateCode('edv'); " onselect="MZn2_edv_updateCode('edv'); " onclick="MZn2_edv_updateCode('edv'); " onchange="MZn2_edv_updateCode('edv'); "></textarea>
			<div id="MZn2_edv_footer" class="edvFooter"><span class="edvTabSelected">Visual</span><span class="edvTab"><a href="#" onclick="document.forms['formCenter'].submit(); return false; ">mznCode</a></span><!-- &nbsp;&middot;&nbsp;<a id="MZn2_edv_toggle" href="#" onclick="MZn2_edv_togglemode('edv'); return false; ">Editar código</a> --></div>
			<script> MZn2_edv_init('edv'); </script>
<?php $l->form_end(); ?>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act mznCode
//-----------------------------------------------------------------------------
else if ($act == "mznCode") {
	$m->req_login();
	
	if ($s->req['news_id']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['news_id']) {continue; }
			$v['news'] = $m->news_parse($v['news'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $v['data']['o'], "code");
			$contents = $v['news'];
		}
	}
	else if ($s->req['fnews_id']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['fnews_id']) {continue; }
			$v['fnews'] = $m->news_parse($v['fnews'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $v['data']['o'], "code");
			$contents = $v['fnews'];
		}
	}
	else if ($s->req['comment_id']) {
		$db = $s->db_table_open($s->cfg['file']['comments']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['comment_id']) {continue; }
			$v['comment'] = $m->news_parse($v['comment'], 0, $s->cat[$cat]['comments']['mzncode'], $s->cat[$cat]['comments']['smilies'], 1, "code");
			$contents = $v['comment'];
		}
	}
	else if ($s->req['MZn2_edv_code']) {$contents = $m->html_to_mznCode($s->req['MZn2_edv_code']); }
	
	if ($s->req['obj'] == "news") {$obj1 = "news"; $obj2 = "data][nm"; }
	else if ($s->req['obj'] == "fnews") {$obj1 = "fnews"; $obj2 = "data][fm"; }
	else if ($s->req['obj'] == "comment") {$obj1 = "comment"; $obj2 = null; }
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title></title>
<style type="text/css">
<!--
@import "img/{skin}/style.css";
-->
</style>
<script type="text/javascript" language="JavaScript">
<!-- ;

var msie             = <?php echo $msie; ?>;
var only_mznCode     = <?php echo $s->req['only_mznCode']; ?>;
var ext_formName     = "formCenter";
var ext_elementCName = "c[<?php echo $obj1; ?>]";
var ext_elementTName = "";<?php if ($obj2) { ?> ext_elementTName = "c[<?php echo $obj2; ?>]";<?php echo "\n"; } ?>
var smWindow = null;

function MZn2_edv_init (id) {
	MZn2_edv_update(id, 1);
}

function MZn2_edv_quit (id) {
	if (smWindow) {smWindow.close(); }
}

function MZn2_edv_paste (id, newText) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; } obj.focus();
	if (msie) {
		var innerText = document.selection.createRange().text;
		document.selection.createRange().text = newText.replace(/\{text\}/, innerText);
		document.selection.createRange().select();
	}
	else {newText = newText.replace(/\{text\}/, ""); obj.value += newText; }
}

function MZn2_edv_exec (id, tag, more) {
	if (tag == "list") {MZn2_edv_paste(id, "[list]\n[*]{text}\n[*]\n[*]\n[/list]"); }
	else if (tag == "listnum") {MZn2_edv_paste(id, "[listnum]\n[*]{text}\n[*]\n[*]\n[/listnum]"); }
	else if (tag == "hr") {MZn2_edv_paste(id, "[hr]"); }
	else if (more) {MZn2_edv_paste(id, "["+tag+"="+more+"]{text}[/"+tag+"]"); }
	else {MZn2_edv_paste(id, "["+tag+"]{text}[/"+tag+"]"); }
}

function MZn2_addSmile (sm, img) {
	var obj = document.getElementById("MZn2_edv_code");
	obj.focus();
	if (msie && !only_mznCode) {MZn2_edv_paste('edv', '[img=absmiddle]'+ img +'[/img]'); }
	else {MZn2_edv_paste('edv', ' '+ sm +' '); }
	obj.focus();
}

function MZn2_edv_ui (id, command) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; }
	if (command == "Font") {
		if (!msie) {MZn2_edv_paste(id, "[font=Verdana][size=2][/size][/font]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_font', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result){
			var paste = "{text}";
			if (result['size']) {paste = "[size="+ result['size'] +"]"+ paste +"[/size]"; }
			if (result['font']) {paste = "[font="+ result['font'] +"]"+ paste +"[/font]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "FgColor") {
		if (!msie) {MZn2_edv_paste(id, "[color=#000000][/color]"); return false; }
		var x = new Array();
		x['mode'] = 'fg';
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_color', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "{text}";
			if (result['color']) {paste = "[color="+ result['color'] +"]"+ paste +"[/color]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "BgColor") {
		if (!msie) {MZn2_edv_paste(id, "[bgcolor=#FFFFFF][/bgcolor]"); return false; }
		var x = new Array();
		x['mode'] = 'bg';
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_color', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "{text}";
			if (result['color']) {paste = "[bgcolor="+ result['color'] +"]"+ paste +"[/bgcolor]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Link") {
		if (!msie) {MZn2_edv_paste(id, "[url=http://www.mznews.kit.net][/url]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_link', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (/^www\./i.test(result['url'])) {result['url'] = "http://"+ result['url']; }
			var link_target = ''; if (result['target'] == "self") {link_target = ',self'; }
			var paste = "{text}";
			if (result['url']) {paste = "[url="+ result['url'] + link_target +"]"+ paste +"[/url]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Mail") {
		if (!msie) {MZn2_edv_paste(id, "[email=email@dominio.com][/email]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_mail', x, 'dialogWidth:406px; dialogHeight:160px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "{text}";
			if (result['mail']) {paste = "[email="+ result['mail'] +"]"+ paste +"[/email]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Image") {
		if (!msie) {MZn2_edv_paste(id, "[img=200x300,absmiddle]http://www.site.com.br/imagem.gif[/img]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_image', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var image_align = ""; if (result['align']) {image_align = "="+ result['align']; }
			var paste = "";
			if (result['url']) {paste = "[img"+ image_align +"]"+ result['url'] +"[/img]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Flash") {
		if (!msie) {MZn2_edv_paste(id, "[flash=200x200]http://www.site.com.br/flash.swf[/flash]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_flash', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "";
			if (result['width'] && result['height'] && result['url']) {paste = "[flash="+ result['width'] +"x"+ result['height'] +"]"+ result['url'] +"[/flash]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Smile") {
		if (smWindow) {smWindow.focus(); }
		else {
			smWindow = window.open('index.php?s={session}&sec=smilies&act=list', '_blank', 'width=406,height=330,directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no');
			smWindow.resizeTo(410, 330);
		}
	}
	MZn2_edv_update(id);
}

function MZn2_edv_update (id, nc) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; }
	if (ext_formName) {
		if (ext_elementCName) {parent.document.forms[ext_formName].elements[ext_elementCName].value = obj.value; }
		if (ext_elementTName) {parent.document.forms[ext_formName].elements[ext_elementTName].value = "c"; }
		if (!nc) {parent.form_changed = 1; }
	}
}

// -->
</script>
	</head>
	<body onunload="MZn2_edv_quit('edv'); ">
		
<?php $l->tabs = 2; $l->form("edv", "index", array("obj" => $s->req['obj'])); ?>
			<div id="MZn2_edv_toolbar" class="toolbar"><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Font'); return false; "><img src="img/editor_font.gif" width="23" height="22" border="0" alt="Formatar fonte" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'b'); return false; "><img id="MZn2_edv_bold" src="img/editor_bold.gif" width="23" height="22" border="0" alt="Texto em negrito" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'i'); return false; "><img id="MZn2_edv_italic" src="img/editor_italic.gif" width="23" height="22" border="0" alt="Texto em itálico" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'u'); return false; "><img id="MZn2_edv_underline" src="img/editor_under.gif" width="23" height="22" border="0" alt="Texto sublinhado" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'FgColor'); return false; "><img src="img/editor_font_fgcolor.gif" width="23" height="22" border="0" alt="Alterar cor da fonte" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'BgColor'); return false; "><img src="img/editor_font_bgcolor.gif" width="23" height="22" border="0" alt="Alterar cor de fundo" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'left'); return false; "><img id="MZn2_edv_left" src="img/editor_left.gif" width="23" height="22" border="0" alt="Alinhar à esquerda" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'center'); return false; "><img id="MZn2_edv_center" src="img/editor_center.gif" width="23" height="22" border="0" alt="Centralizar" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'right'); return false; "><img id="MZn2_edv_right" src="img/editor_right.gif" width="23" height="22" border="0" alt="Alinhar à direita" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'justify'); return false; "><img id="MZn2_edv_justify" src="img/editor_justify.gif" width="23" height="22" border="0" alt="Justificar" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Image'); return false; "><img src="img/editor_image.gif" width="23" height="22" border="0" alt="Inserir imagem" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Link'); return false; "><img src="img/editor_link.gif" width="23" height="22" border="0" alt="Criar link" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Mail'); return false; "><img src="img/editor_email.gif" width="23" height="22" border="0" alt="Criar link de e-mail" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'list'); return false; "><img src="img/editor_list.gif" width="23" height="22" border="0" alt="Inserir lista" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'listnum'); return false; "><img src="img/editor_listnum.gif" width="23" height="22" border="0" alt="Inserir lista numerada" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'hr'); return false; "><img src="img/editor_hr.gif" width="23" height="22" border="0" alt="Inserir linha horizontal" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'move'); return false; "><img src="img/editor_move.gif" width="23" height="22" border="0" alt="Inserir texto em movimento (marquee)" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Flash'); return false; "><img src="img/editor_flash.gif" width="23" height="22" border="0" alt="Inserir objeto flash" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Smile'); return false; "><img src="img/editor_smilies.gif" width="23" height="22" border="0" alt="Inserir smile" /></a></div>
			<textarea id="MZn2_edv_code" name="MZn2_edv_code" class="edvCode" tabindex="1" wrap="off" onmouseup="MZn2_edv_update('edv'); " onkeyup="MZn2_edv_update('edv'); " onselect="MZn2_edv_update('edv'); " onclick="MZn2_edv_update('edv'); " onchange="MZn2_edv_update('edv'); "><?php echo $s->quote_safe($contents); ?></textarea>
			<div id="MZn2_edv_footer" class="edvFooter"><?php if (!$s->req['only_mznCode']) {?><span class="edvTab"><a href="#" onclick="document.forms['formCenter'].submit(); return false; ">Visual</a></span><?php } ?><span class="edvTabSelected">mznCode</span></div>
			<script> MZn2_edv_init('edv'); </script>
<?php $l->form_end(); ?>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act comments
//-----------------------------------------------------------------------------
else if ($act == "comments") {
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title></title>
<style type="text/css">
<!--
.toolbar {padding-left:2px; background-color:#D5D8D6; text-align:left; width:448px; height:auto; }
.toolNormal {background-color:none; width:23px; height:22px; }
.toolSelected {background-color:#AEB3B0; width:23px; height:22px; }
.toolLink {background-color:none; width:23px; height:22px; font-size:22px; }
.toolLink:hover {background-color:#8F9693; width:23px; height:22px; font-size:22px; }
.toolSep {margin-left:2px; margin-right:2px; }
#tbScroll {width:428px; height:22px; position:static; overflow:hidden; }

.edvCode {text-align:left; overflow:auto; width:438px; height:137px; padding:5px; border:1px solid #D5D8D6; border-top:0px; }

.edvTabSelected {background-color:#D5D8D6; padding:0px 10px 0px 10px; }
.edvTab {padding:0px 10px 0px 10px; }
-->
</style>
<script type="text/javascript" language="JavaScript">
<!-- ;

var msie             = <?php echo $msie; ?>;
var ext_formName     = "MZn2_AddComment";
var ext_elementCName = "mzn[comment]";

<?php if ($s->req['limit']) { ?>var limit = <?php echo $s->req['limit'] .";\n"; } else { ?>var limit = 0;<?php echo "\n"; } ?>
var smWindow = null;

var dynScrollObj = new Array(); var dynScrollSpeed = 1; var dynScrollAmount = 1;
function dynScroll(obj, act) {
	if (!obj || !act || !document.getElementById(obj)) {return false; }
	var object = document.getElementById(obj);
	if (act == "none") {
		dynScrollObj[obj] = false;
	}
	else {
		dynScrollObj[obj] = true;
		dynScroll_Do(obj, act);
	}
}
function dynScroll_Do(obj, act) {
	if (!obj || !act || !document.getElementById(obj) || !dynScrollObj[obj]) {return false; }
	var object = document.getElementById(obj);
	if (act == "up") {object.scrollTop -= dynScrollAmount; }
	else if (act == "down") {object.scrollTop += dynScrollAmount; }
	else if (act == "left") {object.scrollLeft -= dynScrollAmount; }
	else if (act == "right") {object.scrollLeft += dynScrollAmount; }
	setTimeout("dynScroll_Do('"+ obj +"', '"+ act +"'); ", dynScrollSpeed);
}

function MZn2_edv_init (id) {
	MZn2_edv_update(id);
}

function MZn2_edv_quit (id) {
	if (smWindow) {smWindow.close(); }
}

function MZn2_edv_paste (id, newText) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; } obj.focus();
	if (msie) {
		var innerText = document.selection.createRange().text;
		document.selection.createRange().text = newText.replace(/\{text\}/, innerText);
		document.selection.createRange().select();
	}
	else {newText = newText.replace(/\{text\}/, ""); obj.value += newText; }
}

function MZn2_edv_exec (id, tag, more) {
	if (tag == "list") {MZn2_edv_paste(id, "[list]\n[*]{text}\n[*]\n[*]\n[/list]"); }
	else if (tag == "listnum") {MZn2_edv_paste(id, "[listnum]\n[*]{text}\n[*]\n[*]\n[/listnum]"); }
	else if (tag == "hr") {MZn2_edv_paste(id, "[hr]"); }
	else if (more) {MZn2_edv_paste(id, "["+tag+"="+more+"]{text}[/"+tag+"]"); }
	else {MZn2_edv_paste(id, "["+tag+"]{text}[/"+tag+"]"); }
}

function MZn2_addSmile (sm, img) {
	var obj = document.getElementById("MZn2_edv_code");
	obj.focus();
	MZn2_edv_paste('edv', ' '+ sm +' ');
	obj.focus();
}

function MZn2_edv_ui (id, command) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; }
	if (command == "Font") {
		if (!msie) {MZn2_edv_paste(id, "[font=Verdana][size=2][/size][/font]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_font', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result){
			var paste = "{text}";
			if (result['size']) {paste = "[size="+ result['size'] +"]"+ paste +"[/size]"; }
			if (result['font']) {paste = "[font="+ result['font'] +"]"+ paste +"[/font]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "FgColor") {
		if (!msie) {MZn2_edv_paste(id, "[color=#000000][/color]"); return false; }
		var x = new Array();
		x['mode'] = 'fg';
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_color', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "{text}";
			if (result['color']) {paste = "[color="+ result['color'] +"]"+ paste +"[/color]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "BgColor") {
		if (!msie) {MZn2_edv_paste(id, "[bgcolor=#FFFFFF][/bgcolor]"); return false; }
		var x = new Array();
		x['mode'] = 'bg';
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_color', x, 'dialogWidth:406px; dialogHeight:270px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "{text}";
			if (result['color']) {paste = "[bgcolor="+ result['color'] +"]"+ paste +"[/bgcolor]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Link") {
		if (!msie) {MZn2_edv_paste(id, "[url=http://www.mznews.kit.net][/url]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_link', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			if (/^www\./i.test(result['url'])) {result['url'] = "http://"+ result['url']; }
			var link_target = ''; if (result['target'] == "self") {link_target = ',self'; }
			var paste = "{text}";
			if (result['url']) {paste = "[url="+ result['url'] + link_target +"]"+ paste +"[/url]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Mail") {
		if (!msie) {MZn2_edv_paste(id, "[email=email@dominio.com][/email]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_mail', x, 'dialogWidth:406px; dialogHeight:160px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "{text}";
			if (result['mail']) {paste = "[email="+ result['mail'] +"]"+ paste +"[/email]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Image") {
		if (!msie) {MZn2_edv_paste(id, "[img=200x300,absmiddle]http://www.site.com.br/imagem.gif[/img]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_image', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var image_align = ""; if (result['align']) {image_align = "="+ result['align']; }
			var paste = "";
			if (result['url']) {paste = "[img"+ image_align +"]"+ result['url'] +"[/img]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Flash") {
		if (!msie) {MZn2_edv_paste(id, "[flash=200x200]http://www.site.com.br/flash.swf[/flash]"); return false; }
		var x = new Array();
		result = showModalDialog('index.php?s={session}&sec=edv&act=dialog_flash', x, 'dialogWidth:406px; dialogHeight:180px; help:No; resizable:No; scroll:No; status:No; unadorned:Yes');
		obj.focus();
		if (result) {
			var paste = "";
			if (result['width'] && result['height'] && result['url']) {paste = "[flash="+ result['width'] +"x"+ result['height'] +"]"+ result['url'] +"[/flash]"; }
			MZn2_edv_paste(id, paste);
			obj.focus();
		}
	}
	else if (command == "Smile") {
		if (smWindow) {smWindow.focus(); }
		else {
			smWindow = window.open('index.php?s={session}&sec=smilies&act=list', '_blank', 'width=406,height=330,directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no');
			smWindow.resizeTo(410, 330);
		}
	}
	MZn2_edv_update(id);
}

function MZn2_edv_update (id) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; }
	if (limit && obj.value.length > limit) {obj.value = obj.value.substring(0, limit); }
	if (ext_formName) {
		if (ext_elementCName) {parent.document.forms[ext_formName].elements[ext_elementCName].value = obj.value; }
	}
}

// -->
</script>
	</head>
	<body onunload="MZn2_edv_quit('edv'); ">
		
<?php $l->tabs = 2; $l->form("edv", "index", array("obj" => $s->req['obj'])); ?>
			<div id="MZn2_edv_toolbar" class="toolbar"><?php if ($s->req['mznCode']) { ?><a href="#" onclick="return false; " onmouseover="dynScroll('tbScroll', 'left'); " onmouseout="dynScroll('tbScroll', 'none'); " onmousedown="dynScrollAmount = 5; " onmouseup="dynScrollAmount = 1; " onfocus="blur(); "><img src="img/editor_scroll_l.gif" width="10" height="22" border="0" alt="" style="float:left; " /></a><a href="#" onclick="return false; " onmouseover="dynScroll('tbScroll', 'right'); " onmouseout="dynScroll('tbScroll', 'none'); " onmousedown="dynScrollAmount = 5; " onmouseup="dynScrollAmount = 1; " onfocus="blur(); "><img src="img/editor_scroll_r.gif" width="10" height="22" border="0" alt="" style="float:right; " /></a><div id="tbScroll"><nobr><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Font'); return false; "><img src="img/editor_font.gif" width="23" height="22" border="0" alt="Formatar fonte" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'b'); return false; "><img id="MZn2_edv_bold" src="img/editor_bold.gif" width="23" height="22" border="0" alt="Texto em negrito" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'i'); return false; "><img id="MZn2_edv_italic" src="img/editor_italic.gif" width="23" height="22" border="0" alt="Texto em itálico" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'u'); return false; "><img id="MZn2_edv_underline" src="img/editor_under.gif" width="23" height="22" border="0" alt="Texto sublinhado" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'FgColor'); return false; "><img src="img/editor_font_fgcolor.gif" width="23" height="22" border="0" alt="Alterar cor da fonte" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'BgColor'); return false; "><img src="img/editor_font_bgcolor.gif" width="23" height="22" border="0" alt="Alterar cor de fundo" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'left'); return false; "><img id="MZn2_edv_left" src="img/editor_left.gif" width="23" height="22" border="0" alt="Alinhar à esquerda" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'center'); return false; "><img id="MZn2_edv_center" src="img/editor_center.gif" width="23" height="22" border="0" alt="Centralizar" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'right'); return false; "><img id="MZn2_edv_right" src="img/editor_right.gif" width="23" height="22" border="0" alt="Alinhar à direita" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'align', 'justify'); return false; "><img id="MZn2_edv_justify" src="img/editor_justify.gif" width="23" height="22" border="0" alt="Justificar" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Image'); return false; "><img src="img/editor_image.gif" width="23" height="22" border="0" alt="Inserir imagem" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Link'); return false; "><img src="img/editor_link.gif" width="23" height="22" border="0" alt="Criar link" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Mail'); return false; "><img src="img/editor_email.gif" width="23" height="22" border="0" alt="Criar link de e-mail" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'list'); return false; "><img src="img/editor_list.gif" width="23" height="22" border="0" alt="Inserir lista" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'listnum'); return false; "><img src="img/editor_listnum.gif" width="23" height="22" border="0" alt="Inserir lista numerada" /></a><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'hr'); return false; "><img src="img/editor_hr.gif" width="23" height="22" border="0" alt="Inserir linha horizontal" /></a><a class="toolLink" href="#" onclick="MZn2_edv_exec('edv', 'move'); return false; "><img src="img/editor_move.gif" width="23" height="22" border="0" alt="Inserir texto em movimento (marquee)" /></a><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Flash'); return false; "><img src="img/editor_flash.gif" width="23" height="22" border="0" alt="Inserir objeto flash" /></a><?php if ($s->req['smilies']) { ?><img class="toolSep" src="img/editor_sep.gif" width="2" height="22" border="0" alt="" /><?php } } if ($s->req['smilies']) { ?><a class="toolLink" href="#" onclick="MZn2_edv_ui('edv', 'Smile'); return false; "><img src="img/editor_smilies.gif" width="23" height="22" border="0" alt="Inserir smile" /></a><?php } if ($s->req['mznCode']) { ?></nobr></div><?php } ?></div>
			<textarea id="MZn2_edv_code" name="MZn2_edv_code" class="edvCode" tabindex="1" wrap="off" onmouseup="MZn2_edv_update('edv'); " onkeyup="MZn2_edv_update('edv'); " onselect="MZn2_edv_update('edv'); " onclick="MZn2_edv_update('edv'); " onchange="MZn2_edv_update('edv'); "><?php echo $s->quote_safe($contents); ?></textarea>
			<script> MZn2_edv_init('edv'); </script>
<?php $l->form_end(); ?>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act dialog_color
//-----------------------------------------------------------------------------
else if ($act == "dialog_color") {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Paleta de Cores - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var data = window.dialogArguments;
var mode = data['mode'];
var gColor = '000000';
var gBColor = 'FFFFFF';

function init () {if (mode == "fg") {if (gColor) {setColor(gColor); } } else {if (gBColor) {setColor(gBColor); } } document.all['example'].style.color = "#"+ gColor; document.all['example'].style.backgroundColor = "#"+ gBColor; }
function setColor(fColor) {document.all['in_color'].value = "#"+ fColor; updateColor(document.all['in_color'].value); }
function updateColor(fColor) {if (mode == "fg") {document.all['example'].innerHTML = 'ABC abc 123'; document.all['example'].style.color = fColor; } else {document.all['example'].style.backgroundColor = fColor; } }

function Save() {
	window.returnValue = new Array();
	if (document.all['in_color'].value) {window.returnValue['color'] = document.all['in_color'].value; }
	window.close();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");
window.onerror = new Function("return true; ");

// -->
</script>
	</head>
	<body onselectstart="if (event && event.srcElement && event.srcElement.tagName) {var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } } " oncontextmenu="if (event && event.srcElement && event.srcElement.tagName) {var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_colors.jpg" width="400" height="70"><br>
		<table align="center" cellpadding="3" cellspacing="0">
			<tr><td><b>Selecione uma cor:</b></td></tr>
			<tr><td><img src="img/editor_colors.gif" width="370" height="60" border="0" usemap="#mapcolors" style="cursor:hand; " onclick="setColor(color); "></td></tr>
			<tr><td><b>Ou digite-a aqui:</b></td></tr>
			<tr><td><input type="text" id="in_color" onkeyup="updateColor(this.value); " style="font:8pt verdana; border:1px solid #D5D3DC; "></td></tr>
			<tr><td colspan="2" align="center">
				<table width="100%" cellpadding="0" cellspacing="0"><tr>
					<td align="center"><div style="width:180; height:30; overflow:hidden; border:1px solid #D5D3DC; " align="center"><table width="100%" height="100%" cellpadding="0" cellspacing="0"><tr><td id="example" align="center" nowrap>Selecione uma cor</td></tr></table></div></td>
					<td width="80" valign="bottom"><button onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button><br><img src="img/_blank.gif" width="1" height="2"><br><button onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button></td>
				</tr></table>
			</td></tr>
		</table>
		<script> init(); </script>
		
<map name="mapcolors">
 <area shape="rect" coords="0,0,10,10"  onmouseover="color = 'FF0000'; "><area shape="rect" coords="10,0,20,10"  onmouseover="color = 'FF3300'; "><area shape="rect" coords="20,0,30,10"  onmouseover="color = 'FF6600'; "><area shape="rect" coords="30,0,40,10"  onmouseover="color = 'FF9900'; "><area shape="rect" coords="40,0,50,10"  onmouseover="color = 'FFCC00'; "><area shape="rect" coords="50,0,60,10"  onmouseover="color = 'FFFF00'; "><area shape="rect" coords="60,0,70,10"  onmouseover="color = 'FFFF00'; "><area shape="rect" coords="70,0,80,10"  onmouseover="color = 'CCFF00'; "><area shape="rect" coords="80,0,90,10"  onmouseover="color = '99FF00'; "><area shape="rect" coords="90,0,100,10"  onmouseover="color = '66FF00'; "><area shape="rect" coords="100,0,110,10"  onmouseover="color = '33FF00'; "><area shape="rect" coords="110,0,120,10"  onmouseover="color = '00FF00'; "><area shape="rect" coords="120,0,130,10"  onmouseover="color = '00FF00'; "><area shape="rect" coords="130,0,140,10"  onmouseover="color = '00FF33'; "><area shape="rect" coords="140,0,150,10"  onmouseover="color = '00FF66'; "><area shape="rect" coords="150,0,160,10"  onmouseover="color = '00FF99'; "><area shape="rect" coords="160,0,170,10"  onmouseover="color = '00FFCC'; "><area shape="rect" coords="170,0,180,10"  onmouseover="color = '00FFFF'; "><area shape="rect" coords="180,0,190,10"  onmouseover="color = '00FFFF'; "><area shape="rect" coords="190,0,200,10"  onmouseover="color = '00CCFF'; "><area shape="rect" coords="200,0,210,10"  onmouseover="color = '0099FF'; "><area shape="rect" coords="210,0,220,10"  onmouseover="color = '0066FF'; "><area shape="rect" coords="220,0,230,10"  onmouseover="color = '0033FF'; "><area shape="rect" coords="230,0,240,10"  onmouseover="color = '0000FF'; "><area shape="rect" coords="240,0,250,10"  onmouseover="color = '0000FF'; "><area shape="rect" coords="250,0,260,10"  onmouseover="color = '3300FF'; "><area shape="rect" coords="260,0,270,10"  onmouseover="color = '6600FF'; "><area shape="rect" coords="270,0,280,10"  onmouseover="color = '9900FF'; "><area shape="rect" coords="280,0,290,10"  onmouseover="color = 'CC00FF'; "><area shape="rect" coords="290,0,300,10"  onmouseover="color = 'FF00FF'; "><area shape="rect" coords="300,0,310,10"  onmouseover="color = 'FF00FF'; "><area shape="rect" coords="310,0,320,10"  onmouseover="color = 'FF00CC'; "><area shape="rect" coords="320,0,330,10"  onmouseover="color = 'FF0099'; "><area shape="rect" coords="330,0,340,10"  onmouseover="color = 'FF0066'; "><area shape="rect" coords="340,0,350,10"  onmouseover="color = 'FF0033'; "><area shape="rect" coords="350,0,360,10"  onmouseover="color = 'FF0000'; "><area shape="rect" coords="360,0,370,10"  onmouseover="color = 'FFFFFF'; ">
 <area shape="rect" coords="0,10,10,20" onmouseover="color = 'EE0000'; "><area shape="rect" coords="10,10,20,20" onmouseover="color = 'EE2200'; "><area shape="rect" coords="20,10,30,20" onmouseover="color = 'EE5500'; "><area shape="rect" coords="30,10,40,20" onmouseover="color = 'EE8800'; "><area shape="rect" coords="40,10,50,20" onmouseover="color = 'EEBB00'; "><area shape="rect" coords="50,10,60,20" onmouseover="color = 'EEEE00'; "><area shape="rect" coords="60,10,70,20" onmouseover="color = 'EEEE00'; "><area shape="rect" coords="70,10,80,20" onmouseover="color = 'BBEE00'; "><area shape="rect" coords="80,10,90,20" onmouseover="color = '88EE00'; "><area shape="rect" coords="90,10,100,20" onmouseover="color = '55EE00'; "><area shape="rect" coords="100,10,110,20" onmouseover="color = '22EE00'; "><area shape="rect" coords="110,10,120,20" onmouseover="color = '00EE00'; "><area shape="rect" coords="120,10,130,20" onmouseover="color = '00EE00'; "><area shape="rect" coords="130,10,140,20" onmouseover="color = '00EE22'; "><area shape="rect" coords="140,10,150,20" onmouseover="color = '00EE55'; "><area shape="rect" coords="150,10,160,20" onmouseover="color = '00EE88'; "><area shape="rect" coords="160,10,170,20" onmouseover="color = '00EEBB'; "><area shape="rect" coords="170,10,180,20" onmouseover="color = '00EEEE'; "><area shape="rect" coords="180,10,190,20" onmouseover="color = '00EEEE'; "><area shape="rect" coords="190,10,200,20" onmouseover="color = '00BBEE'; "><area shape="rect" coords="200,10,210,20" onmouseover="color = '0088EE'; "><area shape="rect" coords="210,10,220,20" onmouseover="color = '0055EE'; "><area shape="rect" coords="220,10,230,20" onmouseover="color = '0022EE'; "><area shape="rect" coords="230,10,240,20" onmouseover="color = '0000EE'; "><area shape="rect" coords="240,10,250,20" onmouseover="color = '0000EE'; "><area shape="rect" coords="250,10,260,20" onmouseover="color = '2200EE'; "><area shape="rect" coords="260,10,270,20" onmouseover="color = '5500EE'; "><area shape="rect" coords="270,10,280,20" onmouseover="color = '8800EE'; "><area shape="rect" coords="280,10,290,20" onmouseover="color = 'BB00EE'; "><area shape="rect" coords="290,10,300,20" onmouseover="color = 'EE00EE'; "><area shape="rect" coords="300,10,310,20" onmouseover="color = 'EE00EE'; "><area shape="rect" coords="310,10,320,20" onmouseover="color = 'EE00BB'; "><area shape="rect" coords="320,10,330,20" onmouseover="color = 'EE0088'; "><area shape="rect" coords="330,10,340,20" onmouseover="color = 'EE0055'; "><area shape="rect" coords="340,10,350,20" onmouseover="color = 'EE0022'; "><area shape="rect" coords="350,10,360,20" onmouseover="color = 'EE0000'; "><area shape="rect" coords="360,10,370,20" onmouseover="color = 'CCCCCC'; ">
 <area shape="rect" coords="0,20,10,30" onmouseover="color = 'DD0000'; "><area shape="rect" coords="10,20,20,30" onmouseover="color = 'DD1100'; "><area shape="rect" coords="20,20,30,30" onmouseover="color = 'DD4400'; "><area shape="rect" coords="30,20,40,30" onmouseover="color = 'DD7700'; "><area shape="rect" coords="40,20,50,30" onmouseover="color = 'DDAA00'; "><area shape="rect" coords="50,20,60,30" onmouseover="color = 'DDDD00'; "><area shape="rect" coords="60,20,70,30" onmouseover="color = 'DDDD00'; "><area shape="rect" coords="70,20,80,30" onmouseover="color = 'AADD00'; "><area shape="rect" coords="80,20,90,30" onmouseover="color = '77DD00'; "><area shape="rect" coords="90,20,100,30" onmouseover="color = '44DD00'; "><area shape="rect" coords="100,20,110,30" onmouseover="color = '11DD00'; "><area shape="rect" coords="110,20,120,30" onmouseover="color = '00DD00'; "><area shape="rect" coords="120,20,130,30" onmouseover="color = '00DD00'; "><area shape="rect" coords="130,20,140,30" onmouseover="color = '00DD11'; "><area shape="rect" coords="140,20,150,30" onmouseover="color = '00DD44'; "><area shape="rect" coords="150,20,160,30" onmouseover="color = '00DD77'; "><area shape="rect" coords="160,20,170,30" onmouseover="color = '00DDAA'; "><area shape="rect" coords="170,20,180,30" onmouseover="color = '00DDDD'; "><area shape="rect" coords="180,20,190,30" onmouseover="color = '00DDDD'; "><area shape="rect" coords="190,20,200,30" onmouseover="color = '00AADD'; "><area shape="rect" coords="200,20,210,30" onmouseover="color = '0077DD'; "><area shape="rect" coords="210,20,220,30" onmouseover="color = '0044DD'; "><area shape="rect" coords="220,20,230,30" onmouseover="color = '0011DD'; "><area shape="rect" coords="230,20,240,30" onmouseover="color = '0000DD'; "><area shape="rect" coords="240,20,250,30" onmouseover="color = '0000DD'; "><area shape="rect" coords="250,20,260,30" onmouseover="color = '1100DD'; "><area shape="rect" coords="260,20,270,30" onmouseover="color = '4400DD'; "><area shape="rect" coords="270,20,280,30" onmouseover="color = '7700DD'; "><area shape="rect" coords="280,20,290,30" onmouseover="color = 'AA00DD'; "><area shape="rect" coords="290,20,300,30" onmouseover="color = 'DD00DD'; "><area shape="rect" coords="300,20,310,30" onmouseover="color = 'DD00DD'; "><area shape="rect" coords="310,20,320,30" onmouseover="color = 'DD00AA'; "><area shape="rect" coords="320,20,330,30" onmouseover="color = 'DD0077'; "><area shape="rect" coords="330,20,340,30" onmouseover="color = 'DD0044'; "><area shape="rect" coords="340,20,350,30" onmouseover="color = 'DD0011'; "><area shape="rect" coords="350,20,360,30" onmouseover="color = 'DD0000'; "><area shape="rect" coords="360,20,370,30" onmouseover="color = '999999'; ">
 <area shape="rect" coords="0,30,10,40" onmouseover="color = 'CC0000'; "><area shape="rect" coords="10,30,20,40" onmouseover="color = 'CC0000'; "><area shape="rect" coords="20,30,30,40" onmouseover="color = 'CC3300'; "><area shape="rect" coords="30,30,40,40" onmouseover="color = 'CC6600'; "><area shape="rect" coords="40,30,50,40" onmouseover="color = 'CC9900'; "><area shape="rect" coords="50,30,60,40" onmouseover="color = 'CCCC00'; "><area shape="rect" coords="60,30,70,40" onmouseover="color = 'CCCC00'; "><area shape="rect" coords="70,30,80,40" onmouseover="color = '99CC00'; "><area shape="rect" coords="80,30,90,40" onmouseover="color = '66CC00'; "><area shape="rect" coords="90,30,100,40" onmouseover="color = '33CC00'; "><area shape="rect" coords="100,30,110,40" onmouseover="color = '00CC00'; "><area shape="rect" coords="110,30,120,40" onmouseover="color = '00CC00'; "><area shape="rect" coords="120,30,130,40" onmouseover="color = '00CC00'; "><area shape="rect" coords="130,30,140,40" onmouseover="color = '00CC00'; "><area shape="rect" coords="140,30,150,40" onmouseover="color = '00CC33'; "><area shape="rect" coords="150,30,160,40" onmouseover="color = '00CC66'; "><area shape="rect" coords="160,30,170,40" onmouseover="color = '00CC99'; "><area shape="rect" coords="170,30,180,40" onmouseover="color = '00CCCC'; "><area shape="rect" coords="180,30,190,40" onmouseover="color = '00CCCC'; "><area shape="rect" coords="190,30,200,40" onmouseover="color = '0099CC'; "><area shape="rect" coords="200,30,210,40" onmouseover="color = '0066CC'; "><area shape="rect" coords="210,30,220,40" onmouseover="color = '0033CC'; "><area shape="rect" coords="220,30,230,40" onmouseover="color = '0000CC'; "><area shape="rect" coords="230,30,240,40" onmouseover="color = '0000CC'; "><area shape="rect" coords="240,30,250,40" onmouseover="color = '0000CC'; "><area shape="rect" coords="250,30,260,40" onmouseover="color = '0000CC'; "><area shape="rect" coords="260,30,270,40" onmouseover="color = '3300CC'; "><area shape="rect" coords="270,30,280,40" onmouseover="color = '6600CC'; "><area shape="rect" coords="280,30,290,40" onmouseover="color = '9900CC'; "><area shape="rect" coords="290,30,300,40" onmouseover="color = 'CC00CC'; "><area shape="rect" coords="300,30,310,40" onmouseover="color = 'CC00CC'; "><area shape="rect" coords="310,30,320,40" onmouseover="color = 'CC0099'; "><area shape="rect" coords="320,30,330,40" onmouseover="color = 'CC0066'; "><area shape="rect" coords="330,30,340,40" onmouseover="color = 'CC0033'; "><area shape="rect" coords="340,30,350,40" onmouseover="color = 'CC0000'; "><area shape="rect" coords="350,30,360,40" onmouseover="color = 'CC0000'; "><area shape="rect" coords="360,30,370,40" onmouseover="color = '666666'; ">
 <area shape="rect" coords="0,40,10,50" onmouseover="color = 'BB0000'; "><area shape="rect" coords="10,40,20,50" onmouseover="color = 'BB0000'; "><area shape="rect" coords="20,40,30,50" onmouseover="color = 'BB2200'; "><area shape="rect" coords="30,40,40,50" onmouseover="color = 'BB5500'; "><area shape="rect" coords="40,40,50,50" onmouseover="color = 'BB8800'; "><area shape="rect" coords="50,40,60,50" onmouseover="color = 'BBBB00'; "><area shape="rect" coords="60,40,70,50" onmouseover="color = 'BBBB00'; "><area shape="rect" coords="70,40,80,50" onmouseover="color = '88BB00'; "><area shape="rect" coords="80,40,90,50" onmouseover="color = '55BB00'; "><area shape="rect" coords="90,40,100,50" onmouseover="color = '22BB00'; "><area shape="rect" coords="100,40,110,50" onmouseover="color = '00BB00'; "><area shape="rect" coords="110,40,120,50" onmouseover="color = '00BB00'; "><area shape="rect" coords="120,40,130,50" onmouseover="color = '00BB00'; "><area shape="rect" coords="130,40,140,50" onmouseover="color = '00BB00'; "><area shape="rect" coords="140,40,150,50" onmouseover="color = '00BB22'; "><area shape="rect" coords="150,40,160,50" onmouseover="color = '00BB55'; "><area shape="rect" coords="160,40,170,50" onmouseover="color = '00BB88'; "><area shape="rect" coords="170,40,180,50" onmouseover="color = '00BBBB'; "><area shape="rect" coords="180,40,190,50" onmouseover="color = '00BBBB'; "><area shape="rect" coords="190,40,200,50" onmouseover="color = '0088BB'; "><area shape="rect" coords="200,40,210,50" onmouseover="color = '0055BB'; "><area shape="rect" coords="210,40,220,50" onmouseover="color = '0022BB'; "><area shape="rect" coords="220,40,230,50" onmouseover="color = '0000BB'; "><area shape="rect" coords="230,40,240,50" onmouseover="color = '0000BB'; "><area shape="rect" coords="240,40,250,50" onmouseover="color = '0000BB'; "><area shape="rect" coords="250,40,260,50" onmouseover="color = '0000BB'; "><area shape="rect" coords="260,40,270,50" onmouseover="color = '2200BB'; "><area shape="rect" coords="270,40,280,50" onmouseover="color = '5500BB'; "><area shape="rect" coords="280,40,290,50" onmouseover="color = '8800BB'; "><area shape="rect" coords="290,40,300,50" onmouseover="color = 'BB00BB'; "><area shape="rect" coords="300,40,310,50" onmouseover="color = 'BB00BB'; "><area shape="rect" coords="310,40,320,50" onmouseover="color = 'BB0088'; "><area shape="rect" coords="320,40,330,50" onmouseover="color = 'BB0055'; "><area shape="rect" coords="330,40,340,50" onmouseover="color = 'BB0022'; "><area shape="rect" coords="340,40,350,50" onmouseover="color = 'BB0000'; "><area shape="rect" coords="350,40,360,50" onmouseover="color = 'BB0000'; "><area shape="rect" coords="360,40,370,50" onmouseover="color = '333333'; ">
 <area shape="rect" coords="0,50,10,60" onmouseover="color = 'AA0000'; "><area shape="rect" coords="10,50,20,60" onmouseover="color = 'AA0000'; "><area shape="rect" coords="20,50,30,60" onmouseover="color = 'AA1100'; "><area shape="rect" coords="30,50,40,60" onmouseover="color = 'AA4400'; "><area shape="rect" coords="40,50,50,60" onmouseover="color = 'AA7700'; "><area shape="rect" coords="50,50,60,60" onmouseover="color = 'AAAA00'; "><area shape="rect" coords="60,50,70,60" onmouseover="color = 'AAAA00'; "><area shape="rect" coords="70,50,80,60" onmouseover="color = '77AA00'; "><area shape="rect" coords="80,50,90,60" onmouseover="color = '44AA00'; "><area shape="rect" coords="90,50,100,60" onmouseover="color = '11AA00'; "><area shape="rect" coords="100,50,110,60" onmouseover="color = '00AA00'; "><area shape="rect" coords="110,50,120,60" onmouseover="color = '00AA00'; "><area shape="rect" coords="120,50,130,60" onmouseover="color = '00AA00'; "><area shape="rect" coords="130,50,140,60" onmouseover="color = '00AA00'; "><area shape="rect" coords="140,50,150,60" onmouseover="color = '00AA11'; "><area shape="rect" coords="150,50,160,60" onmouseover="color = '00AA44'; "><area shape="rect" coords="160,50,170,60" onmouseover="color = '00AA77'; "><area shape="rect" coords="170,50,180,60" onmouseover="color = '00AAAA'; "><area shape="rect" coords="180,50,190,60" onmouseover="color = '00AAAA'; "><area shape="rect" coords="190,50,200,60" onmouseover="color = '0077AA'; "><area shape="rect" coords="200,50,210,60" onmouseover="color = '0044AA'; "><area shape="rect" coords="210,50,220,60" onmouseover="color = '0011AA'; "><area shape="rect" coords="220,50,230,60" onmouseover="color = '0000AA'; "><area shape="rect" coords="230,50,240,60" onmouseover="color = '0000AA'; "><area shape="rect" coords="240,50,250,60" onmouseover="color = '0000AA'; "><area shape="rect" coords="250,50,260,60" onmouseover="color = '0000AA'; "><area shape="rect" coords="260,50,270,60" onmouseover="color = '1100AA'; "><area shape="rect" coords="270,50,280,60" onmouseover="color = '4400AA'; "><area shape="rect" coords="280,50,290,60" onmouseover="color = '7700AA'; "><area shape="rect" coords="290,50,300,60" onmouseover="color = 'AA00AA'; "><area shape="rect" coords="300,50,310,60" onmouseover="color = 'AA00AA'; "><area shape="rect" coords="310,50,320,60" onmouseover="color = 'AA0077'; "><area shape="rect" coords="320,50,330,60" onmouseover="color = 'AA0044'; "><area shape="rect" coords="330,50,340,60" onmouseover="color = 'AA0011'; "><area shape="rect" coords="340,50,350,60" onmouseover="color = 'AA0000'; "><area shape="rect" coords="350,50,360,60" onmouseover="color = 'AA0000'; "><area shape="rect" coords="360,50,370,60" onmouseover="color = '000000'; ">
</map>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act dialog_flash
//-----------------------------------------------------------------------------
else if ($act == "dialog_flash") {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Inserir Objeto Flash - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var data = window.dialogArguments;

function init() {
	
}

function Save() {
	window.returnValue = new Array();
	window.returnValue['url'] = document.all['flash_url'].value;
	window.returnValue['width'] = document.all['flash_width'].value;
	window.returnValue['height'] = document.all['flash_height'].value;
	window.close();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_flash.jpg" width="400" height="70"><br>
		<table width="400" cellpadding="3" cellspacing="0">
			<tr><td width="100" align="right"><b>URL:</b></td><td width="300"><input type="text" id="flash_url" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:294px; "></td></tr>
			<tr><td width="100" align="right"><b>Tamanho:</b></td><td width="300"><input type="text" id="flash_width" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:50px; ">&nbsp;x&nbsp;<input type="text" id="flash_height" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:50px; ">&nbsp;pixels</td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr><td colspan="2" align="center">
				<button onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button>
			</td></tr>
		</table>
		
	</body>
</html>
<?php }


//-----------------------------------------------------------------------------
// Act dialog_font
//-----------------------------------------------------------------------------
else if ($act == "dialog_font") {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Formatar Fonte - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var data = window.dialogArguments;

var scrollIntoView = 0;
function init() {
	if (data['font']) {
		switch (data['font']) {
			case "Arial":
				scrollIntoView = 1;
				ToggleSelect('font', '1', data['font']);
				break;
			case "Arial Black":
				scrollIntoView = 1;
				ToggleSelect('font', '2', data['font']);
				break;
			case "Century Gothic":
				scrollIntoView = 1;
				ToggleSelect('font', '3', data['font']);
				break;
			case "Comic Sans Ms":
				scrollIntoView = 1;
				ToggleSelect('font', '4', data['font']);
				break;
			case "Courier New":
				scrollIntoView = 1;
				ToggleSelect('font', '5', data['font']);
				break;
			case "Impact":
				scrollIntoView = 1;
				ToggleSelect('font', '6', data['font']);
				break;
			case "Lucida Sans Unicode":
				scrollIntoView = 1;
				ToggleSelect('font', '7', data['font']);
				break;
			case "MS Sans Serif":
				scrollIntoView = 1;
				ToggleSelect('font', '8', data['font']);
				break;
			case "Small Fonts":
				scrollIntoView = 1;
				ToggleSelect('font', '9', data['font']);
				break;
			case "Tahoma":
				scrollIntoView = 1;
				ToggleSelect('font', '10', data['font']);
				break;
			case "Times New Roman":
				scrollIntoView = 1;
				ToggleSelect('font', '11', data['font']);
				break;
			case "Trebuchet MS":
				scrollIntoView = 1;
				ToggleSelect('font', '12', data['font']);
				break;
			case "Verdana":
				scrollIntoView = 1;
				ToggleSelect('font', '13', data['font']);
				break;
		}
	}
	if (data['size']) {
		switch (data['size']) {
			case 1:
				scrollIntoView = 1;
				ToggleSelect('size', '1', data['size']);
				break;
			case 2:
				scrollIntoView = 1;
				ToggleSelect('size', '2', data['size']);
				break;
			case 3:
				scrollIntoView = 1;
				ToggleSelect('size', '3', data['size']);
				break;
			case 4:
				scrollIntoView = 1;
				ToggleSelect('size', '4', data['size']);
				break;
			case 5:
				scrollIntoView = 1;
				ToggleSelect('size', '5', data['size']);
				break;
			case 6:
				scrollIntoView = 1;
				ToggleSelect('size', '6', data['size']);
				break;
			case 7:
				scrollIntoView = 1;
				ToggleSelect('size', '7', data['size']);
				break;
		}
	}
}

var xSelected = new Array(); xSelected['font'] = new Array(); xSelected['size'] = new Array();
function ToggleSelect(idType, idRow, name) {
	if (xSelected[idType]['idType']) {document.all[xSelected[idType]['idType']+xSelected[idType]['idRow']].style.backgroundColor = ''; document.all[xSelected[idType]['idType']+xSelected[idType]['idRow']].style.color = ''; }
	if (xSelected[idType]['name'] == name) {xSelected[idType] = new Array(); }
	else  {
		xSelected[idType] = new Array();
		xSelected[idType]['idType'] = idType; xSelected[idType]['idRow'] = idRow; xSelected[idType]['name'] = name;
		document.all[xSelected[idType]['idType']+xSelected[idType]['idRow']].style.backgroundColor = '#5F5B71';
		document.all[xSelected[idType]['idType']+xSelected[idType]['idRow']].style.color = '#FFFFFF';
		if (scrollIntoView) {document.all[xSelected[idType]['idType']+xSelected[idType]['idRow']].scrollIntoView(); scrollIntoView = 0; }
	}
	document.all['example'].innerHTML = "<font face=\""+ xSelected['font']['name'] +"\" size=\""+ xSelected['size']['name'] +"\">ABC abc 123</font>";
}
function Save() {
	window.returnValue = new Array();
	if (xSelected['font']['name']) {window.returnValue['font'] = xSelected['font']['name']; }
	if (xSelected['size']['name']) {window.returnValue['size'] = xSelected['size']['name']; }
	window.close();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_format.jpg" width="400" height="70"><br>
		<table width="400" cellpadding="3" cellspacing="0">
			<tr><td width="330"><b>Fonte:</b></td><td width="70"><b>Tamanho:</b></td></tr>
			<tr><td>
				<div style="width:320; height:92; overflow:auto; border:1px solid #D5D3DC; ">
				<table width="100%" cellpadding="1" cellspacing="0">
					<tr><td onclick="ToggleSelect('font', '1', 'Arial')" id="font1">Arial</td></tr>
					<tr><td onclick="ToggleSelect('font', '2', 'Arial Black')" id="font2">Arial Black</td></tr>
					<tr><td onclick="ToggleSelect('font', '3', 'Century Gothic')" id="font3">Century Gothic</td></tr>
					<tr><td onclick="ToggleSelect('font', '4', 'Comic Sans MS')" id="font4">Comic Sans MS</td></tr>
					<tr><td onclick="ToggleSelect('font', '5', 'Courier New')" id="font5">Courier New</td></tr>
					<tr><td onclick="ToggleSelect('font', '6', 'Impact')" id="font6">Impact</td></tr>
					<tr><td onclick="ToggleSelect('font', '7', 'Lucida Sans Unicode')" id="font7">Lucida Sans Unicode</td></tr>
					<tr><td onclick="ToggleSelect('font', '8', 'MS Sans Serif')" id="font8">MS Sans Serif</td></tr>
					<tr><td onclick="ToggleSelect('font', '9', 'Small Fonts')" id="font9">Small Fonts</td></tr>
					<tr><td onclick="ToggleSelect('font', '10', 'Tahoma')" id="font10">Tahoma</td></tr>
					<tr><td onclick="ToggleSelect('font', '11', 'Times New Roman')" id="font11">Times New Roman</td></tr>
					<tr><td onclick="ToggleSelect('font', '12', 'Trebuchet MS')" id="font12">Trebuchet MS</td></tr>
					<tr><td onclick="ToggleSelect('font', '13', 'Verdana')" id="font13">Verdana</td></tr>
				</table>
				</div>
			</td><td>
				<div style="width:60; height:92; overflow:auto; border:1px solid #D5D3DC; ">
				<table width="100%" cellpadding="1" cellspacing="0">
					<tr><td onclick="ToggleSelect('size', '1', '1')" id="size1">8pt</td></tr>
					<tr><td onclick="ToggleSelect('size', '2', '2')" id="size2">10pt</td></tr>
					<tr><td onclick="ToggleSelect('size', '3', '3')" id="size3">12pt</td></tr>
					<tr><td onclick="ToggleSelect('size', '4', '4')" id="size4">24pt</td></tr>
					<tr><td onclick="ToggleSelect('size', '5', '5')" id="size5">32pt</td></tr>
					<tr><td onclick="ToggleSelect('size', '6', '6')" id="size6">48pt</td></tr>
					<tr><td onclick="ToggleSelect('size', '7', '7')" id="size7">72pt</td></tr>
				</table>
				</div>
			</td></tr>
			<tr><td colspan="2" align="center">
				<table width="100%" cellpadding="0" cellspacing="0"><tr>
					<td align="center"><div style="width:180; height:50; overflow:hidden; border:1px solid #D5D3DC; " align="center"><table width="100%" height="100%" cellpadding="2" cellspacing="0"><tr><td id="example" align="center" nowrap>Selecione uma<br>fonte ou tamanho</td></tr></table></div></td>
					<td width="80" valign="bottom"><button onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button><br><img src="img/_blank.gif" width="1" height="2"><br><button onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button></td>
				</tr></table>
			</td></tr>
		</table>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act dialog_image
//-----------------------------------------------------------------------------
else if ($act == "dialog_image") {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Inserir Imagem - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var data = window.dialogArguments;

function init() {
	
}

var image_align = 'right';
function align_select (align) {
	image_align = align;
}

function Save() {
	window.returnValue = new Array();
	window.returnValue['url'] = document.all['image_url'].value;
	window.returnValue['align'] = image_align;
	window.close();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_image.jpg" width="400" height="70"><br>
		<table width="400" cellpadding="3" cellspacing="0">
			<tr><td width="100" align="right"><b>URL:</b></td><td width="300"><input type="text" id="image_url" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:294px; "></td></tr>
			<tr><td width="100" align="right"><b>Alinhamento:</b></td><td width="300"><select onchange="align_select(this.options[this.selectedIndex].value); " style="font:8pt verdana; width:294px; "><option value="left">À esquerda do texto<option value="right" selected>À direita do texto<option value="top">No topo do texto<option value="middle">No centro do texto<option value="bottom">Na base do texto</select></td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr><td colspan="2" align="center">
				<button onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button>
			</td></tr>
		</table>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act dialog_link
//-----------------------------------------------------------------------------
else if ($act == "dialog_link") {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Inserir Link - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var data = window.dialogArguments;

function init() {
	
}

var link_target = 'blank';
function target_check(target) {
	link_target = target;
}

function Save() {
	window.returnValue = new Array();
	window.returnValue['url'] = document.all['link_url'].value;
	window.returnValue['target'] = link_target;
	window.close();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_link.jpg" width="400" height="70"><br>
		<table width="400" cellpadding="3" cellspacing="0">
			<tr><td width="100" align="right"><b>URL:</b></td><td width="300"><input type="text" id="link_url" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:294px; "></td></tr>
			<tr><td width="100" align="right"><b>Abrir em:</b></td><td width="300"><input type="radio" name="link_target" onkeypress="if (event.keyCode == 13) {Save(); }" onclick="target_check('blank'); " id="link_target_blank" checked><label for="link_target_blank">nova janela</label>&nbsp;&nbsp;<input type="radio" name="link_target" onkeypress="if (event.keyCode == 13) {Save(); }" onclick="target_check('self'); " id="link_target_self"><label for="link_target_self">mesma janela</label></td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr><td colspan="2" align="center">
				<button onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button>
			</td></tr>
		</table>
		
	</body>
</html><?php }


//-----------------------------------------------------------------------------
// Act dialog_mail
//-----------------------------------------------------------------------------
else if ($act == "dialog_mail") {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Inserir Link - MZn² 2.0 ADV</title>
<style type="text/css">
<!--
a         {text-decoration:none; }
a:hover   {text-decoration:underline; }

body, table {font:8pt verdana; cursor:default; scrollbar-face-color:#E2E2E7; scrollbar-shadow-color:#E2E2E7; scrollbar-highlight-color:#FFFFFF; scrollbar-3dlight-color:#d3d2db; scrollbar-arrow-color:#494653; scrollbar-track-color:#EFEFEF; scrollbar-darkshadow-color:#9e9dac; }
-->
</style>
<script language="javascript" type="text/javascript">
<!-- ;

var data = window.dialogArguments;

function init() {
	
}

function Save() {
	window.returnValue = new Array();
	window.returnValue['mail'] = document.all['link_mail'].value;
	window.close();
}

document.onkeypress = new Function ("if (event.keyCode == 27) {window.close(); } ");

// -->
</script>
	</head>
	<body onload="init(); " onselectstart="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " oncontextmenu="var x = event.srcElement.tagName; if (x == 'INPUT' || x == 'TEXTAREA') {return true; } else {return false; } " style="border:0; " bgcolor="#FFFFFF" text="#000000" link="#000000" vlink="#000000" alink="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<img src="img/{skin}/popup_link.jpg" width="400" height="70"><br>
		<table width="400" cellpadding="3" cellspacing="0">
			<tr><td width="100" align="right"><b>E-mail:</b></td><td width="300"><input type="text" id="link_mail" onkeypress="if (event.keyCode == 13) {Save(); }" style="font:8pt verdana; width:294px; "></td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr><td colspan="2" align="center">
				<button onclick="Save(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; "><b>Ok</b></button>&nbsp;<button onclick="window.close(); " style="width:80; font:8pt verdana; border:0; background-color:#E2E2E7; cursor:hand; ">Cancelar</button>
			</td></tr>
		</table>
		
	</body>
</html><?php }



?>
