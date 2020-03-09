<?php

class Chachalaca extends genericClass {

	// structurer l'import :
	var $imports = array(
		'd_'=>"`##_CEN-Entree`|(ChachalacaId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Entree`, `Commentaire`, `Traduc`, `Nahuatl`, `Categorie`, `Racines`)",
		'r_'=>"`##_CEN-Radix`|(ChachalacaId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Nahuatl`, `Espagnol`, `Racines`, `Categorie`, `Source`, `Bases`, `Transit`, `RacineCat`)",
		'prefixes'=>"`##_CEN-Prefixe`|(`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Prefixe`, `Decompo`, `Couper`, `Categorie`, `Commentaire`, `NbCarac`, `CommentaireEs`)",
		'suffixes'=>"`##_CEN-Suffixe`|(`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Suffixe`, `Decompo`, `Couper`, `Categorie`, `Commentaire`, `NbCarac`, `CommentaireEs`)"
	);

	function Save() {
		$zfile = $this->ZipFile;
		$zok = $zfile != '';
		$id = $this->Id;
		$old = null;

		if($id) { // check if zip has changed
			$old = Sys::getOneData('CEN', "Chachalaca/$id");
			$zok = $zfile != $old->ZipFile;
		}

		if(!$zok) return parent::Save();


		// unzip in tmp dir
		$cwd = getcwd();
		$zfile = "$cwd/$zfile";
		$tmp = "$cwd/Home/tmp/chachalaca";
		$dir = "$cwd/Home/2/CEN/Chachacala";
		CEN::rmDir($tmp);
		mkdir($tmp);

		$zip = new ZipArchive;
		$res = $zip->open($zfile);
		if($res === TRUE) $zip->extractTo($tmp);
		$zip->close();
		if($res !== true) {
			$this->addError(array("Message"=>"Erreur sur le fichier zip", "Prop"=>""));
			return false;
		}

		$fs = array_diff(scandir("$tmp"), array('..', '.'));
		foreach($fs as $k=> $table) {
			$f = explode('.', $table);
			
			if(strpos("fra|esp|ang", strtolower($f[1]))) {
				remane("$tmp/$table", "$dir/grammaire/".strtolower($table));
				continue;
			}
			
			if($f[1] != 'EXP') continue;

			$p = substr($f[0], 0, 2);
			$val = '';
			$cod = '';
			switch($p) {
				case 'd_':
				case 'r_':
					$cod = substr($f[0], 2);
					$val = $this->imports[$p];
					break;
				default:
					$val = isset($this->imports[$f[0]]) ? $v = $this->imports[$f[0]] : '';
			}
			if($val) {
				$val = explode('|', $val);

				$id = 0;
				$sql = '';
				if($cod) {
					$cha = Sys::getOneData('CEN', 'Chachalaca/Code='.$cod);
					if($cha) {
						$id = $cha->Id;
						//$cha->Delete();
						$sql = "delete from ".$val[0]." where ChachalacaId=$id";
					} else
							$sql = "insert into `##_CEN-Chachalaca` (`tmsCreate`,`userCreate`,`tmsEdit`,`userEdit`,`uid`,`gid`,`umod`,`gmod`,`omod`,Code) values(0,2,0,2,2,2,7,7,7,'$cod')";
				} else $sql = "delete from ".$val[0];

				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$GLOBALS['Systeme']->Db[0]->exec($sql);
				if($cod && !$id) $id = $GLOBALS['Systeme']->Db[0]->lastInsertId();

				$v = file_get_contents("$tmp/$table");
				$v = utf8_encode($v);
				$ar = explode("\r\n", $v);
				$v = "";
				foreach($ar as $a) {
					if($a == '') continue;
					$a = str_replace("'", "''", $a);
					$a = "(".($id ? $id.',' : '')."0,2,0,2,2,2,7,7,7,'".preg_replace("/ *$/", "", preg_replace("/ *\t/", "','", $a))."')";
					if($v) $v .= ",\n";
					$v .= $a;
				}
				$sql = "INSERT INTO ".$val[0]." ".$val[1]." VALUES ".$v;
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$GLOBALS['Systeme']->Db[0]->exec($sql);
			}
		}

		unlink($zfile);
		CEN::rmDir($tmp);
		return true;
	}

