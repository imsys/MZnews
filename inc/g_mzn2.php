<?php

class MZn2 {
	
	//-----------------------------------------------------------------------------
	// MZn2 Sys
	//-----------------------------------------------------------------------------
	
	// Banco de dados antigo (para as skins)
	function db_old_open ($file) {
		global $s;
		$res = array();
		$tits = array();
		$contents = $s->file_read($file);
		$contents = explode("\n", $contents);
		foreach ($contents as $line) {
			$line = trim($line); if ($line == "") {continue; }
			if (preg_match("/^<\/[^>]+>$/", $line)) {
				$tit = preg_replace("/<\/([^>]+)>/i", "\\1", $line);
				if (preg_match("/(.*)\_(.*)/i", $tits[(count($tits)-1)]) && $tit == preg_replace("/\_(.*)$/i", "", $tits[(count($tits)-1)])) {unset($tits[(count($tits)-1)]); }
				else if ($tit == $tits[(count($tits)-1)]) {unset($tits[(count($tits)-1)]); }
			}
			else if (preg_match("/^<[^>]+>$/i", $line)) {$tits[count($tits)] = preg_replace("/<([^>]+)>/i", "\\1", $line); }
			else {
				$var = "\$res";
				foreach ($tits as $tit) {$var .= "['". $tit ."']"; }
				list ($name, $value) = explode("=", $line, 2);
				$name = trim($name); $value = trim($value);
				$name = preg_replace("/^\$/i", "", $name); $name = addslashes($name); $name = str_replace("\$", "\\$", $name);
				$value = addslashes($value); $value = str_replace("\\'", "'", $value); $value = str_replace("\$", "\\$", $value); $value = str_replace("\\\\n", "\\n", $value); $value = str_replace("\\\\t", "\\t", $value);
				eval($var ."['". $name ."'] = \"". $value ."\"; ");
			}
		}
		return $res;
	}
	
	// Erros
	function error ($file, $line, $msg, $fatal = 0, $extras = array()) {
		global $AbsPath;
		$file = str_replace("PATH". $AbsPath, "", "PATH". $file); $file = str_replace("\\", "/", $file); $file = preg_replace("/^\//", "", $file);
		$msg = preg_replace("/\[link=([^]]+)]([^\[]+)\[\/link]/i", "<a href=\"\\1\" target=\"_blank\" style=\"color:#CC0000; \">\\2</a>", $msg);
		echo "<!-- MZn² - Erro do sistema --><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; color:#CC0000; background-color:#FFFFFF; padding:8px; padding-left:10px; margin:5px; margin-top:10px; \">";
			echo "<h4 style=\"margin:0px; \">MZn² - Erro"; if ($fatal) {echo " Fatal"; } echo "</h4>";
			echo $msg;
			echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; color:#CC0000; \">";
			echo "<tr><td height=\"5\"></td></tr>";
			foreach ($extras as $k => $v) {
				if (preg_match("/^path:/", $v)) {$v = preg_replace("/^path:/", "", $v); $v = str_replace("PATH". $AbsPath, "", "PATH". $v); $v = str_replace("\\", "/", $v); $v = preg_replace("/^\//", "", $v); }
				echo "<tr><td><b>". $k .":</b>&nbsp;". $v ."</td></tr>";
			}
			echo "<tr><td><b>Arquivo:</b>&nbsp;". $file ." - <b>Linha</b> ". $line ."</td></tr>";
			echo "</table>";
		echo "</div>";
		if ($fatal) {exit; }
	}
	function error_redir ($id, $more = "") {
		if ($more) {$more = "&". $more; }
		$this->location("sec=error&id=". $id . $more);
	}
	
