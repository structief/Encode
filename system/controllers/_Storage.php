<?php

	Class Storage {
		var $storage;

		function __construct(){
			//Controller initialized
			if(!isset($_SESSION['_storage'])){
				$this->storage = array();
				$_SESSION['_storage'] = serialize($this->storage);
			}else{
				$this->storage = unserialize($_SESSION['_storage']);
			}
		}

		function __destruct(){
			//Save container
			$session_storage = unserialize($_SESSION['_storage']);
			foreach($this->storage as $key => $var){
				$test = false;
				if(is_object($var) || is_array($var)){
					foreach($var as $key_object => $value_object){
						if($value_object !== null){
							$test = true;
						}
					}
				}elseif($var !== null){
					$test = true;
				}
				if($test){
					$session_storage[$key] = $var;
				}
			}
			$_SESSION['_storage'] = serialize($session_storage);
		}

		function index(){
	    	$this->error->trigger(404, "Page does not exist");
		}

		public function add($name, $object){
			$this->storage[$name] = $object;
		}

		public function get($name){
			if(array_key_exists($name, $this->storage)){
				return $this->storage[$name];
			}else{
				return false;
			}
		}
	}
?>