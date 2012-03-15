<?php

/* @var $plugin Plugin */
/* @var $nuts NutsCore */

// assign table to db
$plugin->listSetDbTable('NutsZone');

// create fields
$plugin->listAddCol('ID', '', 'center; width:30px', true); // with order by
$plugin->listAddCol('Name', $lang_msg[2], '', true);
$plugin->listAddCol('CssName', $lang_msg[3]);
$plugin->listAddCol('Description', $lang_msg[4]);
$plugin->listAddColImg('Navbar', $lang_msg[7], '', true);

// render list
$plugin->listRender(20);



?>