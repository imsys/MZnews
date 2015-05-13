<?php if (!defined("WsSys_Token")) {echo "<title>ERRO - Acesso Negado!</title><font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

if (!$AbsPath || !@is_dir($AbsPath)) {$AbsPath = dirname(__FILE__); }
if (!$AbsDir) {$AbsDir = dirname($HTTP_SERVER_VARS['PHP_SELF']); $AbsDir = preg_replace("/\/$/", "", $AbsDir); }
$c = array();

// É importante que você não altere a linha que define a versão
// do MZn² para não interferir no processo de atualização (update)
// quando precisar instalar uma versão posterior.
$c['ver']['system'] = "2.0.09";

// Esta configuração foi criada apenas para o site do MZn². Contudo,
// se você deseja providenciar uma versão de demonstração no seu site,
// basta defini-la para "1".
$c['ver']['demo']   = "0";

// Não é necessário alterar nenhuma configuração a partir daqui.

// Diretórios absolutos
$c['dir']['data'] = $AbsDir ."/data";
$c['dir']['img']  = $AbsDir ."/img";

// Caminhos absolutos
$c['path']['data'] = $AbsPath ."/data";
$c['path']['img']  = $AbsPath ."/img";
$c['path']['inc']  = $AbsPath ."/inc";

$c['path']['smilies'] = $c['path']['img'] ."/smilies";
$c['dir']['smilies']  = $c['dir']['img'] ."/smilies";

// Arquivos do sistema
$c['file']['categories'] = $c['path']['data'] ."/categories.php";
$c['file']['comments']   = $c['path']['data'] ."/comments.php";
$c['file']['config']     = $c['path']['data'] ."/config.php";
$c['file']['mzn2_safe']  = $c['path']['data'] ."/mzn2_safe.php";
$c['file']['news']       = $c['path']['data'] ."/news.php";
$c['file']['uploads']    = $c['path']['data'] ."/uploads.php";
$c['file']['users']      = $c['path']['data'] ."/users.php";
$c['file']['session']    = $c['path']['data'] ."/session.php";
$c['file']['skin_cache'] = $c['path']['data'] ."/skin_cache.php";

// Arquivos protegidos
$c['protected']['index.*'] = 1;

// Skin padrão (se houver uma skin configurada este valor é ignorado)
$c['skin'] = "blackfog";

// Modelo do e-mail de recuperação de senha
$c['mail']['lostpwd']['s'] = "[MZn²] Sua nova senha!";
$c['mail']['lostpwd']['b'] = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"pt\" lang=\"pt\">\n\t<head>\n<style type=\"text/css\">\n<!--\na       {color:#000000; text-decoration:none; }\na:hover {color:#000000; text-decoration:underline; }\n\nbody        {color:#000000; background-color:#FFFFFF; }\nbody, table {font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif; font-size:10pt; }\n-->\n</style>\n\t</head>\n\t<body>\n\t\t\n\t\t<h2>MZn&sup2; - Recuperação de senha</h2>\n\t\t\n\t\tVocê solicitou uma recuperação de senha pelo MZn² do site {site:name}.<br />\n\t\t<br />\n\t\t<b>Se você não fez nenhuma solicitação, ignore este e-mail.</b><br />\n\t\t<br />\n\t\tComo as senhas do MZn² são criptografadas, a sua recuperação é impossível, então criamos uma nova senha para você. Assim que você usar a sua senha nova, a senha antiga será substituída pela nova, e vice-versa. Veja abaixo os seus dados para login:<br />\n\t\t<br />\n\t\t<table bgcolor=\"#EEEEEE\" align=\"center\">\n\t\t\t<tr><td align=\"right\"><b>Login:</b></td><td>{user:login}</td></tr>\n\t\t\t<tr><td align=\"right\"><b>Nova senha:</b></td><td>{user:pwd}</td></tr>\n\t\t</table>\n\t\t<br />\n\t\tRecomendamos que você troque esta senha assim que entrar no sistema novamente.<br />\n\t\t<br />\n\t\tEste é um e-mail automático, não o responda!<br />\n\t\t\n\t</body>\n</html>";

// mznCode

// Códigos de substituição
$c['hcode']['0'] = "/\[%s\]/siU";
$c['hcode']['1'] = "/\[%s\](.*)\[\/%s\]/siU";
$c['hcode']['2'] = "/\[%s=([^\]]*)\](.*)\[\/%s\]/siU";
$c['hcode']['3'] = "/\[%s=([^,\]]*),([^\]]*)\](.*)\[\/%s\]/siU";
$c['hcode']['4'] = "/\[%s=([^,\]]*),([^,\]]*),([^\]]*)\](.*)\[\/%s\]/siU";
$c['hcode']['3a'] = "/\[%s=([^x\]]*)x([^\]]*)\](.*)\[\/%s\]/siU";
$c['hcode']['4a'] = "/\[%s=([^x\]]*)x([^,\]]*),([^\]]*)\](.*)\[\/%s\]/siU";

// Todas as substituições
$c['hcode']['taglist'] = array(
	"b|<b>\\1</b>|1",
	"i|<i>\\1</i>|1",
	"u|<u>\\1</u>|1",
	"font|<font face=\"\\1\">\\2</font>|2",
	"size|<font size=\"\\1\">\\2</font>|2",
	"color|<font color=\"\\1\">\\2</font>|2",
	"bgcolor|<font style=\"background-color:\\1; \">\\2</font>|2",
	
	"move|<marquee scrollamount=\"\\1\" scrolldelay=\"\\2\">\\3</marquee>|3",
	"move|<marquee>\\1</marquee>|1",

	"list|<ul>\\1</ul>|1",
	"list|<ul type=\"\\1\">\\2</ul>|2",
	"listnum|<ol>\\1</ol>|1",
	"listnum|<ol type=\"\\1\">\\2</ol>|2",
	"li|<li>\\1</li>|1",
	"li|<li />|0",
	"\\*|<li />|0",
	"hr|<hr />|0",
	"br|<br />|0",
	
	"left|<div align=\"left\">\\1</div>|1",
	"center|<div align=\"center\">\\1</div>|1",
	"right|<div align=\"right\">\\1</div>|1",
	"align|<div align=\"\\1\">\\2</div>|2",
	"align|<div>\\1</div>|1",

	"p|<p align=\"\\1\">\\2</p>|2",
	"p|<p>\\1</p>|1",
	
	"url|<a href=\"\\1\" target=\"_\\2\">\\3</a>|3",
	"url|<a href=\"\\1\" target=\"_blank\">\\2</a>|2",
	"url|<a href=\"\\1\" target=\"_blank\">\\1</a>|1",
	
	"email|<a href=\"mailto:\\1\">\\2</a>|2",
	"email|<a href=\"mailto:\\1\">\\1</a>|1",
	"img|<img src=\"\\4\" width=\"\\1\" height=\"\\2\" align=\"\\3\" border=\"0\" />|4a",
	"img|<img src=\"\\3\" width=\"\\1\" height=\"\\2\" border=\"0\" />|3a",
	"img|<img src=\"\\2\" align=\"\\1\" border=\"0\" />|2",
	"img|<img src=\"\\1\" border=\"0\" />|1",
	"flash|<object classid=\"clsid: D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\"\\1\" height=\"\\2\"><param name=\"movie\" value=\"\\3\" /><param name=\"play\" value=\"true\" /><param name=\"loop\" value=\"true\" /><param name=\"quality\" value=\"high\" /><embed src=\"\\3\" width=\"\\1\" height=\"\\2\" play=\"true\" loop=\"true\" quality=\"high\"></embed></object>|3a",
);

?>
