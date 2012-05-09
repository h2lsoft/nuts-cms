var debug = false; // only with FF
var im_refresh = true; // refresh im after system_goto

var last_system_uri;
var last_system_target;
var last_hash;

var list_full_length = false; // extends list table length
var RTE_parent_object = ''; // parent object of RTE

var system_goto_refresh_async = true; // async true by default, false for system refresh


function system_goto(uri, target)
{
	$('body').css('cursor', 'wait');

	// ajax loader
	if(empty(target))target = 'list';
	pos = $('div#'+target).position();

	margin_top = $('div#'+target).css('margin-top');
	if(margin_top > 0)pos.top += parseInt(margin_top);

	margin_left = $('div#'+target).css('margin-left');
	if(margin_left > 0)pos.left += parseInt(margin_left);

	b_top = $('div#'+target).css('border-top-width');
	//pos.top += parseInt(b_top);
	b_left = $('div#'+target).css('border-left-width');
	//pos.left += parseInt(b_left);

	//$('#ajax_loader').css({top:pos.top+'px', left:pos.left+'px'});

	if(pos){
		$('#ajax_loader').css('top', pos.top+'px');
		$('#ajax_loader').css('left', pos.left+'px');
	}

	$('#'+target).fadeTo(0, 0.6);
	$('#ajax_loader').show();


	last_system_uri = uri;
	last_system_target = target;

	t = Math.random();
	if(target == "content")
	{
		uri2 = explode('&', uri);
		tmp = '';
		for(i=0; i < count(uri2) ;i++)
		{
			tmp_arr = explode('=', uri2[i]);
			if(tmp_arr[0] != '_action')
			{
				if(!empty(tmp)) tmp += '&';
				tmp += uri2[i];
			}
		}
		uri2 = str_replace('index.php?', '', tmp);

		// added
		//qs = window.location.search.substring(1);
		//if(qs != uri2)
		//{
			document.location.hash = uri2;
			last_hash = '#'+uri2;
		//}
		//else
		//{
			//last_hash = '';
		//}
	}

	uri += '&ajax=1&target='+target+'&t='+t;

	// log(uri, target);
	// $.get(uri, {}, function (d) {
    $.ajax({
              url: uri,
              cache:false,
              type: 'GET',
              async: system_goto_refresh_async,
              success:  function (d){

                  d = trim(d);
                  $('div#'+target).html(d);
                  $("#ajax_loader").hide();
                  $('div#'+target).fadeTo(0.6, 1);
                  $('body').css('cursor', 'default');
                  setPluginTitle();

                  if(!im_refresh)
                      im_refresh = true;
                  else
                      privateBoxRefresh();

              }
    });

    if(system_goto_refresh_async)system_goto_refresh_async = true;

}

function system_refresh()
{
	if(typeof(last_system_uri) == 'undefined' || last_system_uri == '')
	{
		last_system_uri = document.location;
		//log('new uri => '+last_system_uri);
	}
	if(typeof(last_system_target) == 'undefined' || last_system_target == '')last_system_target = 'list';

	// alert("system_refresh => "+last_system_uri+", "+last_system_target);
    system_goto_refresh_async = true;
	system_goto(last_system_uri, last_system_target);
}

function system_position(uri, list)
{
	t = Math.random();

	list = join(';', list);


	// ajax loader
	$('body').css('cursor', 'wait');
	pos = $('div#list').position();
	margin_top = $('div#list').css('margin-top');
	if(margin_top > 0)pos.top += parseInt(margin_top);
	margin_left = $('div#list').css('margin-left');
	if(margin_left > 0)pos.left += parseInt(margin_left);

	b_top = $('div#list').css('border-top-width');
	b_left = $('div#list').css('border-left-width');

	if(pos)
	{
		$('#ajax_loader').css('top', pos.top+'px');
		$('#ajax_loader').css('left', pos.left+'px');
	}


	$('#list').fadeTo(0, 0.6);
	$('#ajax_loader').show();

	$.post(uri, {list:list}, function (d) {
		// im_refresh = false;
		// system_refresh();

		$("#ajax_loader").hide();
		$('div#list').fadeTo(0.6, 1);

		listTrColor();

	});


}

function getLastUri()
{
	return 	last_system_uri;
}

