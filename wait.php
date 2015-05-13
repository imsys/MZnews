<?php

error_reporting(7); set_magic_quotes_runtime(0);

define("WsSys_Token", 1);

unset($s); unset($m); unset($p);

$AbsPath = dirname(__FILE__);
require_once $AbsPath ."/inc/g_global.php";
	$s = new WsSys;

if ($s->req['sleep'] && $s->req['g']) {
	$s->req['sleep'] *= 1000;
	echo "<font face=\"verdana\" size=\"2\"><b>Aguarde...</b></font>";
	echo "<scr"."ipt type=\"text/javascr"."ipt\" language=\"JavaScr"."ipt\">window.setTimeout('location.replace(\"". addslashes($s->req['g']) ."\"); ', ". $s->req['sleep'] .");</scr"."ipt>";
}

?>
