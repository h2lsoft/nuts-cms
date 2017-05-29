// init from cookie
rCookie = $.cookie('PageManagerResize');
rCookie = parseInt(rCookie);
if(rCookie > 215 && rCookie <= ($(document).width()-700-100))
	$('div#page_tree').width(rCookie);

$('div#page_tree').height($(document).height()-300);
$('div#page_form').height($('div#page_tree').height());
$('div#page_form').width($(document).width()-$('div#page_tree').width()-65);
$('div#page_options_bottom').width($('div#page_form').width()-17);

// tab init
nTabInit('ntabs0');
var tab_height = $('div#page_form').height()-$('#page_form .ui-tabs-nav').height()-$('#page_options_bottom').height()-60;
$('.ui-tabs-panel').css('height', tab_height);
$('#ntabs0_content').css('overflow', 'auto').css('overflow-x', 'hidden').scrollTop(0);


// special for frontoffice toolbar
if(from_mode == 'iframe')
{
	$('#btn_cancel').val(lang_msg_68);
	$('#page_options').hide();
	$('#page_tree').hide();
	$('#preview').hide();

	// resize element
	$('div#page_form').height($(document).height()-30);
	$('div#page_form').width($(document).width()-30);
	
	tab_height = $('div#page_form').height();
	$('.ui-tabs-panel').css('height', tab_height);
	$('div#page_options_bottom').width($('div#page_form').width()-15);

	// update content
	$('#Content').width($('#page_form').width() - 180);
	$('#Content').height(tab_height - 165);
    $('#ContentResume').width($('#page_form').width() - 180);


    // hide main title
    $('#main_title').hide();

    // reload height
    var tab_height = $('div#page_form').height()-$('#page_form .ui-tabs-nav').height()-$('#page_options_bottom').height()-60;
    $('.ui-tabs-panel').css('height', tab_height);
	
	reloadPage($('#dID').val());
}



// update content
$('#Content').width($('#page_form').width()-180);
$('#Content').height(tab_height - 80);
$('#ContentResume').height(100);
$('#ContentResume').width($('#Content').width());


$(window).resize(function(){
    $('div#page_form').width($(document).width()-$('div#page_tree').width()-65);
    $('div#page_options_bottom').width($('div#page_form').width()-17);
    $('#Content').width($('#page_form').width()-180);
    $('#Content').height(tab_height - 80);
    $('#ContentResume').width($('#page_form').width()-180);
});

$("#dID").bind("keypress", function(e){
	if (e.keyCode == 13){
		directID();
		e.keyCode = null;
		return false;
	}
});

// set access key highlight
$('.ui-tabs-nav li, #preview a').each(function(){

    access_key = $(this).attr('accesskey');
    txt = trim($(this).text());

    s1 = strtoupper(access_key);
    if(txt.indexOf(s1) == 0)
        txt = str_replace(s1, '<u>'+s1+'</u>', txt, 1);
    else
        txt = str_replace(access_key, '<u>'+access_key+'</u>', txt, 1);
    
    $(this).html(txt);

});

// hidden fields
for(i=0; i < hfs.length; i++)
{
	n = trim(hfs[i]);
	if(!empty(n))
	{
		if(n.indexOf('fieldset') == 0)
			$('#'+n).hide();
		else
			$('#'+n).parent('p').hide();
	}
}

// variables
$('.ui-tabs-nav li').eq(4).hide(); // hide tab variable
v = $('#fieldset_Variables').children('p').length
if(v == 0)
{
	$('#fieldset_Variables').hide();
}

$('#VirtualPagename').after('<input class="button" type="button" onclick="generateFromH1();" value="'+lang_msg_82+'" /> <input id="urlRewritingBtn" class="button" type="button" value="Url rewriting" onclick="openUrlRewriting()" style="display: none;" />');
// $('#Content, #ContentResume').tabOverride();


// @todo> block select multiple
$("#tab_content_blocs select").asmSelect({
	sortable: true,
	animate: false,
	highlight: false,
	addItemTarget: 'bottom',
    hideWhenAdded: true,
	customized: true
});


// @todo> MetaKeywords autocomplete
uri = 'index.php?mod=_page-manager&do=exec&_action=get_meta_keywords';
$("#MetaKeywords").autocomplete(uri, {
		width: 300,
		multiple: true,
		autoFill: true,
		multipleSeparator: ", ",
		matchContains: false,
		delay:300,
		minChars:2,
		cacheLength:250,

		formatItem: function(data, i, n, value){
			return value;
			//return value.split(".")[0];
		},

		formatResult: function(data, value){
			return value;
			//return value.split(".")[0];
		}
});

// transfrorm multiple select to checklist
$('#former select.checkbox-list').each(function(){

	id = $(this).attr('id');
    id = str_replace('[]', '', id);
    $(this).attr('id', id);

	tmp_str = '<div class="checkbox_list">';
	$('#former #'+id+' option').each(function(){

		tmp_str += '<label>';

		checked = '';
		if($(this).is(':selected'))
			checked = 'checked';

		tmp_str += '<input type="checkbox" '+checked+' name="'+id+'[]" value="'+$(this).val()+'" />';
		tmp_str += $(this).text();
		tmp_str += '</label>';
	});
	tmp_str += '</div>';

	$('#former #'+id).replaceWith(tmp_str);
});

