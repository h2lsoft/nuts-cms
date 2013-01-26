<?php
/**
 * Plugin slider - Form layout
 * 
 * @version 1.0
 * @date 01/01/2013
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsSlider'));

// fields
$plugin->formAddFieldText('Name', $lang_msg[1], true, 'ucfirst');
$plugin->formAddFieldTextarea('Description', "", false, 'ucfirst', 'height:45px;');

// options
$plugin->formAddFieldsetStart('Options');
$plugin->formAddFieldText('Width', "", true, 'number', 'width:45px; text-align:center;', '', '');
$plugin->formAddFieldText('Height', "", true, 'number', 'width:45px; text-align:center;', '', '');
$plugin->formAddFieldBoolean('Circular', "", true);
$plugin->formAddFieldBoolean('Infinite', "", true);
// $plugin->formAddFieldBooleanX('Responsive', "", true);

$opts = array('left', 'right', 'up', 'down');
$plugin->formAddFieldSelect('Direction', "", true, $opts);

$opts = array('left', 'right', 'center');
$plugin->formAddFieldSelect('Align', "", true, $opts);

$plugin->formAddFieldText('Padding', "", false, 'number', '', '', '', '', 0);

$plugin->formAddFieldText('PauseDuration', "Pause duration", true, 'number', '', 'ms', '', '', 3500);
$plugin->formAddFieldText('ScrollDuration', "Transition duration", true, 'number', '', 'ms', '', '', 1000);

$opts = array("crossfade", "fade", "scroll", "directscroll", "cover", "cover-fade", "uncover","uncover-fade", "none");
$plugin->formAddFieldSelect('Fx', "", true, $opts);

$plugin->formAddFieldText('Items', "", true, 'number', '', '', '', '', 1);

$plugin->formAddFieldsetEnd();






?>