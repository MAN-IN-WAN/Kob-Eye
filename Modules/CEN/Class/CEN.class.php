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
	function GetGDN($args) {
		$gdn = genericClass::createInstance('CEN', 'GDN');
		
		switch($args['mode']) {
			case 'dict':
				$dics = Sys::getData('CEN', 'Dictionnaire');
				$ret = array();
				foreach($dics as $d) $ret[$d->Id] = $d->Nom;
				$sel = isset($_SESSION['Dictionaries']) ? $_SESSION['Dictionaries'] : false;
				return array('dictionaries'=>$ret,'selected'=>$sel);
				
			case 'select':
				$_SESSION['Dictionaries'] = $args['selected'];
				return array('selected'=>$_SESSION['Dictionaries']);
				
			case 'list':
				return $gdn->GetList($args);
				
			case 'trad':
				return $gdn->GetGDN($args);
				
			case 'pres':
				$dic = genericClass::createInstance('CEN', 'Dictionnaire');
				$dic->initFromId($args['id']);
				switch($args['lang']) {
					case 'es': $pres = $dic->PresentationEs; break;
					case 'fr': $pres = $dic->PresentationFr; break;
					case 'en': $pres = $dic->PresentationEn; break;
				}
				return array('text'=>$pres);
				
			case 'comm':
				$gdn->initFromId($args['id']);
				$com = $gdn->Commentaires;
				$com = preg_replace('/ *\/\/ */', '<br />', $com);
				$pos = strpos($com, '§ ');
				while($pos !== false) {
					$com = preg_replace('/§ /', '<i>', $com, 1);
					$com = preg_replace('/ §/', '</i>', $com, 1);
					$pos = strpos($com, '§ ');
				}
				return array('text'=>$com);
				
			case 'norm':
				break;
		}
	}
	

	
}