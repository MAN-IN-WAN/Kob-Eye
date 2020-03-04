<?php

class Codex extends genericClass {

	// structurer l'import :
	var $imports = [
		'CODICES'=>"`kob-CEN-Codex`|(`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Archive`, `Clef`, `Code`, `Titre`, `Presentation`, `Etude`, `Realite`, `Dictionnaire`, `Bibliographie`, `Remerciements`, `Credit`, `Repertoire`)",
		'CITATION'=>"`kob-CEN-Citation`|(CodexId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Cote`, `Citation`, `Source`, `Pages`)",
		'ELEMENT'=>"`kob-CEN-Element`|(CodexId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Theme`, `Cote`, `Element`, `Nahuatl`, `Catalogue`, `MisePage`, `Repetition`, `Reali`, `Figur`, `Couleur`, `Variante`, `Orientation`, `Trait`, `Interieur`, `Hybride`)",
		'FORME'=>"`kob-CEN-Forme`|(CodexId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Element`, `Theme`, `Forme`)",
		'GLYPHE'=>"`kob-CEN-Glyphe`|(CodexId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Element`, `Cote`, `OHAA`, `OVAA`, `RLAA`, `RHAA`, `Nom`, `Numero`, `Recit`, `Groupe`, `Type`, `Classe`, `NbElement`, `Lecture`, `MotNouveau`, `SylPers`, `ValSupl`, `Divers`, `OrientaG`, `OrientaC`, `Position`, `Liens`, `LiensG`, `LiensP`, `Contact`, `CotePers`, `AutrePers`, `Composition`, `IntegrInt`, `IntegrExt`, `Multipli`, `Superpo`, `Dimensions`, `Noir`, `Hachure`, `Couleurs`, `SensDeLec`, `SendLecPer`, `Hauteur`, `Largeur`, `Ref`, `Mains`, `Fond`)",
		"MOT"=>"`kob-CEN-Mot`|(CodexId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Paleo`, `Trans`)",
		"NOUVEAU"=>"`kob-CEN-Nouveau`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Lecture`, `MotNouveau`)", 
		"PCITATIO"=>"`kob-CEN-PCitation`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Citation`, `Source`, `Pages`)", 
		"PERSONA"=>"`kob-CEN-Personnage`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Nom`, `Cote`, `Ref`, `Numero`, `Recit`, `Groupe`, `Sexe`, `Age`, `NbElement`, `Elements`, `Positions`, `Altitude`, `Bras`, `Pieds`, `OrientaP`, `OrientaC`, `Liens`, `LiensG`, `AutreGly`, `LiensP`, `Lecture`, `Hauteur`, `Largeur`, `Divers`, `Couleurs`, `MotNouveau`, `SylsPers`, `ValsSupl`, `SensDeLec`, `OHAA`, `OVAA`, `Mains`, `Fond`, `CotePers`)",
		"PLANCHE"=>"`kob-CEN-Planche`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `ABComplete`)",
		"PNOUVEAU"=>"`kob-CEN-PNouveau`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Lecture`, `MotNouveau`)",
		"PSYLPERS"=>"`kob-CEN-PSylPers`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Lecture`, `SylPerso`)",
		"PVALEUR"=>"`kob-CEN-PValeur`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Element`, `Valeur`, `Types`, `Lecture`, `Theme`, `Catalogue`, `Place`, `Niveau`, `Syllabe`, `Determinatif`, `Ordre`)",
		"PVALSUPL"=>"`kob-CEN-PValSupl`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Lecture`, `ValSupl`)",
		"RACINE"=>"`kob-CEN-Racine`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Mot2`, `Decomposition`, `Racine1`, `Racine2`, `Racine3`, `Racine4`, `Racine5`)",
		"SENS"=>"`kob-CEN-Sens`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Element`, `Sens`, `Sens2`, `Sens3`)",
		"SYLPERS"=>"`kob-CEN-SylPers`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Lecture`, `SylPerso`)",
		"TERMINO"=>"`kob-CEN-Termino`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Terme`, `Fichier`, `Espagnol`, `Anglais`)",
		"THEME"=>"`kob-CEN-Theme`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Element`, `Theme`, `Sens`, `Page`, `Group`, `TiTheme`, `TiCategorie`, `Numero`)",
		"VALEUR"=>"`kob-CEN-Valeur`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Element`, `Valeur`, `Types`, `Lecture`, `Theme`, `Catalogue`, `Place`, `Niveau`, `Syllabe`, `Determinatif`, `Ordre`)",
		"VALSUPL"=>"`kob-CEN-ValSupl`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `Lecture`, `ValSupl`)",
		"ZONE"=>"`kob-CEN-Zone`|(`CodexId`, `tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`, `Cote`, `ABComplete`)"
	];
	// find $tmp/$clef $lower 
	var $lower = ' -type f \( -iname "*.aes" -o -iname "*.afr" -o -iname "*.aan" -o -iname "*.jpg" -o -iname "*.bmp"  -o -iname "*.ph*" -o -iname "*.mp3" -o -iname "*.esp" -o -iname "*.fra" -o -iname "*.ang"  \) -exec sh -c \' '.
		'a=$(echo "$0" | sed -r "s/([^\/]*)\$/\L\0/" | sed -r "s/\+./-./g"); '.
		'[ "$a" != "$0" ] && mv "$0" "$a" \' {} \;';
	// $rsync $tmp/$clef $dir
	var $rsync = 'rsync -am --include="*/" --include="*.bmp" --include="*.jpg" --include="*.mp3" --include="*.esp" --include="*.fra" '.
		'--include="*.ang" --include="*.ph*" --include="*.aes" --include="*.afr" --include="*.aan" --exclude="*"';

	
	function Save() {
		$zfile = $this->ZipFile; 
		$zok = $zfile != '';
		$id = $this->Id;		
		$old = null;
		
		// unzip in tmp dir, read dir in CODICES.SQL, create new dir
		$clef = $this->Repertoire;
		if(empty($clef)) {
			$this->addError(array("Message" => "Le répertoire est obligatoire", "Prop" => ""));
			return false;		
		}
		
		if($id) { // check if zip has changed
			$old = Sys::getOneData('CEN', "Codex/$id");
			$zok = $zfile != $old->ZipFile;
		}
				
		$ret =  parent::Save();
		if(!$zok) return $ret;
		
		$cwd = getcwd();
		$zfile = "$cwd/$zfile";
		$tmp = "$cwd/Home/tmp/codex";
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

		$cd = file_get_contents("$tmp/$clef/CODICES.SQL");
		$cd = explode("', '", $cd)[11];
		$dir = trim(explode("'", $cd)[0]);
		// TODO dir # clef
		if($clef !== $dir) {
			$this->addError(array("Message" => "Le répertoire est différent de celui du fichier CODICES", "Prop" => ""));
			return false;		
		}
		
		// remove old dir and create new one
		$dir = "$cwd/Home/2/CEN/Codex/$dir";		
		
		if($old && !empty($old->Repertoire)) CEN::rmDir("$cwd/Home/2/CEN/Codex/".$old->Repertoire);
		CEN::rmDir($dir);
		mkdir($dir);
		
		// lower and replace + by -
		$s = "find $tmp/$clef/ ".$this->lower;
		system($s);
		
		// copy needed files
		$s = $this->rsync." $tmp/$clef/* $dir";
		system($s);
		
		$oid = $id;
		foreach($this->imports as $table => $val) {
		    $cx = $table == 'CODICES'; 
			$val = explode('|',$val);
			
			$v = file_get_contents("$tmp/$clef/$table".($cx ? '.SQL' : '.EXP'));
			$v = utf8_encode($v);
			
		    if($cx) {
				$v = "(0,2,0,2,2,2,7,7,7,".substr($v, 1);
			}
			else {
				$ar = explode("\r\n", $v);
				$v = "";
				foreach($ar as $a) {
					if($a == '') continue;
					$a = str_replace("'","''",$a);
					$a = "($id,0,2,0,2,0,0,7,7,7,'".preg_replace("/ *$/", "", preg_replace("/ *\t/", "','", $a))."')";
					if($v) $v .= ",\n";
					$v .= $a;
				}
			}	    

			if($oid) {
				$sql = "delete from ".$val[0]." where ".($cx ? 'Id=' : 'CodexId=').$oid;
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$GLOBALS['Systeme']->Db[0]->exec($sql);
			}

			$sql = "INSERT INTO ".$val[0]." ".$val[1]." VALUES ".$v;
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$GLOBALS['Systeme']->Db[0]->exec($sql);
			if($cx) {
				$id = $GLOBALS['Systeme']->Db[0]->lastInsertId(); 
				if($oid) {
					$sql = "update ".$val[0]." set Id=$oid where Id=$id";
					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
					$GLOBALS['Systeme']->Db[0]->exec($sql);
					$id = $oid;
				}
			}
		}
		unlink($zfile);
		CEN::rmDir($tmp);
		return $ret;
	}
	