function ajaxHistoricCheckChanges()
{
	if((document.location.hash != '' || (document.location.hash == '' && last_hash != undefined)) && document.location.hash != last_hash)
	{
		// same page?
		nuri = str_replace('#', '', document.location.hash);
		qs = window.location.search.substring(1);

		if(qs != '' && last_hash == undefined)
		{
			//qs = 'index.php?'+qs;
			if(jQuery.trim(nuri) == jQuery.trim(qs))
			{
				return;
			}
		}
		if(nuri == '')
		{
			// bug prevent home return FF
			if(document.location.hash == '' && last_hash != "" && last_hash != document.location.hash)
				nuri = "mod=_home";
			else
				return;
		}
		system_goto('index.php?'+nuri, 'content');
		//document.location.href = nuri;
	}

	setTimeout("ajaxHistoricCheckChanges()", 500);

}

var last_formIt_uri;
function formIt(title, url)
{
	// check if form window is not opened
	$('body').css('cursor', 'wait');


	last_formIt_uri = url;

	t = Math.random();
	$("#form_window").load(url+'&ajax=1&target=form&t='+t, null, function() {

		fWindowTarget = '';
		if(strpos(url, '&do=delete'))
			fWindowTarget = 'delete';

		// for delete ?
		if(fWindowTarget == 'delete')
		{
			h = 350;
			topPos = 'center';
		}
		else
		{
			topPos = 10;
			h = $(window).height();
			h -= 25;
		}

		$("#form_window").dialog({
			width:900,
			minWidth: 900,
			maxHeight: 900,
			height:h,
			minHeight: h,
			maxHeight: h,
			modal: true,
			title: title,
			resizable: false,
			position: ['center', topPos],
			overlay: {
				opacity: 0.6,
				background: "black"
			},

            open: function(event, ui) {
                $('body').css('overflow-y', 'hidden');
                w = $('.ui-dialog-overlay').width();
                $('.ui-dialog-overlay').width(w+16);
            },

            close: function(event, ui) {
                $('body').css('overflow-y', 'scroll');
            }


		});



		forceWYSIWYGUpdate();
		$(this).show();
		$('body').css('cursor', 'default');

	});
}

var codemirror_editor = '';
function initCodeEditor(objID, syntax, popup_version)
{

	/*
	str = '';

	if(popup_version == true)
		str += '<div style="padding:5px; margin:0;">';
	else
		str += '<div id="code_editor_toolbar" style="padding:5px; margin:0; background-color:#e5e5e5; border:1px solid #ccc;">';

	str += '<img src="img/icon-html_code_editor.png" align="absmiddle" /> ';

	// popup_version for former
	if(popup_version == true)
		str += '<a href="javascript:;" onclick="codeEditor(\''+objID+'\', \''+syntax+'\', 0);"> Code Editor popup';
	else
		str += '<a id="code_editor_a" href="javascript:;" onclick="codeEditorInline(\''+objID+'\', \''+syntax+'\');"> Code Editor';

	str += '</a>';
	str += '</div>';

	if(popup_version == false)
		str += '<div id="code_editor_loader"><img src="img/ajax-loader.gif" align="absmiddle" /> loading ...</div>';

	if(popup_version == true)
	{
		str += '<label>&nbsp;</label>';
	}
	else
	{
		$('#'+objID).fadeTo(0, 0.4);
	}

	$('#'+objID).before(''+str+'');


	$('#form_content #'+objID).tabby();



	// direct preview
	setTimeout("$('#code_editor_a').click(); $('#code_editor_loader').remove(); $('#code_editor_toolbar').remove();", 3000);

	return true;
	*/


	str = '';
	if(popup_version == true)
	{
		str += '<div style="padding:5px; margin:0;">';
		str += '<img src="img/icon-html_code_editor.png" align="absmiddle" /> ';
		str += '<a href="javascript:;" onclick="codeEditor(\''+objID+'\', \''+syntax+'\', 0);"> Code Editor popup';
		str += '</a>';
		str += '</div>';
		str += '<label>&nbsp;</label>';

		$('#'+objID).before(''+str+'');
		$('#form_content #'+objID).tabby();
	}
	else
	{
		str += '<div id="code_editor_toolbar" style="padding:5px; margin:0; background-color:#e5e5e5; border:1px solid #ccc;">';
		str += '<img src="img/icon-html_code_editor.png" align="absmiddle" /> ';
		str += '<a id="code_editor_a" href="javascript:;" onclick="codeEditorInline(\''+objID+'\', \''+syntax+'\');"> Code Editor';
		str += '</a>';
		str += '</div>';
		str += '<div id="code_editor_loader"><img src="img/ajax-loader.gif" align="absmiddle" /> loading ...</div>';
		$('#'+objID).fadeTo(0, 0.4);
		$('#'+objID).before(''+str+'');
		$('#form_content #'+objID).tabby();

		setTimeout(function(){

			$('#code_editor_a').click();
			$('#code_editor_loader').remove();
			$('#code_editor_toolbar').remove();

		}, 1500);
	}


}


