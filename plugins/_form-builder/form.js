function updateFormMailer()
{
	v = $("#former #FormValidMailer").val();
	if(v == 'YES')
		$("#former #fieldset_FormValidMailer").show();
	else
		$("#former #fieldset_FormValidMailer").hide();
}

$("#former #FormValidMailer").change(function(){

	updateFormMailer();

});
updateFormMailer();


initCodeEditor('JsCode', 'js', true);
initCodeEditor('FormBeforePhp', 'php', true);
initCodeEditor('FormCustomError', 'php', true);
initCodeEditor('FormValidHtmlCode', 'html', true);
initCodeEditor('FormValidPhpCode', 'php', true);


