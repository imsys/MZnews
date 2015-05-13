<?php

//-----------------------------------------------------------------------------
// WsSys 1.0.022                                               2004-02-14 11h02
//-----------------------------------------------------------------------------

class WsSys {
	
	//-----------------------------------------------------------------------------
	// WsSys Global
	//-----------------------------------------------------------------------------
	
	var $conf  = array();
	var $err   = array();
	var $req   = array();
	var $cache = array();
	var $debug = 0;
	
	// Função de criação do sistema
	function WsSys () {
		$this->parse_request();
		$this->err = $this->set_errors();
	}
	
	function error ($code, $file = "", $debug = "") {
		$extras = array(); if ($file) {$extras['Arquivo'] =  "path:". $file; }
		if (!$this->debug) {$debug = ""; }
		$this->show_error($this->err[$code], 1, $extras, $debug);
	}
	
	// Função geral de erro
	function show_error ($msg = "Erro desconhecido", $fatal = 0, $extras = array(), $debug = "") {
		$msg = preg_replace("/\[link=([^]]+)]([^\[]+)\[\/link]/i", "<a href=\"\\1\" target=\"_blank\" style=\"color:#CC0000; \">\\2</a>", $msg);
		echo "<!-- WsSys - Erro do sistema --><div style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; color:#CC0000; background-color:#FFFFFF; padding:8px; padding-left:10px; margin:5px; margin-top:10px; \">";
			echo "<h4 style=\"margin:0px; \">WsSys - Erro"; if ($fatal) {echo " Fatal"; } echo "</h4>";
			echo $msg;
			if (count($extras) > 0) {
				echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:8pt; color:#CC0000; \">";
				echo "<tr><td height=\"5\"></td></tr>";
				foreach ($extras as $k => $v) {
					if (preg_match("/^path:/", $v)) {$v = preg_replace("/^path:/", "", $v); $v = str_replace("PATH". $AbsPath, "", "PATH". $v); $v = str_replace("\\", "/", $v); $v = preg_replace("/^\//", "", $v); }
					echo "<tr><td><b>". $k .":</b>&nbsp;". $v ."</td></tr>";
				}
				echo "</table>";
			}
			if ($debug) {echo "<pre style=\"color:#000000; background-color:#EEEEEE; padding:5px; \">". $this->quote_safe(print_r($debug, 1)) ."</pre>"; }
		echo "</div>";
		if ($fatal) {exit; }
	}
	
	// Passa as variáveis de sistema ($_REQUEST) de um modo mais prático e seguro
	function parse_request () {
		global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS;
		if (count($HTTP_GET_VARS) > 0)    {foreach ($HTTP_GET_VARS    as $k => $v) {$this->req[$k] = $this->fetch_request($v, 1); } }
		if (count($HTTP_POST_VARS) > 0)   {foreach ($HTTP_POST_VARS   as $k => $v) {$this->req[$k] = $this->fetch_request($v, 1); } }
		if (count($HTTP_COOKIE_VARS) > 0) {foreach ($HTTP_COOKIE_VARS as $k => $v) {$this->req[$k] = $this->fetch_request($v, 1); } }
		if (count($HTTP_SERVER_VARS) > 0) {foreach ($HTTP_SERVER_VARS as $k => $v) {$this->req[$k] = $this->fetch_request($v, 0); } }
	}
	// Complemento da função [parse_request]
	function fetch_request ($now_var, $check_strip = 0) {
		global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_COOKIE_VARS, $HTTP_SERVER_VARS;
		if (is_array($now_var)) {
			$return = array();
			foreach ($now_var as $k => $v) {
				if (get_magic_quotes_gpc() == 1 && $check_strip) {$k = stripslashes($k); }
				$return[$k] = $this->fetch_request($v, $check_strip);
			}
			return $return;
		}
		else {if (get_magic_quotes_gpc() == 1 && $check_strip) {$now_var = stripslashes($now_var); } return $now_var; }
	}
	
	// Substitui uma lista de variáveis entre chaves dentro de uma string
	function replace_vars ($var, $list) {
		if (is_array($list)) {
			foreach ($list as $k => $v) {$var = str_replace("{". $k ."}", $v, $var); }
		}
		return $var;
	}
	
	
	//-----------------------------------------------------------------------------
	// WsSys File
	//-----------------------------------------------------------------------------
	
