<?php

define('F3SKEL', 'true');

if (!file_exists('vendor/autoload.php')) {
	define('INSTALL', 'true');
	include 'install.php';
}else{
	require 'vendor/autoload.php';
	
	$f3 = \F3::instance();
	
	$f3->config('config/config.ini');
	/* */
	foreach(glob("config-enabled/*.ini") as $filename){
		$f3->config($filename);
	}
	/* */
	
	$f3->config('config/routes.ini');
	foreach(glob("routes-enabled/*.ini") as $filename){
		$f3->config($filename);
	}
	
	$f3->run();
}