	static public function GetDics() {
		$sql = "select c.Id as cId,if(c.Nom is null or c.Nom='',c.Code,c.Nom) as dic,g.Id as gId ".
			"from `##_CEN-Chachalaca` c ".
			"left join `##_CEN-Dictionnaire` g on g.Code=c.GDN ".
			"order by dic";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $p)	$dic[] = array('id'=>$p['cId'], 'title'=>$p['dic'], 'gdn'=>$p['gId']);
		return array('dictionaries'=>$dic, 'sql'=>$sql);
	}
	
	static public function GetList($args) {
		$ids = $args['dic'];
		$word = $args['word'];
		$sql = "select distinct Nahuatl as word from `##_CEN-Radix` where ChachalacaId in ($ids) and Nahuatl like '$word%' order by word limit 15";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('words'=>$list); //, 'sql'=>$sql);
	}
	

	static private $error = '';
	static private $suf_imp;
	static private $pre_imp;
	static private $pre_suf;
	static private $imposs;
	static private $testes;
	static private $regbase;
	static private $dicIds;
	static private $premier;
	
	static private function getRules($rule) {
		$t = array();
		$r = Sys::getOneData('CEN', 'Regle/Code=CHACHALACA&Regle='.$rule);
		$s = file_get_contents(getcwd().'/'.$r->FilePath);
		$s = str_replace("\r", '', $s);
		$ls = explode("\n", utf8_encode($s));
		foreach($ls as &$l) {
			$cnd = explode("\t", $l);
			foreach($cnd as &$c) $c = trim($c);
			$t[] = $cnd;
		}
		return $t;
	}

	static function GetMorpho($args) {
		$word = $args['word'];
		if($args['norm'] == 'true') $word = GDN::Normalize($word);

		self::$dicIds = $args['dic'];
		self::$premier = $word;
		
		self::$suf_imp = self::getRules('SUF_IMPOSSIBLE');
		
		// 38_1
		$redoublement = self::redoublement38_1($m_premier);
		// 2_1 + 3_2 
		$t62 = array();
		self::suffixes2_1($t62);
		usort($t62, 'self::sortC0C1');
		// 3_2
		$t61 = array();
		foreach($t62 as $k=> &$l) {
			$t61[] = array($l[0], $l[1], '', '', '');
			klog::l("3_2 t61  ".$l[0].', '.$l[1]);
		}
		usort($t61, 'self::sortC0');
		// 4_3
		$t60 = array();
		self::generation4_3($t61, $t60);
		// 6_3
		self::categorisation6_3($t60);
		// 6_4
		self::conditionsSuf6_4($t60, true);
		// 6->4_5
		$t62 = array();
		self::generation4_5($t60, $t61, $t62);
		// 6->5_1
		$t60 = array();
		self::nettoyage5_1($t62, $t60);
		// 6->6_3
		self::categorisation6_3($t60);
		// 6->6_4
		self::conditionsSuf6_4($t60);
		$t61 = array();
		$t62 = array();
		// 4_4 + 5_5
		$t3 = array();
		$t3[] = self::$premier;
		$t7 = array();
		self::analyse4_4($t60, $t7);
		// 7_3
		$t1 = array();
		foreach($t7 as $l) $t1[] = array($l[1].$l[2], $l[4], '');
		$t1[] = array(self::$premier, '', '');
		foreach($t1 as $l) klog::l("7_3 t1  ".$l[0]."  ".$l[1]);
		// 3_3
		self::conditionsSuf3_3($t1);
		$t1[] = array(self::$premier, '', '');
		
		self::$suf_imp = '';
		self::$pre_imp = self::getRules('PREF_IMPOSSIBLE');
		self::$pre_suf = self::getRules('PREF_SUF_IMPOSSIBLE');


		// PREFIXES ---------------------
		klog::l("************* PRFX ****************");

		// 2_6
		$t7 = array();
		$t60 = array();
		$t61 = array();
		$t62 = array();
		self::prefixes2_6($t62);
		// 3_1
		$t60 = array();
		self::nettoyage3_1($t62, $t60);
		// 4_1 + 5_2 
		$t61 = array();
		self::generation4_1($t60, $t62, $t61);
		// 5_7
		self::nettoyage5_7($t62);
		// 6_1
		self::categorisation6_1($t62);
		// 6_2
		self::conditionPref6_2($t62);
		// 7_1
		self::conditionPref7_1($t60);
		// 4_2 + 5_3
//		$t3 = array();
		$t7 = array();
		self::analyse4_2($t62, $t60, $t7);
		$t3[] = self::$premier;
		// 7_2  ??????????????????????????????????????????????????????
		self::conditionPref7_2($t7);
		// 6_5
		self::conditionPref6_5($t7);
		// 7_5
		self::traitement7_5($t1, $t7, $t61);
		// 7_2
		self::conditionPref7_2($t7);
		
		self::$pre_imp = '';
		self::$pre_suf = '';

		// RACINES ---------------------
		klog::l("RADX----------");
		
		self::$imposs = self::getRules('IMPOSSIBLE');
		self::$testes = self::getRules('TESTES');
		self::$regbase = self::getRules('REG_BASE');

		// 8
		$t1_1 = array();
		$t2_1 = array();
		$t3_1 = array();
		$t4_1 = array();
		$t5 = array();
		$t6 = array();
		self::racines8($t7, $t1_1, $t2_1, $t3_1, $t4_1, $t5, $t6);
		// 9
		self::nettoyage9($t5);
		// 10 + 31
		self::condition10($t5);
		// 5
		self::incoherance5($t5);
		// 12_2 + 31
		$t70 = array();
		self::condition12_2($t5, $t70);
		// 12
		$t8 = array();
		self::traduction12($t5, $t8);
		// 13
		$t9 = array();
		self::traduction13($t5, $t8, $t9);
		// 29
		self::base29($t5);
		// 12
		$t8 = array();
		self::traduction12($t5, $t8);
		// 13
		$t9 = array();
		self::traduction13($t5, $t8, $t9);
		// 40
		$t14 = array();
		foreach($t5 as &$l) $t14[] = array($l[0], $ll[1], $l[2]);
		// 48
		self::ordone48($t5);


		$col = false;
		$mor = array();
		foreach($t5 as &$l) {
			$mor[] = array('mor'=>$l[0], 'cat'=>$l[2], 'typ'=>$l[1], 
			'grn'=>$l[5]=='ivertclair', 'red'=>$l[5]=='irougeclair', 'blu'=>$l[5]=='ibleuclair');
			$col |= ($l[5]=='ivertclair' || $l[5]=='irougeclair' || $l[5]=='ibleuclair');
		}
		$trn = array();
		foreach($t9 as &$l) $trn[] = array('ent'=>$l[0], 'trn'=>$l[1], 'cat'=>$l[2]);

		return array('colour'=>$col, 'word'=>$word, 'double'=>$redoublement, 'morpho'=>$mor, 'trans'=>$trn);
	}
	
	
	// 48
	static private function ordone48(&$t5) {
		foreach($t5 as &$l5) {
			$morphologie = $l5[0];
			$pos_1 = strpos($morphologie, '- +');
			$pos_2 = strpos($morphologie, '+ -');
			if($pos_1 !== false && $pos_2 !== false) $m_racines = trim(substr($morphologie, $pos_1+3, $pos_2-($pos_1+3)));
			elseif($pos_1 === false && $pos_2 !== false) $m_racines = trim(substr($morphologie, 0,  $pos_2-2));
			elseif($pos_1 !== false && $pos_2 === false) $m_racines = trim(substr($morphologie, $pos_1+3));
			else $m_racines = trim($morphologie);
			
			$debut = $cpteur = 0;
			for(;;) {
				$debut = strpos($m_racines, '-', $debut);
				if($debut === false) break;
				$debut++;
				$cpteur++;
			}
			$l5[3] = $cpteur;
			$debut = $cpteur = 0;
			for(;;) {
				$debut = strpos($morphologie, '-', $debut);
				if($debut === false) break;
				$debut++;
				$cpteur++;
			}
			$l5[4] = $cpteur;
		}
		usort($t5, 'self::sortC3C4');
foreach($t5 as $l) klog::l("48 t5  $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5], $l[6]");
	}
	
	
	// 29
	static private function base29(&$t5) {
		$ids = self::$dicIds;
		$sql = "select Nahuatl,Espagnol,Categorie,Source,Bases,Transit from `##_CEN-Radix` where ChachalacaId=$ids and Racines=:rdx and Categorie=:cat";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		foreach(self::$regbase as $cat) {
			self::verifBase($pdo, $cat[0], $cat[1], $t5);
		}
		foreach($t5 as $k=>&$l5) if($l5[1] == '***') unset($t5[$k]);
		$t5 = array_values($t5);

foreach($t5 as $l) klog::l("29 t5  $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5]");
	}
	
	static private function verifBase($pdo, $base_verif, $base_cond, &$t5) {
		foreach($t5 as $k=>&$l) {
			if(strpos($l[2], $base_verif) === false) continue;
			
			$racines = trim($l[0]);
			$pos_1 = strpos($racines, '- +');
			$pos_2 = strpos($racines, '+ -');
			if($pos_1 !== false && $pos_2 !== false) $racines = trim(substr($racines, $pos_1+4, $pos_2-($pos_1+5)));
			elseif($pos_1 === false && $pos_2 !== false) $racines = trim(substr($racines, 0,  $pos_2-1));
			elseif($pos_1 !== false && $pos_2 === false) $racines = trim(substr($racines, $pos_1));

			$debut = $cpteur = $pos_tiret = 0;
			for(;;) {
				$debut = strpos($racines, '-', $debut);
				if($debut === false) break;
				$pos_tiret = $debut++;
				$cpteur++;
			}

			//$resultat = strlen($racines);
			$racine_v = substr($racines, $pos_tiret);
			$a_trouver = 0;

//			$ensemble_cond = '';
//klog::l(">>>29  $racine_v");
			$pdo->execute(array(':rdx'=>$racine_v, ':cat'=>'r.v.'));
			if($pdo->rowCount()) {
				$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
				foreach($rs as $r) {
//klog::l("<<<29  ".$r['Bases'].' : '.$base_cond).' :';
					if(strpos($r['Bases'], $base_cond) !== false) $a_trouver++;
//					$ensemble_cond .= $base_verif.' / '.$base_cond.' / '.$racine_v.' / '.$r['Racine']; 
				}
				if($a_trouver == 0) $l[1] = '***';
			}			
		}
	}


	// 12
	static private function traduction13(&$t5, &$t8, &$t9) {
		$m_premier = self::$premier;
		$ids = self::$dicIds;
		$sql = "select Nahuatl,Espagnol,Categorie from `##_CEN-Radix` where ChachalacaId=$ids and Racines=:rdx and Categorie=:cat";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		$sql = "select Nahuatl,Traduc from `##_CEN-Entree` where ChachalacaId=$ids and Nahuatl=:nah";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo1 = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		$t9 = array();
		foreach($t8 as $k=>&$l8) {
			$racine_num = trim($l8[0]);
			$cat = trim($l8[1]);
			if(strlen($racine_num) > 0) {
				$pdo->execute(array(':rdx'=>$racine_num, ':cat'=>$cat));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$t9[] = array($r['Nahuatl'], $r['Espagnol'], $r['Categorie']);
					}
				}			
			}
		}
		usort($t9, 'self::sortC0C1');
foreach($t9 as $k=>$l) klog::l("13 t9-a  $k: $l[0],  $l[1],  $l[2]");
		$o = null;
		foreach($t9 as $k=>&$l9) {
			if($o && $l9[0] == $o[0] && $l9[1] == $o[1]) unset($t9[$k]);
			else $o = $l9;

		}
		$t9 = array_values($t9);
foreach($t9 as $k=>$l) klog::l("13 t9-b  $k: $l[0],  $l[1],  $l[2]");
		
		if(count($t9)) {
			if(strlen(trim($t5[0][0])) == 0) {
				$t9 = array();
				$pdo1->execute(array(':nah'=>$m_premier));
				if($pdo1->rowCount()) {
					$r = $pdo1->fetch(PDO::FETCH_ASSOC);
					$t9[] = array($r['Nahuatl'], $r['Espagnol'], '');
				}
				else $t9[] = array($m_premier, '?', '');
			}
			else {
				if(self::arraySearch($t9, 0, $m_premier) === false) {
					$pdo1->execute(array(':nah'=>$m_premier));
					if($pdo1->rowCount()) {
						$r = $pdo1->fetch(PDO::FETCH_ASSOC);
						$t9[] = array($r['Nahuatl'], $r['Espagnol'], '');
					}
				}
			}
		}
		else {
			$pdo1->execute(array(':nah'=>$m_premier));
			if($pdo1->rowCount()) {
				$r = $pdo1->fetch(PDO::FETCH_ASSOC);
				$t9[] = array($r['Nahuatl'], $r['Espagnol'], '');
			}
			else $t9[] = array($m_premier, '?', '');
		}
		