	function clear_cache () {
		$this->cache = array();
	}
	
	function file_create ($name, $php_protected = 0) {
		if (file_exists($name)) {$this->error("00:11", $name); return ""; }
		$this->file_write($name, "", $php_protected);
	}
	
	function file_read ($name, $php_protected = 0, $create = 0) {
		if (!file_exists($name)) {
			if ($create) {$this->file_create($name, $php_protected); }
			else {$this->error("00:00", $name); return ""; }
		}
		
		if ($this->cache[addslashes($name)] && $this->cache[addslashes($name)][0] == @filesize($name)) {return $this->cache[addslashes($name)][1]; }
		else {
			$fs = @fopen($name, "r");
			if (!$fs) {$this->error("00:02"); return ""; }
			$contents = @fread($fs, filesize($name)); if ($contents === FALSE) {$this->error("00:03", $name); return ""; }
			$x_act    = @fclose($fs);                 if ($x_act === FALSE)    {$this->error("00:04", $name); return ""; }
			
			$contents = str_replace("\r\n", "\n", $contents);
			if ($php_protected) {$contents = preg_replace("/<"."\?"."php[^\n]+\?".">\n/i", "", $contents); }
			
			$this->cache[addslashes($name)][0] = @filesize($name);
			$this->cache[addslashes($name)][1] = $contents;
			
			return $contents;
		}
	}
	
	function file_write ($name, $contents, $php_protected = 0) {
		if ($php_protected) {$contents = "<"."?"."php if (!defined(\"WsSys_Token\")) {echo \"<title>ERRO - Acesso Negado!</title><font face=\\\"verdana\\\" size=\\\"2\\\" color=\\\"#CC0000\\\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>\"; exit; } ?".">\n". $contents; }
		$contents = str_replace("\r\n", "\n", $contents);
		
		$temp  = @tempnam($this->cfg['path']['data'], "tmp");
		                                  if ($temp === FALSE)  {$this->error("00:05", $name); return ""; }
		$fs    = @fopen($temp, "w");      if ($fs === FALSE)    {$this->error("00:06", $name); return ""; }
		$x_act = @fputs($fs, $contents);  if ($x_act === FALSE) {$this->error("00:07", $name); return ""; }
		$x_act = @fclose($fs);            if ($x_act === FALSE) {$this->error("00:08", $name); return ""; }
		$x_act = @copy($temp, $name);     if ($x_act === FALSE) {$this->error("00:09", $name); return ""; }
		$x_act = @unlink($temp);          if ($x_act === FALSE) {$this->error("00:10", $name); return ""; }
		
		clearstatcache();
	}
	
	
	//-----------------------------------------------------------------------------
	// WsSys DB
	//-----------------------------------------------------------------------------
	
	// SimpleVars
	function vars_import ($string) {
		if (!$string || !is_string($string)) {return array(); }
		$res = array(); $array = explode(";", $string);
		foreach ($array as $v) {
			if ($v === "") {continue; }
			list ($name,$value) = explode(":", $v, 2);
			$res[urldecode($name)] = urldecode($value);
		}
		return $res;
	}
	function vars_export ($array) {
		if (!is_array($array) || count($array) == 0) {return ""; }
		$res = "";
		foreach ($array as $k => $v) {$res .= urlencode($k) .":". urlencode($v) .";"; }
		return preg_replace("/;$/", "", $res);
	}
	
