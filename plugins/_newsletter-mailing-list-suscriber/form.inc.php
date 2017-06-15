<?php

/* @var $plugin Plugin */

// sql table PLUGIN_PATH
$plugin->formDBTable(array('NutsNewsletterMailingListSuscriber')); // put table here

// fields
$plugin->formAddFieldSelectSql('NutsNewsletterMailingListID', 'Mailing-list', true);
$plugin->formAddFieldSelect('Language', $lang_msg[1], true, $nuts_lang_options);
$plugin->formAddFieldDateTime('Date', $lang_msg[2], true);

$plugin->formAddFieldBooleanX('BatchMode', 'Batch mode', true);
$plugin->formAddException('BatchMode');


// email
$plugin->formAddFieldsetStart('Email');
$plugin->formAddFieldText('Email', '', 'Email');
$plugin->formAddFieldText('LastName', $lang_msg[4], false, 'ucfirst');
$plugin->formAddFieldText('FirstName', $lang_msg[5], false, 'ucfirst');
$plugin->formAddFieldsetEnd();

// batch
$plugin->formAddFieldsetStart('Batch');
$plugin->formAddFieldTextArea('Batch', "", false, "", "height:350px;", "", "[EMAIL]; [LAST_NAME]; [FIRST_NAME];");
$plugin->formAddException('Batch');
$plugin->formAddFieldsetEnd();




if($_POST)
{
	// single mode
    if($_POST['BatchMode'] == 'NO')
    {
    	$nuts->notEmpty('Email');
    	
        if(!empty($_POST['NutsNewsletterMailingListID']))
	    {
	    	Query::factory()->select('Email')
	                        ->from('NutsNewsletterMailingListSuscriber')
	                        ->whereNotEqualTo('ID', $_GET['ID'])
	                        ->whereEqualTo('NutsNewsletterMailingListID', $_POST['NutsNewsletterMailingListID'])
	                        ->whereEqualTo('Email', $_POST['Email'])
	                        ->limit(1)
	                        ->execute();
	
	        if($nuts->dbNumRows() > 0)
	        {
	            $nuts->addError('Email', $lang_msg[3]);
	        }
	    }
    }
    
    // batch
    if($_POST['BatchMode'] == 'YES')
    {
    	$BATCH_EMAILS = [];
    	$email_done = [];
    	
        $nuts->notEmpty('Batch');
        
        $batch = trim($_POST['Batch']);
        if(!empty($batch))
        {
        	$lines = explode("\n", $batch);
        
	        $cur_line = 1;
	        foreach($lines as $line)
	        {
	            $line = trim($line);
	            $line = explode(';', $line);
	            $line = array_map('trim', $line);
	            
	            if(count($line) != 4)
		        {
		            $nuts->addError('Batch', "line #{$cur_line}: number of columns not correct");
		        }
		        else
		        {
			        $email = strtolower($line[0]);
			        $last_name  = ucfirst($line[1]);
			        $first_name = ucfirst($line[2]);
			        
			        if(empty($email) || !email($email))
			        {
			            $nuts->addError('Batch', "line #{$cur_line}: email {$email} not correct");
			        }
			        
			        
			        if(in_array($email, $email_done))
			        {
			            $nuts->addError('Batch', "line #{$cur_line}: email {$email} already");
			        }
			        else
			        {
			            $BATCH_EMAILS[] = ['Email' => $email, 'LastName' => $last_name, 'FirstName' => $first_name];
			            $email_done[] = $email;
			        }
		        }
		        
	            $cur_line++;
	        }
        }
        
    }
    
	
}