function codeEditorInline(objID, syntax)
{
	codemirror_editor = '';

	curMode = "application/x-httpd-php-open";
	if(syntax == 'html')curMode = "text/html";
	else if(syntax == 'sql')curMode = "text/x-mysql";
	else if(syntax == 'js')curMode = "text/javascript";
	else if(syntax == 'css')curMode = "text/css";

	codemirror_editor = CodeMirror.fromTextArea(document.getElementById(objID), {
        lineNumbers: true,
        matchBrackets: true,
        mode: curMode,
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",

		onCursorActivity: function() {
								codemirror_editor.setLineClass(hlLine, null);
								hlLine = codemirror_editor.setLineClass(codemirror_editor.getCursor().line, "activeline");
							},

      });

	 var hlLine = codemirror_editor.setLineClass(0, "activeline");


/*
	if(syntax == 'php')
	{
		codemirror_editor = CodeMirror.fromTextArea(objID, {
			parserfile: ["../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js"],
			path: "/library/js/codemirror/js/",
			height : "700px",
			stylesheet: "/library/js/codemirror/contrib/php/css/phpcolors.css"
		});
	}
	else if(syntax == 'css')
	{
		codemirror_editor = CodeMirror.fromTextArea(objID, {
			parserfile: ["parsecss"],
			path: "/library/js/codemirror/js/",
			height: "700px",
			stylesheet: "/library/js/codemirror/css/csscolors.css"
		});
	}
*/
	// hack on button submit
	$("#btn_submit").click(function(){
		$("#former #"+objID).val(codemirror_editor.getValue());
	});


}

function codeEditor(objID, syntax, tinyMCE)
{
    url = "/library/js/codemirror/code_editor.php?";
    url += 'parentID='+objID;
    url += '&syntax='+syntax;
	url += '&tmstp='+time();

    opts = '';
    popupModal(url, 'CodeEditor', 1024, 768, opts);
}


function inputDate(objID, type)
{
	if(type == 'date')
	{
		format = "%Y-%m-%d";
	    if(nutsUserLang == 'fr')
			format = "%d/%m/%Y";
	}
	else
	{
		format = "%Y-%m-%d %H:%M";
	    if(nutsUserLang == 'fr')
			format = "%d/%m/%Y %H:%M";
	}

   $('#'+objID).addClass(type);
   if(type == 'date')
   {
	   $('#'+objID).width(85);
	   $('#'+objID).attr('maxlength', 10);

       v = $('#'+objID).val();
       v = v.substr(0, 10);
       $('#'+objID).val(v);

	   Calendar.setup({
			inputField     :    objID,     // id of the input field
			singleClick    :    true,
			daFormat	   :	format,
			ifFormat	   :	format
		});
   }
   else
   {
	   Calendar.setup({
			inputField     :    objID,     // id of the input field
			singleClick    :    true,
			showsTime	   :	true,
			daFormat	   :	format,
			ifFormat	   :	format
		});

		$('#'+objID).width(130);
		$('#'+objID).attr('maxlength', 19);
	}


}

function helperInit(objID)
{
	// label
	$(objID+' label[title!=""]').append(' <img src="img/icon_help_mini.gif" align="absmiddle" />');
	// $(objID+' label[title!=""]').css('text-decoration', 'underline');
	$(objID+' label[title!=""]').css('cursor', 'help');
	$(objID+' label').tooltip({
	    track: true,
	    delay: 0,
	    showURL: false,
	    showBody: " - ",
	    opacity: 0.85
	});

	// legend
	$(objID+' legend[title!=""]').append(' <img src="img/icon_help_mini.gif" align="absmiddle" />');
	// $(objID+' legend[title!=""]').css('text-decoration', 'underline');
	$(objID+' legend[title!=""]').css('cursor', 'help');
	$(objID+' legend').tooltip({
	    track: true,
	    delay: 0,
	    showURL: false,
	    showBody: " - ",
	    opacity: 0.85
	});

}

function log()
{
	if(!debug || typeof(window['console']) == 'undefined')return;
	for(i=0; i < arguments.length; i++)
		console.log(arguments[i]);
}

var newwindow;
function popup(url, name, windowWidth, windowHeight, opts)
{
    myleft = (screen.width - windowWidth) / 2;
	mytop = (screen.height - windowHeight) / 2;

	myleft += window.screenX;
	mytop += window.screenY;

	properties = "width="+windowWidth+",height="+windowHeight+",resizable=yes,scrollbars=yes,top="+mytop+",left="+myleft;

	if(!empty(opts))
		properties += ', '+opts;

  newwindow = window.open(url, name, properties);
  if (newwindow.focus) {newwindow.focus();}


   return false;
}