	// DB Vars
	function db_vars_create ($file) {
		$this->db_vars_save($file, array());
	}
	function db_vars_open ($file, $skip = 0) {
		if (!file_exists($file)) {
			if ($skip != 0) {return array(); }
			else {$this->error("01:00", $file); return ""; }
		}
		$fs = @file($file); $line = $fs[1]; $line = trim($line); $line = str_replace("<"."?php // ", "", $line); $line = explode(" ", $line);
		if ( $line[0] != "WsDB_Vars" || $line[2] != md5(basename($file) . $line[1] . $line[1] . @filesize($file)) ) {$this->error("01:01", $file); return ""; }
		ob_start(); define("WsSys_Token", 1); @include $file; ob_end_clean();
		return $c;
	}
	function db_vars_save ($file, $array) {
		$contents = $this->db_vars_export($array);
		$rand = substr(md5(time()*rand()), 0, 4);
		$security = md5(basename($file) . $rand . $rand . (strlen($contents) + 275));
		$this->file_write($file, "<"."?php // WsDB_Vars ". $rand ." ". $security ."\n\$c = array();\n". $contents ."?".">", 1);
	}
	function db_vars_export ($array, $pre_var = "") {
		if (is_array($array)) {
			$res = "";
			if ($pre_var) {$pre_var .= "']['"; }
			foreach ($array as $k => $v) {
				$k = addslashes($k);
				$res .= $this->db_vars_export($v, $pre_var . $k);
			}
			return $res;
		}
		else {
			$res = ""; $v_arr = array();
			if (strlen($array) > 500) {
				while (strlen($array) > 500) {
					$v_arr[] = substr($array, 0, 500);
					$array = substr($array, 500);
				}
				$v_arr[] = $array;
				$i = 0; foreach ($v_arr as $v) {
					$v = str_replace("\\", "\\\\", $v); $v = str_replace("\"", "\\\"", $v); $v = str_replace("\r\n", "\n", $v); $v = str_replace("\n", "\\n", $v); $v = str_replace("\t", "\\t", $v);
					$v = str_replace("\$", "\\\$", $v); $v = str_replace("<"."?php", "<\".\"?php", $v); $v = str_replace("?".">", "?\".\">", $v);
					if ($i == 0) {$res .= "\$c['". $pre_var ."'] = \"". $v ."\";\n"; }
					else {$res .= "\$c['". $pre_var ."'] .= \"". $v ."\";\n"; }
				$i++; }
			}
			else {
				$v = $array;
				$v = str_replace("\\", "\\\\", $v); $v = str_replace("\"", "\\\"", $v); $v = str_replace("\r\n", "\n", $v); $v = str_replace("\n", "\\n", $v); $v = str_replace("\t", "\\t", $v);
				$v = str_replace("\$", "\\\$", $v); $v = str_replace("<"."?php", "<\".\"?php", $v); $v = str_replace("?".">", "?\".\">", $v);
				$res = "\$c['". $pre_var ."'] = \"". $v ."\";\n";
			}
			return $res;
		}
	}
	
	// DB Table
	function db_table_create ($file, $header, $overwrite = 0) {
		if (file_exists($file) && !$overwrite) {$this->error("01:02", $file); return ""; }
		$this->file_write($file, $header, 1);
	}
	function db_table_open ($file, $header = "") {
		if (!file_exists($file)) {
			if ($header != "") {$this->db_table_create($file, $header); }
			else {$this->error("01:00", $file); return ""; }
		}
		$res = array(); $res['header'] = array(); $res['data'] = array();
		$db = trim($this->file_read($file, 1)); $db = explode("\n", $db);
		$i = 0; foreach ($db as $row) {
			$row = explode("|", $row);
			if ($i == 0) {$res['header'] = $row; }
			else {
				$resRow = $i - 1;
				$j = 0; foreach ($row as $col) {
					if (preg_match("/:vars$/i", $res['header'][$j])) {$res['data'][$resRow][preg_replace("/:vars$/i", "", $res['header'][$j])] = $this->vars_import($col); }
					else {$col = str_replace("¦", "|", $col); $col = str_replace("\\n", "\n", $col); $col = str_replace("\\t", "\t", $col); $res['data'][$resRow][$res['header'][$j]] = $col; }
				$j++; }
			}
		$i++; }
		return $res;
	}
	function db_table_save ($file, $db) {
		if (!is_array($db['header']) || !$db['header']) {$this->error("01:01", $file); return ""; }
		$contents = implode("|", $db['header']) ."\n";
		if (is_array($db['data'])) {foreach ($db['data'] as $row) {
			$nr = array();
			foreach ($row as $k => $col) {
				if (is_array($col)) {$nr[$k] = $this->vars_export($col); }
				else {$col = str_replace("|", "¦", $col); $col = str_replace("\r\n", "\n", $col); $col = str_replace("\n", "\\n", $col); $col = str_replace("\t", "\\t", $col); $nr[$k] = $col; }
			}
			$contents .= $this->db_table_line($db['header'], $nr) ."\n";
		} }
		$contents = preg_replace("/\|\n/", "\n", $contents);
		$contents = preg_replace("/\n$/", "", $contents);
		$this->file_write($file, $contents, 1);
	}
	function db_table_line ($headers, $row) {
		$res = array();
		foreach ($headers as $header) {
			if (preg_match("/:vars$/i", $header)) {$header = preg_replace("/:vars$/i", "", $header); }
			$res[count($res)] = $row[$header];
		}
		return implode("|", $res);
	}
	
	
	//-----------------------------------------------------------------------------
	// WsSys Format
	//-----------------------------------------------------------------------------
	
