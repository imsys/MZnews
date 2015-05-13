<?php $p['tit'] = "users"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login();

$act = $s->req['act']; if (!$act) {$act = "index"; }


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	$l->form("profile", "save"); $l->table(505);
	$val = $s->usr['data'];
	$l->tb_group("Seus dados pessoais");
			$l->tb_input("text", "c[name]", "<b>Nome</b>", $val['name'], 505);
		$l->tb_nextrow();
			$l->tb_input("text", "c[mail]", "<b>E-mail</b>", $val['mail'], 380);
			$l->tb_input("text", "c[icq]", "ICQ", $val['icq'], 120);
	$l->tb_group("Outros dados / Opções");
			$count = 0;
			if ($s->sys['cfield']['field1']) {$count++; } if ($s->sys['cfield']['field2']) {$count++; } if ($s->sys['cfield']['field3']) {$count++; }
			if ($count > 0) {
				$width = intval((505 / $count) - (($count-1) * 5));
				if ($s->sys['cfield']['field1']) {$l->tb_input("text", "c[field1]", $s->sys['cfield']['field1'], $val['field1'], $width); }
				if ($s->sys['cfield']['field2']) {$l->tb_input("text", "c[field2]", $s->sys['cfield']['field2'], $val['field2'], $width); }
				if ($s->sys['cfield']['field3']) {$l->tb_input("text", "c[field3]", $s->sys['cfield']['field3'], $val['field3'], $width); }
			}
		if ($count > 0 && ($m->perms("admin") || $s->sys['edv'])) {$l->tb_nextrow(); }
			$width = ($m->perms("admin") && $s->sys['edv'])? 250 : 505;
			if ($m->perms("admin")) {$l->tb_check("checkbox", "c[usequeue]", "", "1=Seus posts vão para a fila de moderação", $val['usequeue'], $width); }
			if ($s->sys['edv']) {$l->tb_check("checkbox", "c[noedv]", "", "1=Não usar editor visual", $val['noedv'], $width); }
	$l->tb_group("Trocar a senha - preencha apenas se deseja alterá-la");
			$l->tb_input("password", "c[pwd1]", "Nova senha", "", 250);
			$l->tb_input("password", "c[pwd2]", "Confirme a nova senha", "", 250);
	if ($m->perms("admin")) {
		$l->tb_group("Envio de arquivos");
				if ($val['upload_maxsize']) {$val['upload_maxsize'] = $s->to_bytes($val['upload_maxsize']); }
				$l->tb_input("text", "c[upload_maxsize]", "Tamanho máximo&nbsp;&nbsp;&nbsp;<span class=\"hint\">exemplo: 300 KB</span>", $val['upload_maxsize'], 200);
				$l->tb_input("text", "c[upload_extensions]", "Outras extensões permitidas&nbsp;&nbsp;&nbsp;<span class=\"hint\">sem . e separadas por ,</span>", $val['upload_extensions'], 300);
	}
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => ""));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act save
//-----------------------------------------------------------------------------
else if ($act == "save") {
	foreach ($s->req['c'] as $id => $req) {
		$m->req('c|name', 'c|mail');
	}
	
	if (!$s->cfg['ver']['demo']) {
		$nl = array();
		if ($s->req['c']['pwd1'] || $s->req['c']['pwd2']) {
			$m->req('c|pwd1', 'c|pwd2');
			if ($s->req['c']['pwd1'] != $s->req['c']['pwd2']) {$m->error_redir("passmismatch"); }
			$s->usr['auth'] = md5("WsSys LOGIN ". md5($s->req['c']['pwd1']));
		}
		$s->req['c']['upload_maxsize'] = $s->bytes_to_numbers($s->req['c']['upload_maxsize']);
		$s->req['c']['upload_extensions'] = str_replace(".", "", $s->req['c']['upload_extensions']);
		$s->req['c']['upload_extensions'] = preg_replace("/,\s+/", ",", $s->req['c']['upload_extensions']); $s->req['c']['upload_extensions'] = preg_replace("/\s+/", ",", $s->req['c']['upload_extensions']); $s->req['c']['upload_extensions'] = preg_replace("/^,(.*),$/", "\\1", $s->req['c']['upload_extensions']);
		
		if (!$s->req['c']['usequeue']) {$s->req['c']['usequeue'] = 0; }
		if (!$s->req['c']['noedv']) {$s->req['c']['noedv'] = 0; }
		
		$nl = array();
		if ($s->req['c']['pwd1']) {$nl['pwd'] = md5($s->req['c']['pwd1']); }
		unset($s->req['c']['pwd1']); unset($s->req['c']['pwd2']);
		if (!$m->perms("admin")) {unset($s->req['c']['usequeue']); unset($s->req['c']['upload_maxsize']); unset($s->req['c']['upload_extensions']); }
		$nl['data'] = $s->req['c'];
		
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if ($v['user'] == $s->usr['user']) {$db['data'][$k] = $m->array_sync($db['data'][$k], $nl); }
		}
		
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	$m->location("sec=profile", "Perfil alterado");
}


?>
