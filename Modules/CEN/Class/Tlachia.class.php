<?php

class Tlachia extends genericClass {
	
	function Save() {
		$zfile = $this->ZipFile; 
		$zok = $zfile != '';
		$id = $this->Id;		
		$old = null;
				
		if($id) { // check if zip has changed
			$old = Sys::getOneData('CEN', "Codex/$id");
			$zok = $zfile != $old->ZipFile;
		}
				
		$ret =  parent::Save();
		if(!$zok) return $ret;
		
		// unzip in tmp dir
		$cwd = getcwd();
		$zfile = "$cwd/$zfile";
		$tmp = "$cwd/Home/tmp/tlachia";
		CEN::rmDir($tmp);
		mkdir($tmp);

		$zip = new ZipArchive;
		$res = $zip->open($zfile);
		if($res === TRUE) $zip->extractTo($tmp);
		$zip->close();
		if($res !== true) {
			$this->addError(array("Message" => "Erreur sur le fichier zip", "Prop" => ""));
			return false;		
		}
		
		// copy Aide
		$dir = "$cwd/Home/2/CEN/Codex/Aide";		
		CEN::rmDir($dir);
		mkdir($dir);
		$fs = array_diff(scandir("$tmp/Aide"), array('..', '.'));
		foreach($fs as $k=>$o) {
			$f = explode('.', $o);
			if(array_search($f[1], ['aes', 'afr', 'aan'], true) === false) continue;
			system("cp $tmp/Aide/$o $dir/$o");
		}
		
		// copy reali
		$dir = "$cwd/Home/2/CEN/Codex/reali";		
		CEN::rmDir($dir);
		mkdir($dir);
		$fs = array_diff(scandir("$tmp/reali"), array('..', '.'));
		foreach($fs as $k=>$o) {
			$f = explode('.', $o);
			$e = $f[1];
			if(substr($e, 0, 2) != "ph") continue;
			$f = str_replace('+', '-', $f[0]);
			if($e == 'pho') $n = $f.'_0.bmp';
			else $n = $f.'_'.substr($e, 2).'.bmp';
			system("cp $tmp/reali/$o $dir/$n");
		}
		
		
		unlink($zfile);
		CEN::rmDir($tmp);
		return $ret;
	}

}
