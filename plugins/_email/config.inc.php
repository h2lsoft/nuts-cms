<?php

// default template put [BODY] inside or let it empty
$HTML_TEMPLATE = '
<style type="text/css">
body, td {font-family:arial; font-size:12px; margin-top:0;}
blockquote {border: 1px solid #ccc; background-color: #e5e5e5;  padding: 5px; margin-left: 0; margin-bottom: 0;}
blockquote .author {font-weight: bold;}
img {border:0;}
a {color:blue;}
a:hover {color:red;}
#header {background-color:black; border-bottom:5px solid #D852AD;}
</style>

<div id="header">
<a href="'.WEBSITE_URL.'"><img src="'.WEBSITE_URL.'/nuts/img/logo.png" /></a>
</div>
<br />
<br />

[BODY]';



/** update 0.87 **/
 $email_group = array(''); // email group

?>