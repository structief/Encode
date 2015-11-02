<?php

	Class Index_Controller extends Controller{
		public function index(){
			//Load a layout
			$this->load->layout("myLayout");


			//Output the view
			$this->load->view('index');
		}
	}
?>