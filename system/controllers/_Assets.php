<?php
	/*
	 * Assets Class, handles all request for files, javascripts and css stuff, fonts and other crazy things
	*/
	//For minifying
	use MatthiasMullie\Minify;
	use Encode\Controller as Controller;
	

	Class Assets{
		function __construct(){
			return $this;
		}

		public function get($map, $name){
			$calling_class_location = $this->_get_calling_class_path();$original = $name;

			if(strpos($map, "_") === 0){
				$this->location = "system";
				$map = substr($map, 1);
			}else{
				$this->location = "application";
			}
			if(strpos($calling_class_location, "modules")){
				//Module call
				$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
				$module = substr($module, 0, strpos($module, '/'));

				$path =  $this->scan_dir_for_file($location . '/modules/' . $module . '/assets/' . $map . '/', $name);
				$original_path = $this->scan_dir_for_file($location . '/modules/' . $module . '/assets/' . $map . '/', $original);
			}else{
				$path = $this->scan_dir_for_file($this->location . '/assets/' . $map . '/', $name);
				$original_path = $this->scan_dir_for_file($this->location . '/assets/' . $map . '/', $original);
			}

			return $path;
		}

		private function scan_dir_for_file($dir, $name){
			$files = scandir($dir);
			foreach($files as $file){
				if(substr($file, 0, 1) != '.'){
					if(strpos($file, '.') === false){
						if(($return = $this->scan_dir_for_file($dir . $file . '/', $name)) !== false){
							return $return;
						}
					}elseif(substr($file, 0, strrpos($file, '.')) == $name){
						if($this->location == "application"){
							return BASE_URL . $dir . $file;
						}else{
							return FW_BASE_URL . $dir . $file;
						}
					}
				}
			}
			return false;
		}

		private function getAllFiles($dir, $ext, &$results){
			$files = scandir($dir);
			foreach($files as $file){
				if(substr($file, 0, 1) != '.'){
					if(strpos($file, '.') === false){
						$this->getAllFiles($dir . "/" . $file, $ext, $results);
					}elseif((strrpos($file, "." . $ext) !== false) || (strrpos($file, "." . $ext . ".php") !== false)){
						$results[] = $dir . "/" . $file;
					}
				}
			}
			return false;
		}

		private function _get_calling_class_path() {
		    //get the trace
    		$trace = debug_backtrace();

		    // Get the file that is asking for who awoke it
		    $file = $trace[1]['file'];

		    return $file;
		}
	}

	
?>