	// Início e fim
	function globalStart () {
		global $s;
		if (!is_writable($s->cfg['path']['data'])) {$this->error(__FILE__, __LINE__, "O diretório de dados não tem permissão de escrita!<br /><br />Você precisa alterar as permissões deste diretório.<br />[link=http://help.mznews.kit.net/?open=ftp]Clique aqui e saiba como.[/link]", 1, array("Diretório de dados" => "path:". $s->cfg['path']['data'])); }
		$this->confsRead();
		$this->sessionStart();
	}
	function globalEnd () {
		global $s;
		if (!$s->cfg['block:session']) {$this->sessionEnd(); }
		if (!$s->cfg['block:config']) {$this->confsSave(); }
	}
	
	// Início remoto
	function remoteStart () {
		global $s;
		$this->confsRead(1);
	}
	
	// Configurações
	function confsRead ($remote = 0) {
		global $s, $AbsPath, $AbsDir;
		
		if (!@file_exists($s->cfg['file']['categories'])) {$s->db_vars_create($s->cfg['file']['categories']); }
		if (!@file_exists($s->cfg['file']['comments'])) {$s->db_table_create($s->cfg['file']['comments'], "id|cid|nid|time|title|comment|data:vars"); }
		if (!@file_exists($s->cfg['file']['config'])) {$s->db_vars_create($s->cfg['file']['config']); }
		if (!@file_exists($s->cfg['file']['news'])) {$s->db_table_create($s->cfg['file']['news'], "id|cid|time|user|title|news|fnews|data:vars"); }
		if (!@file_exists($s->cfg['file']['uploads'])) {$s->db_table_create($s->cfg['file']['uploads'], "id|name|size|time|user"); }
		if (!@file_exists($s->cfg['file']['users'])) {$s->db_table_create($s->cfg['file']['users'], "id|user|pwd|data:vars|perms:vars"); }
		if (!@file_exists($s->cfg['file']['session'])) {$s->db_table_create($s->cfg['file']['session'], "session|ip|time|data:vars"); }
		if (!@file_exists($s->cfg['file']['skin_cache'])) {$s->db_vars_create($s->cfg['file']['skin_cache']); }
		
		$s->sys = $s->db_vars_open($s->cfg['file']['config'], 1);
		$s->cat = $s->db_vars_open($s->cfg['file']['categories'], 1);
		
		if (!$remote) {
			$s->sys['sys']['path'] = $AbsPath;
			$s->sys['sys']['dir'] = $AbsDir;
		}
		
		$s->smPack = $s->vars_import($s->sys['sm']['list']);
		$packs = $s->vars_import($s->sys['sm']['packs']);
		$packs['local'] = $s->cfg['dir']['smilies'];
		foreach ($s->smPack as $k => $v) {$s->smPack[$k] = $s->replace_vars($v, $packs); }
		unset($packs);
		
		$s->cfg['time'] = time() + ($s->sys['time']['adjust'] * 60);
		
		if (!$s->sys['skin']) {$s->sys['skin'] = $s->cfg['skin']; }
		$s->skin = $this->skinLoad();
		
		if ($s->sys['skin']) {$s->cfg['skin'] = $s->sys['skin']; }
		else {$s->cfg['skin'] = "blackfog"; }
		
		$s->users = array();
		$db = $s->db_table_open($s->cfg['file']['users'], $s->cfg['header']['users']);
		foreach ($db['data'] as $k => $v) {
			$s->users[$v['user']] = $v['data'];
			$s->users[$v['user']]['id'] = $v['id'];
			$s->users[$v['user']]['k'] = $k;
			$s->users[$v['user']]['pwd'] = $v['pwd'];
			
			$perms = array();
			if ($v['perms']) {
				if (isset($v['perms']['admin'])) {$perms['admin'] = 1; }
				else {
					foreach ($v['perms'] as $pk => $pv) {$perms[$pk] = $pv; }
				}
			}
			$s->users[$v['user']]['perms'] = $perms;
		}
		
		$s->sys['version'] = $s->cfg['ver']['system'];
	}
	function confsSave () {
		global $s;
		$s->db_vars_save($s->cfg['file']['config'], $s->sys);
	}
	
