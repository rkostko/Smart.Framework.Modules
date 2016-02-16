<?php

die('This is sample Disabled ! You can enable it manually by commenting out this line ...');

ini_set('display_errors', '0');	// display runtime errors
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); // error reporting
date_default_timezone_set('UTC');

	$year = date('Y');
	$month = date('m');

	echo json_encode(array(

		array(
			'id' => 111,
			'title' => "Event1",
			'start' => "$year-$month-10",
			'url' => "http://yahoo.com/"
		),

		array(
			'id' => 222,
			'title' => "Event2",
			'start' => "$year-$month-20",
			'end' => "$year-$month-22",
			'url' => "http://yahoo.com/"
		)

	));

?>
