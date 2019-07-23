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
		
		switch($args['mode']) {
			case 'dict':
				$dics = Sys::getData('CEN', 'Dictionnaire');
				$sel = isset($_SESSION['Dictionaries']) ? $_SESSION['Dictionaries'] : false;
				$dicId= array();
				$dic = array();
				foreach($dics as $d) {
					$dic[] = array('id'=>$d->Id, 'title'=>$d->Nom, 'selected'=>1);
					$dicId[$d->Id] = $d->Nom;
				}
				return array('dictionariesId'=>$dicId, 'dictionaries'=>$dic);
				
			case 'select':
				$_SESSION['Dictionaries'] = $args['selected'];
				return array('selected'=>$_SESSION['Dictionaries']);
				
			case 'list':
				$gdn = genericClass::createInstance('CEN', 'GDN');
				return $gdn->GetList($args);
				
			case 'trad':
				$gdn = genericClass::createInstance('CEN', 'GDN');
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
				$gdn = genericClass::createInstance('CEN', 'GDN');
				return $gdn->GetComments($args);
				
			case 'norm':
				break;
		}
	}
	

	
}