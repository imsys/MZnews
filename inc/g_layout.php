<?php

class Layout {
	
	var $page_link = "";
	
	//-----------------------------------------------------------------------------
	// Layout Forms
	//-----------------------------------------------------------------------------
	
	var $tabs = 0;
	var $tabs_cache = array();
	function tabs () {
		if (count($this->tabs_cache) > 0 && $this->tabs_cache[0] == $this->tabs) {return $this->tabs_cache[1]; }
		else {$this->tabs_cache[0] = $this->tabs; $this->tabs_cache[1] = ""; if ($this->tabs > 0) {for ($i = 1; $i <= $this->tabs; $i++) {$this->tabs_cache[1] .= "\t"; } } return $this->tabs_cache[1]; }
	}
	
	
	var $form_name = "";
	var $form_act = "";
	// Argumentos: seção, ação, hiddens (array), método, nome, extras
	function form ($sec = "", $act = "", $extra = array(), $method = "post", $name = "formCenter", $more = "", $action = "index.php") {
		global $s;
		if ($more) {$more = " ". $more; }
		if (strpos($more, " onsubmit=") === FALSE) {$more = " onsubmit=\"if (this.elements['act'].value == 'remove') {if (!confirm('Você está prestes a remover os itens selecionados.\\nTodos os registros que são dependentes destes itens\\ntambém serão removidos!\\n\\nDeseja continuar?')) {return false; } } disableButtons('". $name ."'); \"". $more; }
		
		$this->form_name = $name;
		echo $this->tabs() ."<form name=\"". $name ."\" action=\"". $action ."\" method=\"". $method ."\" autocomplete=\"off\"". $more .">\n";
		$this->tabs++;
			if ($s->req['s']) {echo $this->tabs() ."<input type=\"hidden\" name=\"s\" value=\"". $s->req['s'] ."\" />\n"; }
			if ($sec) {echo $this->tabs() ."<input type=\"hidden\" name=\"sec\" value=\"". $sec ."\" />\n"; }
			if ($act) {echo $this->tabs() ."<input type=\"hidden\" name=\"act\" value=\"". $act ."\" />\n"; $this->form_act = $act; }
			foreach ($extra as $k => $v) {echo $this->tabs() ."<input type=\"hidden\" name=\"". $k ."\" value=\"". $s->quote_safe($v) ."\" />\n"; }
	}
	function form_end () {
		$this->tabs--;
		echo $this->tabs() ."</form>\n";
		$this->form_name = "";
		$this->form_act = "";
	}
	
	var $tb_width = 0;
	// Argumentos: largura, enchimento, espaçamento, extras
	function table ($width = "", $cellpadding = 0, $cellspacing = 0, $more = "") {
		if ($width) {$this->tb_width = $width; $width = " width=\"". $width ."\""; }
		if ($more) {$more = " ". $more; }
		echo $this->tabs() ."<table". $width ." align=\"center\" cellpadding=\"". $cellpadding ."\" cellspacing=\"". $cellspacing ."\" border=\"0\"". $more ." style=\"text-align:left; \">\n";
		$this->tabs++;
	}
	function table_end () {
		if ($this->tb_row) {$this->tb_row_end(); }
		$this->tabs--;
		echo $this->tabs() ."</table>\n";
	}
	
	var $tb_row = 0;
	function tb_row () {
		echo $this->tabs() ."<tr><td valign=\"top\">";
		$this->tb_row = 1;
	}
	function tb_row_end () {
		if ($this->tb_col) {$this->tb_col_end(); }
		echo "</td></tr>\n";
		$this->tb_row = 0;
	}
	function tb_row_sep () {
		echo $this->tabs() ."<tr><td height=\"4\"></td></tr>\n";
		$this->tb_row = 0;
	}
	function tb_nextrow () {
		if ($this->tb_row == 1) {$this->tb_row_end(); }
		$this->tb_row_sep();
		$this->tb_row();
	}
	
	var $tb_group = 0;
	// Argumentos: Título esquerdo, Título direito
	function tb_group ($left, $right = "") {
		if ($this->tb_row) {$this->tb_row_end(); }
		if ($this->tb_group) {echo $this->tabs() ."<tr><td height=\"8\"></td></tr>\n"; }
		echo $this->tabs() ."<tr><td valign=\"top\"><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"formGroup\"><tr><td><b>". $left ."</b></td>"; if ($right) {echo "<td>". $right ."</td>"; } echo "</tr></table></td></tr>\n";
		$this->tb_group++;
	}
	