	static function GetCodex($args) {
		$type = $args['type'];
		$id = isset($args['id']) ? $args['id'] : '';
		$ext = isset($args['ext']) ? $args['ext'] : '';
		$ln = strlen($ext);

		$dic = array();
		$dicId = array();		
		switch($type) {
			case 'codex':
				$dics = Sys::getData('CEN', 'Codex', 0, 999, 'ASC', 'Code');
				foreach($dics as $d) {
					$id = $d->Id;
					$dir = self::getDir($d->Repertoire);
					$dic[] = array('id'=>$id, 'code'=>$d->Code, 'title'=>strtolower($d->Titre), 'selected'=>true, 'imgSel'=>false, 
						'dir'=>$dir, 'img'=>'img_01.bmp');
					$dicId[$id] = array('title'=>strtolower($d->Titre), 'dir'=>$dir);
				}
				return array('codex'=>$dic, 'codexId'=>$dicId); 
				
			case 'planche':
				$sql = "select pCodexId,Id,Cote from `##_CEN-Zone` where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);

				$dics = Sys::getData('CEN', 'Planche/CodexId='.$id, 0, 999, 'ASC', 'Cote');
				foreach($dics as $d) {
					$dic[] = array('codexId'=>$d->CodexId, 'id'=>$d->Id, 'cote'=>$d->Cote); //, 'img'=>self::getImg($d->Cote, 'jpg'));
				}
				return array('planches'=>$dic);
				
			case 'zone':
				$sql = "select CodexId,Id,Cote from `##_CEN-Zone` where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$dic = array();
				foreach($pdo as $d) {
					$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote'])); //, 'img'=>self::getImg($d->cote, 'jpg'));
				}
				return array('zones'=>$dic);

			case 'glyphe':
				$whr = "where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per, 'whr'=>$whr);
				
			case 'element':
				$whr = "where e.CodexId=$id and e.Cote='$ext' order by e.Theme";
				$dic = self::getElementBasic($whr);
				return array('elements'=>$dic);
		}
	}
	
