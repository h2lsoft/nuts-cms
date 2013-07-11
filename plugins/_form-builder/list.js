function cleanForm(formID)
{
	msg_en = "Would you like to clean records of this form ?";
	msg_fr = "Voulez-vous supprimer les enregistrements de ce formulaire ?";

	msg = msg_en;
	if(nutsUserLang == 'fr')
		msg = msg_fr;

	if((c=confirm(msg)))
	{
        uri = ajaxerUrlConstruct('clean', '_form-builder', '', {ID:formID});
		$.get(uri, function (){
			$('#form_'+formID).html(' - ');
		});
	}

}