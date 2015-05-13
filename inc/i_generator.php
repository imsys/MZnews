<?php $p['tit'] = "generator"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login();

$act = $s->req['act']; if (!$act) {$act = "index"; }

//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	$cats = array(); foreach ($s->cat as $k => $v) {$cats[$k] = $v['name']; } asort($cats);
	$list = ""; foreach ($cats as $k => $v) {if ($list) {$list .= "|"; } $list .= $k ."=". $v; }
	
	$l->form("generator", "step1", array(), "get"); $l->table(505);
	$l->tb_group("Escolha uma tarefa");
			$l->tb_select("task", "", "=Esquemas prontos (recomendados para usuários inexperientes)|proj_1=&nbsp;&nbsp;&nbsp;Mostrar esquema 1 (esquema pronto simples)||=Códigos brutos (apenas para usuários avançados)|show_headlines=&nbsp;&nbsp;&nbsp;Mostrar manchetes (headlines)|show_news=&nbsp;&nbsp;&nbsp;Mostrar notícias|show_onenews=&nbsp;&nbsp;&nbsp;Mostrar uma notícia específica|show_fnews=&nbsp;&nbsp;&nbsp;Mostrar notícia completa|show_print=&nbsp;&nbsp;&nbsp;Mostrar notícia para impressão|show_comments=&nbsp;&nbsp;&nbsp;Mostrar comentários de uma notícia|show_commentsform=&nbsp;&nbsp;&nbsp;Mostrar formulário para adicionar um comentário|show_mailform=&nbsp;&nbsp;&nbsp;Mostrar formulário para enviar a notícia por e-mail|show_archive=&nbsp;&nbsp;&nbsp;Mostrar arquivo|do_addcomment=&nbsp;&nbsp;&nbsp;Ação: Salvar comentário|do_sendmail=&nbsp;&nbsp;&nbsp;Ação: Enviar notícia por e-mail", "", 505);
		$l->tb_nextrow();
			$l->tb_select("show_cat", "<b>Categoria</b>", $list, "", 505);
	$l->tb_button("submit", "Próximo", array("accesskey" => "p"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act step1
//-----------------------------------------------------------------------------
else if ($act == "step1") {
	if (!$s->req['task'] || !$s->req['show_cat']) {$m->error_redir("nodata"); }
	if (preg_match("/^proj_/i", $s->req['task'])) {$m->location("sec=generator&act=step2&task=". $s->req['task'] ."&show_cat=". $s->req['show_cat']); }
	$l->form("generator", "step2", array("task" => $s->req['task'], "show_cat" => $s->req['show_cat']), "get"); $l->table(505);
	switch ($s->req['task']) {
		case "show_headlines":
			$l->tb_group("Mostrar manchetes (headlines)");
					$l->tb_input("text", "c[perpage]", "<b>Manchetes por página</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 exibe todas</span>", "", 505);
				$l->tb_nextrow();
					$l->tb_select("c[pagetpl]", "<b>Modelo de paginação</b>", "0=Não usar paginação|1=<< < 3 4 5 6 7 > >>|2=<< < 5 > >>|3=Páginas: (10) << Primeira ... 3 4 [5] 6 7 ... Última >>|4=Página 5 (menu drop-down)|5=Anterior &#124; Página 5 &#124; Próxima", "", 505);
			break;
		case "show_news":
			$l->tb_group("Mostrar notícias");
					$l->tb_input("text", "c[perpage]", "<b>Notícias por página</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 exibe todas</span>", "", 505);
				$l->tb_nextrow();
					$l->tb_select("c[pagetpl]", "<b>Modelo de paginação</b>", "0=Não usar paginação|1=<< < 3 4 5 6 7 > >>|2=<< < 5 > >>|3=Páginas: (10) << Primeira ... 3 4 [5] 6 7 ... Última >>|4=Página 5 (menu drop-down)|5=Anterior &#124; Página 5 &#124; Próxima", "", 505);
			break;
		case "show_onenews":
			$l->tb_group("Mostrar uma notícia específica");
					$l->tb_select("c[from]", "<b>Pegar ID da notícia ...</b>", "0=... do campo do formulário abaixo (query string)|1=... do ID digitado abaixo", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[id]", "<b>Campo do formulário ou ID da notícia</b>", "", 505);
			break;
		case "show_fnews":
			$l->tb_group("Mostrar notícia completa");
					$l->tb_select("c[from]", "<b>Pegar ID da notícia ...</b>", "0=... do campo do formulário abaixo (query string)|1=... do ID digitado abaixo", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[id]", "<b>Campo do formulário ou ID da notícia</b>", "", 505);
			break;
		case "show_print":
			$l->tb_group("Mostrar notícia para impressão");
					$l->tb_select("c[from]", "<b>Pegar ID da notícia ...</b>", "0=... do campo do formulário abaixo (query string)|1=... do ID digitado abaixo", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[id]", "<b>Campo do formulário ou ID da notícia</b>", "", 505);
			break;
		case "show_comments":
			$l->tb_group("Mostrar comentários da notícia");
					$l->tb_select("c[from]", "<b>Pegar ID da notícia ...</b>", "0=... do campo do formulário abaixo (query string)|1=... do ID digitado abaixo", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[id]", "<b>Campo do formulário ou ID da notícia</b>", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[perpage]", "<b>Comentários por página</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 exibe todos</span>", "", 505);
				$l->tb_nextrow();
					$l->tb_select("c[pagetpl]", "<b>Modelo de paginação</b>", "0=Não usar paginação|1=<< < 3 4 5 6 7 > >>|2=<< < 5 > >>|3=Páginas: (10) << Primeira ... 3 4 [5] 6 7 ... Última >>|4=Página 5 (menu drop-down)|5=Anterior &#124; Página 5 &#124; Próxima", "", 505);
			break;
		case "show_commentsform":
			$l->tb_group("Mostrar formulário para adicionar um comentário");
					$l->tb_select("c[from]", "<b>Pegar ID da notícia ...</b>", "0=... do campo do formulário abaixo (query string)|1=... do ID digitado abaixo", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[id]", "<b>Campo do formulário ou ID da notícia</b>", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[args]", "Argumentos extras da query string para salvar o comentário", "", 505);
			break;
		case "show_mailform":
			$l->tb_group("Mostrar formulário para enviar a notícia por e-mail");
					$l->tb_select("c[from]", "<b>Pegar ID da notícia ...</b>", "0=... do campo do formulário abaixo (query string)|1=... do ID digitado abaixo", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[id]", "<b>Campo do formulário ou ID da notícia</b>", "", 505);
				$l->tb_nextrow();
					$l->tb_input("text", "c[args]", "Argumentos extras da query string para enviar a notícia", "", 505);
			break;
		case "show_archive":
			$m->location("sec=generator&act=step2&task=". $s->req['task'] ."&show_cat=". $s->req['show_cat']);
			break;
		case "do_addcomment":
			$m->location("sec=generator&act=step2&task=". $s->req['task'] ."&show_cat=". $s->req['show_cat']);
			break;
		case "do_sendmail":
			$m->location("sec=generator&act=step2&task=". $s->req['task'] ."&show_cat=". $s->req['show_cat']);
			break;
	}
	$l->tb_button("submit", "Próximo", array("accesskey" => "p"));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act step2
//-----------------------------------------------------------------------------
else if ($act == "step2") {
	if (!$s->req['task'] || !$s->req['show_cat']) {$m->error_redir("nodata"); }
	$l->form("generator", "index", array(), "get"); $l->table(505);
		$l->tb_group("Instruções");
			$l->tb_custom("<div align=\"center\" class=\"important\"><br /><b>Programadores - Importante</b><br />Tenha certeza de não usar as variáveis \$s, \$m e \$mzn2 na página que você colocar o MZn².<br />Se você usá-las, o seu sistema, o MZn² ou ambos poderão não funcionar, e você não pode alterá-las.<br /><br />Também evite usar as variáveis de formulário (query string) 'mzn', 'mzn_pg', 'mzn_data', 'mzn_usuario', 'mzn_busca', 'mostrar' e 'acao'. Se precisar usá-las, altere o código abaixo e os modelos das notícias.<br /><br /></div>");
		$l->tb_nextrow();
			$l->tb_custom("<b>Se você tiver dificuldade</b><br />Antes de tudo, tente. Siga as instruções ao pé da letra e veja se você mesmo consegue fazer o<br />que quer, não é tão difícil. Caso você tenha dificuldades, você pode consultar a ajuda online ou um atendimento personalizado de suporte no <a href=\"http://www.mznews.kit.net\" target=\"_blank\"><u>site do MZn²</u></a>.");
		$l->tb_nextrow();
			$l->tb_custom("<b>Passo 1</b><br />Abra a página onde deverão ficar as suas notícias no Bloco de Notas ou em qualquer editor de texto.");
		$l->tb_nextrow();
			$l->tb_custom("<b>Passo 2</b><br />No início da sua página (aperte Ctrl+Home), coloque o código a seguir.<br />Você só precisa colocar ele uma vez.");
		$l->tb_nextrow();
			$l->tb_text("", "", "<"."?php\n\n\$mzn_path = \"". str_replace("\\", "\\\\\\\\", $AbsPath) ."\"; require_once(\$mzn_path .\"/mzn2.php\"); \$mzn_selfpage = \$s->req['PHP_SELF'];\n\$mzn2 = new MZn2_Noticias;\n\n?".">", 505, array("wrap" => "off", "rows" => "6", "class" => "code"));
		$l->tb_nextrow();
			$l->tb_custom("<b>Passo 3</b><br />Procure onde deverá aparecer o que você selecionou (geralmente o mesmo lugar onde aparece o conteúdo do seu site), e coloque o código abaixo:");
	switch ($s->req['task']) {
		case "show_headlines":
			$m->req('c|perpage', 'c|pagetpl');
			if (!preg_match("/^[0-9]+$/", $s->req['c']['pagetpl']) || !preg_match("/^[0-9]+$/", $s->req['c']['perpage'])) {$m->error_redir("onlynumbers"); }
			$code = "\$mzn2->data = \$s->req['mzn_data'];\n\$mzn2->usuario = \$s->req['mzn_usuario'];\n\$mzn2->busca = \$s->req['mzn_busca'];\n\$mzn2->pagina = \$s->req['mzn_pg'];\n\n\$mzn2->porpagina = ". $s->req['c']['perpage'] .";\n\$mzn2->mostrar_manchetes();";
			if ($s->req['c']['pagetpl']) {$code .= "\n\n\$mzn2->mostrar_paginacao(\$mzn_selfpage .\"?mzn_pg={pagina}\", ". $s->req['c']['pagetpl'] .");"; }
			break;
		case "show_news":
			$m->req('c|perpage', 'c|pagetpl');
			if (!preg_match("/^[0-9]+$/", $s->req['c']['pagetpl']) || !preg_match("/^[0-9]+$/", $s->req['c']['perpage'])) {$m->error_redir("onlynumbers"); }
			$code = "\$mzn2->data = \$s->req['mzn_data'];\n\$mzn2->usuario = \$s->req['mzn_usuario'];\n\$mzn2->busca = \$s->req['mzn_busca'];\n\$mzn2->pagina = \$s->req['mzn_pg'];\n\n\$mzn2->porpagina = ". $s->escape($s->req['c']['perpage']) .";\n\$mzn2->mostrar_noticias();";
			if ($s->req['c']['pagetpl']) {$code .= "\n\n\$mzn2->mostrar_paginacao(\$mzn_selfpage .\"?mzn_pg={pagina}\", ". $s->req['c']['pagetpl'] .");"; }
			break;
		case "show_onenews":
			$m->req('c|from', 'c|id');
			if ($s->req['c']['from'] == "0") {$s->req['c']['id'] = "\$s->req['". $s->escape($s->req['c']['id'], "'") ."']"; }
			else if ($s->req['c']['from'] == "1") {$s->req['c']['id'] = "\"". $s->escape($s->req['c']['id']) ."\""; }
			$code = "\$mzn2->noticia = ". $s->req['c']['id'] .";\n\$mzn2->mostrar_noticia();";
			break;
		case "show_fnews":
			$m->req('c|from', 'c|id');
			if ($s->req['c']['from'] == "0") {$s->req['c']['id'] = "\$s->req['". $s->escape($s->req['c']['id'], "'") ."']"; }
			else if ($s->req['c']['from'] == "1") {$s->req['c']['id'] = "\"". $s->escape($s->req['c']['id']) ."\""; }
			$code = "\$mzn2->noticia = ". $s->req['c']['id'] .";\n\$mzn2->mostrar_noticia_completa();";
			break;
		case "show_print":
			$m->req('c|from', 'c|id');
			if ($s->req['c']['from'] == "0") {$s->req['c']['id'] = "\$s->req['". $s->escape($s->req['c']['id'], "'") ."']"; }
			else if ($s->req['c']['from'] == "1") {$s->req['c']['id'] = "\"". $s->escape($s->req['c']['id']) ."\""; }
			$code = "\$mzn2->noticia = ". $s->req['c']['id'] .";\n\$mzn2->mostrar_noticia_para_impressao();";
			break;
		case "show_comments":
			$m->req('c|from', 'c|id', 'c|perpage', 'c|pagetpl');
			if (!preg_match("/^[0-9]+$/", $s->req['c']['pagetpl']) || !preg_match("/^[0-9]+$/", $s->req['c']['perpage'])) {$m->error_redir("onlynumbers"); }
			if ($s->req['c']['from'] == "0") {$s->req['c']['id'] = "\$s->req['". $s->escape($s->req['c']['id'], "'") ."']"; }
			else if ($s->req['c']['from'] == "1") {$s->req['c']['id'] = "\"". $s->escape($s->req['c']['id']) ."\""; }
			$code = "\$mzn2->pagina = \$s->req['mzn_pg'];\n\n\$mzn2->porpagina = ". $s->req['c']['perpage'] .";\n\$mzn2->noticia = ". $s->req['c']['id'] .";\n\$mzn2->mostrar_comentarios();";
			if ($s->req['c']['pagetpl']) {$code .= "\n\n\$mzn2->mostrar_paginacao(\$mzn_selfpage .\"?mzn_pg={pagina}\", ". $s->req['c']['pagetpl'] .");"; }
			break;
		case "show_commentsform":
			$m->req('c|from', 'c|id');
			if ($s->req['c']['from'] == "0") {$s->req['c']['id'] = "\$s->req['". $s->escape($s->req['c']['id'], "'") ."']"; }
			else if ($s->req['c']['from'] == "1") {$s->req['c']['id'] = "\"". $s->escape($s->req['c']['id']) ."\""; }
			if ($s->req['c']['args']) {$s->req['c']['args'] = "\"". $s->escape($s->req['c']['args']) ."\""; }
			$code = "\$mzn2->noticia = ". $s->req['c']['id'] .";\n\$mzn2->mostrar_formulario_comentario(". $s->req['c']['args'] .");";
			break;
		case "show_mailform":
			$m->req('c|from', 'c|id');
			if ($s->req['c']['from'] == "0") {$s->req['c']['id'] = "\$s->req['". $s->escape($s->req['c']['id'], "'") ."']"; }
			else if ($s->req['c']['from'] == "1") {$s->req['c']['id'] = "\"". $s->escape($s->req['c']['id']) ."\""; }
			if ($s->req['c']['args']) {$s->req['c']['args'] = "\"". $s->escape($s->req['c']['args']) ."\""; }
			$code = "\$mzn2->noticia = ". $s->req['c']['id'] .";\n\$mzn2->mostrar_formulario_email(". $s->req['c']['args'] .");";
			break;
		case "show_archive":
			$code = "\$mzn2->mostrar_arquivo(\$mzn_selfpage .\"?mzn_data={data}\");";
			break;
		case "do_addcomment":
			$code = "\$mzn2->adicionar_comentario();";
			break;
		case "do_sendmail":
			$code = "\$mzn2->enviar_email();";
			break;
		case "proj_1":
			$code = base64_decode("JG1vc3RyYXIgPSAkcy0+cmVxWydtb3N0cmFyJ107DQoNCmlmICghJG1vc3RyYXIpIHsNCgkkbXpuMi0+ZGF0YSA9ICRzLT5yZXFbJ216bl9kYXRhJ107DQoJJG16bjItPnVzdWFyaW8gPSAkcy0+cmVxWydtem5fdXN1YXJpbyddOw0KCSRtem4yLT5idXNjYSA9ICRzLT5yZXFbJ216bl9idXNjYSddOw0KCQ0KCSRtem4yLT5wYWdpbmEgPSAoJHMtPnJlcVsnbXpuX3BnJ10gIT0gMSk/ICgkcy0+cmVxWydtem5fcGcnXSAqIDIpIDogMTsNCgkkbXpuMi0+cG9ycGFnaW5hID0gNTsNCgllY2hvICI8ZGl2IGFsaWduPVwiY2VudGVyXCI+PGRpdiBzdHlsZT1cImZvbnQtZmFtaWx5OlRhaG9tYSwgVmVyZGFuYSwgQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZjsgZm9udC1zaXplOjhwdDsgd2lkdGg6NDUwcHg7IFwiPlxuPGRpdiBzdHlsZT1cInRleHQtYWxpZ246bGVmdDsgbWFyZ2luLWJvdHRvbToycHg7IHBhZGRpbmc6MnB4OyBwYWRkaW5nLWxlZnQ6NHB4OyBjb2xvcjojRkZGRkZGOyBiYWNrZ3JvdW5kLWNvbG9yOiM4MDgwODA7IFwiPjxiPkhlYWRsaW5lczwvYj48L2Rpdj5cbjwvZGl2PjwvZGl2PiI7DQoJZWNobyAiPGRpdiBhbGlnbj1cImNlbnRlclwiPjxkaXYgc3R5bGU9XCJmb250LWZhbWlseTpUYWhvbWEsIFZlcmRhbmEsIEFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWY7IGZvbnQtc2l6ZToxMHB0OyB3aWR0aDo0NTBweDsgXCI+XG48ZGl2IHN0eWxlPVwidGV4dC1hbGlnbjpsZWZ0OyBcIj4iOw0KCSRtem4yLT5tb3N0cmFyX21hbmNoZXRlcygpOw0KCWVjaG8gIjwvZGl2PjwvZGl2PiI7DQoJDQoJZWNobyAiPGJyIC8+IjsNCgkNCgkkbXpuMi0+cGFnaW5hID0gJHMtPnJlcVsnbXpuX3BnJ107DQoJJG16bjItPnBvcnBhZ2luYSA9IDEwOw0KCSRtem4yLT5tb3N0cmFyX25vdGljaWFzKCk7DQoJDQoJZWNobyAiPGRpdiBhbGlnbj1cImNlbnRlclwiPiI7DQoJJG16bjItPm1vc3RyYXJfcGFnaW5hY2FvKCRtem5fc2VsZnBhZ2UgLiI/bXpuX3BnPXtwYWdpbmF9IiwgMSk7DQoJZWNobyI8YSBocmVmPVwiIi4gJG16bl9zZWxmcGFnZSAuIj9tb3N0cmFyPWFycXVpdm9cIj5Nb3N0cmFyIGFycXVpdm88L2E+PC9kaXY+IjsNCn0NCmVsc2UgaWYgKCRtb3N0cmFyID09ICJub3RpY2lhIikgew0KCSRtem4yLT5ub3RpY2lhID0gJHMtPnJlcVsnaWQnXTsNCgkkbXpuMi0+bW9zdHJhcl9ub3RpY2lhKCk7DQoJDQoJZWNobyAiPGRpdiBhbGlnbj1cImNlbnRlclwiPjxhIGhyZWY9XCIjXCIgb25jbGljaz1cImhpc3RvcnkuYmFjaygpOyByZXR1cm4gZmFsc2U7IFwiPlZvbHRhcjwvYT48L2Rpdj4iOw0KfQ0KZWxzZSBpZiAoJG1vc3RyYXIgPT0gIm5vdGljaWFjb21wbGV0YSIpIHsNCgkkbXpuMi0+bm90aWNpYSA9ICRzLT5yZXFbJ2lkJ107DQoJJG16bjItPm1vc3RyYXJfbm90aWNpYV9jb21wbGV0YSgpOw0KCQ0KCWVjaG8gIjxkaXYgYWxpZ249XCJjZW50ZXJcIj48YSBocmVmPVwiI1wiIG9uY2xpY2s9XCJoaXN0b3J5LmJhY2soKTsgcmV0dXJuIGZhbHNlOyBcIj5Wb2x0YXI8L2E+PC9kaXY+IjsNCn0NCmVsc2UgaWYgKCRtb3N0cmFyID09ICJjb21lbnRhcmlvcyIpIHsNCgkkYWNhbyA9ICRzLT5yZXFbJ2FjYW8nXTsNCglpZiAoISRhY2FvKSB7DQoJCWVjaG8gIjxkaXYgYWxpZ249XCJjZW50ZXJcIj4iOw0KCQkkbXpuMi0+cG9ycGFnaW5hID0gMDsNCgkJJG16bjItPm5vdGljaWEgPSAkcy0+cmVxWydpZCddOw0KCQkkbXpuMi0+bW9zdHJhcl9ub3RpY2lhKCk7DQoJCQ0KCQllY2hvICI8ZGl2IGFsaWduPVwiY2VudGVyXCI+PGRpdiBzdHlsZT1cImZvbnQtZmFtaWx5OlRhaG9tYSwgVmVyZGFuYSwgQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZjsgZm9udC1zaXplOjhwdDsgd2lkdGg6NDUwcHg7IFwiPlxuXHQ8ZGl2IHN0eWxlPVwidGV4dC1hbGlnbjpsZWZ0OyBtYXJnaW4tYm90dG9tOjJweDsgcGFkZGluZzoycHg7IHBhZGRpbmctbGVmdDo0cHg7IGNvbG9yOiNGRkZGRkY7IGJhY2tncm91bmQtY29sb3I6IzgwODA4MDsgXCI+PGI+Q29tZW504XJpb3M8L2I+PC9kaXY+XG48L2Rpdj48L2Rpdj4iOw0KCQkkbXpuMi0+bW9zdHJhcl9jb21lbnRhcmlvcygpOw0KCQkNCgkJZWNobyAiPGRpdiBhbGlnbj1cImNlbnRlclwiPjxkaXYgc3R5bGU9XCJmb250LWZhbWlseTpUYWhvbWEsIFZlcmRhbmEsIEFyaWFsLCBIZWx2ZXRpY2EsIHNhbnMtc2VyaWY7IGZvbnQtc2l6ZTo4cHQ7IHdpZHRoOjQ1MHB4OyBcIj5cblx0PGRpdiBzdHlsZT1cInRleHQtYWxpZ246bGVmdDsgbWFyZ2luLWJvdHRvbToycHg7IHBhZGRpbmc6MnB4OyBwYWRkaW5nLWxlZnQ6NHB4OyBjb2xvcjojRkZGRkZGOyBiYWNrZ3JvdW5kLWNvbG9yOiM4MDgwODA7IFwiPjxiPkFkaWNpb25lIG8gc2V1ITwvYj48L2Rpdj5cbjwvZGl2PjwvZGl2PiI7DQoJCSRtem4yLT5tb3N0cmFyX2Zvcm11bGFyaW9fY29tZW50YXJpbygibW9zdHJhcj1jb21lbnRhcmlvcyZhY2FvPWFkaWNpb25hciIpOw0KCQkNCgkJZWNobyAiPGRpdiBhbGlnbj1cImNlbnRlclwiPjxhIGhyZWY9XCIjXCIgb25jbGljaz1cImhpc3RvcnkuYmFjaygpOyByZXR1cm4gZmFsc2U7IFwiPlZvbHRhcjwvYT48L2Rpdj4iOw0KCQllY2hvICI8L2Rpdj4iOw0KCX0NCgllbHNlIGlmICgkYWNhbyA9PSAiYWRpY2lvbmFyIikgew0KCQkkbXpuMi0+YWRpY2lvbmFyX2NvbWVudGFyaW8oKTsNCgl9DQp9DQplbHNlIGlmICgkbW9zdHJhciA9PSAiYXJxdWl2byIpIHsNCgllY2hvICI8ZGl2IGFsaWduPVwiY2VudGVyXCI+PGRpdiBzdHlsZT1cImZvbnQtZmFtaWx5OlRhaG9tYSwgVmVyZGFuYSwgQXJpYWwsIEhlbHZldGljYSwgc2Fucy1zZXJpZjsgZm9udC1zaXplOjEwcHQ7IHdpZHRoOjQ1MHB4OyB0ZXh0LWFsaWduOmxlZnQ7IFwiPlxuXHQ8ZGl2IHN0eWxlPVwiZm9udC1zaXplOjhwdDsgdGV4dC1hbGlnbjpsZWZ0OyBtYXJnaW4tYm90dG9tOjJweDsgcGFkZGluZzoycHg7IHBhZGRpbmctbGVmdDo0cHg7IGNvbG9yOiNGRkZGRkY7IGJhY2tncm91bmQtY29sb3I6IzgwODA4MDsgXCI+PGI+QXJxdWl2bzwvYj48L2Rpdj4iOw0KCSRtem4yLT5tb3N0cmFyX2FycXVpdm8oJG16bl9zZWxmcGFnZSAuIj9tem5fZGF0YT17ZGF0YX0iKTsNCgllY2hvICI8L2Rpdj48L2Rpdj4iOw0KfQ==");
			break;
	}
		$code = "<"."?php\n\n\$mzn2->categoria = \"". $s->req['show_cat'] ."\";\n". $code ."\n\n?".">";
		$rows = count(explode("\n", $code));
		if ($rows > 20) {$rows = 20; }
		$l->tb_nextrow();
			$l->tb_text("", "", $code, 505, array("wrap" => "off", "rows" => $rows, "class" => "code"));
		$l->tb_nextrow();
			$l->tb_custom("<b>Passo 4</b><br />Agora, vá em Arquivo (ou File) &gt; Salvar Como (ou Save As), e digite, ENTRE ASPAS o nome da página com a extensão .php, assim a extensão não será .php.txt, apenas .php. Observe que, se a página que você salvou for incluida em um PHP, a extensão não é importante, pois o PHP já executa os códigos automaticamente.<br />&nbsp;&nbsp;Exemplo: \"noticias.php\"");
		$l->tb_nextrow();
			$l->tb_custom("<b>Passo 5</b><br />Envie a nova página para o seu site e pronto, o arquivo deve funcionar corretamente.");
	$l->table_end(); $l->form_end();
}



?>
