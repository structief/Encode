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

	//Define SendGrid API Settings. Generate one @ www.sendgrid.com
	define("SENDGRID_API_KEY", "**sendgridKey**");

?>