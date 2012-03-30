for(i=0; i < hfs.length; i++)
{
	n = trim(hfs[i]);

	if(!empty(n))
	{
		if(n.indexOf('fieldset') == 0)
			$('#former #'+n).hide();
		else
			$('#former #'+n).parent('p').hide();
	}
}


// hide filters
if($('#former #fieldset_CustomFilter').children('p').length == 0)
	$('#former #fieldset_CustomFilter').hide();


// assign twitter
$("#former #Title").blur(function(){

	if($("#former #Twitter").is(':visible'))
	{
		$("#former #Twitter").val($("#former #Title").val());
	}

});


// twitter
if($('#former #Twitter'))
{
	$('#former #Twitter').keyup(function(){
		v = $('#former #Twitter').val();
		v = strlen(v)+1;
		$("#twitter_count").text(v);
	});


	$('#former #twitter_a').click(function(){
		v = $('#Twitter').val();

		twitter_login = $("#twitter_a").attr('data-login');
		uri = "http://twitter.com/home?status="+v;
        popupModal(uri, "Twitter", 800, 600);
	});
}



/// facebook
function openFacebook(url){

	window.open(url, 'facebook_publish', 'width=780,height=250, top=0, left=0, scrollbars=no');

}

if($('#former #Facebook')){

	$('#former #Facebook').parent('p').css('padding-left', '90px');
	$('#former #Facebook').remove();

}





