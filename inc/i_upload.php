<?php $p['tit'] = "upload"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("upload|uplmng");

$act = $s->req['act']; if (!$act) {$act = "index"; }


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	if (!$s->req['pg']) {$s->req['pg'] = 1; }
	
	echo "<span class=\"important\"><b>Atenção</b><br />É possível que o seu host não aceite o envio de arquivos<br />por PHP. Neste caso este sistema não funcionará!</span><br /><br />";
	$l->form("upload", "remove");
	$l->list_header("sel:id", "300:Nome do arquivo", "100:Tamanho", "100:Enviado por", "100:Enviado em");
	$db = $s->db_table_open($s->cfg['file']['uploads']);
	foreach ($db['data'] as $k => $v) {
		$dis = 0;
		if (!$m->perms("uplmng") && $v['user'] != $s->usr['user']) {$dis = 1; }
		
		$l->list_item($k, $v['name'] ."|<a href=\"". $s->cfg['dir']['data'] ."/". $v['name'] ."\">%s</a>", $s->to_bytes($v['size']), $s->users[$v['user']]['name'], $m->parse_date($v['time']), array("disabled" => $dis));
	}
	if (!$s->req['sort']) {$s->req['sort'] = "1:0"; }
	
	$l->page_link = "index.php?s={session}&amp;sec=upload&amp;sort={sort}&amp;pg={pg}&amp;query={query}";
	$l->list_pg = $s->req['pg'];
	$l->list_perpage = $s->sys['edit']['perpage'];
	$l->list_sort = $s->req['sort'];
	$l->list_query = $s->req['query'];
	
	$l->list_sort(); $l->list_filter();
	
	$l->list_build($s->skin['dir'] ."/order_asc.gif", $s->skin['dir'] ."/order_desc.gif");
	$actions = array(); if ($m->perms("upload")) {$actions = array("<b>Enviar</b>" => "index.php?s={session}&amp;sec=upload&amp;act=new"); }
	$l->list_footer($actions, 1, array("remove" => "remover"), 1, 610);
	$l->form_end();
}


