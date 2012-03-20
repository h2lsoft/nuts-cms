<?php

$cf = array();

// you can only configure 3 filters for example
//$cf[] = array('name' => 'Type', 'type' => 'select', 'options' => array('PRESS RELEASES', 'PRESS CUTTINGS'), 'list_view'    => true, 'list_add_sql' => query);
//$cf[] = array('name' => 'Newsfront', 'type' => 'select', 'options' => array( 'NO', 'YES'), 'list_view'    => false);
//$cf[] = array('name' => 'Source', 'type' => 'text', 'list_view'    => false);


/** update 0.7 */
$hidden_fields = "Tags, Event, Comment, VirtualPageName, DateGMTExpiration"; # comma separated

/** update 0.87 **/
 $sql_front_added = ""; // added special sql code in news like DateGMT formater

/** update 1.8 **/

// parent parameters
$news_thumb_parent_constraint = false; // force image constraint
$news_thumb_parent_bkg_color = array(255,255,255); // bkg image color with constraint
$news_thumb_parent_max_size = '2Mo';
$news_thumb_parent_max_width = 950;
$news_thumb_parent_max_height = 800;

// thumb parameters
$news_thumb_list_view = true; // view thumb in the list
$news_thumb_constraint = false;
$news_thumb_bkg_color = array(255,255,255); // bkg image color with constraint
$news_thumb_width = 320;
$news_thumb_height = 80;

$include_plugin_css = true; // include bundle css dynamically


?>