setTimeout(function(){

	$('#former #BatchMode').change();

}, 200);




$('#former #BatchMode').change(function(){

	v = $('#former #BatchMode').val();
	
	
	$('#fieldset_Email').hide();
	$('#fieldset_Batch').hide();
	
	if(v == 'NO')
	{
		$('#fieldset_Email').show();
	}
	else
	{
		$('#fieldset_Batch').show();
	}
	
});