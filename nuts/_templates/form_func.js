// add WYSIWYG markup
$('textarea.mceEditor').each(function (){

	id = this.id;
	v = $('#former #'+id).val();
	v = parse_nuts_tags(v);
	$('#former #'+id).val(v);

});

// ajax autocompletion
$('#former input.ajax_autocomplete').each(function(){

	field = this.id;
	$("#former #"+field).autocomplete(
									form_ajax_ac_uri+field,
									{
										delay:300,
										minChars:2,
										matchSubset:false,
										matchContains:false,
										cacheLength:0,
										autoFill:true,
										max:200
									}
							 );
});


// allow tab in textareas
$('#form_content textarea.tabby').tabby();

// $('#form_content input:text:first').focus();
//$('#form_content textarea.code_editor').each(function(){
  // initCodeEditor(this.id, "php", 0);
//});

// upper
$('#form_content .upper').blur(function(e) {$(this).val(strtoupper($(this).val()));});

// lower
$('#form_content .lower').blur(function(e) {$(this).val(strtolower($(this).val()));});

// ucfirst
$('#form_content .ucfirst').blur(function(e) {$(this).val(ucfirst($(this).val()));});




// $('textarea.resizable:not(.processed)').TextAreaResizer();
// margin-left:150px;

$('a.tt').tooltip({
    track: true,
    delay: 0,
    showURL: false,
    showBody: " - ",
    opacity: 0.85
});


function updateFormContentHeight(err)
{
	h = $(window).height()-125;
	if(err)
	{
		h -= $('#form_error').height();
		h -= 45;
	}
	$('#form_content').height(h);
}


loadRichEditor(currentTheme);
updateFormContentHeight(false);
helperInit('#form_content');
// $('textarea.resizable:not(.processed)').TextAreaResizer();