	// Skins
	function skinLoad () {
		global $s; $error = 0; $skinFile = $s->cfg['path']['img'] ."/". $s->sys['skin'] ."/skin.txt";
		if (@file_exists($s->cfg['file']['skin_cache'])) {
			$skin = $s->db_vars_open($s->cfg['file']['skin_cache']);
			if ($skin['name'] == $s->sys['skin'] && $skin['size'] == @filesize($skinFile)) {return $skin; }
		}
		if (!@file_exists($skinFile)) {$error = 1; }
		else {
			$skin = $this->db_old_open($skinFile);
			$skin = $skin['skin'];
			$skin['size'] = @filesize($skinFile);
			$skin['dir'] = $s->cfg['dir']['img'] ."/". $s->sys['skin'];
			if (version_compare($skin['version'], "2.0", "<")) {$error = 1; }
			else {
				$s->db_vars_save($s->cfg['file']['skin_cache'], $skin);
				return $skin;
			}
		}
		if ($error) {
			$this->error(__FILE__, __LINE__, "A skin <b>". $s->sys['skin'] ."</b> não existe ou não é compatível com esta versão do MZn².<br />A skin padrão foi selecionada. Por favor atualize a página.", 0, array("Skin" => $s->sys['skin']));
			$s->sys['skin'] = $s->cfg['skin'];
			$this->confsSave();
			exit;
		}
	}
	
	// Sessão
	function sessionStart () {
		global $s;
		$s->session = array();
		$s->usr = array();
		$this->session_Read();
		$this->session_Check();
		$s->usr = $s->session[$s->req['s']]['data'];
	}
	function sessionKill () {
		global $s;
		setcookie("s"); unset($s->req['s']);
	}
	function sessionEnd () {
		global $s;
		unset($s->usr['s']); unset($s->usr['data']);
		$s->session[$s->req['s']]['data'] = $s->usr; 
		$this->session_Save();
	}
	function session_Read () {
		global $s;
		$db = $s->db_table_open($s->cfg['file']['session']);
		foreach ($db['data'] as $dbL) {
			$s->session[$dbL['session']]['ip'] = $dbL['ip'];
			$s->session[$dbL['session']]['time'] = $dbL['time'];
			$s->session[$dbL['session']]['data'] = $dbL['data'];
		}
	}
	function session_Check () {
		global $s; $sOk = 0;
		foreach ($s->session as $sHash => $sDt) {
			if ($s->req['s'] == $sHash) {
				if ($s->session[$sHash]['ip'] == $s->req['REMOTE_ADDR']) {$s->session[$sHash]['time'] = time(); setcookie("s", $sHash, time() + 1200); $sOk = 1; }
				else {$this->sessionKill(); }
			}
			if ($s->session[$sHash]['time'] + 1200 <= time()) {unset($s->session[$sHash]); }
		}
		if (!$sOk) {
			if ($s->req['s']) {$sHash = $s->req['s']; }
			else {srand((double)microtime()*1000000); $sHash = md5(uniqid(rand(),1)); setcookie("s", $sHash, time() + 1200); $s->req['s'] = $sHash; }
			$s->session[$sHash]['ip'] = $s->req['REMOTE_ADDR']; $s->session[$sHash]['time'] = time(); $s->session[$sHash]['data'] = array();
		}
		$this->session_Save();
	}
	function session_Save () {
		global $s; $ext = array();
		$ext['header'] = explode("|", "session|ip|time|data:vars");
		$i = 0; foreach ($s->session as $sHash => $sDt) {
			$ext['data'][$i]['session'] = $sHash;
			$ext['data'][$i]['ip'] = $sDt['ip'];
			$ext['data'][$i]['time'] = $sDt['time'];
			$ext['data'][$i]['data'] = $sDt['data'];
			$i++;
		}
		$s->db_table_save($s->cfg['file']['session'], $ext);
	}
	