	static private function getGlyphe($tbl, $whr) {
		$typ = $tbl == 'Glyphe' ? 'Type' : "'person' as Type";
		$sql = "select CodexId,Id,Cote,Lecture,$typ from `##_CEN-$tbl` $whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$gly = array();
		foreach($pdo as $d) {
			$gly[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'type'=>$d['Type'], 'lecture'=>trim($d['Lecture']));
		}
		return $gly;
	}
	
	static private function getElementBasic($whr) {
		$sql = "select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,ifnull(v.Valeur,p.Valeur) as Valeur,f.Forme ".
			"from `##_CEN-Element` e ".
			"left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element ".
			"left join `##_CEN-Valeur` v on v.CodexId=e.CodexId and v.Cote=e.Cote and v.Theme=e.Theme ".
			"left join `##_CEN-PValeur` p on p.CodexId=e.CodexId and p.Cote=e.Cote and p.Theme=e.Theme ".
			"left join `##_CEN-Forme` f on f.CodexId=e.CodexId and f.Theme=e.Theme ".
			"$whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	

	static private function getElementValeur($whr) {
		$sel = "select e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,v.Valeur,f.Forme from ";
		$sel1 = "v inner join `##_CEN-Element` e on e.CodexId=v.CodexId and e.Cote=v.Cote and e.Theme=v.Theme ".
			"left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element ".
			"left join `##_CEN-Forme` f on f.CodexId=e.CodexId and f.Theme=e.Theme ".
			"$whr";
		$sql = "$sel `##_CEN-Valeur` $sel1 union $sel `##_CEN-PValeur` $sel1 order by codexId,Valeur,Element";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	
	
	static function getElementForme($whr) {
		$sql = "select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,ifnull(v.Valeur,p.Valeur) as Valeur,f.Forme ".
			"from `##_CEN-Forme` f ".
			"inner join `##_CEN-Element` e on e.CodexId=f.CodexId and e.Theme=f.Theme ".
			"left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element ".
			"left join `##_CEN-Valeur` v on v.CodexId=e.CodexId and v.Cote=e.Cote and v.Theme=e.Theme ".
			"left join `##_CEN-PValeur` p on p.CodexId=e.CodexId and p.Cote=e.Cote and p.Theme=e.Theme ".
			"$whr order by f.CodexId,e.Cote";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	

	static function getElementSens($whr) {
		$sql = "select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,ifnull(v.Valeur,p.Valeur) as Valeur,f.Forme ".
			"from `##_CEN-Sens` s ".
			"inner join `##_CEN-Element` e on e.CodexId=s.CodexId and e.Element=s.Element ".
			"left join `##_CEN-Valeur` v on v.CodexId=e.CodexId and v.Cote=e.Cote and v.Theme=e.Theme ".
			"left join `##_CEN-PValeur` p on p.CodexId=e.CodexId and p.Cote=e.Cote and p.Theme=e.Theme ".
			"left join `##_CEN-Forme` f on f.CodexId=e.CodexId and f.Theme=e.Theme ".
			"$whr order by s.CodexId,e.Cote";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	

	static private function getCount(&$array, $table, $alias, $where) {
		$sql = "select CodexId,count(*) as cnt from `##_CEN-$table` $alias $where";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cnt = array();
		foreach($pdo as $d) $cnt[$d['CodexId']] = $d['cnt'];
		foreach($array as &$a) $a['count'] = $cnt[$a['codexId']]; 
	}

	private static function getDir($dir) {
		return "/Home/2/CEN/Codex/$dir/";
	}
	
	private static function getCodexDir($id) {
		$cx = Sys::getOneData('CEN', 'Codex/'.$id);
		return self::getDir($cx->Repertoire);
	}
	
	private static function getImg($code, $ext) {
		return str_replace('+', '-', strtolower($code)).'.'.$ext;
	}

	
	static private function wordList($word, $sql) {
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql)." order by word limit 15";
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('word'=>$word, 'words'=>$list, 'sql'=>$sql);		
	}
	
	static function GetReal($args) {
		$theme = $args['theme'];
		$t = str_replace('.', '_', $theme);
		$dir = 'Home/2/CEN/Codex/reali';
		$picts = array();
		$fs = glob("$dir/$t*.bmp");
		foreach($fs as $k=>$f) {
			if(strpos($f, 'limit') === false) $picts[] = array('pict'=>$f, 'folio'=>$theme, 'descr'=>'');
		}
		return array('dir'=>$dir, 'picts'=>$picts);
	}
	

	// result
	static function GetTlachia($args) {
		$word = trim(strtolower($args['word']));
		$cdx = $args['codex'];
		$list = $args['list'] == 'true';
		
		if($args['nah'] && $args['norma']) $word = GDN::Normalize($word);

		$cond = json_decode($args['cond']);
		if($cdx == 'all' || $cdx == '' || $cdx == 'null') $cdx = '';
		
		$type = isset($args['type']) ? $args['type'] : $cond->type;
		$gly = $cond->glyphe;
		$elm = $cond->element;
		
		$mode = '';
		if($type == 'glyphe' && $gly == 'cote')
			$mode = 'start';
		elseif($type == 'element' && $elm == 'theme')
			$mode = 'start';
		else $mode = $cond->mode;
		
		switch($mode) {
			case 'start': $mode = "like '$word%'"; break;
			case 'all': $mode = "= '$word'"; break;
			default: $mode = "like '%$word%'"; break;
		}

		switch($type) {				
			case 'glyphe':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				
				if($gly == 'multiple') {
					if($list) {
						$sql = "select distinct Element as word from `##_CEN-Element` where $cdx Element like '$word%'";
						return self::wordList($word, $sql);
					}
					$mul = $cond->multiple;
					if(!count($mul)) return array('glyphes'=>[], 'personnes'=>[]);
					$whr = "";
					foreach($mul as $m) {
						$whr .= $whr ? " and" : "where $cdx";
						$whr .= " Element like '% $m,%'";
					}
					$whr .= " order by CodexId,Cote";

				}
				else {
					$fld = $gly == 'cote' ? 'Cote' : 'Lecture';
					$whr = "where $cdx $fld $mode";
					if($list) {
						$sql = "select distinct $fld as word from `##_CEN-Glyphe` $whr ".
							"union select distinct $fld as word from `##_CEN-Personnage` $whr ";
						return self::wordList($word, $sql);
					}
					$whr .= " group by CodexId order by CodexId,Cote";
				}
				
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				self::getCount($gly, 'Glyphe', '', $whr);
				self::getCount($per, 'Personnage', '', $whr);
				return array('word'=>$word, 'glyphes'=>$gly, 'personnes'=>$per, 'w'=>$whr);

			case 'element':			
				if($list) {
					if($cdx) $cdx = "a.CodexId in ($cdx) and";
					switch($elm) {
						case 'designation': 
							$sql = "select distinct a.Element as word from `##_CEN-Element` a where $cdx a.Element $mode";
							return self::wordList($word, $sql);
						case 'theme':
							$fld = $args['lang'] == 'fr' ? 'Sens2' : 'Sens';
							$sql = "select distinct concat(a.Theme,' <i>',a.Element,'</i> ',b.$fld) as word ".
								"from `##_CEN-Element` a left join `##_CEN-Sens` b on b.CodexId=a.CodexId and b.Element=a.Element ".
								"where $cdx a.Theme $mode";
							return self::wordList($word, $sql);
						case 'valeur':
							$sel = "select distinct a.Valeur as word from";
							$sel1 = "a inner join `##_CEN-Element` e on e.CodexId=a.CodexId and e.Theme=a.Theme ";
							$sel1 .= "where $cdx a.Valeur $mode";
							$sql = "$sel `##_CEN-Valeur` $sel1 union $sel `##_CEN-PValeur` $sel1 ";
							return self::wordList($word, $sql);
						case 'forme':
							$sql = "select distinct a.Forme as word from `##_CEN-Forme` a inner join `##_CEN-Element` e ".
								"on e.CodexId=a.CodexId and e.Theme=a.Theme where $cdx cast(a.Forme as unsigned) = cast('$word' as unsigned) ";
							return self::wordList($word, $sql);
						case 'traduction':
							$fld = $args['lang'] == 'fr' ? 'Sens2' : 'Sens';
							$sql = "select distinct a.$fld as word from `##_CEN-Sens` a inner join `##_CEN-Element` e ".
								"on e.CodexId=a.CodexId and e.Element=a.Element where $cdx a.$fld $mode";
							return self::wordList($word, $sql);
					}
				}

				$whr = "where ".($cdx ? "e.CodexId in ($cdx) and" : "");
				$ord = '';
				$sql = '';
				
				switch($elm) {
					case 'designation': 
						$whr .= " e.Element $mode"; $ord = "e.Element,e.CodexId,e.Cote"; break;
					case 'theme':
						$whr .= " e.Theme $mode"; $ord = "e.Theme,e.CodexId,e.Cote "; break;
					case 'valeur':
						$grp = $cond->elements ? '' : "group by e.CodexId,v.Valeur,e.Element";
						$whr .= " v.Valeur $mode $grp";
						$ord = "v.Valeur,e.CodexId,e.Cote";
						return array('word'=>$word, 'elements'=>self::getElementValeur($whr));
					case 'forme':
						$grp = $cond->elements ? '' : "group by f.CodexId,f.Forme,e.Element";
						$whr .= " cast(f.Forme as unsigned) = cast('$word' as unsigned) $grp";
						$ord = "e.CodexId,e.Cote";
						return array('word'=>$word, 'elements'=>self::getElementForme($whr));
					case 'traduction':
						$fld = $args['lang'] == 'fr' ? 'Sens2' : 'Sens';
						$grp = $cond->elements ? '' : "group by f.CodexId,s.$fld,e.Element";
						$whr .= " s.$fld $mode $grp";
						$ord = "$fld,e.CodexId,e.Cote";
						return array('word'=>$word, 'elements'=>self::getElementSens($whr));
				}


				
				$grp = $cond->elements ? '' : 'group by e.CodexId';
				$whr .= " $grp order by $ord";
				
				$dic = self::getElementBasic($whr);
				if($grp) self::getCount($dic, 'Element', 'e', $whr);
				return array('word'=>$word, 'elements'=>$dic);

				
			case 'glyphe-elem':
				$el = $args['element'];
				if($cdx) {
					$whr = "where CodexId in ($cdx) and Element like '% $el,%' order by CodexId,Cote";
				}
				else {
					$id = $args['id'];
					$whr = "where CodexId=$id and Element like '% $el,%' order by CodexId,Cote";
				}
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				//self::getCount($dic, 'Element', 'e.', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);

			case 'glyphe-detail':
				$cote = $args['cote'];
				$id = $args['id'];
				$them = $args['theme'];
				$sql = "
select Citation,Source,Pages from `##_CEN-Citation` where CodexId=$id and Cote='$cote'
union all select Citation,Source,Pages from `##_CEN-PCitation` where CodexId=$id and Cote='$cote'
";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$cit = array();
				foreach($pdo as $p)	$cit[] = array('citation'=>$p['Citation'],'source'=>$p['Source'],'page'=>$p['Pages']);

				$sql = "
select ValSupl from `##_CEN-ValSupl` where CodexId=$id and Cote='$cote'
union select ValSupl from `##_CEN-PValSupl` where CodexId=$id and Cote='$cote'
";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$valSup = '';
				foreach($pdo as $p)	{
					if($valSup) $valSup .=', ';
					$valSup .= $p['ValSupl'];
				}


				$whr = "where e.CodexId=$id and e.Cote='$cote' order by e.Theme";
				$elm = self::getElementBasic($whr);
				return array('citations'=>$cit, 'elements'=>$elm, 'valSup'=>$valSup);

			case 'glyphe-lecture':
				$org = json_decode($args['glyphe']);
				$cid = $org->codexId;
				$gid = $org->id;
				$tbl = $org->type == 'person' ? 'Personne' : 'Glyphe';
				$whr = "where CodexId=$cid and Lecture $mode and Id<>$gid order by Cote";
				$gly = self::getGlyphe($tbl, $whr);
				return array('glyphe'=>$gly);
				
//			case 'element-detail':
//				$lang = $args['lang'];
////				$elem = $args['element'];
//				$them = $args['theme'];
//				$id = $args['id'];
//				$sql = "
//select distinct Valeur from `##_CEN-Valeur` where CodexId=$id and Theme='$them'
//union select distinct Valeur from `##_CEN-PValeur` where CodexId=$id and Theme='$them'
//";
//				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
//				$val = '';
//				foreach($pdo as $p)	{
//					if($val) $val .=', ';
//					$val .= $p['Valeur'];
//				}
//				return array('valeur'=>$val,'sql'=>$sql);
		}

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql)." order by word limit 15";
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('word'=>$word, 'words'=>$list, 'sql'=>$sql);		
	}
	
	function GetDescr($args) {
		$dir = '/Home/2/CEN/Codex/'.$this->Repertoire.'/textes/';
		$lix = array_search($args['lang'], ['es','fr','en']);
		$lang = ['.esp','.fra','.ang'][$lix];
		$les = '.esp';

		$type = $args['type'];
		switch($type) {
			case 'codex':		
			case 'glyphe':		
			case 'personne':		
				$pres = strtolower($args['text']);
				break;
			case 'graphie':
			case 'realite':
				$pres = $type == 'realite' ? 'txt_reel' : 'text_dic';
				$dir = '/Home/2/CEN/Codex/Aide/';
				$lang = ['.aes','.afr','.aan'][$lix];
				$les = '.aes';
				break;
			default:
				$pres = str_replace('.', '_', strtolower($args['text']));
				break;
		}
		$txt = '';
		$txt = file_get_contents(getcwd().$dir.$pres.$lang);
		if(!$txt) $txt = file_get_contents(getcwd().$dir.$pres.$les);
		$txt = utf8_encode(nl2br($txt));
		return array('text'=>$txt, 'xxx'=>getcwd().$dir.$pres.$lang);		
	}
	
	function GetTerm($args) {
		$dir = '/Home/2/CEN/Codex/Aide/';
		$lgs = ['es','fr','en'];
		$lix = array_search($args['lang'], $lgs);
		$lang = ['.aes','.afr','.aan'][$lix];
		$les = '.aes';

		$pres = str_replace('.', '_', strtolower($args['text']));
		$txt = '';
		if(file_exists(getcwd().$dir.$pres.$lang)) $txt = file_get_contents(getcwd().$dir.$pres.$lang);
		if(!$txt) $txt = file_get_contents(getcwd().$dir.$pres.$les);
		$txt = utf8_encode(nl2br($txt));
		return array('text'=>$txt); //, 'xxx'=>getcwd().$dir.$pres.$lang);		
	}

	static private function getPicture($type, $p) {
		$id = $p['CodexId'];
		$cx = Sys::getOneData('CEN', 'Codex/'.$id);
		$c = $type == 'element' ? str_replace('.', '_', $p['Theme']) : $p['Cote'];
		$img = self::getDir($cx->Repertoire).self::getImg($c, $type == 'element' ? 'bmp' : 'jpg');
		return array('success'=>true, 'type'=>$type, 'id'=>$id, 'img'=>$img, 'codex'=>$cx->Titre);
	}
	
	static public function GetAnal($args) {
		$lang = $args['lang'];
		$app = $args['app'];
		$word = trim(strtolower($args['word']));
		$id = $args['id'];
		$cid = intval($id) ? "CodexId=$id and" : "";
		
		if($app == 'tlachia') {
			$ext = strtolower(substr($word, -4));
			if($ext == '.jpg' || $ext == '.bmp') {
				$img = self::getCodexDir($id).str_replace('+', '-', strtolower($word));
				return array('success'=>true, 'type'=>'image', 'id'=>$id, 'img'=>$img);
			}

			$cote = str_replace('+', '-', strtolower($word));
			$sql = "select Cote,CodexId from `##_CEN-Planche` where $cid Cote='$cote' limit 1";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$p = '';
				foreach($pdo as $p) return self::getPicture('planche', $p);
			}

			$sql = "select Cote,CodexId from `##_CEN-Zone` where $cid Cote='$cote' limit 1";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$p = '';
				foreach($pdo as $p); return self::getPicture('zone', $p);
			}

			$sql = "select Cote,CodexId from `##_CEN-Glyphe` where $cid Cote='$cote' ".
				"union select Cote,CodexId from `##_CEN-Personnage` where $cid Cote='$cote' limit 1";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$p = '';
				foreach($pdo as $p); return self::getPicture('glyphe', $p);
			}

			$sql = "select Theme,CodexId from `##_CEN-Element` where $cid Theme='$cote' limit 1";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$p = '';
				foreach($pdo as $p); return self::getPicture('element', $p);

			}

