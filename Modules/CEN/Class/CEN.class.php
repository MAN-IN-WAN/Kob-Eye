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
				return array('dictionaries'=>$ret);
				
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
				return array('text'=>$gdn->Commentaires);
				
			case 'norm':
				break;
		}
	}
	

	
}