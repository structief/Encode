<?php
	//Example controller

	namespace Controller;
	
	Class Index extends \Encode\Controller{
		public function index(){
			//Load a layout
			$this->load->layout("myLayout");
			
			//Output the view
			$this->load->view('index');
			echo "Internal: View: ";
			\Internal\View\Example::things();
			echo "<br/>Internal: Extra:";
			\Internal\Extra\Example::things();
		}
	}
?>