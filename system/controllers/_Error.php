<?php
	//Error class, handles all kind of errors, such as 404
	use Encode\Controller as Controller;

	Class Error {
		function __construct(){
			return $this;
		}

		public function trigger($errno, $errstring, $errfile = "", $errline = "", $type = 'ERROR'){
			switch($errno){
				case '404':
					//Trigger 404 with message
					//Determine the top line
					$load = new Load();
					$referer = $_SERVER['HTTP_REFERER'];
					if($referer != null){
						$referer = " from " . $referer;
					}
					if(STAGE == "deploy"){
						$load->view("_404", "splash");
						exit();
					}else{
						echo "Page not found: " . $current_url;
          				exit();
					}
					break;
				case '500':
					//Server error
					$load = new Load();
					$current_url = 'http://' . $_SERVER['HTTP_HOST'];
					$referer = $_SERVER['HTTP_REFERER'];
					if($referer != null){
						$referer = " from " . $referer;
					}
					if(STAGE == "deploy"){
						$load->view("_500", "splash");
						exit();
					}else{
						echo "Server could not complete the request :(";
          				exit();
					}
					break;
				case '503':
					//Database error
					if(STAGE == "deploy"){
						$load = new Load();
						$load->view("_503", "splash");
						exit();
					}else{
						echo "Database not available. Is the service down?<br/> (" . $errstring . ")";
          				exit();
					}
					break;
				case '515':
					//Trigger a database sql error
					Error::shutDownFunction(["message" => $errstring, "file" => $errfile, "line" => $errline]);
					break;
				default:
					if($type == 'ERROR' OR $type == 'error'){
						//Display the error
						Error::shutDownFunction(["message" => $errstring, "file" => $errfile, "line" => $errline]);
					}else{
						//Add the warning/info to the list
					}
					break;
			}
		}

		public static function is_dir_empty($dir){
			if (!is_readable($dir)) return NULL;
	  		return (count(scandir($dir)) <= 2);
		}

		public static function log_error($e_type, $fatal, $errno, $errstr, $errline, $errfile){
			array_push($_SESSION['errors'], array("type"=>$e_type, "fatal"=>$fatal, "number"=>$errno, "message"=>$errstr, "line"=>$errline, "file"=>$errfile));
		}

		public static function display_errors(){
		}

		public static function errorHandler($errno, $errstr, $errfile, $errline){
		    if (!(error_reporting() & $errno)) {
		        // This error code is not included in error_reporting
		        return;
		    }

		    switch ($errno) {
				case 1:
					$e_type = 'ERROR';
					$fatal = "error";
					break;
				case 2:
					$e_type = 'WARNING';
					$fatal = "warning";
					break;
				case 4:
					$e_type = 'PARSE ERROR';
					$fatal = "error";
					break;
				case 8:
					$e_type = 'NOTICE';
					$fatal = "notice";
					break;
				case 16:
					$e_type = 'CORE_ERROR';
					$fatal = "error";
					break;
				case 32:
					$e_type = 'CORE_WARNING';
					$fatal = "warning";
					break;
				case 64:
					$e_type = 'COMPILE_ERROR';
					$fatal = "error";
					break;
				case 128:
					$e_type = 'COMPILE_WARNING';
					$fatal = "warning";
					break;
				case 256:
					$e_type = 'USER_ERROR';
					$fatal = "error";
					break;
				case 512:
					$e_type = 'USER_WARNING';
					$fatal = "warning";
					break;
				case 1024:
					$e_type = 'USER_NOTICE';
					$fatal = "notice";
					break;
				case 2048:
					//Lame error, usually suggestions
					//$e_type = 'STRICT ERROR';
					break;
				case 4096:
					$e_type = 'RECOVERABLE_ERROR';
					$fatal = "warning";
					break;
				case 8192:
					$e_type = 'DEPRECATED';
					$fatal = "notice";
					break;
				case 16384:
					$e_type = 'USER_DEPRECATED';
					$fatal = "notice";
					break;
				case 30719:
					$e_type = 'ALL';
					$fatal = "notice";
					break;
				default:
					$e_type = 'ERROR UNKNOWN';
					$fatal = "notice";
					break;
		    }
		    if(isset($e_type) && in_array(STAGE, array("test", "dev"))){
		    	$controller = new Controller();
		    	$controller->log->_error($e_type . ' (' . $errno . '): ' . $errstr . ' in file ' . $errfile . ' (line ' . $errline . ')');
			    Error::log_error($e_type, $fatal, $errno, $errstr, $errline, $errfile);
			}elseif(STAGE == "deploy"){
				$controller = new Controller();
		    	$controller->log->_error($e_type . ' (' . $errno . '): ' . $errstr . ' in file ' . $errfile . ' (line ' . $errline . ')');
			}


		    /* Don't execute PHP internal error handler */
		    return true;
		}

		public static function shutDownFunction($error = null) {
			if(in_array(STAGE, array("test", "dev"))){
				if($error == null){
				    $error = error_get_last();
				}
			   	if($error != null){
			   		$file = file($error['file']);
			   		$lines = [];
			   		for($i=4;$i>-3;$i--){
			   			$lines[] = $file[$error["line"]-$i];
			   		}
			   		$minTabs = 100;
			   		foreach($lines as $l){
			   			$temp = substr_count($l, "\t");
			   			if($temp < $minTabs && $temp != 0){
			   				$minTabs = $temp;
			   			}
			   		}
			   		foreach($lines as &$l){
			   			for($i=0;$i<$minTabs-1;$i++){
			   				$l = preg_replace("/\t/", "", $l, 1);
			   			}
			   		}
				    include(__DIR__ . '/../views/_fatalError.php');exit();
				}
			}else{
				$load = new Load();
				$load->view("_404", "splash");
			}
		}

		/*
		 * _sendPostToUrl() stuurt post met $data naar $url
		*/
		private function _sendPostToUrl($fields, $url){
			//url-ify the data for the POST
			foreach($fields as $key=>$value) {
				$fields_string .= $key.'='.urlencode($value).'&';
			}
			rtrim($fields_string,'&');

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

			//execute post
			$result = curl_exec($ch);
			return $result;
		}
	}
?>
