<?php
/**
 * Plugin slider image - Form layout
 *
 * @version 1.0
 * @date 01/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsSliderImage'));

if($plugin->formModeIsAdding())
{
    $plugin->formActionAddParameter("NutsSliderID={$_GET['NutsSliderID']}");
}

// fields
$plugin->formAddFieldImage('Slider', 'Image', true);
$plugin->formAddFieldText('Title', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldText('Url', '', false);
$plugin->formAddFieldBoolean('Visible', '', true);

if($_POST)
{
	if(empty($_POST['Url']))$_POST['Url'] = 'javascript:;';


    // form assignation
    if($plugin->formModeIsAdding())
    {
        // get max position
        $_POST['NutsSliderID'] = $_GET['NutsSliderID'];
        $_POST['Position'] = $plugin->formGetMaxPosition('Position', 'NutsSliderID', $_POST['NutsSliderID']);
        $_POST['Position'] += 1;
    }

}


