<?php


/* @var $plugin Plugin */
$plugin->formDBTable(array('NutsForm'));


// fields
$lng_options = nutsGetOptionsLanguages();
$lng_options = '<option value="AUTO">'.$lang_msg[18].'</option>'."\n".$lng_options;

$plugin->formAddFieldSelect('Language', $lang_msg[1], true, $lng_options);
$plugin->formAddFieldText('Name', $lang_msg[2], true);
$plugin->formAddFieldText('Description', $lang_msg[3], false);
$plugin->formAddFieldTextArea('Caption', $lang_msg[25], false, "", "", "", $lang_msg[26]);
$plugin->formAddFieldBooleanX('Captcha', '', true, $lang_msg[22]);
$plugin->formAddFieldHtmlArea('Information', '', false, "", $lang_msg[28]);

//$plugin->formAddFieldsetStart("Fields", $lang_msg[4]);
//$plugin->formAddField('Fields', '', 'hidden', false, array());
//$plugin->formAddFieldsetEnd();

$plugin->formAddFieldTextArea('FormBeforePhp', $lang_msg[29], false, 'php', 'height:100px', "", $lang_msg[30]);
$plugin->formAddFieldTextArea('FormCustomError', $lang_msg[5], false, 'php', 'height:100px', "", $lang_msg[6]);
$plugin->formAddFieldTextArea('JsCode', 'Javascript code', false, 'php', 'height:100px', "", $lang_msg[19]);

// validation
$plugin->formAddFieldsetStart("Formvalidation", $lang_msg[20], array());
$plugin->formAddFieldTextArea('FormValidPhpCode', $lang_msg[8], false, "php", 'height:100px', "", $lang_msg[9]);
$plugin->formAddFieldTextArea('FormValidHtmlCode', $lang_msg[10], false, "html", 'height:100px', "", $lang_msg[11]);
$plugin->formAddFieldBooleanX('FormStockData', $lang_msg[31], true, $lang_msg[32]);

// form mailer
$plugin->formAddFieldBooleanX('FormValidMailer', $lang_msg[12], true, $lang_msg[13]);

$plugin->formAddFieldsetStart("FormValidMailer", $lang_msg[14], array('help' => $lang_msg[24]));
$plugin->formAddFieldText('FormValidMailerFrom', $lang_msg[15], false, "", "", "", "", "", NUTS_EMAIL_NO_REPLY);
$plugin->formAddFieldText('FormValidMailerTo', $lang_msg[16], false, "", "", "", "", $lang_msg[23]);
$plugin->formAddFieldText('FormValidMailerSubject', $lang_msg[17], false);
$plugin->formAddFieldsetEnd();


$plugin->formAddFieldsetEnd();




$nuts->alphaNumeric('Name','_');


if($_POST)
{
	
	if($_POST['FormValidMailer'] == 'YES')
	{
		$nuts->notEmpty('FormValidMailerFrom');
		$nuts->email('FormValidMailerFrom');
		$nuts->notEmpty('FormValidMailerTo');
        // $nuts->email('FormValidMailerTo');
		$nuts->notEmpty('FormValidMailerSubject');
	}
	
}



?>