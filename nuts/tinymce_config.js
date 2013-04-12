var lastWYSIWYG = array();
var WYSIWYG_LAST_ID = '';

var ctrlKeyIsPressed = false;
var altKeyIsPressed = false;

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
    sep = '&nbsp;<span class="rte_separator">|</span>&nbsp;';

	$('textarea.mceEditor').each(function (){
		id = this.id;

		str = '';
		// str += '<input type="checkbox" id="_WYSIWYG_'+id+'" onclick="toggleEditor(\''+id+'\', this.checked)" />';

		str += '<p>';
		str += '<label title="">&nbsp;</label>';
		str += '<div id="'+id+'_WYSIWYG_toolbar" class="WYSIWYG_toolbar" style="margin:0;padding:0px;">';

        str += ' <img class="rte_button" onclick="javascript:WYSIWYGPaste(\''+id+'\');" src="img/rte/paste2.png" align="absmiddle" style="width:12px" /> ';
        str += sep;

        // simple rte
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'bold\');" src="img/rte/B.png" align="absmiddle" /> ';
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'italic\');" src="img/rte/I.png" align="absmiddle" /> ';
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'underline\');" src="img/rte/U.png" align="absmiddle" /> ';
		str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'strikeThrough\');" src="img/rte/S.png" align="absmiddle" /> ';

        str += sep;
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'justifyLeft\');" src="img/rte/align-left.png" align="absmiddle" /> ';
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'justifyCenter\');" src="img/rte/align-center.png" align="absmiddle" /> ';
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'justifyRight\');" src="img/rte/align-right.png" align="absmiddle" /> ';
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'justifyFull\');" src="img/rte/align-justify.png" align="absmiddle" /> ';

        str += sep;
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'insertUnorderedList\');" src="img/rte/UL.png" align="absmiddle" /> ';
        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'insertOrderedList\');" src="img/rte/OL.png" align="absmiddle" /> ';
        str += sep;


        select = '<select onchange="WYSIWYGFormat(\''+id+'\');">';
        select += ' <option class="title">Format</option>';
        select += ' <option value="H1">H1</option>';
        select += ' <option value="H2">H2</option>';
        select += ' <option value="H3">H3</option>';
        select += ' <option value="P">P</option>';
        select += ' <option value="BLOCKQUOTE">Blockquote</option>';


        // justifié
        /*p_label = (nutsUserLang == 'fr') ? 'Alignement' : 'Alignement';
        select += ' <option class="title">'+p_label+'</option>';

        p_label = (nutsUserLang == 'fr') ? 'Gauche' : 'Left';
        select += ' <option value="P-LEFT">'+p_label+'</option>';

        p_label = (nutsUserLang == 'fr') ? 'Centre' : 'Center';
        select += ' <option value="P-CENTER">'+p_label+'</option>';

        p_label = (nutsUserLang == 'fr') ? 'Droite' : 'Right';
        select += ' <option value="P-RIGHT">'+p_label+'</option>';

        p_label = (nutsUserLang == 'fr') ? 'Justifié' : 'Justify';
        select += ' <option value="P-FULL">'+p_label+'</option>';
        */


        select += '</select>';

        str += select;
        str += ' <img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'removeFormat\');" src="img/rte/X.png" align="absmiddle" /> ';

        str += sep;
        str += ' <img class="rte_button" id="'+id+'_WYSIWYG_submenu_url_parent" onclick="javascript:WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');" src="img/rte/A.png" align="absmiddle" /> ';

        // add sub menu
        lbl_library = 'from library';
        lbl_file = 'File';
        lbl_custom = 'Custom...';
        lbl_preview = 'Preview';
        lbl_browse = 'Browse';

        if(nutsUserLang  == 'fr'){
            lbl_library = 'de la bibliothèque';
            lbl_file = 'Fichier';
            lbl_custom = 'Personnalisée...';
            lbl_preview = 'Prévisualiser';
            lbl_browse = 'Parcourir...';
        }

        str += '<div id="'+id+'_WYSIWYG_submenu_url" class="WYSIWYG_submenu">';
        str += '<a tabindex="0" href="javascript:imgBrowser(\''+id+'\', \'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">Image '+lbl_library+'</a><br />';
        str += '<a tabindex="0" href="javascript:mediaBrowser(\''+id+'\', \'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">Media '+lbl_library+'</a><br />';
        str += '<a tabindex="0" href="javascript:allBrowser(\''+id+'\', \'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">'+lbl_file+' '+lbl_library+'</a><br />';
        // str += '----------------------------<br />';
        str += '<hr />';
        str += '<a tabindex="0" href="javascript:cmdWYSIWYG(\''+id+'\', \'link\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">'+lbl_custom+'</a><br />';
        str += '</div>';


        str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'unlink\');" src="img/rte/Ax.png" align="absmiddle" /> ';
        str += sep;

		// gallery
        str += ' <img class="rte_button" src="/nuts/img/rte/browse.png" align="absmiddle" title="Images" onclick="imgBrowser(\'imgTag_'+id+'\', \'\');" />';
		str += ' <img class="rte_button" style="width:16px;" title="'+nuts_lang_msg_72+'" src="/plugins/_gallery/icon.png" align="absmiddle" onclick="popupModal(\'index.php?mod=_gallery&do=list&popup=1&parent_refresh=no&parentID='+id+'\');" />';
		// str += ' <img class="rte_button" src="img/icon-media.png" align="absmiddle" title="'+nuts_lang_msg_73+'" onclick="popupModal(\'index.php?mod=_media&do=list&popup=1&parent_refresh=no&parentID='+id+'\');" />';

        // media submenu
        str += ' <img class="rte_button" src="img/icon-media.png" align="absmiddle" title="'+nuts_lang_msg_73+'" id="'+id+'_WYSIWYG_submenu_media_parent" onclick="javascript:WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');" />';

        str += '<div id="'+id+'_WYSIWYG_submenu_media" class="WYSIWYG_submenu">';
        str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=YOUTUBE%20VIDEO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/youtube.png" style="width:16px" /> Youtube</a><br />';
        str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=DAILYMOTION&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/dailymotion.png" style="width:16px" /> Dailymotion</a><br />';
        str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=EMBED%20CODE&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/embed.png" style="width:16px" /> Embed code</a><br />';
        str += '<hr />';
        str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=IFRAME&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/iframe.png" style="width:16px" /> Iframe</a><br />';
        str += '<hr />';
        str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=VIDEO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/video.png" style="width:16px" /> Video</a><br />';
        str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=AUDIO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/audio.png" style="width:16px" /> Audio</a><br />';
        str += '</div>';



		str += ' <img class="rte_button" src="/nuts/img/widget.png" title="Widgets" align="absmiddle" onclick="widgetsWindowOpen(\''+id+'\');" /> ';


        // richeditor
        str += sep;
        str += '<input type="button" class="button" value="Richeditor" onclick="openWYSIWYG(\''+id+'\');" tabindex="0" />';


        // rte
        str += sep;

        // repaint
        str += ' <img  class="rte_button" title="Repaint" onclick="javascript:refreshWYSIWYG(\''+id+'\');" src="img/icon-refresh.png" align="absmiddle" /> ';

        // help
        menu_sep = '--------------------------------\\n';
        msg = '<b>Help :</b>\\n';
        msg += '==========================\\n';
        msg += 'Ctrl + Alt + E: RichEditor\\n';
        msg += menu_sep;
        msg += 'Ctrl + B: Bold\\n';
        msg += 'Ctrl + I: Italic\\n';
        msg += 'Ctrl + U: Underline\\n';
        msg += 'Ctrl + S: Striked\\n';
        msg += menu_sep;
        msg += 'Ctrl + 1: H1\\n';
        msg += 'Ctrl + 2: H2\\n';
        msg += 'Ctrl + 3: H3\\n';
        msg += 'Ctrl + 4: Paragraph\\n';
        msg += menu_sep;
        msg += 'Ctrl + L: List\\n';
        msg += 'Ctrl + M: Ordered List\\n';
        msg += menu_sep;
        msg += 'Ctrl + Q: Blockquote\\n';
        msg += menu_sep;
        msg += 'Ctrl + Z: Undo\\n';
        msg += 'Ctrl + Y: Redo\\n';
        msg = str_replace('\\n', '<br>', msg);

        str += ' <span class="tooltip yellow-tooltip tooltip-middle tooltip-middle-right"><img class="rte_button" src="img/rte/help.png" align="absmiddle" /><dd>'+msg+'</dd></span> ';


        str += sep;


        // source
        str += ' <input tabindex="-1" type="checkbox" id="iframe_radio_'+id+'" onclick="WYSIWYGToggleIt(\''+id+'\');" /><img style="cursor:pointer;" class="nohide" src="/nuts/img/rte/HTML.png" align="absmiddle" onclick="$(\'#iframe_radio_\'+\''+id+'\').click();" />';

		str += '</div>';
		str += '</p>';

		// $('#'+id).before('<p><label>&nbsp;</label>'+str+'</p>');
        if($('#'+id+'_WYSIWYG_toolbar').length == 0)
            $('textarea#'+id).parent('p:visible').before(str);

	});
}





