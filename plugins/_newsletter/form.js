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