	// Redirecionamento
	function location ($args = "", $msg = "") {
		global $s, $m;
		if ($args) {$args = "s=". $s->req['s'] ."&". $args; }
		else {$args = "s=". $s->req['s']; }
		if ($msg) {$args .= "&msg=". urlencode($msg); }
		@header("Location: ". $s->req['PHP_SELF'] ."?". $args); echo "<meta http-equiv=\"refresh\" content=\"0; URL=". $s->quote_safe($s->req['PHP_SELF'] ."?". $args) ."\" />";
		$m->globalEnd();
		exit;
	}
	
	// Sincronia de arrays
	function array_sync ($from, $to) {
		if (is_array($from)) {
			$ret = array(); $gone = array();
			foreach ($from as $k => $v) {
				$gone[$k] = true;
				if (array_key_exists($k, $to)) {$ret[$k] = $this->array_sync($v, $to[$k]); }
				else {$ret[$k] = $v; }
			}
			foreach ($to as $k => $v) {
				if ($gone[$k]) {continue; } $gone[$k] = true;
				$ret[$k] = $v;
			}
			return $ret;
		}
		else {return $to; }
	}
	
	function parse_date ($time, $sep = " ") {
		global $s;
		$res = ""; $now = $s->cfg['time'];
		$dt = date("d-m-Y", $time); $hr = date("H:i:s", $time);
		$ndt = date("d-m-Y", $now); $ydt = date("d-m-Y", $now - 86400);
		if ($ndt == $dt) {$res .= "hoje às". $sep . $hr; }
		else if ($ydt == $dt) {$res .= "ontem às". $sep . $hr; }
		else {$res .= "dia ". $dt . $sep ."às ". $hr; }
		return $res;
	}
	
	
	//-----------------------------------------------------------------------------
	// MZn² mznCode & News parsing
	//-----------------------------------------------------------------------------
	
	function news_parse ($contents, $nobr = 0, $nocode = 0, $nosmilies = 0, $onlymzncode = 0, $mode = "html") {
		global $s;
		$res = $contents;
		if (!$onlymzncode) {
			if (!$nobr) {
				$res = str_replace("\n", "[br]", $res);
			}
			if (!$nosmilies) {
				$smFind = array(); $smRepl = array(); foreach ($s->smPack as $smTx => $smUrl) {$smTx = $s->regex_safe($smTx); $smFind[] = "/(^|\s|\])(". $smTx .")(\s|\n|\.|,|\[|$)/"; $smRepl[] = "\\1[img=middle]". $smUrl ."[/img]\\3"; }
				$res = preg_replace($smFind, $smRepl, $res);
			}
			if (!$nocode && $mode == "html") {
				$hcFind = array(); $hcRepl = array(); foreach ($s->cfg['hcode']['taglist'] as $hcL) {$hcL = explode("|", $hcL); $hcFind[] = sprintf($s->cfg['hcode'][$hcL[2]], $hcL[0], $hcL[0]); $hcRepl[] = $hcL[1]; }
				$res = preg_replace($hcFind, $hcRepl, $res);
			}
		}
		return $res;
	}
	
	function mznCode_to_html ($c) {
		global $s;
		
		$c = preg_replace("/\[flash=([^x]+)x([^\]]+)\]([^\[]+)\[\/flash\]/iU", "<img style=\"WIDTH: \\1px; HEIGHT: \\2px\" height=\\2 src=\"img/editor_flash_obj.gif\" width=\\1 border=0 flash_url=\"\\3\">", $c);
		
		$hcFind = array(); $hcRepl = array(); foreach ($s->cfg['hcode']['taglist'] as $hcL) {$hcL = explode("|", $hcL); $hcFind[] = sprintf($s->cfg['hcode'][$hcL[2]], $hcL[0], $hcL[0]); $hcRepl[] = $hcL[1]; }
		$c = preg_replace($hcFind, $hcRepl, $c); $c = preg_replace($hcFind, $hcRepl, $c); $c = preg_replace($hcFind, $hcRepl, $c);
		
		return $c;
	}
	
