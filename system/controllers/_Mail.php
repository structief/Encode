<?php
	/*
	* Mail class, makes mailing easier!
	* Uses mandrill app, settings are to be stored in all.config.inc.php
	*/
	Class Mail {
		var $subject = "";
		var $from = array();
		var $headers = array();
		var $recipients = array();
		var $HTMLBody = "";
		var $plainBody = "";
		var $importance = false;
		var $mandrill;
		var $isTemplate = false;
		var $availableTemplates = array();
		var $archive = true;

		function __construct($archive = true){
			$this->mandrill = new Mandrill(MANDRILL_API_KEY);

			$this->from = array("name"=>TITLE . " system", "email"=>"bot@" . str_replace(" ", "_", TITLE) . ".com");
			$this->subject = "Mail from " . TITLE;

			if ($handle = opendir('system/layout/mail')) {
			    while (false !== ($entry = readdir($handle))) {
			    	if(strpos($entry, ".") > 1){
				        array_push($this->availableTemplates, strtolower(substr($entry, 0, strpos($entry, "."))));
				    }
			    }
			    closedir($handle);
			}
			$this->archive = $archive;
		}

		public function setSubject($subject){
			if($subject == ""){
				trigger_error("Subject is empty", E_USER_WARNING);
			}
			if($this->isTemplate){
				$this->addContent("subject", $subject);
			}
			$this->subject = $subject;


			//Return instance for chaining
			return $this;
		}

		public function setHeaders($headers){
			foreach($headers as $key => $value){
				$this->headers[$key] = $value;
			}

			//Return instance for chaining
			return $this;
		}

		public function addTo($toEmail, $toName = ""){
			if(!isset($toEmail)){
				trigger_error("Mail recipients not correctly defined", E_USER_ERROR);
			}else{
				array_push($this->recipients, array("name"=>$toName, "email"=>$toEmail, "type" => 'to'));
			}

			//Return instance for chaining
			return $this;
		}

		public function addCc($ccEmail, $ccName = ""){
			if(!isset($ccEmail)){
				trigger_error("Mail recipients not correctly defined", E_USER_ERROR);
			}else{
				array_push($this->recipients, array("name"=>$ccName, "email"=>$ccEmail, "type" => 'cc'));
			}

			//Return instance for chaining
			return $this;
		}

		public function addBcc($bccEmail, $bccTo = ""){
			if(!isset($bccEmail)){
				trigger_error("Mail recipients not correctly defined", E_USER_ERROR);
			}else{
				array_push($this->recipients, array("name"=>$bccTo, "email"=>$bccEmail, "type" => 'bcc'));
			}

			//Return instance for chaining
			return $this;
		}

		public function setFrom($email, $name=""){
			if(!isset($email)){
				trigger_error("Mail sender not correctly defined", E_USER_ERROR);
			}else{
				$this->from = array("name"=>$name, "email"=>$email);
			}

			//Return instance for chaining
			return $this;
		}

		public function setBody($body, $type = "html"){
			switch($type){
				case 'html':
				default:
					$this->HTMLBody = $body;
					break;
				case 'plain':
					$this->plainBody = $body;
					break;	
			}

			//Return instance for chaining
			return $this;
		}

		public function setImportance($flag){
			if($flag != true AND $flag != false){
				trigger_error("Importance should be true or false", E_USER_ERROR);
			}else{
				$this->importance = $flag;
			}

			//Return instance for chaining
			return $this;
		}

		public function setTemplate($template){
			if(in_array(strtolower($template), $this->availableTemplates)){
				$this->isTemplate = true;
				$this->HTMLBody = file_get_contents("system/layout/mail/" . strtolower($template) . ".html");

				//Update the ARCHIVE, LOGO, FACEBOOK and TWITTER-link immediately
				if(!$this->archive){$this->addContent("ARCHIVE_LINK", ARCHIVE_LINK);}
				$c = new Controller();$c->load->helper('_language');
				$this->addContent("FACEBOOK", FACEBOOK_LINK)
					->addContent("TWITTER", TWITTER_LINK)
					->addContent("EMAIL_ADDRESS_TEXT", t(""))
					->addContent("OPEN_IN_BROWSER", t("Lees deze e-mail in je browser"))
					->addContent("LOGO_LINK", LINK_URL . $c->assets->get('images/landing_images', 'logo'))
					->addContent("PROFILE_SETTINGS_URL", LINK_URL . "/settings")
					->addContent("PROFILE_SETTINGS_TEXT", t("Wijzig profielinstellingen"))
					->addContent("EMAIL_ADDRESS_TEXT", t("Ons e-mailadres is") . ": ");
			}else{
				$this->isTemplate = false;
				trigger_error("The selected template does not exist", E_USER_WARNING);
			}

			//Return instance for chaining
			return $this;
		}

		public function addContent($key, $value){
			if($this->isTemplate){
				$this->HTMLBody = str_replace("[[" . strtoupper($key) . "]]", $value . "[[" . strtoupper($key) . "]]", $this->HTMLBody);
			}else{
				$this->HTMLBody .= $value;
			}

			//Return instance for chaining
			return $this;
		}

		public function send(){
			//ARCHIVE THIS EMAIL
			if($this->archive){
				$rand = substr(md5(rand(0, 100) . date("dmyHis")), 0, 10);
				//Set url in HTML body
				$this->HTMLBody = str_replace("[[ARCHIVE_LINK]]", LINK_URL . "application/logs/" . $rand . ".html", $this->HTMLBody);
			}

			//Remove all variables ([[var]]) in template, if necessairy
			while(($startpos = strpos($this->HTMLBody, "[[")) !== false){
				$endpos = strpos(substr($this->HTMLBody, $startpos), "]]") + $startpos;
				$temp = substr($this->HTMLBody, 0, $startpos) . substr($this->HTMLBody, $endpos+2);
				$this->HTMLBody = $temp;
			}

			//Create the archived document
			if($this->archive){
				$doc = new Document("application/logs", $rand . '.html');
				if(STAGE == "test"){
					$string = "From: " . $this->from['name'] . ' (' .$this->from['email'] . ')<br/>To: ' . print_r($this->recipients, true) . "<br/>Subject: " . $this->subject . '<br/>';
				}else{
					$string = "";
				}
				$string .= $this->HTMLBody;
				$doc->write($string);
			}

			$message = array(
		        'html' => $this->HTMLBody,
		        'text' => $this->plainBody,
		        'subject' => $this->subject ? $this->subject : '',
		        'from_email' => $this->from['email'],
		        'from_name' => $this->from['name'],
		        'to' => $this->recipients,
		        'headers' => $this->headers,
		        'important' => $this->importance,
		    );
		    $async = false;

			switch(STAGE){
				case 'deploy':
				case 'test':
					try {
					    $results = $this->mandrill->messages->send($message, $async);
					} catch(Mandrill_Error $e) {
						trigger_error("A mail error occured: " . get_class($e) . ' - ' . $e->getMessage(), E_USER_ERROR);
					}

					$sent = array();$rejected = array();
					foreach($results as $result){
						if($result['status'] == "sent"){
							array_push($sent, $result['email']);
						}else{
							array_push($rejected, array($result['email'] => $result['reject_reason']));
						}
					}

					return array("sent" => $sent, "rejected" => $rejected);
					break;
				case 'dev':
				default:
					//Display email in browser, don't send
					print_r($message);
					return array("sent" => $this->recipients);
					break;
			}
		}



		//CLEAR FUNCTIONS

		public function clearSubject(){
			$this->subject = "";

			//Return instance for chaining
			return $this;
		}

		public function clearHeaders(){
			$this->headers = array();

			//Return instance for chaining
			return $this;
		}

		public function clearBody(){
			$this->HTMLBody = "";
			$this->plainBody = "";

			//Return instance for chaining
			return $this;
		}

		public function clearFrom(){
			$this->from = array();

			//Return instance for chaining
			return $this;
		}

		public function clearRecipients(){
			$this->recipients = array();

			//Return instance for chaining
			return $this;
		}
	}
?>