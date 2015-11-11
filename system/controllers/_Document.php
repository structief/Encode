<?php
	/*
	 * Document Controller, handles in and output of documents
	*/

	Class Document {
		var $doc = null;
		var $hidden = false;
		var $fHandler = null;

		function __construct($url, $name, $hidden = false) {
			if(file_exists($url . '/' . $name)){
				$this->doc = $url . '/' . $name;
				$this->fHandler = fopen($this->doc, "r+");
			}else{
				if(!file_exists($url)){
					//Remove the first '/' if the path starts with one
					if(strpos($url, "/") == 0){
						$url = substr($url, 1);
					}
					//Split foldername
					$dir = explode("/", $url);$mapped_dir = "";
					//Loop over folders
					for($i = 0; $i < count($dir);$i++){
						if($i > 0){
							$mapped_dir .= "/";
						}
						$mapped_dir .= $dir[$i];
						//If the directory does not exist yet, make a new one
						if(!file_exists($mapped_dir)){
							mkdir($mapped_dir);
						}
					}
				}
			   	$this->fHandler = fopen($url . '/' . $name, 'w+') or die("can't open file " . $url . "/" . $name);
				$this->doc = $url . '/' . $name;
				if($hidden){
					file_put_contents($this->doc, "<?php /*
 */ ?>", LOCK_EX);
					$this->hidden = true;
				}
			}
			if($hidden){
				$this->hidden = true;
			}

			return $this;
		}

		function __destruct(){
			if(is_resource($this->fHandler)){
				fclose($this->fHandler);
			}
		}
		
		public function write($string){
			if($this->hidden){
				$string = "<?php /*
" . $string . " */ ?>";
			}
			return (file_put_contents($this->doc, $string, LOCK_EX) !== FALSE);
		}

		public function replace($fromString, $toString){
			$string = file_get_contents($this->doc);
			$string = str_replace($fromString, $toString, $string);
			return (file_put_contents($this->doc, $string, LOCK_EX) !== FALSE);
		}

		public function pregreplace($start, $end, $fillIn){
			$string = file_get_contents($this->doc);
			$string = preg_replace('#('.$start.')(.*)('.$end.')#si', '$1 ' . $fillIn . ' $3', $string);
			return (file_put_contents($this->doc, $string, LOCK_EX) !== FALSE);
		}

		public function append($string){
			if($this->hidden){
				return $this->replace(" */ ?>", $string . " */ ?>");
			}else{
				return (file_put_contents($this->doc,$string . '<br/>', FILE_APPEND | LOCK_EX) !== FALSE);
			}
		}

		public function remove(){
			return unlink($this->doc);
		}

		public function read(){
			return file_get_contents($this->doc);
		}
	}
?>