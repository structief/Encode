<?php
	Class Routing {

	    public static function start_routing(){
	    	if(isset($_GET['file'])){
	    		$content_types = [
	    			"css" => "text/css",
	    			"js" => "applicaton/x-javascript",
	    			"woff2" => "font/woff2",
	    			"woff" => "font/woff",
	    			"ttf" => "font/ttf",
	    		];
	    		foreach($content_types as $control => $type){
	    			if(strpos($_GET["get"], "." . $control)){
	    				header('Content-type: ' . $type);
	    			}
	    		}
	    		$query = $_SERVER['REQUEST_URI'];
	    		$query = substr($query, strpos($query, "?")+1);
	    		$query = explode("&", $query);$temp = $query;$query = array();
	    		foreach($temp as $q){
	    			$t = explode("=",$q);
	    			if(!isset($_GET[$t[0]])){
	    				if(count($t) > 1){
			    			$_GET[$t[0]] = $t[1];
			    		}else{
			    			$_GET[$t[0]] = null;
			    		}
		    		}
	    		}
	    		echo file_get_contents($_GET['get']);
	    		exit();
	    	}
	    	if(FW_CONFIGS){
		    	//Split the url to pieces
		    	if(strpos($_SERVER['REQUEST_URI'], '?') !== false){
		    		//Yeah..well, facebook tends to make a problem here, so we replace other parameters
		    		$url = $_SERVER['REQUEST_URI'];
		    		$sub_url = "&" . substr($url, strpos($url, '?')+1);
		    		while(($startpos = strpos($sub_url,"&")) !== false){
		    			$startpos += 1;
		    			//Determine the parameter name
		    			$par_name = substr($sub_url, $startpos);
		    			$par_name = substr($par_name, 0, strpos($par_name, "="));

		    			//Now get the value
		    			$sub_url = substr($sub_url,$startpos);
		    			$temp = substr($sub_url, strpos($sub_url, "=")+1);
		    			if(($endpos = strpos($temp, "&")) === false){ $endpos = strlen($temp); };
		    			$par_value = substr($temp, 0, $endpos);
		    			$_GET[$par_name] = $par_value;
		    			$_REQUEST[$par_name] = $par_value;
		    		}
		    	}
		    	$url = Routing::split_url($_GET);

		    	//Rename url-parts for proper use
		    	if(isset($url[0])){
			    	$class = $url[0];
			    }else{
			    	$class = null;
			    }
		    	if(isset($url[1]) && $url[1] != "" && $url[1] != null && substr($url[1], 0, 1) != "_"){
			    	$action = $url[1];
			    }else{
			    	$action = 'index';
			    }
			    $count=2;$params = array();
			    while(isset($url[$count])){
			    	array_push($params, $url[$count]);
			    	$count++;
			    }
		    	//Be smart and get the correct controller for each page:
		    	if(class_exists(ucfirst($class) . '_Controller')){
		    		$class = ucfirst($class) . '_Controller';
			    	$obj = new $class();
			    	//And execute the correct function please:
			    	if(method_exists($obj, $action)){
			    		call_user_func_array(array($obj, $action), $params);
			    	}else{
			    		//Function not existing, render 404
				    	$c = new Controller();
				    	$c->error->trigger(404, "Function does not exist");
			    	}
			    }elseif($class == null){
			    	//Landing page, please?
			    	$LANDING_CONTROLLER = LANDING_CONTROLLER;
			    	$obj = new $LANDING_CONTROLLER();
			    	$obj->index();
			    }else{
			    	/* FOLLOWING CODE IS FOR CHECKING IF A COMPANY EXISTS. BUT WE NEED IT FOR ANGULAR. FIND A BETTER WAY LATER
			    	//Let's do a db-check here, see if the company exists, otherwhise call the controller
			    	$db = new DBConnection();
			    	$db->select('*')->from("companies")->where("name = '" . str_replace("_", " ", $url[0]) . "'")->execute();
			    	$result = $db->fetch_one();

			    	if(count($result) == 0){
				    	//Class nor company do exist, call 404
				    	$c = new Controller();
				    	$c->error->trigger(404, "Company does not exist");
				    }else{
				    	//Load the form called, found in $action
				    	//LOAD THE DAMN FORM
				    }
				    */
			    	$LANDING_CONTROLLER = LANDING_CONTROLLER;
			    	$obj = new $LANDING_CONTROLLER();
			    	$obj->index();
			    }
			}else{
				//Render the page for setting environment variables. That'll help users start more easily.
				$obj = new Initialize();
				$obj->index();
			}

	    	//Yep, that's it.
	   	}

	   	public static function split_url($getArray){
	    	//Get the GET post, and split it to reason!
	    	if(array_key_exists("get", $getArray)){
				$getArray = $getArray['get'];
				while(substr($getArray, -1) == '/'){
					$getArray = substr($getArray, 0, -1);
				}

				if(strpos($getArray, '/') !== false){
					$getArray = explode('/',$getArray);
				}else{
					$getArray = array($getArray);
				}
				return $getArray;
			}else{
				return $getArray;
			}
	   	}
	}

?>