var lastWYSIWYG = array();
var WYSIWYG_LAST_ID = '';

function getIFrameDocument(aID){

  // if contentDocument exists, W3C compliant (Mozilla)
  if (document.getElementById(aID).contentWindow){
    return document.getElementById(aID).contentWindow.document;
  } else {
    // IE
    return document.frames[aID].document;
  }
}

function getIFrameWindow(aID){
    if (document.getElementById(aID).contentWindow){
        return document.getElementById(aID).contentWindow;
    } else {
        // IE
        return document.frames[aID];
    }
}



function initWYSIWYGOption()
{
    sep = ' &nbsp; <span class="rte_separator">|</span> &nbsp; ';

	$('textarea.mceEditor').each(function (){
		id = this.id;

		str = '';
		// str += '<input type="checkbox" id="_WYSIWYG_'+id+'" onclick="toggleEditor(\''+id+'\', this.checked)" />';

		str += '<p>';
		str += '<label title="">&nbsp;</label>';
		str += '<div id="'+id+'_WYSIWYG_toolbar" class="WYSIWYG_toolbar" style="margin:0;padding:0px;">';

		// help
		msg = 'Help:\\n';
		msg += '==========================\\n';
		msg += 'Ctrl + E: Editor\\n\\n';

		msg += 'Alt + B: Bold\\n';
		msg += 'Alt + I: Italic\\n';
		msg += 'Alt + U: Underline\\n\\n';
		msg += 'Alt + 1: H1\\n';
		msg += 'Alt + 2: H2\\n';
		msg += 'Alt + 3: H3\\n\\n';
		msg += 'Alt + L: List\\n\\n';

		msg += 'Ctrl + Z: Undo\\n';
		msg += 'Ctrl + Y: Redo\\n';

		str += '<img class="rte_button" onmouseover="$(body).css(\'cursor\', \'help\');" onmouseout="$(body).css(\'cursor\', \'default\');" title="Help" src="img/icon-help.png" align="absmiddle"  onclick="alert(\''+msg+'\')" />';

		// repaint
        str += sep;
        str += '<img  class="rte_button" title="Repaint" onclick="javascript:refreshWYSIWYG(\''+id+'\');" src="img/icon-refresh.png" align="absmiddle" />';


        // simple rte
        str += sep;
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'bold\');" src="img/rte/b.gif" align="absmiddle" />';
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'italic\');" src="img/rte/i.gif" align="absmiddle" />';
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'underline\');" src="img/rte/u.gif" align="absmiddle" />';
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'strikeThrough\');" src="img/rte/strike.png" align="absmiddle" />';
        str += sep;
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'insertUnorderedList\');" src="img/rte/ul.gif" align="absmiddle" />';
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'insertOrderedList\');" src="img/rte/ol.gif" align="absmiddle" />';
        str += sep;
        str += '<img class="rte_button" id="'+id+'_WYSIWYG_submenu_url_parent" onclick="javascript:WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');" src="img/rte/link.gif" align="absmiddle" />';

        // add sub menu
        lbl_library = 'from library';
        lbl_file = 'File';
        lbl_custom = 'Custom...';

        if(nutsUserLang  == 'fr'){
            lbl_library = 'de la bibliothèque';
            lbl_file = 'Fichier';
            lbl_custom = 'Personnalisée...';
        }

        str += '<div id="'+id+'_WYSIWYG_submenu_url" class="WYSIWYG_submenu">';
        str += '<a tabindex="0" href="javascript:imgBrowser(\''+id+'\', \'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">Image '+lbl_library+'</a><br />';
        str += '<a tabindex="0" href="javascript:mediaBrowser(\''+id+'\', \'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">Media '+lbl_library+'</a><br />';
        str += '<a tabindex="0" href="javascript:allBrowser(\''+id+'\', \'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">'+lbl_file+' '+lbl_library+'</a><br />';
        str += '---------------------------------<br />';
        str += '<a tabindex="0" href="javascript:cmdWYSIWYG(\''+id+'\', \'link\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">'+lbl_custom+'</a><br />';
        str += '</div>';


        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'unlink\');" src="img/rte/unlink.gif" align="absmiddle" />';
        str += sep;

		// gallery
		str += ' <img class="rte_button" title="'+nuts_lang_msg_72+'" src="img/icon-preview-mini.gif" align="absmiddle" onclick="popupModal(\'index.php?mod=_gallery&do=list&popup=1&parent_refresh=no&parentID='+id+'\');" />';
		str += ' <img class="rte_button" src="img/icon-media.png" align="absmiddle" title="'+nuts_lang_msg_73+'" onclick="popupModal(\'index.php?mod=_media&do=list&popup=1&parent_refresh=no&parentID='+id+'\');" />';
		str += ' <img class="rte_button" src="/nuts/img/widget.png" title="Widgets" align="absmiddle" onclick="widgetsWindowOpen(\''+id+'\');" />';

        // rte
        str += sep;
        str += '<img src="img/icon-code_editor.png" align="absmiddle" />';
        str += ' <a href="javascript:openWYSIWYG(\''+id+'\');" tabindex="0">Rich Editor</a>'

        // source
        str += sep;
        str += ' <input tabindex="-1" type="checkbox" id="iframe_radio_'+id+'" onclick="WYSIWYGToggleIt(\''+id+'\');" /> Source';

		str += '</div>';
		str += '</p>';

		// $('#'+id).before('<p><label>&nbsp;</label>'+str+'</p>');
		$('textarea#'+id).parent('p:visible').before(str);
	});
}





