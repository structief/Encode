<?php
	//Handles the log-departement

	Class Log{
		public function add($string){
			$d = new Document(__DIR__ . '/../../application/logs', date("d_m_Y") . ".php", true);
			$d->append("(" . date("H:i:s") . ") " . $string . "
");
		}

		public function _error($string){
			$d = new Document(__DIR__ . '/../logs', date("d_m_Y") . ".php", true);
			$d->append("(" . date("H:i:s") . ") " . $string . "
");
		}

		public function getReferer(){
			return $_SERVER['HTTP_REFERER'];
		}

		public function online($log){
			$values['log_time'] = date('d-m-Y H:i:s');

			//Verplicht in $log!
			$values['log_type'] = $log['type'];
			$values['executor_id'] = $log['executor_id'];
			$values['log_message'] = $log['message'];
			/*
			* Eventueel nuttig om later messages automatisch te creeeren

			switch($log['type']){
				case 'insert':
					$values['log_message'] = $log['message'];
					break;
				case 'delete':
					$values['log_message'] = $log['message'];
					break;
				case 'create':
					$values['log_message'] = $log['message'];
					break;
				case 'edit':
					$values['log_message'] = $log['message'];
					break;
				case 'spam':
					$values['log_message'] = $log['message'];
					break;
			}
			*/

			//Optioneel in $log
			if(isset($log['form_id'])){
				$values['form_id'] = $log['form_id'];
			}
			if(isset($log['subs_id'])){
				$$values['subs_id'] = $log['subs_id'];
			}
			if(isset($log['member_id'])){
				$values['member_id'] = $log['member_id'];
			}
			if(isset($log['company_id'])){
				$values['company_id'] = $log['company_id'];
			}

			$db = new DBConnection();
			$db->insert($values)->into("logs")->execute();
		}

		public function logApiCall($log){
			$values['call_time'] = date('d-m-Y H:i:s');
			//Get caller ip
			if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			    $ip = $_SERVER['HTTP_CLIENT_IP'];
			}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}elseif(!empty($_SERVER['REMOTE_ADDR'])){
			    $ip = $_SERVER['REMOTE_ADDR'];
			}else{
				$ip = "not known";
			}
			$values['caller_ip'] = $ip;

			//Verplicht in $log!
			$values['url'] = $log['url'];
			$values['data'] = json_encode($log['data']);
			$values['response_code'] = $log['response_code'];
			unset($log['response']['code']);
			if($log['response_code'] !== 200){
				$values['response'] = $log['response']['db_code'];
			}
			$db = new DBConnection();
			$db->insert($values)->into("api_calls")->execute();
		}
	}
?>