foreach($t9 as $k=>$l) klog::l("13 t9-c  $k: $l[0],  $l[1],  $l[2]");

	}
	
	// 12
	static private function traduction12(&$t5, &$t8) {
		$t8 = array();
		foreach($t5 as &$l5) {
			$morphologie = $l5[0];
			$pos_1 = strpos($morphologie, '- +');
			$pos_2 = strpos($morphologie, '+ -');
			if($pos_1 !== false && $pos_2 !== false) $m_racines = trim(substr($morphologie, $pos_1+3, $pos_2-($pos_1+3)));
			elseif($pos_1 === false && $pos_2 !== false) $m_racines = trim(substr($morphologie, 0,  $pos_2-1));
			elseif($pos_1 !== false && $pos_2 === false) $m_racines = trim(substr($morphologie, $pos_1+3));
			else $m_racines = trim($morphologie);

			$categorie = $l5[2];
			$pos_1 = strpos($categorie, '- +');
			$pos_2 = strpos($categorie, '+ -');
			if($pos_1 !== false && $pos_2 !== false) $m_categorie = trim(substr($categorie, $pos_1+3, $pos_2-($pos_1+3)));
			elseif($pos_1 === false && $pos_2 !== false) $m_categorie = trim(substr($categorie, 0,  $pos_2-1));
			elseif($pos_1 !== false && $pos_2 === false) $m_categorie = trim(substr($categorie, $pos_1+3));
			else $m_categorie = trim($categorie);
			
			$rs = explode('-', $m_racines);
			$cs = explode('-', $m_categorie);		
			$l = count($rs);
			for($i = 0; $i < $l; $i++) $t8[] = array(trim($rs[$i]), trim($cs[$i], ''));
		}
		
		usort($t8, 'self::sortC0C1');
		$o = null;
		foreach($t8 as $k=>&$l8) {
			if($o && $l8[0] == $o[0] && $l8[1] == $o[1]) unset($t8[$k]);
			else $o = $l8;

		}
		$t8 = array_values($t8);
		
foreach($t8 as $k=>$l) klog::l("12 t8  $k: $l[0],  $l[1],  $l[2]");
	}
	
	
	// 12_2
	static private function condition12_2(&$t5, &$t70) {
		$ids = self::$dicIds;
		$sql = "select Categorie,Source,Bases,Transit from `##_CEN-Radix` where ChachalacaId=$ids and Racines=:rdx and Categorie=:cat";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		$nb_tour = 0;
		$ch1 = '+';
		$ch2 = '';
		foreach($t5 as &$l5) {
			$t70 = array();
			$t70[] = array('££££', '', '', '', '', '', '');
			$l[1] = '';
			$nb_objet = $nb_r_nominale = $nb_causatif = $nb_applicatif = $nb_suf_possed = $nb_pref_pos = $m_nb_morph = 0;
			$nb_suf_particip = $nb_tot_objet = $nb_objet_def = $nb_reflechi = $nb_passif = $nb_suf_nomina = $nb_hum = 0;
			$nb_pref_refl_indef = $nb_suf_adj = $nb_causatif_applicatif = 0;
			
			$morphologie = trim($l5[0]);
			$ms = explode('-', $morphologie);
			foreach($ms as $m) {
				$m = trim($m);
				if(strlen($m)) {
					$t70[] = array(++$m_nb_morph, '', '', $m, '', '', '');
				}
			}
			$categorie = trim($l5[2]);
			$ms = explode('-', $categorie);
			$nb_tour = 1;
			foreach($ms as $m) {
				$m = trim($m);
				if(strlen($m)) {
					if(isset($t70[$nb_tour])) $t70[$nb_tour][2] = $m;
					else self::$error .= "condition12_2 t70[$nb_tour] not set\n";
					$nb_tour++;
				}
			}
if(self::$error) klog::l(">>>>ERROR: ".self::$error);
//foreach($t70 as $k=>$l) klog::l("12_2-a t70  $k: $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5],  $l[6]");
			
			foreach($t70 as &$l7) {
				$l7[3] = trim(str_replace('+', '', $l7[3]));
				$l7[2] = trim(str_replace('+', '', $l7[2]));
			}
			
			foreach($t70 as &$l7) {
				if($l7[0] == '££££') continue;
				$cat = trim($l7[2]);
				if(strpos($cat, 'préf.') === false && strpos($cat, 'suf.') === false) {
					$m_mot = $l7[3];
//klog::l("++++$m_mot");
					$nb_tour = 0;
					$rech = $m_mot.'...'.$cat.'...';
					
					$m_origines = $m_bases = $m_transitifs = '';
					$pdo->execute(array(':rdx'=>$m_mot, ':cat'=>$cat));
					if($pdo->rowCount()) {
						$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
						foreach($rs as $r) {
							if($nb_tour++) {
								$m_origines .= ' / ';
								$m_bases .= ' / ';
								$m_transitifs .= ' / ';
							}
							$m_origines .= $r['Source'];
							$m_bases .= $r['Base'];
							$m_transitifs .= $r['Transit'];
						}
					}
					$l7[4] = $m_origines;
					$l7[5] = $m_bases;
					$l7[6] = $m_transitifs;
				}
			}
//foreach($t70 as $k=>$l) klog::l("12_2-b t70  $k: $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5],  $l[6]");
	

			foreach($t70 as $k=>$l) {
				$c = $l[2];
				if($c) {
					if(strpos($c, 'obj.') !== false) $nb_objet++;
					if(strpos($c, 'préf. pos.') !== false) $nb_pref_pos++;
					if(strpos($c, 'suf. possed.') !== false) $nb_suf_possed++;
					if(strpos($c, 'suf. particip. (ca)') !== false) $nb_suf_particip++;
					if(strpos($c, 'suf. verb. caus.') !== false) $nb_causatif++;
					if(strpos($c, 'suf. verb. apl.') !== false) $nb_applicatif++;
					if(strpos($c, 'r.n.') !== false || strpos($cat, 'num.') !== false) $nb_r_nominale++;
					if(strpos($c, 'préf. obj. déf.') !== false) $nb_objet_def++;
					if(strpos($c, 'préf. réfl.') !== false) $nb_reflechi++;
					if(strpos($c, 'suf. verb. pas.') !== false) $nb_passif++;
					if(strpos($c, 'suf. verb. nom.') !== false) $nb_suf_nomina++;
					if(strpos($c, 'préf. obj. hum. indéf.') !== false) $nb_hum++;
					if(strpos($c, 'préf. réfl. indéf.') !== false) $nb_pref_refl_indef++;
					if(strpos($c, 'suf. adj.') !== false) $nb_suf_adj++;
				}
			}
			$nb_tot_objet = $nb_objet+$nb_r_nominale+$nb_pref_refl_indef+$nb_reflechi;
			$nb_causatif_applicatif = $nb_causatif+$nb_applicatif;

			//array_splice($t70, 0, 0, array('££££'));
			$t70[] = array('££££', '', '', '', '', '', '');
//			$t70 = array_values($t70);
//foreach($t70 as $k=>$l) klog::l("12_2-c t70  $k: $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5],  $l[6]");
			
file_put_contents('/home/paul/tmp/src.php', "<?php\n");

			$old = $col15 = $l5[1];
			$len70 = count($t70);
			$ltst = count(self::$testes);
			$n = $len70-1;
			for($ii = 1; $ii < $n; $ii++) {
				for($j = 0; $j < $ltst; ) {
					$tst = self::$testes[$j++];
					if(empty($tst[0]) || substr($tst[0],0,1) == '*') continue;

					$source = '';
					$par = array();
					$npar = -1;
					$m_test = substr($tst[0], 0, strlen($tst[0])-2);
					while($j < $ltst && $m_test == substr($tst[0], 0, strlen($tst[0])-2)) {
						$s = $tst[1];
						if(substr($s, 0, 2) != '//') {
							$s = self::wd2php($s);
							
							if(substr($s, 0, 1) == '}')  {
								if($npar >= 0 && $par[$npar]) $source .= "break;\n"; 
								array_splice($par, $npar--, 1);
							}
							if(substr($s, -1, 1) == '{') $par[++$npar] = 0;
							if($npar >= 0 && $par[$npar] && (substr($s, 0, 5) == 'case ' || $s == 'default:')) {
								$source .= "break;\n";
								$par[$npar]--;
							}
							if(substr($s, 0, 5) == 'case ') $par[$npar]++;
		
							$source .= "$s\n";
						}
						$tst = self::$testes[$j++];
					}
					$test_fait = 0;
file_put_contents('/home/paul/tmp/src.php', "//------- $m_test\n$source", FILE_APPEND);
					try {
						eval($source);
						if($col15 == '***') $test_fait = 1;
//if($old != '***' && $col15 == '***') {
//	klog::l(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>$m_test : $morphologie : $categorie : $source");
//	$old = '***';
//}
					} catch(Exception $e) {
						klog::l("**********************".$e->getMessage());
					}
					$res = '';
				}
			}
			$l5[1] = $col15;
		}

		foreach($t5 as $k=>&$l5) if($l5[1] == '***') unset($t5[$k]);
		$t5 = array_values($t5);
		foreach($t5 as &$l) klog::l("12_2 t5  $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5]");
	}
	
	
	static private function wd2php($w) {
		$w = strtolower($w);
		$p = '/^(.*)milieu\(([\w\d\[\]\+\-\(\)]*),([\w\d\[\]\+\-\(\)]*),?([\w\d\[\]\+\-\(\)]*?)\)(.*)$/';
		while(preg_match($p, $w, $m)) {
			$w = $m[1].'substr('.$m[2].','.($m[3] == '1' ? '0' : $m[3].'-1');
			if($m[4]) $w .= ','.$m[4];
			$w .= ')'.$m[5];
		}
		if(preg_match('/^si (.*)$/', $w, $m)){
			$s = str_replace(' et ', ' && ', $m[1]);
			$s = str_replace('"et ', '" && ', $s);
			$s = str_replace(')et ', ') && ', $s);
			$s = str_replace(' ou ', ' || ', $s);
			$s = str_replace('"ou ', '" || ', $s);
			$s = str_replace(')ou ', ') || ', $s);
			$s = str_replace(') > 0', ') !== false', $s);
			$s = str_replace(')> 0', ') !== false', $s);
			$s = str_replace(') <> 0', ') !== false', $s);
			$s = str_replace(') = 0', ') === false', $s);
			$s = str_replace(') =0', ') === false', $s);
			$s = str_replace(')= 0', ') === false', $s);
			$s = str_replace(' = ', ' == ', $s);
			$s = str_replace(' <> ', ' != ', $s);
			$w = 'if('.$s.') {';
		}
		$w = preg_replace('/origines\[([\$\+\-\.\d\w\(\)]*)\]/', '$t70[$1][4]', $w);
		$w = preg_replace('/categories\[([\$\+\-\.\d\w\(\)]*)\]/', '$t70[$1][2]', $w);
		$w = preg_replace('/bases\[([\$\+\-\.\d\w\(\)]*)\]/', '$t70[$1][5]', $w);
		$w = preg_replace('/morphemes\[([\$\+\-\.\d\w\(\)]*)\]/', '$t70[$1][3]', $w);
		$w = preg_replace('/nb_morph\[([\$\+\-\.\d\w\(\)]*)\]/', '$t70[$1][0]', $w);
		$w = preg_replace('/transitifs\[([\$\+\-\.\d\w\(\)]*)\]/', '$t70[$1][0]', $w);
		$w = str_replace('col15[j]', '$col15', $w);
		$w = str_replace('$col15 \= $col15 \+', '$col15 .=', $w);
		$w = preg_replace('/^\$col15 \= (.*)$/', '\$col15 = $1;', $w);
		$w = preg_replace('/^\$col15 \.\= (.*)$/', '\$col15 .= $1;', $w);
		$w = preg_replace('/^test_fait = (.*)$/', '\$test_fait = $1;', $w);
		$w = preg_replace('/^table5\[j\]..couleur = (.*)$/', '\$l5[5] = "$1";', $w);
		$w = str_replace('table70..occurrence', '$len70-1', $w);
		$w = str_replace('taille(', 'strlen(', $w);
		$w = str_replace('position(', 'strpos(', $w);
		$w = str_replace('taille(', 'strlen(', $w);
		$w = str_replace('sansespace(', 'trim(', $w);
		$w = str_replace('nb_', '$nb_', $w);
		$w = preg_replace('/^sinon$/', '} else {', $w);
		$w = preg_replace('/^fin$/', '}', $w);
		$w = preg_replace('/^selon (.*)$/', 'switch($1) {', $w);
		$w = preg_replace('/^cas (.*)$/', 'case $1:', $w);
		$w = preg_replace('/^autre cas$/', 'default:', $w);
		$w = str_replace('ii', '$ii', $w);
		return $w;
	}
	
