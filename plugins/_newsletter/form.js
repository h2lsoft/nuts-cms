$(function(){
	
	
	$('#former #p_MailingList label').remove();
	
	setTimeout(function(){
	
		$('#former #MailingList').multiSelect();
		
	}, 500)
	
});




function trtUpdateMode(){

	v = $("#former #ModeTest").val();

	if(v == 'YES'){
		$('#fieldset_MailingList').hide();
		$('#fieldset_TestTo').show();
	}
	else{
		$('#fieldset_MailingList').show();
		$('#fieldset_TestTo').hide();
	}
}

trtUpdateMode();

$("#former #ModeTest").change(function(){
	trtUpdateMode();
});


function trtUpdateDraft(){

	v = $("#former #Draft").val();

	if(v == 'YES')
	{
		$('#fieldset_SchedulerDate').hide();
	}
	else{
		$('#fieldset_SchedulerDate').show();
	}
}

trtUpdateDraft();

$("#former #Draft").change(function(){
	trtUpdateDraft();
});




$('#former #TemplateMode').change(function(){

	v = $(this).val();

	$('#fieldset_Template').hide();
	$('#fieldset_Body').hide();

	if(v == 'YES')
	{
		$('#fieldset_Template').show();
	}
	else
	{
		$('#fieldset_Body').show();
	}


});

$('#former #TemplateMode').change();
