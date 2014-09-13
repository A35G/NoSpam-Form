<?php

	/**
	 * NoSpam Form - Analyze and filter spam by Contacts Form
	 * -------------------------------------------------------
	 * vers. 0.0.135 - June, 2014 - last rev. 12/09/2014
	 * -------------------------------------------------------
	 * Project Page: https://github.com/A35G/NoSpam-Form
	 * -------------------------------------------------------
	 * © 2014 - Gianluigi 'A35G' - http://www.gmcode.it/
	 * http://www.hackworld.it/ - a35g@hackworld.it
	 * -------------------------------------------------------
	 */

	if (file_exists(dirname(__FILE__).'/lib/filterm.class.php'))
		include(dirname(__FILE__).'/lib/filterm.class.php');

	$anlz = new NoSpamContent;
	echo $anlz->analyze();