tinyMCEPopup.requireLangPack();

var WordCount2Dialog = {
	init : function() {
		
		var f = document.forms[0];
		
		content = tinyMCEPopup.editor.getContent({format:'text'});
		content = strip_tags(content);
		content = trim(content);
		content_original = content;
		
		// words
		if(content == '')
			document.getElementById('words').innerHTML = 0;
		else
		{
			content_w = content.replace(/[^a-zA-Z0-9 éèêëàâäùüôöïî.]+/g, '');
			document.getElementById('words').innerHTML = str_word_count(content_w);
		}
				
		// spaces without spaces		
		content_no_space = strtolower(content);		
		content_no_space =  content_no_space.replace(/[^a-zA-Z0-9éèêëàâäùüôöïî.;%()!?,-\\'"]+/g, '');		
		document.getElementById('cars1').innerHTML = strlen(content_no_space);
		
		// with spaces
		document.getElementById('cars2').innerHTML = strlen(content_original);				
	}
};

tinyMCEPopup.onInit.add(WordCount2Dialog.init, WordCount2Dialog);