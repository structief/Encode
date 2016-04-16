<?php
	/*
	* Are the really essential config's set?
	* If true, then yes, continue. If not, then load the config settings page
	*/

	//Load package.json, select variables
	$package = json_decode(file_get_contents("package.json"));
	
	define("FW_TITLE", $package->name);
	define("FW_VERSION", $package->version);
	define("FW_BASE_URL", "");
	define("FW_CONFIGS", false);

	define("LAST_RENDERED_FILE_TIME", NULL);
?>