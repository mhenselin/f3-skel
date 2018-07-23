<?php

function output ($outtext) {
	echo $outtext . "<br />\n";
	flush();
    ob_flush();
}

if (defined('F3SKEL') && defined('INSTALL')){
	header( 'Content-type: text/html; charset=utf-8' );
	output("running installer ...");
	
    if (substr(php_uname(), 0, 7) == "Windows"){ 
        $os = 'WIN';  
		$php = 'C:\xampp\php\php';
    } 
    else { 
        $os = 'LNX';   
		$php = 'php';
    } 

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
			output(' composer downloaded.');
		} else{
			output("Status Code: " . $statusCode);
		}

		fclose($fp);
	}

	output("downloading and installing requirements ...");
	
	if (file_exists($composerPhar)) {
		output(system($php . ' ' . $composerPhar . " install"));
	}else{
		output($composerPhar . ' does not exist!');
	}
	
	if (file_exists('vendor/autoload.php')) {
		require 'vendor/autoload.php';
		
		output("requirements installed ...");
	}
	
	output("creating directories ...");
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
	
	output('Generating standard config and routes ...');
	$myFS->write( 'config/config.ini', "[globals]\nAUTOLOAD=app/controllers/|app/models/\nUI=ui/\ndb_dns=mysql:host=your_database_host_name;port=3306;dbname=" );
	$myFS->write( 'config/routes.ini', "[routes]\nGET /=MainController->start" );
	
	output('Creating MainController ...');
	$myFS->write( 'app/controllers/Controller.php', gzdecode(base64_decode("H4sIAAAAAAACCm2SUUvDMBSFn5tfcRWhLTgnDHxo7US3PQhznbOCD0JJ21s76JKaZMgY/e/ezDqoLg95+M495yY3ub1rqoaxvOZaw0QKo2Rdo4I9Awa0GiUN5gYLuChH4V9UZKHDnAMttyI3aykgw1IqVHJr0PP3zHGGQ0jiafxT1tLer+elQdWVwx7IgHklIcFNU3ODQbAW2nCRkzwYKxQFKs+t+Y4cV5XZuH54TO4Hp2kuyaq2ubHJnWwXXSV64LqXTXej1usSPFIH4w80nltkaSG068NZBOebnf6sg0pqE1FvlRbc8IxCUotSwTcYNlKZaDS6vgmLzILo3PZ1HIcGFQn8gunD+8vz3LPIOdHmCnrQRrj+5f/qrUZ1UmjoFX8FrhTfefC+nMZBcJ8kq3S2Wj3F0xlE4452IJ29TWbL5DFegG+tNAvaD+c21VoPxnR6+9REWqw17nsSRLB4nc8PKjvYOomGfPwzLWvZN86KrKFrAgAA")) );
	$myFS->write( 'app/controllers/MainController.php', gzdecode(base64_decode("H4sIAAAAAAACCnXLMQ+CMBCG4f1+xQ0mhURcHDU4ODM7mloO26S0DXeICeG/W3HRwXe8+57jKdkEYLxmxka7cI5Bhug9DUhPodAyfp1mQMBcGm/eGezGYMTFgCx6kKKc1+e7jVjHVd3tq5pJCpX0na6WdKu26kLexJ5UecCf/uKHoym76eN2Vvps1/UCsMALO/hJwMQAAAA=")) );
	
	output('Generating basic templates ...');
	//header.htm
	$myFS->write( 'ui/header.htm', gzdecode(base64_decode("H4sIAAAAAAACCmVRwUoDMRQ8V/AfXnMpFNMiXgQ30rUt2ENRbAt6THdfN9FNsiZvW7T0z7z5Y2ZLkUVzybxkZsJMku7kYbx8eZyCIlPenp8lzQ6ltIVgObJ4AnElCmUecaeTGCQJmZI+IAlW04Zfs9aNIqo4vtd6K9gzX6V87EwlSa9LZJA5S2ijbDYVmBfYFlppULCtxl3lPLW4O52TEjludYb8OFyAtpq0LHnIZIni8uTT5RwmGuEK5tGRL2URwK3RQt98f4WAtg+fNfpAoA00iSAQKrQ38IpFqTOFHoKzgXQRUUPgM6tkSWDqEKBvZab6kGuMVnB0f3PGRMz5qadjV2sZEJTHjWD7PYzu0sV00Bv2BqPVDA4HBsNI/ps7x5B5XZF2thX9fz+yJuV8izJf3MMIlulTujiyk+Hpqxq8dvnH72NSW/Au1sUaGC1KGYJgjVOc0TfyH7ezh4wPAgAA")) );
		
	//footer.htm
	$myFS->write( 'ui/footer.htm', gzdecode(base64_decode("H4sIAAAAAAACCjWOOw7CMBBEa0fKHSxXUIAvsFiUNKk4gWMviiV/kL0gcnv8Ubaa0dPsDGMgg3ZRzdM8MQavlAgzN16XchPDCcUbY2Dd9yAmRaqxBjtjUN46HpTwR5fwIbRCmYy6Cr7ufHk++CnobNDfN4wFvYvXiHQG2dLjE8ha0yXI0d+38Xog12T3akFuFHwVf8QEKfy/AAAA")) );

	//welcome.htm
	$myFS->write( 'ui/welcome.htm', gzdecode(base64_decode("H4sIAAAAAAACCrPJMLQLz8zJyc7PzU3NU7TRB/IBRKLL0hQAAAA=")) );
	
	//layout.htm
	$myFS->write( 'ui/layout.htm', gzdecode(base64_decode("H4sIAAAAAAACCrPJzEvOKU1JVcgoSk2zVcpITUxJLdLLKMlVUtC347JBla2uVnAoy0wtV6itxSadlp9fgtAMAJ9m029aAAAA")) );
	
}else{
	require 'vendor/autoload.php';
}

output('ALL done!');

sleep(15);

$f3=\F3::instance();
	$f3->config('config/config.ini');
	$f3->config('config/routes.ini');
$f3->run();