var newwindow2;
function popup2(url, name, windowWidth, windowHeight, opts)
{
	if(!windowWidth)windowWidth = 1100;
	if(!windowHeight)windowHeight = 850;

    myleft = (screen.width - windowWidth) / 2;
	mytop = (screen.height - windowHeight) / 2;

	myleft += window.screenX;
	mytop += window.screenY;

	properties = "width="+windowWidth+",height="+windowHeight+",scrollbars=yes,top="+mytop+",left="+myleft;



	if(!empty(opts))
		properties += ', '+opts;
	properties = str_replace(' ', '', properties);

	newwindow2 = window.open(url, name, properties);

	newwindow2.resizeTo(windowWidth, windowHeight);


	if(newwindow2.focus) {newwindow2.focus();}

   return false;
}

function popupModal(url, name, windowWidth, windowHeight, opts)
{
    if(name == undefined)name = 'nWindow';
    if(opts == undefined)opts = '';

	if(!windowWidth)windowWidth = 1100;
	if(!windowHeight)windowHeight = 850;

    myleft = (screen.width - windowWidth) / 2;
	mytop = (screen.height - windowHeight) / 2;

	myleft += window.screenX;
	mytop += window.screenY;

	properties = "dialogWidth:"+windowWidth+"; dialogHeight:"+windowHeight+"; status:yes; resizable:yes; scroll:yes; dialogTop:"+mytop+"; dialogLeft:"+myleft;

	if(!empty(opts))
		properties += ', '+opts;
	properties = str_replace(' ', '', properties);

   // add dynamic popup parameter
   if(url.indexOf('http') == -1 && url.indexOf('ftp') == -1  &&  url.indexOf('mailto') == -1 &&  url.indexOf('&popup=1') == -1 && url.indexOf('?') >= 0)
        url += '&popup=1';

   newwindow = window.showModalDialog(url, name, properties);
}


function initMainMenu()
{
	$('#menu ul li').click(function(){
		$('#menu ul li ul').show('normal');
	});

	$('#menu ul li ul li').click(function(){
		$('#menu ul li ul').hide('normal');
	});
}

var dtimer_num = 0;

var dtimer_days_en = new Array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
var dtimer_days_fr = new Array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

var dtimer_months_en = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
var dtimer_months_fr = new Array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
function initTime()
{
	d = new Date();

	// day
	if(nutsUserLang == 'fr')
		dtimer_day = dtimer_days_fr[d.getDay()];
	else
		dtimer_day = dtimer_days_en[d.getDay()];


	str = dtimer_day+" "+d.getDate();

	// month
	if(nutsUserLang == 'fr')
		dtimer_month = dtimer_months_fr[d.getMonth()];
	else
		dtimer_month = dtimer_months_en[d.getMonth()];

	str += " "+dtimer_month;

	// year
	dtimer_year = d.getFullYear();
	str += " "+dtimer_year+", ";

	// hours
	dtimer_hour = d.getHours();
	if(dtimer_hour < 10)dtimer_hour = '0'+dtimer_hour;
	str += " "+dtimer_hour+":";

	// minutes
	dtimer_minutes = d.getMinutes();
	if(dtimer_minutes < 10)dtimer_minutes = '0'+dtimer_minutes;
	str += dtimer_minutes;


	if(dtimer_num == 0)
	{
		dtimer_num = 1;
	}
	else if(dtimer_num == 1)
	{
		dtimer_num = 0;
		str = str_replace(':', ' ', str);
	}

	$('span.time').html(str);
	setTimeout("initTime()", 1000);
}

function copyToClipboard(text)
{
	if(window.clipboardData)
    {
		window.clipboardData.setData('text',text);
    }
    else
    {
		/*
		var clipboarddiv=document.getElementById('divclipboardswf');
    	if(clipboarddiv==null)
		{
			clipboarddiv=document.createElement('div');
            clipboarddiv.setAttribute("name", "divclipboardswf");
        	clipboarddiv.setAttribute("id", "divclipboardswf");
        	document.body.appendChild(clipboarddiv);
     	}

		clipboarddiv.innerHTML='<embed src="clipboard.swf" FlashVars="clipboard='+
		encodeURIComponent(text)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		*/

     }
     return false;

}

function rememberMe(val1, val2)
{
	// destroy
	if(val1 == -1)
	{
		$.cookie('NutsRemember', 'null', {path: '/', expires: 15});
		return true;
	}

	v = val1+'|||'+val2;
	v = base64_encode(v);
	v = strrev(v);

	url = document.location+'';
	url = url.split(/\/+/g)[1];
	$.cookie('NutsRemember', v, {path: '/', expires: 15});

}

