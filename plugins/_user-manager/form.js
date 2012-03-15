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