<?php

if (!file_exists('vendor/autoload.php')) {
	
    if (substr(php_uname(), 0, 7) == "Windows"){ 
        $os = 'WIN';  
		$php = 'C:\xampp\php\php';
    } 
    else { 
        $os = 'LNX';   
		$php = 'php';
    } 
	
	//header("Content-type: text/html");
	//
	// Run composer with a PHP script in browser
	//
	// http://stackoverflow.com/questions/17219436/run-composer-with-a-php-script-in-browser
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	// 600 seconds = 10 minutes
	set_time_limit(600);
	ini_set('max_execution_time', 600);
	
	// https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors
	ini_set('memory_limit', '-1');
	
	// Download composer
	$composerPhar = __DIR__ . '/vendor/composer/composer.phar';

	if (!file_exists($composerPhar)) {
		if (!file_exists(__DIR__ . '/vendor')) { mkdir(__DIR__ . '/vendor'); }
		if (!file_exists(__DIR__ . '/vendor/composer')) { mkdir(__DIR__ . '/vendor/composer'); }
		
		$fp = fopen($composerPhar, 'w+') or die('Unable to write a file');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($ch, CURLOPT_URL, 'https://getcomposer.org/composer.phar');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		$result = curl_exec($ch);

		//If there was an error, throw an Exception
		if(curl_errno($ch)){
			throw new Exception(curl_error($ch));
		}		

 
		//Get the HTTP status code.
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		 
		//Close the cURL handler.
		curl_close($ch);
		 
		if($statusCode == 200){
			echo 'Downloaded!';
		} else{
			echo "Status Code: " . $statusCode;
		}

		fclose($fp);
	}
	
	if (file_exists($composerPhar)) {
		
		//include $composerPhar;
		
		echo system($php . ' ' . $composerPhar . " install");
	}else{
		echo $composerPhar . ' does not exist';
	}
	
	if (file_exists('vendor/autoload.php')) {
		require 'vendor/autoload.php';
		
		echo "installed ...";
	}
}else{
	require 'vendor/autoload.php';
}

	$myFS = new \FAL\LocalFS('./'); 
	
	$dirs = array(
		'app',
		'app/controllers',
		'app/models',
		'config',
		'config/conf-available',
		'config/conf-enabled',
		'config/routes-available',
		'config/routes-enabled',
		'ui',
		'themes'
	);
	
	foreach ($dirs as $dir){
		if (!$myFS->isDir( $dir )) { 
			$myFS->createDir( $dir ); 
		}
	}

	
	
