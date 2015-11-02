<?php
	//Helps _breadcrumbs.php to create a breadcrumb

function getUrl(){
	$urlArray = Routing::split_url($_GET);
	if(!array_key_exists(0, $urlArray) OR $urlArray[0] == null){
		$return['Home'] = BASE_URL;
		return $return;
	}else{
		$return = array();$url = BASE_URL;
		foreach($urlArray as $crumb){
			$url .= $crumb . '/';
			$return[$crumb] = $url;
		}
		return $return;
	}
}

?>