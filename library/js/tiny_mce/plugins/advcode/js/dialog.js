tinyMCEPopup.requireLangPack();


function str_replace (search, replace, subject, count) {
    // Replaces all occurrences of search in haystack with replace
            f = [].concat(search),
            r = [].concat(replace),
            s = subject,
            ra = r instanceof Array, sa = s instanceof Array;    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }
     for (i=0, sl=s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }
        for (j=0, fl=f.length; j < fl; j++) {            temp = s[i]+'';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {
                this.window[count] += (temp.length-s[i].length)/f[j].length;}        }
    }
    return sa ? s : s[0];
}

var codemirror_editor = '';
var CodeDialog = {
  init : function() {

	// resize window
	//window.moveTo(0,0);
	//window.resizeTo(screen.availWidth,screen.availHeight);

    var f = document.forms[0];

	src = tinyMCEPopup.editor.getContent();
	src = str_replace('">', '">\n', src);
	//src = str_replace('<p', '\n\n<p', src);
	//src = str_replace('<p>', '<p>\n', src);
	src = str_replace('</p>', '</p>\n', src);

	src = str_replace('<div', '\n\n<div', src);
	src = str_replace('</div>', '</div>\n\n', src);

	// h2, h3, h4, h5
	for(k=2; k <= 5; k++)
	{
		src = str_replace('<h'+k, '\n\n<h'+k, src);
		src = str_replace('</h'+k+'>', '</h'+k+'>\n\n', src);
	}
	


	src = str_replace('<br>', '<br>\n', src);
	src = str_replace('<br />', '<br />\n', src);	
	src = str_replace('<li>', '    \n<li>', src);	
	src = str_replace('<ul>', '\n\n<ul>', src);
	src = str_replace('</ul>', '\n</ul>\n\n', src);
	src = str_replace('<ol>', '\n\n<ol>', src);
	src = str_replace('</ol>', '\n</ol>\n\n', src);

	src = str_replace('<tr', '\n<tr', src);
	src = str_replace('<td', '\n    <td', src);
	src = str_replace('<td>', '<td>\n        ', src);
	src = str_replace('</td>', '\n    </td>', src);
	src = str_replace('</tr>', '\n</tr>', src);
	src = str_replace('</tbody>', '\n</tbody>', src);
	src = str_replace('</table>', '\n</table>', src);
	src = str_replace(' <table', '<table', src);

	src = str_replace('<pre', '\n\n<pre', src);
	src = str_replace('</pre>', '\n</pre>\n\n', src);

	src = str_replace('\n\n\n', '\n\n', src);
	src = str_replace('</p>\n<p', '</p>\n\n<p', src);

	src = str_replace('%20%20%20%20', '    ', src);
	src = str_replace('\t', '    ', src);

	src = trim(src);

    f.codepress.value = src;

	tinyMCEPopup.codemirror = CodeMirror.fromTextArea('codepress', {
			parserfile: ["parsexml"],
			path: "/library/js/codemirror/js/",
			lineNumbers: false,
			indentUnit:0,
			stylesheet: "/library/js/codemirror/css/xmlcolors.css"
		});


  },

  insert : function() {
    v = tinyMCEPopup.codemirror.getCode();
	v = str_replace('    ', '\t' , v);

    tinyMCEPopup.editor.setContent(v);
    tinyMCEPopup.close();
  }
};

tinyMCEPopup.onInit.add(CodeDialog.init, CodeDialog);