	// Argumentos: Altura, conteúdo
	function tb_separator ($height = 30, $contents = "") {
		if ($this->tb_row) {$this->tb_row_end(); }
		echo $this->tabs() ."<tr><td height=\"". $height ."\">". $contents ."</td></tr>\n";
		$this->tb_group = 0;
	}
	
	var $tb_col = 0;
	// Argumentos: largura, alinhamento
	function tb_col ($width = "", $align = "left", $valign = "top") {
		if (!$this->tb_row) {$this->tb_row(); }
		if ($width) {$width = " width=\"". $width ."\""; } else {$width = ""; }
		$tb_width = $this->tb_width; if ($tb_width) {$tb_width = "100%"; }
		if (!$this->tb_footer) {$class = "formItem"; } else {$class = "formFooter"; }
		echo "<table cellpadding=\"0\" cellspacing=\"0\" width=\"". $tb_width ."\" class=\"". $class ."\"><tr><td valign=\"". $valign ."\" align=\"". $align ."\"". $width .">";
		$this->tb_col = 1;
	}
	function tb_col_end () {
		echo "</td></tr></table>";
		$this->tb_col = 0;
	}
	function tb_nextcol ($width = "", $align = "left", $valign = "top") {
		if (!$this->tb_col) {$this->tb_col($width, $align); }
		else {
			if ($width) {$width = " width=\"". $width ."\""; }
			echo "</td><td width=\"5\"><img src=\"img/_blank.gif\" width=\"5\" height=\"1\" border=\"0\" alt=\"\" /></td><td valign=\"". $valign ."\" align=\"". $align ."\"". $width .">";
		}
	}
	
	function tb_custom ($contents, $width = "", $align = "left", $valign = "top") {
		if (!$this->tb_col) {$this->tb_col($width, $align, $valign); }
		else {$this->tb_nextcol($width, $align, $valign); }
		echo $contents;
	}

	var $tabindex = 1;
	// Argumentos: tipo, nome, texto, valor, largura, extras
	function tb_input ($type, $name, $text = "", $value = "", $width = 505, $extra = array()) {
		global $s;
		if (!$this->tb_col) {$this->tb_col($width); } else {$this->tb_nextcol($width); }
		if ($text) {$text = $text ."<br />"; }
		$width -= 4;
		if (!$extra["class"]) {$extra["class"] = "normal"; }
		if (!$extra["style"]) {$extra["style"] = "width:". $width ."px; "; }
		$more = ""; foreach ($extra as $k => $v) {if (preg_match("/^_/", $k)) {continue; } $more .= " ". $k ."=\"". $v ."\""; }
		echo $text . $extra["_before"] ."<input type=\"". $type ."\" name=\"". $name ."\" id=\"form_". $name ."\" value=\"". $s->quote_safe($value) ."\" tabindex=\"". $this->tabindex ."\"". $more ." onchange=\"form_changed = 1; \" />". $extra["_after"];
		$this->tabindex++;
	}
	// Argumentos: tipo, nome, texto, lista (a=b;c=d), selecionado, largura, extras
	function tb_check ($type, $name, $text = "", $list = "", $selected = "", $width = 505, $extra = array()) {
		global $s;
		if (!$this->tb_col) {$this->tb_col($width); } else {$this->tb_nextcol($width); }
		if ($text) {$text = $text ."<br />"; }
		if (!$extra["class"]) {$extra["class"] = "normal"; }
		$more = ""; foreach ($extra as $k => $v) {if (preg_match("/^_/", $k)) {continue; } $more .= " ". $k ."=\"". $v ."\""; }
		echo $text;
		$list = explode("|", $list);
		foreach ($list as $item) {
			list ($value, $label) = explode("=", $item, 2);
			$label = str_replace(" ", "&nbsp;", $label);
			echo "<input type=\"". $type ."\" name=\"". $name ."\" id=\"form_". $name ."_". $value ."\" value=\"". $s->quote_safe($value) ."\" tabindex=\"". $this->tabindex ."\"". $more .""; if ($selected == $value) {echo " checked"; } echo " onchange=\"form_changed = 1; \" /><label for=\"form_". $name ."_". $value ."\">". $label ."</label>";
			$this->tabindex++;
		}
	}
	// Argumentos: nome, texto, lista (a=b;c=d), selecionado, largura, extras
	function tb_select ($name, $text = "", $list = "", $selected = "", $width = 505, $extra = array()) {
		global $s;
		if (!$this->tb_col) {$this->tb_col($width); } else {$this->tb_nextcol($width); }
		if ($text) {$text = $text ."<br />"; }
		if (!$extra["class"]) {$extra["class"] = "normal"; }
		if (!$extra["style"]) {$extra["style"] = "width:". $width ."px; "; }
		$more = ""; foreach ($extra as $k => $v) {if (preg_match("/^_/", $k)) {continue; } $more .= " ". $k ."=\"". $v ."\""; }
		echo $text ."<select name=\"". $name ."\" tabindex=\"". $this->tabindex ."\"". $more ." onchange=\"form_changed = 1; \">";
		$list = explode("|", $list);
		foreach ($list as $item) {
			list ($value, $label) = explode("=", $item, 2);
			$label = str_replace(" ", "&nbsp;", $label);
			echo "<option value=\"". $s->quote_safe($value) ."\""; if ($selected && $selected == $value) {echo " selected"; } echo ">". $label ."</option>";
		}
		echo "</select>";
		$this->tabindex++;
	}
	// Argumentos: nome, texto, valor, largura, extras
	function tb_text ($name, $text = "", $value = "", $width = 505, $extra = array()) {
		global $s;
		if (!$this->tb_col) {$this->tb_col($width); } else {$this->tb_nextcol($width); }
		if ($text) {$text = $text ."<br />"; }
		$width -= 4;
		if (!$extra["class"]) {$extra["class"] = "normal"; }
		if (!$extra["style"]) {$extra["style"] = "width:". $width ."px; "; }
		$more = ""; foreach ($extra as $k => $v) {if (preg_match("/^_/", $k)) {continue; } $more .= " ". $k ."=\"". $v ."\""; }
		echo $text ."<textarea name=\"". $name ."\" tabindex=\"". $this->tabindex ."\"". $more ." onchange=\"form_changed = 1; \">";
		echo $s->quote_safe($value);
		echo "</textarea>";
		$this->tabindex++;
	}
	
