<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'opheme30',
			'username'  => 'opheme30_backend',
			'password'  => 'backend1357!',
			'charset'   => 'utf8',
			'collation' => 'utf8_general_ci',
			'prefix'    => '',
		),
		'mongodb' => array(
			'driver'   => 'mongodb',
			'host'     => 'localhost',
			'port'     => 27017,
//			'username' => 'opheme30_api',
//			'password' => 'opheme30_api',
			'database' => 'messages'
		),
	),
);
