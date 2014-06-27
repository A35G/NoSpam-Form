<?php

	if (file_exists(dirname(__FILE__).'/lib/filterm.class.php'))
		include(dirname(__FILE__).'/lib/filterm.class.php');

	$res_text = "Lorem ipsum dolor";

	$anlz = new NoSpamContent;
	//$check_spam = $anlz->analyze($res_text, 'lifestile@msn.com');
	$check_spam = $anlz->analyze($res_text);

	if (!empty($check_spam))
		echo "Possible spam into content of this e-mail or the sender is present in the list of spam addresses";
	else
		echo "This e-mail is clean from spam";