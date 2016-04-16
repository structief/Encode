<?php
	//Example controller

	namespace Controller;
	
	Class Index extends \Encode\Controller{
		public function index(){
			//Load a layout
			$this->load->layout("myLayout");
			
			//This outputs "Encode Framework" since it is called from namespace Internal\View
			$title = \Internal\View\Example::renderTitle();
			$test = new \Internal\View\Example();
			//This outputs "Welcome to Encode", since it is called from namespace Internal\Extra
			$welcomeMessage = \Internal\Extra\Example::renderWelcomeMessage();

			//This outputs "But" since it is called from namespace Model, (it is stored in the folder "models")
			$model = new \Model\Example();
			$butMessage = $model->things();

			$db = new \DBConnection();

			//Output the view
			$this->load->view('index', ["title" => $title, "welcomeMessage" => $welcomeMessage, "butMessage" => $butMessage]);
		}
	}
?>