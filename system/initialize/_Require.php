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

	//Require all System classes, controllers and config files
	$GLOBALS["userFolders"] = array(locateFiles('application/controllers','php'), locateFiles('system/api', 'php'));
	$systemFolders = array(locateFiles('system/initialize','php'), locateFiles('system/controllers','php'), locateFiles('application/config','php'));
	
	//Systemclasses, controllers, configfiles
	foreach ($systemFolders as $systemFolder){
		foreach($systemFolder as $filename){
		    require_once $filename;
		}
	}

	$modules = [];
	//Add enabled modules
	if(!Error::is_dir_empty('application/modules/')){
		foreach($enabled_modules as $module){
			$configs = locateFiles('application/modules/' . $module . '/config'); 
			$GLOBALS["modules"][$module] = [
				"paths" => locateFiles('application/modules/' . $module . '/controllers'),
			];
			foreach($configs as $p){
				require_once $p;
			}
		}
	}


	//Autoload classes that have not been defined yet
	spl_autoload_register(function ($class) {
		//Require all classes
		//Loop through user controllers / modules to find the right file
		$trigger = false;
		foreach($GLOBALS["userFolders"] as $folder){
			foreach($folder as $filename){
				$file = substr($filename, strrpos($filename, "/")+1);
				//Check if filename maps with controller-name
				$file = substr($file, 0, strrpos($file, ".php"));
				$stripped_class = substr($class, strrpos($class, "\\")+1);
				if($file == $stripped_class){
				    require_once $filename;
				    return true;
				}
			}
		}
		//If we haven't found it by now, it's gotta be a module
		foreach($GLOBALS["modules"] as $module){
			foreach($module["paths"] as $path){
				//Check filename for controller name and require_once it
				$file = substr($path, strrpos($path, "/"));
				//Check if filename maps with controller-name
				$stripped_class = substr($class, strrpos($class, "\\")+1);
				if($file == $stripped_class){
				    require_once $path;
				    return true;
				}
			}
		}
	});

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