function saveCookie(name, val)
{
	//url = document.location+'';
	//url = url.split(/\/+/g)[1];
	$.cookie(name, val, {path: '/', expires: 365});
}


/*
	parseUri 1.2.1
	(c) 2007 Steven Levithan <stevenlevithan.com>
	MIT License
*/

function parseUri (str) {
	var	o   = parseUri.options,
		m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
		uri = {},
		i   = 14;

	while (i--) uri[o.key[i]] = m[i] || "";

	uri[o.q.name] = {};
	uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
		if ($1) uri[o.q.name][$1] = $2;
	});

	return uri;
}

parseUri.options = {
	strictMode: false,
	key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
	q:   {
		name:   "queryKey",
		parser: /(?:^|&)([^&=]*)=?([^&]*)/g
	},
	parser: {
		strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
		loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
	}
};


function getQueryParam(key)
{
	uri = document.location.href;
	if(last_system_uri != undefined && last_system_uri != '')
		uri = last_system_uri;

	m = parseUri(uri);

	param = m.queryKey[key];
	if(param == undefined)
		param = '';

	return param;

}


function mediaBrowser(f, folder)
{
	uri = WEBSITE_URL+'/library/js/tiny_mce/plugins/pdw_file_browser/index.php?editor=standalone&filter=media&returnID='+f;
	if(folder != '')
		uri += "&path="+folder;

	popupModal(uri, 'mediaBrowser', 1024, 800, '');
}


function allBrowser(f, folder)
{
	uri = WEBSITE_URL+'/library/js/tiny_mce/plugins/pdw_file_browser/index.php?editor=standalone&filter=file&returnID='+f;
	if(folder != '')
		uri += "&path="+folder;

	popupModal(uri, 'allBrowser', 1024, 800, '');
}

function imgBrowser(f, folder)
{
	uri = WEBSITE_URL+'/library/js/tiny_mce/plugins/pdw_file_browser/index.php?editor=standalone&filter=image&returnID='+f;
	if(folder != '')
		uri += "&path="+folder;

	popupModal(uri, 'imgBrowser', 1024, 800, '');
}

function openFile(f)
{
	uri = $("#"+f+"").val();
	if(uri == '')
	{
		alert(nuts_lang_msg_61);
		$("#"+f+"").focus();
		return;
	}


	// image interception for imagebox
	tab = explode('.', uri);
	ext = tab[tab.length-1];
	ext = strtolower(ext);
	if(in_array(ext, ['jpg', 'jpeg', 'gif', 'png']))
	{
		imageBox(uri);
		return;
	}



	popupModal(uri, 'openFile', 960, 750, '');
	/*windowWidth = 960;
	windowHeight = 750;
	myleft = (screen.width - windowWidth) / 2;
	mytop = (screen.height - windowHeight) / 2;

	properties = "width="+windowWidth+",height="+windowHeight+",resizable=yes,scrollbars=yes,top="+mytop+",left="+myleft;

	window.open(uri, 'openFile', properties);*/

}

var print_html_inside = "";
function printDialog()
{
	title = $('.ui-dialog-title').html();
	title = urlencode(strip_tags(title));

	popupModal("printable_dialog.php?t="+title, "Print", 910, 768, '');

}

function translator(objID1, objID2, lang1, lang2)
{
	msg = $('#'+objID1).val();
	if(msg == '')return;

	$('#'+objID2).val("Loading translation...");

	/*
		google.language.translate(msg, "", lang2, function(result) {
			if(result.error)
				$('#'+objID2).val(msg);
			else
				$('#'+objID2).val(result.translation);
		});
	*/

	uri = "/nuts/translator.php";
	$.post(uri, {lngIn:lang1, lngOut:lang2, txt:msg}, function(data){

		if(data.indexOf('Error') != -1)
			alert(data);
		else
			$('#'+objID2).val(data);
	});



}

//  special tags *******************************************************************

/**
 * convert attribute to correct image
 */
