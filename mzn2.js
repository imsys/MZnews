//
// ====================== Auxílio JavaScript do MZn² ======================
//
// Copyright © 2003-2004 WsTec - Todos os direitos reservados
// Qualquer cópia total ou parcial deste script sem autorização é ilegal
//
// ========================================================================
//


// Saída da página
var exit_msg = "ATENÇÃO\n\nVocê efetuou alterações no formulário e não o submeteu ainda.\nAo sair desta página sem submetê-lo, todas as alterações feitas\nserão perdidas!\n\nDeseja prosseguir?";
var form_changed = 0;
function exitPage() {
	if (form_changed) {
		if (!confirm(exit_msg)) {return false; }
	}
	return true;
}

// Entrada
function loadPage() {
	if (document.getElementById("mznBanner")) {document.getElementById("mznBanner").src = 'http://www.mznews.kit.net/ext/ad.html'; }
}

// Formulários e listas
function disableButtons(fName) {
	if (!fName) {fName = "formCenter"; }
	if (!document.forms[fName]) {return; }
	for (i = 0; i < document.forms[fName].elements.length; i++) {
		var tp = document.forms[fName].elements[i].type.toLowerCase();
		if (tp == "submit" || tp == "reset" || tp == "button") {document.forms[fName].elements[i].disabled = true; }
	}
}

function selectAll(fName) {
	if (!fName) {fName = "formCenter"; }
	if (!document.forms[fName]) {return; }
	var j = 0; for (i = 0; i < document.forms[fName].elements.length; i++) {
		var obj = document.forms[fName].elements[i], tp = obj.type.toLowerCase();
		if (tp == "checkbox") {
			if (!obj.disabled) {
				obj.checked = true;
				if (document.getElementById('list_'+ fName +'_'+ j)) {document.getElementById('list_'+ fName +'_'+ j).className = 'listItemSelected'; }
			}
			j++;
		}
	}
}
function selectInvert(fName) {
	if (!fName) {fName = "formCenter"; }
	if (!document.forms[fName]) {return; }
	var j = 0; for (i = 0; i < document.forms[fName].elements.length; i++) {
		var obj = document.forms[fName].elements[i], tp = obj.type.toLowerCase();
		if (tp == "checkbox") {
			if (!obj.disabled) {
				var x = !obj.checked;
				obj.checked = x;
				if (document.getElementById('list_'+ fName +'_'+ j)) {document.getElementById('list_'+ fName +'_'+ j).className = (x) ? 'listItemSelected' : 'listItem'; }
			}
			j++;
		}
	}
}

// Inserção de texto em uma caixa de textos
function MZn2_edv_paste (id, newText) {
	var obj = document.getElementById("MZn2_"+ id +"_code");
	if (!obj) {return; } obj.focus();
	if (msie) {
		var innerText = document.selection.createRange().text;
		document.selection.createRange().text = newText.replace(/\{text\}/, innerText);
		document.selection.createRange().select();
	}
	else {newText = newText.replace(/\{text\}/, ""); obj.value += newText; }
}

// Verificação dos campos nos formulários
function checkFields (oForm) {
	if (!oForm) {return false; }
	var args = checkFields.arguments, res = true;
	for (i = 1; i < args.length; i++) {
		arg = args[i];
		if (arg.indexOf("=") != -1) {
			arg = arg.split("=");
			field1 = oForm.elements[arg[0]].value;
			field2 = oForm.elements[arg[1]].value;
			if (field1 != field2) {res = false; }
		}
		else if (/:url$/.test(arg)) {
			arg = arg.replace(/:url$/, "");
			field = oForm.elements[arg].value;
			if (field.indexOf("http://") != 0) {res = false; }
		}
		else if (/:numbers$/.test(arg)) {
			arg = arg.replace(/:numbers$/, "");
			field = oForm.elements[arg].value;
			if (!/^[0-9]+$/.test(field)) {res = false; }
		}
		else if (/:sensitive$/.test(arg)) {
			arg = arg.replace(/:sensitive$/, "");
			field = oForm.elements[arg].value;
			if (!/^[0-9a-z_]+$/.test(field)) {res = false; }
		}
		else if (/:mail$/.test(arg)) {
			arg = arg.replace(/:mail$/, "");
			field = oForm.elements[arg].value;
			pos1 = field.indexOf("@");
			pos2 = field.lastIndexOf(".");
			if (pos1 == -1 || pos2 == -1 || pos2 <= pos1) {res = false; }
		}
		else {
			arg = arg.replace(/:mail$/, "");
			field = oForm.elements[arg].value;
			if (!field) {res = false; }
		}
	}
	return res;
}

