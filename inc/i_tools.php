<?php $p['tit'] = "tools"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

$count = 1;
if ($m->perms("upload") || $m->perms("uplmng")) {$count++; }
if ($m->perms("backup")) {$count++; }
if ($m->perms("smilies")) {$count++; }
$width = intval(100 / $count) ."%";
?>
<b>Selecione uma ferramenta</b><br />
<table align="center" cellpadding="0" cellspacing="20" class="small"><tr>
<?php if ($m->perms("upload") || $m->perms("uplmng")) { ?>	<td valign="top" width="<?php echo $width; ?>"><a href="index.php?s={session}&amp;sec=upload"><img src="img/{skin}/tool_upload.jpg" border="0" alt="Envia arquivos e gerencia arquivos enviados" /><br /><img src="img/_blank.gif" width="1" height="5" border="0" alt="" /><br />Enviar<br />arquivos</a></td><?php echo "\n"; } ?>
<?php if ($m->perms("backup")) { ?>	<td valign="top" width="<?php echo $width; ?>"><a href="index.php?s={session}&amp;sec=backup"><img src="img/{skin}/tool_backup.jpg" border="0" alt="Cria e restaura backups do banco de dados" /><br /><img src="img/_blank.gif" width="1" height="5" border="0" alt="" /><br />Backup</a></td><?php echo "\n"; } ?>
<?php if ($m->perms("smilies")) { ?>	<td valign="top" width="<?php echo $width; ?>"><a href="index.php?s={session}&amp;sec=smilies"><img src="img/{skin}/tool_smilies.jpg" border="0" alt="Adiciona, altera e remove smilies do sistema" /><br /><img src="img/_blank.gif" width="1" height="5" border="0" alt="" /><br />Smilies</a></td><?php echo "\n"; } ?>
	<td valign="top" width="<?php echo $width; ?>"><a href="index.php?s={session}&amp;sec=generator"><img src="img/{skin}/tool_generator.jpg" border="0" alt="Abre o gerador de PHP que auxilia na criação dos includes do novo MZn²" /><br /><img src="img/_blank.gif" width="1" height="5" border="0" alt="" /><br />Gerador<br />de PHP</a></td>
</tr></table>
