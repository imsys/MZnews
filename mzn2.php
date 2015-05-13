<?php

/////////////////////////////////////////////////////////////////////////////
//                                                                         //
//                      __  __   ____           ___                        //
//                     |  \/  | |_  /  _ _ ®   |_  )                       //
//                     | |\/| |  / /  | ' \     / /                        //
//                     |_|  |_| /___| |_||_|   /___|                       //
//                                                                         //
//                                                                         //
//        Copyright 2003-2004 WsTec - Todos os direitos reservados         //
//                                                                         //
//////////////////////////////////////   by Wesley de Souza - GokuSSJ5   ////
//                                                                         //
//      É ALTAMENTE PERIGOSA QUALQUER ALTERAÇÃO A PARTIR DESTE PONTO!      //
//                                                                         //
/////////////////////////////////////////////////////////////////////////////

error_reporting(7); set_magic_quotes_runtime(0);

define("WsSys_Token", 1);

if (!$mzn_path) {echo "<b>Erro!</b> - Caminho não localizado!"; exit; }
if (!file_exists($mzn_path ."/inc/g_global.php")) {echo "<b>Erro!</b> - Caminho inválido!"; exit; }

unset($s); unset($m);

$AbsPath = $mzn_path;
require_once $mzn_path ."/inc/g_global.php";
require_once $mzn_path ."/inc/g_config.php";
	$s = new WsSys;
	$s->cfg = $c;
	$s->debug = 1;

require_once $AbsPath ."/inc/g_mzn2.php";
	$m = new MZn2;

// Condição de existência do banco de dados
$system_ok = 0;
if (@file_exists($s->cfg['file']['mzn2_safe'])) {$system_ok = 1; $m->remoteStart(); }

class MZn2_Noticias {
	
	var $categoria = "principal";
	var $ordenar_por = "data";
	var $ordem = "decrescente";
	var $data = "";
	var $busca = "";
	var $usuario = "";
	var $noticia = "";
	var $porpagina = 10;
	var $pagina = 1;
	var $paginas = 1;
	var $ip = "";
	
	var $mostrar_agrupamento_diario = 1;
	var $mostrar_link_noticia_completa = 1;
	
	var $dbC = array();
	var $dbN = array();
	var $filterFind = array();
	var $filterRepl = array();
	
	var $msg = "O MZn² ainda não foi instalado.";
	
	// Constructor
	function MZn2_Noticias () {
		global $s, $m, $system_ok;
		if (!$system_ok) {return; }
		if (!$this->pagina) {$this->pagina = 1; }
		$this->dbC = $s->db_table_open($s->cfg['file']['comments']); $this->dbC = $this->dbC['data'];
		$this->dbN = $s->db_table_open($s->cfg['file']['news']); $this->dbN = $this->dbN['data'];
		$this->filter_load();
		
		$addrs = array();
		foreach(array_reverse(explode(",", $s->req['HTTP_X_FORWARDED_FOR'])) as $x_f) {
			$x_f = trim($x_f);
			if (preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $x_f )) {$addrs[] = $x_f; }
		}
		$addrs[] = $s->req['REMOTE_ADDR'];
		$addrs[] = $s->req['HTTP_PROXY_USER'];
		$ip = $this->select_var($addrs);
		$ip = preg_replace("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $ip);
		$s->req['REMOTE_ADDR'] = $ip;
		$this->ip = $ip;
	}
	
	function select_var($array) {
		if (!is_array($array)) {return ""; } ksort($array);
		$chosen = "";
		foreach ($array as $k => $v) {
			if (isset($v)) {
				$chosen = $v;
				break;
			}
		}
		return $chosen;
	}
	
	function verify_ip () {
		global $s, $m;
		$user_ip = $this->ip; $blocked = $s->sys['visitor']['blockip'];
		if (!$user_ip) {return false; }
		if (!$blocked) {return true; }
		$blocked = explode(",", $blocked);
		foreach ($blocked as $ip) {
			$ip = $s->regex_safe($ip);
			$ip = str_replace("\\*", "[0-9]{1,3}", $ip);
			if (preg_match("/". $ip ."/iU", $user_ip)) {return false; }
		}
		return true;
	}
	
	function test_login ($v1, $v2) {
		global $s;
		if (md5($v1) == $v2) {return true; }
		else {return false; }
	}
	
	function tpl ($tpl, $replace = array(), $useFrom = "") {
		global $s, $m, $system_ok;
		if ($system_ok) {
			$cat = $this->categoria; if ($useFrom) {$cat = $useFrom; }
			if ($s->cat[$cat]['templates']['usefrom']) {return $this->tpl($tpl, $replace, $s->cat[$cat]['templates']['usefrom']); }
			if (!$s->cat[$cat]['templates'][$tpl]) {return ""; }
			$res = $s->cat[$cat]['templates'][$tpl];
			foreach ($replace as $k => $v) {
				$res = str_replace("{". $k ."}", $v, $res);
			}
			return $res;
		}
	}
	