//	static private function remplaceVerif(&$ch, $chini, $chremp) {
//		$li = strlen($chini);
//		$lr = strlen($chremp);
//		$nb = 0;
//		$p = strpos($ch, $chini);
//		if($p !== false) {
//			$nb++;
//			$ch = substr($ch, 0, $p-1).$chremp.substr($ch, $p+$lr);
//			$p = strpos($ch, $chini, $p+$lr);
//		}
//		return $nb;
//	}
	
	
	// 5
	static private function incoherance5(&$t5) {
		$m_premier = self::$premier;
		foreach($t5 as $k=>&$l) {
			$ch = $l[0];
			$ch = str_replace('-', '', $ch);
			$ch = str_replace('+', '', $ch);
			$ch = str_replace(' ', '', $ch);
			$ch = str_replace('(i)', '', $ch);
			$ch = str_replace('(o)', '', $ch);
			$ch = str_replace('(t)', '', $ch);
			$ch = str_replace('(qu)', '', $ch);
//			self::remplaceOr($ch, '-', '');
//			self::remplaceOr($ch, '+', '');
//			self::remplaceOr($ch, ' ', '');
//			self::remplaceOr($ch, '(i)', '');
//			self::remplaceOr($ch, '(o)', '');
//			self::remplaceOr($ch, '(t)', '');
//			self::remplaceOr($ch, '(qu)', '');
			if($ch != $m_premier) unset($t5[$k]);
		}
		foreach($t5 as &$l) klog::l("5 t5  $l[0],  $l[1],  $l[2],  $l[3],  $l[4],  $l[5]");		
	}
	
//	static private function remplaceOr(&$ch, $chini, $chremp) {
//		$li = strlen($chini);
//		$lr = strlen($chremp);
//		$p = strpos($ch, $chini);
//		if($p !== false) {
//			$ch = substr($ch, 0, $p).$chremp.substr($ch, $p+$li);
//			$p = strpos($ch, $chini, $p+$lr);
//		}
//		return $ch;
//	}

	// 10 + 31
	static private function condition10(&$t5) {
		usort($t2, 'self::sortC0C2');
		
		foreach(self::$imposs as $cnd) {
			$ensemble_condition = "$cnd[0] $cnd[1] $cnd[2] $cnd[3] $cnd[4] $cnd[5]";
			$c0 = !empty($cnd[0]);
			$c1 = !empty($cnd[1]);
			$c2 = !empty($cnd[2]);
			$c3 = !empty($cnd[3]);
			$c4 = !empty($cnd[4]);
			$c5 = !empty($cnd[5]);
			
			foreach($t5 as $k=>&$l) {
				$v = $l[2];
				if(!$c1 && !$c2 && !$c3 && !$c4 && !$c5) {
					$resultat1 = strpos($v, $cnd[0]);
					if($resultat1 !== false) $l[1] = '***';
				}
				if($c1 && !$c2 && !$c3 && !$c4 && !$c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat2 = strpos($v, $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[1] = '***';
				}
				if(!$c1 && $c2 && !$c3 && !$c4 && !$c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat3 = strpos($v, $cnd[2]);
					if($resultat1 === false && $resultat3 !== false) $l[1] = '***';
				}
				if($c0 && !$c1 && $c2 && $c3 && !$c4 && !$c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat3 = strpos($v, $cnd[2]);
					$resultat4 = strpos($v, $cnd[3]);
					if($resultat1 !== false && $resultat3 === false && $resultat4 === false) $l[1] = '***';
				}
				if($c0 && $c1 && $c2 && $c3 && !$c4 && !$c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat2 = strpos($v, $cnd[1]);
					$resultat3 = strpos($v, $cnd[2]);
					$resultat4 = strpos($v, $cnd[3]);
					if($resultat1 !== false && $resultat2 === false && $resultat3 === false && $resultat4 === false) $l[1] = '***';
				}
				if($c0 && !$c1 && $c2 && $c3 && $c4 && !$c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat3 = strpos($v, $cnd[2]);
					$resultat4 = strpos($v, $cnd[3]);
					$resultat5 = strpos($v, $cnd[4]);
					if($resultat1 !== false && $resultat3 === false && $resultat4 === false && $resultat5 === false) $l[1] = '***';
				}
				if($c0 && !$c1 && $c2 && $c3 && $c4 && $c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat3 = strpos($v, $cnd[2]);
					$resultat4 = strpos($v, $cnd[3]);
					$resultat5 = strpos($v, $cnd[4]);
					$resultat6 = strpos($v, $cnd[5]);
					if($resultat1 !== false && $resultat3 === false && $resultat4 === false && $resultat5 === false && $resultat6 === false) $l[1] = '***';
				}
				if($c0 && $c1 && $c2 && $c3 && $c4 && $c5) {
					$resultat1 = strpos($v, $cnd[0]);
					$resultat2 = strpos($v, $cnd[1]);
					$resultat3 = strpos($v, $cnd[2]);
					$resultat4 = strpos($v, $cnd[3]);
					$resultat5 = strpos($v, $cnd[4]);
					$resultat6 = strpos($v, $cnd[5]);
					if($resultat1 !== false && $resultat2 === false && $resultat3 === false && $resultat4 === false && $resultat5 === false && $resultat6 === false) $l[1] = '***';
				}
			}
		}
		
		//$resultat1 = 0;
		foreach($t5 as &$l) {
			$v = "  $l[2]";
			if(strpos($v, '  préf. indéf.') !== false || strpos($v, '  liga.') !== false || strpos($v, '  suf. abstr.') !== false) $l[1] = '***';
//				$resultat1++;
//			if($resultat1) {
//				$l[1] = '***';
//				$resultat1 = 0;
//			}
		}
		
		klog::l("10 t5  ---".count($t5));
		foreach($t5 as &$l) klog::l("10 t5  $l[0],  $l[1],  $l[2]");
				
		foreach($t5 as $k=>&$l) if($l[1] == '***') unset($t5[$k]);
		$t5 = array_values($t5);

		klog::l("31 t5  ---".count($t5));
		foreach($t5 as &$l) klog::l("31 t5  $l[0],  $l[1],  $l[2], $l[3]");
	}

	// 9
	static private function nettoyage9(&$t5) {
		usort($t5, 'self::sortC0C2');
		$o = null;
		foreach($t5 as $k=>&$l) {
			if($o && $l[0] == $o[0] && $l[2] == $o[2]) unset($t5[$k]);
			else $o = $l;
		}
		$t5 = array_values($t5);
		usort($t5, 'self::sortC1');
		klog::l("9 t5  ---".count($t5));
		foreach($t5 as &$l) klog::l("9 t5  $l[0],  $l[1],  $l[2], $l[3]");
	}
	
	// 8
	static private function racines8(&$t7, &$t1_1, &$t2_1, &$t3_1, &$t4_1, &$t5, &$t6) {
		$nb_entree = 0;
		$sep_cat = ' + ';
		$ids = self::$dicIds;
		$sql = "select Racines,Categorie from `##_CEN-Radix` where ChachalacaId=$ids and Racines=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		
		$tmp1 = array();
		$tmp2 = array();
		$tmp3 = array();

		foreach($t7 as &$l7) {
			$mot = $l7[1];
			$reste = $longeur = strlen($mot);
			if(!$longeur) continue;
			while($reste) {
				$a_chercher = substr($mot, 0, $reste--);
				$pdo->execute(array(':fix'=>$a_chercher));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$k = $r['Racines'].'|'.$r['Categorie'];
						if(!isset($tmp1[$k])) {
							$tmp1[$k] = 0;
							$t1_1[] = array($r['Racines'], $r['Categorie']);
						}
					}
				}
			}
			$reste = 0;
			while($reste < $longeur) {
				$a_chercher = substr($mot, -(++$reste));
				$pdo->execute(array(':fix'=>$a_chercher));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$k = $r['Racines'].'|'.$r['Categorie'];
						if(!isset($tmp2[$k])) {
							$tmp2[$k] = 0;
							$t2_1[] = array($r['Racines'], $r['Categorie'], '');
						}
					}
				}
			}
			$long = $longeur-2;
			$debut = 0;
			while($debut < $long) {
				$debut++;
				$reste = $longeur-$debut;
				while($reste) {
					//$reste--;
					$a_chercher = substr($mot, $debut, $reste--);
//klog::l(">>> $debut,  $reste,  $a_chercher");
					$pdo->execute(array(':fix'=>$a_chercher));
					if($pdo->rowCount()) {
						$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
						foreach($rs as $r) {
							$k = $r['Racines'].'|'.$r['Categorie'];
							if(!isset($tmp3[$k])) {
								$tmp3[$k] = 0;
								$t3_1[] = array($r['Racines'], $r['Categorie'], '');
							}
						}
					}
				}
			}
			$k = 'xxx|xxx';
			if(!isset($tmp3[$k])) {
				$tmp3[$k] = 0;
				$t3_1[] = array('xxx', 'xxx', '');
			}
			

