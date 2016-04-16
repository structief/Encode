<?php
	/*
	* Mail class, makes mailing easier!
	* Uses SendGrid app, settings are to be stored in all.config.inc.php
	*/
	Class Mail {
		var $subject = "";
		var $from = array();
		var $headers = array();
		var $recipients = array();
		var $HTMLBody = "";
		var $plainBody = "";
		var $sendgrid;
		var $isTemplate = false;
		var $availableTemplates = array();
		var $archive = true;

		function __construct($archive = true){
			$this->sendgrid = new SendGrid(SENDGRID_API_KEY);

			$this->from = array("name"=>TITLE . " bot", "email"=>TITLE . "@" . TITLE . ".be");
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

		public function setTemplate($template){
			if(in_array(strtolower($template), $this->availableTemplates)){
				$this->isTemplate = true;
				$this->HTMLBody = file_get_contents("system/layout/mail/" . strtolower($template) . ".html");

				//Update the LOGO, FACEBOOK and TWITTER-link immediately
				$this->addContent("LOGO_LINK", LOGO_LINK);
				$this->addContent("FACEBOOK_LINK", FACEBOOK_LINK);
				$this->addContent("TWITTER_LINK", TWITTER_LINK);
				$this->addContent("INSTAGRAM_LINK", INSTAGRAM_LINK);
				$this->addContent("BASE_URL", LINK_BASE);
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
				$rand = substr(md5(rand(0, 100) . date("dmyHis")), -10);
				//Set url in HTML body
				$this->HTMLBody = str_replace("[[ARCHIVE]]", LINK_BASE . "archive/" . $rand . ".html", $this->HTMLBody);
			}

			//Remove all variables ([[var]]) in template, if necessairy
			while(($startpos = strpos($this->HTMLBody, "[[")) !== false){
				$endpos = strpos(substr($this->HTMLBody, $startpos), "]]") + $startpos;
				$temp = substr($this->HTMLBody, 0, $startpos) . substr($this->HTMLBody, $endpos+2);
				$this->HTMLBody = $temp;
			}

			//Create the archived document
			if($this->archive){
				$doc = new Document("archive/", $rand . '.html');
				if(STAGE == "test"){
					$string = "From: " . $this->from['name'] . ' (' .$this->from['email'] . ')<br/>To: ' . print_r($this->recipients, true) . "<br/>Subject: " . $this->subject . '<br/>';
				}else{
					$string = "";
				}
				$string .= $this->HTMLBody;
				$doc->write($string);
			}

		    $email = new SendGrid\Email();
		    $email->setFrom($this->from["email"])
		    		->setFromName($this->from["name"])
		    		->setSubject($this->subject)
		    		->setText($this->plainBody)
		    		->setHTML($this->HTMLBody)
		    		->setHeaders($this->headers);
		    //Add recipients
		    foreach($this->recipients as $recipient){
		    	switch($recipient["type"]){
		    		case "to":
		    		default:
		    			$email->addTo($recipient['email'], $recipient["name"]);
		    			break;
		    		case "cc":
		    			$email->addCc($recipient['email'], $recipient["name"]);
		    			break;
		    		case "bcc":
		    			$email->addBcc($recipient['email'], $recipient["name"]);
		    			break;
		    	}	
		    }

			switch(STAGE){
				case 'deploy':
				case 'test':
					$result = [];
					try {
					    $result = $this->sendgrid->send($email);
					} catch(\SendGrid\Exception $e) {
					    foreach($e->getErrors() as $er) {
					        trigger_error("A mail error occured: " . $e->getCode() . " - " . $er, E_USER_ERROR);
					    }
					}

					$sent = array();$rejected = array();
					if($result->code == 200){
						array_push($sent, true);
					}else{
						array_push($rejected, $result->body->message);
					}

					return array("sent" => $sent, "rejected" => $rejected);
				break;
				case 'dev':
				default:
					//Display email in browser, don't send
					print_r($email);
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