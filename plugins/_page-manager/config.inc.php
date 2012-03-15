<?php

$custom_fields = array(); # do not touch this line

/* custom special fields name, type (text, select, date, datetime, textarea, image, file, colorpicker), help (tooltip)

  example for text:
  
  $custom_fields[] = array(
             'name' => 'Test', 
             'type' => 'text',
             'help' => 'test 1!'
             );

  example for select:  
  
  $custom_fields[] = array(
             'name' => 'Test2', 
             'type' => 'select',
             'options' => array('1', '2', '3'),
              'help' => 'test 2 !'
             );  
*/

// create your own here ****************************************************************
 
// end of your own code ****************************************************************




/** update 0.7 */
$hidden_fields = "ContentType, TopBar, BottomBar, Tags, ContentResume, fieldset_Media"; # comma separated

/** update 0.87 **/
$allowed_groups_block = array("Right");

/** update 0.92 **/
$javascript_onsubmit_function = ""; // name of the js function to verify custom value

?>