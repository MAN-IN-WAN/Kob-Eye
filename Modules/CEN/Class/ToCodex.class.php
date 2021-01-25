<?php

class ToCodex extends genericClass {

	
	function Save() {
		$cwd = getcwd();
		$basedir = "$cwd/Home/2/CEN/ToCodex";
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
			$old = Sys::getOneData('CEN', "ToCodex/$id");
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
		klog::l(">>>>CSV");

		// remove old dir and create new one
		if($old && $old->Repertoire) CEN::rmDir($basedir.'/'.$old->Repertoire);
		$dir = "$basedir/".$this->Repertoire;
		$s = "mv $basedir/tmp $dir";
		system($s);
		//unlink($zfile);
		
		if($id) {
			$sql = "delete from `##_CEN-ToImage` where ToCodexId=$id";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$GLOBALS['Systeme']->Db[0]->exec($sql);
		}
		$n = 0;
		$ls = explode("\r\n", file_get_contents("$dir/$clef.csv"));
		foreach($ls as $l) {
			if(!$n++ || empty(trim($l))) continue;
			$cs = explode(';', trim($l));
			$img = genericClass::createInstance('CEN', 'ToImage');
			$img->addParent($this);
			$img->Type = $cs[0];
			$img->Nom = $cs[1];
			$img->Jour = $cs[2];
			$img->Nombre = $cs[3];
			$img->Mois = $cs[4];
			$img->DJ = $cs[5];
			$img->D13 = $cs[6];
			$img->DN = $cs[7];
			$img->Volatile = $cs[8];
			$img->D13ene = $cs[9];
			$img->D20ene = $cs[10];
			$img->Treizaine = $cs[11];
			$img->Arbre = isset($cs[12]) ? $cs[12] : '';
			$img->G20ene = isset($cs[13]) ? $cs[13] : '';
			$img->Save();
		}
		return $ret;
	}
	
	static public function GetImageTable($args) {
		$codex = $args['codex'];
		$type = $args['type'];
		$field = ['Jour','Mois','Nombre'][$type];
		
		$sql = "select Nom,$field as img "
			."from `##_CEN-ToImage` i "
			."left join `##_CEN-ToCodex` c on i.ToCodexId=c.Id "
			."where ToCodexId=$codex and Type=$type order by Nom";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
//klog::l($sql);
		$tmp = [];
		foreach($rs as $r) $tmp[$r['Nom']] = ['codex'=>$codex,'dir'=>$field,'role'=>$fld,'img'=>$r['img']];
		return ['success'=>true,'images'=>$tmp];
	}

	static public function GetImages($args) {
		$type2fld = ['A'=>'DJ','B'=>'D13','C'=>'DN','D'=>'D13ene','O'=>'Volatile'];
		$fld2role = ['Jour'=>'Jour','Mois'=>'Mois','Nombre'=>'Nombre','DJ'=>'Dieu du jour','D13'=>'Dieu du treizième','DN'=>'Seigneur de la nuit',
			'D13ene'=>'Dieu de la treizaine','D20ene'=>'Dieu de la vingtaine','Treizaine'=>'Image de la treizaine','Volatile'=>'Volatile','Arbre'=>'Arbre'];
		$type = $args['type'];
		$select = isset($args['select']) ? $args['select'] : '';
		$god = isset($args['god']) ? $args['god'] : '';
		$codex = isset($args['codex']) ? $args['codex'] : '';
		
		switch($type) {
			case 0: $flds = ['Jour']; break;
			case 1: $flds = ['Mois']; break;
			case 2: $flds = ['Nombre']; break;
			case 3: 
				if($select)  $flds = [$type2fld[$select]];
				else $flds = ['DJ','D13','DN','D13ene','D20ene','Treizaine','Volatile','Arbre'];
				break;
		}
		$sql = "select i.ToCodexId";
		if($codex) $sql .= ",t.Id";
		foreach($flds as $fld) $sql .= ",i.$fld";
		$sql .= " from `##_CEN-ToImage` i "
			." left join `##_CEN-ToCodex` c on i.ToCodexId=c.Id ";
			if($codex) $sql .= " left join `##_CEN-Tonalpohua` t on t.Nahuatl=Nom"
			." where i.`Type`=$type ".($god ? "and i.Nom='$god' " : '').($codex ? "and i.ToCodexId=$codex" : '')
			." order by ".($codex ? 't.Id' : 'c.Ordre');
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//klog::l($sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tmp = [];
		$id = 0;
		foreach($rs as $r) {
			$cdx = $r['ToCodexId'];
			if($codex) $id = $r['Id'];
			foreach($flds as $fld) {
				if($r[$fld]) {
					$imgs = explode(',', $r[$fld]);
					foreach($imgs as $img) {
						$tmp[] = ['codex'=>$cdx,'id'=>$id,'dir'=>$fld,'role'=>$fld2role[$fld],'img'=>trim($img)];
					}
				}
			}
		}
		return ['success'=>true, 'images'=>$tmp, 'role'=>($select ? $fld2role[$type2fld[$select]] : '')];
	}

	static public function GetCodex($args) {
		$codex = $args['codex'];
		$days = self::GetImageTable(['codex'=>$codex, 'type'=>0]);
		$months = self::GetImageTable(['codex'=>$codex, 'type'=>1]);
		$number = self::GetImageTable(['codex'=>$codex, 'type'=>2]);
		return array('success'=>true,data=>['days'=>$days['images'],'months'=>$months['images']]);
	}
	
}
