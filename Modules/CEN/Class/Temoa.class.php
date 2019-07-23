<?php

class Temoa extends genericClass {

	// importation d'un dictionnaire
	function Save() {
		if($this->ZipFile != '') {
			$uid = Sys::$User->Id;
			$dir = "./Home/$uid/CEN/Temoa/".$this->Code;
			mkdir($dir);
			$zip = new ZipArchive;
			$res = $zip->open($this->ZipFile);
			if($res === TRUE) {
				$zip->extractTo($dir);
				$zip->close();
				$f = "$dir/$this->Code";
				$this->Nahuatl = file_get_contents("$f.rtf");
				$this->Trad1 = file_exists("$f".'trad.rtf') ? file_get_contents("$f".'trad.rtf') : '';
				$this->Trad2 = file_exists("$f".'trad2.rtf') ? file_get_contents("$f".'trad2.rtf') : '';
				$this->importRtf($f.'_esp.rtf', 'PresentationEs'); 
				$this->importRtf($f.'_fra.rtf', 'PresentationFr'); 
				$this->importRtf($f.'_ang.rtf', 'PresentationEn'); 
				unlink($this->ZipFile);
				$this->ZipFile = "";
			}
			else $zip->Close();
		}
		return parent::Save();
	}
	
	private function importRtf($file, $field) {
		require_once ('Class/Lib/rtf-html-php.php');
		
		$this->{$field} = '';
		if(file_exists($file)) {
			$reader = new RtfReader();
			$rtf = file_get_contents($file);
			$result = $reader->Parse($rtf);
			if($result) {
				$formatter = new RtfHtml();
				$this->{$field} = $formatter->Format($reader->root);
			}
		} 
	}
	
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


}