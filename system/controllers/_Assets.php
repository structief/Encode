<?php
	/*
	 * Assets Class, handles all request for files, javascripts and css stuff, fonts and other crazy things
	*/

	Class Assets{
		function __construct(){
			return $this;
		}

		public function get($map, $name){
			$calling_class_location = $this->_get_calling_class_path();$original = $name;

			if(strpos($map, "_") === 0){
				$location = "system";
				$map = substr($map, 1);
			}else{
				$location = "application";
			}
			if(strpos($calling_class_location, "modules")){
				//Module call
				$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
				$module = substr($module, 0, strpos($module, '/'));

				if($this->_isCacheable(["map"=>$map, "name"=>$name, "calling_class_location"=>$calling_class_location])){
					$path =  $this->scan_dir_for_file($location . '/assets/renders/', $name);
				}else{
					$path =  $this->scan_dir_for_file($location . '/modules/' . $module . '/assets/' . $map . '/', $name);
				}
				$original_path = $this->scan_dir_for_file($location . '/modules/' . $module . '/assets/' . $map . '/', $original);
			}else{
				$path = $this->scan_dir_for_file($location . '/assets/' . $map . '/', $name);
				$original_path = $this->scan_dir_for_file($location . '/assets/' . $map . '/', $original);
			}

			if($this->_isCacheable(["map"=>$map, "name"=>$name, "calling_class_location"=>$calling_class_location])){
				//If the render is outdated, create a new one
				if(count($file) !== 0 && strtotime($file['cache_date']) < filemtime($original_path)){
					$this->_render_file($db, ['original_path'=>$original_path, 'map'=>$file['file_map'], 'name'=>$file['file_name']]);
				}
			}

			return $path;
		}

		public function render($layout){
			$components = [];$layout_files = ["header.php", "footer.php"];
			$asset_urls = [
				"css" => "<link rel='stylesheet' href='/@url@'>",
				"js" => "<script type='text/javascript' src='/@url@'></script>",
			];

			//Scan header and footer for tags
			foreach($layout_files as $l){
				$file = file_get_contents("application/layout/" . $layout . "/" . $l);
				preg_match_all("<!-- injection:s*([^nr]*) -->", $file, $matches);
				$components[$matches[1][0]] = [];
			}

			//Scan assets/bower_components
			$dir = "application/assets/bower_components";
			$files = scandir($dir);
			foreach($files as $file){
				if(substr($file, 0, 1) != '.'){
					if(strpos($file, '.') === false){
						//It's a component!
						//Check for the main source files
						$json = json_decode(file_get_contents($dir . "/" . $file . "/bower.json"));
						if(is_string($json->main)){
							//Loop over possible injections
							foreach($components as $key => &$array){
								if(strpos($json->main, $key) !== false){
									$array[] = $dir . "/" . $file . "/" . $json->main;
								}
							}
						}else{
							foreach($json->main as $main_file){
								//Loop over possible injections
								foreach($components as $key => &$array){
									if(strpos($main_file, $key) !== false){
										$array[] = $dir . "/" . $file . "/" . $main_file;
									}
								}
							}
						}
					}
				}
			}
			//Inject js into layout-files
			foreach($layout_files as $l_file){
				$doc = new Document("application/layout/" . $layout, $l_file);
				foreach($components as $key => $assets){
					$string = "";
					foreach($assets as $asset){
						$string .= str_replace("@url@", $asset, $asset_urls[$key]) . "\n";
					}
					$doc->pregreplace("<!-- injection:" . $key . " -->", "<!-- endInjection:" . $key . " -->", $string);
				}
				$doc->__destruct();
			}
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
						return BASE_URL . $dir . $file;
					}
				}
			}
			return false;
		}

		private function _render_file($db, $file){
			if(!isset($file['original_path'])){
				if(strpos($file['calling_class_location'], "modules")){
					//Module call
					$module = substr($file['calling_class_location'], strpos($file['calling_class_location'], "modules/")+strlen('modules/'));
					$module = substr($module, 0, strpos($module, '/'));

					$path =  $this->scan_dir_for_file('application/modules/' . $module . '/assets/' . $file['map'] . '/', $file['name']);
				}else{
					$path = $this->scan_dir_for_file('application/assets/' . $file['map'] . '/', $file['name']);
				}
			}else{
				$path = $file['original_path'];
			}
			$new_file_name = md5(microtime());
			$new_file_date = date("d-m-Y H:i:s");
			$new_file = file_get_contents($path);
			$new_file_extension_array = explode(".", $path);
			$new_file_extension = end($new_file_extension_array);

			$doc = new Document('application/assets/renders/', $new_file_name . "." . $new_file_extension);
			$new_file = str_replace(array("\t", "\n", "\r"), "", $new_file);
			$doc->write($new_file);

			//Add to db
			$db->insert(["file_name"=>$file['name'], "file_map"=>$file['map'], "file_extension" => $new_file_extension, "cache_date"=>$new_file_date, "cache_name"=>$new_file_name])->into("caches")->execute();
			return $new_file_name;
		}

		private function _get_calling_class_path() {
		    //get the trace
    		$trace = debug_backtrace();

		    // Get the file that is asking for who awoke it
		    $file = $trace[1]['file'];

		    return $file;
		}

		private function _isCacheable($file){
			if(strpos($file['map'], "css") !== false OR strpos($file['map'], "js") !== false){
				if(strpos($file['calling_class_location'], "modules")){
					//Module call
					$module = substr($file['calling_class_location'], strpos($file['calling_class_location'], "modules/")+strlen('modules/'));
					$module = substr($module, 0, strpos($module, '/'));

					$path =  $this->scan_dir_for_file('application/modules/' . $module . '/assets/' . $file['map'] . '/', $file['name']);
				}else{
					$path = $this->scan_dir_for_file('application/assets/' . $file['map'] . '/', $file['name']);
				}
				if(strpos($path, ".php") === false && CACHING){
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	}

	
?>