	function html_to_mznCode ($c) {
		$c = str_replace("[", "&#91;", $c);
		$c = str_replace("]", "&#93;", $c);
		
		$c = preg_replace("/<a href=(\"|)mailto:([^\"]+)(\"|)>([\s\S]*)<\/a>/iU", "[email=\\2]\\4[/email]", $c);
		$c = preg_replace("/<a href=(\"|)mailto:([^\"]+)(\"|)>([\s\S]*)<\/a>/iU", "[email=\\2]\\4[/email]", $c);
		$c = preg_replace("/<a href=(\"|)mailto:([^\"]+)(\"|)>([\s\S]*)<\/a>/iU", "[email=\\2]\\4[/email]", $c);
		
		$c = preg_replace("/<a href=(\"|)([^\|]+)(\"|) target=(\"|)_([^\|]+)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,\\5]\\7[/url]", $c);
		$c = preg_replace("/<a href=(\"|)([^\|]+)(\"|) target=(\"|)_([^\|]+)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,\\5]\\7[/url]", $c);
		$c = preg_replace("/<a href=(\"|)([^\|]+)(\"|) target=(\"|)_([^\|]+)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,\\5]\\7[/url]", $c);

		$c = preg_replace("/<a href=(\"|)([^\|]+)\|(\/|)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2]\\5[/url]", $c);
		$c = preg_replace("/<a href=(\"|)([^\|]+)\|(\/|)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2]\\5[/url]", $c);
		$c = preg_replace("/<a href=(\"|)([^\|]+)\|(\/|)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2]\\5[/url]", $c);
		
		$c = preg_replace("/<a href=(\"|)([^\|]+)\|([^\/\">]+)(\/|)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,\\3]\\6[/url]", $c);
		$c = preg_replace("/<a href=(\"|)([^\|]+)\|([^\/\">]+)(\/|)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,\\3]\\6[/url]", $c);
		$c = preg_replace("/<a href=(\"|)([^\|]+)\|([^\/\">]+)(\/|)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,\\3]\\6[/url]", $c);
		
		$c = preg_replace("/<a href=(\"|)([^\|]+)(\"|)>([\s\S]*)<\/a>/iU", "[url=\\2,self]\\4[/url]", $c);

		$c = preg_replace("/<div align=(\"|)([^>]+)(\"|)>([\s\S]*)<\/div>/iU", "[align=\\2]\\4[/align]", $c);
		$c = preg_replace("/<div align=(\"|)([^>]+)(\"|)>([\s\S]*)<\/div>/iU", "[align=\\2]\\4[/align]", $c);
		$c = preg_replace("/<div align=(\"|)([^>]+)(\"|)>([\s\S]*)<\/div>/iU", "[align=\\2]\\4[/align]", $c);

		$c = preg_replace("/<div>([\s\S]*)<\/div>/iU", "[align]\\1[/align]", $c);
		$c = preg_replace("/<div>([\s\S]*)<\/div>/iU", "[align]\\1[/align]", $c);
		$c = preg_replace("/<div>([\s\S]*)<\/div>/iU", "[align]\\1[/align]", $c);
		
		$c = preg_replace("/<p align=(\"|)([^>]+)(\"|)>([\s\S]*)<\/p>/iU", "[p=\\2]\\4[/p]", $c);
		$c = preg_replace("/<p align=(\"|)([^>]+)(\"|)>([\s\S]*)<\/p>/iU", "[p=\\2]\\4[/p]", $c);
		$c = preg_replace("/<p align=(\"|)([^>]+)(\"|)>([\s\S]*)<\/p>/iU", "[p=\\2]\\4[/p]", $c);

		$c = preg_replace("/<p>([\s\S]*)<\/p>/iU", "[p]\\1[/p]", $c);
		$c = preg_replace("/<p>([\s\S]*)<\/p>/iU", "[p]\\1[/p]", $c);
		$c = preg_replace("/<p>([\s\S]*)<\/p>/iU", "[p]\\1[/p]", $c);

		$c = preg_replace("/<p( align=(\"|)([^>]+)|)(\"|)>([\s\S]*)<\/p>/iU", "[p=\\3]\\5[/p]", $c);
		$c = preg_replace("/<p( align=(\"|)([^>]+)|)(\"|)>([\s\S]*)<\/p>/iU", "[p=\\3]\\5[/p]", $c);
		$c = preg_replace("/<p( align=(\"|)([^>]+)|)(\"|)>([\s\S]*)<\/p>/iU", "[p=\\3]\\5[/p]", $c);
		
		$c = preg_replace("/<marquee( [^>]+|)>([\s\S]*)<\/marquee>/iU", "[move]\\2[/move]", $c);
		$c = preg_replace("/<marquee( [^>]+|)>([\s\S]*)<\/marquee>/iU", "[move]\\2[/move]", $c);
		$c = preg_replace("/<marquee( [^>]+|)>([\s\S]*)<\/marquee>/iU", "[move]\\2[/move]", $c);
		
		$c = preg_replace("/<b( [^>]+|)>/iU", "[b]", $c); $c = preg_replace("/<\/b>/iU", "[/b]", $c);
		$c = preg_replace("/<strong( [^>]+|)>/iU", "[b]", $c); $c = preg_replace("/<\/strong>/iU", "[/b]", $c);
		$c = preg_replace("/<i( [^>]+|)>/iU", "[i]", $c); $c = preg_replace("/<\/i>/iU", "[/i]", $c);
		$c = preg_replace("/<em( [^>]+|)>/iU", "[i]", $c); $c = preg_replace("/<\/em>/iU", "[/i]", $c);
		$c = preg_replace("/<u( [^>]+|)>/iU", "[u]", $c); $c = preg_replace("/<\/u>/iU", "[/u]", $c);
		$c = preg_replace("/<ul( [^>]+|)>/iU", "[list]", $c); $c = preg_replace("/<\/ul>/iU", "[/list]", $c);
		$c = preg_replace("/<ol( [^>]+|)>/iU", "[listnum]", $c); $c = preg_replace("/<\/ol>/iU", "[/listnum]", $c);
		$c = preg_replace("/<li( [^>]+|)>/iU", "[li]", $c); $c = preg_replace("/<\/li>/iU", "[/li]", $c);
		$c = preg_replace("/<hr( [^>]+|)>/iU", "[hr]", $c);
		$c = preg_replace("/<br( [^>]+|)>/iU", "[br]", $c);
		
		$c = preg_replace("/<font>([\s\S]*)<\/font>/iU", "\\1", $c);
		$c = preg_replace("/<font( style=\"BACKGROUND-COLOR: ([^\" ]+)\"|)( face=(\"([^\"]+)\"|([^ ]+))|)( color=([^ ]+)|)( size=([^ ]+)|)>([\s\S]*)<\/font>/iU", "[bgcolor=\\2][font=\\5\\6][color=\\8][size=\\10]\\11[/size][/color][/font][/bgcolor]", $c);
		$c = preg_replace("/<font( style=\"BACKGROUND-COLOR: ([^\" ]+)\"|)( face=(\"([^\"]+)\"|([^ ]+))|)( color=([^ ]+)|)( size=([^ ]+)|)>([\s\S]*)<\/font>/iU", "[bgcolor=\\2][font=\\5\\6][color=\\8][size=\\10]\\11[/size][/color][/font][/bgcolor]", $c);
		$c = preg_replace("/<font( style=\"BACKGROUND-COLOR: ([^\" ]+)\"|)( face=(\"([^\"]+)\"|([^ ]+))|)( color=([^ ]+)|)( size=([^ ]+)|)>([\s\S]*)<\/font>/iU", "[bgcolor=\\2][font=\\5\\6][color=\\8][size=\\10]\\11[/size][/color][/font][/bgcolor]", $c);
		$c = preg_replace("/<font( style=\"BACKGROUND-COLOR: ([^\" ]+)\"|)( face=(\"([^\"]+)\"|([^ ]+))|)( color=([^ ]+)|)( size=([^ ]+)|)>([\s\S]*)<\/font>/iU", "[bgcolor=\\2][font=\\5\\6][color=\\8][size=\\10]\\11[/size][/color][/font][/bgcolor]", $c);
		$c = preg_replace("/<font( style=\"BACKGROUND-COLOR: ([^\" ]+)\"|)( face=(\"([^\"]+)\"|([^ ]+))|)( color=([^ ]+)|)( size=([^ ]+)|)>([\s\S]*)<\/font>/iU", "[bgcolor=\\2][font=\\5\\6][color=\\8][size=\\10]\\11[/size][/color][/font][/bgcolor]", $c);
		$c = preg_replace("/<font( style=\"BACKGROUND-COLOR: ([^\" ]+)\"|)( face=(\"([^\"]+)\"|([^ ]+))|)( color=([^ ]+)|)( size=([^ ]+)|)>([\s\S]*)<\/font>/iU", "[bgcolor=\\2][font=\\5\\6][color=\\8][size=\\10]\\11[/size][/color][/font][/bgcolor]", $c);
		
		$c = preg_replace("/\[size=]([\s\S]*)\[\/size]/iU", "\\1", $c); $c = preg_replace("/\[color=]([\s\S]*)\[\/color]/iU", "\\1", $c); $c = preg_replace("/\[font=]([\s\S]*)\[\/font]/iU", "\\1", $c); $c = preg_replace("/\[bgcolor=]([\s\S]*)\[\/bgcolor]/iU", "\\1", $c);
		$c = preg_replace("/\[size=]([\s\S]*)\[\/size]/iU", "\\1", $c); $c = preg_replace("/\[color=]([\s\S]*)\[\/color]/iU", "\\1", $c); $c = preg_replace("/\[font=]([\s\S]*)\[\/font]/iU", "\\1", $c); $c = preg_replace("/\[bgcolor=]([\s\S]*)\[\/bgcolor]/iU", "\\1", $c);
		$c = preg_replace("/\[size=]([\s\S]*)\[\/size]/iU", "\\1", $c); $c = preg_replace("/\[color=]([\s\S]*)\[\/color]/iU", "\\1", $c); $c = preg_replace("/\[font=]([\s\S]*)\[\/font]/iU", "\\1", $c); $c = preg_replace("/\[bgcolor=]([\s\S]*)\[\/bgcolor]/iU", "\\1", $c);
		$c = preg_replace("/\[size=]([\s\S]*)\[\/size]/iU", "\\1", $c); $c = preg_replace("/\[color=]([\s\S]*)\[\/color]/iU", "\\1", $c); $c = preg_replace("/\[font=]([\s\S]*)\[\/font]/iU", "\\1", $c); $c = preg_replace("/\[bgcolor=]([\s\S]*)\[\/bgcolor]/iU", "\\1", $c);
		
		$c = preg_replace("/<img( style=\"WIDTH: ([^p]+)px; HEIGHT: ([^p]+)px\"|)( height=([^ ]+)|)( src=\"([^\"]+)\"|)( width=([^ ]+)|)( align=([^ ]+)|)( border=([^ ]+)|) flash_url=\"([^\"]+)\">/iU", "[flash=\\2x\\3]\\14[/flash]", $c);
		$c = preg_replace("/<img( height=([^ ]+)|)( src=\"([^\"]+)\"|)( width=([^ ]+)|)( align=([^ ]+)|)( border=([^>]+)|)>/iU", "[img=\\6x\\2,\\8]\\4[/img]", $c);
		$c = preg_replace("/<img( style=\"WIDTH: ([^p]+)px; HEIGHT: ([^p]+)px\"|)( height=([^ ]+)|)( src=\"([^\"]+)\"|)( width=([^ ]+)|)( align=([^ ]+)|)( border=([^>]+)|)>/iU", "[img=\\2x\\3,\\11]\\7[/img]", $c);
		$c = preg_replace("/\[img=x,([^]]+)]([\s\S]*)\[\/img]/iU", "[img=\\1]\\2[/img]", $c);
		return $c;
	}
	