function parse_nuts_tags(text)
{
	text = str_replace('    ', "\t", text);
    text2 = text;

	next_position = 0;
	do
	{
		cur_tags = extract_str("{@NUTS\tTYPE='", "'}", text, next_position, true);

		// prevent bug
		cur_tags = trim(cur_tags);
		if(cur_tags.lastIndexOf('}') != strlen(cur_tags)-1)
			cur_tags = substr(0, cur_tags.lastIndexOf('}')+1, cur_tags);

		next_position = text.indexOf(cur_tags, next_position);
		next_position += strlen(cur_tags)-1;

		if(!empty(cur_tags))
		{
			tag_type = extract_str("{@NUTS\tTYPE='", "'\t", cur_tags, 0, false);
			tag_type = trim(tag_type);

			//console.log('tag_type => `'+tag_type+'` tags => `'+cur_tags+'`');
			if(in_array(tag_type, array('PLUGIN', 'MEDIA', 'REGION', 'BLOCK', 'MENU', 'GALLERY', 'ZONE', 'FORM', 'SURVEY')))
			{
				tag_type = strtolower(tag_type);
			}
			else
			{
				if(tag_type == 'PAGE')
					tag_type = '_exit_tag';
				else
					tag_type = 'unknown';
			}

			// page exception exclude
			if(tag_type != '_exit_tag')
			{
				label = "";
				if(tag_type == 'survey')
				{
					label = extract_str("TITLE='", "'", cur_tags, 0, 0);
					if(label != '')
					{
						if(label.charAt(strlen(label)-1) == '}')
							label = substr(label, 0, strlen(label)-1);
						label = trim(label);
						label = base64_encode(label);
					}
				}
                else if(tag_type == 'plugin')
                {
                    label = extract_str("NAME='", "'", cur_tags, 0, 0);
                    label2 = extract_str("PARAMETERS='", "'", cur_tags, 0, 0);
                    if(label != '')
                    {
                        if(label.charAt(strlen(label)-1) == '}')
                            label = substr(label, 0, strlen(label)-1);
                        label = trim(label);

                        label2 = trim(label2);
                        if(!empty(label2))
                        {
                            if(label2.charAt(strlen(label2)-1) == '}')
                                label2 = substr(label2, 0, strlen(label2)-1);
                            label2 = trim(label2);

                            if(!empty(label2))
                                label += " - PARAMETERS("+label2+")";
                        }




                        label = trim(label);
                        label = base64_encode(label);
                    }
                }
				else
				{
					label = extract_str("NAME='", "'", cur_tags, 0, 0);
					label2 = extract_str("FROM='", "'", cur_tags, 0, 0);
					if(label != '' || label2 != '')
					{
						if(empty(label) && !empty(label2))
							label = label2;

						if(label.charAt(strlen(label)-1) == '}')
							label = substr(label, 0, strlen(label)-1);
						label = trim(label);
						label = base64_encode(label);
					}
				}

				rep = '\n\n<img class="nuts_tags" title="'+cur_tags+'" src="/nuts/img/icon_tags/tag.php?tag='+tag_type+'&amp;label='+label+'" border="0" />\n\n';
				text2 = str_replace(cur_tags, rep, text2);
			}

		}

	} while(cur_tags != '');


	text2 = str_replace('\n\n', '\n', text2);
	return text2;
}

function remove_nuts_tags(text)
{
	text = str_replace('    ', "\t", text);
	text2 = text;

	next_position = 0;
	do
	{
		cur_tags = extract_str('<img class="nuts_tags"', '>', text2, 0, true);
		//next_position = text.indexOf(cur_tags, next_position);
		//next_position += strlen(cur_tags)-1;

		if(!empty(cur_tags))
		{
			// prevent bug
			cur_tags = trim(cur_tags);
			if(cur_tags.lastIndexOf('>') != strlen(cur_tags)-1)
			{
				cur_tags = cur_tags.substring(0, cur_tags.lastIndexOf('>')+1);
			}

			tag_type = extract_str('title="', '"', cur_tags, 0, false);
			tag_type = trim(tag_type);

			text2 = str_replace(cur_tags, tag_type, text2);
		}
	} while(cur_tags != '');

	return text2;

}

function extract_str(start, end, text, pos1_start, return_full)
{
	astr = '';

	pos_start = text.indexOf(start, pos1_start);

	pos_end_f = pos_start+strlen(start);
	pos_end = text.indexOf(end, pos_end_f);

	if(pos_start != -1 && pos_end != -1)
	{
		//console.log(pos_start, pos_end, strlen(text));
		astr = text.substring(pos_start, pos_end+2);
		if(return_full == false)
		{
			astr = str_replace(start, '', astr);
			astr = str_replace(end, '', astr);
		}

	}


	return astr;
}

function iframeResizeByContents(iframe, height_added)
{
  //find the height of the internal page
  var the_height = document.getElementById(iframe).contentWindow.document.body.scrollHeight;

  //change the height of the iframe
  document.getElementById(iframe).height = the_height + height_added;

  wx = window.scrollX;
  window.scrollTo(wx, 0);


}

