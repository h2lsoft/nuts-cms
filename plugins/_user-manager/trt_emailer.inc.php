<?php

if($_POST['IdentificationEmail'] == 'YES')
{	
	nutsSendEmail($lang_msg[13], $_POST, $_POST['Email']);
}




?>