function widgetsWindowOpen(id)
{
	uri = 'widgets.php?parentID='+id;
	width = 1024;
	height = 650;

    popupModal(uri, '', width, height, 'resizable=no');

}


function iframeContentProtector(id)
{

}

function WYSIWYGToggleIt(id)
{
    // WYSIWYG mode
    if(!$('#iframe_radio_'+id).is(':checked'))
	{
		// resizer grip hacks
		height = $('#former #'+id).css('height');
		$('#iframe_'+id).css('height', height+"px");
		$('textarea#'+id).hide();
		$('#iframe_'+id).show();

        $('#'+id+'_WYSIWYG_toolbar img, '+'#'+id+'_WYSIWYG_toolbar a,'+'#'+id+'_WYSIWYG_toolbar .rte_separator').css('visibility', 'visible');


		WYSIWYGIFrameReload(id);
	}
	else
	{


		$('textarea#'+id).show();
		$('#iframe_'+id).hide();
		WYSIWYGTextareaReload(id);

        $('#'+id+'_WYSIWYG_toolbar img, '+'#'+id+'_WYSIWYG_toolbar a,'+'#'+id+'_WYSIWYG_toolbar .rte_separator').css('visibility', 'hidden');
	}
}

var secondPassfunction = false; // prevent twice times function
function WYSIWYGEventFocus(id, e)
{
	// secondPassfunction = false;
}

