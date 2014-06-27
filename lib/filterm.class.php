<?php

	if (file_exists(dirname(__FILE__).'/config.php'))
		include(dirname(__FILE__).'/config.php');

	class NoSpamContent {

		var $robot = self_learning;
		var $in_file = use_file;
		var $in_db = use_db;
		var $word_list = lstw;

		private $host = hostname;
		private $user = username;
		private $password = password;
		protected $databs = database;
		private $wrdfile = path_file;

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
				die("There was a problem connecting to the database &quot;" . $database . "&quot;");

			mysql_query("SET NAMES 'UTF-8';");

		}

		protected function getQuery($query) {

			if (isset($query) && !empty($query)) {

				$sqr = explode(' ', strtolower($query));

				if ($sqr[0] == 'select') {

					$res = mysql_query($query, $this->dbconn) or die("Query error: " . mysql_error());
					if (mysql_num_rows($res)) {

						while ($row = mysql_fetch_assoc($res))
							$result[] = $row;

					}

					return $result;

				} else {

					mysql_query($query, $this->dbconn) or die("Query error: " . mysql_error());

				}

			}

		}

		private function checkExtLink($text) {

			$dspam = json_decode($this->word_list);

			if (is_array($dspam) && !empty($dspam)) {

				foreach ($dspam as $tofilter) {

					$offset = $pos = 0;

					while (is_integer($pos)) {

						$pos = strpos($text, $tofilter, $offset);

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

		private function checkDB($mailm='', $mittm='') {

			if (isset($mailm) && !empty($mailm)) {

				$this->connessione();

				$srcm = $this->getQuery("SELECT id_spam FROM `flt_data` WHERE mail_spam = '".$this->mres($mailm)."' LIMIT 1;");
				if (!empty($srcm))
					$this->point++;

			}

		}

		private function checkFile($mailm='', $mittm='') {

			if (file_exists($this->wrdfile)) {

				if (isset($mailm) && !empty($mailm)) {

					//	Set auto_detect_line_endings to deal with Mac line endings
					ini_set('auto_detect_line_endings', TRUE);

					try {

		    		$csv = new SplFileObject($this->wrdfile, 'r');

					} catch (RuntimeException $e ) {

		    		printf("Error openning csv: %s\n", $e->getMessage());

					}

					while(!$csv->eof() && ($row = $csv->fgetcsv()) && $row[0] !== null) {

						if ($row[1] == $mailm)
							$this->point++;

					}

				}

			}

		}

		private function saveSpam($mailm='', $mittm='') {

			if (!empty($mailm)) {

				if (isset($this->in_db) && !empty($this->in_db) && ($this->in_db)) {

					$this->connessione();

					$this->getQuery("INSERT INTO `flt_data`(from_spam,mail_spam) VALUES('".$this->mres($mittm)."','".$this->mres($mailm)."');");

				}

				if (isset($this->in_file) && !empty($this->in_file) && ($this->in_file)) {

					if (file_exists($this->wrdfile)) {

						try {

			    		$ncsv = new SplFileObject($this->wrdfile, 'a+');

						} catch (RuntimeException $e ) {

			    		printf("Error openning csv: %s\n", $e->getMessage());

						}

						$new_spam[0] = $mittm;
						$new_spam[1] = $mailm;

						$data_file = $ncsv->fgets();

						if (($data_file !== FALSE) && !empty($data_file))
							$antep = "\r\n";
						else
							$antep = '';

						if (method_exists($ncsv, 'fputcsv'))
							$ncsv->fputcsv ($new_spam);
						else
							$ncsv->fwrite($antep.implode(',', $new_spam));

					}

				}

			}

		}

		public function analyze($text, $mailm='', $mittm='') {

			//	Analyze content of message
			$this->checkExtLink($text);

			//	if lookup is enabled in the database, proceed to connect to the database and the new research
			if (isset($this->in_db) && !empty($this->in_db) && ($this->in_db))
				$this->checkDB($mailm, $mittm);

			//	if lookup is enabled in the file, proceed to open file and the new research
			if (isset($this->in_file) && !empty($this->in_file) && ($this->in_file))
				$this->checkFile($mailm, $mittm);

			if (!empty($this->point)) {

				//	if the self-learning is enabled and the email is marked as spam, save into database or into file, the sender and e-mail address
				if (isset($this->robot) && !empty($this->robot) && ($this->robot))
					$this->saveSpam($mailm, $mittm);

			}

			return $this->point;

		}

	}