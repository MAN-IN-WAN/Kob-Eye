<?php

class CEN extends Module {

	// liste d'entrées du CEN 
	public static function GetCEN($args) {	
		switch($args['mode']) {
			case 'chacha':
				return Chachalaca::Suff($args);

			case 'tlachia-anal':
				return Codex::GetAnal($args);
				
			case 'codex':
				return Codex::GetCodex($args);
				
			case 'tlachia-list':
				return Codex::GetList($args);
				
			case 'tlachia':
				return Codex::GetTlachia($args);
				
			case 'tlachia-real':
				return Codex::GetReal($args);
				
			case 'genor':
				return Temoa::GetGenor($args);
				
			case 'temoa':
				return Temoa::GetTargets($args);
				
			case 'docs':
				return Temoa::GetDocs();
				
			case 'doc':
				return Temoa::GetDocument($args);
				
			case 'notes':
				return Temoa::GetNotes($args);

			case 'picts':
				return Temoa::GetPicts($args);

			case 'temoa-trad':
				return Temoa::GetTraduction($args);
				
			case 'dict':
				return Gdn::GetDics();
				
			case 'list':
				return GDN::GetList($args);
				
			case 'trad':
				return GDN::GetGDN($args);
				
			case 'pres':
				$id = $args['id'];
				$lang = $args['lang'];
				$lang = strtoupper(substr($lang, 0, 1)).substr($lang, 1, 1);
				
				switch($args['pres']) {
					case 'chacha':
						return Chachalaca::GetGrammar($args);
						
					case 'tlachia':
						$tla = Sys::getOneData('CEN', 'Codex/'.$id);
						return $tla->GetDescr($args);
						
					case 'term':
						$tla = Sys::getOneData('CEN', 'Codex/'.$id);
						return $tla->GetTerm($args);

					case 'pres':
						$o = Sys::getOneData('CEN', 'Presentation/Code='.$args['id']);
						switch($args['type']) {
							case 'intro': $type = 'Texte'; break;
							case 'pres': $type = 'Present'; break;
							case 'thanks': $type = 'Remer'; break;
							case 'credits': $type = 'Credit'; break;
							case 'help': $type = 'Aide'; break;
						}
						$type .= $lang;
						return array('text'=> $o->$type);
						
					case 'dic':	$dic = Sys::getOneData('CEN', 'Dictionnaire/'.$id);	break;
					case 'doc':	$dic = Sys::getOneData('CEN', 'Temoa/'.$id);	break;
					case 'comm': return GDN::GetComments($args);
					case 'doc-read':
						$dic = Sys::getOneData('CEN', 'Temoa/'.$id);	
						return array('text'=>$dic->DocHtml);
				}
				switch($args['lang']) {
					case 'es': $pres = $dic->PresentationEs; break;
					case 'fr': $pres = $dic->PresentationFr; break;
					case 'en': $pres = $dic->PresentationEn; break;
				}
				if(empty($pres)) $pres = $dic->PresentationEs;
				return array('text'=>$pres);
				
			case 'norm':
				break;
			
			case 'lang';
				$lang = Sys::getOneData('CEN', 'Regle/Code=Langue');
				$lang = file_get_contents($lang->FilePath);
				$lang = utf8_encode($lang);
				$lang = str_replace("\r\n", "\n", $lang);
				return array('lang'=>$lang);

			default:
				return array('error'=>'mode inconnu:'.$args['mode']);
		}
	}

