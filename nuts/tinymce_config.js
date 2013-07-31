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

        mode_simple = false;
        if($(this).hasClass('simple'))mode_simple = true;

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


        if(mode_simple == false)
        {
            str += sep;

            select = '<select onchange="WYSIWYGFormat(\''+id+'\');">';
            select += ' <option class="title">Format</option>';
            select += ' <option value="H1">H1</option>';
            select += ' <option value="H2">H2</option>';
            select += ' <option value="H3">H3</option>';
            select += ' <option value="P">P</option>';
            // select += ' <option value="CITE">Citation</option>';
            select += ' <option value="PRE">Pre</option>';
            select += ' <option value="BLOCKQUOTE">Blockquote</option>';
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
            str += '<hr />';
            str += '<a tabindex="0" href="javascript:cmdWYSIWYG(\''+id+'\', \'link\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_url\');">'+lbl_custom+'</a><br />';
            str += '</div>';


            str += '<img class="rte_button" onclick="javascript:cmdWYSIWYG(\''+id+'\', \'unlink\');" src="img/rte/Ax.png" align="absmiddle" /> ';
            str += sep;

            // gallery
            str += ' <img class="rte_button" src="/nuts/img/rte/browse.png" align="absmiddle" title="Images" onclick="imgBrowser(\'imgTag_'+id+'\', \'\');" />';
            str += ' <img class="rte_button" style="width:16px;" title="'+nuts_lang_msg_72+'" src="/plugins/_gallery/icon.png" align="absmiddle" onclick="popupModal(\'index.php?mod=_gallery&do=list&popup=1&parent_refresh=no&parentID='+id+'\');" />';

            // media submenu
            str += ' <img class="rte_button" src="img/icon-media.png" align="absmiddle" title="'+nuts_lang_msg_73+'" id="'+id+'_WYSIWYG_submenu_media_parent" onclick="javascript:WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');" />';

            str += '<div id="'+id+'_WYSIWYG_submenu_media" class="WYSIWYG_submenu">';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=YOUTUBE%20VIDEO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/youtube.png" style="width:16px" /> Youtube</a><br />';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=DAILYMOTION&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/dailymotion.png" style="width:16px" /> Dailymotion</a><br />';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=VIDEO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/video.png" style="width:16px" /> Video</a><br />';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=AUDIO&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/audio.png" style="width:16px" /> Audio</a><br />';
            str += '<hr />';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_gmaps&do=list&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_gmaps/icon.png" style="width:16px" /> Map</a><br />';
            str += '<hr />';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=IFRAME&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/iframe.png" style="width:16px" /> Iframe</a><br />';
            str += '<a tabindex="0" href="javascript:popupModal(\'index.php?mod=_media&do=list&user_se=1&Type=EMBED%20CODE&Type_operator=_equal_&popup=1&parent_refresh=no&parentID='+id+'\');WYSIWYGSubMenu(\''+id+'_WYSIWYG_submenu_media\');"><img align="absbottom" src="/plugins/_media/img/embed.png" style="width:16px" /> Embed code</a><br />';


            str += '</div>';

            str += ' <img class="rte_button" src="/nuts/img/widget.png" title="Widgets" align="absmiddle" onclick="widgetsWindowOpen(\''+id+'\');" /> ';
            str += ' <img class="rte_button" style="width: 16px;" src="/plugins/_rte_template/icon.png" title="Code" align="absmiddle" onclick="popupModal(\'index.php?mod=_rte_template&do=list&popup=1&parent_refresh=no&parentID='+id+'\');" />';



            // richeditor
            str += sep;
            str += '<input type="button" class="button" value="Richeditor" onclick="openWYSIWYG(\''+id+'\');" tabindex="0" />';


            // rte
            str += sep;

            // repaint
            str += ' <img  class="rte_button" title="Spellchecker" onclick="javascript:WYSIWYGspellchecker(\''+id+'\');" src="img/rte/spellcheck.png" align="absmiddle" /> ';
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
        }

		str += '</div>';
		str += '</p>';

        if($('#'+id+'_WYSIWYG_toolbar').length == 0)
        {
           $('textarea#'+id).attr('wrap', '').parent('p:visible').before(str);
        }

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

		$('#iframe_'+id).hide();
        WYSIWYGTextareaReload(id);
        $(objs).css('visibility', 'hidden');

        htmlFormatter(id);
        $('textarea#'+id).show();



	}

    $(obj_source).css('visibility', 'visible');
}

