<?php $p['tit'] = "categories"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("categories");

function form_edit ($id, $val = array()) {
	global $s, $m, $l;
	$list = ""; foreach ($s->cat as $k => $v) {if ($k != $id && !$v['templates']['usefrom']) {$list .= "|". $s->quote_safe($k) ."=Usar de ". $s->quote_safe($v['name']); } }
	if ($id == "__NEW__") {
		$val['headlines']['cut'] = 0;
		$val['headlines']['limit'] = 0;
		$val['news']['cut'] = 0;
		$val['news']['limit'] = 0;
		$val['fullnews'] = 1;
		$val['comments']['active'] = 1;
		$val['comments']['req_mail'] = 1;
		$val['comments']['req_title'] = 1;
		$val['comments']['smilies'] = 1;
		$val['comments']['limit_title'] = 0;
		$val['comments']['limit_comment'] = 0;
		$l->tb_group("Dados gerais da nova categoria");
				$l->tb_input("text", "c[". $s->quote_safe($id) ."][id]", "<b>Identificação</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">apenas letras e números, somente em letras minúsculas</span>", "", 505);
			$l->tb_nextrow();
				$l->tb_input("text", "c[". $s->quote_safe($id) ."][name]", "<b>Nome da categoria</b>", "", 505);
	}
	else {
		$l->tb_group("Dados gerais de ". $id);
				$l->tb_input("text", "c[". $s->quote_safe($id) ."][name]", "<b>Nome da categoria</b>", $val['name'], 505);
	}
	$l->tb_group("Manchetes (headlines)");
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][headlines][cut]", "<b>Cortar título depois de</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 não corta o título</span>", $val['headlines']['cut'], 505, array("style" => "width:80px; ", "_after" => "&nbsp;caracteres"));
	$l->tb_group("Notícias");
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][news][cut]", "<b>Cortar título depois de</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 não corta o título</span>", $val['news']['cut'], 250, array("style" => "width:80px; ", "_after" => "&nbsp;caracteres"));
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][news][limit]", "<b>Limitar o título em</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 não limita o título</span>", $val['news']['limit'], 250, array("style" => "width:80px; ", "_after" => "&nbsp;caracteres"));
		$l->tb_nextrow();
			$l->tb_select("c[". $s->quote_safe($id) ."][news][default_align]", "Alinhamento padrão do texto", "left=Esquerda|center=Centralizado|right=Direita|justify=Justificado", $val['news']['default_align'], 505);
	$l->tb_group("Comentários");
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][comments][active]", "", "1=Ativar comentários", $val['comments']['active'], 165);
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][comments][req_mail]", "", "1=Exigir e-mail", $val['comments']['req_mail'], 165);
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][comments][req_title]", "", "1=Exigir título", $val['comments']['req_title'], 165);
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][comments][mzncode]", "<b>mznCode</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">antigo hCode</span>", "1=Ativar substituições", $val['comments']['mzncode'], 165);
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][comments][smilies]", "<b>Smilies</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">:-)</span>", "1=Ativar substituições", $val['comments']['smilies'], 165);
			$l->tb_check("checkbox", "c[". $s->quote_safe($id) ."][comments][queue]", "<b>Fila</b>", "1=Colocar na fila", $val['comments']['queue'], 165);
		$l->tb_nextrow();
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][comments][limit_title]", "<b>Limitar o título em</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 não limita o título</span>", $val['comments']['limit_title'], 250, array("style" => "width:80px; ", "_after" => "&nbsp;caracteres"));
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][comments][limit_comment]", "<b>Limitar o conteúdo em</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 não limita o conteúdo</span>", $val['comments']['limit_comment'], 250, array("style" => "width:80px; ", "_after" => "&nbsp;caracteres"));
		$l->tb_nextrow();
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][comments][default_expire]", "<b>Bloqueio padrão de novas notícias</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">deixe em branco para não bloquear</span>", $val['comments']['default_expire'], 505, array("style" => "width:80px; ", "_before" => "Bloquear depois de&nbsp;", "_after" => "&nbsp;dias"));
		$l->tb_nextrow();
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][comments][field1]", "Campo personalizado 1", $val['comments']['field1'], 250);
			$l->tb_input("text", "c[". $s->quote_safe($id) ."][comments][field2]", "Campo personalizado 2", $val['comments']['field2'], 250);
	$l->tb_group("Modelos");
			$l->tb_select("c[". $s->quote_safe($id) ."][templates][usefrom]", "", "=Usar modelos próprios". $list, $val['templates']['usefrom'], 505);
}

