<?php
	//Functions for configuration at start.

	function isValid($configArray){
		$errors = array();
		foreach($configArray as $field => $value){
			if(in_array($field, array("productTitle", "baseUrl", "dbHost","dbUser", "dbPswd", "dbName"))){
				if($value == "" OR !isset($value) OR $value == null){
					$errors[$field] = true;
				}
			}
		}
		return $errors;
	}
?>