	//-----------------------------------------------------------------------------
	// MZn² Debug
	//-----------------------------------------------------------------------------
	
	function debug_dump ($var) {
		global $s; $var = str_replace("\r\n", "\n", $var); $var = str_replace("\n", "\\n", $var);
		echo "<pre>". $s->quote_safe(print_r($var, 1)) ."</pre>";
	}
	
	//-----------------------------------------------------------------------------
	// MZn² Misc
	//-----------------------------------------------------------------------------
	
	function perms($perm, $cat = "") {
		global $s; $res = 0;
		if ($s->usr['data']['perms']['admin']) {$res = 1; }
		else {
			if (preg_match("/\|/", $perm)) {
				$perm = explode("|", $perm); $res = 0;
				foreach ($perm as $p) {
					if ($cat != "") {if (strpos(",". $s->usr['data']['perms'][$cat] .",", ",". $p .",") !== FALSE || strpos(",". $s->usr['data']['perms']['all'] .",", ",". $p .",") !== FALSE) {$res += 1; } }
					else {if (strpos(",". $s->usr['data']['perms']['general'] .",", ",". $p .",") !== FALSE) {$res += 1; } }
				}
				$res = ($res > 0);
			}
			else if (preg_match("/&/", $perm)) {
				$perm = explode("&", $perm); $res = 0;
				foreach ($perm as $p) {
					if ($cat != "") {if (strpos(",". $s->usr['data']['perms'][$cat] .",", ",". $perm .",") !== FALSE || strpos(",". $s->usr['data']['perms']['all'] .",", ",". $p .",") !== FALSE) {$res += 1; } }
					else {if (strpos(",". $s->usr['data']['perms']['general'] .",", ",". $perm .",") !== FALSE) {$res += 1; } }
				}
				$res = ($res >= count($perm));
			}
			else {
				if ($cat != "") {if (strpos(",". $s->usr['data']['perms'][$cat] .",", ",". $perm .",") !== FALSE || strpos(",". $s->usr['data']['perms']['all'] .",", ",". $perm .",") !== FALSE) {$res = 1; } }
				else {if (strpos(",". $s->usr['data']['perms']['general'] .",", ",". $perm .",") !== FALSE) {$res = 1; } }
			}
		}
		return $res;
	}
	
