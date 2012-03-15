tinyMCEPopup.requireLangPack();
var editor = "";
  
var Code2Dialog = {
	init : function() {
		var f = document.forms[0];

		// Get the selected contents as text and place it in the input
		// f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		// f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
		src = tinyMCEPopup.editor.getContent();
		f.source.value = src;
		
		editor = CodeMirror.fromTextArea(
										document.getElementById("source"), 
										{
											mode: "htmlmixed", 
											lineNumbers: true,
											tabMode: "indent"
										});	
		CodeMirror.commands["selectAll"](editor);
		editor.autoFormatRange(editor.getCursor(true), editor.getCursor(false));
		
		editor.scrollTo(0,0);
		
	},

	insert : function() {
		// Insert the contents from the input into the document
		//tinyMCEPopup.editor.execCommand('mceInsertContent', false, document.forms[0].someval.value);
		tinyMCEPopup.editor.setContent(editor.getValue());
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(Code2Dialog.init, Code2Dialog);