function privateBoxRefresh(){

	uri = 'index.php?mod=_internal-messaging&do=list&action=nb_read';
	$.get(uri, {}, function (d) {

			d = parseInt(d);

			if(d == 0)
			{
				msg = nuts_lang_msg_65;
			}
			else
			{
				msg = nuts_lang_msg_66;
				msg = str_replace('[MAIL_NB]', d, msg);
				if(d > 1)
					msg += 's';
				msg = '<span>'+msg+'</span>';
			}

			$('span.internal_message a').html(msg);
			getUserOnline();

	}, 'text');


}

function getUserOnline(){

	uri = 'index.php?_action=users_online';
	$.get(uri, {}, function (d) {

		// refresh data
		$('#user_online_count').text(d.length);

		str = '';
		for(i=0; i < d.length; i++){
			str += '<img src="'+d[i].avatar_url+'" /> '+d[i].Name+'<br /><span>'+d[i].Application+'</span><div style="clear:left;"></div>';
		}

		$('#user_online_list').html(str);


	}, 'json');

}

function viewOnlineUsers(blur){

	if(blur == true)
	{
		$('#user_online_list').hide();
		return;
	}


	if(!$('#user_online_list').is(':visible'))
	{
		if($('#user_online_count').text() == 0)
		{
			$('#user_online_list').hide();
			return;
		}

		pos = $('#user_online_count').position();
		$('#user_online_list').css('top', '80px');
		$('#user_online_list').css('left', pos.left-22);
		$('#user_online_list').slideDown();
	}
	else
	{
		$('#user_online_list').hide();
	}


}

function selectSetOptionClass(objName)
{
	className =  $('#'+objName+' option:selected').attr('class');
	$('#'+objName).removeClass().addClass(className);

}

function selectSetOptionStyle(objName)
{
	styleVal =  $('#'+objName+' option:selected').attr('style');
	$('#'+objName).attr('style', styleVal);
}

function setPluginTitle()
{
	document.title = APP_TITLE+" - "+$('.col_title a').text();
}

function logoutForm(title)
{
	// check if form window is not opened
	$('body').css('cursor', 'wait');

	t = Math.random();
	$("#form_window").load('/nuts/logout.php', null, function() {

		h = 350;
		topPos = 'center';

		$("#form_window").dialog({
			width:900,
			minWidth: 900,
			maxWidth: 900,
			height:h,
			minHeight: h,
			maxHeight: h,
			modal: true,
			title: title,
			resizable: false,
			position: ['center', topPos],
			overlay: {
				opacity: 0.6,
				background: "black"
			}
		});

		$(this).show();
		$('body').css('cursor', 'default');

	});
}


var notifyID = -1;
function notify(type, message)
{
	notifyID++;

	class_added = "";

	if(type == '' || type == 'normal')
	{
		message = '<img src="img/icon-help.png" align="absmiddle" /> &nbsp;'+message;
	}

	if(type == 'ok')
	{
		message = '<img src="img/icon-accept.gif" align="absmiddle" /> &nbsp;'+message;
		class_added = 'notify_itOK';
	}

	if(type == 'error')
	{
		message = '<img src="img/icon-error.gif" align="absmiddle" /> &nbsp;'+message;
		class_added = 'notify_itKO';
	}

	tpl = '<div class="notify_it '+class_added+'" id="notify_it_'+notifyID+'">';
	tpl += message;
	tpl += '</div>';

	$("body").append(tpl);


	n_scrollit = $(window).scrollTop();
	n_scrollit += 15;
	$('#notify_it_'+notifyID).css("top", n_scrollit);


	$('#notify_it_'+notifyID).fadeIn('normal', function(){

		setTimeout(function(){
			$('#notify_it_'+notifyID).fadeOut('normal');
		}, 3000);

	});

}

function imageBox(src)
{
	img = '<img id="imagebox_previewed" src="'+src+'" />';

	$('#nuts_canvas_content').html(img);
	$('#nuts_canvas').height($(document).height()+60);

	$('#nuts_canvas').fadeIn('normal', function(){

		$('#nuts_canvas_content').show();

		margin_top = $('body').height() - $('#imagebox_previewed').height();
		if(margin_top <= 0)
			margin_top = 80;
		else
			margin_top = margin_top / 2;

		$('#imagebox_previewed').css('margin-top', margin_top);

	});

}

function imageBoxClose()
{
	$('#nuts_canvas_content').html("").hide();
	$('#nuts_canvas').fadeOut('normal');
}


function pluginAddNotificationCounter(name, counter)
{
    if(counter > 0)
    {
        pos = $('.mod[name="'+name+'"]').position();
        plugin_top = pos.top;
        plugin_left = pos.left;

        tmp = '<div style="top:'+plugin_top+'px; left:'+plugin_left+'px" class="plugin_notification_counter">'+counter+'</div>';
        $('#home').append(tmp);

        $('.mod[name="'+name+'"]').css('background-color', '#ffcccc');
    }
}


