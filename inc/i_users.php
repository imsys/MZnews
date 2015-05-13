<?php $p['tit'] = "users"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("users");

function form_edit ($id, $val = array()) {
	global $s, $m, $l;
	$cats = array(); foreach ($s->cat as $k => $v) {$cats[$k] = $v['name']; } asort($cats);
	if ($id === "__NEW__") {
		$l->tb_group("Dados gerais do novo usuário");
				$l->tb_input("text", "c[". $s->quote_safe($id) ."][login]", "<b>Login</b>&nbsp;¹", "", 200);
				$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][name]", "<b>Nome</b>", "", 300);
	}
	else {
		$l->tb_group("Dados gerais de ". $val['user']);
				$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][name]", "<b>Nome</b>", $val['data']['name'], 505);
	}
		$l->tb_nextrow();
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][mail]", "<b>E-mail</b>", $val['data']['mail'], 380);
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][icq]", "ICQ", $val['data']['icq'], 120);
	if ($id === "__NEW__") {
		$l->tb_group("Senha de acesso");
				$l->tb_input("password", "c[". $s->quote_safe($id) ."][pwd1]", "<b>Senha</b>", "", 250);
				$l->tb_input("password", "c[". $s->quote_safe($id) ."][pwd2]", "<b>Confirme a senha</b>", "", 250);
	}
	$l->tb_group("Outros dados / Opções");
			$count = 0;
			if ($s->sys['cfield']['field1']) {$count++; } if ($s->sys['cfield']['field2']) {$count++; } if ($s->sys['cfield']['field3']) {$count++; }
			if ($count > 0) {
				$width = intval((505 / $count) - (($count-1) * 5));
				if ($s->sys['cfield']['field1']) {$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][field1]", $s->sys['cfield']['field1'], $val['data']['field1'], $width); }
				if ($s->sys['cfield']['field2']) {$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][field2]", $s->sys['cfield']['field2'], $val['data']['field2'], $width); }
				if ($s->sys['cfield']['field3']) {$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][field3]", $s->sys['cfield']['field3'], $val['data']['field3'], $width); }
			}
		if ($count > 0) {$l->tb_nextrow(); }
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][data][usequeue]", "", "1=Novos posts vão direto para a fila de moderação automaticamente", $val['data']['usequeue'], 505);
	if ($id !== "__NEW__") {
			$l->tb_group("Trocar a senha - preencha apenas se deseja alterá-la");
				$l->tb_input("password", "c[". $s->quote_safe($id) ."][pwd1]", "Nova senha", "", 250);
				$l->tb_input("password", "c[". $s->quote_safe($id) ."][pwd2]", "Confirme a nova senha", "", 250);
	}
	$l->tb_group("Envio de arquivos");
			if ($val['data']['upload_maxsize']) {$val['data']['upload_maxsize'] = $s->to_bytes($val['data']['upload_maxsize']); }
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][upload_maxsize]", "Tamanho máximo&nbsp;&nbsp;&nbsp;<span class=\"hint\">exemplo: 300 KB</span>", $val['data']['upload_maxsize'], 200);
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][data][upload_extensions]", "Extensões permitidas&nbsp;&nbsp;&nbsp;<span class=\"hint\">sem . e separadas por ,</span>", $val['data']['upload_extensions'], 300);
	if ($id === "__NEW__") {
		$l->tb_group("Permissões". $user);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][admin]", "<b>Administrador</b>", "1=Este usuário é um administrador e possui todas as permissões abaixo", $perms['admin'], 505);
		$l->tb_group("Permissões globais (em TODAS as categorias)");
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][all][post]", "", "1=Postar notícias", $perms['all']['post'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][all][editown]", "", "1=Editar as próprias notícias", $perms['all']['editown'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][all][editall]", "", "1=Editar qualquer notícia", $perms['all']['editall'], 165);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][all][comments]", "", "1=Gerenciar os comentários", $perms['all']['comments'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][all][cgdate]", "", "1=Alterar as datas das notícias", $perms['all']['cgdate'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][all][usehtml]", "", "1=Usar HTML nas notícias", $perms['all']['usehtml'], 165);
		foreach ($cats as $cid => $cname) {
			$l->tb_group("Permissões na categoria ". $cname);
					$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][". $cid ."][post]", "", "1=Postar notícias", $perms[$cid]['post'], 165);
					$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][". $cid ."][editown]", "", "1=Editar as próprias notícias", $perms[$cid]['editown'], 165);
					$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][". $cid ."][editall]", "", "1=Editar qualquer notícia", $perms[$cid]['editall'], 165);
				$l->tb_nextrow();
					$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][". $cid ."][comments]", "", "1=Gerenciar os comentários", $perms[$cid]['comments'], 165);
					$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][". $cid ."][cgdate]", "", "1=Alterar as datas das notícias", $perms[$cid]['cgdate'], 165);
					$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][". $cid ."][usehtml]", "", "1=Usar HTML nas notícias", $perms[$cid]['usehtml'], 165);
		}
		$l->tb_group("Permissões gerais");
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][categories]", "", "1=Gerenciar categorias / Alterar os modelos", $perms['general']['categories'], 335);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][config]", "", "1=Alterar configurações", $perms['general']['config'], 165);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][upload]", "", "1=Enviar arquivos", $perms['general']['upload'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][uplmng]", "", "1=Gerenciar arquivos enviados", $perms['general']['uplmng'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][smilies]", "", "1=Gerenciar smilies", $perms['general']['smilies'], 165);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][users]", "", "1=Gerenciar usuários", $perms['general']['users'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][backup]", "", "1=Usar o backup", $perms['general']['backup'], 165);
				$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][perms][general][editqueue]", "", "1=Gerenciar filas", $perms['general']['editqueue'], 165);
	}
}