	function parse_date ($date, $time) {
		global $s;
		$weekds = array("0" => "Domingo", "1" => "Segunda", "2" => "Terça", "3" => "Quarta", "4" => "Quinta", "5" => "Sexta", "6" => "Sábado");
		$months = array("01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
		$weekds_s = array("0" => "Dom", "1" => "Seg", "2" => "Ter", "3" => "Qua", "4" => "Qui", "5" => "Sex", "6" => "Sáb");
		$months_s = array("01" => "Jan", "02" => "Fev", "03" => "Mar", "04" => "Abr", "05" => "Mai", "06" => "Jun", "07" => "Jul", "08" => "Ago", "09" => "Set", "10" => "Out", "11" => "Nov", "12" => "Dez");
		$date = str_replace("%F", $months[date("m", $time)], $date);
		$date = str_replace("%l", $weekds[date("m", $time)], $date);
		$date = str_replace("%M", $months_s[date("m", $time)], $date);
		$date = str_replace("%D", $weekds_s[date("m", $time)], $date);
		$date_res = array(); preg_match_all("/%(.)/U", $date, $date_res); foreach ($date_res[1] as $v2) {$date = preg_replace("/%". $v2 ."/U", date($v2, $time), $date); }
		return $date;
	}
	
	function parse_date_adv ($str, $time, $prefix) {
		global $s;
		$weekds = array("0" => "Domingo", "1" => "Segunda", "2" => "Terça", "3" => "Quarta", "4" => "Quinta", "5" => "Sexta", "6" => "Sábado");
		$months = array("01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
		$weekds_s = array("0" => "Dom", "1" => "Seg", "2" => "Ter", "3" => "Qua", "4" => "Qui", "5" => "Sex", "6" => "Sáb");
		$months_s = array("01" => "Jan", "02" => "Fev", "03" => "Mar", "04" => "Abr", "05" => "Mai", "06" => "Jun", "07" => "Jul", "08" => "Ago", "09" => "Set", "10" => "Out", "11" => "Nov", "12" => "Dez");
		$str = str_replace("{". $prefix ."%F}", $months[date("m", $time)], $str);
		$str = str_replace("{". $prefix ."%l}", $weekds[date("w", $time)], $str);
		$str = str_replace("{". $prefix ."%M}", $months_s[date("m", $time)], $str);
		$str = str_replace("{". $prefix ."%D}", $weekds_s[date("w", $time)], $str);
		$str_res = array(); preg_match_all("/\{". $s->regex_safe($prefix) ."%(.)\}/U", $str, $str_res); foreach ($str_res[1] as $v2) {$str = preg_replace("/\{". $s->regex_safe($prefix) ."%". $v2 ."\}/", date($v2, $time), $str); }
		return $str;
	}
	
	function filter_load () {
		global $s, $m;
		$list = trim($s->sys['filter']['list']); $list = explode("\n", $list);
		foreach ($list as $item) {
			list($find, $replace) = explode("=", $item, 2);
			$find = $s->simple_string($find); $find = $s->regex_search($find);
			$this->filterFind[] = "/". $find ."/i";
			$this->filterRepl[] = $replace;
		}
	}
	
	function verify_name ($name) {
		global $s, $m;
		$name = $s->simple_string($name);
		$list = trim($s->sys['visitor']['lock_custom']); $list = explode("\n", $list);
		foreach ($list as $find) {
			$find = $s->simple_string($find); $find = $s->regex_search($find);
			if (preg_match("/". $find ."/iU", $name)) {return false; }
		}
		foreach ($s->users as $v) {
			$find = $v['name']; $find = $s->simple_string($find); $find = $s->regex_safe($find);
			if (preg_match("/". $find ."/iU", $name)) {return false; }
		}
		return true;
	}
	
	function news_parse ($contents, $nobr = 0, $nocode = 0, $nosmilies = 0, $nohtml = 0) {
		global $s;
		$res = $contents;
		if ($nohtml) {
			$res = str_replace("<", "&lt;", $res);
			$res = str_replace(">", "&gt;", $res);
		}
		if (!$nobr) {
			if ($nocode) {$res = str_replace("\n", "<br />", $res);}
			else {$res = str_replace("\n", "[br]", $res); }
		}
		if (!$nosmilies) {
			$smFind = array(); $smRepl = array(); foreach ($s->smPack as $smTx => $smUrl) {if ($nohtml) {$smTx = str_replace("<", "&lt;", $smTx); $smTx = str_replace(">", "&gt;", $smTx); } $smTx = $s->regex_safe($smTx); $smFind[] = "/(^|\s|\])(". $smTx .")(\s|\n|\.|,|\[|$)/"; if ($nocode) {$smRepl[] = "\\1<img src=\"". $smUrl ."\" align=\"absMiddle\" border=\"0\" />\\3"; } else {$smRepl[] = "\\1[img=absMiddle]". $smUrl ."[/img]\\3"; } }
			$res = preg_replace($smFind, $smRepl, $res);
		}
		if (!$nocode) {
			$hcFind = array(); $hcRepl = array(); foreach ($s->cfg['hcode']['taglist'] as $hcL) {$hcL = explode("|", $hcL); $hcFind[] = sprintf($s->cfg['hcode'][$hcL[2]], $hcL[0], $hcL[0]); $hcRepl[] = $hcL[1]; }
			$res = preg_replace($hcFind, $hcRepl, $res);
			$res = preg_replace($hcFind, $hcRepl, $res);
			$res = preg_replace($hcFind, $hcRepl, $res);
		}
		return $res;
	}
	
	function mostrar_manchetes() {$this->mostrar_noticias("headlines"); }
	// Aliases
	function mostrar_headlines() {$this->mostrar_manchetes(); }
	function mostrar_cabecalhos() {$this->mostrar_manchetes(); }
	function mostrar_cabeçalhos() {$this->mostrar_manchetes(); }
	
	function mostrar_noticias($type = "news") {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$o1 = $s->simple_string($this->ordenar_por); $o2 = $s->simple_string(substr($this->ordem, 0, 1));
		
		if ($this->data) {$data = explode("-", $this->data); }
		if ($this->busca) {$busca = $s->regex_search($this->busca); }
		if (!$this->pagina) {$this->pagina = 1; }
		
		$comments = array();
		foreach ($this->dbC as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['data']['q']) {continue; }
			if (!$comments[$v['nid']]) {$comments[$v['nid']] = 0; }
			$comments[$v['nid']] += 1;
		}
		
		$ndb = array();
		$i = 0; foreach ($this->dbN as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['data']['q'] || ($v['data']['t'] && $s->cfg['time'] < $v['time'])) {continue; }
			if ($data) {
				if ($data[0] && date("Y", $v['time']) != $data[0]) {continue; }
				if ($data[1] && date("m", $v['time']) != $data[1]) {continue; }
				if ($data[2] && date("d", $v['time']) != $data[2]) {continue; }
			}
			if ($busca) {
				$search = ""; foreach ($v as $item) {if (is_array($item)) {continue; } if ($search) {$search .= "|"; } $search .= $item; }
				if (!preg_match("/". $busca ."/iU", $search)) {continue; }
			}
			if ($this->usuario) {
				if ($v['user'] != $this->usuario) {continue; }
			}
			$k = "";
			if ($o1 == "data") {$k .= $v['time']; while (strlen($k) < 20) {$k = "0". $k; } }
			else if ($o1 == "usuario") {$k = $s->users[$v['user']]['name']; }
			$k .= "|". $i;
			$ndb[$k] = $v;
		$i++; }
		
		if ($o2 == "d") {krsort($ndb); }
		else {ksort($ndb); }
		
		$count = count($ndb); $page = $this->pagina; $perpage = $this->porpagina;
		if ($count) {
			if ($perpage == 0) {$iStart = 0; $iEnd = $count; $this->paginas = 1; }
			else {
				$pg_tot = 1; while ($count - $perpage > 0) {$pg_tot++; $count -= $perpage; }
				if ($page > $pg_tot) {$page = $pg_tot; } if ($page < 1) {$page = 1; }
				$iStart = intval(($page - 1) * $perpage); $iEnd = round($page * $perpage);
				$this->paginas = $pg_tot;
			}
			
			$res = array();
			$i = 0; foreach ($ndb as $k => $v) {
				if ($i >= $iStart && $i < $iEnd) {
					if (!$comments[$v['id']]) {$comments[$v['id']] = 0; }
					if ($s->sys['filter']['news']) {$v['title'] = preg_replace($this->filterFind, $this->filterRepl, $v['title']); $v['news'] = preg_replace($this->filterFind, $this->filterRepl, $v['news']); $v['fnews'] = preg_replace($this->filterFind, $this->filterRepl, $v['fnews']); }
					$nohtml = 0; if (!$s->users[$v['user']]['perms'][$this->categoria]['usehtml'] && !$s->users[$v['user']]['perms']['admin']) {$nohtml = 1; }
					$v['news'] = $this->news_parse($v['news'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
					$v['fnews'] = $this->news_parse($v['fnews'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
					$repl = array();
					$repl['system:mzn2dir'] = $s->sys['sys']['dir'];
					$repl['system:thispage'] = $s->req['PHP_SELF'];
					$repl['news:id'] = $v['id'];
					$repl['news:title'] = $v['title']; if ($s->cat[$this->categoria][$type]['cut'] && strlen($repl['news:title']) > $s->cat[$this->categoria][$type]['cut']) {$repl['news:title'] = substr($repl['news:title'], 0, intval($s->cat[$this->categoria][$type]['cut'] - 3)) ."..."; }
					$repl['news:title:nocut'] = $v['title'];
					$repl['news:contents'] = $v['news'];
					$repl['news:full'] = $v['fnews'];
					$repl['news:comments'] = $comments[$v['id']]; if ($v['data']['nc']) {$repl['news:comments'] = "-"; }
					$date = $this->tpl("date"); $date = $this->parse_date($date, $v['time']);
					$repl['news:date'] = $date;
					$repl['date:day'] = date("d", $v['time']); $repl['date:month'] = date("m", $v['time']); $repl['date:year'] = date("Y", $v['time']); $repl['date:hour'] = date("H", $v['time']); $repl['date:minute'] = date("i", $v['time']); $repl['date:second'] = date("s", $v['time']);
					$repl['user:login'] = $v['user'];
					$repl['user:name'] = $s->users[$v['user']]['name'];
					$repl['user:mail'] = $s->users[$v['user']]['mail'];
					$repl['user:icq'] = $s->users[$v['user']]['icq'];
					$repl['user:field1'] = $s->users[$v['user']]['field1'];
					$repl['user:field2'] = $s->users[$v['user']]['field2'];
					$repl['user:field3'] = $s->users[$v['user']]['field3'];
					$repl['user:posts'] = $s->users[$v['user']]['posts'];
					if ($v['data']['f'] && $this->mostrar_link_noticia_completa) {$repl['news:contents'] .= $this->tpl("fnews_link", $repl); }
					$final = $this->tpl($type, $repl);
					$final = $this->parse_date_adv($final, $v['time'], "date:");
					$res[date("d/m/Y", $v['time'])] .= $final;
				}
				$i++;
			}
			
			foreach ($res as $k => $day) {
				if (strpos($this->tpl("daygroup"), "{news}") !== FALSE && $type == "news" && $this->mostrar_agrupamento_diario) {
					$repl = array(); $k = explode("/", $k, 3);
					$repl['news'] = $day;
					$repl['date:day'] = $k[0];
					$repl['date:month'] = $k[1];
					$repl['date:year'] = $k[2];
					$final = $this->tpl("daygroup", $repl);
					$final = $this->parse_date_adv($final, mktime(0, 0, 0, $k[1], $k[0], $k[2]), "date:");
					echo $final;
				}
				else {echo $day; }
			}
			
		}
		else if ($count == 0) {
			if ($busca) {echo "Sua busca não encontrou resultados"; }
			else {echo "Não há notícias para exibir"; }
		}
	}
	// Aliases
	function mostrar_news() {$this->mostrar_noticias(); }
	function mostrar_notícias() {$this->mostrar_noticias(); }
	
	function mostrar_noticia($tpl = "news") {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$comments = array();
		foreach ($this->dbC as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['data']['q']) {continue; }
			if (!$comments[$v['nid']]) {$comments[$v['nid']] = 0; }
			$comments[$v['nid']] += 1;
		}
		
		$i = 0; foreach ($this->dbN as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['id'] != $this->noticia) {continue; }
			if (!$comments[$v['id']]) {$comments[$v['id']] = 0; }
			if ($s->sys['filter']['news']) {$v['title'] = preg_replace($this->filterFind, $this->filterRepl, $v['title']); $v['news'] = preg_replace($this->filterFind, $this->filterRepl, $v['news']); $v['fnews'] = preg_replace($this->filterFind, $this->filterRepl, $v['fnews']); }
			$nohtml = 0; if (!$s->users[$v['user']]['perms'][$this->categoria]['usehtml'] && !$s->users[$v['user']]['perms']['admin']) {$nohtml = 1; }
			$v['news'] = $this->news_parse($v['news'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
			$v['fnews'] = $this->news_parse($v['fnews'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
			$repl = array();
			$repl['system:mzn2dir'] = $s->sys['sys']['dir'];
			$repl['system:thispage'] = $s->req['PHP_SELF'];
			$repl['news:id'] = $v['id'];
			$repl['news:title'] = $v['title']; if ($s->cat[$this->categoria]['news']['cut'] && strlen($repl['news:title']) > $s->cat[$this->categoria]['news']['cut']) {$repl['news:title'] = substr($repl['news:title'], 0, intval($s->cat[$this->categoria]['news']['cut'] - 3)) ."..."; }
			$repl['news:title:nocut'] = $v['title'];
			$repl['news:contents'] = $v['news'];
			$repl['news:full'] = $v['fnews'];
			$repl['news:comments'] = $comments[$v['id']]; if ($v['data']['nc']) {$repl['news:comments'] = "-"; }
			$date = $this->tpl("date"); $date = $this->parse_date($date, $v['time']);
			$repl['news:date'] = $date;
			$repl['date:day'] = date("d", $v['time']);
			$repl['date:month'] = date("m", $v['time']);
			$repl['date:year'] = date("Y", $v['time']);
			$repl['date:hour'] = date("H", $v['time']);
			$repl['date:minute'] = date("i", $v['time']);
			$repl['date:second'] = date("s", $v['time']);
			$repl['user:login'] = $v['user'];
			$repl['user:name'] = $s->users[$v['user']]['name'];
			$repl['user:mail'] = $s->users[$v['user']]['mail'];
			$repl['user:icq'] = $s->users[$v['user']]['icq'];
			$repl['user:field1'] = $s->users[$v['user']]['field1'];
			$repl['user:field2'] = $s->users[$v['user']]['field2'];
			$repl['user:field3'] = $s->users[$v['user']]['field3'];
			$repl['user:posts'] = $s->users[$v['user']]['posts'];
			if ($v['data']['f'] && $this->mostrar_link_noticia_completa) {$repl['news:contents'] .= $this->tpl("fnews_link", $repl); }
			$final = $this->tpl($tpl, $repl);
			$final = $this->parse_date_adv($final, $v['time'], "date:");
			$news = $final;
			if (strpos($this->tpl("daygroup"), "{news}") !== FALSE && $this->mostrar_agrupamento_diario) {
				$repl = array(); $k = explode("/", date("d/m/Y", $v['time']), 3);
				$repl['news'] = $news;
				$repl['date:day'] = $k[0];
				$repl['date:month'] = $k[1];
				$repl['date:year'] = $k[2];
				$final = $this->tpl("daygroup", $repl);
				$final = $this->parse_date_adv($final, mktime(0, 0, 0, $k[1], $k[0], $k[2]), "date:");
				echo $final;
			}
			else {echo $news; }
			break;
		}
	}
	// Aliases
	function mostrar_notícia($tpl = "news") {$this->mostrar_noticia($tpl); }
	
	function mostrar_noticia_para_impressao() {
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		$this->mostrar_agrupamento_diario = 0;
		$this->mostrar_link_noticia_completa = 0;
		$this->mostrar_noticia("print");
	}
	// Aliases
	function mostrar_notícia_para_impressão() {$this->mostrar_noticia_para_impressao(); }
	
	function mostrar_noticia_completa() {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$comments = array();
		foreach ($this->dbC as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['data']['q']) {continue; }
			if (!$comments[$v['nid']]) {$comments[$v['nid']] = 0; }
			$comments[$v['nid']] += 1;
		}
		
		$i = 0; foreach ($this->dbN as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['id'] != $this->noticia) {continue; }
			if (!$comments[$v['id']]) {$comments[$v['id']] = 0; }
			if ($s->sys['filter']['news']) {$v['title'] = preg_replace($this->filterFind, $this->filterRepl, $v['title']); $v['news'] = preg_replace($this->filterFind, $this->filterRepl, $v['news']); $v['fnews'] = preg_replace($this->filterFind, $this->filterRepl, $v['fnews']); }
			$nohtml = 0; if (!$s->users[$v['user']]['perms'][$this->categoria]['usehtml'] && !$s->users[$v['user']]['perms']['admin']) {$nohtml = 1; }
			$v['news'] = $this->news_parse($v['news'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
			$v['fnews'] = $this->news_parse($v['fnews'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
			$repl = array();
			$repl['system:mzn2dir'] = $s->sys['sys']['dir'];
			$repl['system:thispage'] = $s->req['PHP_SELF'];
			$repl['news:id'] = $v['id'];
			$repl['news:title'] = $v['title']; if ($s->cat[$this->categoria]['news']['cut'] && strlen($repl['news:title']) > $s->cat[$this->categoria]['news']['cut']) {$repl['news:title'] = substr($repl['news:title'], 0, intval($s->cat[$this->categoria]['news']['cut'] - 3)) ."..."; }
			$repl['news:title:nocut'] = $v['title'];
			$repl['news:contents'] = $v['news'];
			$repl['news:full'] = $v['fnews'];
			$repl['news:comments'] = $comments[$v['id']]; if ($v['data']['nc']) {$repl['news:comments'] = "-"; }
			$date = $this->tpl("date"); $date = $this->parse_date($date, $v['time']);
			$repl['news:date'] = $date;
			$repl['date:day'] = date("d", $v['time']);
			$repl['date:month'] = date("m", $v['time']);
			$repl['date:year'] = date("Y", $v['time']);
			$repl['date:hour'] = date("H", $v['time']);
			$repl['date:minute'] = date("i", $v['time']);
			$repl['date:second'] = date("s", $v['time']);
			$repl['user:login'] = $v['user'];
			$repl['user:name'] = $s->users[$v['user']]['name'];
			$repl['user:mail'] = $s->users[$v['user']]['mail'];
			$repl['user:icq'] = $s->users[$v['user']]['icq'];
			$repl['user:field1'] = $s->users[$v['user']]['field1'];
			$repl['user:field2'] = $s->users[$v['user']]['field2'];
			$repl['user:field3'] = $s->users[$v['user']]['field3'];
			$repl['user:posts'] = $s->users[$v['user']]['posts'];
			if ($v['data']['f']) {$repl['news:contents'] .= $this->tpl("fnews_link", $repl); }
			$final = $this->tpl("fnews", $repl);
			$final = $this->parse_date_adv($final, $v['time'], "date:");
			echo $final;
			break;
		}
	}
	// Aliases
	function mostrar_notícia_completa() {$this->mostrar_noticia_completa(); }
	
	function mostrar_comentarios() {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$o1 = $s->simple_string($this->ordenar_por); $o2 = $s->simple_string(substr($this->ordem, 0, 1));
		
		if ($this->busca) {$busca = $s->regex_search($this->busca); }
		if (!$this->pagina) {$this->pagina = 1; }
		
		$ndb = array();
		$i = 0; foreach ($this->dbC as $k => $v) {
			if ($v['cid'] != $this->categoria || $v['nid'] != $this->noticia || $v['data']['q']) {continue; }
			$k = "";
			if ($o1 == "data") {$k .= $v['time']; while (strlen($k) < 20) {$k = "0". $k; } }
			else if ($o1 == "usuario") {$k = $v['data']['n']; }
			$k .= "|". $i;
			$ndb[$k] = $v;
		$i++; }
		
		if ($o2 == "d") {krsort($ndb); }
		else {ksort($ndb); }
		
		$count = count($ndb); $page = $this->pagina; $perpage = $this->porpagina;
		if ($count) {
			if ($perpage == 0) {$iStart = 0; $iEnd = $count; $this->paginas = 1; }
			else {
				$pg_tot = 1; while ($count - $perpage > 0) {$pg_tot++; $count -= $perpage; }
				if ($page > $pg_tot) {$page = $pg_tot; } if ($page < 1) {$page = 1; }
				$iStart = intval(($page - 1) * $perpage); $iEnd = round($page * $perpage);
				$this->paginas = $pg_tot;
			}
			
			$i = 0; foreach ($ndb as $k => $v) {
				if ($i >= $iStart && $i < $iEnd) {
					if ($s->sys['filter']['comments']) {$v['title'] = preg_replace($this->filterFind, $this->filterRepl, $v['title']); $v['comment'] = preg_replace($this->filterFind, $this->filterRepl, $v['comment']); }
					$v['comment'] = $this->news_parse($v['comment'], 0, !$s->cat[$this->categoria]['comments']['mzncode'], !$s->cat[$this->categoria]['comments']['smilies'], 1);
					$repl = array();
					$repl['system:mzn2dir'] = $s->sys['sys']['dir'];
					$repl['system:thispage'] = $s->req['PHP_SELF'];
					$repl['comment:id'] = $v['id'];
					$repl['comment:title'] = $v['title'];
					$repl['comment:contents'] = $v['comment'];
					$date = $this->tpl("date"); $date = $this->parse_date($date, $v['time']);
					$repl['comment:date'] = $date;
					$repl['comment:name'] = $v['data']['n'];
					$repl['comment:mail'] = $v['data']['m'];
					$repl['comment:ip'] = $v['data']['i'];
					$repl['date:day'] = date("d", $v['time']);
					$repl['date:month'] = date("m", $v['time']);
					$repl['date:year'] = date("Y", $v['time']);
					$repl['date:hour'] = date("H", $v['time']);
					$repl['date:minute'] = date("i", $v['time']);
					$repl['date:second'] = date("s", $v['time']);
					$final = $this->tpl("comment", $repl);
					$final = $this->parse_date_adv($final, $v['time'], "date:");
					echo $final;
				}
				$i++;
			}
		}
		else {echo "Não há comentários<br /><br />"; }
	}
	// Aliases
	function mostrar_comentários() {$this->mostrar_comentarios(); }
	
	function mostrar_arquivo ($link) {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$months = array("01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
		$dates = array();
		foreach ($this->dbN as $k => $v) {
			$k = date("Y-m", $v['time']);
			if (!$dates[$k]) {$dates[$k] = 0; } $dates[$k]++;
		}
		ksort($dates);
		foreach ($dates as $date => $count) {
			list($year, $month) = explode("-", $date);
			$repl = array();
			$repl['link:href'] = str_replace("{data}", $date, $link);
			$repl['link:target'] = "_self";
			$repl['link:text'] = $year ." - ". $months[$month];
			echo $this->tpl("link", $repl);
		}
	}
	
	function mostrar_usuarios ($link) {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$users = array();
		foreach ($s->users as $k => $v) {
			$users[$k] = $v['name'];
		}
		asort($users);
		foreach ($users as $id => $user) {
			list($year, $month) = explode("-", $date);
			$repl = array();
			$repl['link:href'] = str_replace("{usuario}", $id, $link);
			$repl['link:target'] = "_self";
			$repl['link:text'] = $user;
			echo $this->tpl("link", $repl);
		}
	}
	// Aliases
	function mostrar_usuários ($link) {$this->mostrar_usuarios($link); }
	
	function mostrar_paginacao ($link, $tpl = 1) {
		global $s, $m;
		global $system_ok; if (!$system_ok) {return; }
		
		if (!$this->pagina) {$this->pagina = 1; }
		
		$link = str_replace("{página}", "{pagina}", $link);
		if (strpos($link, "{pagina}") === FALSE || $this->paginas == 1) {return; }
		
		$res = "";
		
		if ($tpl == 1) {
			if ($this->pagina > 3) {$res .= "<a href=\"". str_replace("{pagina}", 1, $link) ."\" title=\"Ir para a primeira página\">&nbsp;&laquo;&nbsp;</a> "; }
			if ($this->pagina > 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 1), $link) ."\" title=\"Ir para a página anterior\">&nbsp;‹&nbsp;</a> "; }
			if ($this->pagina > 2) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 2), $link) ."\" title=\"Ir para a página ". ($this->pagina - 2) ."\">&nbsp;". ($this->pagina - 2) ."&nbsp;</a> "; }
			if ($this->pagina > 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 1), $link) ."\" title=\"Ir para a página ". ($this->pagina - 1) ."\">&nbsp;". ($this->pagina - 1) ."&nbsp;</a> "; }
			$res .= "<a href=\"#\" title=\"Ir para uma página específica\" onclick=\"var page = ". $this->pagina .", pages = ". $this->paginas .", selected = 1; if (pages > 1 && page == pages) {selected = pages - 1; } else if (pages > 1) {selected = page + 1;} var go = prompt('Escolha uma página para ir.\\nDigite um número entre 1 e '+ pages, selected); if (go) {document.location = '". str_replace("{pagina}", "'+ go +'", $link) ."'; } return false; \">&nbsp;<b>". $this->pagina ."</b>&nbsp;</a> ";
			if ($this->pagina < $this->paginas) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 1), $link) ."\" title=\"Ir para a página ". ($this->pagina + 1) ."\">&nbsp;". ($this->pagina + 1) ."&nbsp;</a> "; }
			if ($this->pagina < $this->paginas - 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 2), $link) ."\" title=\"Ir para a página ". ($this->pagina + 2) ."\">&nbsp;". ($this->pagina + 2) ."&nbsp;</a> "; }
			if ($this->pagina < $this->paginas) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 1), $link) ."\" title=\"Ir para a próxima página\">&nbsp;›&nbsp;</a> "; }
			if ($this->pagina < $this->paginas - 2) {$res .= "<a href=\"". str_replace("{pagina}", $this->paginas, $link) ."\" title=\"Ir para a última página\">&nbsp;&raquo;&nbsp;</a>"; }
		}
		
		else if ($tpl == 2) {
			if ($this->pagina > 3) {$res .= "<a href=\"". str_replace("{pagina}", 1, $link) ."\" title=\"Ir para a primeira página\">&nbsp;&laquo;&nbsp;</a> "; }
			if ($this->pagina > 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 1), $link) ."\" title=\"Ir para a página anterior\">&nbsp;‹&nbsp;</a> "; }
			$res .= "<a href=\"#\" title=\"Ir para uma página específica\" onclick=\"var page = ". $this->pagina .", pages = ". $this->paginas .", selected = 1; if (pages > 1 && page == pages) {selected = pages - 1; } else if (pages > 1) {selected = page + 1;} var go = prompt('Escolha uma página para ir.\\nDigite um número entre 1 e '+ pages, selected); if (go) {document.location = '". str_replace("{pagina}", "'+ go +'", $link) ."'; } return false; \">&nbsp;<b>". $this->pagina ."</b>&nbsp;</a> ";
			if ($this->pagina < $this->paginas) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 1), $link) ."\" title=\"Ir para a próxima página\">&nbsp;›&nbsp;</a> "; }
			if ($this->pagina < $this->paginas - 2) {$res .= "<a href=\"". str_replace("{pagina}", $this->paginas, $link) ."\" title=\"Ir para a última página\">&nbsp;&raquo;&nbsp;</a>"; }
		}
		
		else if ($tpl == 3) {
			$res .= "<a href=\"#\" title=\"Ir para uma página específica\" onclick=\"var page = ". $this->pagina .", pages = ". $this->paginas .", selected = 1; if (pages > 1 && page == pages) {selected = pages - 1; } else if (pages > 1) {selected = page + 1;} var go = prompt('Escolha uma página para ir.\\nDigite um número entre 1 e '+ pages, selected); if (go) {document.location = '". str_replace("{pagina}", "'+ go +'", $link) ."'; } return false; \">Páginas:</a> (". $this->paginas .") ";
			if ($this->pagina > 3) {$res .= "<a href=\"". str_replace("{pagina}", 1, $link) ."\" title=\"Ir para a primeira página\">&laquo; Primeira</a> ... "; }
			if ($this->pagina > 2) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 2), $link) ."\" title=\"Ir para a página ". ($this->pagina - 2) ."\">". ($this->pagina - 2) ."</a> "; }
			if ($this->pagina > 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 1), $link) ."\" title=\"Ir para a página ". ($this->pagina - 1) ."\">". ($this->pagina - 1) ."</a> "; }
			$res .= "<b>[". $this->pagina ."]</b> ";
			if ($this->pagina < $this->paginas) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 1), $link) ."\" title=\"Ir para a página ". ($this->pagina + 1) ."\">". ($this->pagina + 1) ."</a> "; }
			if ($this->pagina < $this->paginas - 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 2), $link) ."\" title=\"Ir para a página ". ($this->pagina + 2) ."\">". ($this->pagina + 2) ."</a> "; }
			if ($this->pagina < $this->paginas - 2) {$res .= "... <a href=\"". str_replace("{pagina}", $this->paginas, $link) ."\" title=\"Ir para a última página\">Última &raquo;</a>"; }
		}
		
		else if ($tpl == 4) {
			$res .= "<select onchange=\"var page = ". $this->pagina .", pages = ". $this->paginas ."; go = this.options[this.selectedIndex].value; if (go) {document.location = '". str_replace("{pagina}", "'+ go +'", $link) ."'; } \">";
			for ($i = 1; $i <= $this->paginas; $i++) {$res .= "<option value=\"". $i ."\""; if ($i == $this->pagina) {$res .= " selected"; } $res .= ">Página ". $i ."</option>"; }
			$res .= "</select>";
		}
		
		else if ($tpl == 5) {
			if ($this->pagina > 1) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina - 1), $link) ."\" title=\"Ir para a página ". ($this->pagina - 1) ."\">Anterior</a> "; }
			else {$res .= "Anterior"; }
			$res .= " | <a href=\"#\" title=\"Ir para uma página específica\" onclick=\"var page = ". $this->pagina .", pages = ". $this->paginas .", selected = 1; if (pages > 1 && page == pages) {selected = pages - 1; } else if (pages > 1) {selected = page + 1;} var go = prompt('Escolha uma página para ir.\\nDigite um número entre 1 e '+ pages, selected); if (go) {document.location = '". str_replace("{pagina}", "'+ go +'", $link) ."'; } return false; \">Página ". $this->pagina ."</a> | ";
			if ($this->pagina < $this->paginas) {$res .= "<a href=\"". str_replace("{pagina}", ($this->pagina + 1), $link) ."\" title=\"Ir para a página ". ($this->pagina + 1) ."\">Próxima</a> "; }
			else {$res .= "Próxima"; }
		}
		
		echo $res;
		
	}
	// Aliases
	function mostrar_paginação ($link, $tpl = 1) {$this->mostrar_paginacao($link, $tpl); }
	
	function mostrar_formulario_email ($args = "", $cancelar = "") {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		if ($args) {$args = "?". $args; }
		if ($cancelar) {
			if ($cancelar == "voltar") {$cancelar = "history.back(); "; }
			else if ($cancelar == "fechar") {$cancelar = "window.close(); "; }
			else {$cancelar = "location.href = '"+ $s->quote_safe($cancelar) +"'; "; }
		}
		if (!$this->noticia) {
			echo "ID faltando!<br />";
		}
		else {
			$res .= "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\" src=\"". $s->sys['sys']['dir'] ."/mzn2.js\"></scr"."ipt>\n";
			$res .= "<form style=\"margin:0px; \" name=\"MZn2_FormMail\" action=\"". $s->req['PHP_SELF'] . $args ."\" method=\"post\" autocomplete=\"off\" onsubmit=\"if (checkFields(this, 'mzn[from][name]', 'mzn[from][mail]:mail', 'mzn[to][name]', 'mzn[to][mail]:mail', 'mzn[subject]')) {return true; document.getElementById('btSub').disabled = '1'; } else {alert('Por favor, preencha corretamente todos os campos em negrito.'); return false; } \">\n";
			$res .= "	<input type=\"hidden\" name=\"mzn[id]\" value=\"". $this->noticia ."\" />\n";
			$res .= "	<table width=\"450\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"text-align:left; \" id=\"MZn2\">\n";
			$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr><td valign=\"top\" align=\"left\" width=\"222\"><b>Seu nome</b><br /><input type=\"text\" name=\"mzn[from][name]\" id=\"form_mzn[from][name]\" value=\"\" tabindex=\"1\" class=\"normal\" style=\"width:216px; \" /></td><td width=\"5\"></td><td valign=\"top\" align=\"left\" width=\"222\"><b>Seu e-mail</b><br /><input type=\"text\" name=\"mzn[from][mail]\" id=\"form_mzn[from][mail]\" value=\"\" tabindex=\"2\" class=\"normal\" style=\"width:216px; \" /></td></tr></table></td></tr>\n";
			$res .= "		<tr><td height=\"4\"></td></tr>\n";
			$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr><td valign=\"top\" align=\"left\" width=\"222\"><b>Nome do seu amigo(a)</b><br /><input type=\"text\" name=\"mzn[to][name]\" id=\"form_mzn[to][name]\" value=\"\" tabindex=\"3\" class=\"normal\" style=\"width:216px; \" /></td><td width=\"5\"></td><td valign=\"top\" align=\"left\" width=\"222\"><b>E-mail do seu amigo(a)</b><br /><input type=\"text\" name=\"mzn[to][mail]\" id=\"form_mzn[to][mail]\" value=\"\" tabindex=\"4\" class=\"normal\" style=\"width:216px; \" /></td></tr></table></td></tr>\n";
			$res .= "		<tr><td height=\"4\"></td></tr>\n";
			$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr><td valign=\"top\" align=\"left\" width=\"450\"><b>Assunto</b><br /><input type=\"text\" name=\"mzn[subject]\" id=\"form_mzn[subject]\" value=\"\" tabindex=\"5\" class=\"normal\" style=\"width:444px; \" /></td></tr></table></td></tr>\n";
			$res .= "		<tr><td><hr color=\"#000000\" noshade size=\"1\" /></td></tr>\n";
			$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formFooter\"><tr><td valign=\"top\" align=\"right\"><button id=\"btSub\" type=\"submit\" class=\"submit\" tabindex=\"6\" accesskey=\"e\" title=\"Tecla de atalho: Alt+E\">Enviar</button>"; if ($cancelar) {$res .= "&nbsp;<button type=\"button\" onclick=\"". $cancelar ."\" tabindex=\"7\">Cancelar</button>"; } $res .= "</td></tr></table></td></tr>\n";
			$res .= "	</table>\n";
			$res .= "</form>\n";
			echo $res;
		}
	}
	// Aliases
	function mostrar_formulário_email($i_name, $i_value, $cancelar = "") {$this->mostrar_formulário_email($i_name, $i_value, $cancelar); }
	
	function enviar_email () {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$id = $s->req['mzn']['id'];
		if (!$s->req['mzn']['from']['name'] || !$s->req['mzn']['from']['mail'] || !$s->req['mzn']['to']['name'] || !$s->req['mzn']['to']['mail'] || !$s->req['mzn']['subject']) {
			echo "Por favor preencha todos os campos em negrito!";
		}
		else if (!$id) {
			echo "ID faltando!";
		}
		else {
			$this->noticia = $id;
			
			$comments = array();
			foreach ($this->dbC as $k => $v) {
				if ($v['cid'] != $this->categoria || $v['data']['q']) {continue; }
				if (!$comments[$v['nid']]) {$comments[$v['nid']] = 0; }
				$comments[$v['nid']] += 1;
			}
			
			$i = 0; foreach ($this->dbN as $k => $v) {
				if ($v['cid'] != $this->categoria || $v['id'] != $this->noticia) {continue; }
				if (!$comments[$v['id']]) {$comments[$v['id']] = 0; }
				if ($s->sys['filter']['news']) {$v['title'] = preg_replace($this->filterFind, $this->filterRepl, $v['title']); $v['news'] = preg_replace($this->filterFind, $this->filterRepl, $v['news']); $v['fnews'] = preg_replace($this->filterFind, $this->filterRepl, $v['fnews']); }
				$nohtml = 0; if (!$s->users[$v['user']]['perms'][$this->categoria]['usehtml'] && !$s->users[$v['user']]['perms']['admin']) {$nohtml = 1; }
				$v['news'] = $this->news_parse($v['news'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
				$v['fnews'] = $this->news_parse($v['fnews'], $v['data']['b'], $v['data']['c'], $v['data']['s'], $nohtml);
				$repl = array();
				$repl['system:mzn2dir'] = $s->sys['sys']['dir'];
				$repl['system:thispage'] = $s->req['PHP_SELF'];
				$repl['news:id'] = $v['id'];
				$repl['news:title'] = $v['title']; if ($s->cat[$this->categoria]['news']['cut'] && strlen($repl['news:title']) > $s->cat[$this->categoria]['news']['cut']) {$repl['news:title'] = substr($repl['news:title'], 0, intval($s->cat[$this->categoria]['news']['cut'] - 3)) ."..."; }
				$repl['news:title:nocut'] = $v['title'];
				$repl['news:contents'] = $v['news'];
				$repl['news:full'] = $v['fnews'];
				$repl['news:comments'] = $comments[$v['id']]; if ($v['data']['nc']) {$repl['news:comments'] = "-"; }
				$date = $this->tpl("date"); $date = $this->parse_date($date, $v['time']);
				$repl['news:date'] = $date;
				$repl['date:day'] = date("d", $v['time']); $repl['date:month'] = date("m", $v['time']); $repl['date:year'] = date("Y", $v['time']); $repl['date:hour'] = date("H", $v['time']); $repl['date:minute'] = date("i", $v['time']); $repl['date:second'] = date("s", $v['time']);
				$repl['user:login'] = $v['user'];
				$repl['user:name'] = $s->users[$v['user']]['name'];
				$repl['user:mail'] = $s->users[$v['user']]['mail'];
				$repl['user:icq'] = $s->users[$v['user']]['icq'];
				$repl['user:field1'] = $s->users[$v['user']]['field1'];
				$repl['user:field2'] = $s->users[$v['user']]['field2'];
				$repl['user:field3'] = $s->users[$v['user']]['field3'];
				$repl['user:posts'] = $s->users[$v['user']]['posts'];
				break;
			}
			$repl['mail:from_name'] = $s->req['mzn']['from']['name'];
			$repl['mail:from_mail'] = $s->req['mzn']['from']['mail'];
			$repl['mail:to_name'] = $s->req['mzn']['to']['name'];
			$repl['mail:to_mail'] = $s->req['mzn']['to']['mail'];
			$repl['mail:subject'] = $s->req['mzn']['subject'];
			$final = $this->tpl("mailnews", $repl);
			$final = $this->parse_date_adv($final, $v['time'], "date:");
			if (!$s->cfg['ver']['demo']) {
				$mailBody = $final;
				$mailSent = @mail($s->req['mzn']['to']['name'] ." <". $s->req['mzn']['to']['mail'] .">", "[MZn²] ". $s->req['mzn']['subject'], $mailBody, "From: ". $s->req['mzn']['from']['name'] ." <". $s->req['mzn']['from']['mail'] .">\r\nContent-type: text/html");
			} else {$mailSent = 1; }
			if ($mailSent) {echo "O e-mail foi enviado!"; }
			else {echo "Não foi possível enviar o e-mail. Tente novamente mais tarde."; }
		}
	}
	
	function mostrar_formulario_comentario ($args = "") {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		if ($args) {$args = "?". $args; }
		if (!$s->cat[$this->categoria]['comments']['active']) {echo "Os comentários não estão<br />ativados nesta categoria<br />"; }
		else if (!$this->noticia) {
			echo "<b>ID faltando!</b><br /><br />";
		}
		else if (!$this->verify_ip()) {
			echo "<b>O seu IP foi bloqueado!</b><br /><br />";
		}
		else {
			$ok = 0;
			foreach ($this->dbN as $k => $v) {
				if ($v['cid'] != $this->categoria || $v['data']['q'] || ($v['data']['t'] && $s->cfg['time'] < $v['time']) || $v['id'] != $this->noticia) {continue; }
				$ok = 1; if ($v['data']['nc']) {$ok = 2; } else if ($v['data']['cx'] && $v['time'] + ($v['data']['cx'] * 86400) < $s->cfg['time']) {$ok = 3; }
			}
			if (!$ok) {echo "<b>ID inválido!</b><br /><br />"; }
			else if ($ok == 2) {echo "A adição de comentários<br />foi bloqueada<br /><br />"; }
			else if ($ok == 3) {echo "O período de adição de<br />comentários expirou<br /><br />"; }
			else {
				$res .= "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\" src=\"". $s->sys['sys']['dir'] ."/mzn2.js\"></scr"."ipt>\n";
				$res .= "<form style=\"margin:0px; \" name=\"MZn2_AddComment\" action=\"". $s->req['PHP_SELF'] . $args ."\" method=\"post\" autocomplete=\"off\" onsubmit=\"if (checkFields(this, 'mzn[n]'"; if ($s->cat[$this->categoria]['comments']['req_mail']) {$res .= ", 'mzn[m]:mail'"; } if ($s->cat[$this->categoria]['comments']['req_title']) {$res .= ", 'mzn[title]'"; } $res .= ", 'mzn[comment]')) {return true; document.getElementById('btSub').disabled = '1'; } else {alert('Por favor, preencha corretamente todos os campos em negrito.'); return false; } \">\n";
				if ($args) {$res .= "	<input type=\"hidden\" name=\"args\" value=\"". $s->quote_safe($args) ."\" />\n"; }
				$res .= "	<input type=\"hidden\" name=\"mzn[id]\" value=\"". $this->noticia ."\" />\n";
				$res .= "	<input type=\"hidden\" name=\"mzn[comment]\" value=\"\" />\n";
				$res .= "	<table width=\"450\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"text-align:left; \">\n";
				$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr><td valign=\"top\" align=\"left\" width=\"222\"><b>Seu nome</b><br /><input type=\"text\" name=\"mzn[n]\" id=\"form_mzn[n]\" value=\"\" tabindex=\"1\" class=\"normal\" style=\"width:218px; \" onchange=\"form_changed = 1; \" /></td><td width=\"5\"><img src=\"". $s->sys['sys']['dir'] ."/img/_blank.gif\" width=\"5\" height=\"1\" border=\"0\" alt=\"\" /></td><td valign=\"top\" align=\"left\" width=\"222\">"; if ($s->cat[$this->categoria]['comments']['req_mail']) {$res .= "<b>Seu e-mail</b>"; } else {$res .= "Seu e-mail"; } $res .= "<br /><input type=\"text\" name=\"mzn[m]\" id=\"form_mzn[m]\" value=\"\" tabindex=\"2\" class=\"normal\" style=\"width:218px; \" onchange=\"form_changed = 1; \" /></td></tr></table></td></tr>\n";
				$res .= "		<tr><td height=\"4\"></td></tr>\n";
				if ($s->cat[$this->categoria]['comments']['field1'] || $s->cat[$this->categoria]['comments']['field2']) {
					$width = ($s->cat[$this->categoria]['comments']['field1'] && $s->cat[$this->categoria]['comments']['field2'])? 222 : 450;
					$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr>";
					if ($s->cat[$this->categoria]['comments']['field1']) {$res .= "<td valign=\"top\" align=\"left\" width=\"". $width ."\">". $s->cat[$this->categoria]['comments']['field1'] ."<br /><input type=\"text\" name=\"mzn[f1]\" id=\"form_mzn[f1]\" value=\"\" tabindex=\"3\" class=\"normal\" style=\"width:". ($width - 4) ."px; \" onchange=\"form_changed = 1; \" /></td>"; }
						if ($s->cat[$this->categoria]['comments']['field1'] && $s->cat[$this->categoria]['comments']['field2']) {$res .= "<td width=\"5\"><img src=\"". $s->sys['sys']['dir'] ."/img/_blank.gif\" width=\"5\" height=\"1\" border=\"0\" alt=\"\" /></td>"; }
					if ($s->cat[$this->categoria]['comments']['field2']) {$res .= "<td valign=\"top\" align=\"left\" width=\"". $width ."\">". $s->cat[$this->categoria]['comments']['field2'] ."<br /><input type=\"text\" name=\"mzn[f2]\" id=\"form_mzn[f2]\" value=\"\" tabindex=\"4\" class=\"normal\" style=\"width:". ($width - 4) ."px; \" onchange=\"form_changed = 1; \" /></td>"; }
					$res .= "</tr></table></td></tr>\n";
					$res .= "		<tr><td height=\"4\"></td></tr>\n";
				}
				$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr><td valign=\"top\" align=\"left\" width=\"450\">"; if ($s->cat[$this->categoria]['comments']['req_title']) {$res .= "<b>Título</b>"; } else {$res .= "Título"; } $res .= "<br /><input type=\"text\" name=\"mzn[title]\" id=\"form_mzn[title]\" value=\"\""; if ($s->cat[$this->categoria]['comments']['limit_title']) {$res .= " maxlength=\"". $s->cat[$this->categoria]['comments']['limit_title'] ."\""; } $res .=" tabindex=\"5\" class=\"normal\" style=\"width:446px; \" onchange=\"form_changed = 1; \" /></td></tr></table></td></tr>\n";
				$res .= "		<tr><td height=\"4\"></td></tr>\n";
				$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formItem\"><tr><td valign=\"top\" align=\"left\" width=\"505\"><iframe src=\"". $s->sys['sys']['dir'] ."/wait.php?sleep=1&g=". urlencode($s->sys['sys']['dir'] ."/index.php?sec=edv&act=comments&limit=". $s->cat[$this->categoria]['comments']['limit_comment'] ."&mznCode=". $s->cat[$this->categoria]['comments']['mzncode'] ."&smilies=". $s->cat[$this->categoria]['comments']['smilies']) ."\" tabindex=\"6\" name=\"edv_comment\" scrolling=\"no\" width=\"450\" height=\"172\" marginwidth=\"0\" marginheight=\"0\" frameborder=\"no\"></iframe></td></tr></table></td></tr>\n";
				$res .= "		<tr><td><hr color=\"#000000\" noshade size=\"1\" /></td></tr>\n";
				$res .= "		<tr><td valign=\"top\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" class=\"formFooter\"><tr><td valign=\"top\" align=\"right\"><button id=\"btSub\" type=\"submit\" class=\"submit\" tabindex=\"7\" accesskey=\"a\" title=\"Tecla de atalho: Alt+A\">Adicionar</button></td></tr></table></td></tr>\n";
				$res .= "	</table>\n";
				$res .= "</form>\n";
				echo $res;
			}
		}
	}
	// Aliases
	function mostrar_formulário_comentário ($args = "") {$this->mostrar_formulario_comentario($args); }
	
	function adicionar_comentario () {
		global $s, $m;
		global $system_ok; if (!$system_ok) {echo $this->msg; return; }
		
		$id = $s->req['mzn']['id'];
		if (!$s->cat[$this->categoria]['comments']['active']) {echo "Os comentários não estão<br />ativados nesta categoria<br />"; }
		else if (!$id) {
			echo "ID faltando!<br /><br />";
		}
		else if (!$this->verify_ip()) {
			echo "<b>O seu IP foi bloqueado!</b><br /><br />";
		}
		else if (!$s->req['HTTP_REFERER']) {echo "Referência inválida!<br /><br />"; }
		else {
			$ok = 0; $login_ok = 0;
			if ($s->req['mzn']['login_usr'] && $s->req['mzn']['login_pwd'] && !$this->test_login($s->users[$s->req['mzn']['login_usr']]['pwd'], $s->req['mzn']['login_pwd'])) {$login_ok = 1; }
			foreach ($this->dbN as $k => $v) {
				if ($v['cid'] != $this->categoria || $v['data']['q'] || ($v['data']['t'] && $s->cfg['time'] < $v['time']) || $v['id'] != $id) {continue; }
				$ok = 1; if ($v['data']['nc']) {$ok = 2; } else if ($v['data']['cx'] && $v['time'] + ($v['data']['cx'] * 86400) < $s->cfg['time']) {$ok = 3; }
			}
			if (!$ok) {echo "<b>ID inválido!</b><br /><br />"; }
			else if ($ok == 2) {echo "A adição de comentários<br />foi bloqueada<br /><br />"; }
			else if ($ok == 3) {echo "O período de adição de<br />comentários expirou<br /><br />"; }
			else if ($s->req['last'] && ($s->req['last'] + $s->sys['visitor']['floodint']) > $s->cfg['time']) {echo "Por favor aguarde mais ". (($s->req['last'] + $s->sys['visitor']['floodint']) - $s->cfg['time']) ." segundos antes de postar outro comentário.<br />"; }
			else if (!$s->req['mzn']['n'] || (!$s->req['mzn']['m'] && $s->cat[$this->categoria]['comments']['req_mail']) || (!$s->req['mzn']['title'] && $s->cat[$this->categoria]['comments']['req_title']) || !$s->req['mzn']['comment']) {echo "Preencha todos os campos em negrito!<br />"; }
			else if (!$this->verify_name($s->req['mzn']['n']) && !$login_ok) {echo "<b>Nome reservado!</b><br /><br />Você só pode usar este nome se for este usuário ou um administrador.<br /><br /><a href=\"#\" onclick=\"window.open('". $s->sys['sys']['dir'] ."/index.php?sec=login&act=popup&form=MZn2_AddComment&ufield=". urlencode("mzn[login_usr]") ."&pfield=". urlencode("mzn[login_pwd]") ."', 'MZn2login', 'width=400,height=155,top=20,left=20,directories=no,location=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no'); return false; \">Clique aqui para efetuar login.</a><form name=\"MZn2_AddComment\" action=\"". $s->req['PHP_SELF'] . $s->req['args'] ."\" method=\"post\"><input type=\"hidden\" name=\"mzn[back]\" value=\"". $s->quote_safe($s->req['HTTP_REFERER']) ."\" /><input type=\"hidden\" name=\"mzn[login_usr]\" value=\"\" /><input type=\"hidden\" name=\"mzn[login_pwd]\" value=\"\" /><input type=\"hidden\" name=\"mzn[id]\" value=\"". $s->quote_safe($id) ."\" /><input type=\"hidden\" name=\"mzn[n]\" value=\"". $s->quote_safe($s->req['mzn']['n']) ."\" /><input type=\"hidden\" name=\"mzn[m]\" value=\"". $s->quote_safe($s->req['mzn']['m']) ."\" /><input type=\"hidden\" name=\"mzn[f1]\" value=\"". $s->quote_safe($s->req['mzn']['f1']) ."\" /><input type=\"hidden\" name=\"mzn[f2]\" value=\"". $s->quote_safe($s->req['mzn']['f2']) ."\" /><input type=\"hidden\" name=\"mzn[title]\" value=\"". $s->quote_safe($s->req['mzn']['title']) ."\" /><input type=\"hidden\" name=\"mzn[comment]\" value=\"". $s->quote_safe($s->req['mzn']['comment']) ."\" /></form>"; }
			else {
				if (!$s->cfg['ver']['demo']) {
					$nl = array();
					$nl['id'] = substr(md5(rand()*time()), 0, 10);
					$nl['cid'] = $this->categoria;
					$nl['nid'] = $id;
					$nl['time'] = $s->cfg['time'];
					$nl['title'] = $s->req['mzn']['title']; if ($s->cat[$this->categoria]['comments']['limit_title'] && strlen($s->req['mzn']['title']) > $s->cat[$this->categoria]['comments']['limit_title']) {$s->req['mzn']['title'] = substr($s->req['mzn']['title'], 0, $s->cat[$this->categoria]['comments']['limit_title']); }
					$nl['comment'] = $s->req['mzn']['comment']; if ($s->cat[$this->categoria]['comments']['limit_comment'] && strlen($s->req['mzn']['comment']) > $s->cat[$this->categoria]['comments']['limit_comment']) {$s->req['mzn']['comment'] = substr($s->req['mzn']['comment'], 0, $s->cat[$this->categoria]['comments']['limit_comment']); }
					$nl['data']['n'] = $s->req['mzn']['n'];
					$nl['data']['m'] = $s->req['mzn']['m'];
					$nl['data']['i'] = $this->ip;
					$nl['data']['f1'] = $s->req['mzn']['f1'];
					$nl['data']['f2'] = $s->req['mzn']['f2'];
					$nl['data']['q'] = $s->cat[$this->categoria]['comments']['queue'];
					
					$db = $s->db_table_open($s->cfg['file']['comments']);
					$db['data'][count($db['data'])] = $nl;
					$s->db_table_save($s->cfg['file']['comments'], $db);
				}
				
				if (!$s->req['mzn']['back']) {$s->req['mzn']['back'] = $s->req['HTTP_REFERER']; }
				echo "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\">document.cookie = \"last=". $s->cfg['time'] ."; expires=Fri, 31 Dec 2004 23:59:59 UTC\"; location.replace(\"". addslashes($s->req['mzn']['back'] ."#Comentario_". $nl['id']) ."\"); </scr"."ipt>";
			}
		}
	}
	
}

?>
