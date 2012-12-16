<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsMedia'));

// fields
$plugin->formAddFieldSelect('Type', $lang_msg[1], true, array('VIDEO', 'EMBED CODE', 'AUDIO'));
$plugin->formAddFieldText('Name', $lang_msg[2], 'notEmpty|unique');
$plugin->formAddFieldTextArea('Description', $lang_msg[3], false);
$plugin->formAddFieldMediaBrowser('Url', '', false);
$plugin->formAddFieldTextArea('EmbedCode', $lang_msg[11], false, '', '', '', $lang_msg[12]);
$plugin->formAddFieldText('EmbedCodePreviewUrl', $lang_msg[13], false, '', '', '', '', $lang_msg[14]);

// filters audio *********************************************************
$plugin->formAddFieldsetStart('AudioParams', $lang_msg[6]);
$plugin->formAddFieldBooleanX('PA_autoreplay', 'Loop', false);
$plugin->formAddFieldsetEnd();
// end of filters audio **************************************************


// filters video *********************************************************
$plugin->formAddFieldsetStart('VideoParams', $lang_msg[4]);

$plugin->formAddFieldText('PV_width', $lang_msg[7], false, '', 'width:40px; text-align:center;', '', '', '', $video_default_width);
$plugin->formAddFieldText('PV_height', $lang_msg[8], false, '', 'width:40px; text-align:center;', '', '', '', $video_default_height);
$plugin->formAddFieldImageBrowser('PV_startimage', $lang_msg[9], false, '', $lang_msg[10]);
$plugin->formAddFieldBooleanX('PV_autoplay', 'Autoplay', false);
$plugin->formAddFieldBooleanX('PV_loop', 'Loop', false);
$plugin->formAddFieldText('PV_top1', 'Logo', false, '', '', '', '', $lang_msg[5], $video_default_top);
$plugin->formAddFieldText('PV_skin', 'Skin', false, '', '', '', '', '', $video_default_skin);

$plugin->formAddFieldsetEnd();
// end of filters video **************************************************


$plugin->formAddFieldHidden('Parameters', '', false);


// forbiden
$plugin->formAddException('PV_*');
$plugin->formAddException('PA_*');


// custom errors *************************************************************
if($_POST)
{
	if($_POST['Type'] == 'AUDIO')
	{
		$nuts->notEmpty('Url');
	}
	elseif($_POST['Type'] == 'VIDEO')
	{
		$nuts->notEmpty('PV_width');
		$nuts->notEmpty('PV_height');
		$nuts->notEmpty('Url');
	}
	elseif($_POST['Type'] == 'EMBED CODE')
	{
		$nuts->notEmpty('EmbedCode');
		$nuts->notEmpty('EmbedCodePreviewUrl');
	}
}



if($_POST && $nuts->formGetTotalError() == 0)
{
	$pre = ($_POST['Type'] == 'AUDIO') ? 'PA' : 'PV';
	
	$_POST['Parameters'] = '';
	foreach($_POST as $key => $val)
	{
		if(preg_match("/^$pre/", $key))
		{
			$key = str_replace($pre."_", '', $key);
			$_POST['Parameters'] .= $key.'=>'.$val.'@@';
		}
	}
}







?>