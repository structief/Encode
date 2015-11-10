<?php
	
	//The following settings are ESSENTIAL for the working of this framework. Please fill them in carefully and correctly
	

	//First of all, some basic information about your product
	define('VERSION', '**productVersion**');
	define('TITLE', '**productTitle**');
	define('BASE_URL', '**baseUrl**');

	//What stage are you in? (dev, test or deploy)
	define("STAGE", "**productStage**");
	
	//Define your landing controller
	define("LANDING_CONTROLLER", "Index");

	//Define mandrill api settings. Generate one @ www.mandrillapp.com to use the Mail class and all his advantages
	define("MANDRILL_API_KEY", "**mandrillKey**");

?>