function WYSIWYGEvent(id, e, shortcut)
{
	if(!secondPassfunction)
	{
		secondPassfunction = true;
	}
	else
	{
		secondPassfunction = false;
		return;
	}

	// do not use css
	getIFrameDocument('iframe_'+id).execCommand('styleWithCSS', null, false);

	notcancelkey = true;
	if(shortcut)
	{
		// ctrl + E
		if(e.ctrlKey && e.charCode == 101)
		{
			openWYSIWYG(id);
			notcancelkey = false;
		}
		// Alt + B
		else if(e.altKey && e.charCode == 98)
		{
			// log("Ctrl + B detected !");
			getIFrameDocument('iframe_'+id).execCommand('bold', false, null );
			notcancelkey = false;
		}
		// Alt + I
		else if(e.altKey && e.charCode == 105)
		{
			getIFrameDocument('iframe_'+id).execCommand('italic', false, null );
			notcancelkey = false;
		}
		// Alt + U
		else if(e.altKey && e.charCode == 117)
		{
			getIFrameDocument('iframe_'+id).execCommand('underline', false, null );
			notcancelkey = false;
		}

		// Alt + 1
		else if(e.altKey && (e.charCode == 38 || e.charCode == 49))
		{
			getIFrameDocument('iframe_'+id).execCommand('heading', false, 'H1');
			notcancelkey = false;
		}
		// alt + 2
		else if(e.altKey && (e.charCode == 233 || e.charCode == 50))
		{
			getIFrameDocument('iframe_'+id).execCommand('heading', false, 'H2');
			notcancelkey = false;
		}
		// alt + 3
		else if(e.altKey && (e.charCode == 34 || e.charCode == 51))
		{
			getIFrameDocument('iframe_'+id).execCommand('heading', false, 'H3');
			notcancelkey = false;
		}
		// alt + L
		else if(e.altKey && e.charCode == 108)
		{
			getIFrameDocument('iframe_'+id).execCommand('insertUnorderedList', false, null );
			notcancelkey = false;
		}

		// TAB
		else if(e.charCode == 0 && e.keyCode == 9)
		{
			getIFrameDocument('iframe_'+id).execCommand('indent', false, null );
			notcancelkey = false;
		}

	}


	WYSIWYGTextareaReload(id);

	//e.cancelBubble is supported by IE - this will kill the bubbling process.
	if(!notcancelkey)
	{
		e.stopPropagation ? e.stopPropagation() : e.cancelBubble = true;
		e.preventDefault();

		// focus on
		// getIFrameDocument('iframe_'+id).focus();


		return false;
	}

	return true;


}

function refreshWYSIWYG(id)
{
	if(!$('#iframe_radio_'+id).is(':checked'))
	{
		WYSIWYGTextareaReload(id);
	}
	initWYSIWYGIFrame(id);
}

function initWYSIWYGIFrame(id)
{

	// initialize preview frame
	code_source_mode = false;
	if($('#former #iframe_radio_'+id).is(':checked'))
		code_source_mode = true;


	$('#iframe_'+id).remove();
	//if($('textarea#'+id).is(':visible'))
	//{
		width = $('#former #'+id).css('width');
		height = $('#former #'+id).css('height');

		iframe = '<iframe code_source="'+code_source_mode+'" style="width:'+width+'; height:'+height+';" name="iframe_'+id+'" class="nuts_editor" id="iframe_'+id+'" frameborder="0"></iframe>';
		$('textarea#'+id).before(iframe);
		$('textarea#'+id).hide();
	//}

	   setTimeout( function() {

		// detect event in
		obj = document.getElementById('iframe_'+id).contentWindow;
		if(obj.addEventListener)
		{
			obj.addEventListener('focus', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEventFocus(id, e);}, false);
			obj.addEventListener('keypress', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, true);}, false);
			obj.addEventListener('mousedown', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, false);}, false);
		}
		else
		{
			obj.attachEvent('focus', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEventFocus(id, e);}, false);
			obj.attachEvent('keypress', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, true);}, false);
			obj.attachEvent('mousedown', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, false);}, false);
		}

		// parse nuts tags
		getIFrameDocument('iframe_'+id).designMode = 'on';
		getIFrameDocument('iframe_'+id).body.innerHTML = $('textarea#'+id).val();

		head = getIFrameDocument('iframe_'+id).getElementsByTagName('head')[0];
		link = document.createElement('link');
		link.setAttribute('rel',"stylesheet");
		link.setAttribute('href',"/library/themes/editor_css.php?t="+current_theme+"&ncache="+time());
		link.setAttribute('type',"text/css");
		head.appendChild(link);

		if($('#iframe_'+id).attr('code_source') == 'true')
		{
			$('#former  #iframe_radio_'+id).attr('checked', true);
			WYSIWYGToggleIt(id);
		}


	}, 500);

	// $('#iframe_'+objID).show();

}

