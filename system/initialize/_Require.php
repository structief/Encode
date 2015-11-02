<?php
	session_start();
	$_SESSION['errors'] = array();

	//Require the Routing controller
	require_once('_Routing.php');

	//Set include path
	$path = __DIR__;
	$path = substr($path, 0, strrpos($path, '/'));
	$path = substr($path, 0, strrpos($path, '/'));
	set_include_path($path . '/');
	set_include_path(get_include_path() . PATH_SEPARATOR . '.');

	//Require all classes
	$userFolders = array(locateFiles('application/controllers','php'), locateFiles('application/config','php'));
	$systemFolders = array(locateFiles('system/initialize','php'), locateFiles('system/controllers','php'), locateFiles('system/api', 'php'));
	
	//Systemclasses, controllers, configfiles
	foreach ($systemFolders as $systemFolder){
		foreach($systemFolder as $filename){
		    require_once $filename;
		}
	}

	//Userclasses, controllers and configfiles
	foreach ($userFolders as $userFolder){
		foreach($userFolder as $filename){
		    require_once $filename;
		}
	}

	//Add enabled modules
	if(!Error::is_dir_empty('application/modules/')){
		foreach($enabled_modules as $module){
			$paths = locateFiles('application/modules/' . $module . '/controllers');
			$configs = locateFiles('application/modules/' . $module . '/config'); 
			foreach($paths as $p){
				require_once $p;
			}
			foreach($configs as $p){
				require_once $p;
			}
		}
	}

	//set date tot Brussels
	date_default_timezone_set('Europe/Brussels');

	//Set own error handler
	set_error_handler("Error::errorHandler");
	register_shutdown_function('Error::shutdownFunction');

	function locateFiles($path, $extension = "php"){
		$results = array();
		$dirs = array($path);
		while(count($dirs) > 0){
			$temp = scandir($dirs[0]);
			foreach($temp as $t){
				if(strpos($t, ".") === false){
					$dirs[] = $dirs[0] . '/' . $t;
				}elseif(strpos($t, "." . $extension) !== false){
					$results[] = $dirs[0] . '/' . $t;
				}
			}
			unset($dirs[0]);
			$dirs = array_values($dirs);
		}
		return $results;
	}
?>