function widgetsWindowOpen(id) {
	uri = 'widgets.php?parentID='+id;
	width = 1024;
	height = 650;

    popupModal(uri, '', width, height, 'resizable=no');
}


function iframeContentProtector(id){
}


function WYSIWYGToggleIt(id)
{
    objs = '#'+id+'_WYSIWYG_toolbar select, #'+id+'_WYSIWYG_toolbar input[type=button], #'+id+'_WYSIWYG_toolbar img, '+'#'+id+'_WYSIWYG_toolbar a,'+'#'+id+'_WYSIWYG_toolbar .rte_separator, '+'#'+id+'_WYSIWYG_toolbar span';
    obj_source = '#'+id+'_WYSIWYG_toolbar .nohide';

    // WYSIWYG mode
    if(!$('#iframe_radio_'+id).is(':checked'))
	{
		// resizer grip hacks
		height = $('#former #'+id).css('height');
		$('#iframe_'+id).css('height', height+"px");
		$('textarea#'+id).hide();
		$('#iframe_'+id).show();

        $(objs).css('visibility', 'visible');



		WYSIWYGIFrameReload(id);
	}
	else
	{
		$('textarea#'+id).show();
		$('#iframe_'+id).hide();
		WYSIWYGTextareaReload(id);

        $(objs).css('visibility', 'hidden');

	}

    $(obj_source).css('visibility', 'visible');
}