function listSearchUserView(plugin_name){

    if(!$('#list_container .list_searches_menu').is(':visible'))
    {
        pos = $('#list_container .list_searches_menu_parent').position();
        $('#list_container .list_searches_menu').css("left", pos.left+'px');
        $('#list_container .list_searches_menu').html('<div style="padding: 3px; color:black; font-weight: normal;"><img align="absbottom" src="img/ajax-loader.gif"> loading..</div>');
        $('#list_container .list_searches_menu').show();

        uri = 'index.php?_action=list_search_users&_action2=list&plugin='+plugin_name;
        $.get(uri, {}, function(data){

            if(data['error'])
            {
                alert('Error: '+data['error_msg']);
                return;
            }

            // no record
            if(!count(data['list']))
            {
                msg = 'No searches found';
                if(nutsUserLang == 'fr')
                    msg = 'Aucune recherche trouvée';
                $('#list_container .list_searches_menu').html('<div style="padding: 3px; color:black; font-weight: normal;">'+msg+'</div>');
            }
            else
            {
                str = '<table cellspacing="0" cellpadding="3">';

                for(i=0; i < count(data['list']); i++)
                {
                    str += '<tr>';
                    str += '    <td class="label" nowrap><a href="javascript:listSearchSelect(\''+plugin_name+'\', '+data['list'][i]['ID']+')">'+data['list'][i]['Name']+'</a></td>';
                    str += '    <td align="center" width="16"><img onclick="listSearchDelete(\''+plugin_name+'\', '+data['list'][i]['ID']+')" src="/nuts/img/icon-delete.png"></td>';
                    str += '</tr>';
                }

                str += '</table>';

                $('#list_container .list_searches_menu').html(str);

            }


        }, 'json');
    }
    else
    {
        $('#list_container .list_searches_menu').hide();
    }
}

function listSearchSave(plugin_name){

    // no checkbox checked
    if(!$('#search_form input[type=checkbox]:checked').length)
    {
        msg = 'Please, fill one filter at least';
        if(nutsUserLang == 'fr')
            msg = 'Merci de renseigner au moins un critère de recherche';
        alert(msg);
        return;
    }


    msg = 'Please, enter the name of your search';
    if(nutsUserLang == 'fr')
        msg = 'Entrer le nom de votre recherche';

    name = prompt(msg);
    name = trim(name);
    if(name != '')
    {
        uri = 'index.php?_action=list_search_users&_action2=add&plugin='+plugin_name+'&name='+name;

        serialized = '';
        $('#search_form input[type=checkbox]:checked').each(function(){

            id = str_replace('_checkbox', '', $(this).attr('id'));

            if(!empty(serialized))serialized += '\n';
            serialized += id+';'+$('#search_form #'+id+'_operator').val()+';';

            // select or input
            if($('#search_form #se_'+id).is(':visible'))
                serialized += $('#search_form #se_'+id).val();
            else
                serialized += $('#search_form #'+id).val();

        });

        $.post(uri, {serialized:serialized}, function(data){

            if(data['error'])
            {
                alert('Error: '+data['error_msg']);
                return;
            }


        }, 'json');

    }


}


function listSearchSelect(plugin_name, ID)
{

}





function listSearchReset(uri) {

    document.forms['search_form'].reset();

    $('#search_form input[type=checkbox]').each(function(){
        id = str_replace('_checkbox', '', $(this).attr('id'));
        listSearchCheckbox(id);
    });

    system_goto(uri, 'list');
}


function listSearchCheckbox(objName, focus)
{
    if($('#list_search_content #'+objName+'_checkbox').is(':checked'))
    {
        $('#list_search_content #'+objName+'_operator').show();
        $('#list_search_content #'+objName).show();
        $('#list_search_content #se_'+objName).show();

        $('#list_search_content #'+objName+'_label').css('font-weight', 'bold');

        if(focus)
        {
            $('#list_search_content #'+objName).focus();
            $('#list_search_content #se_'+objName).focus();
        }

    }
    else
    {
        $('#list_search_content #'+objName+'_operator').hide();
        $('#list_search_content #'+objName).hide();
        $('#list_search_content #se_'+objName).hide();

        $('#list_search_content #'+objName+'_label').css('font-weight', 'normal');

    }
}


function checkboxSelectAll(objName)
{
    sel = 'input[type=checkbox][name="'+objName+'[]"]';
    if(!$(sel+':checked').length)
        $(sel).attr('checked', true);
    else
        $(sel).attr('checked', false);
}