$act = $s->req['act']; if (!$act) {$act = "index"; }


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	if (!$s->req['pg']) {$s->req['pg'] = 1; }
	
	$l->form("categories", "edit");
	$l->list_header("sel:id", "150:ID", "150:Nome", "150:Modelos", "150:Ações:nosort");
	$db = $s->db_vars_open($s->cfg['file']['categories']);
	foreach ($db as $k => $v) {
		$tpl = ""; $links = ""; $tpl_edit = $k; if ($v['templates']['usefrom']) {$tpl = "Usando de ". $s->cat[$v['templates']['usefrom']]['name']; $tpl_edit = $v['templates']['usefrom']; } else {$tpl = "Próprios"; }
		$links .= "<span class=\"small\"><a href=\"index.php?s={session}&amp;sec=categories&amp;act=tpl&amp;id=". $tpl_edit ."\">Editar modelos</a></span>";
		$format = "|<a href=\"index.php?s={session}&amp;sec=categories&amp;act=edit&amp;sel[". $k ."]=1\">%s</a>";
		$l->list_item($k, $k . $format, $v['name'] . $format, $tpl . $format, $links);
	}
	if (!$s->req['sort']) {$s->req['sort'] = "2:0"; }
	
	$l->page_link = "index.php?s={session}&amp;sec=categories&amp;sort={sort}&amp;pg={pg}&amp;query={query}";
	$l->list_pg = $s->req['pg'];
	$l->list_perpage = $s->sys['edit']['perpage'];
	$l->list_sort = $s->req['sort'];
	$l->list_query = $s->req['query'];
	
	$l->list_sort(); $l->list_filter();
	
	$l->list_build($s->skin['dir'] ."/order_asc.gif", $s->skin['dir'] ."/order_desc.gif");
	$l->list_footer(array("<b>Adicionar</b>" => "index.php?s={session}&amp;sec=categories&amp;act=new"), 1, array("edit" => "editar", "import" => "importar modelos para", "remove" => "remover"), 1, 616);
	$l->form_end();
}


