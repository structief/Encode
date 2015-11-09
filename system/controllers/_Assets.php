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

				if($this->_isCacheable(["map"=>$map, "name"=>$name, "calling_class_location"=>$calling_class_location])){
					$path =  $this->scan_dir_for_file($location . '/assets/renders/', $name);
				}else{
					$path =  $this->scan_dir_for_file($location . '/modules/' . $module . '/assets/' . $map . '/', $name);
				}
				$original_path = $this->scan_dir_for_file($location . '/modules/' . $module . '/assets/' . $map . '/', $original);
			}else{
				$path = $this->scan_dir_for_file($this->location . '/assets/' . $map . '/', $name);
				$original_path = $this->scan_dir_for_file($this->location . '/assets/' . $map . '/', $original);
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
				preg_match_all("<!-- inject:s*([^nr]*) -->", $file, $matches);
				foreach($matches[1] as $match){
					$components[$match] = [];
				}
			}

			//Scan assets/bower_components
			foreach($components as $file_type => $res){
				$this->getAllFiles("application", $file_type, $components[$file_type]);
			}
			
			switch (STAGE) {
				case 'dev':
				default:
					//Insert the real files into the header/footer
					//Delete the previous compiled scripts
					foreach($components as $file_type => $res){
						foreach($res as $key => $r){
							switch($file_type){
								case "js":
									if(strrpos($r, "_scripts.min.js") !== false){
										unset($components[$file_type][$key]);
									}
									break;
								case "css":
									if(strrpos($r, "_styles.min.css") !== false){
										unset($components[$file_type][$key]);
									}
									break;
								default:
									break;
							}
						}
					}
					break;
				case "test":
				case "deploy":
					//Insert the minified version into the header/footer
					foreach($components as $file_type => $results){
						switch ($file_type) {
							case 'js':
								//Loop over scripts and remove the minified ones already
								$temp = [];
								foreach($results as $key => $r){
									if(strpos($r, ".min.") !== false || strpos($r, ".php") !== false){
										$temp[] = $r;
										unset($results[$key]);
									}
								}
								//Loop over last-updated date of the files. If any of them is updated later then the date, compile a new version
								//Loop over last-updated date of the files. If any of them is updated later then the date, compile a new version
								$recompile = false; $lastRenderedFileTime = LAST_RENDERED_FILE_TIME;
								foreach($results as $r){
									if(filemtime($r) > $lastRenderedFileTime){
										$recompile = true;
										$lastRenderedFileTime = filemtime($r);
									}
								}
								
								if(count($results) > 0 && $recompile == true){
									//Update LAST_RENDERED_FILE_TIME
									if(LAST_RENDERED_FILE_TIME == null){
										$str_time = "NULL";
									}else{
										$str_time = LAST_RENDERED_FILE_TIME;
									}
									$d = new Document('./system/initialize', '_all_config.inc.php');
									$d->replace('define("LAST_RENDERED_FILE_TIME", ' . $str_time . ');', 'define("LAST_RENDERED_FILE_TIME", ' . $lastRenderedFileTime . ');');
									
									//Actual minifying
									$initiate = true;
									foreach($results as $r){
										if($initiate){
											$minifier = new Minify\JS('./' . $r);
											$initiate = false;
										}else{
											$minifier->add('./' . $r);
										}
									}
									//Uglify the js
									$myPacker = new JavaScriptPacker($minifier->minify());
									$packed = $myPacker->pack();
									
									//Write to file
									$d = new Document("./application/assets/js/minified", $lastRenderedFileTime . "_scripts.min.js");
									$d->write($packed);

									//Update components with the newly minified script
									$test = true;
									foreach($temp as $key => $t){
										if($t == 'application/assets/js/minified/' . $lastRenderedFileTime . "_scripts.min.js"){
											$test = false;
										}elseif(strrpos($t, "_scripts.min.js") !== false){
											//Delete this script, it is not the same render
											unset($temp[$key]);
											unlink($t);
										}
									}
									if($test){
										$temp[] = 'application/assets/js/minified/' . $lastRenderedFileTime . "_scripts.min.js";
									}
								}
								$components["js"] = $temp;
								break;
							case 'css':
							default:
								$temp = [];
								foreach($results as $key => $r){
									if(strpos($r, ".min.") !== false || strpos($r, ".php") !== false){
										$temp[] = $r;
										unset($results[$key]);
									}
								}
								//Loop over last-updated date of the files. If any of them is updated later then the date, compile a new version
								$recompile = false; $lastRenderedFileTime = LAST_RENDERED_FILE_TIME;
								foreach($results as $r){
									if(filemtime($r) > $lastRenderedFileTime){
										$recompile = true;
										$lastRenderedFileTime = filemtime($r);
									}
								}
								if(count($results) > 0 && $recompile){
									//Update LAST_RENDERED_FILE_TIME
									if(LAST_RENDERED_FILE_TIME == null){
										$str_time = "NULL";
									}else{
										$str_time = LAST_RENDERED_FILE_TIME;
									}
									$d = new Document('./system/initialize', '_all_config.inc.php');
									$d->replace('define("LAST_RENDERED_FILE_TIME", ' . $str_time . ');', 'define("LAST_RENDERED_FILE_TIME", ' . $lastRenderedFileTime . ');');
								
									//Actual minifying
									$initiate = true;
									foreach($results as $r){
										if($initiate){
											$minifier = new Minify\CSS('./' . $r);
											$initiate = false;
										}else{
											$minifier->add('./' . $r);
										}
									}
									//Write to file
									$d = new Document("./application/assets/css/minified",  $lastRenderedFileTime . "_styles.min.css");
									$d->write($minifier->minify());

									//Update components with the newly minified stylesheet
									$test = true;
									foreach($temp as $key => $t){
										if($t == 'application/assets/css/minified/' . $lastRenderedFileTime . "_styles.min.css"){
											$test = false;
										}elseif(strrpos($t, "_styles.min.css") !== false){
											//Delete this styles, it is not the same render
											unset($temp[$key]);
											unlink($t);
										}
									}
									if($test){
										$temp[] = 'application/assets/css/minified/' . $lastRenderedFileTime . "_styles.min.css";
									}
								}
								$components["css"] = $temp;
								break;
						}
					}
					break;
			}

			//Inject js into layout-files
			foreach($layout_files as $l_file){
				$doc = new Document("application/layout/" . $layout, $l_file);
				foreach($components as $key => $assets){
					$string = "";
					foreach($assets as $asset){
						$string .= str_replace("@url@", $asset, $asset_urls[$key]) . "\n";
					}
					$doc->pregreplace("<!-- inject:" . $key . " -->", "<!-- end:" . $key . " -->", $string);
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