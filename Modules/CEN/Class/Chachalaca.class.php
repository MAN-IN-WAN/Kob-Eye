<?php

class Chachalaca extends genericClass {
	
	// structurer l'import :
	var $imports = [
		'd_'=>"`##_CEN-Entree`|(ChachalacaId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Entree`, `Commentaire`, `Traduc`, `Nahuatl`, `Categorie`, `Racines`)",
		'r_'=>"`##_CEN-Radix`|(ChachalacaId,`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Nahuatl`, `Espagnol`, `Racines`, `Categorie`, `Source`, `Bases`, `Transit`, `RacineCat`)",
		'prefixes'=>"`##_CEN-Prefixe`|(`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Prefixe`, `Decompo`, `Couper`, `Categorie`, `Commentaire`, `NbCarac`, `CommentaireEs`)",
		'suffixes'=>"`##_CEN-Suffixe`|(`tmsCreate`, `userCreate`, `tmsEdit`, `userEdit`, `uid`, `gid`, `umod`, `gmod`, `omod`,`Suffixe`, `Decompo`, `Couper`, `Categorie`, `Commentaire`, `NbCarac`, `CommentaireEs`)"
	];

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
		
		$fs = array_diff(scandir("$tmp"), array('..', '.'));
		foreach($fs as $k=>$table) {
			$f = explode('.', $table);
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
				$val = explode('|',$val);
				
				$id = 0;
				$sql = '';
				if($cod) {
					$cha = Sys::getOneData('CEN', 'Chachalaca/Code='.$cod);
					if($cha) {
						$id = $cha->Id;
						//$cha->Delete();
						$sql = "delete from ".$val[0]." where ChachalacaId=$id";
					}
					else $sql = "insert into `##_CEN-Chachalaca` (`tmsCreate`,`userCreate`,`tmsEdit`,`userEdit`,`uid`,`gid`,`umod`,`gmod`,`omod`,Code) values(0,2,0,2,2,2,7,7,7,'$cod')";
				}
				else $sql = "delete from ".$val[0];
				
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$GLOBALS['Systeme']->Db[0]->exec($sql);
				if($cod && !$id) $id = $GLOBALS['Systeme']->Db[0]->lastInsertId();
					
				$v = file_get_contents("$tmp/$table");
				$v = utf8_encode($v);
				$ar = explode("\r\n", $v);
				$v = "";
				foreach($ar as $a) {
					if($a == '') continue;
					$a = str_replace("'","''",$a);
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
	
	static private $suf_imp;
	static private $pre_imp;
	
	static function Suff($args) {
		$word = $args['word'];
		
		$m_premier = $word;

		$r = Sys::getOneData('CEN', 'Regle/Code=CHACHALACA&Regle=SUF_IMPOSSIBLE');
		self::$suf_imp = explode("\r\n", utf8_encode(file_get_contents(getcwd().'/'.$r->FilePath)));
		$r = Sys::getOneData('CEN', 'Regle/Code=CHACHALACA&Regle=PREF_IMPOSSIBLE');
		self::$pre_imp = explode("\r\n", utf8_encode(file_get_contents(getcwd().'/'.$r->FilePath)));

		// 38_1  affiche le bouton
		//$m_premier = self::redoublement($m_premier);

		// 2_1 + 3_2 
		$t62 = array();
		self::suffixes($m_premier, $t62);
		// 3_2
		$t61 = array();
		foreach($t62 as $k => &$l) {
			$t61[] = [$l[0], $l[1], '', '', ''];
klog::l("t61  ".$l[0].', '.$l[1]);
		}
		usort($t61, 'self::sort61c64');
		// 4_3
		$t60 = array();
		self::generation($m_premier, $t61, $t60);
		// 6_3
		self::categorisation($t60, false);
		// 6_4
		self::nettoyageSuf($t60);
		// 4_4 + 5_5
		$t3 = array();
		$t3[] = $m_premier;
		$t7 = array();
		self::analyse($t60, $t7);
		// 7_3
		$t1 = array();
		foreach($t7 as $l) $t1[] = [$l[1].$l[2],'',$l[4]];
		// 6_4 (3_3)
		self::nettoyageSuf($t1);

		// PREFIXES ---------------------
klog::l("PRFX----------");
		
		// 2_6
		$t62 = array();
		self::prefixes($m_premier, $t62);
		// 3_1
		$t60 = array();
		self::nettoyage($t62, $t60);
		// 4_1 + 5_2 
		$t61 = array();
		self::generation4_1($t60, $t62, $t61);
		// 5_7
		self::nettoyage5_2($t62);
		// 6_1
		self::categorisation($t62, true);
		// 6_2
		self::nettoyagePref($t62);
		

		return array('suf'=>$t60);
	}
	
	
	// 6_2
	static private function nettoyagePref(&$t62) {
		foreach(self::$pre_imp as $si) {
			$cnd = explode("\t", $si);
			foreach($cnd as &$c) $c = trim($c);
			
			foreach($t62 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[3], $cnd[0]);
					if($resultat1 !== false) $l[0] == '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[3])) {
					$resultat1 = strpos($l[3], $cnd[0]);
					$resultat2 = strpos($l[3], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] == '***';
				}
			}
		}
		
		foreach($t62 as $k => &$l) {
			klog::l("6_2 t62  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
			if($l[0] == '***') unset($t62[$k]);
		}
		
		usort($t62, 'self::sort62c71c72');
		foreach($t62 as &$l) klog::l("6_2 t62+  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
	}
	
	
//	// 5_7
//	static private function nettoyage5_7(&$t61) {
//		foreach($t62 as &$l) {
//			$ch = $l[0];
//			self::remplaceUn($ch, '--', '-', 0);
//			$l[0] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
//			$ch = $l[2];
//			self::remplaceUn($ch, '--', '-', 0);
//			$l[2] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
//		}
//		usort($t61, 'self::sort62c68');
//		$n = count($t61);
//		for($i = 1; $i < $n; $i++) {
//			if($t61[$i][0].$t61[$i][1].$t61[$i][2] == $t61[$i-1][0].$t61[$i-1][1].$t61[$i-1][2]) $t61[$i][3] = '***';
//		}
//		foreach($t61 as $k => &$l) {
//			if($l[3] == '***') unset($t61[$k]);
//			else klog::l("5_7 t62  $l[0], $l[1], $l[2]");
//		}
//	}
	
	// 5_2 (5_7)
	static private function nettoyage5_2(&$t61) {
		foreach($t61 as &$l) {
			//$ch = $l[0];
			$ch = str_replace('--', '-', $l[0]);  //self::remplaceUn($ch, '--', '-', 0);
			$l[0] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
			//$ch = $l[2];
			$ch = str_replace('--', '-', $l[2]);  //self::remplaceUn($ch, '--', '-', 0);
			$l[2] = substr($ch, 0, 1) == '-' ? substr($ch, 1) : $ch;
		}
		usort($t61, 'self::sort61c64');
		$n = count($t61);
		for($i = 1; $i < $n; $i++) {
			if($t61[$i][0].$t61[$i][1].$t61[$i][2] == $t61[$i-1][0].$t61[$i-1][1].$t61[$i-1][2]) $t61[$i][3] = '***';
		}
		foreach($t61 as $k => &$l) {
			if($l[3] == '***') unset($t61[$k]);
			else klog::l("5_2 t61  $l[0], $l[1], $l[2]");
		}
	}
	
	// 4_1
	static private function generation4_1(&$t60, &$t62, &$t61) {
		$ttmp = array();
		foreach($t62 as &$l2) {
			$remplacement = $l2[1];
			//$m_reste = $l2[3];
			foreach($t60 as &$l0) {
				$ch = $l0[0];
				$m_reste = $l0[2];
				$ch1 = $remplacement;
				$ch2 = '-'.$remplacement.'-';
				$nb = self::remplace($ch, $ch1, $ch2);
				$s = $ch.$m_reste;
				if(!isset($ttmp[$s])) {
					$ttmp[$s] = 0;
					$t61[] = [$ch, $m_reste, $ch.$m_reste, '', ''];
klog::l("4_1 t61  $ch, $m_reste, $ch$m_reste");
				}
			}
		}
klog::l("-------------------");
		self::nettoyage5_2($t61);
//klog::l("-------------------");
//		self::nettoyage5_2($t61);
klog::l("-------------------");
		
		$t62 = array();
		foreach($t60 as $l0) {
			$remplacement = $l0[1];
			//$m_reste = $l0[2];
			foreach($t61 as $l1) {
				$ch = $l1[0];
				$m_reste = $l1[1];
				$ch1 = $remplacement;
				$ch2 = '-'.$remplacement.'-';
				$nb = self::remplace($ch, $ch1, $ch2);
				$s = $ch.$reste;
				if(!isset($ttmp[$s])) {
					$ttmp[$s] = 0;
					$t62[$ch.$reste] = [$ch, $m_reste, $ch.$m_reste, '', '', '']; 
klog::l("4_1 t62  $ch, $m_reste, $ch$m_reste");
				}
			}
		}
	}
	
	static private function remplace(&$ch, $chini, $chremp) {
		$nb = 0;
		$l = strlen($chini);
		$pos = strpos($ch, $chini);
		while($pos !== false && $nb < 10) {
			$ch = substr($ch, 0, $pos).$chremp.substr($ch, $pos+$l);
			$pos = strpos($ch, $chini, ($pos+$l+1));
			$nb++;
		}
		return $nb;
	}


	// 3_1
	static private function nettoyage(&$t62, &$t60) {
		foreach($t62 as $k=>$l) {
			if($l[0] == 't-' && strpos('lz', substr($l[2], 0, 1)) !== false) unset($t62[$k]);
			if($l[0] == 'c-' && substr($l[2], 0, 1) == 'h') unset($t62[$k]);
		}
		usort($t62, 'self::sort62c71c72');
		foreach($t62 as $l) {
			$t60[] = [$l[0], $l[1], $l[2], ''];
klog::l("3_1 t60: $l[0],  $l[1],  $l[2]");
		}
	}

	// 2_6
	static private function prefixes($m_premier, &$t62) {
		$sql = "select Id from `##_CEN-Prefixe` where prefixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);	

		$m_mot = $m_premier;
		$long_entree = strlen($m_mot);
		$trouve = 0;
		$trouve_pas = 0;
		$m_categorie = '';
		$ttmp = array();
		
		for($ind_61 = 0; $ind_61 < $long_entree; $ind_61++) {
			$m_entree = substr($m_mot, $ind_61);
			$longueur = $long_entree-$ind_61;
			for($ind_62 = 0; $ind_62 < 6; $ind_62++) {
				$trouve = 0;
				$long_coupe = $ind_62;
				$point_coupe = 0;
				$m_coupe = substr($m_entree, $point_coupe, $long_coupe);
				$m_reste = substr($m_entree, $point_coupe+$long_coupe, $longueur);
				$m_avant = substr($m_mot, 0, $ind_61);

				if(!empty($m_avant)) $m_precedent = "$m_avant-$m_coupe-";
				else $m_precedent = "$m_coupe-";

				$pdo->execute(array(':fix'=>$m_coupe));
				if($pdo->rowCount()) {
					$s = "$m_precedent\t$m_coupe\t$m_reste";
					if(!isset($ttmp[$s])) {
						$ttmp[$s] = 0;
						$t62[] = [$m_precedent, $m_coupe, $m_reste, $m_avant.$m_coupe, $m_categorie, ''];
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
	static private function combinaison(&$t5, &$t7) {
		for($nb_tour = 0; $nb_tour < 5; $nb_tour++) {
			if(!count($t5[$nb_tour])) break;
		}
		
		$affixes = '';
		$categories = '';

		$compteur = 0;
		$nb_tour--;
		self::combinaison2(0, $nb_tour, $t5, $t7, $affixes, $categories, $compteur);
	}
	static private function combinaison2($n, $mx, &$t5, &$t7, &$affixes, &$categories, &$compteur) {
		$afx = "";
		$cat = "";
		foreach($t5[$n] as $l) {
			$afx = $affixes.$l[0];
			$cat = $categories.$l[2];
			if($n < $mx) self::combinaison2($n+1, $mx, $t5, $t7, $afx, $cat, $compteur);
			else {
				$reste = $l[1];
				$place_sep = strpos($reste, '-');
				if($place_sep !== false) {
					$racines = substr($reste, $place_sep-1);
					$suffixes = substr($reste, $place_sep);
				}
				else {
					$racines = $reste;
					$suffixes = '';
				}
				$t7[] = ['', $racines, $afx, '', $cat];
				$compteur++;
klog::l("t7  $racines,  $afx, $cat");				
			}
		}
	}


	// 4_4
	static private function analyse(&$t60, &$t7) {
		$sql = "select Categorie,Decompo from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);	

		
		foreach($t60 as &$l) {
			$t5 = array([],[],[],[],[]);
			$m_prefixes = $l[0];
			$cat_suf = $l[3];
			$m_reste = $l[1];
			
			$debut = 0;
			$cpteur = 0;
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
						$t5[$nb_tour][] = [$m_prefixe1, $m_reste, '-'.$cat_1, $cat_suf];
					}
				}
				$nb_tour++;
			}
for($i = 0; $i < 5; $i++) {
	$t = $t5[$i];
	for($j = 0; $j < count($t); $j++){
		$u = $t[$j];
		klog::l("t5[$i]  ".$u[0]."  ".$u[1]."  ".$u[2]);
	}
}

			self::combinaison($t5, $t7);
		}

	}
	
	// 38_1
	static private function redoublement($m_premier) {
		return $m_premier;
	}
	
	static private function sort62c68($a, $b) {
		return strcmp($a[0], $b[0]);
	}
	static private function sort61c64($a, $b) {
		return strcmp($a[0], $b[0]);
	}
	static private function sort60c63($a, $b) {
		return strcmp($a[2], $b[2]);
	}
	static private function sort62c71c72($a, $b) {
		return strcmp($a[3].$a[4], $b[3].$b[4]);
	}
	
	// 6_4
	static private function nettoyageSuf(&$t60) {
		foreach(self::$suf_imp as $si) {
			$cnd = explode("\t", $si);
			foreach($cnd as &$c) $c = trim($c);
			
			foreach($t60 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[2], $cnd[0]);
					if($resultat1 !== false) $l[0] == '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[3])) {
					$resultat1 = strpos($l[2], $cnd[0]);
					$resultat2 = strpos($l[2], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] == '***';
				}
			}
		}
		
		foreach($t60 as $k => &$l) {
			if($l[0] == '***' || substr($l[0], 0, 1) == '-') unset($t60[$k]);
			else $l[1] = explode('-', $l[0])[0];
		}	
		
		usort($t60, 'self::sort60c63');
		foreach($t60 as &$l) klog::l("6_4 t60  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
	}
	
	
	// 6_3
	static private function categorisation(&$t60, $pfx) {
		$ps = $pfx ? 'Prefixe' : 'Suffixe';
		$sql = "select Categorie from `##_CEN-$ps` where $ps=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);	

		foreach($t60 as &$l) {
			$debut = 0;
			$cpteur = 0;
			$m_analyse = trim($l[0]);
klog::l('<<<'.$m_analyse.':');
			for(;;) {
				$debut = strpos($m_analyse, '-', $debut);
				if($debut === false) break;
				$cpteur++;
				$debut++;
			}
			$numcol = 0;
			$nb_tour = 0;
			$ch = "";
			$tout_cat = "";
			$t_analyse = explode('-', $m_analyse);
			$n_analyse = count($t_analyse);
			while($numcol < $n_analyse && $nb_tour < $cpteur) {
				$nb_tour++;
				$ch = $t_analyse[$numcol+($pfx ? 0 : 1)];
				$numcol++;
				$nb_pref = 0;
klog::l('>>>'.$ch.':');
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$m_categorie = $r['Categorie'];
						$nb_pref++;
					}
					if($nb_pref == 1) {
						if($nb_tour == 1) $tout_cat .= $m_categorie;
						else $tout_cat .= ' - '.$m_categorie;
					}
					else $tout_cat .= ' - ? ';
				}
				else $tout_cat = 'effacer';				
			}
			$l[2] = $tout_cat;
klog::l("6_3-6_1 t60  $m_analyse  $tout_cat");
		}
		foreach($t60 as $k => $l) if(strpos($l[2], 'effacer') !== false) unset($t60[$k]);

foreach($t60 as &$l) klog::l("6_3-6_1 t60+  ".$l[0].",  ".$l[1].",  ".$l[2]);

	}
	
	// 4_3
	static private function generation($m_premier, &$t61, &$t60) {
		foreach($t61 as $l) {
			$m_suffixe = $l[1];
			$debut = $cpteur = 0;
			$pos = [0, 0, 0, 0, 0];
			for(;$cpteur<5;) {
				$debut = strpos($m_premier, $m_suffixe, $debut);
				if($debut === false) break;
				$pos[$cpteur++] = $debut++;
			}
			self::introModif($t60, $m_premier, $m_suffixe, $cpteur, $pos);
		}
		foreach($t60 as &$l) {
			$s = $l[0];
			if(substr($s, -1, 1) == '-') $l[0] = substr($s, 0, strlen($s)-1);
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
				if($p !== false) $ch = substr($ch, 0, $p).$ch2.substr($ch, $p+$len);
				$t60[] = [$ch,'','',''];
			}
		}
	}
	
