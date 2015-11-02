<?php
	/*
	 * Class handlers Inputs, like get or post
	*/

	Class Input {
		var $getArray;
		var $postArray;

		function __construct(){
			$this->getArray = array();
			$this->postArray = array();
		}

		public function getGetVariable($var = null){
			if($var == null){
				return $_GET;
			}elseif(array_key_exists($var, $_GET)){
				return $_GET[$var];
			}else{
				return false;
			}
		}

		public function getPostVariable($var = null){
			$post = json_decode(file_get_contents('php://input'), true);
			if($var == null){
				return $post;
			}elseif(array_key_exists($var, $post)){
				return $post[$var];
			}else{
				return false;
			}
		}

		public function getVariable($var = null){
			if($var == null){
				return $_REQUEST;
			}elseif(array_key_exists($var, $_REQUEST)){
				return $_REQUEST[$var];
			}else{
				return false;
			}
		}

		public function setGetVariable($variables){
			//do something with them
			foreach($variables as $key => $value){
				$_GET[$key] = $value;
			}
		}

		public function setPostVariable($variables){
			//do something with them
			foreach($variables as $key => $value){
				$_POST[$key] = $value;
			}
		}

		public function isPost(){
			$post = json_decode(file_get_contents('php://input'), true);
			if(isset($post) && count($post)>0){
				return true;
			}else{
				return false;
			}
		}
	}
?>