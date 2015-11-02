<?php
	/*
	* Cookie class, runs cookie handling
	*/

	class Cookie {
		public function delete($title){
			if(isset($_COOKIE[$title])){
				return setcookie($title, $this->get($title), time()-3600, "/");
			}else{
				return false;
			}
		}

		public function get($title){
			if(isset($_COOKIE[$title])){
				return $_COOKIE[$title];
			}else{
				return false;
			}
		}

		public function set($title, $value, $expiration = null){
			if($expiration = null){
				$expiration = (2592000 + time());
			}
			return setcookie($title, $value, $expiration, "/");
		}
	}
?>