function WYSIWYGTextareaReload(id)
{
	v = getIFrameDocument('iframe_'+id).body.innerHTML;

	// protect data uri
	// %7B@NUTS%09TYPE=%27PAGE%27%09CONTENT=%27URL%27%09ID=%271%27%7D
	v = str_replace('%7B', '{', v);
	v = str_replace('%09', '	', v);
	v = str_replace('%27', "'", v);
	v = str_replace('%7D', "}", v);

	$('textarea#'+id).val(v);
}

function WYSIWYGIFrameReload(id)
{
	getIFrameDocument('iframe_'+id).body.innerHTML = $('textarea#'+id).val();
}

function openWYSIWYG(objID)
{
	t = time();

	uri = '../library/js/tiny_mce.php?theme='+current_theme+'&objID='+objID+'&lang='+nutsCurrentPageLang+'&t='+t;
	windowWidth = 1024;
	windowHeight = 768;
	opts = '';

	popupModal(uri, 'RichEditor', windowWidth, windowHeight, opts);

}

function closeWYSIWYG()
{
	$('body').css('overflow', '');
	//$('div#richeditor_layer').hide();
	$('iframe#richeditor_content').hide()

}



function forceWYSIWYGUpdate()
{
	$('#former textarea.mceEditor').each(function (){
		id = this.id;
		initWYSIWYGIFrame(id);
	});
}

function WYSIWYGhackSubmit()
{
	$('textarea.mceEditor').each(function (){
		id = this.id;
		// we repaint only in WYSIWYG mode
		if(!$('#former #'+id).is(':visible'))
		{
			WYSIWYGTextareaReload(id);
		}

	});
}

function loadRichEditor()
{
	initWYSIWYGOption();
}


function RTERefresh()
{
	//$('iframe#richeditor_content').remove();
	//t = time();
	//iframe_c = '<iframe src="../library/js/tiny_mce.php?theme=default&t='+t+'" name="richeditor_content" id="richeditor_content" frameborder="0" scrolling="no" width="90%" height="90%"></iframe>';
	//$('#richeditor_layer').after(iframe_c);
	// $('iframe#richeditor_content').attr('src', '../library/js/tiny_mce.php?theme=default&t='+t);
}


function WYSIWYGAddText(id, txt)
{
	txt = str_replace('``', '"', txt);

	// mode html
	if(!$('#iframe_radio_'+id).is(':checked'))
	{
		c = getIFrameDocument('iframe_'+id).body.innerHTML;
		c = trim(c);
		if(c == '<br>' || c == '<br />' || c == '<BR>')
		{
			getIFrameDocument('iframe_'+id).body.innerHTML = txt;
		}
		else
		{
			getIFrameDocument('iframe_'+id).body.innerHTML += txt;
		}

		WYSIWYGTextareaReload(id);
	}
	else
	{
		v = $('textarea#'+id).val();
		v += txt;

		$('textarea#'+id).val(v);
	}
}



function cmdWYSIWYG(id, action, data)
{
    // source mode no command
    if($('#iframe_radio_'+id).is(':checked'))return;


    if(action == 'link')
    {
        var url = prompt("URL", 'http://');

        // On vérifie qu'on a bien tapé quelque chose
        if (url != null && url != '') {
            action = 'createLink';
            data = url;
        }
    }


    getIFrameDocument('iframe_'+id).execCommand(action, false, data);
    getIFrameWindow('iframe_'+id).focus();
}





function WYSIWYGSubMenu(id){

    if($('#'+id).is(':visible'))
    {
        $('#'+id).hide();
    }
    else
    {
        pos = $('#'+id+'_parent').position();
        $('#'+id).css('left', pos.left+'px');

        $('#'+id).show();
    }
}







// deprecated ********************************************************
function killWYSIWYG(){
}

function toggleEditor(id, checktest){
}


