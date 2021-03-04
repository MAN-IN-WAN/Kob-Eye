<?php

class ToMenu extends genericClass {
	
	function Save() {
		$cwd = getcwd();
		$basedir = "$cwd/Home/2/CEN/ToMenu";
		$zfile = $this->ZipFile; 
		$zok = false;
		$id = $this->Id;		
		$old = null;
		
		$clef = $this->Repertoire;
		if(empty($clef)) {
			$this->addError(array("Message" => "Le répertoire est obligatoire", "Prop" => ""));
			return false;		
		}
		
		if($id) { // check if zip file is present
			$old = Sys::getOneData('CEN', "ToMenu/$id");
			$zok = $zfile != '' && file_exists("$cwd/".$zfile);
		}
				
		$ret =  parent::Save();
		if(!$zok) return $ret;
		
		$zfile = "$cwd/$zfile";
		$tmp = "$basedir/tmp";
		CEN::rmDir($tmp);
		mkdir($tmp);

		$zip = new ZipArchive;
		$res = $zip->open($zfile);
		if($res === TRUE) $zip->extractTo($tmp);
		$zip->close();
		if($res !== true) {
			$this->addError(array("Message" => "Erreur sur le fichier zip", "Prop" => ""));
			unlink($zfile);
			CEN::rmDir($tmp);
			return false;		
		}
		
		// check if cvs exists
		if(!file_exists("$basedir/tmp/$clef.csv")) {
			$this->addError(array("Message" => "Fichier $basedir/tmp/$clef.csv non trouvé", "Prop" => ""));
			unlink($zfile);
			CEN::rmDir($tmp);
			return false;		
		}

		// remove old dir and create new one
		if($old && $old->Repertoire) CEN::rmDir($basedir.'/'.$old->Repertoire);
		$dir = "$basedir/".$this->Repertoire;
		$s = "mv $basedir/tmp $dir";
		system($s);
		//unlink($zfile);
		
		if($id) {
			$sql = "delete from `##_CEN-ToDetail` where ToMenuId=$id";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$GLOBALS['Systeme']->Db[0]->exec($sql);
		}
		$flds = [];
		$cols = 0;
		$n = 0;
		$ls = explode("\r\n", file_get_contents("$dir/$clef.csv"));
		foreach($ls as $l) {
			if(empty(trim($l))) continue;
			if(!$n++) {
				$flds = explode(';', trim($l));
				$cols = count($flds);
				continue;
			}
			$cs = explode(';', trim($l));
			$det = genericClass::createInstance('CEN', 'ToDetail');
			$det->addParent($this);
			$det->TitreFR = $cs[0];
			$det->TitreEN = $cs[1];
			$det->TitreES = $cs[2];
			$det->DetailFR = $cs[3];
			$det->DetailEN = $cs[4];
			$det->DetailES = $cs[5];
			$det->Fichier = $cs[6];
			$det->Ordre = $cs[7];
			$det->Save();
		}
		return $ret;
	}
	
	public static function GetMenus($lang) {
		$sql = "select Id,Tab,Menu$lang as menu,MenuES,Icone from `##_CEN-ToMenu` order by Tab,Ordre";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rup = 0;
		$mnu = [];
		foreach($rs as $r) {
			$tab = $r['Tab'];
			if($rup !== $tab) {
				if($rup) $mnu[$rup] = $t;
				$rup = $tab;
				$t = [];
			}
			$m = $r['menu'];
			if(!$m) $m = $r['MenuES'];
			$t[] = ['id'=>$r['Id'], 'menu'=>$m, 'icon'=>$r['Icone']];
		}
		if($rup) $mnu[$rup] = $t;

		return $mnu;
	}
}