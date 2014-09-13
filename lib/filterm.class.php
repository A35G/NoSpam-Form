<?php

	if (file_exists(dirname(__FILE__).'/config.php'))
		include(dirname(__FILE__).'/config.php');

	class NoSpamContent {

		//	Options of script
		var $robot = self_learning;
		var $in_file = use_file;
		var $in_db = use_db;
		var $word_list = lstw;
		var $remote_check = check_ext;

		//	DB settings
		private $host = hostname;
		private $user = username;
		private $password = password;
		protected $databs = database;

		//	Path file for local data check
		private $wrdfile = path_file;

		//	Fields to check
		private $sender_m = name_sndr;
		private $mail_m = email_sndr;
		private $text_m = text_sndr;

		var $dbconn;

		private $point;

		function __construct() {

			$this->point = 0;
			$this->dbconn = '';

		}

		//	Connection and authentication to the host
		protected function connessione() {

			if (!$this->dbconn = @mysql_connect($this->host, $this->user, $this->password))
				die("Error connecting to the host indicated.");
			else
				$this->load_db($this->databs);

		}

		protected function load_db($database) {

			if (!$this->dbloading = mysql_select_db($database, $this->dbconn))
				die("There was a problem connecting to the database &quot;".$database."&quot;");

			mysql_query("SET NAMES 'UTF-8';");

		}

		protected function getQuery($query) {

			if (isset($query) && !empty($query)) {

				$sqr = explode(' ', strtolower($query));

				if ($sqr[0] == 'select') {

					$res = mysql_query($query, $this->dbconn) or die("Query error: ".mysql_error());
					if (mysql_num_rows($res)) {

						while ($row = mysql_fetch_assoc($res))
							$result[] = $row;

					}

					return $result;

				} else {

					mysql_query($query, $this->dbconn) or die("Query error: ".mysql_error());

				}

			}

		}

		private function checkExtLink() {

			$dspam = json_decode($this->word_list);

			if (is_array($dspam) && !empty($dspam)) {

				foreach ($dspam as $tofilter) {

					$offset = $pos = 0;

					while (is_integer($pos)) {

						$pos = strpos($this->text_m, $tofilter, $offset);

						if (is_integer($pos)) {

							$arrPos[] = $pos;
							$offset = ($pos + strlen($tofilter));

						}

					}

					if (isset($arrPos))
						$this->point++;

				}

			}

		}

		public function mres($field) {
			return mysql_real_escape_string($field);
		}

		private function checkDB() {

			$dbqr = array();

			if (isset($this->mail_m) && !empty($this->mail_m))
				$dbqr[] = "mail_spam = '".$this->mres($this->mail_m)."'";

			if (isset($this->sender_m) && !empty($this->sender_m))
				$dbqr[] = "from_spam = '".$this->mres($this->sender_m)."'";

			if (!empty($dbqr)) {

				$str_query = implode(" OR ", $dbqr);

				$this->connessione();

				$srcm = $this->getQuery("SELECT id_spam FROM `flt_data` WHERE '".$str_query."' LIMIT 1;");
				if (!empty($srcm))
					$this->point++;

			}

		}

		private function checkFile() {

			if (file_exists($this->wrdfile)) {

				//	Set auto_detect_line_endings to deal with Mac line endings
				ini_set('auto_detect_line_endings', TRUE);

				try {

		    	$csv = new SplFileObject($this->wrdfile, 'r');

				} catch (RuntimeException $e ) {

		    	printf("Error openning csv: %s\n", $e->getMessage());

				}

				while(!$csv->eof() && ($row = $csv->fgetcsv()) && $row[0] !== null) {

					if (isset($this->mail_m) && !empty($this->mail_m) && ($row[1] == $this->mail_m))
						$this->point++;

					if (isset($this->sender_m) && !empty($this->sender_m) && ($row[0] == $this->sender_m))
						$this->point++;

				}

			}

		}

		private function saveSpam() {

			if (isset($this->in_db) && !empty($this->in_db) && ($this->in_db)) {

				$this->connessione();

				$this->getQuery("INSERT INTO `flt_data`(from_spam,mail_spam) VALUES('".$this->mres($this->sender_m)."','".$this->mres($this->mail_m)."');");

			}

			if (isset($this->in_file) && !empty($this->in_file) && ($this->in_file)) {

				if (file_exists($this->wrdfile)) {

					try {

			    	$ncsv = new SplFileObject($this->wrdfile, 'a+');

					} catch (RuntimeException $e) {

			    	printf("Error openning csv: %s\n", $e->getMessage());

					}

					$new_spam[0] = $this->sender_m;
					$new_spam[1] = $this->mail_m;

					$data_file = $ncsv->fgets();

					$antep = (($data_file !== FALSE) && !empty($data_file)) ? "\r\n" : "";

					if (method_exists($ncsv, 'fputcsv'))
						$ncsv->fputcsv ($new_spam);
					else
						$ncsv->fwrite($antep.implode(',', $new_spam));

				}

			}

		}

		public function checkMail() {
			$err_m = 0;

			if (!filter_var($this->mail_m, FILTER_VALIDATE_EMAIL) && !preg_match('/@.+\./', $this->mail_m))
				$err_m++;

			return (empty($err_m)) ? TRUE : FALSE;
		}

		public function errList($errcode) {

			if (!empty($errcode)) {

				switch($errcode) {
					case '#123':
						$phcode = "E-mail address isn't valid!";
					break;
					case '#ispam':
						$phcode = "Possible spam into content of this e-mail or the sender is present in the list of spam addresses.";
					break;
					case '#clean':
						$phcode = '';	//	Clean from SPAM
					break;
				}

			} else {
				$phcode = "Unable to control the information indicated.";
			}

			return $phcode;

		}

		public function remoteCheck() {

			$codes = '';

			//	Use remote check? GO!
			if (isset($this->remote_check) && !empty($this->remote_check) && ($this->remote_check)) {

				//	Get the domain that is making the request
				$domain_s = (!empty($_SERVER['HTTP_HOST'])) ? htmlentities($_SERVER['HTTP_HOST']) : htmlentities($_SERVER['SERVER_NAME']);

				if (empty($domain_s))
					$domain_s = "Not reached";

				//	IP of sender
				$ip_addr = htmlentities($_SERVER['REMOTE_ADDR']);

				// Field of Form
				$sender_frm = htmlentities($this->sender_m, ENT_QUOTES, 'UTF-8');
				$mail_sender = htmlentities($this->mail_m);
				$text_frm = htmlentities($this->text_m, ENT_QUOTES, 'UTF-8');

				$data_check = array(
					"domain" => urlencode($domain_s),
					"ip_address" => urlencode($ip_addr),
					"name_form" => urlencode($sender_frm),
					"mail_form" => urlencode($mail_sender),
					"text_form" => urlencode($text_frm)
				);

				if (!function_exists('curl_init'))
					die('Sorry cURL is not installed!');

				$data_cform = json_encode($data_check);

				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'http://gmcode.it/no-spam/api/',
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => "check_args=".$data_cform,
					CURLOPT_CONNECTTIMEOUT => 10,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_USERAGENT => 'Is SPAM? Check it - https://github.com/A35G/NoSpam-Form',
					CURLOPT_SSL_VERIFYPEER => false
				));

				$result = curl_exec($curl);

				if (!$result)
					die('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
				else
					$codes = $result;

				curl_close($curl);

			}

			return $this->errList($codes);

		}

		public function analyze() {

			if (!empty($this->text_m))
				$this->checkExtLink();

			if (!empty($this->mail_m) && $this->checkMail()) {

				//	if lookup is enabled in the database, proceed to connect to the database and the new research
				if (isset($this->in_db) && !empty($this->in_db) && ($this->in_db))
					$this->checkDB();

				//	if lookup is enabled in the file, proceed to open file and the new research
				if (isset($this->in_file) && !empty($this->in_file) && ($this->in_file))
					$this->checkFile();

			} else {
				$codes = '#123';
			}

			if (!empty($this->point)) {

				$codes = '#ispam';

				//	if the self-learning is enabled and the email is marked as spam, save into database or into file, the sender and e-mail address
				if (isset($this->robot) && !empty($this->robot) && ($this->robot))
					$this->saveSpam();

			} else {
				$codes = '#clean';
			}

			return $this->errList($codes);

		}

	}