// transfrorm multiple select to checklist
$('#former select[type=select-multiple].checkbox-list').each(function(){

	id = $(this).attr('id');
    id = str_replace('[]', '', id);
    $(this).attr('id', id);

	tmp_str = '<div class="checkbox_list">';

    label = 'Select All';
    if(nutsUserLang == 'fr')
        label = 'Selectionner Tous';

    tmp_str += '<a href="javascript:;" onclick="checkboxSelectAll(\''+id+'\')">'+label+'</a>';

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




var options = {
	target:        '',   // target element(s) to be updated with server response
	beforeSubmit:  showRequest,  // pre-submit callback
	success:       showResponse  // post-submit callback
};

// bind form using 'ajaxForm'
$('#former').ajaxForm(options);

// pre-submit callback
function showRequest(formData, jqForm, options)
{
    // show form white canvas
    $('#nuts_form_canvas').width($('#form_content').width()+10);
    $('#nuts_form_canvas').height($('#form_content').height()+$('#form_error').height());
    pos = $('#form_content').offset();
    $('#nuts_form_canvas').css('top', pos.top+'px');
    $('#nuts_form_canvas').css('left', pos.left+'px');
    $('#nuts_form_canvas').show();

	// WYSIWYG interception
	for(l=0; l < formData.length; l++)
	{
		if($('#former #'+formData[l].name).hasClass('mceEditor'))
			formData[l].value = remove_nuts_tags(formData[l].value);
	}

	var queryString = $.param(formData);

	$('#btn_submit').attr('value', nuts_lang_msg_23);
 	$('#btn_submit').attr("disabled", true);
	// $('#form_content').fadeTo(0, 0.33);

	forceWYSIWYGUpdate();



    return true;
}

// post-submit callback
function showResponse(responseText, statusText)
{
    resp = responseText;
	ret = $(resp);
	cont = $('#form_error', ret).html();

	if(jQuery.trim(cont) != '')
	{
		// cont = utf8_decode(cont);
        cont = str_replace('&lt;img', '<img', cont);
        cont = str_replace('/&gt;', '/>', cont);
		$('#form_error').html(cont);

		fields = new Array();
		tab = cont.split('<img src="img/icon-error.gif" align="absmiddle">');

        for(i=0; i < tab.length; i++)
		{
			tmp = tab[i].split("`");
			if(tmp.length >= 3)
			{
				label_text = tmp[1];
				label_text = str_replace("\n", '', label_text);
				label_text = str_replace("\r", '', label_text);
				label_text = str_replace("\t", '', label_text);
				label_text = trim(label_text);
				if(!empty(label_text))
				{
					for(k in form_fields)
					{
						if(form_fields[k] == label_text)
						{
							label_text = k;
							break;
						}
					}
				}

				fields[fields.length] = label_text;
			}
		}

		// reset all style
		$('#form input').css('border', '');
		$('#form select').css('border', '');
		$('#form select').css('background-color', '');
        $('#form textarea').css('border', '');

		for(i=0; i < fields.length; i++)
		{
			f = fields[i];

			t = $('#form #'+f).attr('type');
			if(t == 'text' || t == 'textarea')
			{
				$('#form #'+f).css('border', '1px solid red');
			}
			else if(t == 'select-one' || t == 'select')
			{
				$('#form #'+f).css('border', '1px solid red');
				$('#form #'+f).css('background-color', 'red');
			}
		}

		//$('#form').html(resp);
		updateFormContentHeight(true);
		$('#form_error').show();
		$('#btn_submit').attr('value', nuts_lang_msg_21);
	 	$('#btn_submit').removeAttr("disabled");
        $('#nuts_form_canvas').hide();

		//$('#form_content').fadeTo(0.33, 1, function(){
			forceWYSIWYGUpdate();
		//});
	}
	else
	{
		// force close for form_percent
		if(!form_percent)
		{
			// form is valid but no close is checked !
			if(!$('#close_after').is(':checked'))
			{
                // reset all style
                $('#form input').css('border', '');
                $('#form select').css('border', '');
                $('#form select').css('background-color', '');
                $('#form textarea').css('border', '');


				// remove add by edit mode
				str = $('#former').attr('action');
				if(str.indexOf('&do=add&ID=0&') != -1)
				{
					NUTS_REC_CUR_ID = $('#NUTS_REC_CUR_ID', ret).val();
					str_rep = str_replace('&do=add&ID=0&', '&do=edit&ID='+NUTS_REC_CUR_ID+'&',str);
					$('#former').attr('action', str_rep);
				}

				// mode no direct
				$('#form_error').slideUp();
				$('#btn_submit').removeAttr("disabled");
				$('#btn_submit').attr('value', nuts_lang_msg_21);
                $('#nuts_form_canvas').hide();

				//$('#form_content').fadeTo(0.33, 1, function(){
					forceWYSIWYGUpdate();
                    updateFormContentHeight();
				//});

				// refresh window
				if(popup == '1')
                {
                    window.opener.system_refresh();
                }
				else
                {
                    system_refresh();
                }

			}
			else
			{
				// prevent WYSIWYG loading
				setTimeout(function(){

					$('#form').html(resp);
                    $('#nuts_form_canvas').hide();
					$('#form_valid').show();

					// refresh parent window
					if(popup == '1')
					{
						window.opener.system_refresh();
						setTimeout("window.close();", 2000);
					}
					else
					{
						h_tmp = $(window).height() / 2 - 225;
						$('.ui-dialog').height(450).css('top', h_tmp);

                        if(form_plugin == '_user-profile')
                            document.location.reload();
                        else
                        {
                            system_refresh();
                            setTimeout("$('#form_window').dialog('close')", 1700);
                        }
					}

				}, 1000);
			}
		}
		else // percent treatment
		{
			// prevent WYSIWYG loading
			setTimeout(function(){

					$('#form').html(resp);
                    $('#nuts_form_canvas').hide();

					// init treatment
					$('#trt_progress_window').show();

					// launch big treatment by iframe
					formPercentRender();

			}, 1000);
		}
	}
}


function formPercentRender(){

	$.get(form_percent_uri+'&time='+time(), {}, function(data){

		if(!is_array(data))
		{
			alert(data);
			return;
		}


		if(data.start >= data.end)
		{
			$('#trt_progress_window').hide();
			system_refresh();
		}
		else
		{
			$('#trt_progress_bar').width(data.percent);
			$('#trt_progress_percent').text(data.percent);
			$('#trt_progress_start').text(data.start);
			$('#trt_progress_end').text(data.end);

			formPercentRender();
		}

	}, 'json');

}