//			usort($t1_1, 'self::sortC0C1');
//			$o = null;
//			foreach($t1_1 as $k=>&$l11) {
//				if($o && $l11[0] == $o[0] && $l11[1] == $o[1]) unset($t1_1[$k]);
//				else $o = $l11;
//			}
//			$t1_1 = array_values($t1_1);
			
//			usort($t2_1, 'self::sortC0C1');
//			$o = null;
//			foreach($t2_1 as $k=>&$l11) {
//				if($o && $l11[0] == $o[0] && $l11[1] == $o[1]) unset($t2_1[$k]);
//				else $o = $l11;
//			}
//			$t2_1 = array_values($t2_1);
			
//			usort($t3_1, 'self::sortC0C1');
//			$o = null;
//			foreach($t3_1 as $k=>&$l11) {
//				if($o && $l11[0] == $o[0] && $l11[1] == $o[1]) unset($t3_1[$k]);
//				else $o = $l11;
//			}
//			$t3_1 = array_values($t3_1);
//			unset($l11);

			usort($t1_1, 'self::sortC0C1');
			usort($t2_1, 'self::sortC0C1');
			usort($t3_1, 'self::sortC0C1');
			
//klog::l(">>>APRES $mot++++  b: ".count($t1_1)."  ".count($t2_1)."  ".count($t3_1));


			$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
			$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;

			$i1 = 0;
			foreach($t1_1 as &$l11) {
				$i1++;
				$racine1 = $l11[0];
				$cat_1 = $l11[1];
			
				$i2 = 0;
				foreach($t2_1 as &$l21) {
					$i2++;
					$racine2 = $l21[0];
					$cat_2 = $l21[1];
					
					$i3 = 0;
					foreach($t3_1 as &$l31) {
						$i3++;
						$racine3 = $l31[0];
						$cat_3 = $l31[1];
						
						if($racine1 == $mot) {
							$decomposition = $racine1;
							$m_categorie = $cat_1;
							//$racine_1 = $racine1;
							//$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
							//$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;
							
							$v0 = $l7[3].$sepa_cat1.$m_categorie.$sepa_cat2.$l7[4];
							$v1 = $l7[0].$sepa_cat1.$decomposition;
							$resultat = self::arraySearch($t5, 2, $v0);
							if($resultat === false || strpos($t5[$resultat][0], $v1) === false) {
								$nb_entree++;
								$t5[] = array($v1.$sepa_cat2.$l7[2], "$i1 $i2 $i3", $v0, '', '', '');
							}
						}
						
						if(strlen($racine2)) {
							if($racine1.$racine2 == $mot) {
								$decomposition = "$racine1-$racine2";
								$m_categorie = "$cat_1 - $cat_2";
								//$racine_1 = $racine1;
								//$racine_2 = $racine2;
								//$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
								//$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;
								
								$v0 = $l7[3].$sepa_cat1.$m_categorie.$sepa_cat2.$l7[4];
								$v1 = $l7[0].$sepa_cat1.$decomposition;
								$resultat = self::arraySearch($t5, 2, $v0);
								if($resultat === false || strpos($t5[$resultat][0], $v1) === false) {
									$nb_entree++;
									$t5[] = array($v1.$sepa_cat2.$l7[2], "$i1 $i2 $i3", $v0, '', '', '');
								}
							}
						}

						if($racine1.$racine3.$racine2 == $mot) {
							$decomposition = "$racine1-$racine3-$racine2";
							$m_categorie = "$cat_1 - $cat_3 - $cat_2";
							//$racine_1 = $racine1;
							//$racine_2 = $racine2;
							//$racine_3 = $racine3;
							//$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
							//$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;
							
							$v0 = $l7[3].$sepa_cat1.$m_categorie.$sepa_cat2.$l7[4];
							$v1 = $l7[0].$sepa_cat1.$decomposition;
							$resultat = self::arraySearch($t5, 2, $v0);
							if($resultat === false || strpos($t5[$resultat][0], $v1) === false) {
								$nb_entree++;
								$t5[] = array($v1.$sepa_cat2.$l7[2], "$i1 $i2 $i3", $v0, '', '', '');
							}
						}
					}
				}
			}
		}
		
		foreach($t7 as &$l) {
			$prefixe = $l[0];
			$suffixe = $l[2];
			$ensemble = substr($prefixe, 0, strlen($prefixe)-1).substr($suffixe, 1);
			if($ensemble == self::$premier) $t5[] = array($l[0].$l[2], '', '', $l[3].$l[4], '', '');
		}
		
		if(!count($t5)) {
			$m_entree = self::$premier;
			$t5[] = array($m_entree, '', '', '', '', '');
			$t6[] = array($m_categorie, '');
			$pdo->execute(array(':fix'=>$a_chercher));
			if($pdo->rowCount()) {
				$sql = "select Nahuatl,Entree from `##_CEN-Entree` where ChachalacaId=$ids and Nahuatl='$m_entree'";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				foreach($rs as $r) {
					$t4_1[] = array($r['Nahuatl'], $r['Entree'], '');
					break;
				}
			}

		}
		
		klog::l("8 t5  ---".count($t5));
		foreach($t5 as &$l) klog::l("8 t5  $l[0],  $l[1],  $l[2],  $l[3]");
	}
	
	static private function arraySearch(&$arr, $col, &$val) {
		foreach($arr as $k=>&$v) {
			if($v[$col] == $val) return $k;
		}
		return false;
	}
	
	
	// PREFIXES ---------------------
	
	// 7_5
	static private function traitement7_5(&$t1, &$t7, &$t61) {
		$t61 = array();
		foreach($t7 as $k=>&$l) $t61[] = array($l[0], $l[1], $l[2], $l[3], $l[4]);
		
		$suf_reste = '';
		foreach($t61 as &$l0) {
			$pref = $l0[0];
			$pref_reste = $l0[1];
			$pref_cat = $l0[3];
			$taille_pref = strlen($pref);
			$taille_pref_reste = strlen($pref_reste);
			foreach($t1 as &$l1) {
				$analyse = $l1[0];
				$suf_cat = $l1[1];
				$p = strpos($analyse, '-');
				$suf = $p !== false ? substr($analyse, $p) : '';
				$suf_sans = str_replace('-', '', $suf);
				$taille_suf_sans = strlen($suf_sans);
				$taille_suf = strlen($suf);
				if(!$taille_pref) $t7[] = array($pref, $suf_reste, $suf, $pref_cat, $suf_cat);
				elseif($taille_suf) {
					if($taille_pref_reste >= $taille_suf_sans) {
						$s = $pref_reste.' ';
						$p = strpos($s, $suf_sans.' ');
						$p = $p === false ? 0 : ($p > 0 ? $p-1 : 0);
						$reste_def = substr($pref_reste, 0, $p);
						$t7[] = array($pref, $reste_def, $suf, $pref_cat, $suf_cat);
					}
					else {
						$t7[] = array($pref, $pref_reste, $suf, $pref_cat, $suf_cat);
						$t7[] = array($pref, $suf_reste, $suf, $pref_cat, $suf_cat);
					}
				}
			}
		}
		
		klog::l("7_5 t7a  ---".count($t7));
		foreach($t7 as &$l) {
			klog::l("7_5 t7  $l[0], $l[1], $l[2], $l[3], $l[4]");
		}

		foreach($t1 as &$l) {
			$analyse = $l[0];
			$suf_cat = $l[1];
			$p = strpos($analyse, '-');
			if($p !== false) {
				$suf = substr($analyse, $p);
				$reste_def = substr($analyse, 0, $p);
			}
			else {
				$suf = '';
				$reste_def = $analyse;
			}
			$t7[] = array('', $reste_def, $suf, '', $suf_cat);
		}

		klog::l("7_5 t7b  ---".count($t7));
		foreach($t7 as &$l) {
			klog::l("7_5 t7  $l[0], $l[1], $l[2], $l[3], $l[4]");
		}
		die;
	}

	
	// 5_3
	static private function combinaison5_3(&$t5, &$t7) {
		$nb_tour = 0;
		for($nb_tour = 0; $nb_tour < 5; $nb_tour++) {
			if(!count($t5[$nb_tour])) break;
		}

		$affixes = '';
		$categories = '';

		$compteur = 0;
		$nb_tour--;
		self::combinaison5_3b(0, $nb_tour, $t5, $t7, $affixes, $categories, $compteur);
	}

	static private function combinaison5_3b($n, $mx, &$t5, &$t7, &$affixes, &$categories, &$compteur) {
		$afx = "";
		$cat = "";
		foreach($t5[$n] as $l) {
			$afx = $affixes.$l[0];
			$cat = $categories.$l[2];
			if($n < $mx) self::combinaison5_3b($n + 1, $mx, $t5, $t7, $afx, $cat, $compteur);
			else {
				$reste = $l[1];
				$place_sep = strpos($reste, '-');
				if($place_sep !== false) {
					$racines = substr($reste, $place_sep - 1);
					$suffixes = substr($reste, $place_sep);
				} else {
					$racines = $reste;
					$suffixes = '';
				}
				$t7[] = array($afx, $racines, $suffixes, $cat, $l[3]);
				$compteur++;
				klog::l("5_3 t7  $afx,  $racines,  $suffixes,  $cat,  $l[3]");
			}
		}
	}
	
	// 4_2
	static private function analyse4_2(&$t62, &$t60, &$t7) {
		$sql = "select Categorie,Decompo from `##_CEN-Prefixe` where Prefixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		$t60 = array();
		foreach($t62 as &$l) $t60[] = array($l[0], $l[1], '', '');
		
		foreach($t60 as &$l) {
			$t5 = array([], [], [], [], []);
			$m_prefixes = $l[0];
			$cat_suf = $l[3];
			$m_reste = $l[1];

			$debut = $cpteur = 0;
			$m_analyse = $m_prefixes;
//klog::l(">>>a:$m_analyse");
			for(;;) {
				$debut = strpos($m_analyse, '-', $debut);
				if($debut === false) break;
				$cpteur++;
				$debut++;
			}
			$numcol = 0;
			$nb_tour = 0;
			$t_analyse = explode('-', $m_analyse);
			$n_analyse = count($t_analyse);
			while($numcol < $n_analyse && $nb_tour < $cpteur) {
				$ch = $t_analyse[$numcol++];
//klog::l(">>>b:$ch");
				$nb_pref = 0;
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$nb_pref++;
						$cat_1 = $r['Categorie'];
						$m_prefixe1 = $r['Decompo'];
//klog::l(">>>c:$m_prefixe1,  $cat_1");
						$t5[$nb_tour][] = array($m_prefixe1, $m_reste, $cat_1.'-', $cat_suf);
					}
				}
				$nb_tour++;
			}
