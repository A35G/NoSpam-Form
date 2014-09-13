NoSpam-Form
===========

A small class in PHP for the control of the data before sending the contents of contact form at the recipient.

Based on my personal [Gist](https://gist.github.com/A35G/10366883 "Check Link and Filter text"), this class allows you to analyze content, the name sender and e-mail address of the sender, before forwarding the contents of the form to the recipient.

Depending on the configuration you choose, you can search for any trace of spam through a wordlist, a csv file, a mysql database or via cURL request to a script on my website.

The class, in case of option enabled, allows the self-learning with traces of spam detected.

Features
===============

-	**Completed**:
	- [x] Search in a wordlist;
	- [x] Search in a CSV file;
	- [x] Search in a MySQL database;
	- [x] Remote search for spam (_beta_);
	- [x] Self-learning by results of search;

- **To complete**:
	- [	] Save URL contained within the text sent via form;
	- [ ] Save IP address of sender;
	- [ ] Save the date, type of e-mail sent and other useful information for analysis and statistics;
	- [ ] Range of tolerance customizable on the score of the controls (currently, it is only 0 or 1);

Disclaimer
===============

Using the remote control of information, will be forwarded along with information on the form below (e-mail address of the sender, the sender's name, the text written by the sender), also the site URL and the IP address of the sender.

From data sent through this script, only those marked as spam are stored and only for statistical purposes and preventive in case of infection.

The list will be updated daily with information automatically by the main script.

The owners of the domains involved, they can contact me to request removal of their information.

Usage
===============

1. Edit **config.php** file in _lib folder_
  * Set **self_learning** as true (default **false**), if you want the script that _learns by e-mail marked as spam_
  * Set **use_file** as true (default **false**), if you want to use a file for _further research_
    * By default, path of file is "_lib/flt_data.csv_"
  * Set **use_db** as true (default **false**), if you want to use a database for _further research_
    * Valuing the variables for the database connection
  * The array "**$dspam**" contains the words that will be sought within the text
2. Include the file **filterm.class.php** content in it and _declares_ the class in the file that receives the form data
3. Use the "analyze" contained in the class, to control the content and the sender transmitted by contact form.

*For example:*
```php
	if (file_exists(dirname(__FILE__).'/lib/filterm.class.php'))
		include(dirname(__FILE__).'/lib/filterm.class.php');

	$anlz = new NoSpamContent;
	echo $anlz->analyze();
```

*or*

```php
	if (file_exists(dirname(__FILE__).'/lib/filterm.class.php'))
		include(dirname(__FILE__).'/lib/filterm.class.php');

	$anlz = new NoSpamContent;
	echo $anlz->remoteCheck();
```

The script will return the status of the checks performed on the data sent.

### Notice

The remote control of the information is still in the process of building and testing.