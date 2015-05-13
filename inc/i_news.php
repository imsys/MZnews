<?php $p['tit'] = "news"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("post|editown|editall|comments", $cat);

$act = $s->req['act']; if (!$act) {$act = "index"; }

//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	$m->req_perms("post|editown|editall|comments", $cat);
	if (!$s->req['pg']) {$s->req['pg'] = 1; }
	
	$db = $s->db_table_open($s->cfg['file']['comments']); $comments = array(); $comments_q = array();
	foreach ($db['data'] as $k => $v) {
		if ($v['cid'] != $cat) {continue; }
		if ($v['data']['q']) {
			if (!$comments_q[$v['nid']]) {$comments_q[$v['nid']] = 0; }
			$comments_q[$v['nid']] += 1;
		}
		if (!$comments[$v['nid']]) {$comments[$v['nid']] = 0; }
		$comments[$v['nid']] += 1;
	}
	
	$links = array(); if ($m->perms("post", $cat)) {$links = array("<b>Adicionar</b>" => "index.php?s={session}&amp;sec=news&amp;act=new"); }
	$actions = array("copy" => "copiar", "move" => "mover", "remove" => "remover"); if ($m->perms("editqueue", $cat)) {$actions = array("show" => "exibir", "hide" => "ocultar", "copy" => "copiar", "move" => "mover", "remove" => "remover"); }
	
	$l->form("news", "remove");
	
	$l->list_header("sel:id", "370:Notícia", "140:Por", "100:Em");
	$db = $s->db_table_open($s->cfg['file']['news']);
	foreach ($db['data'] as $k => $v) {
		if ($v['cid'] != $cat) {continue; }
		$c_count = ($comments[$v['id']])? $comments[$v['id']] : 0;
		$c_queue = ($comments_q[$v['id']])? $comments_q[$v['id']] : 0;
		if ($c_queue) {$c_count .= " (". $c_queue ." na fila)"; }
		
		$dis = 0; $link = "|<a href=\"index.php?s={session}&amp;sec=news&amp;act=edit&amp;id=". $v['id'] ."\"><b>%s</b></a>"; $dateLink = "";
		if (!$m->perms("editown|editall", $cat)) {$dis = 1; $link = "|<b>%s</b>"; $dateLink = "|". $m->parse_date($v['time'], "<br />"); }
		else if (!$m->perms("editall", $cat) && $v['user'] != $s->usr['user']) {$dis = 1; $link = "|<b>%s</b>"; $dateLink = "|". $m->parse_date($v['time'], "<br />"); }
		else {if ($v['data']['q']) {$link = "|<a href=\"index.php?s={session}&amp;sec=news&amp;act=edit&amp;id=". $v['id'] ."\"><b>%s</b>&nbsp;<img src=\"img/{skin}/news_hidden.gif\" align=\"absmiddle\" border=\"0\" alt=\"Esta notícia não está sendo exibida pois está na fila de moderação\" /></a>"; } if ($v['data']['t'] && $s->cfg['time'] < $v['time']) {$link = "|<a href=\"index.php?s={session}&amp;sec=news&amp;act=edit&amp;id=". $v['id'] ."\"><b>%s</b>&nbsp;<img src=\"img/{skin}/news_hidden.gif\" align=\"absmiddle\" border=\"0\" alt=\"Esta notícia só será exibida em ". date("d/m/Y", $v['time']) ." às ". date("H:i:s", $v['time']) ."\" /></a>"; } $dateLink = "|<a href=\"index.php?s={session}&amp;sec=news&amp;act=edit&amp;id=". $v['id'] ."\">". $m->parse_date($v['time'], "<br />") ."</a>"; }
		
		if ($m->perms("comments", $cat)) {$link .= "<br /><a href=\"index.php?s={session}&amp;sec=comments&amp;nid=". $v['id'] ."\">Comentários:&nbsp;". $c_count ."</a>"; }
		else {$link .= "<br />Comentários:&nbsp;". $c_count; }
		// A linha abaixo coloca (desativado) se os comentários estiverem desativados
		//if ($v['data']['nc']) {$link .= " (desativado)"; }
		
		if ($v['user'] == $s->usr['user']) {$usrLink = "|<a href=\"index.php?s={session}&amp;sec=profile\">%s</a>"; }
		else if ($m->perms("users")) {$usrLink = "|<a href=\"index.php?s={session}&amp;sec=users&amp;act=edit&amp;sel[". $s->users[$v['user']]['id'] ."]=1\">%s</a>"; }
		else {$usrLink = ""; }
		
		$l->list_item($k, $v['title'] . $link, $s->users[$v['user']]['name'] . $usrLink, $v['time'] . $dateLink, array("colclass" => "small", "disabled" => $dis));
	}
	if (!$s->req['sort']) {$s->req['sort'] = "3:1"; }
	
	$l->page_link = "index.php?s={session}&amp;sec=news&amp;sort={sort}&amp;pg={pg}&amp;query={query}";
	$l->list_pg = $s->req['pg'];
	$l->list_perpage = $s->sys['edit']['perpage'];
	$l->list_sort = $s->req['sort'];
	$l->list_query = $s->req['query'];
	
	$l->list_sort(); $l->list_filter();
	
	$l->list_build($s->skin['dir'] ."/order_asc.gif", $s->skin['dir'] ."/order_desc.gif", null);
	$l->list_footer($links, 1, $actions, 1, 616);
	$l->form_end();
}