	var $tb_footer = 0;
	function tb_footer () {
		if ($this->tb_row) {$this->tb_row_end(); }
//		if ($this->tb_group) {echo $this->tabs() ."<tr><td height=\"4\"></td></tr>\n"; }
		echo $this->tabs() ."<tr><td><hr color=\"#000000\" noshade size=\"1\" /></td></tr>\n";
		$this->tb_footer = 1;
	}
	
	
	// Argumentos: legenda
	function tb_caption ($caption) {
		if (!$this->tb_footer) {
			$this->tb_footer();
			$this->tb_col(intval($tb_width/2));
		}
		echo $caption;
	}
	
	var $tb_button = 0;
	// Argumentos: tipo, texto, extras (_go, _br)
	function tb_button ($type, $text, $extra = array()) {
		if (!$this->tb_footer && !$this->tb_button) {
			$this->tb_footer();
			$this->tb_col($tb_width, "right");
		}
		else if ($this->tb_footer && !$this->tb_button) {
			$this->tb_nextcol(intval($tb_width/2), "right");
		}
		if ($this->tb_button) {
			if ($extra["_br"]) {echo "<br />"; }
			else {echo "&nbsp;"; }
		}
		if ($extra['accesskey'] && !$extra['title']) {$extra['title'] = "Tecla de atalho: Alt+". strtoupper($extra['accesskey']); }
		$more = ""; foreach ($extra as $k => $v) {if (preg_match("/^_/", $k)) {continue; } if ($more) {$more .= " "; } $more .= $k ."=\"". $v ."\""; } if ($more) {$more = " ". $more; }
		if (strtolower($type) == "submit") {
			echo "<button type=\"submit\" class=\"submit\" tabindex=\"". $this->tabindex ."\"". $more .">". $text ."</button>"; $this->tabindex++;
		}
		else if (strtolower($type) == "cancel") {
			if ($extra["_go"]) {$extra["_go"] = "&". $extra["_go"]; }
			echo "<button type=\"button\" tabindex=\"". $this->tabindex ."\" onclick=\"if (exitPage()) {document.location = 'index.php?s={session}". $extra["_go"] ."'; } \" ". $more .">". $text ."</button>"; $this->tabindex++;
		}
		else {
			echo "<button type=\"". $type ."\" tabindex=\"". $this->tabindex ."\"". $more .">". $text ."</button>"; $this->tabindex++;
		}
		$this->tb_button = 1;
	}
	
	//-----------------------------------------------------------------------------
	// Layout Lists
	//-----------------------------------------------------------------------------
	
