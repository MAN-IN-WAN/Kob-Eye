<?php
	class RedactionCategorie extends genericClass{
		function getModele (){
			$out = Array();
			//skin
			$dirs = $this->listDirectory('Skins');
			if (is_array($dirs))foreach ($dirs as $dir){
				if ($dir!=SHARED_SKIN)
					$out = array_merge($out,$this->listFilesDirectory('Skins/'.$dir.'/Modules/Redaction/Modeles',"$dir > "));
			}
			//Modules
			$out = array_merge($out,$this->listFilesDirectory('Modules/Redaction/Modeles',"module > "));
			//Common
			$out = array_merge($out,$this->listFilesDirectory('Skins/'.SHARED_SKIN.'/Modules/Redaction/Modeles',"commun > "));
			return $out;
		}
		function listFilesDirectory($dirname,$prefixe=""){
			$out=Array();
			if (file_exists($dirname)){
				$dir = opendir($dirname); 
				while($file = readdir($dir)) {
					if($file != '.' && $file != '..' && !is_dir($dirname.'/'.$file) && !preg_match("#^\..*#", $file)){
						preg_match("#(.*)\.(.*)$#", $file,$fi);
						if ($fi[2]=="md")
							$out[$fi[1]] = $prefixe.$fi[1];
					}
				}
				closedir($dir);
			}
			return $out;
		}
		function listDirectory($dirname){
			$out=Array();
			if (file_exists($dirname)){
				$dir = opendir($dirname); 
				while($file = readdir($dir)) {
					if($file != '.' && $file != '..' && is_dir($dirname.'/'.$file)&& !preg_match("#^\..*#", $file)){
						$out[] = $file;
					}
				}
				closedir($dir);
			}
			return $out;
		}
	}
?>