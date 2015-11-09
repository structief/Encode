<?php
	use Encode\Controller as Controller;

	function t($string, $values = array(), $changeCase = true){
		/*
		* Deze functie vertaalt volledige stukken tekst naar de gekozen taal (in session of cookie)
		* Dit gebeurt door de language-files op te halen en daar de string uit te halen
		* Wanneer de $string variabele een array is met mogelijkheden, stuurt deze functie de juiste mogelijkeheid terug
		* Als er %x% variabelen in de string teruggevonden worden, worden deze vervangen door de juiste waarde uit $values
		*/
		$language = getLanguageAbbr();
		if(is_string($string)){
			//laad de vertalingen in
			$var = strtolower($string);
			if(!file_exists("application/languages/" . $language . "_set.php")){
				if($changeCase){
					if(ctype_upper($string)){
						$return =  strtoupper($string);
					}
					if(ctype_lower($string)){
						$return =  strtolower($string);
					}
					if(ucfirst($string) == $string){
						$return = ucfirst($string);
					}
				}else{
					$return = $string;
				}
			}else{
				require("application/languages/" . $language . "_set.php");
				$return = $translations[$var];
				if($changeCase){
					if($string === strtoupper($string)){
						$return = strtoupper($return);
					}
					if($string === strtolower($string)){
						$return = strtolower($return);
					}
					if(ucfirst($string) == $string){
						$return = ucfirst($return);
					}
				}
			}
			if(count($values) != 0){
				foreach($values as $num => $val){
					$return = str_replace("%" . $num . "%", $val, $return);
				}
			}
		}elseif(is_array($string)){
			$return = $string[$language];
		}else{
			$return = $string;
		}
		if(empty($return)){
			$return = $string;
		}
		return $return;
	}

	function getLanguageAbbr(){
		$c = new Controller();
		$language = $c->cookie->get("language");
		if($language === false){
			$language = "be_nl";
			$c->cookie->set("language", "be_nl", time() + (60*60*24*365));
		}
		return $language;
	}

	function getLanguageId(){
		$c = new Controller();
		$language = $c->cookie->get("language");
		if($language === false){
			$language = "be_nl";
			$c->cookie->set("language", "be_nl", time() + (60*60*24*365));
		}
		$db = new DBConnection();
		$db->select("language_id")->from("languages")->where("abbr = '" . $language . "'")->execute();
		if(count($result = $db->fetch_one()) > 0){
			return $result['language_id'];
		}else{
			return 0;
		}
	}

	function getCompanyTypeText($company_type_id){
		$db = new DBConnection();
		$db->select("translation")->from("translation_company_types")->where("language_id = " . getLanguageId())->where("company_type_id = " . $company_type_id)->execute();
		if(count($res = $db->fetch_one()) > 0){
			return $res['translation'];
		}else{
			return false;
		}
	}

	function getPrefferedLanguage(){
		$accepted = parseLanguageList($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$return = "gb_en";
		$db = new DBConnection();
		$db->select("abbr")->from("languages")->execute();
		$availableLanguages = "";
		$languages = $db->fetch_array();
		foreach($languages as $count => $language){
			$availableLanguages .= substr($language['abbr'], strpos($language['abbr'], "_")+1) . ", ";
		}
		$availableLanguages = substr($availableLanguages, 0, -2);
		$available = parseLanguageList($availableLanguages);
		$matches = findMatches($accepted, $available);
		foreach($languages as $count => $language){
			if(strpos($language['abbr'], reset($matches)[0]) !== false){
				$return = $language['abbr'];
			}
		}
		return $return;
	}

	//parse list of comma separated language tags and sort it by the quality value
	function parseLanguageList($languageList) {
	    if (is_null($languageList)) {
	        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	            return array();
	        }
	        $languageList = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	    }
	    $languages = array();
	    $languageRanges = explode(',', trim($languageList));
	    foreach ($languageRanges as $languageRange) {
	        if (preg_match('/(\*|[a-zA-Z0-9]{1,8}(?:-[a-zA-Z0-9]{1,8})*)(?:\s*;\s*q\s*=\s*(0(?:\.\d{0,3})|1(?:\.0{0,3})))?/', trim($languageRange), $match)) {
	            if (!isset($match[2])) {
	                $match[2] = '1.0';
	            } else {
	                $match[2] = (string) floatval($match[2]);
	            }
	            if (!isset($languages[$match[2]])) {
	                $languages[$match[2]] = array();
	            }
	            $languages[$match[2]][] = strtolower($match[1]);
	        }
	    }
	    krsort($languages);
	    return $languages;
	}

	// compare two parsed arrays of language tags and find the matches
	function findMatches($accepted, $available) {
	    $matches = array();
	    $any = false;
	    foreach ($accepted as $acceptedQuality => $acceptedValues) {
	        $acceptedQuality = floatval($acceptedQuality);
	        if ($acceptedQuality === 0.0) continue;
	        foreach ($available as $availableQuality => $availableValues) {
	            $availableQuality = floatval($availableQuality);
	            if ($availableQuality === 0.0) continue;
	            foreach ($acceptedValues as $acceptedValue) {
	                if ($acceptedValue === '*') {
	                    $any = true;
	                }
	                foreach ($availableValues as $availableValue) {
	                    $matchingGrade = matchLanguage($acceptedValue, $availableValue);
	                    if ($matchingGrade > 0) {
	                        $q = (string) ($acceptedQuality * $availableQuality * $matchingGrade);
	                        if (!isset($matches[$q])) {
	                            $matches[$q] = array();
	                        }
	                        if (!in_array($availableValue, $matches[$q])) {
	                            $matches[$q][] = $availableValue;
	                        }
	                    }
	                }
	            }
	        }
	    }
	    if (count($matches) === 0 && $any) {
	        $matches = $available;
	    }
	    krsort($matches);
	    return $matches;
	}

	// compare two language tags and distinguish the degree of matching
	function matchLanguage($a, $b) {
	    $a = explode('-', $a);
	    $b = explode('-', $b);
	    for ($i=0, $n=min(count($a), count($b)); $i<$n; $i++) {
	        if ($a[$i] !== $b[$i]) break;
	    }
	    return $i === 0 ? 0 : (float) $i / count($a);
	}
?>