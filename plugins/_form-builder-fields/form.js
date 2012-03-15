initCodeEditor('OtherValidation', 'php', true);
initCodeEditor('PhpCode', 'php', true);

function trtUpdateValues(){

	v = strtolower($("#Type").val());
	if(v == 'radio' || v == 'checkbox' || v == 'select'  || v == 'select-multiple')
	{
		$("#Value").parents("p").show();
	}
	else
	{
		$("#Value").parents("p").hide();
	}

	// section
	if(v == 'section' || v == 'html')
	{
		$("#Name").parents("p").hide();
		$("#Attributes").parents("p").hide();
		$("#Required").parents("p").hide();
		$("#Email").parents("p").hide();
		$("#I18N").parents("p").hide();
		$("#TextAfter").parents("p").hide();
		$("#OtherValidation").parents("p").hide();
	}
	else
	{
		$("#Name").parents("p").show();
		$("#Attributes").parents("p").show();
		$("#Required").parents("p").show();
		$("#Email").parents("p").show();
		$("#I18N").parents("p").show();
		$("#TextAfter").parents("p").show();
		$("#OtherValidation").parents("p").show();
	}

	// html code
	if(v == 'html')
	{
		$("#Label").parents("p").show();
		$("#HtmlCode").parents("p").show();
	}
	else
	{
		$("#Label").parents("p").show();
		$("#HtmlCode").parents("p").hide();
	}


	// php code
	if(v != 'php')
	{
		$("#PhpCode").parents("p").hide();
	}
	else
	{
		$("#PhpCode").parents("p").show();
	}

	// file	
	if(v != 'file')
	{
		$("#FilePath").parents("p").hide();
		$("#FileAllowedExtensions").parents("p").hide();
		$("#FileAllowedMimes").parents("p").hide();
		$("#FileMaxSize").parents("p").hide();
	}
	else
	{
		$("#FilePath").parents("p").show();
		$("#FileAllowedExtensions").parents("p").show();
		$("#FileAllowedMimes").parents("p").show();
		$("#FileMaxSize").parents("p").show();
	}

	// text after
	if(v == 'text' || v == 'file' || v == 'password')
	{
		$("#TextAfter").parents("p").show();
	}
	else
	{
		$("#TextAfter").parents("p").hide();
	}





}

$("#Type").change(function(){
	trtUpdateValues();
});

$("#Type").keypress(function(){
	trtUpdateValues();
});


trtUpdateValues();



$("#Name").blur(function(){

	if($("#Label").val() == '')
	{
		$("#Label").val($("#Name").val());
	}
});



