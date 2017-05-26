initCodeEditor('Configuration', Syntax, 0);

var options = {
	beforeSubmit:  showRequest,  // pre-submit callback
	success:       showResponse  // post-submit callback
};
$('#former').ajaxForm(options);


// pre-submit callback
function showRequest(formData, jqForm, options) {

	var queryString = $.param(formData);

	$('#btn_submit').attr('value', nuts_lang_msg_23);
 	$('#btn_submit').attr("disabled", true);
 	
    return true;
}

// post-submit callback
function showResponse(responseText, statusText)  {

	if(responseText == 'ok')
	{
		notify('ok', lang_msg_2);
	}
	else
	{
		notify('error', lang_msg_1);
	}

	// $('#Configuration').fadeTo(0.33, 1);

	$('#btn_submit').attr('value', nuts_lang_msg_49+" - Alt+S");
 	$('#btn_submit').removeAttr("disabled");
}

function updateCfFile()
{
	v = $('#f').val();
	uri = $('#former').attr('action')+'&_action2=get&f='+base64_encode(v);
	system_goto(uri, 'content');
}


setTimeout(function(){
	
	v = f;
	if(v != "")
	{
		v = base64_decode(v);
		$('#f').val(v);
	}
	
}, 300);


