$('#former #Password').after('  <a href="javascript:generatePassword()">'+lang_msg_11+'</a>');

function generatePassword()
{
	keylist = "abcdefghijklmnopqrstuvwxyz1234567890";
	temp = '';
	plength = 8;

	for(i=0; i < plength; i++)
		temp += keylist.charAt(Math.floor(Math.random()*keylist.length))
	
	$('#former #Password').val(temp);
}


function avatarInit(){

    v = $('#former #Avatar').val();
    if(empty(v))
        $('#former #avatar_image').attr('src', '/nuts/img/gravatar.jpg');
    else
        $('#former #avatar_image').attr('src', v);

}
avatarInit();

$('#former #Avatar').keyup(function(){
    avatarInit();
});

$('#former #Avatar').click(function(){
    $(this).select();
});