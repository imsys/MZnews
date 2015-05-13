<?php

/*

== Observações ==
Para trocar o número de notícias exibidas, altere o valor de
$mzn2->porpagina para o número desejado (linha 23).

*/

// Define as variáveis iniciais e chama a classe de exibição do MZn²
$mzn_path = dirname(__FILE__); $mzn_path = str_replace("/classic:EOF:", "", $mzn_path .":EOF:"); $mzn_path = str_replace("\\classic:EOF:", "", $mzn_path .":EOF:"); $mzn_path = str_replace(":EOF:", "", $mzn_path); require_once($mzn_path ."/mzn2.php"); $mzn_selfpage = $s->req['PHP_SELF'];

// Define $mzn2 como a classe com todas as funções de exibição do MZn²
$mzn2 = new MZn2_Noticias;

// Define a categoria que será usada
$mzn2->categoria = "principal";

// == Observações ==
// Para trocar o número de notícias
// exibidas, altere o valor abaixo.
$mzn2->porpagina = 10;

// Mostra as headlines
$mzn2->mostrar_noticias();

?>