var secondPassfunction = false; // prevent twice times function
function WYSIWYGEventFocus(id, e)
{
	// secondPassfunction = false;
}


function WYSIWYGEvent(id, e, shortcut){

    // firefox capture ENTER
    if(e.type == 'keypress' && BrowserDetect.browser == 'Firefox')
    {
        // ENTER
        if(e.which == 13)
        {
           e.preventDefault();
           html = '<p>&nbsp;</p>';
           if(e.shiftKey)html = '<br />&nbsp;';

           // document.getElementById('iframe_'+id).contentWindow.document.execCommand("insertHTML", false, html);
           getIFrameDocument('iframe_'+id).execCommand("insertHTML", false, html);
           getIFrameWindow('iframe_'+id).focus();

           return false;
        }
    }


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
        obj.addEventListener('paste', function(e){id = str_replace('iframe_', '', this.name); WYSIWYGHandlePaste(this, e, id); }, false);

        // obj.addEventListener('paste', function(e){e.preventDefault();}, false);


        // add special shortcut catcher for Chrome
        // tools: http://jonathan.tang.name/files/js_keycode/test_keycode.html
        setTimeout( function() {

                cur_target = getIFrameWindow('iframe_'+id);
                if(BrowserDetect.browser == 'Chrome')
                    cur_target = getIFrameDocument('iframe_'+id);

                if(BrowserDetect.browser == 'Firefox'){
                    shortcut.add('Ctrl+B', function(){ cmdWYSIWYG(id, 'bold', '');}, {'target':cur_target});
                    shortcut.add('Ctrl+I', function(){ cmdWYSIWYG(id, 'italic', '');}, {'target':cur_target});
                    shortcut.add('Ctrl+U', function(){ cmdWYSIWYG(id, 'underline', '');}, {'target':cur_target});

                    shortcut.add('Ctrl+V', function(){WYSIWYGHandlePaste(this, e, id); }, {'target':cur_target});

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


                shortcut.add('Ctrl+Z', function(){getIFrameDocument('iframe_'+id).execCommand('undo', false, null);}, {'target':cur_target});
                shortcut.add('Ctrl+Y', function(){getIFrameDocument('iframe_'+id).execCommand('redo', false, null);}, {'target':cur_target});


                shortcut.add('Esc', function(){$('#btn_cancel').click()}, {'target':cur_target});

            }, 500);

        // parse nuts tags
		getIFrameDocument('iframe_'+id).designMode = 'on';
		getIFrameDocument('iframe_'+id).body.innerHTML = $('textarea#'+id).val();
        getIFrameDocument('iframe_'+id).body.spellcheck = false;
        getIFrameDocument('iframe_'+id).execCommand('defaultParagraphSeparator', 0,  'p');// for chrome

		head = getIFrameDocument('iframe_'+id).getElementsByTagName('head')[0];
		link = document.createElement('link');
		link.setAttribute('rel',"stylesheet");
		link.setAttribute('href',"/themes/editor_css.php?t="+current_theme+"&ncache="+time());
		link.setAttribute('type',"text/css");
        head.appendChild(link);


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


function WYSIWYGHandlePaste(obj, e, id)
{
    if(BrowserDetect.browser == 'Firefox')
    {
        WYSIWYGPaste(id);
        e.stopPropagation();
        e.preventDefault();
        return false;
    }
    else
    {
        clipboard_txt = e.clipboardData.getData('text/html');

        // detect if text is from ms word
        if(clipboard_txt.indexOf('MsoNormal') != -1 || clipboard_txt.indexOf('class="Mso') != -1 || clipboard_txt.indexOf('<!--[if !')  != -1)
        {
            WYSIWYGPaste(id);
            e.stopPropagation();
            e.preventDefault();
            return false;
        }
    }


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
    else if(v == 'CITE')cmdWYSIWYG(id, 'formatBlock', 'CITE');
    else if(v == 'PRE')cmdWYSIWYG(id, 'formatBlock', 'PRE');
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




function WYSIWYGspellchecker(id)
{
    // content = getIFrameDocument('iframe_'+id).body.innerHTML;
    // alert("Spellchecker => "+id);
    popupModal('/nuts/rte_spellchecker.php?parent='+id+'&lang='+nutsCurrentPageLang, "Spellchecker");

}


/*** tabification ***/
var TABIFY_level = 0;
var TABIFY_LOOP_SIZE=100;
var TABIFY_source_current_ID = null;


function htmlFormatter(id)
{
    TABIFY_source_current_ID = id;
    var code = document.getElementById(id).value;
    cleanHTML(code);
}

function runTabifier(id){}

function finishTabifier(code) {
    code=code.replace(/\n\s*\n/g, '\n');  //blank lines
    code=code.replace(/^[\s\n]*/, ''); //leading space
    code=code.replace(/[\s\n]*$/, ''); //trailing space

    document.getElementById(TABIFY_source_current_ID).value = code;
    TABIFY_level=0;
}

function showProgress(done, total) {}
function hideProgress() {}

function cleanHTML(code) {
    var i=0;
    function cleanAsync() {
        var iStart=i;
        for (; i<code.length && i<iStart+TABIFY_LOOP_SIZE; i++) {
            point=i;

            //if no more tags, copy and exit
            if (-1==code.substr(i).indexOf('<')) {
                out+=code.substr(i);
                finishTabifier(out);
                return;
            }

            //copy verbatim until a tag
            while (point<code.length && '<'!=code.charAt(point)) point++;
            if (i!=point) {
                cont=code.substr(i, point-i);
                if (!cont.match(/^\s+$/)) {
                    if ('\n'==out.charAt(out.length-1)) {
                        out+=tabs();
                    } else if ('\n'==cont.charAt(0)) {
                        out+='\n'+tabs();
                        cont=cont.replace(/^\s+/, '');
                    }
                    cont=cont.replace(/\s+/g, ' ');
                    out+=cont;
                } if (cont.match(/\n/)) {
                    out+='\n'+tabs();
                }
            }
            start=point;

            //find the end of the tag
            while (point<code.length && '>'!=code.charAt(point)) point++;
            tag=code.substr(start, point-start);
            i=point;

            //if this is a special tag, deal with it!
            if ('!--'==tag.substr(1,3)) {
                if (!tag.match(/--$/)) {
                    while ('-->'!=code.substr(point, 3)) point++;
                    point+=2;
                    tag=code.substr(start, point-start);
                    i=point;
                }
                if ('\n'!=out.charAt(out.length-1)) out+='\n';
                out+=tabs();
                out+=tag+'>\n';
            } else if ('!'==tag[1]) {
                out=placeTag(tag+'>', out);
            } else if ('?'==tag[1]) {
                out+=tag+'>\n';
            } else if (t=tag.match(/^<(script|style)/i)) {
                t[1]=t[1].toLowerCase();
                tag=cleanTag(tag);
                out=placeTag(tag, out);
                end=String(code.substr(i+1)).toLowerCase().indexOf('</'+t[1]);
                if (end) {
                    cont=code.substr(i+1, end);
                    i+=end;
                    out+=cont;
                }
            } else {
                tag=cleanTag(tag);
                out=placeTag(tag, out);
            }
        }

        showProgress(i, code.length);
        if (i<code.length) {
            setTimeout(cleanAsync, 0);
        } else {
            finishTabifier(out);
        }
    }

    var point=0, start=null, end=null, tag='', out='', cont='';
    cleanAsync();
}

function tabs() {
    var s='';
    for (var j=0; j<TABIFY_level; j++) s+='\t';
    return s;
}

function cleanTag(tag) {
    var tagout='';
    tag=tag.replace(/\n/g, ' ');       //remove newlines
    tag=tag.replace(/[\s]{2,}/g, ' '); //collapse whitespace
    tag=tag.replace(/^\s+|\s+$/g, ' '); //collapse whitespace
    var suffix='';
    if (tag.match(/\/$/)) {
        suffix='/';
        tag=tag.replace(/\/+$/, '');
    }
    var m, partRe = /\s*([^= ]+)(?:=((['"']).*?\3|[^ ]+))?/;
    while (m = partRe.exec(tag)) {
        if (m[2]) {
            tagout += m[1].toLowerCase() + '=' + m[2];
        } else if (m[1]) {
            tagout += m[1].toLowerCase();
        }
        tagout += ' ';

        // Why is this necessary?  I thought .exec() went from where it left off.
        tag = tag.substr(m[0].length);
    }
    return tagout.replace(/\s*$/, '')+suffix+'>';
}

/////////////// The below variables are only used in the placeTag() function
/////////////// but are declared global so that they are read only once
//opening and closing tag on it's own line but no new indentation TABIFY_level
var ownLine=['area', 'body', 'head', 'hr', 'iframe', 'link', 'meta',
    'noscript', 'style', 'table', 'tbody', 'thead', 'tfoot'];

//opening tag, contents, and closing tag get their own line
//(i.e. line before opening, after closing)
var contOwnLine=['li', 'dt', 'dt', 'h[1-6]', 'option', 'script'];

//line will go before these tags
var lineBefore=new RegExp(
    '^<(/?'+ownLine.join('|/?')+'|'+contOwnLine.join('|')+')[ >]'
);

//line will go after these tags
lineAfter=new RegExp(
    '^<(br|/?'+ownLine.join('|/?')+'|/'+contOwnLine.join('|/')+')[ >]'
);

//inside these tags (close tag expected) a new indentation TABIFY_level is created
var newTABIFY_level=['blockquote', 'div', 'dl', 'fieldset', 'form', 'frameset',
    'map', 'ol', 'p', 'pre', 'select', 'td', 'th', 'tr', 'ul'];
newTABIFY_level=new RegExp('^</?('+newTABIFY_level.join('|')+')[ >]');
function placeTag(tag, out) {
    var nl=tag.match(newTABIFY_level);
    if (tag.match(lineBefore) || nl) {
        out=out.replace(/\s*$/, '');
        out+="\n";
    }

    if (nl && '/'==tag.charAt(1)) TABIFY_level--;
    if ('\n'==out.charAt(out.length-1)) out+=tabs();
    if (nl && '/'!=tag.charAt(1)) TABIFY_level++;

    out+=tag;
    if (tag.match(lineAfter) || tag.match(newTABIFY_level)) {
        out=out.replace(/ *$/, '');
        out+="\n";
    }
    return out;
}

function cleanCSS(code) {
    var i=0, instring=false, incomment=false, c, cp;
    function cleanAsync() {
        var iStart=i;
        for (; i<code.length && i<iStart+TABIFY_LOOP_SIZE; i++) {
            c=code.charAt(i);
            cp=null;
            try {
                cp=code.charAt(i+1);
            } catch (e) { }

            if (incomment) {
                if ('*' == c && '/' == cp) {
                    incomment=false;
                    out+='*/';
                    i++;
                } else {
                    out+=c;
                }
            } else if (instring) {
                if (instring==c) {
                    instring=false;
                }
                out+=c;
            } else if ('/'==c && '*'==cp) {
                incomment=true;
                out+='/*';
                i++;
            } else if ('{'==c) {
                TABIFY_level++;
                out+=' {\n'+tabs();
            } else if ('}'==c) {
                out=out.replace(/\s*$/, '');
                TABIFY_level--;
                out+='\n'+tabs()+'}\n'+tabs();
            } else if ('"'==c || "'"==c) {
                if (instring && c==instring) {
                    instring=false;
                } else {
                    instring=c;
                }
                out+=c;
            } else if (';'==c) {
                out+=';\n'+tabs();
            } else if ('\n'==c) {
                out+='\n'+tabs();
            } else {
                out+=c;
            }
        }

        showProgress(i, code.length);
        if (i<code.length) {
            setTimeout(cleanAsync, 0);
        } else {
            TABIFY_level=li;
            out=out.replace(/[\s\n]*$/, '');
            finishTabifier(out);
        }
    }

    if ('\n'==code[0]) code=code.substr(1);
    code=code.replace(/([^\/])?\n*/g, '$1');
    code=code.replace(/\n\s+/g, '\n');
    code=code.replace(/[   ]+/g, ' ');
    code=code.replace(/\s?([;:{},+>])\s?/g, '$1');
    code=code.replace(/\{(.*):(.*)\}/g, '{$1: $2}');

    var out=tabs(), li=TABIFY_level;
    cleanAsync();
}

function cleanCStyle(code) {
    var i=0;
    function cleanAsync() {
        var iStart=i;
        for (; i<code.length && i<iStart+TABIFY_LOOP_SIZE; i++) {
            c=code.charAt(i);

            if (incomment) {
                if ('//'==incomment && '\n'==c) {
                    incomment=false;
                } else if ('/*'==incomment && '*/'==code.substr(i, 2)) {
                    incomment=false;
                    c='*/\n';
                    i++;
                }
                if (!incomment) {
                    while (code.charAt(++i).match(/\s/)) ;; i--;
                    c+=tabs();
                }
                out+=c;
            } else if (instring) {
                if (instring==c && // this string closes at the next matching quote
                    // unless it was escaped, or the escape is escaped
                    ('\\'!=code.charAt(i-1) || '\\'==code.charAt(i-2))
                    ) {
                    instring=false;
                }
                out+=c;
            } else if (infor && '('==c) {
                infor++;
                out+=c;
            } else if (infor && ')'==c) {
                infor--;
                out+=c;
            } else if ('else'==code.substr(i, 4)) {
                out=out.replace(/\s*$/, '')+' e';
            } else if (code.substr(i).match(/^for\s*\(/)) {
                infor=1;
                out+='for (';
                while ('('!=code.charAt(++i)) ;;
            } else if ('//'==code.substr(i, 2)) {
                incomment='//';
                out+='//';
                i++;
            } else if ('/*'==code.substr(i, 2)) {
                incomment='/*';
                out+='\n'+tabs()+'/*';
                i++;
            } else if ('"'==c || "'"==c) {
                if (instring && c==instring) {
                    instring=false;
                } else {
                    instring=c;
                }
                out+=c;
            } else if ('{'==c) {
                TABIFY_level++;
                out=out.replace(/\s*$/, '')+' {\n'+tabs();
                while (code.charAt(++i).match(/\s/)) ;; i--;
            } else if ('}'==c) {
                out=out.replace(/\s*$/, '');
                TABIFY_level--;
                out+='\n'+tabs()+'}\n'+tabs();
                while (code.charAt(++i).match(/\s/)) ;; i--;
            } else if (';'==c && !infor) {
                out+=';\n'+tabs();
                while (code.charAt(++i).match(/\s/)) ;; i--;
            } else if ('\n'==c) {
                out+='\n'+tabs();
            } else {
                out+=c;
            }
        }

        showProgress(i, code.length);
        if (i<code.length) {
            setTimeout(cleanAsync, 0);
        } else {
            TABIFY_level=li;
            out=out.replace(/[\s\n]*$/, '');
            finishTabifier(out);
        }
    }

    code=code.replace(/^[\s\n]*/, ''); //leading space
    code=code.replace(/[\s\n]*$/, ''); //trailing space
    code=code.replace(/[\n\r]+/g, '\n'); //collapse newlines

    var out=tabs(), li=TABIFY_level, c='';
    var infor=false, forcount=0, instring=false, incomment=false;
    cleanAsync();
}

function cleanJson(code) {
    var i=0;
    function cleanAsync() {
        var iStart=i;
        for (; i<code.length && i<iStart+TABIFY_LOOP_SIZE; i++) {
            c=code.charAt(i);

            if (instring) {
                if (instring==c && // this string closes at the next matching quote
                    // unless it was escaped, or the escape is escaped
                    ('\\'!=code.charAt(i-1) || '\\'==code.charAt(i-2))
                    ) {
                    instring=false;
                }
                out+=c;
            } else if ('"'==c || "'"==c) {
                if (instring && c==instring) {
                    instring=false;
                } else {
                    instring=c;
                }
                out+=c;
            } else if ('{'==c || '['==c) {
                TABIFY_level++;
                out+=c+'\n'+tabs();
                while (code.charAt(++i).match(/\s/)) ;; i--;
            } else if ('}'==c || ']'==c) {
                out=out.replace(/\s*$/, '');
                TABIFY_level--;
                if (!out.match(/({|\[)$/)) out+='\n'+tabs();
                out+=c;
                while (code.charAt(++i).match(/\s/)) ;; i--;
            } else if (','==c) {
                out+=',\n'+tabs();
                while (code.charAt(++i).match(/\s/)) ;; i--;
            } else {
                out+=c;
            }
        }

        showProgress(i, code.length);
        if (i<code.length) {
            setTimeout(cleanAsync, 0);
        } else {
            TABIFY_level=li;
            out=out.replace(/[\s\n]*$/, '');
            finishTabifier(out);
        }
    }

    code=code.replace(/^[\s\n]*/, ''); //leading space
    code=code.replace(/[\s\n]*$/, ''); //trailing space
    code=code.replace(/[\n\r]+/g, '\n'); //collapse newlines

    var out=tabs(), li=TABIFY_level, c='';
    var infor=false, forcount=0, instring=false, incomment=false;
    cleanAsync();
}
/*** /tabification ***/


















// deprecated ********************************************************
function killWYSIWYG(){}
function toggleEditor(id, checktest){}





