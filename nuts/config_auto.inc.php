<?php

// nuts
if(!defined('NUTS_PATH'))define('NUTS_PATH', WEBSITE_PATH.'/nuts');
if(!defined('NUTS_URL'))define('NUTS_URL', WEBSITE_URL.'/nuts');

// library
if(!defined('NUTS_LIBRARY_PATH'))define('NUTS_LIBRARY_PATH', WEBSITE_PATH.'/library');
if(!defined('NUTS_LIBRARY_URL'))define('NUTS_LIBRARY_URL', '/library');

// app
if(!defined('NUTS_APPS_PATH'))define('NUTS_APPS_PATH', WEBSITE_PATH.'/app');
if(!defined('NUTS_APPS_URL'))define('NUTS_APPS_URL', '/app');


// uploads
if(!defined('NUTS_UPLOADS_PATH'))define('NUTS_UPLOADS_PATH', WEBSITE_PATH.'/uploads');
if(!defined('NUTS_UPLOADS_URL'))define('NUTS_UPLOADS_URL', '/uploads');


// plugins
if(!defined('NUTS_PLUGINS_PATH'))define('NUTS_PLUGINS_PATH', WEBSITE_PATH.'/plugins');
if(!defined('NUTS_PLUGINS_URL'))define('NUTS_PLUGINS_URL', '/plugins');


// themes
if(!defined('NUTS_THEMES_PATH'))define('NUTS_THEMES_PATH', WEBSITE_PATH.'/themes');
if(!defined('NUTS_THEMES_URL'))define('NUTS_THEMES_URL', '/themes');

// php
if(!defined('NUTS_PHP_PATH'))define('NUTS_PHP_PATH', NUTS_LIBRARY_PATH.'/php');

// js
if(!defined('NUTS_JS_URL'))define('NUTS_JS_URL', NUTS_LIBRARY_URL.'/js');

// media
if(!defined('NUTS_MEDIA_PATH'))define('NUTS_MEDIA_PATH', NUTS_LIBRARY_PATH.'/media');
if(!defined('NUTS_MEDIA_URL'))define('NUTS_MEDIA_URL', NUTS_LIBRARY_URL.'/media');

// images
if(!defined('NUTS_IMAGES_PATH'))define('NUTS_IMAGES_PATH', NUTS_MEDIA_PATH.'/images');
if(!defined('NUTS_IMAGES_URL'))define('NUTS_IMAGES_URL', NUTS_MEDIA_URL.'/images');

if(!defined('NUTS_HEADER_IMAGES_PATH'))define('NUTS_HEADER_IMAGES_PATH', NUTS_IMAGES_PATH.'/user/header');
if(!defined('NUTS_HEADER_IMAGES_URL'))define('NUTS_HEADER_IMAGES_URL', NUTS_IMAGES_URL.'/user/header');

// gallery images
if(!defined('NUTS_GALLERY_PATH'))define('NUTS_GALLERY_PATH', NUTS_IMAGES_PATH.'/gallery');
if(!defined('NUTS_GALLERY_URL'))define('NUTS_GALLERY_URL', NUTS_IMAGES_URL.'/gallery');

if(!defined('NUTS_GALLERY_IMAGES_PATH'))define('NUTS_GALLERY_IMAGES_PATH', NUTS_IMAGES_PATH.'/gallery_images');
if(!defined('NUTS_GALLERY_IMAGES_URL'))define('NUTS_GALLERY_IMAGES_URL', NUTS_IMAGES_URL.'/gallery_images');

if(!defined('NUTS_GALLERY_IMAGES_HD_PATH'))define('NUTS_GALLERY_IMAGES_HD_PATH', NUTS_IMAGES_PATH.'/gallery_images_hd');
if(!defined('NUTS_GALLERY_IMAGES_HD_URL'))define('NUTS_GALLERY_IMAGES_HD_URL', NUTS_IMAGES_URL.'/gallery_images_hd');

// news images
if(!defined('NUTS_NEWS_IMAGES_PATH'))define('NUTS_NEWS_IMAGES_PATH', NUTS_IMAGES_PATH.'/news');
if(!defined('NUTS_NEWS_IMAGES_URL'))define('NUTS_NEWS_IMAGES_URL', NUTS_IMAGES_URL.'/news');

// thumbnail
if(!defined('NUTS_PAGE_THUMBNAIL_PATH'))define('NUTS_PAGE_THUMBNAIL_PATH', NUTS_IMAGES_PATH.'/page');
if(!defined('NUTS_PAGE_THUMBNAIL_URL'))define('NUTS_PAGE_THUMBNAIL_URL', NUTS_IMAGES_URL.'/page');


$nutsBackLogo = (BACKOFFICE_LOGO_URL == '') ? 'img/logo.png' : BACKOFFICE_LOGO_URL;

// customised chars
if(!defined('CR'))define("CR", "\n");
if(!defined('TAB'))define("TAB", "\t");