var secondPassfunction = false; // prevent twice times function
function WYSIWYGEventFocus(id, e)
{
	// secondPassfunction = false;
}


function WYSIWYGEvent(id, e, shortcut){

    // detect keyup, keydown
    if(e.type == 'keydown' || e.type == 'keyup'){
        return;
    }

    // WYSIWYGTextareaReload(id);

}



function refreshWYSIWYG(id)
{
	if(!$('#iframe_radio_'+id).is(':checked'))
	{
		WYSIWYGTextareaReload(id);
	}
	initWYSIWYGIFrame(id);
}

function initWYSIWYGIFrame(id) {

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
		obj.addEventListener('focus', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEventFocus(id, e);}, false);
		obj.addEventListener('keyup', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, true);}, false);
		obj.addEventListener('keydown', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, true);}, false);
        obj.addEventListener('mousedown', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, false);}, false);
        obj.addEventListener('keypress', function(e){id = str_replace('iframe_', '', this.name);WYSIWYGEvent(id, e, true);}, false);

        // obj.addEventListener('paste', function(e){e.preventDefault();}, false);


        // add special shortcut catcher for Chrome
        // tools: http://jonathan.tang.name/files/js_keycode/test_keycode.html
        // if(BrowserDetect.browser == 'Chrome'){

           if(BrowserDetect.browser == 'Firefox'){
               // shortcut.remove("Ctrl+B");
               // shortcut.remove("Ctrl+I");
               // shortcut.remove("Ctrl+U");

           }
           // shortcut.remove("Ctrl+Alt+E");

           // shortcut.remove("Ctrl+S");
           // shortcut.remove("Ctrl+L");
           // shortcut.remove("Ctrl+M");
           // shortcut.remove("Ctrl+Q");
           // shortcut.remove("Ctrl+0");
           // shortcut.remove("Ctrl+1");
           // shortcut.remove("Ctrl+2");
           //  shortcut.remove("Ctrl+3");
           // shortcut.remove("Esc");

            setTimeout( function() {

                cur_target = getIFrameWindow('iframe_'+id);
                if(BrowserDetect.browser == 'Chrome')
                    cur_target = getIFrameDocument('iframe_'+id);

                if(BrowserDetect.browser == 'Firefox'){
                    shortcut.add('Ctrl+B', function(){ cmdWYSIWYG(id, 'bold', '');}, {'target':cur_target});
                    shortcut.add('Ctrl+I', function(){ cmdWYSIWYG(id, 'italic', '');}, {'target':cur_target});
                    shortcut.add('Ctrl+U', function(){ cmdWYSIWYG(id, 'underline', '');}, {'target':cur_target});
                }

                shortcut.add('Ctrl+Alt+E', function(){openWYSIWYG(id);}, {'target':cur_target});
                shortcut.add('Ctrl+S', function(){ cmdWYSIWYG(id, 'strikeThrough', '');}, {'target':cur_target});

                shortcut.add('Ctrl+L', function(){cmdWYSIWYG(id, 'insertUnorderedList', '');}, {'target':cur_target});
                shortcut.add('Ctrl+M', function(){cmdWYSIWYG(id, 'insertOrderedList', '');}, {'target':cur_target});
                shortcut.add('Ctrl+Q', function(){cmdWYSIWYG(id, 'formatBlock', 'BLOCKQUOTE');}, {'target':cur_target});
                shortcut.add('Ctrl+0', function(){cmdWYSIWYG(id, 'removeFormat', ''); cmdWYSIWYG(id, 'formatBlock', 'P');}, {'target':cur_target});
                shortcut.add('Ctrl+1', function(){cmdWYSIWYG(id, 'formatBlock', 'H1');}, {'target':cur_target});
                shortcut.add('Ctrl+2', function(){cmdWYSIWYG(id, 'formatBlock', 'H2');}, {'target':cur_target});
                shortcut.add('Ctrl+3', function(){cmdWYSIWYG(id, 'formatBlock', 'H3');}, {'target':cur_target});
                shortcut.add('Esc', function(){$('#btn_cancel').click()}, {'target':cur_target});

                // shortcut.add('Ctrl+V', function(){ WYSIWYGPaste(id); e.preventDefault(); }, {'target':cur_target});


            }, 500);


           //}

        // parse nuts tags
		getIFrameDocument('iframe_'+id).designMode = 'on';
		getIFrameDocument('iframe_'+id).body.innerHTML = $('textarea#'+id).val();
        getIFrameDocument('iframe_'+id).body.spellcheck = false;

		head = getIFrameDocument('iframe_'+id).getElementsByTagName('head')[0];
		link = document.createElement('link');
		link.setAttribute('rel',"stylesheet");
		link.setAttribute('href',"/themes/editor_css.php?t="+current_theme+"&ncache="+time());
		link.setAttribute('type',"text/css");
        head.appendChild(link);

        if(BrowserDetect.browser == 'Chrome')
        {
            // add special handler support for img, table, td

        }


        /*
        setTimeout(function(){

            css_rules = getIFrameDocument('iframe_'+id).styleSheets[0];
            css_rules = css_rules.cssRules;

            all_css = [];


            str = "";
            for(i=0; i < css_rules.length; i++) {
                style = css_rules[i].selectorText;

                // css must begin by .
                if(style.indexOf('.') != -1 && style.indexOf('.nuts_tags') == -1){

                    styles = explode(',', style);
                    for(j=0; j < styles.length ;j++){

                        classes = explode(' ', styles[j]);

                        only_class = true;
                        for(h=0; h < classes.length; h++){
                            cur_class = trim(classes[h]);

                            if(cur_class.indexOf('.') == -1){
                                only_class = false;
                                break;
                            }
                        }

                        if(only_class){
                            all_css[all_css.length] = css_rules[i].selectorText;
                        }
                    }
                }
            }

            // all css length
            if(all_css.length > 0){
                opts = $('#'+id+'_WYSIWYG_toolbar select:eq(0)').html();
                for(i=0; i < all_css.length; i++)
                    str += '<option value="'+all_css[i]+'">'+all_css[i]+'</option>';

                str = '<option class="title">Styles</option>'+str;
                $('#'+id+'_WYSIWYG_toolbar select:eq(0)').html(opts+str);
            }


        }, 600);
        */




        if($('#iframe_'+id).attr('code_source') == 'true'){
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

    WYSIWYGTextareaReload(objID); // force before open

	t = time();

	uri = '../library/js/tiny_mce.php?theme='+current_theme+'&objID='+objID+'&lang='+nutsCurrentPageLang+'&t='+t;
	windowWidth = 1024;
	windowHeight = 768;
	opts = '';

    setTimeout(function(){
        popupModal(uri, 'RichEditor', windowWidth, windowHeight, opts);
    }, 1000);


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

    // hack pdf or http
    if(action == 'createLink')
    {
        tmp = strtolower(data);
        if(tmp.indexOf('http') != -1 || tmp.indexOf('.pdf') != -1)
        {
            data += '[DATA:REPLACEX]';
        }
    }


    getIFrameDocument('iframe_'+id).execCommand('styleWithCSS', null, false);// do not use css
    getIFrameDocument('iframe_'+id).execCommand(action, false, data);

    // update content
    content = getIFrameDocument('iframe_'+id).body.innerHTML;
    content = str_replace('[DATA:REPLACEX]', '" target="_blank', content);
    getIFrameDocument('iframe_'+id).body.innerHTML = content;

    getIFrameWindow('iframe_'+id).focus();
}



