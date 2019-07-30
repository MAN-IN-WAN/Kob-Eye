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
			case 'dsel':
				$_SESSION['dictionaries'] = $args['dic'];
				return true;
				
			case 'dict':
				$dics = Sys::getData('CEN', 'Dictionnaire');
				$dicId= array();
				$dic = array();
				foreach($dics as $d) {
					$id = $d->Id;
					$s = !$sel || strpos($sel,",$id,") ? 1 : 0;
					$dic[] = array('id'=>$d->Id, 'title'=>$d->Nom, 'selected'=>$s);
					$dicId[$d->Id] = $d->Nom;
				}
				return array('dictionariesId'=>$dicId, 'dictionaries'=>$dic, 'select'=>isset($_SESSION['dictionaries']) ? $_SESSION['dictionaries'] : '');
				
			case 'list':
				return GDN::GetList($args);
				
			case 'trad':
				return GDN::GetGDN($args);
				
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
				return GDN::GetComments($args);
				
			case 'norm':
				break;
			
			case 'temoa':
				return Temoa::GetList($args);
		}
	}

	
	
}