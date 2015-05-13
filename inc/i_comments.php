<?php $p['tit'] = "news"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("comments", $cat);

$act = $s->req['act']; if (!$act) {$act = "index"; }

$db = $s->db_table_open($s->cfg['file']['news']); $ok = 0;
foreach ($db['data'] as $k => $v) {
	if ($v['id'] == $s->req['nid']) {$ok = 1; }
}
if (!$ok) {$m->error_redir("idinvalid"); }
$dbC = $s->db_table_open($s->cfg['file']['comments']);
foreach ($dbC['data'] as $k => $v) {
	if ($v['nid'] != $s->req['nid']) {unset($dbC['data'][$k]); }
}


//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	if (!$s->req['pg']) {$s->req['pg'] = 1; }
	
	$actions = array("remove" => "remover"); if ($m->perms("editqueue", $cat)) {$actions = array("show" => "exibir", "hide" => "ocultar", "remove" => "remover"); }
	
	$l->form("comments", "remove", array("nid" => $s->req['nid']));
	
	$l->list_header("sel:id", "370:Comentário", "140:IP", "100:Em");
	foreach ($dbC['data'] as $k => $v) {
		if ($v['cid'] != $cat) {continue; }
		
		$queue = ""; if ($v['data']['q']) {$queue = "<img src=\"img/{skin}/news_hidden.gif\" align=\"absmiddle\" border=\"0\" alt=\"Este comentário não está sendo exibido pois está na fila de moderação\" />"; }
		
		$comment = "";
		if ($v['title']) {$comment .= $v['title'] ."|<a href=\"index.php?s={session}&amp;sec=comments&amp;nid=". $s->req['nid'] ."&amp;act=edit&amp;id=". $v['id'] ."\"><b>%s</b>". $queue ."</a>"; }
		else {$comment .= "sem título|<a href=\"index.php?s={session}&amp;sec=comments&amp;nid=". $s->req['nid'] ."&amp;act=edit&amp;id=". $v['id'] ."\"><b>(sem título)</b>". $queue ."</a>"; }
		$comment .= "<br />Por: ". $v['data']['n'];
		if ($v['data']['m']) {$comment .= "&nbsp;&middot;&nbsp;<a href=\"mailto:". $v['data']['m'] ."\">". $v['data']['m'] ."</a>"; }
		
		$link = "|<a href=\"index.php?s={session}&amp;sec=comments&amp;nid=". $s->req['nid'] ."&amp;act=edit&amp;id=". $v['id'] ."\">%s</a>";
		$dateLink = "|<a href=\"index.php?s={session}&amp;sec=comments&amp;nid=". $s->req['nid'] ."&amp;act=edit&amp;id=". $v['id'] ."\">". $m->parse_date($v['time'], "<br />") ."</a>";
		
		$l->list_item($v['id'], $comment, $v['data']['i'] . $link, $v['time'] . $dateLink, array("colclass" => "small"));
	}
	if (!$s->req['sort']) {$s->req['sort'] = "3:1"; }
	
	$l->page_link = "index.php?s={session}&amp;sec=comments&amp;nid=". $s->req['nid'] ."&amp;sort={sort}&amp;pg={pg}&amp;query={query}";
	$l->list_pg = $s->req['pg'];
	$l->list_perpage = $s->sys['edit']['perpage'];
	$l->list_sort = $s->req['sort'];
	$l->list_query = $s->req['query'];
	
	$l->list_sort(); $l->list_filter();
	
	$l->list_build($s->skin['dir'] ."/order_asc.gif", $s->skin['dir'] ."/order_desc.gif", null);
	$l->list_footer(array("<b>Voltar às notícias</b>" => "index.php?s={session}&amp;sec=news"), 1, $actions, 1, 616);
	$l->form_end();
}