	var $lists = array();
	var $list_i = 0;
	var $list_sort = null;
	var $list_pages = 0;
	function list_header () {
		$fArgs = func_get_args();
		for ($i = 0; $i < count($fArgs); $i++) {
			$col = $fArgs[$i];
			$this->lists['header'][] = $col;
		}
	}
	
	function list_item () {
		$fArgs = func_get_args();
		$x = count($this->lists['data']);
		for ($i = 0; $i < count($fArgs); $i++) {
			$item = $fArgs[$i];
			if (is_array($item)) {$this->lists['data'][$x]['args'] = $item; }
			else {$this->lists['data'][$x][] = $item; }
		}
	}
	
	function list_sort () {
		$ls = $this->lists; $order = $this->list_sort; $nls = array(); $sls = array();
		if (!$ls['data'] || !$order) {return; }
		list ($col, $reverse) = explode(":", $order);
		$i = 0; foreach ($ls['data'] as $item) {
			$k = $item[$col];
			if (preg_match("/\|/", $k)) {$k = preg_replace("/([^\|]*)\|[\s\S]*/", "\\1", $k); }
			if (preg_match("/^[0-9]+$/", $item[$col])) {while (strlen($k) < 15) {$k = "0". $k; } }
			$k = strtolower($k);
			$k = $k ."|". $i;
			$sls[$k] = $item;
		$i++; }
		if ($reverse) {krsort($sls); }
		else {ksort($sls); }
		foreach ($sls as $item) {
			$nls[] = $item;
		}
		$this->lists['data'] = $nls;
		$this->list_sort = $col .":". $reverse;
	}
	
	function list_filter () {
		global $s;
		$ls = $this->lists; $query = $this->list_query; $nls = array();
		if (!$ls['data'] || !$query) {return; }
		$query = $s->regex_search($query);
		$i = 0; foreach ($ls['data'] as $k => $items) {
			$search = ""; foreach ($items as $item) {if (is_array($item)) {continue; } if (preg_match("/\|/", $item)) {$item = preg_replace("/([^\|]*)\|.*/", "\\1", $item); } if ($search) {$search .= "|"; } $search .= $item; }
			if (preg_match("/". $query ."/iU", $search)) {$nls[$k] = $items; }
		$i++; }
		$this->lists['data'] = $nls;
	}
	
