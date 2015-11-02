<?php
	/*
	* Session class, runs session handling
	*/

	class Session {
		public function start(){
			if (session_status() == PHP_SESSION_NONE OR session_id() == '') {
				if(session_start()){
					return true;
				}else{
					trigger_error("Session could not be started", E_USER_WARNING);
					return false;
				}
			}else{
				return true;
			}
		}

		public function destroy(){
			if (session_status() != PHP_SESSION_NONE OR session_id() != '') {
				if(session_destroy()){
					return true;
				}else{
					trigger_error("Session could not be destroyed", E_USER_WARNING);
					return false;
				}
			}else{
				return true;
			}
		}

		public function get($varArray){
			$this->start();
			if(is_array($varArray)){
				$return = array();
				foreach($varArray as $var){
					if(isset($_SESSION[$var])){
						$return[$var] = $_SESSION[$var];
					}else{
						return false;
					}
				}
			}else{
				if(isset($_SESSION[$varArray])){
					$return = $_SESSION[$varArray];
				}else{
					return false;
				}
			}
			return $return;
		}

		public function set($varArray){
			if(is_array($varArray)){
				foreach ($varArray as $key => $value) {
					$_SESSION[$key] = $value;
				}
				return true;
			}else{
				return false;
			}
		}
	}
?>