	static private function remplaceUn(&$ch, $chini, $chremp, $posini) {
		$p = strpos($ch, $chini, $posini);
		if($p !== false) $ch = substr($ch, 0, $p).$chremp.substr($ch, $p+strlen($chini));
		return $pos;
	}
	
	// 2_1
	static private function suffixes($m_premier, &$t62) {
		$sql = "select Decompo from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);	

		$ttmp = array();
		$m_mot = $m_premier;
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
					$m_reste = substr($m_entree, $point_coupe+$long_coupe, $long_entree);
					$m_avant = substr($m_mot, 0, $point_coupe);
					
					if($m_reste != '') {
						if($m_avant != '') $m_precedent = "$m_avant-$m_coupe-$m_reste";
						else $m_precedent = "$m_coupe-$m_reste";
					}
					else $m_precedent = "$m_avant-$m_coupe";

					$pdo->execute(array(':fix'=>$m_coupe));
					if($pdo->rowCount()) {
						if(!empty($m_avant)) {
							$s = "$m_coupe\t$m_avant";
							if(!isset($ttmp[$s])) {
								$ttmp[$s] = 0;
								 $t62[] = ['-'.$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie, ''];
klog::l("-$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie");
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
					}
					else {
						//sortir
					} // si ind_62
				} // ind_62 >=0
			} // Pour ind_62
		} // Pour ind_61
	}
	
	
	static public function normalize($word) {
		$r = Sys::getOneData('CEN', 'Regle/Code=GDN');
			
		$word = ' '.strtolower(trim($word)).' ';
		
		$os = array();
		$file = fopen(getcwd().'/'.$r->FilePath, 'r');
		if(!$file) return $word;
		while(! feof($file)) {
			$o = explode("\t", utf8_encode(fgets($file)));
			if(count($o) < 4) continue;
			$os[] = $o;
		}
		fclose($file);
		usort($os, 'sortOrtho');

		$ch = $word;
		foreach($os as $o) {
			$ch1 = $o[0];
			$ch2 = $o[1];
			switch($o[2]) {
				case 'début.': $ch1 = ' '.$ch1; $ch2 = ' '.$ch2; break;
				case 'fin.': $ch1 = $ch1.' '; $ch2 = $ch2.' '; break;
			}
			$numb = self::replOrtho($ch, $ch1, $ch2);
			$new = trim($ch);
		}
		return $new;
	}


}