for($i = 0; $i < 5; $i++) {
	$t = $t5[$i];
	for($j = 0; $j < count($t); $j++) {
		$u = $t[$j];
		klog::l("4_2 t5[$i]  ".$u[0]."  ".$u[1]."  ".$u[2]);
	}
}

			self::combinaison5_3($t5, $t7);
		}
		
	}


	static private function conditionPref7_2(&$t7) {
		foreach(self::$pre_suf as $cnd) {
			$c1 = !empty($cnd[1]);
			$c2 = !empty($cnd[2]);
			foreach($t7 as &$l) {
				if(!$c1 && !$c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					if($resultat1 !== false) $l[2] = '***';
				}
				else if($c1 && !$c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					$resultat2 = strpos($l[4], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[2] = '***';
				}
				else if(!$c1 && $c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					$resultat2 = strpos($l[4], $cnd[2]);
					if($resultat1 === false && $resultat2 !== false) $l[2] = '***';
				}
			}
		}

		foreach($t7 as $k=> &$l) if($l[2] == '***') unset($t7[$k]);
		$t7 = array_values($t7);

		usort($t7, 'self::sortC0C2C3C4');
		klog::l("7_2 t7  ---".count($t7));
		foreach($t7 as $k=>&$l) klog::l("7_2 t7  $k: ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3].",  ".$l[4]);
	}
	
	static private function conditionPref7_1(&$t60) {
		foreach(self::$pre_suf as $cnd) {
			$c1 = !empty($cnd[1]);
			$c2 = !empty($cnd[2]);
			foreach($t60 as &$l) {
				if(!$c1 && !$c2) {
					$resultat1 = strpos($l[2], $cnd[0]);
					if($resultat1 !== false) $l[0] = '***';
				}
				else if($c1 && !$c2) {
					$resultat1 = strpos($l[2], $cnd[0]);
					$resultat2 = strpos($l[3], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] = '***';
				}
				else if(!$c1 && $c2) {
					$resultat1 = strpos($l[2], $cnd[0]);
					$resultat2 = strpos($l[3], $cnd[2]);
					if($resultat1 !== false && $resultat2 === false) $l[0] = '***';
				}
			}
		}

		foreach($t60 as $k=> &$l) if($l[0] == '***') unset($t60[$k]);
		$t60 = array_values($t60);

		usort($t60, 'self::sortC0C1');
		klog::l("7_1 t60  ---".count($t60));
		foreach($t60 as $k=>&$l) klog::l("7_1 t60  $k: ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
	}


	// 6_5
	static private function conditionPref6_5(&$t7) {
		foreach(self::$pre_imp as $cnd) {
			$c1 = !empty($cnd[1]);
			$c2 = !empty($cnd[2]);
			foreach($t7 as &$l) {
				if(!$c1 && !$c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					if($resultat1 !== false) $l[2] = '***';
				}
				else if($c1 && !$c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					$resultat2 = strpos($l[3], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[2] = '***';
				}
			}
		}

		foreach($t7 as $k=> &$l) if($l[2] == '***') unset($t7[$k]);
		usort($t7, 'self::sortC0C2C3C4');
		$o = null;
		foreach($t7 as $k=>&$l) {
			if($o && $l[0] == $o[0] && $l[2] == $o[2] && $l[3] == $o[3] && $l[4] == $o[4]) unset($t7[$k]);
			else $o = $l;
		}
		$t7 = array_values($t7);
		
		klog::l("6_5 t7  ---".count($t7));
		foreach($t7 as &$l) klog::l("6_5 t7  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3].",  ".$l[4]);
	}

	// 6_2
	static private function conditionPref6_2(&$t62) {
		foreach(self::$pre_imp as $cnd) {
			$c1 = !empty($cnd[1]);
			$c2 = !empty($cnd[2]);
			foreach($t62 as &$l) {
				if(!$c1 && !$c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					if($resultat1 !== false) $l[1] = '***';
				}
				else if($c1 && !$c2) {
					$resultat1 = strpos($l[3], $cnd[0]);
					$resultat2 = strpos($l[3], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[1] = '***';
				}
			}
		}

		foreach($t62 as $k=> &$l) if($l[1] == '***') unset($t62[$k]);
		usort($t62, 'self::sortC3C4');
		
		klog::l("6_2 t62  ---".count($t62));
		foreach($t62 as &$l) klog::l("6_2 t62  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
	}


	// 5_7
	static private function nettoyage5_7(&$t62) {
		usort($t62, 'self::sortC0');
		foreach($t62 as &$l) {
			$ch = str_replace('--', '-', $l[0]);
			$l[0] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
			$ch = str_replace('--', '-', $l[2]);
			$l[2] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
		}
		usort($t62, 'self::sortC0');
		$o = null;
		foreach($t62 as $k=>&$l) {
			if($o && $l[0] == $o[0] && $l[1] == $o[1] && $l[2] == $o[2]) unset($t62[$k]);
			else $o = $l;
		}
		$t62 = array_values($t62);
		klog::l("5_7 t62  ---".count($t62));
		foreach($t62 as &$l) klog::l("5_7 t62  $l[0], $l[1], $l[2]");
	}

	// 5_2
	static private function nettoyage5_2(&$t61) {
		usort($t61, 'self::sortC0');
		foreach($t61 as &$l) {
			$ch = $l[0];
			self::remplaceUn($ch, '--', '-', 0);
			$l[0] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
			$ch = $l[2];
			self::remplaceUn($ch, '--', '-', 0);
			$l[2] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
		}
		usort($t61, 'self::sortC0');
		$o = null;
		foreach($t61 as $k=>&$l) {
			if($o && $l[0] == $o[0] && $l[1] == $o[1] && $l[2] == $o[2]) unset($t61[$k]);
			else $o = $l;
		}
		$t61 = array_values($t61);
		klog::l("5_2 t61  ---".count($t61));
		foreach($t61 as &$l) klog::l("5_2 t61  $l[0], $l[1], $l[2]");
	}

	// 4_1
	static private function generation4_1(&$t60, &$t62, &$t61) {
		$ttmp = array();
		foreach($t62 as &$l2) {
			$remplacement = $l2[1];
			foreach($t60 as &$l0) {
				$ch = $l0[0];
				$m_reste = $l0[2];
				$ch1 = $remplacement;
				$ch2 = '-'.$remplacement.'-';
				$ch = str_replace($ch1, $ch2, $ch);
				//$nb = self::remplace($ch, $ch1, $ch2);

				$s = $ch.$m_reste;
				if(!isset($ttmp[$s])) {
					$ttmp[$s] = 0;
					$t61[] = array($ch, $m_reste, $s, '', '');
					//klog::l("4_1 t61  $ch, $m_reste, $s");
				}
			}
		}
		klog::l("-------------------");
		self::nettoyage5_2($t61);
klog::l("-------------------");
		self::nettoyage5_2($t61);
		klog::l("-------------------");

		$ttmp = array();
		$t62 = array();
		foreach($t60 as &$l0) {
			$remplacement = $l0[1];
			//$m_reste = $l0[2];
			foreach($t61 as &$l1) {
				$ch = $l1[0];
				$m_reste = $l1[1];
				$ch1 = $remplacement;
				$ch2 = '-'.$remplacement.'-';
				$ch = str_replace($ch1, $ch2, $ch); //$nb = self::remplace($ch, $ch1, $ch2);
				$s = $ch.$m_reste;
				if(!isset($ttmp[$s])) {
					$ttmp[$s] = 0;
					$t62[] = array($ch, $m_reste, $s, '', '', '');
					//klog::l("4_1 t62  $ch, $m_reste, $s");
				}
			}
		} 
		klog::l("4_1 t62  ---".count($t62));
		foreach($t62 as $l) klog::l("4_1 t62  $l[0],  $l[1],  $l[2]");
	}

	static private function remplace(&$ch, $chini, $chremp) {
		$nb = 0;
		$l = strlen($chini);
		$pos = strpos($ch, $chini);
		while($pos !== false && $nb < 10) {
			$ch = substr($ch, 0, $pos).$chremp.substr($ch, $pos + $l);
			$pos = strpos($ch, $chini, ($pos + $l + 1));
			$nb++;
		}
		return $nb;
	}

	// 3_1
	static private function nettoyage3_1(&$t62, &$t60) {
		usort($t62, 'self::sortC0C1C2');
		foreach($t62 as $k=> &$l) {
			if($l[0] == 't-' && strpos('lz', substr($l[2], 0, 1)) !== false) unset($t62[$k]);
			if($l[0] == 'c-' && substr($l[2], 0, 1) == 'h') unset($t62[$k]);
		}
		usort($t62, 'self::sortC3C4');
		foreach($t62 as &$l) {
			$t60[] = array($l[0], $l[1], $l[2], '');
			klog::l("3_1 t60: $l[0],  $l[1],  $l[2]");
		}
	}

	// 2_6
	static private function prefixes2_6(&$t62) {
		$sql = "select Id from `##_CEN-Prefixe` where prefixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		$m_mot = self::$premier;
		$long_entree = strlen($m_mot);
		$trouve = 0;
		$trouve_pas = 0;
		$m_categorie = '';
		$ttmp = array();

		for($ind_61 = 0; $ind_61 < $long_entree; $ind_61++) {
			$m_entree = substr($m_mot, $ind_61);
			$longueur = strlen($m_entree); //$long_entree - $ind_61;
			for($ind_62 = 0; $ind_62 < 6; $ind_62++) {
				$trouve = 0;
				$long_coupe = $ind_62+1;
				$point_coupe = 0;
				$m_coupe = substr($m_entree, $point_coupe, $long_coupe);
				$m_reste = substr($m_entree, $point_coupe + $long_coupe, $longueur);
				$m_avant = substr($m_mot, 0, $ind_61);

				if(!empty($m_avant)) $m_precedent = "$m_avant-$m_coupe-";
				else $m_precedent = "$m_coupe-";

//klog::l("<<<$ind_61  $ind_62  $m_avant  $m_coupe  $m_reste  $m_precedent");
				$pdo->execute(array(':fix'=>$m_coupe));
				if($pdo->rowCount()) {
					$s = "$m_precedent\t$m_coupe\t$m_reste\t$m_avant";
					if(!isset($ttmp[$s])) {
						$ttmp[$s] = 0;
						$t62[] = array($m_precedent, $m_coupe, $m_reste, $m_avant.$m_coupe, $m_categorie, '');
klog::l("2_6 t62  $m_precedent, $m_coupe, $m_reste, $m_avant$m_coupe, $m_categorie");
					}
					$trouve++;
					$trouve_pas = 0;
				}
			} // Pour ind_62

			if($trouve == 0) {
				$trouve_pas++;
				if($trouve_pas > 2) break;
			}
		} // Pour ind_61
	}

	// SUFFIXES -----------------------------
	// 5_5
	static private function combinaison5_5(&$t5, &$t7) {
		$nb_tour = 0;
		for($nb_tour = 0; $nb_tour < 5; $nb_tour++) {
			if(!count($t5[$nb_tour])) break;
		}

		$affixes = '';
		$categories = '';

		$compteur = 0;
		$nb_tour--;
		self::combinaison5_5b(0, $nb_tour, $t5, $t7, $affixes, $categories, $compteur);
	}

	static private function combinaison5_5b($n, $mx, &$t5, &$t7, &$affixes, &$categories, &$compteur) {
		$afx = "";
		$cat = "";
		foreach($t5[$n] as $l) {
			$afx = $affixes.$l[0];
			$cat = $categories.$l[2];
			if($n < $mx) self::combinaison5_5b($n + 1, $mx, $t5, $t7, $afx, $cat, $compteur);
			else {
				$reste = $l[1];
				$place_sep = strpos($reste, '-');
				if($place_sep !== false) {
					$racines = substr($reste, $place_sep - 1);
					$suffixes = substr($reste, $place_sep);
				} else {
					$racines = $reste;
					$suffixes = '';
				}
				$t7[] = array('', $racines, $afx, '', $cat);
				$compteur++;
				klog::l("5_5 t7  $racines,  $afx,  $cat");
			}
		}
	}

	// 4_4
	static private function analyse4_4(&$t60, &$t7) {
		$sql = "select Categorie,Decompo from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		foreach($t60 as &$l) {
			$t5 = array([], [], [], [], []);
			$m_prefixes = $l[0];
			$cat_suf = $l[3];
			$m_reste = $l[1];

			$debut = $cpteur = 0;
			$m_analyse = $m_prefixes;
			for(;;) {
				$debut = strpos($m_analyse, '-', $debut);
				if($debut === false) break;
				$cpteur++;
				$debut++;
			}
			$numcol = 0;
			$nb_tour = 0;
			$t_analyse = explode('-', $m_analyse);
			$n_analyse = count($t_analyse);
			while($numcol < $n_analyse && $nb_tour < $cpteur) {
				$numcol++;
				$ch = $t_analyse[$numcol];
				$nb_pref = 0;
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$cat_1 = $r['Categorie'];
						$m_prefixe1 = $r['Decompo'];
						$nb_pref++;
						$t5[$nb_tour][] = array($m_prefixe1, $m_reste, '-'.$cat_1, $cat_suf);
					}
				}
				$nb_tour++;
			}
for($i = 0; $i < 5; $i++) {
	$t = $t5[$i];
	for($j = 0; $j < count($t); $j++) {
		$u = $t[$j];
		klog::l("4_4 t5[$i]  ".$u[0]."  ".$u[1]."  ".$u[2]);
	}
}

			self::combinaison5_5($t5, $t7);
		}
	}

	// 38_1
	static private function redoublement38_1() {
		$bon_binome = $bon_trinome = '';
		$origine = self::$premier;
		$l = strlen($origine);
		for($i = 0; $i < $l; $i++) {
			$binome = substr($origine, $i, 2);
			$bi_suivant = substr($origine, $i+2, 2);
			if($binome == $bi_suivant) $bon_binome = $binome;
		}
		$ch = $origine;
		$nb = self::remplaceRed($ch, $bon_binome, '$$');
		
		$origine = $ch;
		$l = strlen($origine);
		for($i = 0; $i < $l; $i++) {
			$trinome = substr($origine, $i, 3);
			$tri_suivant = substr($origine, $i+3, 3);
			if($trinome == $tri_suivant) $bon_trinome = $trinome;
		}
		$ch = $origine;
		$nb = self::remplaceRed($ch, $bon_trinome, '$$$');
		
		return $ch;
	}
	static private function remplaceRed(&$ch, $chini, $chremp) {
		$nb = 0;
		$pos = strpos($ch, $chini);
		if($pos !== false) {
			$ch = substr($ch, 0, $pos).$chremp.substr($ch, $pos+strlen($chremp));
			$nb++;
		}
		return $nb;
	}

//	static private function sort62c68($a, $b) {
//		return strcmp($a[0], $b[0]);
//	}
	
	static private function sortC0($a, $b) {
		return strcmp($a[0], $b[0]);
	}
	static private function sortC1($a, $b) {
		return strcmp($a[1], $b[1]);
	}
	static private function sortC2($a, $b) {
		return strcmp($a[2], $b[2]);
	}
	static private function sortC0C1($a, $b) {
		$c = strcmp($a[0], $b[0]);
		return $c ? $c : strcmp($a[1], $b[1]);
	}
	static private function sortC0C2($a, $b) {
		$c = strcmp($a[0], $b[0]);
		return $c ? $c : strcmp($a[2], $b[2]);
	}
	static private function sortC0C1C2($a, $b) {
		$c = strcmp($a[0], $b[0]);
		if($c) return $c;
		$c = strcmp($a[1], $b[1]);
		return $c ? $c : strcmp($a[2], $b[2]);
	}
	static private function sortC0C2C3C4($a, $b) {
		$c = strcmp($a[0], $b[0]);
		if($c) return $c;
		$c = strcmp($a[2], $b[2]);
		if($c) return $c;
		$c = strcmp($a[3], $b[3]);
		return $c ? $c : strcmp($a[4], $b[4]);
	}
	static private function sortC3C4($a, $b) {
		$c = strcmp($a[3], $b[3]);
		return $c ? $c : strcmp($a[4], $b[4]);
	}

	static private function sort62c68c69($a, $b) {
		$c = strcmp($a[0], $b[0]);
		return $c ? $c : strcmp($a[1], $b[1]);
	}

	static private function sort61c64($a, $b) {
		return strcmp($a[0], $b[0]);
	}

	static private function sort60c63($a, $b) {
		return strcmp($a[2], $b[2]);
	}

	static private function sort62c71c72($a, $b) {
		$c = strcmp($a[3], $b[3]);
		return $c ? $c : strcmp($a[4], $b[4]);
	}

	// 5_1
	static private function nettoyage5_1(&$t62, &$t60) {
		$t60 = array();
		usort($t62, 'self::sortC0C1');
		$o = array('');
		foreach($t62 as $k=>&$l) {
			if($k && $l[0] == $o[0] && $l[1] == $o[1]) unset($t62[$k]);
			else $o = $l;
		}
		$t62 = array_values($t62);
		foreach($t62 as &$l) $t60[] = array($l[0], '', '', '');
		
		usort($t60, 'self::sortC0C1');
foreach($t60 as &$l) klog::l("5_1 t60  ".$l[0]);
	}
	
	// 3_3
	static private function conditionsSuf3_3(&$t1) {
		foreach(self::$suf_imp as $cnd) {
			foreach($t60 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[1], $cnd[0]);
					if($resultat1 !== false) $l[0] = '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[1], $cnd[0]);
					$resultat2 = strpos($l[1], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] = '***';
				}
			}
		}


		foreach($t1 as $k=> &$l) if($l[0] == '***' || empty(trim($l[1]))) unset($t1[$k]);
		$t1[] = array(self::$premier, '', '');
		usort($t1, 'self::sortC0C1');
		$o = null;
		foreach($t1 as $k=>&$l) {
			if($o && $l[0] == $o[0] && $l[1] == $o[1]) unset($t1[$k]);
			else $o = $l;
		}
		$t1 = array_values($t1);

		klog::l("3_3 t1  ---".count($t1));
		foreach($t60 as &$l)
			klog::l("3_3 t1  ".$l[0].",  ".$l[1].",  ".$l[2]);

	}


	// 6_4
	static private function conditionsSuf6_4(&$t60) {
		foreach(self::$suf_imp as $cnd) {
			foreach($t60 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[2], $cnd[0]);
					if($resultat1 !== false) $l[0] = '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[2], $cnd[0]);
					$resultat2 = strpos($l[2], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] = '***';
				}
			}
		}



		foreach($t60 as $k=> &$l) {
			if($l[0] == '***' || strpos($l[0], 0, 1) == '-') unset($t60[$k]);
			else $l[1] = explode('-', $l[0])[0];
		}
		usort($t60, 'self::sortC2');
		klog::l("6_4 t60  ---".count($t60));
		foreach($t60 as &$l)
			klog::l("6_4 t60  ".$l[0].",  ".$l[1].",  ".$l[2]);

	}
	
	// 6_1
	static private function categorisation6_1(&$t62) {
		$sql = "select Categorie from `##_CEN-Prefixe` where Prefixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		usort($t62, 'self::sortC0C1');
		
		foreach($t62 as &$l) {
			$debut = $cpteur = 0;
			$m_analyse = trim($l[0]);
			for(;;) {
				$debut = strpos($m_analyse, '-', $debut);
				if($debut === false) break;
				$cpteur++;
				$debut++;
			}
//klog::l('6_3<<<'.$m_analyse.':'.$cpteur);
			$numcol = $nb_tour = 0;
			$ch = "";
			$tout_cat = "";
			$t_analyse = explode('-', $m_analyse);
			$n_analyse = count($t_analyse);
			while($numcol < $n_analyse && $nb_tour < $cpteur) {
				$nb_tour++;
				$ch = $t_analyse[$numcol++];
				//$numcol++;
				$nb_pref = 0;
//klog::l('6_3>>>'.$ch.':');
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					$m_categorie = '';
					foreach($rs as $r) {
						$m_categorie = trim($r['Categorie']);
						$nb_pref++;
					}
					if($nb_pref == 1) {
						if($nb_tour == 1) $tout_cat .= $m_categorie;
						else $tout_cat .= '-'.$m_categorie;
					} else $tout_cat .= '- ? ';
				}
				else $tout_cat = 'effacer';
			}
			$l[3] = $tout_cat;
//klog::l('6_3>>>'.$tout_cat);
		}
		foreach($t62 as $k => &$l) {
			if(strpos($l[3], 'effacer') !== false) unset($t60[$k]);
		}
		$t62 = array_values($t62);

		klog::l("6_1 t62  ---".count($t62));
		foreach($t62 as &$l) klog::l("6_1 t62  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
	}


	// 6_3 
	static private function categorisation6_3(&$t60) {
		$sql = "select Categorie from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		usort($t60, 'self::sortC0C1');
		
		foreach($t60 as &$l) {
			$debut = 0;
			$cpteur = 0;
			$m_analyse = trim($l[0]);
			for(;;) {
				$debut = strpos($m_analyse, '-', $debut);
				if($debut === false) break;
				$cpteur++;
				$debut++;
			}
//klog::l('6_3<<<'.$m_analyse.':'.$cpteur);
			$numcol = $nb_tour = 0;
			$ch = "";
			$tout_cat = "";
			$t_analyse = explode('-', $m_analyse);
			$n_analyse = count($t_analyse);
			while($numcol < $n_analyse && $nb_tour < $cpteur) {
				$nb_tour++;
				$numcol++;
				$ch = $t_analyse[$numcol];
				$nb_pref = 0;
//klog::l('6_3>>>'.$ch.':');
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$m_categorie = trim($r['Categorie']);
						$nb_pref++;
					}
					if($nb_pref == 1) {
						if($nb_tour == 1) $tout_cat .= $m_categorie;
						else $tout_cat .= '-'.$m_categorie;
					} else $tout_cat .= '- ? ';
				} else $tout_cat = 'effacer';
			}
			$l[2] = $tout_cat;