	// Remove todos os caracteres não-alfanuméricos, transformando caracteres especiais em simples
	function simple_string ($str, $super_simple = 0) {
		$str = strtolower($str);
		$str = preg_replace("/[áàãâä]/i", "a", $str); $str = preg_replace("/[éèêë]/i", "e", $str); $str = preg_replace("/[íìîï]/i", "i", $str); $str = preg_replace("/[óòõôö]/i", "o", $str); $str = preg_replace("/[úùûü]/i", "u", $str); $str = preg_replace("/[ç]/i", "c", $str);
		if ($super_simple) {$str = preg_replace("/[^a-z0-9]/i", "", $str); }
		else {$str = preg_replace("/[^a-z0-9\s\.\,\?\*]/i", "", $str); }
		return $str;
	}
	
	// Remove todos os caracteres não-alfanuméricos, transformando caracteres especiais em simples
	function simple_number ($str) {
		$str = preg_replace("/[^0-9\,\.]+/", "", $str); $str = preg_replace("/,/", ".", $str);
		$str = preg_replace("/^0+([0-9])/", "\\1", $str); $str = preg_replace("/0+$/", "", $str); $str = preg_replace("/\.$/", "", $str);
		return $str;
	}
	
	// Adiciona \ antes de \ e ' ou " dependendo do segundo argumento
	function escape ($str, $quote = "\"") {
		$str = str_replace("\\",   "\\\\",       $str);
		$str = str_replace("\r\n", "\n",         $str);
		$str = str_replace("\n",   "\\n",        $str);
		$str = str_replace("\t",   "\\t",        $str);
		$str = str_replace($quote, "\\". $quote, $str);
		return $str;
	}
	
	// Tenta imitar a função 'escape' do JavaScript
	function jsescape ($str) {
		$str = urlencode($str);
		$str = str_replace("+", "%20", $str);
		return $str;
	}
	
	// Permite que qualquer string seja usada numa expressão regular para busca (usando os curingas ? e *)
	function regex_search ($str) {
		// Minúsculas
		$str = strtolower($str);
		
		// Altera caracteres especiais
		$str = str_replace("\\", "\\\\",  $str);
		$str = str_replace("/",  "\/",   $str);
		$str = str_replace(".",  "\\.",   $str);
		$str = str_replace("[",  "\\[",   $str);
		$str = str_replace("]",  "\\]",   $str);
		$str = str_replace("(",  "\\(",   $str);
		$str = str_replace(")",  "\\)",   $str);
		$str = str_replace("+",  "\\+",   $str);
		$str = str_replace("^",  "\\^",   $str);
		$str = str_replace("\$", "\\\$",  $str);
		$str = str_replace("|",  "\\|",   $str);
		
		// Desconsiderar acentos
		$str = str_replace("a", "[aáàãâä]", $str); $str = str_replace("e", "[eéèêë]", $str); $str = str_replace("i", "[iíìîï]", $str); $str = str_replace("o", "[oóòõôö]", $str); $str = str_replace("u", "[uúùûü]", $str); $str = str_replace("c", "[cç]", $str);
		
		// Caracteres especiais da busca
		$str = str_replace("*",  "[a-zA-Z0-9áàãâäéèêëíìîïóòõôöuúùûüç]*", $str);
		$str = str_replace("?",  "[a-zA-Z0-9áàãâäéèêëíìîïóòõôöuúùûüç]",  $str);
		
		return $str;
	}
	