$act = $s->req['act']; if (!$act) {$act = "index"; }


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	if (!$s->req['pg']) {$s->req['pg'] = 1; }
	
	$l->form("users", "edit");
	$l->list_header("sel:id", "190:Usuário / E-mail", "70:Posts:center", "100:Último login", "100:Último post", "70:Ativo?:center", "70:Admin?:center");
	$db = $s->db_table_open($s->cfg['file']['users']);
	foreach ($db['data'] as $k => $v) {
		$active = "Não"; if ($v['data']['active']) {$active = "Sim"; }
		$admin = "Não"; if ($v['perms']['admin']) {$admin = "Sim"; }
		$format = "|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $v['id'] ."]=1\">%s</a>";
		$lastLogin = "0|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $v['id'] ."]=1\">Nunca</a>"; if ($v['data']['lastlogin']) {$lastLogin = $v['data']['lastlogin'] ."|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $v['id'] ."]=1\">". $m->parse_date($v['data']['lastlogin']) ."</a>"; }
		$lastPost = "0|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $v['id'] ."]=1\">Nunca</a>"; if ($v['data']['lastpost']) {$lastPost = $v['data']['lastpost'] ."|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $v['id'] ."]=1\">". $m->parse_date($v['data']['lastpost']) ."</a>"; }
		$l->list_item($v['id'], $v['data']['name'] ."|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $v['id'] ."]=1\"><b>%s</b><br />". $v['data']['mail'] ."</a>", $v['data']['posts'] . $format, $lastLogin, $lastPost, $active . $format, $admin . $format, array("colclass" => "small"));
	}
	if (!$s->req['sort']) {$s->req['sort'] = "1:0"; }
	
	$l->page_link = "index.php?s={session}&amp;sec=users&amp;sort={sort}&amp;pg={pg}&amp;query={query}";
	$l->list_pg = $s->req['pg'];
	$l->list_perpage = $s->sys['edit']['perpage'];
	$l->list_sort = $s->req['sort'];
	$l->list_query = $s->req['query'];
	
	$l->list_sort(); $l->list_filter();
	
	$l->list_build($s->skin['dir'] ."/order_asc.gif", $s->skin['dir'] ."/order_desc.gif");
	$l->list_footer(array("<b>Adicionar</b>" => "index.php?s={session}&amp;sec=users&amp;act=new"), 1, array("activate" => "ativar", "disactivate" => "desativar", "edit" => "editar", "perms" => "alterar pemissões dos", "remove" => "remover"), 1, 616);
	$l->form_end();
}


