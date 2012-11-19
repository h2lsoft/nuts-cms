<?php
/**
 * Plugin services - Form layout
 * 
 * @version 1.0
 * @date 19/11/2012
 * @author H2lsoft (contact@h2lsoft.com) - http://www.h2lsoft.com
 */

/* @var $plugin Plugin */
/* @var $nuts NutsCore */


// sql table
$plugin->formDBTable(array('NutsService'));

// fields
$plugin->formAddFieldText('Name', "", true);
$plugin->formAddFieldTextArea('Description', "", true, '', 'height:80px');

$help = "Tips ! youy can use get parameter in {_GET['pageID']}";
$plugin->formAddFieldTextArea('Query', "", true, 'sql', "height:200px", '', $help);

$help = "Use keyword `\$row` to manage current row (ex: `\$row['ID']`)";
$plugin->formAddFieldTextArea('HookData', "", false, 'php', "height:200px", '', $help);

$opts = array('JSON', 'ARRAY', 'XML');
$plugin->formAddFieldSelect('Output', "", true, $opts);




?>