// allow url rewriting
if(userAllowedPluginUrlRewriting == '1')
	$('#urlRewritingBtn').show();


// site map
$("#former #AccessRestricted").change(function(){
	trtUpdateAccessRestrict();
});

$("#former #Sitemap").change(function(){
	trtUpdateSitemap();
});

$("#former #SitemapPageType").change(function(){
	trtUpdateSitemapOptions();
});

// ucfirst
$('#former .ucfirst').blur(function(e) {
	$(this).val(ucfirst($(this).val()));
});

// upper
$('#former .upper').blur(function(e) {
	$(this).val(strtoupper($(this).val()));
});

// lower
$('#former .lower').blur(function(e) {
	$(this).val(strtolower($(this).val()));
});


// init lang image option
$('#pager_content #Language option').each(function(){
	flag = $(this).val();
	str = 'background:url(/library/media/images/flag/'+flag+'.gif) no-repeat 2px 3px; background-color:white;';
	$(this).attr('style', str);
});

selectSetOptionStyle('Language');


// color picker
$('#former .widget_colorpicker').each(function(){

    current_id = $(this).attr('id');
    $('#former #'+current_id).ColorPicker({

	    onSubmit: function(hsb, hex, rgb, el) {
		    $(el).val('#'+hex);
		    $('#former #'+current_id+'_colorpicker_preview').css('background-color', $(el).val());
		    $(el).ColorPickerHide();
	    },

        onBeforeShow: function () {
            v = str_replace('#', '', this.value);
		    $(this).ColorPickerSetColor(v);
	    }
    });

    $('#former #'+current_id+'_colorpicker_preview').css('background-color', $('#former #'+current_id).val());

});

// content view
$('#NutsPageContentViewID').change(function(){

    vID = $(this).val();
    if(empty(vID))vID = 0;

    $('.content_view_wrapper').hide();
    $('#content_view_wrapper_'+vID).show();
	
    // force update color preview
    $('#former .widget_colorpicker').each(function(){
        current_id = $(this).attr('id');
        $('#former #'+current_id+'_colorpicker_preview').css('background-color', $('#former #'+current_id).val());
    });


});


// detect delete key on simpletree
if(from_mode != 'iframe')
{
    $(document).keydown(function(e) {
        if(e.keyCode == 46 && $('.simpleTree span.active').length && !$('#page_form').is(':visible')) {
            deletePage();
            e.stopPropagation();
        }
    });
}




// init MetaTitle, urlrewriting from H
$('#former #H1').change(function(){

    if(empty($(this).val()) || $(this).val() == 'Untitled')return;


    cur_val = ucfirst($(this).val());

    if($('#former #MenuName').val() == "Untitled")
    {
        $('#former #MenuName').val(cur_val);
    }

    if(empty($('#former #MetaTitle').val()))
    {
        $('#former #MetaTitle').val(cur_val);
    }
    else
    {
        if((c=confirm(lang_msg_103)))
        {
            $('#former #MetaTitle').val(cur_val);
        }
    }
    
    if(empty($('#former #VirtualPagename').val()))
    {
        generateFromH1();
    }
    else
    {
        if((c=confirm(lang_msg_104)))
        {
            generateFromH1();
        }
    }
});

$('#tpl_search').keypress(function(e){
    if(e.which == 13)
    {
        e.preventDefault();
        $("#tpls_preview div:visible").eq(0).find('img').click();
        return false;
    }
    
});

$('#tpl_search').keyup(function(e){

    // enter
    if(e.which == 13)
    {
        e.preventDefault();
        return false;
    }

    // esc
    if(e.which == 27)
    {
        $('#tpls_preview div').show();
    }
    else
    {
        v = ucfirst($(this).val());
        if(v == '')
        {
            $('#tpls_preview div').show();
        }
        else
        {
            $('#tpls_preview div').hide();
            $("#tpls_preview div[title*='"+v+"']").show();
        }
    }
});


$('#page_tree').scroll(function(){
    $('#page_tree .ui-resizable-handle').css('top', $('#page_tree').scrollTop());
    // $('#page_tree .ui-resizable-handle').css('left', $('#page_tree').width());
});


$('body').click(function(){
    hideContext();
});


pageManagerInit();
reloadPage();
helperInit('#page_form');
loadRichEditor(currentTheme);



// initialize option after asmSelect
setTimeout(function(){

    // block_options = ' <a href="javascript:blockReload();"><i class="icon-loop"></i></a>';

    block_options = '';
    if(userAllowedPluginBlockManager == '1')
        block_options = ' <a href="javascript:popupModal(\'index.php?mod=_block_builder&do=list&parent_refresh=0\', \'block builder\');"><img src="/nuts/img/icon-folder.png" align="absmiddle"></a>';

    if(block_options != '')
        $('.asmSelect').after(block_options);

}, 500);