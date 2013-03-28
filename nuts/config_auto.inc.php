<?php

// nuts
define('NUTS_PATH', WEBSITE_PATH.'/nuts');
define('NUTS_URL', WEBSITE_URL.'/nuts');

// library
define('NUTS_LIBRARY_PATH', WEBSITE_PATH.'/library');
define('NUTS_LIBRARY_URL', '/library');

// app
define('NUTS_APPS_PATH', WEBSITE_PATH.'/app');
define('NUTS_APPS_URL', '/app');


// uploads
define('NUTS_UPLOADS_PATH', WEBSITE_PATH.'/uploads');
define('NUTS_UPLOADS_URL', '/uploads');


// plugins
define('NUTS_PLUGINS_PATH', WEBSITE_PATH.'/plugins');
define('NUTS_PLUGINS_URL', '/plugins');


// themes
define('NUTS_THEMES_PATH', WEBSITE_PATH.'/themes');
define('NUTS_THEMES_URL', '/themes');

// php
define('NUTS_PHP_PATH', NUTS_LIBRARY_PATH.'/php');

// js
define('NUTS_JS_URL', NUTS_LIBRARY_URL.'/js');

// media
define('NUTS_MEDIA_PATH', NUTS_LIBRARY_PATH.'/media');
define('NUTS_MEDIA_URL', NUTS_LIBRARY_URL.'/media');

// images
define('NUTS_IMAGES_PATH', NUTS_MEDIA_PATH.'/images');
define('NUTS_IMAGES_URL', NUTS_MEDIA_URL.'/images');

define('NUTS_HEADER_IMAGES_PATH', NUTS_IMAGES_PATH.'/user/header');
define('NUTS_HEADER_IMAGES_URL', NUTS_IMAGES_URL.'/user/header');

// gallery images
define('NUTS_GALLERY_PATH', NUTS_IMAGES_PATH.'/gallery');
define('NUTS_GALLERY_URL', NUTS_IMAGES_URL.'/gallery');

define('NUTS_GALLERY_IMAGES_PATH', NUTS_IMAGES_PATH.'/gallery_images');
define('NUTS_GALLERY_IMAGES_URL', NUTS_IMAGES_URL.'/gallery_images');

define('NUTS_GALLERY_IMAGES_HD_PATH', NUTS_IMAGES_PATH.'/gallery_images_hd');
define('NUTS_GALLERY_IMAGES_HD_URL', NUTS_IMAGES_URL.'/gallery_images_hd');

// news images
define('NUTS_NEWS_IMAGES_PATH', NUTS_IMAGES_PATH.'/news');
define('NUTS_NEWS_IMAGES_URL', NUTS_IMAGES_URL.'/news');



$nutsBackLogo = (BACKOFFICE_LOGO_URL == '') ? 'img/logo.png' : BACKOFFICE_LOGO_URL;

// customised chars
if(!defined('CR'))define("CR", "\n");
if(!defined('TAB'))define("TAB", "\t");



?>