//-----------------------------------------------------------------------------
// Act new
//-----------------------------------------------------------------------------
else if ($act == "new") {
	$l->form("users", "new_save"); $l->table(505);
	form_edit("__NEW__");
	$l->tb_caption("Preencha todos os campos em <b>negrito</b><br />¹: Somente letras <b>minúsculas</b>, números e _");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=users"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act new_save
//-----------------------------------------------------------------------------
else if ($act == "new_save") {
	foreach ($s->req['c'] as $id => $req) {
		$m->req('c|'.$id.'|login', 'c|'.$id.'|data|name', 'c|'.$id.'|data|mail', 'c|'.$id.'|pwd1', 'c|'.$id.'|pwd2');
	}
	
	if (!$s->cfg['ver']['demo']) {
		$s->req['c']['__NEW__']['data']['upload_maxsize'] = $s->bytes_to_numbers($s->req['c']['__NEW__']['data']['upload_maxsize']);
		$s->req['c']['__NEW__']['data']['upload_extensions'] = str_replace(".", "", $s->req['c']['__NEW__']['data']['upload_extensions']);
		$s->req['c']['__NEW__']['data']['upload_extensions'] = preg_replace("/,\s+/", ",", $s->req['c']['__NEW__']['data']['upload_extensions']); $s->req['c']['__NEW__']['data']['upload_extensions'] = preg_replace("/\s+/", ",", $s->req['c']['__NEW__']['data']['upload_extensions']); $s->req['c']['__NEW__']['data']['upload_extensions'] = preg_replace("/^,(.*),$/", "\\1", $s->req['c']['__NEW__']['data']['upload_extensions']);
		
		$login = $s->req['c']['__NEW__']['login'];
		if ($s->users[$login]) {$m->error_redir("login_inuse"); }
	 	if ($login == "__NEW__" || $login == "general" || $login == "all") {$m->error_redir("login_system"); }
		if (!preg_match("/^[0-9a-z_]+$/", $login)) {$m->error_redir("login_invalid"); }
		if ($s->req['c']['__NEW__']['pwd1'] != $s->req['c']['__NEW__']['pwd2']) {$m->error_redir("passmismatch"); }
		
		$nl = array();
		$nl['id'] = substr(md5(time()*rand()), 0, 10);
		$nl['user'] = $s->req['c']['__NEW__']['login'];
		$nl['pwd'] = md5($s->req['c']['__NEW__']['pwd1']);
		$nl['data'] = $s->req['c']['__NEW__']['data'];
		$nl['data']['posts'] = 0; $nl['data']['active'] = 1;
		foreach ($s->req['c']['__NEW__']['perms'] as $perm => $active) {
			if ($perm == "admin") {$nl['perms']['admin'] = 1; break; }
			else if (is_array($active)) {
				$pl = "";
				foreach ($active as $pk => $pv) {
					if ($pl) {$pl .= ","; } $pl .= $pk;
				}
				$nl['perms'][$perm] = $pl;
			}
		}
		
		$db = $s->db_table_open($s->cfg['file']['users'], 1);
		$db['data'][] = $nl;
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	$m->location("sec=users", "Usuário adicionado");
}


//-----------------------------------------------------------------------------
// Act edit
//-----------------------------------------------------------------------------
else if ($act == "edit") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$l->form("users", "edit_save"); $l->table(505);
	$db = $s->db_table_open($s->cfg['file']['users']);
	foreach ($db['data'] as $k => $v) {
		if (!$s->req['sel'][$v['id']]) {continue; }
		if ($v['user'] == $s->usr['user']) {$m->error_redir("login_own"); }
		form_edit($k, $v);
		$l->tb_separator(30);
	}
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=users"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act edit_save
//-----------------------------------------------------------------------------
else if ($act == "edit_save") {
	foreach ($s->req['c'] as $id => $req) {
		$m->req('c|'.$id.'|data|name', 'c|'.$id.'|data|mail');
	}
	
	if (!$s->cfg['ver']['demo']) {
		$nl = array();
		foreach ($s->req['c'] as $id => $req) {
			if ($id === $s->usr['k']) {$m->error_redir("login_own"); }
			if ($s->req['c'][$id]['pwd1'] || $s->req['c'][$id]['pwd2']) {
				$m->req('c|'.$id.'|pwd1', 'c|'.$id.'|pwd2');
				if ($s->req['c'][$id]['pwd1'] != $s->req['c'][$id]['pwd2']) {$m->error_redir("passmismatch"); }
			}
			$s->req['c'][$id]['data']['upload_maxsize'] = $s->bytes_to_numbers($s->req['c'][$id]['data']['upload_maxsize']);
			$s->req['c'][$id]['data']['upload_extensions'] = str_replace(".", "", $s->req['c'][$id]['data']['upload_extensions']);
			$s->req['c'][$id]['data']['upload_extensions'] = preg_replace("/,\s+/", ",", $s->req['c'][$id]['data']['upload_extensions']); $s->req['c'][$id]['data']['upload_extensions'] = preg_replace("/\s+/", ",", $s->req['c'][$id]['data']['upload_extensions']); $s->req['c'][$id]['data']['upload_extensions'] = preg_replace("/^,(.*),$/", "\\1", $s->req['c'][$id]['data']['upload_extensions']);
			
			if (!$s->req['c'][$id]['data']['usequeue']) {$s->req['c'][$id]['data']['usequeue'] = 0; }
		
			$nl[$id] = array();
			if ($s->req['c'][$id]['pwd1']) {$nl[$id]['pwd'] = md5($s->req['c'][$id]['pwd1']); }
			$nl[$id]['data'] = $s->req['c'][$id]['data'];
		}
		
		$db = $s->db_table_open($s->cfg['file']['users']);
		$db['data'] = $m->array_sync($db['data'], $nl);
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	$m->location("sec=users", "Alterações salvas");
}


//-----------------------------------------------------------------------------
// Act perms
//-----------------------------------------------------------------------------
else if ($act == "perms") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	if ($s->req['sel'][$s->usr['data']['id']] == 1) {$m->error_redir("login_own"); }
	
	$cats = array(); foreach ($s->cat as $k => $v) {$cats[$k] = $v['name']; } asort($cats);
	$selected = array(); foreach ($s->req['sel'] as $k => $v) {$selected['sel['. $k .']'] = $v; }
	
	$user = ""; $perms = array();
	$db = $s->db_table_open($s->cfg['file']['users']);
	foreach ($db['data'] as $v) {
		if ($s->req['sel'][$v['id']] && $v['user'] == $s->usr['user']) {$m->error_redir("perms_own"); }
		if ($s->req['sel'][$v['id']] && count($perms) == 0) {
			$user = $v['data']['name'];
			$perms = $v['perms'];
			if (!$v['perms']['admin'] && is_array($v['perms'])) {
				$perms = $v['perms'];
				foreach ($perms as $pk => $pv) {
					$pv = explode(",", $pv); $pn = array();
					foreach ($pv as $perm) {$pn[$perm] = 1; }
					$perms[$pk] = $pn;
				}
			}
		}
	}
	
	$l->form("users", "perms_save", $selected); $l->table(505);
	$l->tb_group("Baseadas no usuário ". $user);
			$l->tb_check("checkbox", "c[admin]", "<b>Administrador</b>", "1=Este usuário é um administrador e possui todas as permissões abaixo", $perms['admin'], 505);
	$l->tb_group("Permissões globais (em TODAS as categorias)");
			$l->tb_check("checkbox", "c[all][post]", "", "1=Postar notícias", $perms['all']['post'], 165);
			$l->tb_check("checkbox", "c[all][editown]", "", "1=Editar as próprias notícias", $perms['all']['editown'], 165);
			$l->tb_check("checkbox", "c[all][editall]", "", "1=Editar qualquer notícia", $perms['all']['editall'], 165);
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c[all][comments]", "", "1=Gerenciar os comentários", $perms['all']['comments'], 165);
			$l->tb_check("checkbox", "c[all][cgdate]", "", "1=Alterar as datas das notícias", $perms['all']['cgdate'], 165);
			$l->tb_check("checkbox", "c[all][usehtml]", "", "1=Usar HTML nas notícias", $perms['all']['usehtml'], 165);
	foreach ($cats as $id => $name) {
		$l->tb_group("Permissões na categoria ". $name);
				$l->tb_check("checkbox", "c[". $id ."][post]", "", "1=Postar notícias", $perms[$id]['post'], 165);
				$l->tb_check("checkbox", "c[". $id ."][editown]", "", "1=Editar as próprias notícias", $perms[$id]['editown'], 165);
				$l->tb_check("checkbox", "c[". $id ."][editall]", "", "1=Editar qualquer notícia", $perms[$id]['editall'], 165);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[". $id ."][comments]", "", "1=Gerenciar os comentários", $perms[$id]['comments'], 165);
				$l->tb_check("checkbox", "c[". $id ."][cgdate]", "", "1=Alterar as datas das notícias", $perms[$id]['cgdate'], 165);
				$l->tb_check("checkbox", "c[". $id ."][usehtml]", "", "1=Usar HTML nas notícias", $perms[$id]['usehtml'], 165);
	}
	$l->tb_group("Permissões gerais");
			$l->tb_check("checkbox", "c[general][categories]", "", "1=Gerenciar categorias / Alterar os modelos", $perms['general']['categories'], 335);
			$l->tb_check("checkbox", "c[general][config]", "", "1=Alterar configurações", $perms['general']['config'], 165);
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c[general][upload]", "", "1=Enviar arquivos", $perms['general']['upload'], 165);
			$l->tb_check("checkbox", "c[general][uplmng]", "", "1=Gerenciar arquivos enviados", $perms['general']['uplmng'], 165);
			$l->tb_check("checkbox", "c[general][smilies]", "", "1=Gerenciar smilies", $perms['general']['smilies'], 165);
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c[general][users]", "", "1=Gerenciar usuários", $perms['general']['users'], 165);
			$l->tb_check("checkbox", "c[general][backup]", "", "1=Usar o backup", $perms['general']['backup'], 165);
			$l->tb_check("checkbox", "c[general][editqueue]", "", "1=Gerenciar filas", $perms['general']['editqueue'], 165);
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=users"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act perms_save
//-----------------------------------------------------------------------------
else if ($act == "perms_save") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	if ($s->req['sel'][$s->usr['data']['id']] == 1) {$m->error_redir("login_own"); }
	
	if (!$s->cfg['ver']['demo']) {
		$perms = array();
		foreach ($s->req['c'] as $perm => $active) {
			if ($perm == "admin") {$perms['admin'] = 1; break; }
			else if (is_array($active)) {
				$pl = "";
				foreach ($active as $pk => $pv) {
					if ($pl) {$pl .= ","; } $pl .= $pk;
				}
				$perms[$perm] = $pl;
			}
		}
		
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if (!$s->req['sel'][$v['id']]) {continue; }
			$db['data'][$k]['perms'] = $perms;
		}
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	
	$m->location("sec=users", "Permissões alteradas");
}


//-----------------------------------------------------------------------------
// Act activate | disactivate
//-----------------------------------------------------------------------------
else if ($act == "activate" || $act == "disactivate") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	if ($s->req['sel'][$s->usr['data']['id']] == 1) {$m->error_redir("login_own"); }
	$count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if (!$s->req['sel'][$v['id']]) {continue; }
			$count++;
			if ($act == "activate") {$db['data'][$k]['data']['active'] = 1; } else {$db['data'][$k]['data']['active'] = 0; }
		}
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	
	$word = "ativado"; if ($act == "disactivate") {$word = "des". $word; }
	$msg = "Usuário ". $word; if ($count > 1) {$msg = $count ." usuários ". $word ."s"; }
	$m->location("sec=users", $msg);
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$rn = array(); $ru = array(); $rcm = array();
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if (!$s->req['sel'][$v['id']]) {continue; }
			if ($v['user'] == $s->usr['user']) {$m->error_redir("login_own"); }
			$ru[$v['user']] = 1; unset($db['data'][$k]);
		}
		$s->db_table_save($s->cfg['file']['users'], $db);
	
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($db['data'] as $k => $v) {
			if ($ru[$v['user']]) {$rn[$v['id']] = 1; unset($db['data'][$k]); }
		}
		$s->db_table_save($s->cfg['file']['news'], $db);
	
		$db = $s->db_table_open($s->cfg['file']['comments']);
		foreach ($db['data'] as $k => $v) {
			if ($rn[$v['nid']] == 1) {$rcm[$v['id']] = 1; unset($db['data'][$k]); }
		}
		$s->db_table_save($s->cfg['file']['comments'], $db);
	}
	
	$msg = "Usuário removido"; if (count($ru) > 1) {$msg = count($ru) ." usuários removidos"; }
	$m->location("sec=users", $msg);
}


?>
