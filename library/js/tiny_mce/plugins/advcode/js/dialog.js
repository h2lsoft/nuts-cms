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