			switch($lang) {
				case 'fr': $fld = "Terme"; $ext = 'afr'; break;
				case 'es': $fld = "Espagnol"; $ext = 'aes'; break;
				case 'fr': $fld = "Anglais"; $ext = 'aan'; break;
			}
			$w = CEN::removeAccents($word);
			$sql = "select distinct $fld as term,Fichier from `##_CEN-Termino` where $cid ($fld like '%$w%') limit 15";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$lst = array();
				foreach($pdo as $p) $lst[] = array('term'=>$p['term'], 'text'=>explode('.', $p['Fichier'])[0]);
				return array('success'=>true, 'type'=>'term', 'term'=>$word, 'terms'=>$lst);
			}	
		}
		
		if($app == 'chachalaca') {
			$ext = Chachalaca::CheckGrammar($word, $lang);
			if($ext) return array('success'=>true, 'type'=>'grammar', 'term'=>$word, 'text'=>$word.$ext);
		}

		
		$norma = $args['norma'] ? GDN::Normalize($word) : $word;
		
		
		$sql = "select Trans from `##_CEN-Mot` where $cid (Paleo='$norma' or Trans='$norma') limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return array('success'=>false, 'type'=>'undef', 'nahuatl'=>$word);		
		foreach($pdo as $p) $word = $p['Trans'];
		
		$tran = self::getTrans($cid, $word, $lang);
		
		$sql = "select Decomposition,Racine1,Racine2,Racine3,Racine4,Racine5 from `##_CEN-Racine` where $cid Mot2='$norma' limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return array('success'=>false, 'type'=>'undef', 'nahuatl'=>$word);		
		$rs = array();
		foreach($pdo as $p) {
			$anal = $p['Decomposition'];
			for($i = 1; $i < 6; $i++) {
				$t = 'Racine'.$i;
				$r = trim($p[$t]);
				if($r) $rs[] = ['root'=>$r, 'trans'=>self::getTrans($cid, $r, $lang)];
			}
		}

		$cx = Sys::getOneData('CEN', 'Codex/'.$id);
		$dir = self::getDir($d->Repertoire);
		
		return array('success'=>true, 'type'=>'anal', 'title'=>$cx->Titre, 'nahuatl'=>$norma, 'trans'=>$tran, 'anal'=>$anal, 'roots'=>$rs);
	}
	
	static private function getTrans($cid, $word, $lang) {
		$sql = "select Sens,Sens2,Sens3 from `##_CEN-Sens` where $cid Element='$word' limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//klog::l($sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return '';
		
		switch($lang) {
			case 'es': $t = 'Sens'; break;
			case 'fr': $t = 'Sens2'; break;
			case 'en': $t = 'Sens3'; break;
		}
		foreach($pdo as $p) return $p[$t];
	}
	
	static private function getPicts($cid, $word) {
		$sql = "
select Cote from `kob-CEN-Glyphe` where $cid Lecture='xolotl' group by CodexId
union select Cote from `kob-CEN-Personnage` where $cid Lecture='xolotl' group by CodexId		
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return '';
		
	}
	
	
	
}