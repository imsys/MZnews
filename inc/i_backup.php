<?php $p['tit'] = "backup"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("backup");

$act = $s->req['act']; if (!$act) {$act = "index"; }


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	if (!$s->req['pg']) {$s->req['pg'] = 1; }
	$exist = @file_exists($s->cfg['path']['data'] ."/backup.tar");
	
	echo "<div class=\"important\" style=\"width:505px; \"><b>Atenção</b><br />É possível que o seu host não aceite o envio de arquivos por PHP. Neste caso você não poderá enviar o backup de seu computador manualmente, use seu FTP e envie para a pasta ". $s->cfg['dir']['data'] .". O sistema não faz backup dos arquivos enviados!</div><br />";
	
	$l->table(505);
	$l->tb_group("Manutenção do backup");
		$l->tb_nextrow();
			$l->tb_custom("Aqui você pode criar, baixar ou remover o seu backup. É altamente recomendável que você remova o backup assim que baixá-lo, impedindo que outros usuários tenham acesso aos seus arquivos.", 505, "center");
		$l->tb_nextrow();
			$links = "<b><a href=\"index.php?s={session}&amp;sec=backup&amp;act=create\">Criar</a>";
			if ($exist) {$links .= " &middot; <a href=\"". $s->cfg['dir']['data'] ."/backup.tar\">Baixar</a> &middot; <a href=\"index.php?s={session}&amp;sec=backup&amp;act=remove\">Remover</a>"; }
			$links .= "</b>";
			$l->tb_custom($links, 505, "center");
	$l->table_end();
	
	$checked = ($exist)? 'local' : 'file';
	$l->form("backup", "restore", array(), "post", "formCenter", "enctype=\"multipart/form-data\""); $l->table(505);
	$l->tb_group("Restaurar um backup");
		$l->tb_nextrow();
			$l->tb_custom("Aqui você pode restaurar um backup antigo, estando ele na pasta <b>data</b> ou no seu computador.", 505, "center");
		if ($checked == "local") {
			$l->tb_nextrow();
				$l->tb_check("radio", "use", "", "local=  Usar o arquivo <b>". $s->cfg['dir']['data'] ."/backup.tar</b>", $checked, 505);
		}
		$l->tb_nextrow();
			$l->tb_check("radio", "use", "", "file=", $checked, 15);
			$l->tb_input("file", "file", "", "", 485, array("onfocus" => "document.getElementById('form_use_file').click(); "));
		$l->tb_nextrow();
	$l->tb_button("submit", "Restaurar", array("accesskey" => "r"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act create
//-----------------------------------------------------------------------------
else if ($act == "create") {
	if (!$s->cfg['ver']['demo']) {
		if (@file_exists($s->cfg['path']['data'] ."/backup.tar")) {@unlink($s->cfg['path']['data'] ."/backup.tar"); }
		require_once $s->cfg['path']['inc'] ."/l_tar.php";
		$tar = new tar();
		$tar->new_tar($s->cfg['path']['data'], "backup.tar");
		$list = array(); foreach ($s->cfg['file'] as $file) {$list[] = basename($file); }
		$tar->add_files($list, $s->cfg['path']['data']);
		$tar->write_tar();
	}
	$m->location("sec=backup", "Backup criado");
}


//-----------------------------------------------------------------------------
// Act restore
//-----------------------------------------------------------------------------
else if ($act == "restore") {
	if (!$s->cfg['ver']['demo']) {
		require_once $s->cfg['path']['inc'] ."/l_tar.php";
		$tar = new tar();
		
		if ($s->req['use'] == "local") {if (!@file_exists($s->cfg['path']['data'] ."/backup.tar")) {$m->error_redir("backup_notar"); } }
		else {
			if (@file_exists($s->cfg['path']['data'] ."/backup.tar")) {@unlink($s->cfg['path']['data'] ."/backup.tar"); }
			if (!$HTTP_POST_FILES['file']['tmp_name']) {$m->error_redir("backup_upload_notmp"); }
			if (!preg_match("/\.tar$/", $HTTP_POST_FILES['file']['name'])) {@unlink($HTTP_POST_FILES['file']['tmp_name']); $m->error_redir("backup_upload_invalid"); }
			@move_uploaded_file($HTTP_POST_FILES['file']['tmp_name'], $s->cfg['path']['data'] ."/backup.tar");
		}
	
		$tar->new_tar($s->cfg['path']['data'], "backup.tar");
		$list1 = $tar->list_files();
		$list2 = array(); foreach ($s->cfg['file'] as $file) {$list2[] = basename($file); }
		
		if ($list1 !== $list2) {@unlink($s->cfg['path']['data'] ."/backup.tar"); $m->error_redir("backup_corrupt"); }
		
		$tar->extract_files( $s->cfg['path']['data'] );
	}
	
	$s->cfg['block:config'] = 1; $m->location("sec=backup", "Backup restaurado");
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	if (!$s->cfg['ver']['demo']) {
		if (@file_exists($s->cfg['path']['data'] ."/backup.tar")) {@unlink($s->cfg['path']['data'] ."/backup.tar"); }
	}
	$m->location("sec=backup", "Backup removido");
}



?>
