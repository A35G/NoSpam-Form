<?php

	/**
	 * PRIMARY SETTINGS - IMPORTANT!
	 */

	//	Valorize with name of field for sender
	define('name_sndr', $_POST['field_sender']);
	//	Valorize with name of field for e-mail
	define('email_sndr', $_POST['field_mail']);
	// Valorize with name of field for text present into the form
	define('text_sndr', $_POST['field_text_sent']);

	/**
	 * END PRIMARY SETTINGS
	 */

	//	Set true, if you want the script that learns by e-mail marked as spam
	define('self_learning', false);

	//	Set true, if you want to use a file for further research
	define('use_file', false);

	//	Valorize only if var 'use_file' is set true
	define('path_file', dirname(__FILE__).'/flt_data.csv');

	//	Set true, if you want to use a database for further research
	define('use_db', false);

	//	Valorize only if var 'use_db' is set true
	define('hostname', 'localhost');
	define('username', 'test');
	define('password', '');
	define('database', 'test-nospam');

	//	List of words to be find in text to mark action as spam
	$dspam = array(
		"a href",
		"href",
		"[url",
		"[link",
		"www",
		"http",
		"@",
		"[at]",
		"mailto",
		"drug",
		"drugs",
		"Visa",
		"pay",
		" mg "
	);

	define('lstw', json_encode($dspam));

	//	Set true if you want to use remote check of data by form
	define('check_ext', true);