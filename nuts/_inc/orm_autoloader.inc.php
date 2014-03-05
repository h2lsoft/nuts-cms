<?php

function orm_autoloader($class) {
	include WEBSITE_PATH.'/x_includes/orm/'.$class.'.db.class.php';
}
spl_autoload_register('orm_autoloader');