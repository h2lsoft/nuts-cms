function createCommentCookie(name,value,days) {

	if(days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function refreshCurrentPage(){

	uri = document.location.href;
	if(uri.indexOf('#') != -1)
		uri = uri.substr(0, uri.indexOf('#'));
	document.location.href = uri;
}

function citeIt(ID, Author){

	v = '[blockquote]';
	v += '[span]'+Author+' : [/span]\n\n';
	v += $('#comment'+ID+' .comment_text').text()+'\n';
	v += '[/blockquote]\n\n';

	$('#form_post_comment #Comment').val(v).focus();
}