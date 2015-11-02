<?php

	Class Controller {
		var $container;

		function __construct(){
			//Controller initialized
			if(!isset($_SESSION['_container'])){
				$this->container = array();
				$_SESSION['_container'] = serialize($this->container);
			}else{
				$this->container = unserialize($_SESSION['_container']);
			}
		}

		function __destruct(){
			//Save container
			$session_container = unserialize($_SESSION['_container']);
			foreach($this->container as $key => $obj){
				$test = false;
				foreach($obj as $key_object => $value_object){
					if($value_object !== null){
						$test = true;
					}
				}
				if($test){
					$session_container[$key] = $obj;
				}
			}
			$_SESSION['_container'] = serialize($session_container);
		}

		function __get($key){
			if(is_array($this->container) && array_key_exists($key, $this->container)){
				return $this->container[$key];
			}else{	
				$obj = new $key();
				$this->container[$key] = $obj;
				return $this->container[$key];
			}
		}

		public function getClassFromContainer($name){
			return $this->container[$name];
		}

		function index(){
	    	$this->error->trigger(404, "Page does not exist");
		}

		public function _add($name, $object){
			$this->container[$name] = $object;
		}

		public function _get($name){
			if(array_key_exists($name, $this->container)){
				return $this->container[$name];
			}else{
				return false;
			}
		}
	}
?>