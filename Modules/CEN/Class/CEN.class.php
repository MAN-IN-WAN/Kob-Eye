<?php

class CEN extends Module {
	
	// description d'une application
	public static function GetPresentation($args) {
		$o = Sys::getOneData('CEN', 'Presentation/Code='.$args['code']);
		switch($args['lang']) {
			case 'es': $pres = $o->TexteEs; break;
			case 'fr': $pres = $o->TexteFr; break;
			case 'en': $pres = $o->TexteEn; break;
		}
		return array('presentation'=>$pres);
	}


	// liste d'entrées du GDN 
	public static function GetGDN($args) {	
		switch($args['mode']) {
			case 'tlachia-anal':
				return Codex::GetAnal($args);
				
			case 'codex':
				return Codex::GetCodex($args);
				
			case 'tlachia-list':
				return Codex::GetList($args);
				
			case 'tlachia':
				return Codex::GetTlachia($args);
				
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
					case 'tlachia':
						$tla = Sys::getOneData('CEN', 'Codex/'.$id);
						return $tla->GetDescr($args);
						
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

//	public static function read_file($f) {
//		$t = '';
//		if($fh = fopen($f, "rb")) {
//			$t = fread($fh, filesize($f));
//			fclose($fh);
//		}
//		return($t);
//	}
	
}