// MD5 - Para segurança no login
var hex_chr = "0123456789abcdef";
function rhex(num) {str = ""; for(j = 0; j <= 3; j++) {str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) + hex_chr.charAt((num >> (j * 8)) & 0x0F); } return str; }
function str2blks_MD5(str) {nblk = ((str.length + 8) >> 6) + 1; blks = new Array(nblk * 16); for(i = 0; i < nblk * 16; i++) {blks[i] = 0; } for(i = 0; i < str.length; i++) blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8); blks[i >> 2] |= 0x80 << ((i % 4) * 8); blks[nblk * 16 - 2] = str.length * 8; return blks; }
function add(x, y) {var lsw = (x & 0xFFFF) + (y & 0xFFFF); var msw = (x >> 16) + (y >> 16) + (lsw >> 16); return (msw << 16) | (lsw & 0xFFFF); }
function rol(num, cnt) {return (num << cnt) | (num >>> (32 - cnt)); }
function cmn(q, a, b, x, s, t) {return add(rol(add(add(a, q), add(x, t)), s), b); }
function ff(a, b, c, d, x, s, t) {return cmn((b & c) | ((~b) & d), a, b, x, s, t); }
function gg(a, b, c, d, x, s, t) {return cmn((b & d) | (c & (~d)), a, b, x, s, t); }
function hh(a, b, c, d, x, s, t) {return cmn(b ^ c ^ d, a, b, x, s, t); }
function ii(a, b, c, d, x, s, t) {return cmn(c ^ (b | (~d)), a, b, x, s, t); }
function calcMD5(str) {x = str2blks_MD5(str); a =  1732584193; b = -271733879; c = -1732584194; d =  271733878; for(i = 0; i < x.length; i += 16) {olda = a; oldb = b; oldc = c; oldd = d; a = ff(a, b, c, d, x[i+ 0], 7 , -680876936); d = ff(d, a, b, c, x[i+ 1], 12, -389564586); c = ff(c, d, a, b, x[i+ 2], 17,  606105819); b = ff(b, c, d, a, x[i+ 3], 22, -1044525330); a = ff(a, b, c, d, x[i+ 4], 7 , -176418897); d = ff(d, a, b, c, x[i+ 5], 12,  1200080426); c = ff(c, d, a, b, x[i+ 6], 17, -1473231341); b = ff(b, c, d, a, x[i+ 7], 22, -45705983); a = ff(a, b, c, d, x[i+ 8], 7 ,  1770035416); d = ff(d, a, b, c, x[i+ 9], 12, -1958414417); c = ff(c, d, a, b, x[i+10], 17, -42063); b = ff(b, c, d, a, x[i+11], 22, -1990404162); a = ff(a, b, c, d, x[i+12], 7 ,  1804603682); d = ff(d, a, b, c, x[i+13], 12, -40341101); c = ff(c, d, a, b, x[i+14], 17, -1502002290); b = ff(b, c, d, a, x[i+15], 22,  1236535329); a = gg(a, b, c, d, x[i+ 1], 5 , -165796510); d = gg(d, a, b, c, x[i+ 6], 9 , -1069501632); c = gg(c, d, a, b, x[i+11], 14,  643717713); b = gg(b, c, d, a, x[i+ 0], 20, -373897302); a = gg(a, b, c, d, x[i+ 5], 5 , -701558691); d = gg(d, a, b, c, x[i+10], 9 ,  38016083); c = gg(c, d, a, b, x[i+15], 14, -660478335); b = gg(b, c, d, a, x[i+ 4], 20, -405537848); a = gg(a, b, c, d, x[i+ 9], 5 ,  568446438); d = gg(d, a, b, c, x[i+14], 9 , -1019803690); c = gg(c, d, a, b, x[i+ 3], 14, -187363961); b = gg(b, c, d, a, x[i+ 8], 20,  1163531501); a = gg(a, b, c, d, x[i+13], 5 , -1444681467); d = gg(d, a, b, c, x[i+ 2], 9 , -51403784); c = gg(c, d, a, b, x[i+ 7], 14,  1735328473); b = gg(b, c, d, a, x[i+12], 20, -1926607734); a = hh(a, b, c, d, x[i+ 5], 4 , -378558); d = hh(d, a, b, c, x[i+ 8], 11, -2022574463); c = hh(c, d, a, b, x[i+11], 16,  1839030562); b = hh(b, c, d, a, x[i+14], 23, -35309556); a = hh(a, b, c, d, x[i+ 1], 4 , -1530992060); d = hh(d, a, b, c, x[i+ 4], 11,  1272893353); c = hh(c, d, a, b, x[i+ 7], 16, -155497632); b = hh(b, c, d, a, x[i+10], 23, -1094730640); a = hh(a, b, c, d, x[i+13], 4 ,  681279174); d = hh(d, a, b, c, x[i+ 0], 11, -358537222); c = hh(c, d, a, b, x[i+ 3], 16, -722521979); b = hh(b, c, d, a, x[i+ 6], 23,  76029189); a = hh(a, b, c, d, x[i+ 9], 4 , -640364487); d = hh(d, a, b, c, x[i+12], 11, -421815835); c = hh(c, d, a, b, x[i+15], 16,  530742520); b = hh(b, c, d, a, x[i+ 2], 23, -995338651); a = ii(a, b, c, d, x[i+ 0], 6 , -198630844); d = ii(d, a, b, c, x[i+ 7], 10,  1126891415); c = ii(c, d, a, b, x[i+14], 15, -1416354905); b = ii(b, c, d, a, x[i+ 5], 21, -57434055); a = ii(a, b, c, d, x[i+12], 6 ,  1700485571); d = ii(d, a, b, c, x[i+ 3], 10, -1894986606); c = ii(c, d, a, b, x[i+10], 15, -1051523); b = ii(b, c, d, a, x[i+ 1], 21, -2054922799); a = ii(a, b, c, d, x[i+ 8], 6 ,  1873313359); d = ii(d, a, b, c, x[i+15], 10, -30611744); c = ii(c, d, a, b, x[i+ 6], 15, -1560198380); b = ii(b, c, d, a, x[i+13], 21,  1309151649); a = ii(a, b, c, d, x[i+ 4], 6 , -145523070); d = ii(d, a, b, c, x[i+11], 10, -1120210379); c = ii(c, d, a, b, x[i+ 2], 15,  718787259); b = ii(b, c, d, a, x[i+ 9], 21, -343485551); a = add(a, olda); b = add(b, oldb); c = add(c, oldc); d = add(d, oldd); } return rhex(a) + rhex(b) + rhex(c) + rhex(d); }
function md5(text) {return calcMD5(text); }
