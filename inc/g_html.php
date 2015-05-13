<?php if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

/*

 -------------------  A T E N Ç Ã O  -------------------
| A ALTERAÇÃO DESTE ARQUIVO É ALTAMENTE DESACONSELHÁVEL |
| E PODERÁ INUTILIZAR O SISTEMA. ALTERE POR SUA PRÓPRIA |
| CONTA E RISCO.                                        |
 -------------------------------------------------------

*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt" lang="pt">
	<head>
		<title>MZn² 2.0 ADV - {site}</title>
<style type="text/css">
<!--
@import url("img/{skin}/style.css");
iframe     {position:static; display:block; overflow:inherit; visibility:visible; }
#mznBanner {position:static; display:block; overflow:inherit; visibility:visible; }
#logo      {position:static; display:block; overflow:inherit; visibility:visible; }
#botTxt    {position:static; display:block; overflow:inherit; visibility:visible; }
-->
</style>
<script type="text/javascript" language="JavaScript">
<!-- ;

var absDir = "<?php echo addslashes($AbsDir); ?>",
    imgDir = "<?php echo addslashes($s->cfg['dir']['img']); ?>",
    session = "<?php echo addslashes($s->req['s']); ?>",
    version = "<?php echo addslashes($s->sys['version']); ?>",
    do_autoupdate = "<?php echo ($s->sys['checkupdates'] && $m->perms("config")); ?>",
    unique = "<?php echo $s->usr['unique']; ?>",
    msie = <?php echo (preg_match("/msie (5.5|6)/i", $s->req['HTTP_USER_AGENT']) && !preg_match("/opera/i", $s->req['HTTP_USER_AGENT']))? 1 : 0; ?>;
var user = "<?php echo addslashes($s->usr['data']['name']); ?>";

// -->
</script>
<?php echo "<scr"."ipt type=\"text/javascript\" language=\"JavaScript\" src=\"mzn2.js\"></scr"."ipt>\n"; ?>
	</head>
	<body onload="loadPage(); " leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0">
		
		<div id="container">
		
		<div id="contents">
		
		<div id="logo" onclick="window.open('http://www.mznews.kit.net', '_blank'); ">
			<iframe id="mznBanner" name="mznBanner" src="about:blank" frameborder="0" scrolling="no" width="468" height="60" marginwidth="0" marginheight="0"></iframe>
		</div>
		
		<div id="menu">
			<div class="menuFirst"></div>
			<div class="menuUsr"><nobr><?php if ($s->usr['data']) {?><a href="index.php?s={session}&amp;sec=profile" class="menuUsr" onclick="return exitPage(); " title="Alterar os dados do seu perfil"><?php echo $s->usr['data']['name']; ?></a><?php } else {echo "Não logado"; } ?></nobr></div>
<?php if ($s->usr['data']) { ?>			<div class="menuMznTit"></div>
			<div class="menuMznBox"><div class="menuCat">Categoria:<br /><select class="menuCat" onchange="var x = this.options[this.selectedIndex].value; if (x && exitPage()) {document.location = 'index.php?s={session}&cat='+ x +'<?php $x = $m->filter_query(); if ($x) {echo "&". $x; } unset($x); ?>'; } else {this.selectedIndex = {cat_num}; } ">{cats}</select></div><?php if ($m->perms("post", $cat)) { ?><a href="index.php?s={session}&amp;sec=news&amp;act=new" onclick="return exitPage(); "><img src="img/{skin}/icon_post.jpg" border="0" class="menuImg" alt="Postar notícias" /></a><?php } else { ?><img src="img/{skin}/icon_post_dis.jpg" border="0" class="menuImg" alt="Você não pode adicionar notícias" /><?php } if ($m->perms("editown|editall|comments", $cat)) { ?><a href="index.php?s={session}&amp;sec=news" onclick="return exitPage(); "><img src="img/{skin}/icon_news.jpg" border="0" class="menuImg" alt="Gerenciar notícias e comentários" /></a><?php } else { ?><img src="img/{skin}/icon_news_dis.jpg" border="0" class="menuImg" alt="Você não pode gerenciar notícias e comentários" /><?php } ?></div>
			<div class="menuAdmTit"></div>
			<div class="menuAdmBox"><?php if ($m->perms("config")) { ?><a href="index.php?s={session}&amp;sec=config" onclick="return exitPage(); "><img src="img/{skin}/icon_config.jpg" border="0" class="menuImg" alt="Alterar as configurações" /></a><?php } else { ?><img src="img/{skin}/icon_config_dis.jpg" border="0" class="menuImg" alt="Você não pode alterar as configurações" /><?php } if ($m->perms("categories")) { ?><a href="index.php?s={session}&amp;sec=categories" onclick="return exitPage(); "><img src="img/{skin}/icon_categories.jpg" border="0" class="menuImg" alt="Gerenciar categorias e modelos" /></a><?php } else { ?><img src="img/{skin}/icon_categories_dis.jpg" border="0" class="menuImg" alt="Você não pode gerenciar as categorias e modelos" /><?php } if ($m->perms("users")) { ?><a href="index.php?s={session}&amp;sec=users" onclick="return exitPage(); "><img src="img/{skin}/icon_users.jpg" border="0" class="menuImg" alt="Gerenciar usuários" /></a><?php } else { ?><img src="img/{skin}/icon_users_dis.jpg" border="0" class="menuImg" alt="Você não pode gerenciar usuários" /><?php } ?><a href="index.php?s={session}&amp;sec=tools" onclick="return exitPage(); "><img src="img/{skin}/icon_tools.jpg" border="0" class="menuImg" alt="Ferramentas" /></a></div><?php echo "\n"; } ?>
			<?php if ($s->usr['data']) { ?><div class="menuLogout"><a href="index.php?s={session}&amp;sec=login&amp;act=exit" style="color:#FFFFFF; " onclick="return exitPage(); "><img src="img/_blank.gif" width="100%" height="100%" border="0" alt="Sair do sistema" /></a></div><?php } else { ?><div class="menuLogin"><a href="index.php?s={session}&amp;sec=login" style="color:#FFFFFF; "><img src="img/_blank.gif" width="100%" height="100%" border="0" alt="Entrar no sistema" /></a></div><?php } echo "\n"; ?>
			<div class="menuLast"></div>
		</div>
		
		<div id="title" style="background-image:url(img/{skin}/tit_{tit}.jpg); "></div>
		
		<div id="site">
		
<?php if ($s->cfg['ver']['demo']) { ?>		<div id="demo">Versão de demonstração - Nenhuma alteração será salva!</div><?php echo "\n"; } ?>
<?php if ($s->req['msg']) { ?>		<div id="msg"><?php echo $s->req['msg']; ?></div>
		<?php echo "\n"; } ?>
<!-- Conteúdo -->
{body}
<!-- Fim conteúdo -->
		
		</div>
		
		<div id="botTxt">
<?php if ($s->skin['author']['name']) { ?>			Skin criada por <?php if ($s->skin['author']['mail']) { ?><a href="mailto:<?php echo $s->skin['author']['mail']; ?>"><?php } echo $s->skin['author']['name']; if ($s->skin['author']['mail']) { ?></a><?php } if ($s->skin['author']['siteurl']) { ?> | <a href="<?php echo $s->skin['author']['siteurl']; ?>" target="_blank"><?php echo $s->skin['author']['sitename']; ?></a><?php } ?><br /><?php echo "\n"; } ?>
			<b>Sistema produzido por <a href="http://www.wstec.net/" target="_blank">WsTec</a> - Copyright © 2003-2004 <a href="http://www.mundo-dbz.com.br/" target="_blank">Mundo DBZ</a></b>
		</div>
		<div id="bot" onclick="window.open('http://www.mznews.kit.net', '_blank'); "></div>
		
		</div>
		
		</div>
		
	</body>
</html>