//-----------------------------------------------------------------------------
// Act new
//-----------------------------------------------------------------------------
else if ($act == "new") {
	$m->req_perms("post", $cat);
	
	$cancel = ""; if ($m->perms("editown|editall")) {$cancel = "sec=news"; }
	$val = array(); $val['user'] = $s->usr['user'];
	$val['news'] = "<div></div>";
	
	$only_mznCode = 1;
	if ($s->sys['edv']) {$only_mznCode = 0; if ($s->usr['data']['noedv']) {$only_mznCode = 1; } }
	$mode = "h"; if ($only_mznCode) {$mode = "c"; }
	
	$l->form("news", "new_save", array("c[news]" => $val['news'], "c[data][nm]" => $mode, "c[fnews]" => $val['fnews'], "c[data][fm]" => $mode, "c[data][o]" => $only_mznCode)); $l->table(505);
	$l->tb_group("Dados");
		if ($m->perms("editall", $cat)) {
			$list = ""; $list_array = array(); foreach ($s->users as $k => $v) {$list_array[$k] = $v['name']; } asort($list_array); foreach ($list_array as $k => $v) {if ($list) {$list .= "|"; } $list .= $k ."=". $v; }
				$l->tb_select("c[user]", "<b>Usuário</b>", $list, $val['user'], 505);
			$l->tb_nextrow();
		}
			$tit_extra = array("title" => "Se o fundo mudar de cor, o título será cortado!");
			if ($s->cat[$cat]['news']['cut'] > 0) {$tit_extra['onkeyup'] = "if (this.value.length > ". $s->cat[$cat]['news']['cut'] .") {if (this.className != 'warning') {this.className = 'warning'; } } else {if (this.className != 'normal') {this.className = 'normal'; } } "; }
			if ($s->cat[$cat]['news']['limit'] > 0) {$tit_extra['maxlength'] = $s->cat[$cat]['news']['limit']; }
			$l->tb_input("text", "c[title]", "<b>Título</b>", $val['title'], 505, $tit_extra);
	$l->tb_group("Notícia");
			$l->tb_custom("<iframe src=\"wait.php?sleep=1&g=". urlencode("index.php?s=". $s->req['s'] ."&sec=edv&only_mznCode=". $only_mznCode ."&obj=news") ."\" tabindex=\"". $l->tabindex ."\" name=\"edv_news\" scrolling=\"no\" width=\"505\" height=\"290\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"no\"></iframe>", 505); $l->tabindex++;
	$l->tb_group("Notícia completa");
			$l->tb_check("checkbox", "c[data][f]", "", "1=Usar notícia completa", $val['data']['f'], 505);
		$l->tb_nextrow();
			$l->tb_custom("<iframe src=\"wait.php?sleep=2&g=". urlencode("index.php?s=". $s->req['s'] ."&sec=edv&only_mznCode=". $only_mznCode ."&obj=fnews") ."\" tabindex=\"". $l->tabindex ."\" name=\"edv_fnews\" scrolling=\"no\" width=\"505\" height=\"290\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"no\"></iframe>", 505); $l->tabindex++;
	if ($m->perms("cgdate", $cat)) {
		$l->tb_group("Data (opcional)");
				$months = array("janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
				$list = "=-"; for ($i = 1; $i <= 31; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][day]", "", $list, $val['date']['day'], 51);
				$l->tb_custom("de", 20, "center", "middle");
				$list = "=-"; for ($i = 1; $i <= 12; $i++) {$it = $months[$i - 1]; $list .= "|". $i ."=". $it; } $l->tb_select("c[date][month]", "", $list, $val['date']['month'], 102);
				$l->tb_custom("de", 20, "center", "middle");
				$list = "=-"; for ($i = intval(date("Y", $s->cfg['time']) - 5); $i <= intval(date("Y", $s->cfg['time']) + 10); $i++) {$it = $i; $list .= "|". $i ."=". $it; } $l->tb_select("c[date][year]", "", $list, $val['date']['year'], 76);
				$l->tb_custom("às", 23, "center", "middle");
				$list = "=-"; for ($i = 0; $i <= 24; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][hour]", "", $list, $val['date']['hour'], 51);
				$l->tb_custom(":", 5, "center", "middle");
				$list = "=-"; for ($i = 0; $i <= 59; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][minute]", "", $list, $val['date']['minute'], 51);
				$l->tb_custom(":", 5, "center", "middle");
				$list = "=-"; for ($i = 0; $i <= 59; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][second]", "", $list, $val['date']['second'], 51);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[data][t]", "", "1=Não exibir a notícia até esta data", $val['data']['t'], 505);
	}
	$l->tb_group("Bloqueio de comentários");
				$l->tb_check("checkbox", "c[data][nc]", "", "1=Bloquear sempre", $val['data']['nc'], 150);
				$l->tb_input("text", "c[data][cx]", "<b>Bloqueio temporizado</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">deixe em branco para não bloquear</span>", $s->cat[$cat]['comments']['default_expire'], 350, array("style" => "width:60px; ", "_before" => "Bloquear depois de&nbsp;", "_after" => "&nbsp;dias"));
	$l->tb_group("Opções");
		if ($only_mznCode) {
				$l->tb_check("checkbox", "c[data][b]", "", "1=Não substituir quebras de linha por &lt;br&gt;", $val['data']['b'], 250);
				$l->tb_check("checkbox", "c[data][c]", "", "1=Não substituir mznCode", $val['data']['c'], 250);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[data][s]", "", "1=Não substituir smilies", $val['data']['s'], 250);
		}
		else {
				$l->tb_check("checkbox", "c[data][b]", "", "1=Não substituir quebras de linha por &lt;br&gt;", "1", 250, array("disabled" => 1));
				$l->tb_check("checkbox", "c[data][c]", "", "1=Não substituir mznCode", "0", 250, array("disabled" => 1));
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[data][s]", "", "1=Não substituir smilies", "1", 250, array("disabled" => 1));
		}
		if ($m->perms("editqueue", $cat)) {
				$l->tb_check("checkbox", "c[data][q]", "", "1=Está na fila (oculta)", $val['data']['q'], 250);
		}
	
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
//	$l->tb_button("button", "Visualizar");
	$l->tb_button("cancel", "Cancelar", array("_go" => $cancel));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act new_save
//-----------------------------------------------------------------------------
else if ($act == "new_save") {
	$m->req_perms("post", $cat);
	
	if (!$s->req['c']['data']['o']) {
		if ($s->req['c']['data']['nm'] == "h") {$s->req['c']['news'] = $m->html_to_mznCode($s->req['c']['news']); if ($s->req['c']['news'] == "[align=left][/align]") {$s->req['c']['news'] = ""; } }
		if ($s->req['c']['data']['fm'] == "h") {$s->req['c']['fnews'] = $m->html_to_mznCode($s->req['c']['fnews']); if ($s->req['c']['fnews'] == "[align=left][/align]") {$s->req['c']['fnews'] = ""; } }
		$s->req['c']['data']['b'] = 1;
		$s->req['c']['data']['c'] = 0;
		$s->req['c']['data']['s'] = 1;
	}
	
	$m->req('c|title', 'c|news'); if ($s->req['c']['data']['f']) {$m->req('c|fnews'); }
	if (!$m->perms("editqueue", $cat)) {unset($s->req['c']['data']['q']); }
	if ($s->usr['data']['usequeue']) {$s->req['c']['data']['q'] = 1; }
	
	if (!$s->cfg['ver']['demo']) {
		$nl = array();
		$nl['id'] = substr(md5(rand()*time()), 0, 10);
		$nl['cid'] = $cat;
		$nl['time'] = $s->cfg['time']; if ($m->perms("cgdate", $cat) && $s->req['c']['date']['day'] != "" && $s->req['c']['date']['month'] != "" && $s->req['c']['date']['year'] != "" && $s->req['c']['date']['hour'] != "" && $s->req['c']['date']['minute'] != "" && $s->req['c']['date']['second'] != "") {$nl['time'] = mktime($s->req['c']['date']['hour'], $s->req['c']['date']['minute'], $s->req['c']['date']['second'], $s->req['c']['date']['month'], $s->req['c']['date']['day'], $s->req['c']['date']['year']); }
		$nl['user'] = $s->usr['user']; if ($m->perms("editall", $cat)) {$nl['user'] = $s->req['c']['user']; }
		$nl['title'] = $s->req['c']['title']; if ($s->cat[$cat]['news']['limit'] > 0) {$nl['title'] = substr($nl['title'], 0, $s->cat[$cat]['news']['limit']); }
		$nl['news'] = $s->req['c']['news'];
		$nl['fnews'] = $s->req['c']['fnews'];
		$nl['data'] = $s->req['c']['data'];
		
		$db = $s->db_table_open($s->cfg['file']['news']);
		$db['data'][] = $nl;
		$s->db_table_save($s->cfg['file']['news'], $db);
		
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if ($v['user'] != $nl['user']) {continue; }
			$db['data'][$k]['data']['posts'] += 1;
			$db['data'][$k]['data']['lastpost'] = $s->cfg['time'];
		}
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	
	$m->location("sec=news", "Notícia adicionada");
}


//-----------------------------------------------------------------------------
// Act edit
//-----------------------------------------------------------------------------
else if ($act == "edit") {
	$m->req_perms("editown|editall", $cat);
	
	if (!$s->req['id']) {$m->error_redir("idinvalid"); }
	$ok = 0; $val = array();
	$db = $s->db_table_open($s->cfg['file']['news']);
	foreach ($db['data'] as $k => $v) {
		if ($v['cid'] != $cat || $v['id'] != $s->req['id']) {continue; }
		$ok = 1;
		if (!$m->perms("editall", $cat) && $v['user'] != $s->usr['user']) {$m->error_redir("idinvalid"); }
		$val = $v;
	}
	if (!$ok) {$m->error_redir("idinvalid"); }
	
	$only_mznCode = 1; if ($s->sys['edv']) {$only_mznCode = 0; if ($val['data']['o'] || $s->usr['data']['noedv']) {$only_mznCode = 1; } }
	
	$l->form("news", "edit_save", array("id" => $s->req['id'], "c[news]" => $val['news'], "c[data][nm]" => $val['data']['nm'], "c[fnews]" => $val['fnews'], "c[data][fm]" => $val['data']['fm'], "c[data][o]" => $only_mznCode)); $l->table(505);
	$l->tb_group("Dados");
		if ($m->perms("editall", $cat)) {
			$list = ""; foreach ($s->users as $k => $v) {if ($list) {$list .= "|"; } $list .= $k ."=". $v['name']; }
				$l->tb_select("c[user]", "<b>Usuário</b>", $list, $val['user'], 505);
			$l->tb_nextrow();
		}
			$tit_extra = array("title" => "Se o fundo estiver de uma outra cor, o título será cortado!");
			if ($s->cat[$cat]['news']['cut'] > 0 && strlen($val['title']) > $s->cat[$cat]['news']['cut']) {$tit_extra['class'] = "warning"; }
			if ($s->cat[$cat]['news']['cut'] > 0) {$tit_extra['onkeyup'] = "if (this.value.length > ". $s->cat[$cat]['news']['cut'] .") {if (this.className != 'warning') {this.className = 'warning'; } } else {if (this.className != 'normal') {this.className = 'normal'; } } "; }
			if ($s->cat[$cat]['news']['limit'] > 0) {$tit_extra['maxlength'] = $s->cat[$cat]['news']['limit']; }
			$l->tb_input("text", "c[title]", "<b>Título</b>", $val['title'], 505, $tit_extra);
	$l->tb_group("Notícia");
			$l->tb_custom("<iframe src=\"wait.php?sleep=1&g=". urlencode("index.php?s=". $s->req['s'] ."&sec=edv&only_mznCode=". $only_mznCode ."&obj=news&mode=". $val['data']['nm'] ."&news_id=". $val['id']) ."\" tabindex=\"". $l->tabindex ."\" name=\"edv_news\" scrolling=\"no\" width=\"505\" height=\"290\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"no\"></iframe>", 505); $l->tabindex++;
	$l->tb_group("Notícia completa");
			$l->tb_check("checkbox", "c[data][f]", "", "1=Usar notícia completa", $val['data']['f'], 505);
		$l->tb_nextrow();
			$l->tb_custom("<iframe src=\"wait.php?sleep=2&g=". urlencode("index.php?s=". $s->req['s'] ."&sec=edv&only_mznCode=". $only_mznCode ."&obj=fnews&mode=". $val['data']['fm'] ."&fnews_id=". $val['id']) ."\" tabindex=\"". $l->tabindex ."\" name=\"edv_fnews\" scrolling=\"no\" width=\"505\" height=\"290\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"no\"></iframe>", 505); $l->tabindex++;
	if ($m->perms("cgdate", $cat)) {
		$l->tb_group("Data (opcional)");
				$months = array("janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro");
				$list = "=-"; for ($i = 1; $i <= 31; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][day]", "", $list, date("d", $val['time']), 51);
				$l->tb_custom("de", 20, "center", "middle");
				$list = "=-"; for ($i = 1; $i <= 12; $i++) {$it = $months[$i - 1]; $list .= "|". $i ."=". $it; } $l->tb_select("c[date][month]", "", $list, date("n", $val['time']), 102);
				$l->tb_custom("de", 20, "center", "middle");
				$list = "=-"; for ($i = intval(date("Y", $s->cfg['time']) - 5); $i <= intval(date("Y", $s->cfg['time']) + 10); $i++) {$it = $i; $list .= "|". $i ."=". $it; } $l->tb_select("c[date][year]", "", $list, date("Y", $val['time']), 76);
				$l->tb_custom("às", 23, "center", "middle");
				$list = "=-"; for ($i = 0; $i <= 23; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][hour]", "", $list, date("H", $val['time']), 51);
				$l->tb_custom(":", 5, "center", "middle");
				$list = "=-"; for ($i = 0; $i <= 59; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][minute]", "", $list, date("i", $val['time']), 51);
				$l->tb_custom(":", 5, "center", "middle");
				$list = "=-"; for ($i = 0; $i <= 59; $i++) {$it = $i; if ($i < 10) {$it = "0". $i; } $list .= "|". $i ."=". $it; } $l->tb_select("c[date][second]", "", $list, date("s", $val['time']), 51);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[data][t]", "", "1=Não exibir a notícia até esta data", $val['data']['t'], 505);
	}
	$l->tb_group("Bloqueio de comentários");
				$l->tb_check("checkbox", "c[data][nc]", "", "1=Bloquear sempre", $val['data']['nc'], 150);
				$l->tb_input("text", "c[data][cx]", "<b>Bloqueio temporizado</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">deixe em branco para não bloquear</span>", $val['data']['cx'], 350, array("style" => "width:60px; ", "_before" => "Bloquear depois de&nbsp;", "_after" => "&nbsp;dias"));
	$l->tb_group("Opções");
		if ($only_mznCode) {
				$l->tb_check("checkbox", "c[data][b]", "", "1=Não substituir quebras de linha por &lt;br&gt;", $val['data']['b'], 250);
				$l->tb_check("checkbox", "c[data][c]", "", "1=Não substituir mznCode", $val['data']['c'], 250);
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[data][s]", "", "1=Não substituir smilies", $val['data']['s'], 250);
		}
		else {
				$l->tb_check("checkbox", "c[data][b]", "", "1=Não substituir quebras de linha por &lt;br&gt;", "1", 250, array("disabled" => 1));
				$l->tb_check("checkbox", "c[data][c]", "", "1=Não substituir mznCode", "0", 250, array("disabled" => 1));
			$l->tb_nextrow();
				$l->tb_check("checkbox", "c[data][s]", "", "1=Não substituir smilies", "1", 250, array("disabled" => 1));
		}
		if ($m->perms("editqueue", $cat)) {
				$l->tb_check("checkbox", "c[data][q]", "", "1=Está na fila (oculta)", $val['data']['q'], 250);
		}
	
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
//	$l->tb_button("button", "Visualizar");
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=news"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act edit_save
//-----------------------------------------------------------------------------
else if ($act == "edit_save") {
	$m->req_perms("editown|editall", $cat);
	
	if (!$s->req['id']) {$m->error_redir("idinvalid"); }
	
	if (!$s->req['c']['data']['o']) {
		if ($s->req['c']['data']['nm'] == "h") {$s->req['c']['news'] = $m->html_to_mznCode($s->req['c']['news']); if ($s->req['c']['news'] == "[align=left][/align]") {$s->req['c']['news'] = ""; } }
		if ($s->req['c']['data']['fm'] == "h") {$s->req['c']['fnews'] = $m->html_to_mznCode($s->req['c']['fnews']); if ($s->req['c']['fnews'] == "[align=left][/align]") {$s->req['c']['fnews'] = ""; } }
		$s->req['c']['data']['b'] = 1;
		$s->req['c']['data']['c'] = 0;
		$s->req['c']['data']['s'] = 1;
	}
	
	$m->req('c|title', 'c|news'); if ($s->req['c']['data']['f']) {$m->req('c|title', 'c|fnews'); }
	$m->req_sync('c|data|f', 'c|data|t', 'c|data|b', 'c|data|c', 'c|data|s', 'c|data|nc', 'c|data|q');
	
	if (!$s->cfg['ver']['demo']) {
		$ok = 0;
		$db = $s->db_table_open($s->cfg['file']['news']); $oldUser = ""; $newUser = "";
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['id']) {continue; }
			$ok = 1;
			if (!$m->perms("editall", $cat) && $v['user'] != $s->usr['user']) {$m->error_redir("noperms"); }
			if (!$m->perms("editqueue", $cat)) {unset($s->req['c']['data']['q']); }
			
			$oldUser = $v['user'];
			
			$nl = $v;
			$nl['cid'] = $cat;
			if ($m->perms("cgdate", $cat) && $s->req['c']['date']['day'] != "" && $s->req['c']['date']['month'] != "" && $s->req['c']['date']['year'] != "" && $s->req['c']['date']['hour'] != "" && $s->req['c']['date']['minute'] != "" && $s->req['c']['date']['second'] != "") {$nl['time'] = mktime($s->req['c']['date']['hour'], $s->req['c']['date']['minute'], $s->req['c']['date']['second'], $s->req['c']['date']['month'], $s->req['c']['date']['day'], $s->req['c']['date']['year']); }
			if ($m->perms("editall", $cat)) {$nl['user'] = $s->req['c']['user']; }
			$nl['title'] = $s->req['c']['title']; if ($s->cat[$cat]['news']['limit'] > 0) {$nl['title'] = substr($nl['title'], 0, $s->cat[$cat]['news']['limit']); }
			$nl['news'] = $s->req['c']['news'];
			$nl['fnews'] = $s->req['c']['fnews'];
			$nl['data'] = $s->req['c']['data'];
			$db['data'][$k] = $nl;
			
			$newUser = $nl['user'];
		}
		if (!$ok) {$m->error_redir("idinvalid"); }
		
		$s->db_table_save($s->cfg['file']['news'], $db);
		
		if ($newUser != "" && $oldUser != $newUser) {
			$db = $s->db_table_open($s->cfg['file']['users']);
			foreach ($db['data'] as $k => $v) {
				if ($v['user'] == $oldUser) {$db['data'][$k]['data']['posts'] -= 1; }
				else if ($v['user'] == $newUser) {$db['data'][$k]['data']['posts'] += 1; }
			}
			$s->db_table_save($s->cfg['file']['users'], $db);
		}
	}
	
	$m->location("sec=news", "Notícia alterada");
}


//-----------------------------------------------------------------------------
// Act move
//-----------------------------------------------------------------------------
else if ($act == "move") {
	$m->req_perms("editown|editall", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$cats = array(); foreach ($s->cat as $k => $v) {$cats[$k] = $v['name']; } asort($cats);
	$list = ""; foreach ($cats as $k => $v) {if (!$m->perms("editown|editall", $k) || $k == $cat) {continue; } if ($list) {$list .= "|"; } $list .= $k ."=". $v; } unset($cats);
	$selected = array(); foreach ($s->req['sel'] as $k => $v) {$selected['sel['. $k .']'] = $v; }
	$l->form("news", "move_save", $selected); $l->table(505);
	
	$l->tb_group("Selecione a categoria de destino");
			$l->tb_select("c[cat]", "", $list);
	
	$l->tb_button("submit", "Mover", array("accesskey" => "i"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=news"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act move_save
//-----------------------------------------------------------------------------
else if ($act == "move_save") {
	$m->req_perms("editown|editall", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	if (!$s->req['c']['cat'] || !$s->cat[$s->req['c']['cat']]) {$m->error_redir("idinvalid"); }
	$count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($s->req['sel'] as $k => $v) {
			if (!$m->perms("editall", $cat) && $db['data'][$k]['user'] != $s->usr['user']) {continue; }
			else if (!$m->perms("editown", $s->req['c']['cat']) && $db['data'][$k]['user'] == $s->usr['user']) {continue; }
			else if (!$m->perms("editall", $s->req['c']['cat']) && $db['data'][$k]['user'] != $s->usr['user']) {continue; }
			$db['data'][$k]['cid'] = $s->req['c']['cat']; $count += 1;
		}
		$s->db_table_save($s->cfg['file']['news'], $db);
	}
	
	$msg = "Notícia movida"; if ($count > 1) {$msg = $count ." notícias movidas"; }
	$m->location("sec=news", $msg);
}


//-----------------------------------------------------------------------------
// Act copy
//-----------------------------------------------------------------------------
else if ($act == "copy") {
	$m->req_perms("editown|editall", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$cats = array(); foreach ($s->cat as $k => $v) {$cats[$k] = $v['name']; } asort($cats);
	$list = ""; foreach ($cats as $k => $v) {if (!$m->perms("editown|editall", $k) || $k == $cat) {continue; } if ($list) {$list .= "|"; } $list .= $k ."=". $v; } unset($cats);
	$selected = array(); foreach ($s->req['sel'] as $k => $v) {$selected['sel['. $k .']'] = $v; }
	$l->form("news", "copy_save", $selected); $l->table(505);
	
	$l->tb_group("Selecione a categoria de destino");
			$l->tb_select("c[cat]", "", $list);
	
	$l->tb_button("submit", "Copiar", array("accesskey" => "i"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=news"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act copy_save
//-----------------------------------------------------------------------------
else if ($act == "copy_save") {
	$m->req_perms("editown|editall", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	if (!$s->req['c']['cat'] || !$s->cat[$s->req['c']['cat']]) {$m->error_redir("idinvalid"); }
	$count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['news']); $count = 0;
		foreach ($s->req['sel'] as $k => $v) {
			if (!$m->perms("editall", $cat) && $db['data'][$k]['user'] != $s->usr['user']) {continue; }
			else if (!$m->perms("editown", $s->req['c']['cat']) && $db['data'][$k]['user'] == $s->usr['user']) {continue; }
			else if (!$m->perms("editall", $s->req['c']['cat']) && $db['data'][$k]['user'] != $s->usr['user']) {continue; }
			$nl = $db['data'][$k];
			$nl['id'] = substr(md5(rand()*time()), 0, 10);
			$nl['cid'] = $s->req['c']['cat'];
			$db['data'][count($db['data'])] = $nl;
			$count += 1;
		}
		$s->db_table_save($s->cfg['file']['news'], $db);
	}
	
	$msg = "Notícia copiada"; if ($count > 1) {$msg = $count ." notícias copiadas"; }
	$m->location("sec=news", $msg);
}


//-----------------------------------------------------------------------------
// Act show | hide
//-----------------------------------------------------------------------------
else if ($act == "show" || $act == "hide") {
	$m->req_perms("editqueue", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['news']);
		foreach ($s->req['sel'] as $k => $v) {
			if ($act == "show") {$db['data'][$k]['data']['q'] = 0; }
			else if ($act == "hide") {$db['data'][$k]['data']['q'] = 1; }
			$count += 1;
		}
		$s->db_table_save($s->cfg['file']['news'], $db);
	}
	
	if ($act == "show") {$msg = "Notícia exibida"; if ($count > 1) {$msg = $count ." notícias exibidas"; }}
	else if ($act == "hide") {$msg = "Notícia oculta"; if ($count > 1) {$msg = $count ." notícias ocultas"; }}
	$m->location("sec=news", $msg);
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	$m->req_perms("editown|editall", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); }
	$rcm = array(); $rp = array();
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['news']); $rem = 0;
		foreach ($s->req['sel'] as $k => $v) {
			if (!$m->perms("editall", $cat) && $db['data'][$k] != $s->usr['user']) {continue; }
			if (!$rp[$db['data'][$k]['user']]) {$rp[$db['data'][$k]['user']] = 0; } $rp[$db['data'][$k]['user']]++;
			if (!$rcm[$db['data'][$k]['id']]) {$rcm[$db['data'][$k]['id']] = 0; } $rcm[$db['data'][$k]['id']]++;
			unset($db['data'][$k]); $rem += 1;
		}
		$s->db_table_save($s->cfg['file']['news'], $db);
		
		$db = $s->db_table_open($s->cfg['file']['comments']);
		foreach ($db['data'] as $k => $v) {
			if ($rcm[$v['nid']] == 1) {unset($db['data'][$k]); }
		}
		$s->db_table_save($s->cfg['file']['comments'], $db);
	
		$db = $s->db_table_open($s->cfg['file']['users']);
		foreach ($db['data'] as $k => $v) {
			if ($rp[$v['user']]) {$db['data'][$k]['data']['posts'] -= $rp[$v['user']]; }
		}
		$s->db_table_save($s->cfg['file']['users'], $db);
	}
	
	$msg = "Notícia removida"; if ($rem > 1) {$msg = $rem ." notícias removidas"; }
	$m->location("sec=news", $msg);
}


?>