//-----------------------------------------------------------------------------
// Act new
//-----------------------------------------------------------------------------
else if ($act == "new") {
	$l->form("categories", "new_save"); $l->table(505);
	form_edit("__NEW__");
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=categories"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act new_save
//-----------------------------------------------------------------------------
else if ($act == "new_save") {
	foreach ($s->req['c'] as $id => $req) {
		$m->req('c|'. $id .'|id', 'c|'. $id .'|name', 'c|'. $id .'|headlines|cut', 'c|'. $id .'|news|cut', 'c|'. $id .'|news|limit', 'c|'. $id .'|comments|limit_title', 'c|'. $id .'|comments|limit_comment');
	}
	$id = $s->req['c']['__NEW__']['id'];
	if ($s->cats[$id]) {$m->error_redir("id_inuse"); }
	if ($id == "__NEW__" || $id == "general" || $id == "all") {$m->error_redir("id_system"); }
	if (!preg_match("/^[0-9a-z_]+$/", $id)) {$m->error_redir("id_invalid"); }
	if ($s->req['c']['__NEW__']['templates']['usefrom'] == "") {unset($s->req['c']['__NEW__']['templates']['usefrom']); }
	$s->req['c'][$id] = $s->req['c']['__NEW__']; unset($s->req['c']['__NEW__']);
	
	$db = $s->db_vars_open($s->cfg['file']['categories'], 1);
	$db = $m->array_sync($db, $s->req['c']);
	if (!$s->cfg['ver']['demo']) {$s->db_vars_save($s->cfg['file']['categories'], $db); }
	$m->location("sec=categories", "Categoria adicionada");
}


//-----------------------------------------------------------------------------
// Act edit
//-----------------------------------------------------------------------------
else if ($act == "edit") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$l->form("categories", "edit_save"); $l->table(505);
	foreach ($s->cat as $id => $val) {
		if (!$s->req['sel'][$id]) {continue; }
		form_edit($id, $val);
		$l->tb_separator(30);
	}
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=categories"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act edit_save
//-----------------------------------------------------------------------------
else if ($act == "edit_save") {
	foreach ($s->req['c'] as $id => $req) {
		$m->req('c|'. $id .'|name', 'c|'. $id .'|headlines|cut', 'c|'. $id .'|news|cut', 'c|'. $id .'|news|limit', 'c|'. $id .'|comments|limit_title', 'c|'. $id .'|comments|limit_comment');
		$m->req_sync('c|'. $id .'|fullnews', 'c|'. $id .'|comments|active', 'c|'. $id .'|comments|req_mail', 'c|'. $id .'|comments|req_title', 'c|'. $id .'|comments|mzncode', 'c|'. $id .'|comments|smilies', 'c|'. $id .'|comments|queue', 'c|'. $id .'|comments|field1_r', 'c|'. $id .'|comments|field2_r');
	}
	
	$db = $s->db_vars_open($s->cfg['file']['categories']);
	$db = $m->array_sync($db, $s->req['c']);
	foreach ($s->req['c'] as $id => $req) {
		if ($req['templates']['usefrom'] == "") {unset($db[$id]['templates']['usefrom']); }
		else {unset($db[$id]['templates']); $db[$id]['templates'] = $req['templates']; }
	}
	if (!$s->cfg['ver']['demo']) {$s->db_vars_save($s->cfg['file']['categories'], $db); }
	$m->location("sec=categories", "Alterações salvas");
}


//-----------------------------------------------------------------------------
// Act import
//-----------------------------------------------------------------------------
else if ($act == "import") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$cats = array(); foreach ($s->cat as $k => $v) {$cats[$k] = $v['name']; } asort($cats);
	$list = ""; foreach ($cats as $k => $v) {if ($s->cat[$k]['templates']['usefrom']) {continue; } if ($list) {$list .= "|"; } $list .= $k ."=". $v; } unset($cats);
	$selected = array(); foreach ($s->req['sel'] as $k => $v) {$selected['sel['. $k .']'] = $v; }
	$l->form("categories", "import_save", $selected); $l->table(505);
	
	$l->tb_group("Selecione uma categoria da qual deseja importar");
			$l->tb_select("from", "", $list);
	
	$l->tb_button("submit", "Importar", array("accesskey" => "i"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=categories"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act import_save
//-----------------------------------------------------------------------------
else if ($act == "import_save") {
	if (!$s->req['from'] || !$s->cat[$s->req['from']] || $s->cat[$s->req['from']]['templates']['usefrom']) {$m->error_redir("idinvalid"); }
	
	$db = $s->db_vars_open($s->cfg['file']['categories']);
	$db = $m->array_sync($db, $s->req['c']);
	foreach ($s->req['sel'] as $id => $sel) {
		if ($sel) {$db[$id]['templates'] = $s->cat[$s->req['from']]['templates']; unset($db[$id]['templates']['usefrom']); }
	}
	if (!$s->cfg['ver']['demo']) {$s->db_vars_save($s->cfg['file']['categories'], $db); }
	$m->location("sec=categories", "Modelos importados");
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	unset($s->req['sel']['principal']); $rc = array(); $rp = array();
	
	function retrieve_tpl ($cat) {
		global $s, $m;
		if ($s->cat[$cat]['templates']['usefrom']) {return retrieve_tpl($s->cat[$cat]['templates']['usefrom']); }
		return $s->cat[$cat]['templates'];
	}
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($db['data'] as $k => $v) {
			if ($sel[$v['cid']] == 1) {if (!$rp[$v['user']]) {$rp[$v['user']] = 0; } $rp[$v['user']]++; unset($db['data'][$k]); }
		}
		$s->db_table_save($s->cfg['file']['news'], $db);
		
		$db = $s->db_table_open($s->cfg['file']['comments']);
		foreach ($db['data'] as $k => $v) {
			if ($sel[$v['cid']] == 1) {unset($db['data'][$k]); }
		}
		$s->db_table_save($s->cfg['file']['comments'], $db);
		
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if ($rp[$v['user']]) {$db['data'][$k]['data']['posts'] -= $rp[$v['user']]; }
			foreach ($v['perms'] as $pk => $pv) {
				if ($sel[$pk] == 1) {unset($db['data'][$k]['perms'][$pk]); }
			}
		}
		$s->db_table_save($s->cfg['file']['users'], $db);
		
		$db = $s->db_vars_open($s->cfg['file']['categories']);
		foreach ($s->req['sel'] as $id => $sel) {
			if ($sel) {$rc[$id] = 1; unset($db[$id]); }
		}
		foreach ($db as $k => $v) {
			if ($rc[$v['templates']['usefrom']] == 1) {
				$v['templates'] = retrieve_tpl($v['templates']['usefrom']);
				$db[$k] = $v;
			}
		}
		$s->db_vars_save($s->cfg['file']['categories'], $db);
	}
	
	$msg = "Categoria removida"; if (count($rc) > 1) {$msg = count($rc) ." categorias removidas"; }
	$m->location("sec=categories", $msg);
}


//-----------------------------------------------------------------------------
// Act tpl
//-----------------------------------------------------------------------------
else if ($act == "tpl") {
	if (preg_match("/:/", $s->req['id'])) {$s->req['tpl'] = preg_replace("/([^:]*):(.*)/", "\\2", $s->req['id']); $s->req['id'] = preg_replace("/([^:]*):(.*)/", "\\1", $s->req['id']); }
	if (!$s->req['id'] || !$s->cat[$s->req['id']] || $s->cat[$s->req['id']]['templates']['usefrom']) {$m->error_redir("idinvalid"); }
	$p['tit'] = "templates";
	
	$macros = array();
	$macros['headlines'] = array(
"<a href=\"#Noticia_{news:id}\" title=\"{news:title:nocut}\">{news:title}</a>" => "Link para a notícia (quando exibida na mesma página)",
"<a href=\"pagina_com_as_noticias.php#Noticia_{news:id}\" title=\"{news:title:nocut}\">{news:title}</a>" => "Link para a notícia (quando exibida em outra página)",
"<a href=\"#\" title=\"{news:title:nocut}\" onclick=\"window.open('{system:mzn2dir}/classic/view.php?type=news&id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">{news:title}</a>" => "Link para a notícia em uma janela PopUp (usa a estrutura clássica)",
	);
	$macros['news'] = array(
"<img src=\"http://wwp.icq.com/scripts/online.dll?icq={user:icq}&img=5\">" => "Status do ICQ (flor verde, cinza ou vermelha)",
"<a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/comments.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Comentários: {news:comments}</a>" => "Link para os comentários em uma janela PopUp (usa a estrutura clássica)",
"<a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/print.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Imprimir notícia</a>" => "Link para impressão da notícia em uma janela PopUp (usa a estrutura clássica)",
"<a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/sendmail.php?id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Enviar por e-mail</a>" => "Link para enviar notícia por e-mail em uma janela PopUp (usa a estrutura clássica)",
	);
	$macros['fnews_link'] = array(
"<a href=\"#\" onclick=\"window.open('{system:mzn2dir}/classic/view.php?type=fnews&id={news:id}', '_blank', 'width=491,height=380,resizable=1,scrollbars=1'); return false; \">Ler notícia completa</a>" => "Link em uma janela PopUp (usa a estrutura clássica)",
	);
	
	$fields = array();
	$fields['system'] = array(
"system:mzn2dir" => "Pasta do MZn²",
"system:thispage" => "Caminho da página atual",
	);
	$fields['news'] = array(
"news:id" => "ID",
"news:title" => "Título",
"news:title:nocut" => "Título sem cortes",
"news:contents" => "Notícia",
"news:full" => "Notícia completa",
"news:comments" => "Número de comentários",
"news:date" => "Data formatada"
	);
	$fields['comments'] = array(
"comment:id" => "ID",
"comment:title" => "Título",
"comment:contents" => "Comentário",
"comment:date" => "Data formatada",
"comment:name" => "Nome do visitante",
"comment:mail" => "E-mail do visitante",
"comment:ip" => "IP do visitante",
"comment:field1" => $s->cat[$s->req['id']]['comments']['field1'],
"comment:field2" => $s->cat[$s->req['id']]['comments']['field2']
	);
	$fields['user'] = array(
"user:login" => "Login",
"user:name" => "Nome",
"user:mail" => "E-mail",
"user:icq" => "ICQ",
"user:field1" => $s->sys['cfield']['field1'],
"user:field2" => $s->sys['cfield']['field2'],
"user:field3" => $s->sys['cfield']['field3'],
"user:posts" => "Total de posts"
	);
	$fields['date1'] = array(
"date:%D" => "Dia da semana - Seg à Dom",
"date:%l" => "Dia da semana - Segunda à Domingo",
"date:%d" => "Dia do mês - 01 à 31",
"date:%j" => "Dia do mês - 1 à 31",
"date:%m" => "Mês - 01 à 12",
"date:%M" => "Mês - Jan à Dez",
"date:%F" => "Mês - Janeiro à Dezembro",
"date:%y" => "Ano - 00",
"date:%Y" => "Ano - 2000",
	);
	$fields['date2'] = array(
"date:%g" => "Hora - 12h - 1 à 12",
"date:%G" => "Hora - 24h - 0 à 23",
"date:%h" => "Hora - 12h - 01 à 12",
"date:%H" => "Hora - 24h - 00 à 23",
"date:%i" => "Minutos - 00 à 59",
"date:%s" => "Segundos - 00 à 59",
"date:%a" => "am ou pm",
"date:%A" => "AM ou PM",
	);
	$fields['mail'] = array(
"mail:from_mail" => "E-mail do remetente",
"mail:from_name" => "Nome do remetente",
"mail:to_mail" => "E-mail do destinatário",
"mail:to_name" => "Nome do destinatário",
"mail:subject" => "Assunto",
	);
	
	$names = array(
"headlines" => "Headlines (manchetes)",
"news" => "Notícia",
"fnews" => "Notícia completa",
"fnews_link" => "Link para notícia completa",
"daygroup" => "Agrupador diário",
"comment" => "Comentário",
"date" => "Formato da data",
"mailnews" => "Notícia por e-mail",
"print" => "Notícia para impressão",
"link" => "Modelo geral de link",
	);
	
	function fields_return($array = array(), $pre = "") {
		$res = "";
		foreach ($array as $k => $v) {if (!$v) {continue; } if ($res) {$res .= "|"; } $res .= "{". $k ."}=". $pre . $v; }
		return $res;
	}
	function macros_return($array = array(), $pre = "") {
		global $s;
		$res = "";
		foreach ($array as $k => $v) {if (!$v) {continue; } if ($res) {$res .= "|"; } $res .= "macro/". $s->jsescape($k) ."=". $pre . $v; }
		return $res;
	}
	
	if ($s->req['tpl']) {
		if (!$names[$s->req['tpl']]) {$m->error_redir("idinvalid"); }
		
		$tools = "=Tags do MZn² e HTMLs predefinidos||";
		switch ($s->req['tpl']) {
			case "headlines":
				$tools .= "=HTMLs predefinidos|". macros_return($macros['headlines'], "   ") ."||=Variáveis do sistema|". fields_return($fields['system'], "   ") ."||=Campos da notícia|". fields_return($fields['news'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ") ."||=Dados do usuário|". fields_return($fields['user'], "   ");
				break;
			case "news":
				$tools .= "=HTMLs predefinidos|". macros_return($macros['news'], "   ") ."||=Variáveis do sistema|". fields_return($fields['system'], "   ") ."||=Campos da notícia|". fields_return($fields['news'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ") ."||=Dados do usuário|". fields_return($fields['user'], "   ");
				break;
			case "fnews":
				$tools .= "=HTMLs predefinidos|". macros_return($macros['news'], "   ") ."||=Variáveis do sistema|". fields_return($fields['system'], "   ") ."||=Campos da notícia|". fields_return($fields['news'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ") ."||=Dados do usuário|". fields_return($fields['user'], "   ");
				break;
			case "fnews_link":
				$tools .= "=HTMLs predefinidos|". macros_return($macros['fnews_link'], "   ") ."||=Variáveis do sistema|". fields_return($fields['system'], "   ") ."||=Campos da notícia|". fields_return($fields['news'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ") ."||=Dados do usuário|". fields_return($fields['user'], "   ");
				break;
			case "daygroup":
				$tools .= "{news}=Notícias do dia||=Data|". fields_return($fields['date1'], "   ");
				break;
			case "comment":
				$tools .= "=Variáveis do sistema|". fields_return($fields['system'], "   ") ."||=Campos do comentário|". fields_return($fields['comments'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ");
				break;
			case "date":
				$tools .= "=Campos do calendário (data)|%D=   Dia da semana - Seg à Dom|%l=   Dia da semana - Segunda à Domingo|%d=   Dia do mês - 01 à 31|%j=   Dia do mês - 1 à 31|%m=   Mês - 01 à 12|%M=   Mês - Jan à Dez|%F=   Mês - Janeiro à Dezembro|%y=   Ano - 00|%Y=   Ano - 2000||=Campos do relógio (hora)|%g=   Hora - 12h - 1 à 12|%G=   Hora - 24h - 0 à 23|%h=   Hora - 12h - 01 à 12|%H=   Hora - 24h - 00 à 23|%i=   Minutos - 00 à 59|%s=   Segundos - 00 à 59|%a=   am ou pm|%A=   AM ou PM";
				break;
			case "mailnews":
				$tools .= "=Campos do e-mail|". fields_return($fields['mail'], "   ") ."||=Campos da notícia|". fields_return($fields['news'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ") ."||=Dados do usuário|". fields_return($fields['user'], "   ");
				break;
			case "print":
				$tools .= "=Variáveis do sistema|". fields_return($fields['system'], "   ") ."||=Campos da notícia|". fields_return($fields['news'], "   ") ."||=Campos do calendário (data)|". fields_return($fields['date1'], "   ") ."||=Campos do relógio (hora)|". fields_return($fields['date2'], "   ") ."||=Dados do usuário|". fields_return($fields['user'], "   ");
				break;
			case "link":
				$tools .= "=Campos do link|{link:href}=   Destino do link (href)|{link:target}=   Alvo do link (target)|{link:text}=   Texto do link";
				break;
		}
		
		$list = "__NEXT__=o próximo modelo|__INDEX__=a lista de modelos|__CATS__=a lista de categorias"; foreach ($names as $k => $v) {$list .= "|". $k ."=o modelo de ". $v; }
		
		$val = array();
		$val['html'] = $s->cat[$s->req['id']]['templates'][$s->req['tpl']];
		
		$l->form("categories", "tpl_save", array("id" => $s->req['id'], "tpl" => $s->req['tpl'])); $l->table(600);
		
		$l->tb_group($names[$s->req['tpl']]);
				$l->tb_select("", "", $tools, null, 600, array("onchange" => "var x = this.options[this.selectedIndex].value; if (x) {if (x.indexOf('macro/') == 0) {x = x.substring(6, x.length); MZn2_edv_paste('edv', unescape(x));  } else {MZn2_edv_paste('edv', x); } } this.selectedIndex = 0; ", "cla-ss" => "small"));
			$l->tb_nextrow();
				$l->tb_text("c[html]", "", $val['html'], 600, array("id" => "MZn2_edv_code", "wrap" => "off", "class" => "code", "style" => "width:594px; height:400px; "));
		$l->tb_group("Ao salvar, ir para...");
				$l->tb_select("go", "", $list, "__NEXT__", 600);
		
		$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
		$l->tb_button("cancel", "Cancelar", array("_go" => "sec=categories&act=tpl&id=". $s->req['id']));
		$l->table_end(); $l->form_end();
	}
	else {
		$list = ""; foreach ($names as $k => $v) {if ($list) {$list .= "|"; } $list .= $k ."=". $v; }
		$l->form("categories", "tpl", array("id" => $s->req['id']), "get"); $l->table(505);
		
		$l->tb_group("Selecione um modelo para editar");
				$l->tb_select("tpl", "", $list);
		
		$l->tb_button("submit", "Editar", array("accesskey" => "e"));
		$l->tb_button("cancel", "Cancelar", array("_go" => "sec=categories"));
		$l->table_end(); $l->form_end();
	}
}


//-----------------------------------------------------------------------------
// Act tpl_save
//-----------------------------------------------------------------------------
else if ($act == "tpl_save") {
	$names = array(
"headlines" => "Headlines (manchetes)",
"news" => "Notícia",
"fnews" => "Notícia completa",
"fnews_link" => "Link para notícia completa",
"daygroup" => "Agrupador diário",
"comment" => "Comentário",
"date" => "Formato da data",
"mailnews" => "Notícia por e-mail",
"print" => "Notícia para impressão",
"link" => "Modelo geral de link",
	);
	
	if (!$s->req['id'] || !$s->req['tpl'] || !$s->cat[$s->req['id']] || $s->cat[$s->req['id']]['templates']['usefrom']) {$m->error_redir("idinvalid"); }
	$m->req('c|html');
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_vars_open($s->cfg['file']['categories']);
		$db[$s->req['id']]['templates'][$s->req['tpl']] = $s->req['c']['html'];
		$s->db_vars_save($s->cfg['file']['categories'], $db);
	}
	
	$go = $s->req['go']; if (!$go) {$go = "__INDEX__"; } $go_url = "";
	
	if ($go == "__INDEX__") {$go_url = "sec=categories&act=tpl&id=". $s->req['id']; }
	else if ($go == "__CATS__") {$go_url = "sec=categories"; }
	else if ($go == "__NEXT__") {reset($names); while (list($k, $v) = each($names)) {if ($k == $s->req['tpl']) {break; } } $go_url = "sec=categories&act=tpl&id=". $s->req['id'] ."&tpl=". key($names); }
	else {$go_url = "sec=categories&act=tpl&id=". $s->req['id'] ."&tpl=". $go; }
	
	$m->location($go_url, "Alterações salvas");
}



?>