	function req() {
		global $s;
		$fArgs = func_get_args();
		for ($i = 0; $i < count($fArgs); $i++) {
			$req = $fArgs[$i];
			$req = str_replace("|", "']['", $req);
			eval("if (!isset(\$s->req['". $req ."']) || \$s->req['". $req ."'] == \"\") {\$this->error_redir(\"incform\"); } ");
		}
	}
	
	function req_sync() {
		global $s;
		$fArgs = func_get_args();
		for ($i = 0; $i < count($fArgs); $i++) {
			$req = $fArgs[$i];
			$req = str_replace("|", "']['", $req);
			eval("if (!\$s->req['". $req ."']) {\$s->req['". $req ."'] = 0; } ");
		}
	}
	
	function req_perms($perm, $cat = "") {
		global $s;
		if (!$this->perms($perm, $cat)) {$this->error_redir("noperms"); }
	}
	
	function req_login() {
		global $s;
		if (!$s->usr['user']) {$this->error_redir("noperms"); }
	}
	
	function filter_query () {
		global $s;
		$query = $s->req['QUERY_STRING'];
		$query = preg_replace("/(&|)s=[a-f0-9]{32}/", "", $query);
		$query = preg_replace("/(&|)cat=[^&]+/", "", $query);
		$query = preg_replace("/^&/", "", $query);
		return $query;
	}
	
}

?>
