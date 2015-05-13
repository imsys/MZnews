<?php $p['tit'] = "config"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }
$m->req_login(); $m->req_perms("config");

$act = $s->req['act']; if (!$act) {$act = "index"; }

//-----------------------------------------------------------------------------
// Act index
//-----------------------------------------------------------------------------
if ($act == "index") {
	$skinList = "";
	$dir = @opendir($s->cfg['path']['img']);
	if ($dir) {
		while (($file = @readdir($dir)) !== false) {
			if (preg_match("/^\./", $file) || !@is_dir($s->cfg['path']['img'] ."/". $file) || !@file_exists($s->cfg['path']['img'] ."/". $file ."/skin.txt")) {continue; }
			$db = $m->db_old_open($s->cfg['path']['img'] ."/". $file ."/skin.txt");
			if (version_compare($db['skin']['version'], "2.0", "<")) {continue; }
			if ($skinList) {$skinList .= "|"; }
			$skinList .= $file ."=". $db['skin']['name'];
		}
		@closedir($dir);
	}
	
	$time_adjust_mode = "-"; if ($s->sys['time']['adjust'] >= 0) {$time_adjust_mode = "+"; }
	$time_adjust = abs($s->sys['time']['adjust']);
	
	$l->form("config", "save"); $l->table(505);
	$l->tb_group("Dados do seu site");
			$l->tb_input("text", "c:site:name", "<b>Nome do site</b>", $s->sys['site']['name'], 505);
		$l->tb_nextrow();
			$l->tb_input("text", "c:site:url", "<b>URL do site</b>", $s->sys['site']['url'], 505);
	$l->tb_group("Preferências gerais do sistema");
			$l->tb_select("c:skin", "<b>Skin</b>", $skinList, $s->sys['skin'], 150);
			$x = "<b>Ajuste no relógio</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">horário atual <b>com ajuste</b> ". date("d-m-Y H:i:s", $s->cfg['time']) ."</span><br /><select name=\"c:time:adjust:mode\" tabindex=\"". $l->tabindex ."\" class=\"normal\" onchange=\"form_changed = 1; \">";
			if ($time_adjust_mode == "-") {$x .= "<option value=\"+\">adicionar (avançar)</option><option value=\"-\" selected>retirar (retroceder)</option>"; }
			else {$x .= "<option value=\"+\" selected>adicionar (avançar)</option><option value=\"-\">retirar (retroceder)</option>"; }
			$x .= "</select>&nbsp;<input type=\"text\" name=\"c:time:adjust\" id=\"form_c:time:adjust\" value=\"". $time_adjust ."\" tabindex=\"". ($l->tabindex + 1) ."\" class=\"normal\" style=\"width:80px; \" onchange=\"form_changed = 1; \" />&nbsp;minutos";
			$l->tb_custom($x, 350); $l->tabindex += 2;
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c:edv", "<b>Editor visual mznCode</b>", "1=Ativar o Editor Visual de mznCode nas notícias", $s->sys['edv'], 250);
			$l->tb_check("checkbox", "c:lostpwd", "<b>Recuperação de senha</b>", "1=Ativar o envio de uma nova senha por e-mail", $s->sys['lostpwd'], 250);
	$l->tb_group("Fila de moderação");
			$l->tb_check("checkbox", "c:queue:popup", "", "1=Exibir um alerta ao entrar no MZn² se houver itens na fila", $s->sys['queue']['popup'], 505);
	$l->tb_group("Manutenção de notícias, modelos, etc.");
			$l->tb_input("text", "c:edit:perpage", "<b>Itens por página</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">0 exibe todos os itens</span>", $s->sys['edit']['perpage'], 505, array("style" => "width:80px; ", "_after" => "&nbsp;itens"));
	$l->tb_group("Envio de arquivos");
			$l->tb_input("text", "c:upload:maxsize", "<b>Tamanho máximo</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">exemplo: 300 KB</span>", $s->to_bytes($s->sys['upload']['maxsize']), 200);
			$l->tb_input("text", "c:upload:extensions", "<b>Extensões permitidas</b>&nbsp;&nbsp;&nbsp;<span class=\"hint\">sem . e separadas por ,</span>", $s->sys['upload']['extensions'], 300);
	$l->tb_group("Campos personalizados dos usuários");
			$l->tb_input("text", "c:cfield:field1", "Campo 1", $s->sys['cfield']['field1'], 165);
			$l->tb_input("text", "c:cfield:field2", "Campo 2", $s->sys['cfield']['field2'], 165);
			$l->tb_input("text", "c:cfield:field3", "Campo 3", $s->sys['cfield']['field3'], 165);
	$l->tb_group("Filtro de palavras");
			$l->tb_custom("<span class=\"hint\">Este filtro substitui palavras de acordo com as suas regras. Digite apenas uma palavra por linha, sem espaços. Você pode usar os curingas ? e *, o curinga ? procura um caractere qualquer, já o * procura qualquer sequência de caracteres. O filtro é case-insensitive, ou seja, ABC é igual à abc e acentos também são desconsiderados. Se você não especificar uma substituição, a palavra será apenas removida.</span>");
		$l->tb_nextrow();
			$l->tb_custom("<span class=\"hint\">Exemplo - substituir palavras que começam em \"merd\" por \"CENSURADO\":<br />merd*=CENSURADO</span>");
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c:filter:news", "", "1=Filtrar notícias", $s->sys['filter']['news'], 250);
			$l->tb_check("checkbox", "c:filter:comments", "", "1=Filtrar comentários", $s->sys['filter']['comments'], 250);
		$l->tb_nextrow();
			$l->tb_text("c:filter:list", "Lista de palavras", $s->sys['filter']['list'], 505, array("wrap" => "off", "style" => "width:505px; height:50px; "));
	$l->tb_group("Visitantes (acesso aos comentários)");
			$l->tb_input("text", "c:visitor:floodint", "Intervalo entre posts permitidos", $s->sys['visitor']['floodint'], 160, array("style" => "width:80px; ", "_after" => "&nbsp;segundos"));
			$l->tb_input("text", "c:visitor:blockip", "IPs Bloqueados&nbsp;&nbsp;&nbsp;<span class=\"hint\">separados por , e podem conter o curinga *</span>", $s->sys['visitor']['blockip'], 340);
		$l->tb_nextrow();
			$l->tb_check("checkbox", "c:visitor:lock", "", "1=Exigir senha quando alguém usar um nome que já está em uso por um usuário registrado", $s->sys['visitor']['lock'], 505);
		$l->tb_nextrow();
			$l->tb_text("c:visitor:lock_custom", "Lista de nomes protegidos&nbsp;&nbsp;&nbsp;<span class=\"hint\">digite um nome por linha, os critérios são os mesmos do filtro de palavras</span>", $s->sys['visitor']['lock_custom'], 505, array("wrap" => "off", "style" => "width:505px; height:50px; "));
	$l->tb_caption("Preencha todos os campos em <b>negrito</b>");
	$l->tb_button("submit", "Salvar", array("accesskey" => "s"));
	$l->tb_button("cancel", "Cancelar", array("_go" => ""));
	$l->table_end(); $l->form_end();
}


//-----------------------------------------------------------------------------
// Act settings_save
//-----------------------------------------------------------------------------
else if ($act == "save") {
	$m->req('c:site:name', 'c:site:url', 'c:skin', 'c:time:adjust', 'c:edit:perpage', 'c:upload:maxsize', 'c:upload:extensions');
	$m->req_sync('c:edv', 'c:smilies', 'c:lostpwd', 'c:queue:popup', 'c:edit:nowrap', 'c:filter:news', 'c:filter:comments', 'c:visitor:lock');
	$s->cfg['block:config'] = 1;
	
	if (!$s->cfg['ver']['demo']) {
		if ($s->req['c:time:adjust:mode'] == "-") {$s->req['c:time:adjust'] = - $s->req['c:time:adjust']; }; unset($s->req['c:time:adjust:mode']);
		$s->req['c:upload:maxsize'] = $s->bytes_to_numbers($s->req['c:upload:maxsize']);
		$s->req['c:upload:extensions'] = str_replace(".", "", $s->req['c:upload:extensions']);
		$s->req['c:upload:extensions'] = preg_replace("/,\s+/", ",", $s->req['c:upload:extensions']); $s->req['c:upload:extensions'] = preg_replace("/\s+/", ",", $s->req['c:upload:extensions']); $s->req['c:upload:extensions'] = preg_replace("/^,(.*),$/", "\\1", $s->req['c:upload:extensions']);
		$s->req['c:visitor:blockip'] = preg_replace("/,\s+/", ",", $s->req['c:visitor:blockip']); $s->req['c:visitor:blockip'] = preg_replace("/\s+/", ",", $s->req['c:visitor:blockip']); $s->req['c:visitor:blockip'] = preg_replace("/^,(.*),$/", "\\1", $s->req['c:visitor:blockip']);
		$s->req['c:filter:list'] = trim($s->req['c:filter:list']); $s->req['c:filter:list'] = str_replace("\r\n", "\n", $s->req['c:filter:list']); $s->req['c:filter:list'] = preg_replace("/\n+/", "\n", $s->req['c:filter:list']);
		
		$db = $s->db_vars_open($s->cfg['file']['config'], 1);
		foreach ($s->req as $k => $v) {
			if (preg_match("/^c:/", $k)) {
				$k = preg_replace("/^c:/", "", $k); $k = str_replace(":", "']['", $k);
				$v = addslashes($v); $v = str_replace("\\'", "'", $v);
				eval("\$db['". $k ."'] = \"". $v ."\"; ");
			}
		}
		$s->db_vars_save($s->cfg['file']['config'], $db);
	}
	$m->location("sec=config", "Preferências salvas");
}



?>
