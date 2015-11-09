<?php
	/*
	//Class does nothing more than loading view parts and using the variables given
	*/
	use Encode\Controller as Controller;

	Class Load{
		var $layout = "splash";
		
		function __construct(){
			return $this;
		}

		public function layout($layout){
			$this->layout = $layout;
			$c = new Controller();
			$c->assets->render($this->layout);
		}

		public function view($viewPath, $variables = array()){	
			$this->helper("_language");

			//Set variables
			foreach($variables as $variableName => $variableValue){
				if(is_array($variableValue)){
					$$variableName = $this->_handleArray($variableValue);
				}elseif($variableValue !== null){
					$$variableName = $variableValue;
				}
			}

			if(is_dir("application/layout/" . $this->layout)){
				include("application/layout/" . $this->layout . "/header.php");
			}else{
				include("system/layout/splash/_header.php");
			}

			//Add all the header-magnets
			include('application/config/modules.inc.php');
			foreach($enabled_modules as $m){
				if(file_exists('application/modules/' . $m . '/magnets/header.php')){
					include('application/modules/' . $m . '/magnets/header.php');
				}
			}

			$calling_class_location = $this->_get_calling_class_path();

			if(substr($viewPath, 0, 1) == '_'){
				include('system/views/' . $viewPath . '.php');
			}elseif(strpos($calling_class_location, "modules")){
				//Determine the module
				$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
				$module = substr($module, 0, strpos($module, '/'));
				
				if(file_exists("application/modules/" . $module . '/views/' . $viewPath . '.php')){
					include("application/modules/" . $module . '/views/' . $viewPath . '.php');
				}
			}else{
				if(file_exists('application/views/' . $viewPath . '.php')){
					include('application/views/' . $viewPath . '.php');
				}
			}

			//Add all the footer-magnets
			foreach($enabled_modules as $m){
				if(file_exists('application/modules/' . $m . '/magnets/footer.php')){
					include('application/modules/' . $m . '/magnets/footer.php');
				}
			}


			if(is_dir("application/layout/" . $this->layout)){
				if(file_exists("application/layout/" . $this->layout . "/footer.php")){
					include("application/layout/" . $this->layout . "/footer.php");
				}
			}else{
				if(file_exists("system/layout/splash/_footer.php")){
					include("system/layout/splash/_footer.php");
				}
			}
		}

		public function helper($helperPath){
			$calling_class_location = $this->_get_calling_class_path();
			if(is_array($helperPath)){
				foreach($helperPath as $helper){
					if(substr($helper, 0, 1) == "_"){
						include_once('system/helpers/' . $helper . '_helper.php');
					}elseif(strpos($calling_class_location, "modules")){
						//Determine the module
						$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
						$module = substr($module, 0, strpos($module, '/'));
						
						include_once('application/modules/' . $module . '/helpers/' . $helper . '_helper.php');
					}else{
						include_once('application/helpers/' . $helper . '_helper.php');
					}
				}
			}else{
				if(substr($helperPath, 0, 1) == "_"){
					include_once('system/helpers/' . $helperPath . '_helper.php');
				}elseif(strpos($calling_class_location, "modules")){
					//Determine the module
					$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
					$module = substr($module, 0, strpos($module, '/'));
					
					include_once('application/modules/' . $module . '/helpers/' . $helper . '_helper.php');
				}else{
					include_once('application/helpers/' . $helperPath . '_helper.php');
				}
			}
		}

		public function model($models){
			$calling_class_location = $this->_get_calling_class_path();
			if(is_array($models)){
				foreach($models as $model){
					if(strpos($calling_class_location, "modules")){
						//Determine the module
						$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
						$module = substr($module, 0, strpos($module, '/'));
						
						include('application/modules/' . $module . '/models/' . ucfirst($model) . "_model.php");
					}else{
						require_once("application/models/" . ucfirst($model) . "_model.php");
					}
				}
			}else{
				if(strpos($calling_class_location, "modules")){
					//Determine the module
					$module = substr($calling_class_location, strpos($calling_class_location, "modules/")+strlen('modules/'));
					$module = substr($module, 0, strpos($module, '/'));
					
					include('application/modules/' . $module . '/models/' . ucfirst($models) . "_model.php");
				}else{
					require_once("application/models/" . ucfirst($models) . "_model.php");
				}
			}
		}

		private function _handleArray($array){
			$object = new stdClass();
			foreach($array as $key=>$value){
				if(is_array($value)){
					$object->$key = $this->_handleArray($value);
				}else{
					$object->$key = $value;
				}
			}
			return $object;
		}

		private function _get_calling_class_path() {
		    //get the trace
    		$trace = debug_backtrace();

		    // Get the class that is asking for who awoke it
		    $class = $trace[1]['class'];

		    // +1 to i because we have to account for calling this function
		    for ( $i=1; $i<count( $trace ); $i++ ) {
				if ( isset( $trace[$i] ) && isset($trace[$i]['class'])){
					if ( $class != $trace[$i]['class'] ){
						if($trace[$i]['class'] != ""){
							$reflector = new ReflectionClass($trace[$i]['class']);
							return $reflector->getFileName();
						}else{
							return "";
						}
					}else{
						return "";
					}
				}else{
					return "";
				}
		    }
		}
	}
?>