//-----------------------------------------------------------------------------
// Act edit
//-----------------------------------------------------------------------------
else if ($act == "edit") {
	if (!$s->req['id']) {$m->error_redir("idinvalid"); }
	$ok = 0; $val = array();
	foreach ($dbC['data'] as $k => $v) {
		if ($v['cid'] != $cat || $v['id'] != $s->req['id']) {continue; }
		$ok = 1;
		$val = $v;
	}
	if (!$ok) {$m->error_redir("idinvalid"); }
	
	$l->form("comments", "edit_save", array("nid" => $s->req['nid'], "id" => $s->req['id'], "c[comment]" => $val['comment'])); $l->table(505);
	$l->tb_group("Dados");
			$l->tb_input("text", "c[data][n]", "<b>Nome do usuário</b>", $val['data']['n'], 505);
		$l->tb_nextrow();
			$x = "E-mail do usuário"; if ($s->cat[$cat]['comments']['req_mail']) {$x = "<b>". $x ."</b>"; }
			$l->tb_input("text", "c[data][m]", $x, $val['data']['m'], 505);
		$l->tb_nextrow();
			$x = "Título"; if ($s->cat[$cat]['comments']['req_title']) {$x = "<b>". $x ."</b>"; }
			$tit_extra = array(); if ($s->cat[$cat]['comments']['limit_title'] > 0) {$tit_extra['maxlength'] = $s->cat[$cat]['comments']['limit_title']; }
			$l->tb_input("text", "c[title]", $x, $val['title'], 505, $tit_extra);
			$count = 0;
			if ($s->cat[$cat]['comments']['field1']) {$count++; } if ($s->cat[$cat]['comments']['field2']) {$count++; }
			if ($count > 0) {
				$l->tb_nextrow();
				$width = intval((505 / $count) - (($count-1) * 5));
				if ($s->cat[$cat]['comments']['field1']) {$l->tb_input("text", "c[data][f1]", $s->cat[$cat]['comments']['field1'], $val['data']['f1'], $width); }
				if ($s->cat[$cat]['comments']['field2']) {$l->tb_input("text", "c[data][f2]", $s->cat[$cat]['comments']['field2'], $val['data']['f2'], $width); }
			}
	$l->tb_group("Comentário");
			$l->tb_custom("<iframe src=\"wait.php?sleep=1&g=". urlencode("index.php?s=". $s->req['s'] ."&sec=edv&only_mznCode=1&obj=comment&comment_id=". $val['id']) ."\" tabindex=\"". $l->tabindex ."\" name=\"edv_news\" scrolling=\"no\" width=\"505\" height=\"290\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"no\"></iframe>", 505); $l->tabindex++;
	$l->tb_group("Data");
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
	if ($m->perms("editqueue", $cat)) {
		$l->tb_group("Opções");
				$l->tb_check("checkbox", "c[data][q]", "", "1=Está na fila de moderação (comentário oculto)", $val['data']['q'], 505);
	}
	
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => "sec=comments&nid=". $s->req['nid']));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act edit_save
//-----------------------------------------------------------------------------
else if ($act == "edit_save") {
	if (!$s->req['id']) {$m->error_redir("idinvalid"); }
	
	$m->req('c|data|n', 'c|comment');
	if ($s->cat[$cat]['comments']['req_mail']) {$m->req('c|data|m'); }
	if ($s->cat[$cat]['comments']['req_title']) {$m->req('c|title'); }
	if (!$m->perms("editqueue", $cat)) {unset($s->req['c']['data']['q']); } else {$m->req_sync('c|data|q'); }
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['comments']); $ok = 0;
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || $v['id'] != $s->req['id']) {continue; } $ok = 1;
			$nl = $v;
			if ($s->req['c']['date']['day'] != "" && $s->req['c']['date']['month'] != "" && $s->req['c']['date']['year'] != "" && $s->req['c']['date']['hour'] != "" && $s->req['c']['date']['minute'] != "" && $s->req['c']['date']['second'] != "") {$nl['time'] = mktime($s->req['c']['date']['hour'], $s->req['c']['date']['minute'], $s->req['c']['date']['second'], $s->req['c']['date']['month'], $s->req['c']['date']['day'], $s->req['c']['date']['year']); }
			$nl['title'] = $s->req['c']['title']; if ($s->cat[$cat]['comments']['limit_title'] > 0) {$nl['title'] = substr($nl['title'], 0, $s->cat[$cat]['comments']['limit_title']); }
			$nl['comment'] = $s->req['c']['comment']; if ($s->cat[$cat]['comments']['limit_comment'] > 0) {$nl['comment'] = substr($nl['comment'], 0, $s->cat[$cat]['comments']['limit_comment']); }
			$nl['data'] = $m->array_sync($v['data'], $s->req['c']['data']);
			$db['data'][$k] = $nl;
		}
		if (!$ok) {$m->error_redir("idinvalid"); }
		
		$s->db_table_save($s->cfg['file']['comments'], $db);
	}
	
	$m->location("sec=comments&nid=". $s->req['nid'], "Comentário alterado");
}


//-----------------------------------------------------------------------------
// Act show | hide
//-----------------------------------------------------------------------------
else if ($act == "show" || $act == "hide") {
	$m->req_perms("editqueue", $cat);
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); } $count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['comments']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || !$s->req['sel'][$v['id']]) {continue; }
			if ($act == "show") {$db['data'][$k]['data']['q'] = 0; }
			else if ($act == "hide") {$db['data'][$k]['data']['q'] = 1; }
			$count += 1;
		}
		$s->db_table_save($s->cfg['file']['comments'], $db);
	}
	
	if ($act == "show") {$msg = "Comentário exibido"; if ($count > 1) {$msg = $count ." comentários exibidos"; }}
	else if ($act == "hide") {$msg = "Comentário oculto"; if ($count > 1) {$msg = $count ." comentários ocultos"; }}
	$m->location("sec=comments&nid=". $s->req['nid'], $msg);
}


//-----------------------------------------------------------------------------
// Act remove
//-----------------------------------------------------------------------------
else if ($act == "remove") {
	if (count($s->req['sel']) < 1) {$m->error_redir("nosel"); } $count = 0;
	
	if (!$s->cfg['ver']['demo']) {
		$db = $s->db_table_open($s->cfg['file']['comments']);
		foreach ($db['data'] as $k => $v) {
			if ($v['cid'] != $cat || !$s->req['sel'][$v['id']]) {continue; }
			unset($db['data'][$k]);
			$count += 1;
		}
		$s->db_table_save($s->cfg['file']['comments'], $db);
	}
	
	$msg = "Comentário removido"; if ($count > 1) {$msg = $count ." comentários removidos"; }
	$m->location("sec=comments&nid=". $s->req['nid'], $msg);
}


?>
