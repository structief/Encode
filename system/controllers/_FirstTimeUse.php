<?php
	/*
	* Class is only called on first use on framework. Needs to set default environment variables and stuff, so do so ;)
	*/
	use Encode\Controller as Controller;

	Class Initialize extends Controller{
		var $configPath;
		var $dbConfigPath;

		function __construct(){
			$this->configPath = __DIR__ . '/../../application/config';
			$this->dbConfigPath = __DIR__ . '/../../application/config';
		}

		function index(){
			if(FW_CONFIGS == false){
				$base_url = substr(__DIR__, 0, -19);
				$base_url = substr($base_url, strrpos($base_url, '/'), 20);
				$base_url .= '/';

				if($this->input->isPost()){
					//Validate form

					$errors = isValid($this->input->getPostVariable());
					if(count($errors) == 0){
						$config = new Document($this->configPath, "all_config.inc.php");
						$dbConfig = new Document($this->dbConfigPath, "db_config.inc.php");

						$configString = $config->read();
						$dbConfigString = $dbConfig->read();

						foreach($this->input->getPostVariable() as $field => $value){
							if(in_array($field, array("productTitle", "baseUrl", "productVersion", "mandrillKey", "productStage"))){
								$configString = str_replace("**" . $field . "**", $value, $configString);
							}elseif($field == "db_prefix" OR $field == "DB_PREFIX"){
								$dbConfigString = str_replace("**" . $field . "**", "_" . $value, $dbConfigString);
							}else{
								$dbConfigString = str_replace("**" . $field . "**", $value, $dbConfigString);
							}
						}


						$config->write($configString);
						$dbConfig->write($dbConfigString);

						//Update config Framework
						$fr = new Document(__DIR__ . '/../initialize', '_all_config.inc.php');
						$fr->replace('
	define("FW_CONFIGS", false);', 'define("FW_CONFIGS", true);');

						//Require the files again, since the settings are changed
						include($this->configPath . '/all_config.inc.php');
						include($this->dbConfigPath . '/db_config.inc.php');

						//Load the view
						$this->load->view("_config", array("form"=>false));
					}else{
						$this->load->view("_config", array("base_url"=>$base_url, "form"=>true, "errors"=>$errors, "post"=>$this->input->getPostVariable()));
					}
				}else{
					$this->load->view("_config", array("base_url"=>$base_url, "form"=>true));
				}
			}else{
				$fof = new FourOhFour("You are somewhere you should not be");
			}
		}

	}
?>