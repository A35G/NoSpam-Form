NoSpam-Form
===========

A small class in PHP for the control of the sender and the message body before sending the contents of a contact form.

Based on my personal [Gist](https://gist.github.com/A35G/10366883 "Check Link and Filter text"), this class allows you to analyze the content of the text and e-mail address of the sender, before forwarding the contents of the form to the recipient.

There is the possibility to use the search within a database table or inside a CSV file.

### Usage

1. Edit **config.php** file in _lib folder_
  * Set **self_learning** as true (default **false**), if you want the script that _learns by e-mail marked as spam_
  * Set **use_file** as true (default **false**), if you want to use a file for _further research_
    * By default, path of file is "_lib/flt_data.csv_"
  * Set **use_db** as true (default **false**), if you want to use a database for _further research_
    * Valuing the variables for the database connection
  * The array "**$dspam**" contains the words that will be sought within the text
2. Include the file **filterm.class.php** content in it and _declares the class in the file that receives the form data
3. Use the "analyze" contained in the class, to control the content and the sender transmitted by contact form.

*For example:*
```php
$var = new NoSpamContent;
$var->analyze($content, $mail_sender, $name_sender);
```

If there are no errors, the message is clean and can be forwarded to the recipient.