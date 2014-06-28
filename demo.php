<?php

	/**
	 * NoSpam Form - Analyze and filter spam by Contacts Form
	 * -------------------------------------------------------
	 * vers. 0.0.135 - June, 2014 - last rev. 27/06/2014
	 * -------------------------------------------------------
	 * Project Page: https://github.com/A35G/NoSpam-Form
	 * -------------------------------------------------------
	 * © 2014 - Gianluigi 'A35G' - http://www.gmcode.it/
	 * http://www.hackworld.it/ - a35g@hackworld.it
	 * -------------------------------------------------------
	 */

	$resp_form = $response = '';

	if (isset($_POST['submit'])) {

		if (file_exists(dirname(__FILE__).'/lib/filterm.class.php'))
			include(dirname(__FILE__).'/lib/filterm.class.php');

		$sender_frm = htmlentities($_POST['frmCont'], ENT_QUOTES, 'UTF-8');
		$mail_sender = htmlentities($_POST['frmSendMail']);
		$text_frm = htmlentities($_POST['frmText'], ENT_QUOTES, 'UTF-8');

		$anlz = new NoSpamContent;

		$check_spam = $anlz->analyze($text_frm);
		//$check_spam = $anlz->analyze($text_frm, $mail_sender);
		//$check_spam = $anlz->analyze($text_frm, $mail_sender, $sender_frm);

		if (!empty($check_spam)) {

			$resp_form = ($check_spam != 123) ? "Possible spam into content of this e-mail or<br />the sender is present in the list of spam addresses" : "E-mail address isn't valid!";


		} else {

			$resp_form = "This e-mail is clean from spam";

		}

		$response = (!empty($check_spam)) ? 'red' : 'green';

	}

	if (file_exists('./tpl/index.php'))
		include('./tpl/index.php');

		echo $tpl_demo;