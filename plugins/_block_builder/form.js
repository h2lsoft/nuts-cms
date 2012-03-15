$("#former #Type").change(function(){
	trtUpdateType();
});



function trtUpdateType(){

	v = $("#former #Type").val();

	if(v == 'TEXT')
	{
		$('#iframe_radio_Text').attr('checked', true);
		WYSIWYGToggleIt('Text');
	}
	else
	{
		if($('#iframe_radio_Text').attr('checked'))
		{
			$('#iframe_radio_Text').attr('checked', false);
			WYSIWYGToggleIt('Text');
		}
	}
}


$(document).ready(function(){


	setTimeout(function () {

		trtUpdateType();

	}, 1000);
	

});