	function list_build ($sort_icon_0 = "", $sort_icon_1 = "", $width = "") {
		global $s;
		$ls = $this->lists;
		
		$m_link = $this->page_link;
		$page = $this->list_pg;
		$perpage = $this->list_perpage;
		
		if ($sort_icon_0) {
			$sort_icon = 1;
			list($sort_col, $sort_order) = explode(":", $this->list_sort);
		}
		
		$this->table($width, 2); $sel = array(); $hid = array(); $wid = array(); $alg = array();
		echo $this->tabs() ."<tr class=\"listHeader\">";
		$is_sel = 0;
		$h = 0; foreach ($ls['header'] as $k => $head) {
			list ($dt, $name, $align) = explode(":", $head); $args = "";
			$args = ""; if ($align == "left" || $align == "center" || $align == "right") {$alg[$k] = $align; $args .= " align=\"". $align ."\""; }
			if ($dt == "sel") {$sel[$k] = $name; $is_sel = 1; }
			else if ($dt == "hid") {$hid[$k] = $name; }
			else {
				if ($dt > 0) {$args .= " width=\"". $dt ."\""; }
				$wid[$k] = $dt;
				if ($is_sel) {$is_sel = 0; $wid[$k] -= 20; $args .= " colspan=\"2\""; $h++; }
				if ($sort_icon && $align != "nosort") {
					if ($sort_col == $k) {
						if ($sort_order == 1) {$sorder = 0; }
						else {$sorder = 1; }
					}
					else {$sorder = 0; }
					
					$sort_link = $m_link;
					$sort_link = $s->replace_vars($sort_link, array("pg" => $page));
					$sort_link = $s->replace_vars($sort_link, array("query" => $this->list_query));
					$sort_link = $s->replace_vars($sort_link, array("sort" => $k .":". $sorder));
					
					if ($sort_col == $k && $sort_order == 1) {$name = "<a href=\"". $sort_link ."\" class=\"listHeaderSelected\">". $name ."&nbsp;<img src=\"". $sort_icon_1 ."\" border=\"0\" alt=\"\" align=\"absmiddle\" /></a>"; }
					else if ($sort_col == $k && $sort_order == 0) {$name = "<a href=\"". $sort_link ."\" class=\"listHeaderSelected\">". $name ."&nbsp;<img src=\"". $sort_icon_0 ."\" border=\"0\" alt=\"\" align=\"absmiddle\" /></a>"; }
					else {$name = "<a href=\"". $sort_link ."\" class=\"listHeader\">". $name ."</a>"; }
				}
				echo "<td". $args .">". $name ."</td>";
				$h++;
			}
		}
		
		$count = count($ls['data']);
		if ($count) {
			if ($perpage == 0) {$iStart = 0; $iEnd = $count; }
			else {
				$pg_tot = 1; while ($count - $perpage > 0) {$pg_tot++; $count -= $perpage; }
				if ($page > $pg_tot) {$page = $pg_tot; } if ($page < 1) {$page = 1; }
				$iStart = intval(($page - 1) * $perpage); $iEnd = round($page * $perpage);
			}
			$this->list_pages = $pg_tot;
			
			echo "</tr>\n";
			$i = 0; foreach ($ls['data'] as $item) {
				if ($i >= $iStart && $i < $iEnd) {
					$class = "listItem";
					if ($item['args']['disabled']) {$class = "listItemDisabled"; }
					if ($item['args']['class']) {$class = $item['args']['class']; }
					
					echo $this->tabs() ."<tr class=\"". $class ."\" id=\"list_". $this->form_name ."_". $i ."\">";
					foreach ($item as $k => $col) {
						$args = ""; if ($alg[$k]) {$args .= " align=\"". $alg[$k] ."\""; }
						if ($k === "args") {continue; }
						$class = ""; if ($item['args']['colclass']) {$class = $item['args']['colclass']; }
 						if (preg_match("/\|/", $col)) {$format = preg_replace("/([^\|]*)\|/", "", $col); $col = sprintf($format, preg_replace("/([^\|]*)\|[\s\S]*/", "\\1", $col));}
						if ($hid[$k]) {echo "<input type=\"hidden\" name=\"". $hid[$k] ."\" value=\"". $s->quote_safe($col) ."\">"; }
						else if ($sel[$k]) {echo "<td width=\"20\" class=\"". $class ."\"><input type=\"checkbox\" name=\"sel[". $s->quote_safe($col) ."]\" value=\"1\""; if (!$item['args']['disabled']) {echo " onclick=\"var obj = document.getElementById('list_". $this->form_name ."_". $i ."'); if (obj) {if (this.checked) {obj.className = 'listItemSelected'; } else {obj.className = 'listItem'; } } \""; } else {echo " disabled=\"1\""; } echo "></td>"; }
						else {echo "<td width=\"". $wid[$k] ."\" class=\"". $class ."\"". $args .">". $col ."</td>"; }
					}
					echo "</tr>\n";
				}	
				$i++;
			}
		}
		else {echo $this->tabs() ."<tr><td colspan=\"". $h ."\" align=\"center\">Nenhum item localizado. Adicione um item ou tente refinar sua busca.</tr>\n"; }
		
		$this->table_end();
		$this->lists = array();
		$this->list_i += 1;
	}
	
