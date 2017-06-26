$(document).ready(function (){

	$('input').click(function(){

		$(this).select();

	});

});


function block(state){

	cur_step = wi_step - 1;
	if(state)
	{
		$.blockUI({

					 message: '<img src="../nuts/img/ajax-loader.gif" align="absmiddle" /> Loading...',
					 fadeIn: 700,
					 fadeOut: 700,
					 centerY: false,
					 showOverlay: false,
					 css: {
							top: '300px',
							width: '350px',
							padding: '10px',
							backgroundColor: '#fff',
							color: '#000'
							}
				});
	}
	else
	{
		$.unblockUI();
	}


}

function stepNext()
{
	arr = {};
	arr['wi_step'] = wi_step;

	arr['WEBSITE_NAME'] = $('#WEBSITE_NAME').val();
	arr['WEBSITE_PATH'] = $('#WEBSITE_PATH').val();
	arr['WEBSITE_URL'] = $('#WEBSITE_URL').val();
	arr['ADMIN_EMAIL'] = $('#ADMIN_EMAIL').val();
	arr['NO_REPLY_EMAIL'] = $('#NO_REPLY_EMAIL').val();


	if(wi_step == 4)
	{
		arr['DB_HOST'] = $('#DB_HOST').val();
		arr['DB_LOGIN'] = $('#DB_LOGIN').val();
		arr['DB_PASS'] = $('#DB_PASS').val();
		arr['DB_PORT'] = $('#DB_PORT').val();
		arr['DB_NAME'] = $('#DB_NAME').val();
		arr['DB_LANG'] = $('#DB_LANG').val();
		
	}

	$('#btn_next').attr('disabled', true);


	block(1);
	$.post('index.php?ajax=1', arr, function(data) {


		$.unblockUI({
                onUnblock: function(){


						if(data.error)
		{
			// alert("Error(s) :\n======\n\n"+data.error_msg);

			msg = "<strong>Error(s) :</strong><br />=========<br />"+data.error_msg;

			$.blockUI({
								title:    'Error',
								message: msg,
								timeout:3000,
								centerY:false,

								css: {
										top: '300px',
										padding: '15px',
										backgroundColor: 'red',
										color: '#fff'
								}
							});


			 $('.blockOverlay').click($.unblockUI);

			if(wi_step == 1 || wi_step == 4)
				$('#btn_next').attr('disabled', false);

			return;
		}
		else
		{
			cur_step = wi_step - 1;
			$('div.ni_content_text').eq(cur_step).hide();
			wi_step = wi_step + 1;
			cur_step = wi_step - 1;
			$('div.ni_content_text').eq(cur_step).show();


			$('#result_'+wi_step).html(data.result);

			if(wi_step > 1)$('#btn_prev').show();

			$('#ni_left li').removeClass('selected');
			$('#ni_left li').eq(cur_step).addClass('selected');

			$('#btn_next').attr('disabled', false);
			if($('.msg_error').length > 0)
			{
				$('#btn_next').attr('disabled', true);
			}

			// final
			if(wi_step == 5)
			{
				
				
				$('#ni_content_bottom_bar').hide();
			}


		}


				}
        });



	}, 'json');

}

function stepPrev()
{
	$('#btn_prev').attr('disabled', true);


	cur_step = wi_step - 1;
	$('div.ni_content_text').eq(cur_step).hide();

	wi_step = wi_step - 1;
	cur_step = wi_step - 1;
	$('div.ni_content_text').eq(cur_step).show();

	$('#ni_left li').removeClass('selected');
	$('#ni_left li').eq(cur_step).addClass('selected');

	if(wi_step == 1)
		$('#btn_prev').hide();


	$('#btn_prev').attr('disabled', false);
	$('#btn_next').attr('disabled', false);

}