//klog::l('6_3>>>'.$tout_cat);
		}
		foreach($t60 as $k => &$l) if(strpos($l[2], 'effacer') !== false) unset($t60[$k]);
		$t60 = array_values($t60);

		foreach($t60 as &$l) klog::l("6_3 t60  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);

	}

	// 4_5
	static private function generation4_5(&$t60, &$t61, &$t62) {
		foreach($t60 as &$l0) {
			$mot_liste = $l0[0];
			$t62[] = array($mot_liste, '', '', '', '', '');

			foreach($t61 as &$l1) {
				$m_suffixe = $l1[1];
				$debut = $cpteur = 0;
				$pos = array(0, 0, 0, 0, 0);
				for(; $cpteur < 5;) {
					$debut = strpos($mot_liste, $m_suffixe, $debut);
					if($debut === false) break;
					$pos[$cpteur++] = $debut++;
				}
				self::introModif($t62, $mot_liste, $m_suffixe, $cpteur, $pos);
			}
		}
		usort($t62, 'self::sortC0C1');
		foreach($t62 as &$l) {
			$s = $l[0];
			if(substr($s, -1, 1) == '-') $l[0] = substr($s, 0, strlen($s) - 1);
			klog::l("4_5 t62  ".$l[0]);
		}
	}

	// 4_3
	static private function generation4_3(&$t61, &$t60) {
		$m_premier = self::$premier;
		foreach($t61 as &$l) {
			$m_suffixe = $l[1];
			$debut = $cpteur = 0;
			$pos = array(0, 0, 0, 0, 0);
			for(; $cpteur < 5;) {
				$debut = strpos($m_premier, $m_suffixe, $debut);
				if($debut === false) break;
				$pos[$cpteur++] = $debut++;
			}
			self::introModif($t60, $m_premier, $m_suffixe, $cpteur, $pos);
		}
		usort($t60, 'self::sortC0C1');
		foreach($t60 as &$l) {
			$s = $l[0];
			if(substr($s, -1, 1) == '-') $l[0] = substr($s, 0, strlen($s) - 1);
			klog::l("4_3 t60  ".$l[0]);
		}
	}

	static private function introModif(&$t60, $m_premier, $m_suffixe, $cpteur, $pos) {
		$ch = $m_premier;
		$ch1 = $m_suffixe;
		$ch2 = '-'.$m_suffixe.'-';
		$len = strlen($ch1);
		for($p0 = 0; $p0 < $cpteur; $p0++) {
			$ch = $m_premier;
			for($p1 = $p0; $p1 >= 0; $p1--) {
				$p = strpos($ch, $ch1, $pos[$p1]);
				if($p !== false) $ch = substr($ch, 0, $p).$ch2.substr($ch, $p + $len);
				$t60[] = array($ch, '', '', '', '', '');
			}
		}
	}

	static private function remplaceUn(&$ch, $chini, $chremp, $posini) {
		$p = strpos($ch, $chini, $posini);
		if($p !== false) $ch = substr($ch, 0, $p).$chremp.substr($ch, $p + strlen($chini));
		return $pos;
	}

	// 2_1
	static private function suffixes2_1(&$t62) {
		$sql = "select Decompo from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		$ttmp = array();
		$m_mot = self::$premier;
		$long_entree = strlen($m_mot);

		$m_categorie = '';
		for($ind_61 = $long_entree; $ind_61 > 1; $ind_61--) {
			$m_entree = substr($m_mot, 0, $ind_61);
			$avance = 0;
			$long_reste = $ind_61;
			//rajouté pour les problèmes rencontrés avec acaltica
			$trouve_pas = 0;
			for($ind_62 = $long_reste; $ind_62 > ($long_reste - 10); $ind_62--) {
				if($ind_62 >= 0) {
					$avance++;
					$trouve = 0;
					$long_coupe = $avance;
					$point_coupe = ($long_reste - $avance);
					if($point_coupe < 0) $point_coupe = 0;
					$m_coupe = substr($m_entree, $point_coupe, $long_coupe);
					$m_reste = substr($m_entree, $point_coupe + $long_coupe, $long_entree);
					$m_avant = substr($m_mot, 0, $point_coupe);

					if($m_reste != '') {
						if($m_avant != '') $m_precedent = "$m_avant-$m_coupe-$m_reste";
						else $m_precedent = "$m_coupe-$m_reste";
					} else $m_precedent = "$m_avant-$m_coupe";

					$pdo->execute(array(':fix'=>$m_coupe));
					if($pdo->rowCount()) {
						if(!empty($m_avant)) {
							$s = "$m_coupe\t$m_avant";
							if(!isset($ttmp[$s])) {
								$ttmp[$s] = 0;
								$t62[] = array('-'.$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie, '');
								klog::l("-$m_coupe, $m_coupe, $m_avant, $m_avant$m_coupe, $m_categorie");
							}
						}
						$trouve++;
						$trouve_pas = 0;
					}
					// else sortir
					if($trouve == 0) {
						$trouve_pas++;
						// Attention c'est ce chiffre qui détermine jusqu'où on remonte pour chercher un nouveau suffixe.....
						if($trouve_pas > 5) {
							$trouve_pas = 0;
							break;
						}
					} else {
						//sortir
					} // si ind_62
				} // ind_62 >=0
			} // Pour ind_62
		} // Pour ind_61
	}

	static public function CheckGrammar($text, $lang) {
		$dir = '/Home/2/CEN/Chachalaca/grammaire/';
		$lix = array_search($lang, ['es','fr','en']);
		$lg = ['.esp','.fra','.ang'][$lix];
		if(file_exists(getcwd().$dir.$text.$lg)) return $lg;
		if(file_exists(getcwd().$dir.$text.'.esp')) return '.esp';
		return false;
	}


	static public function GetGrammar($args) {
		$dir = '/Home/2/CEN/Chachalaca/grammaire/';
		
		switch($args['type']) {
			case 'grammar':
				$text = $args['text'];
				if(empty($text)) $text = 'liste'.self::CheckGrammar('liste', $args['lang']);
				$tmp = file_get_contents(getcwd().$dir.$text);
				$txt = utf8_encode(nl2br($tmp));
				return array('text'=>$txt);	
				
			case 'dict':
				break;
		}
		
	}
}