	public static function rmDir($dir, $root=true) {
		$cwd = getcwd();
		if(substr($dir, 0, strlen($cwd)+6) !== $cwd.'/Home/') return false;
		
		$files = array_diff(scandir($dir), array('.', '..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file")) ? self::rmDir("$dir/$file") : unlink("$dir/$file"); 
		}
		if($root) rmdir($dir); 
	}


	public static function removeAccents($str) {
		static $map = [
        // single letters
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'ą' => 'a',
        'å' => 'a',
        'ā' => 'a',
        'ă' => 'a',
        'ǎ' => 'a',
        'ǻ' => 'a',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Ą' => 'A',
        'Å' => 'A',
        'Ā' => 'A',
        'Ă' => 'A',
        'Ǎ' => 'A',
        'Ǻ' => 'A',


        'ç' => 'c',
        'ć' => 'c',
        'ĉ' => 'c',
        'ċ' => 'c',
        'č' => 'c',
        'Ç' => 'C',
        'Ć' => 'C',
        'Ĉ' => 'C',
        'Ċ' => 'C',
        'Č' => 'C',

        'ď' => 'd',
        'đ' => 'd',
        'Ð' => 'D',
        'Ď' => 'D',
        'Đ' => 'D',


        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ę' => 'e',
        'ē' => 'e',
        'ĕ' => 'e',
        'ė' => 'e',
        'ě' => 'e',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ę' => 'E',
        'Ē' => 'E',
        'Ĕ' => 'E',
        'Ė' => 'E',
        'Ě' => 'E',

        'ƒ' => 'f',


        'ĝ' => 'g',
        'ğ' => 'g',
        'ġ' => 'g',
        'ģ' => 'g',
        'Ĝ' => 'G',
        'Ğ' => 'G',
        'Ġ' => 'G',
        'Ģ' => 'G',


        'ĥ' => 'h',
        'ħ' => 'h',
        'Ĥ' => 'H',
        'Ħ' => 'H',

        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ĩ' => 'i',
        'ī' => 'i',
        'ĭ' => 'i',
        'į' => 'i',
        'ſ' => 'i',
        'ǐ' => 'i',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ĩ' => 'I',
        'Ī' => 'I',
        'Ĭ' => 'I',
        'Į' => 'I',
        'İ' => 'I',
        'Ǐ' => 'I',

        'ĵ' => 'j',
        'Ĵ' => 'J',

        'ķ' => 'k',
        'Ķ' => 'K',


        'ł' => 'l',
        'ĺ' => 'l',
        'ļ' => 'l',
        'ľ' => 'l',
        'ŀ' => 'l',
        'Ł' => 'L',
        'Ĺ' => 'L',
        'Ļ' => 'L',
        'Ľ' => 'L',
        'Ŀ' => 'L',


        'ñ' => 'n',
        'ń' => 'n',
        'ņ' => 'n',
        'ň' => 'n',
        'ŉ' => 'n',
        'Ñ' => 'N',
        'Ń' => 'N',
        'Ņ' => 'N',
        'Ň' => 'N',

        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ð' => 'o',
        'ø' => 'o',
        'ō' => 'o',
        'ŏ' => 'o',
        'ő' => 'o',
        'ơ' => 'o',
        'ǒ' => 'o',
        'ǿ' => 'o',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ō' => 'O',
        'Ŏ' => 'O',
        'Ő' => 'O',
        'Ơ' => 'O',
        'Ǒ' => 'O',
        'Ǿ' => 'O',


        'ŕ' => 'r',
        'ŗ' => 'r',
        'ř' => 'r',
        'Ŕ' => 'R',
        'Ŗ' => 'R',
        'Ř' => 'R',


        'ś' => 's',
        'š' => 's',
        'ŝ' => 's',
        'ş' => 's',
        'Ś' => 'S',
        'Š' => 'S',
        'Ŝ' => 'S',
        'Ş' => 'S',

        'ţ' => 't',
        'ť' => 't',
        'ŧ' => 't',
        'Ţ' => 'T',
        'Ť' => 'T',
        'Ŧ' => 'T',


        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ũ' => 'u',
        'ū' => 'u',
        'ŭ' => 'u',
        'ů' => 'u',
        'ű' => 'u',
        'ų' => 'u',
        'ư' => 'u',
        'ǔ' => 'u',
        'ǖ' => 'u',
        'ǘ' => 'u',
        'ǚ' => 'u',
        'ǜ' => 'u',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ũ' => 'U',
        'Ū' => 'U',
        'Ŭ' => 'U',
        'Ů' => 'U',
        'Ű' => 'U',
        'Ų' => 'U',
        'Ư' => 'U',
        'Ǔ' => 'U',
        'Ǖ' => 'U',
        'Ǘ' => 'U',
        'Ǚ' => 'U',
        'Ǜ' => 'U',


        'ŵ' => 'w',
        'Ŵ' => 'W',

        'ý' => 'y',
        'ÿ' => 'y',
        'ŷ' => 'y',
        'Ý' => 'Y',
        'Ÿ' => 'Y',
        'Ŷ' => 'Y',

        'ż' => 'z',
        'ź' => 'z',
        'ž' => 'z',
        'Ż' => 'Z',
        'Ź' => 'Z',
        'Ž' => 'Z',


        // accentuated ligatures
        'Ǽ' => 'A',
        'ǽ' => 'a',
		];
		return strtr($str, $map);
	}
	
}