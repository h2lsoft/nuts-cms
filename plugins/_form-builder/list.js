function cleanForm(formID)
{
	msg_en = "Would you like to clean records of this form ?";
	msg_fr = "Voulez-vous supprimer les enregistrements de ce formulaire ?";

	msg = msg_en;
	if(nutsUserLang == 'fr')
		msg = msg_fr;

	if((c=confirm(msg)))
	{
		$.get("/nuts/index.php", {mod:'_form-builder', do: 'list', action: 'clean', ID: formID}, function (){
			$('#form_'+formID).html(' - ');
		});
	}

}