function WYSIWYGFormat(id){

    v = $('#'+id+'_WYSIWYG_toolbar select:eq(0)').val();
    $('#'+id+'_WYSIWYG_toolbar select:eq(0)').val('');

    if(v == '')return;

    if(v == 'H1')cmdWYSIWYG(id, 'formatBlock', 'H1');
    else if(v == 'H2')cmdWYSIWYG(id, 'formatBlock', 'H2');
    else if(v == 'H3')cmdWYSIWYG(id, 'formatBlock', 'H3');
    else if(v == 'P')cmdWYSIWYG(id, 'formatBlock', 'P');
    else if(v == 'P-LEFT')cmdWYSIWYG(id, 'justifyLeft');
    else if(v == 'P-CENTER')cmdWYSIWYG(id, 'justifyCenter');
    else if(v == 'P-RIGHT')cmdWYSIWYG(id, 'justifyRight');
    else if(v == 'P-FULL')cmdWYSIWYG(id, 'justifyFull');
    else if(v == 'BLOCKQUOTE')cmdWYSIWYG(id, 'formatBlock', 'BLOCKQUOTE');
    else{

        // add span to selected text
        WYSIWYGGetSelectionText(id);

    }

}

function WYSIWYGGetSelectionText(id){

    userSelection = '';
    if (getIFrameWindow('iframe_'+id).getSelection)
        userSelection = getIFrameWindow('iframe_'+id).getSelection();
    else if (getIFrameDocument('iframe_'+id).selection)
        userSelection = getIFrameDocument('iframe_'+id).selection.createRange();


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


function WYSIWYGPaste(id){

    popupModal('/nuts/rte.php?view=paste&id='+id, '', 500, 450);


}






// deprecated ********************************************************
function killWYSIWYG(){
}

function toggleEditor(id, checktest){
}


