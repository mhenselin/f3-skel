<?php

define('F3SKEL', 'true');

if (!file_exists('vendor/autoload.php')) {
	define('INSTALL', 'true');
	include 'install.php';
}else{
	require 'vendor/autoload.php';
	
	$f3 = \F3::instance();
	
	$f3->config('config/config.ini');

	foreach(glob("config/conf-enabled/*.ini") as $filename){
		$f3->config($filename);
	}
	
	$f3->config('config/routes.ini');
	foreach(glob("config/routes-enabled/*.ini") as $filename){
		$f3->config($filename);
	}
	
	Multilang::instance();
	
	$f3->set('ONREROUTE',function($url,$permanent) use($f3,$ml){
		$f3->clear('ONREROUTE');
		//TODO log the old routing !!!
		Multilang::instance()->reroute($url,$permanent);
	});
	
	//current language -> Multilang::instance()->current;
	//primary language -> Multilang::instance()->primary;
	//autodetected language -> Multilang::instance()->auto;
	
	$f3->run();
}

