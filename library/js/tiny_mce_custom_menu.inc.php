<?php

/**
 * Add your custom menu here
 *
 * Code example :

 $nuts->DoQuery("SELECT ID, Name FROM MyTable WHERE Deleted = 'NO' AND Active = 'YES'");
 if($nuts->dbNumrows() == 0)return;
 echo 'sub = m.addMenu({title : "Nuts custom"});';
 while($row = $nuts->dbFetch())
 {
	$ID = $row['ID'];
    $name = $row['Name'];
    $code = sprintf("{@NUTS	TYPE='PLUGIN'	NAME=''	PARAMETERS='%s,%s'}", $ID, $name);

    echo 'sub.add({title : "'.$name.'", onclick : function() {
						tinyMCE.activeEditor.execCommand("mceInsertContent", false, "'.$code.'");
	}});';
 }
*/



