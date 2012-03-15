tinyMCEPopup.requireLangPack();

var GtranslateDialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		// alert(tinyMCEPopup.editor.selection.getContent());
		// f.from.value = tinyMCEPopup.editor.selection.getContent();
		f.source.value = tinyMCEPopup.editor.selection.getContent();
		// gTranslateX();		
		// f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
	},

	insert : function() {
		// Insert the contents from the input into the document
		if(document.forms[0].to.value != '')
			tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].translate.value);
		
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(GtranslateDialog.init, GtranslateDialog);