	function list_footer ($links = array(), $selects = 0, $actions = array(), $search = 0, $width = "") {
		global $s;
		$this->table($width);
		echo $this->tabs() ."<tr class=\"listFooter\">";
		
		$m_link = $this->page_link;
		$page = $this->list_pg;
		$sort = $this->list_sort;
		$query = $this->list_query;
		
		$count = 0; $length = 0;
		if (count($links) > 0) {$length++; }
		if ($this->list_pages && $this->list_pages != 1) {$length++; }
		if ($selects) {$length++; }
		if (count($actions) > 0) {$length++; }
		
		if (count($links) > 0) {
			$count++;
			if ($count == 1) {$align = "left"; }
			else if ($count == $length) {$align = "right"; }
			else {$align = "center"; }
			echo "<td nowrap=\"1\" align=\"". $align ."\">";
			if (!$links['_sep']) {$links['_sep'] = "&nbsp;&middot;&nbsp;"; }
			$i = 0; foreach ($links as $text => $link) {
				if (preg_match("/^_/", $text)) {continue; }
				if ($i) {echo $links['_sep']; }
				echo "<a href=\"". $link ."\">". $text ."</a>";
			$i++; }
			echo "</td>";
		}
		
		if ($this->list_pages && $this->list_pages > 1) {
			$x_link = $m_link;
			$m_link = $s->replace_vars($m_link, array("sort" => $sort));
			$m_link = $s->replace_vars($m_link, array("query" => $query));
			$count++;
			if ($count == 1) {$align = "left"; }
			else if ($count == $length) {$align = "right"; }
			else {$align = "center"; }
			echo "<td nowrap=\"1\" align=\"". $align ."\">";
			if ($page > 1) {$pg = $page - 1; echo "<a href=\"". $s->replace_vars($m_link, array("pg" => 1)) ."\" title=\"Primeira página\">&laquo;&laquo;</a>&nbsp;<a href=\"". $s->replace_vars($m_link, array("pg" => $pg)) ."\" title=\"Página anterior\">&laquo;</a>"; }
			else {echo "<span class=\"disabled\">&laquo;&laquo;&nbsp;&laquo;</span>"; }
			echo "<a href=\"#\" title=\"Ir para uma página específica\" onclick=\"var page = ". $page .", pages = ". $this->list_pages .", selected = 1; if (pages > 1 && page == pages) {selected = pages - 1; } else if (pages > 1) {selected = page + 1;} var go = prompt('Escolha uma página para ir.\\nDigite um número entre 1 e '+ pages, selected); if (go) {document.location = '". $s->replace_vars($m_link, array("pg" => "'+ go +'")) ."'; } return false; \">&nbsp;". $page ."&nbsp;</a>";
			if ($page < $this->list_pages) {$pg = $page + 1; echo "<a href=\"". $s->replace_vars($m_link, array("pg" => $pg)) ."\" title=\"Próxima página\">&raquo;</a>&nbsp;<a href=\"". $s->replace_vars($m_link, array("pg" => $this->list_pages)) ."\" title=\"Última página\">&raquo;&raquo;</a>"; }
			else {echo "<span class=\"disabled\">&raquo;&nbsp;&raquo;&raquo;</span>"; }
			echo "</td>";
			$m_link = $x_link;
		}
		
		if ($selects) {
			$count++;
			if ($count == 1) {$align = "left"; }
			else if ($count == $length) {$align = "right"; }
			else {$align = "center"; }
			echo "<td nowrap=\"1\" align=\"". $align ."\">";
			echo "<a href=\"#\" onclick=\"selectAll('". $this->form_name ."', 'sel_'); return false; \">marcar tudo</a>&nbsp;&middot;&nbsp;<a href=\"#\" onclick=\"selectInvert('". $this->form_name ."', 'sel_'); return false; \">inverter</a>";
			echo "</td>";
		}
		
		if (count($actions) > 0) {
			$count++;
			if ($count == 1) {$align = "left"; }
			else if ($count == $length) {$align = "right"; }
			else {$align = "center"; }
			echo "<td nowrap=\"1\" align=\"". $align ."\">";
			echo "<select onchange=\"this.form.elements['act'].value = this.options[this.selectedIndex].value; \">";
			foreach ($actions as $act => $name) {
				if (preg_match("/^_/", $act)) {continue; }
				echo "<option value=\"". $act ."\"";
				if ($act == $this->form_act) {echo " selected"; }
				echo ">". $name ."</option>";
			}
			echo "</select> marcados <input type=\"submit\" value=\"Ok\" class=\"submit\">";
			echo "</td>";
		}
		
		echo "</tr>\n";
		
		if ($search) {
			$x_link = $m_link;
			$m_link = $s->replace_vars($m_link, array("sort" => $sort));
			$m_link = $s->replace_vars($m_link, array("pg" => $page));
			echo $this->tabs() ."<tr class=\"listFooter\">";
			echo "<td colspan=\"". $count ."\" align=\"right\">";
			if ($query) {echo "<a href=\"". $s->replace_vars($m_link, array("query" => "")) ."\">Mostrar tudo</a>&nbsp;&middot;&nbsp;"; }
			echo "Procurar: <input type=\"text\" id=\"search_query\" name=\"search_query\" value=\"". $s->quote_safe($query) ."\" class=\"small\" style=\"width:120px; \" onkeypress=\"if (event.keyCode == 13) {var x = escape(this.value); document.location = '". $s->replace_vars($m_link, array("query" => "'+ x +'")) ."'; return false; } \" />&nbsp;<input type=\"button\" name=\"search_submit\" value=\"Ok\" class=\"submit\" onclick=\"var x = escape(document.getElementById('search_query').value); document.location = '". $s->replace_vars($m_link, array("query" => "'+ x +'")) ."'; \" /></td>";
			echo "</tr>\n";
			$m_link = $x_link;
		}
		
		$this->table_end();
	}
	
}

?>