	// Permite que qualquer string seja usada numa expressão regular para busca (usando os curingas ? e *)
	function regex_safe ($str) {
		$str = str_replace("\\", "\\\\", $str);
		$str = str_replace("/",  "\/",   $str);
		$str = str_replace(".",  "\\.",  $str);
		$str = str_replace("[",  "\\[",  $str);
		$str = str_replace("]",  "\\]",  $str);
		$str = str_replace("(",  "\\(",  $str);
		$str = str_replace(")",  "\\)",  $str);
		$str = str_replace("+",  "\\+",  $str);
		$str = str_replace("^",  "\\^",  $str);
		$str = str_replace("\$", "\\\$", $str);
		$str = str_replace("|",  "\\|",  $str);
		$str = str_replace("*",  "\\*",  $str);
		$str = str_replace("?",  "\\?",  $str);
		return $str;
	}
	
	// Permite que uma string fique entre aspas (aplicável desde <input>s a URLs, dentro de formulários)
	function quote_safe ($str) {
		$str = str_replace("&",    "&amp;",  $str);
		$str = str_replace("\r\n", "\n",     $str);
		$str = str_replace("\n",   "&#013;", $str);
		$str = str_replace("\"",   "&quot;", $str);
		$str = str_replace("<",    "&lt;",   $str);
		$str = str_replace(">",    "&gt;",   $str);
		return $str;
	}
	
	// Passa bytes (100 KB) para números (102400)
	function bytes_to_numbers ($byt) {
		$byt = trim($byt);
		$num = preg_replace("/^([0-9\.\,]+)(.*)/", "\\1", $byt); $num = str_replace(",", ".", $num);
		$type = preg_replace("/^([0-9\.\,]+)(\s*)/", "", $byt);  $type = substr(strtolower(trim($type)), 0, 1);
		if ($type == "k") {$num *= 1024; }
		else if ($type == "m") {$num *= 1048576; }
		else if ($type == "g") {$num *= 1073741824; }
		else if ($type == "t") {$num *= 1099511627776; }
		return $num;
	}
	
	// Passa números (102400) para bytes (100 KB)
	function to_bytes ($num, $precision = 2) {
		$types = array("Bytes", "KB", "MB", "GB", "TB"); $typeK = 0;
		while ($types[$typeK] && $num >= 1024) {$num /= 1024; $typeK++; }
		$num = number_format($num, $precision, ",", ""); $num = preg_replace("/^0+([0-9])/", "\\1", $num); $num = preg_replace("/0+$/", "", $num); $num = preg_replace("/,$/", "", $num);
		return $num ." ". $types[$typeK];
	}
	
	// Formata um número em moeda
	function to_money ($num, $str_pre = "", $str_pos = "") {
		$num = str_replace(",", ".", $num);
		$num = number_format($num, 2, ",", " ");
		return $str_pre . $num . $str_pos;
	}
	
	
	//-----------------------------------------------------------------------------
	// WsSys Sets
	//-----------------------------------------------------------------------------
	
	// Define os erros
	function set_errors () {
		$err = array();
		
		// Arquivo
		$err['00:00'] = "Arquivo inexistente.";
		$err['00:01'] = "Impossível criar o arquivo.";
		$err['00:02'] = "Impossível abrir o arquivo.";
		$err['00:03'] = "Impossível obter dados do arquivo.";
		$err['00:04'] = "Impossível fechar o stream do arquivo.";
		$err['00:05'] = "Impossível criar um arquivo temporário.";
		$err['00:06'] = "Impossível abrir o arquivo temporário para escrita.";
		$err['00:07'] = "Impossível inserir dados no arquivo temporário.";
		$err['00:08'] = "Impossível fechar o stream do arquivo temporário.";
		$err['00:09'] = "Impossível copiar o arquivo temporário para o solicitado.";
		$err['00:10'] = "Impossível remover o arquivo temporário.";
		$err['00:11'] = "Arquivo já existe.";
		
		// Banco de dados
		$err['01:00'] = "Banco de dados inexistente.";
		$err['01:01'] = "Banco de dados inválido ou corrompido.";
		$err['01:02'] = "Banco de dados já existe.";
		
		return $err;
	}
	
}

?>
