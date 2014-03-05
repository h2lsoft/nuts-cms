<?php

spl_autoload_register(function ($class) {
	include WEBSITE_PATH.'/x_includes/orm/'.$class.'.db.class.php';
});

