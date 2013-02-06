<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

include(PLUGIN_PATH.'/config.inc.php');

// sql table
$plugin->formDBTable(array('NutsMedia'));

// fields
$plugin->formAddFieldSelect('Type', $lang_msg[1], true, array('YOUTUBE VIDEO', 'EMBED CODE', 'VIDEO', 'AUDIO'));
$plugin->formAddFieldText('Name', $lang_msg[2], 'notEmpty|unique', 'ucfirst');
$plugin->formAddFieldTextArea('Description', $lang_msg[3], false, 'ucfirst', 'height:45px;');
$plugin->formAddFieldMediaBrowser('Url', '', false);
$plugin->formAddFieldTextArea('EmbedCode', $lang_msg[11], false, '', '', '', $lang_msg[12]);
$plugin->formAddFieldText('EmbedCodePreviewUrl', $lang_msg[13], false, '', '', '', '', $lang_msg[14]);

// filters youtube video *********************************************************
$plugin->formAddFieldsetStart('YoutubeVideoParams', $lang_msg[4]);
$plugin->formAddFieldText('PVYT_url', 'Youtube url', false);
$plugin->formAddFieldText('PVYT_width', $lang_msg[7], false, '', 'width:40px; text-align:center;', '', '', '', $video_default_width);
$plugin->formAddFieldText('PVYT_height', $lang_msg[8], false, '', 'width:40px; text-align:center;', '', '', '', $video_default_height);
$plugin->formAddFieldsetEnd();
// end of filters youtube video **************************************************


// filters audio *********************************************************
$plugin->formAddFieldsetStart('AudioParams', $lang_msg[6]);
$plugin->formAddFieldBooleanX('PA_autoplay', 'Auto play', false);
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
$plugin->formAddException('PVYT_*');
$plugin->formAddException('PV_*');
$plugin->formAddException('PA_*');


// custom errors *************************************************************
if($_POST)
{

	if($_POST['Type'] == 'AUDIO')
	{
		$nuts->notEmpty('Url');
	}
	elseif($_POST['Type'] == 'VIDEO' || $_POST['Type'] == 'YOUTUBE VIDEO')
	{
		$nuts->notEmpty('PV_width');
		$nuts->notEmpty('PV_height');
		if($_POST['Type'] == 'VIDEO')$nuts->notEmpty('Url');
		if($_POST['Type'] == 'YOUTUBE VIDEO')$nuts->notEmpty('PVYT_url');

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
    if($_POST['Type'] == 'YOUTUBE VIDEO')$pre = 'PVYT';
	
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