//-----------------------------------------------------------------------------
// Act new
//-----------------------------------------------------------------------------
else if ($act == "new") {
	$m->req_perms("upload");
	
	$maxSize = $s->sys['upload']['maxsize']; if ($s->usr['data']['upload_maxsize']) {$maxSize = $s->usr['data']['upload_maxsize']; }
	$maxSizeStr = $s->to_bytes($maxSize);
	if ($s->sys['upload']['extensions'] == "*" || $s->usr['data']['upload_extensions'] == "*") {$extensions = "todas"; } else {$extensions = $s->sys['upload']['extensions']; if ($s->usr['data']['upload_extensions']) {$extensions .= ",". $s->usr['data']['upload_extensions']; } $extensions = str_replace(",", ", ", $extensions); }
	
	echo "<span class=\"important\"><b>Atenção</b><br />É possível que o seu host não aceite o envio de arquivos<br />por PHP. Neste caso este sistema não funcionará!</span><br /><br />";
	$l->form("upload", "new_save", array("MAX_FILE_SIZE" => $maxSize), "post", "formCenter", "enctype=\"multipart/form-data\""); $l->table(505);
			$l->tb_input("file", "file1", "<b>Arquivo 1</b>", "", 505);
		$l->tb_nextrow();
			$l->tb_input("file", "file2", "Arquivo 2", "", 505);
		$l->tb_nextrow();
			$l->tb_input("file", "file3", "Arquivo 3", "", 505);
		$l->tb_nextrow();
			$l->tb_input("file", "file4", "Arquivo 4", "", 505);
		$l->tb_nextrow();
			$l->tb_input("file", "file5", "Arquivo 5", "", 505);
		$l->tb_nextrow();
			$l->tb_input("file", "file6", "Arquivo 6", "", 505);
	$l->tb_caption("Envie pelo menos um arquivo");
	$l->tb_button("submit", "Enviar", array("accesskey" => "e"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=upload"));
		$l->tb_nextrow();
	$l->tb_custom("<br /><b>Extensões permitidas:</b> ". $extensions ."<br /><b>Tamanho máximo:</b> ". $maxSizeStr, 505);
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act new_save
//-----------------------------------------------------------------------------
else if ($act == "new_save") {
	$m->req_perms("upload");
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['uploads']); $exist = array();
		foreach ($db as $k => $v) {$exist[$v['name']] = 1; }
		
		$maxSize = $s->sys['upload']['maxsize']; if ($s->usr['data']['upload_maxsize']) {$maxSize = $s->usr['data']['upload_maxsize']; }
		$validExt = "*"; if ($s->sys['upload']['extensions'] != "*" && $s->usr['data']['upload_extensions'] != "*") {$validExt = ",". $s->sys['upload']['extensions'] .","; if ($s->usr['data']['upload_extensions']) {$validExt .= $s->usr['data']['upload_extensions'] .","; } }
		$protected = array();
		foreach ($s->cfg['file'] as $k => $v) {$protected[basename($v)] = 1; }
		$protected += $s->cfg['protected'];
		
		$nofile = 0;
		for ($i = 1; $i <= 6; $i++) {
			$f_tmpname = $HTTP_POST_FILES['file'. $i]['tmp_name']; $f_name = $HTTP_POST_FILES['file'. $i]['name']; $f_size = $HTTP_POST_FILES['file'. $i]['size']; $f_error = $HTTP_POST_FILES['file'. $i]['error'];
			
			// Verificação 1: erros internos
			if ($f_error == 1) {$m->error_redir("upload_toobigsys", "file=". $i); }
			else if ($f_error == 2 || $f_size > $maxSize) {$m->error_redir("upload_toobig", "file=". $i); }
			else if ($f_error == 3) {$m->error_redir("upload_broken", "file=". $i); }
			else if ($f_error == 4) {$nofile += 1; }
			else if ($f_tmpname) {
				// Verificação 2: extensão
				$ext = pathinfo($f_name); $ext = $ext['extension'];
				if ($validExt != "*" && strpos($validExt, ",". $ext .",") === FALSE) {$m->error_redir("upload_extinvalid", "file=". $i); }
				
				// Verificação 3: arquivo existe -> é do dono ou pode alterar todos?
				if ($exist[$f_name] == 1) {$m->error_redir("upload_noover", "file=". $i); }
				
				// Verificação 4: arquivo não protegido?
				$f_name_strip = preg_replace("/\.[^\.]+$/", "", $f_name);
				if ($protected[$f_name] || $protected[$f_name_strip .".*"]) {$m->error_redir("upload_protected", "file=". $i); }
			}
			else {$nofile += 1; }
		}
		if ($nofile == 6) {$m->error_redir("upload_noupload"); }
		
		$error = 0; $nl = array();
		if (!$s->cfg['ver']['demo']) { for ($i = 1; $i <= 6; $i++) {
			$f_tmpname = $HTTP_POST_FILES['file'. $i]['tmp_name']; $f_name = $HTTP_POST_FILES['file'. $i]['name']; $f_size = $HTTP_POST_FILES['file'. $i]['size']; $f_error = $HTTP_POST_FILES['file'. $i]['error'];
			if (!$f_error && $f_tmpname) {
				$move = @move_uploaded_file($f_tmpname, $s->cfg['path']['data'] ."/". $f_name);
				if ($move) {
					$x = count($nl) + count($db['data']);
					$nl[$x] = array();
					$nl[$x]['id'] = substr(md5(time()*rand()), 0, 10);
					$nl[$x]['name'] = $f_name;
					$nl[$x]['size'] = $f_size;
					$nl[$x]['time'] = $s->cfg['time'];
					$nl[$x]['user'] = $s->usr['user'];
				}
				else {$error += 1; }
			}
		} }
		$db['data'] += $nl;
		$s->db_table_save($s->cfg['file']['uploads'], $db);
	}
	
	$m->location("sec=upload", "Envio realizado com sucesso");
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['uploads']);
		foreach ($s->req['sel'] as $k => $v) {
			if (!$m->perms("uplmng") && $db['data'][$k]['user'] != $s->usr['user']) {continue; }
			$remove = @unlink($s->cfg['path']['data'] ."/". $db['data'][$k]['name']);
			if ($remove) {unset($db['data'][$k]); $count += 1; }
		}
		if ($count < 1) {$m->error_redir("nosel"); }
		$s->db_table_save($s->cfg['file']['uploads'], $db);
	}
	
	$msg = "Arquivo removido"; if ($count > 1) {$msg = $count ." arquivos removidos"; }
	$m->location("sec=upload", $msg);
}



?>
