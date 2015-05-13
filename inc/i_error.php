<?php $p['tit'] = "error"; if (!defined('WsSys_Token')) {echo "<font face=\"verdana\" size=\"2\" color=\"#CC0000\"><b>[ Erro ]</b> Você não pode acessar este arquivo!</font>"; exit; }

$err['unknown'] = "Ocorreu um erro desconhecido.";

$err['demo'] = "Esta função não está ativada na versão de demonstração.|back";
$err['noperms'] = "Você não tem permissão para executar esta ação!|back";
$err['nodata'] = "Dados faltando!|back";
$err['incform'] = "Preencha todos os campos em <b>negrito</b> do formulário.|back";
$err['idinvalid'] = "ID inválido!|back";
$err['nosel'] = "Selecione pelo menos um item para esta ação.|back";
$err['bytesinvalid'] = "Você digitou um tamanho inválido!<br />Formato esperado: <b>1 KB</b>.|back";
$err['onlynumbers'] = "Você usou caracteres onde não era permitido.<br />Verifique os campos que só permitem números.|back";

$err['news_noperms'] = "Você não tem permissão para modificar esta notícia!|back";

$err['perms_own'] = "Você não pode alterar as suas próprias permissões!<br />Peça a um administrador para alterá-las para você.|back";

$err['id_inuse'] = "A identificação digitada já está em uso.<br />Por favor escolha outra.|back";
$err['id_system'] = "A identificação digitada é de uso exclusivo do sistema.<br />Por favor escolha outra.|back";
$err['id_invalid'] = "A identificação digitada é inválida!<br />Use apenas letras <b>minúsculas</b>, números ou _.|back";

$err['login_own'] = "Você não pode alterar a sua conta de usuário por esta seção!<br />Para editá-lo, clique no seu nome de usuário, no menu ao lado.|back";
$err['login_inuse'] = "O login digitado já está em uso.<br />Por favor escolha outro.|back";
$err['login_system'] = "O login digitado é de uso exclusivo do sistema.<br />Por favor escolha outro.|back";
$err['login_invalid'] = "O login digitado é inválido!<br />Use apenas letras <b>minúsculas</b>, números ou _.|back";

$err['lostpwd_disabled'] = "Este recurso foi bloqueado.<br />Solicite a mudança da senha a um administrador.|back";
$err['lostpwd_noemail'] = "Este usuário não tem um e-mail configurado!<br />Solicite a mudança da senha a um administrador.|back";
$err['lostpwd_emailmismatch'] = "Nome de usuário ou e-mail inválidos!|back";
$err['lostpwd_notsent'] = "Ocorreu um erro ao enviar o e-mail, tente novamente mais tarde.<br />Nenhuma alteração foi feita.|continue";

$err['pack_invalidurl'] = "A URL informada não é suportada pelo MZn².<br /><br />Um exemplo de URL válida:<br /><b>http://www.meusite.com.br/pacote/</b>|back";
$err['pack_nosocket'] = "Não foi possível estabelecer uma conexão com o servidor do pacote.<br /><br />É possível que a URL seja inválida, o host pode estar fora<br />do ar, ou o PHP não pode acessar arquivos externos.|back";
$err['pack_notfound'] = "Não foi possível localizar a<br />URL especificada no servidor.<br /><br />Verifique se você digitou-a corretamente.|back";
$err['pack_noaccess'] = "A URL especificada retornou<br />um erro de acesso negado.<br /><br />Verifique se você digitou-a corretamente.|back";
$err['pack_redir'] = "É um redirecionamento para outra URL.<br /><br />Informe a URL verdadeira.|back";
$err['pack_not200'] = "A URL especificada retornou<br />um erro desconhecido.<br /><br />Verifique se você digitou-a corretamente.|back";
$err['pack_invalid'] = "A URL especificada retornou um arquivo<br />desconhecido e não um pacote de smilies.<br /><br />Verifique se você digitou-a corretamente.|back";

$err['logininvalid'] = "Nome de usuário ou senha inválidos!|back";

$err['passmismatch'] = "As senhas digitadas são diferentes!<br />Para uma maior sergurança você deve digitar a mesma senha nos dois campos.|back";

$err['backup_notar'] = "Não foi possível localizar o arquivo <b>backup.tar</b>|back";
$err['backup_corrupt'] = "O arquivo de backup está corrompido ou é inválido e, por isso, foi removido.|back";
$err['backup_upload_notmp'] = "Não foi possível localizar o arquivo enviado.<br /><br />Verifique se o seu host suporta envio de arquivos<br />e se você selecionou um arquivo do seu computador.|back";
$err['backup_upload_invalid'] = "O tipo de arquivo enviado não corresponde a um arquivo de backup!<br />Verifique o arquivo selecionado e tente novamente.|back";

$err['upload_noupload'] = "Nenhum arquivo foi enviado.<br /><br />Verifique se você selecionou algum arquivo do seu<br />computador ou se os arquivos não ultrapassam o limite.<br /><br /><span class=\"important\">Este erro pode ocorrer se o seu host<br />bloqueia envio de arquivos por PHP.</span>|back";
$err['upload_toobigsys'] = "O tamanho do <b>Arquivo ". $s->req['file'] ."</b> excede<br />o limite imposto pelo PHP.<br /><br />Entre em contato com o administrador do<br />seu host para mais esclarecimentos.<br /><br />Nenhum outro arquivo foi salvo.|back";
$err['upload_toobig'] = "O tamanho do <b>Arquivo ". $s->req['file'] ."</b> excede o<br />limite imposto pelo administrador.<br /><br />Nenhum outro arquivo foi salvo.|back";
$err['upload_broken'] = "O <b>Arquivo ". $s->req['file'] ."</b> não foi enviado<br />completamente e está corrompido.<br /><br />Nenhum outro arquivo foi salvo.|back";
$err['upload_extinvalid'] = "A extensão do arquivo <b>". $s->req['file'] ."</b> não é permitida.<br /><br />Nenhum outro arquivo foi salvo.|back";
$err['upload_noover'] = "O <b>Arquivo ". $s->req['file'] ."</b> já havia sido enviado por outro usuário.<br />Altere o nome deste arquivo ou apague-o e tente novamente.<br /><br />Nenhum outro arquivo foi salvo.|back";
$err['upload_protected'] = "O <b>Arquivo ". $s->req['file'] ."</b> é um arquivo do<br />sistema e não pode ser enviado.<br /><br />Altere o nome deste arquivo e tente novamente.<br /><br />Nenhum outro arquivo foi salvo.|back";

if (!$err[$s->req['id']]) {$err[$s->req['id']] = "Ocorreu um erro desconhecido|back"; }

list ($errMsg, $errLnk) = explode("|", $err[$s->req['id']]);

echo $errMsg;
if ($errLnk) {
	echo "<br /><br /><span class=\"small\">";
	if ($errLnk == "continue") {echo "<a href=\"index.php\">Clique aqui para continuar</a>"; }
	else if ($errLnk == "back") {echo "<a href=\"". $s->req['HTTP_REFERER'] ."\" onclick=\"history.back(); return false; \">Clique aqui para voltar</a>"; }
	else if ($errLnk == "retry") {echo "<a href=\"index.php\" onclick=\"location.reload(); return false; \">Clique aqui para tentar novamente</a>"; }
	echo "<br /></span>";
}

?>
