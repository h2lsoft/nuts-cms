function lostPassword()
{

	$("#nuts_login_lost_password .submit").hide();
	
	$.get(login_uri, {action:'lost_password', Email:$('.nuts_form #uEmail').val()}, function(data){

		data = explode('@@@', data);
		resp = data[0];
		if(resp != 'ok' && resp != 'ko')
			$("#nuts_login_lost_password .lost_password_form_message").text("Unknow error").show();
		else
		{
			$("#nuts_login_lost_password .lost_password_form_message").removeClass('lost_password_form_message_ok');
			if(resp == 'ok')
            {
                $("#nuts_login_lost_password .lost_password_form_message").addClass('lost_password_form_message_ok');

                setTimeout(function(){
                    $("#nuts_login_lost_password .lost_password_form_message").hide();
                    $('div#nuts_login_lost_password').slideUp('normal');
                }, 1200);

            }
			$("#nuts_login_lost_password .lost_password_form_message").text(data[1]).show();
		}
	
		$("#nuts_login_lost_password .submit").show();
		
	}, 'text');


}