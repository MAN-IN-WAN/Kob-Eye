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

	static private $suf_imp;
	static private $pre_imp;
	static private $pre_suf;
	static private $dicIds;
	static private $premier;
	
	static private function getRules($rule) {
		$t = array();
		$r = Sys::getOneData('CEN', 'Regle/Code=CHACHALACA&Regle='.$rule);
		$ls = explode("\r\n", utf8_encode(file_get_contents(getcwd().'/'.$r->FilePath)));
		foreach($ls as &$l) {
			$cnd = explode("\t", $l);
			foreach($cnd as &$c) $c = trim($c);
			$t[] = $cnd;
		}
		return $t;
	}

	static function Suff($args) {
		$word = $args['word'];

		self::$suf_imp = self::getRules('SUF_IMPOSSIBLE');
		self::$pre_imp = self::getRules('PREF_IMPOSSIBLE');
		self::$pre_suf = self::getRules('PREF_SUF_IMPOSSIBLE');
		self::$dicIds = '3';
		self::$premier = $word;
		
		// 38_1  affiche le bouton
		//$m_premier = self::redoublement($m_premier);
		// 2_1 + 3_2 
		$t62 = array();
		self::suffixes2_1($t62);
		usort($t62, 'self::sortC0C1');
		// 3_2
		$t61 = array();
		foreach($t62 as $k=> &$l) {
			$t61[] = [$l[0], $l[1], '', '', ''];
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
		self::conditionsSuf6_4($t60, true);
		$t61 = array();
		$t62 = array();
		// 4_4 + 5_5
		$t3 = array();
		$t3[] = self::$premier;
		$t7 = array();
		self::analyse4_4($t60, $t7);
		// 7_3
		$t1 = array();
		foreach($t7 as $l) $t1[] = [$l[1].$l[2], $l[4], ''];
		$t1[] = [self::$premier, '', ''];
		foreach($t1 as $l) klog::l("7_3 t1  ".$l[0]."  ".$l[1]);
		// 6_4 (3_3)
		self::conditionsSuf6_4($t1, false);
		$t1[] = [self::$premier, '', ''];
		
		

		// PREFIXES ---------------------
		klog::l("PRFX----------");

		// 2_6
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
		self::conditionPref6_2($t62, true);
		// 7_1
		self::conditionPref7_1($t60, true);
		// 4_2 + 5_3
		$t3 = array();
		$t7 = array();
		self::analyse4_2($t62, $t60, $t7);
		$t3[] = self::$premier;
		// 7_2
		self::conditionPref7_1($t7, false);
		// 6_5
		self::conditionPref6_2($t7, false);
		// 7_5
		self::traitement7_5($t1, $t7, $t61);
		// 7_2
		self::conditionPref7_1($t7, false);

		// RACINES ---------------------
		klog::l("RADX----------");
		
		$t1_1 = array();
		$t2_1 = array();
		$t3_1 = array();
		$t4_1 = array();
		$t5 = array();
		$t6 = array();
		self::racines8($t7, $t1_1, $t2_1, $t3_1, $t4_1, $t5, $t6);


		return array('suf'=>$t60);
	}
	
	static private function racines8(&$t7, &$t1_1, &$t2_1, &$t3_1, &$t4_1, &$t5, &$t6) {
		$nb_entree = 0;
		$sep_cat = ' + ';
		$ids = self::$dicIds;
		$sql = "select Racines,Categorie from `##_CEN-Radix` where ChachalacaId=$ids and Racines=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

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
						$t1_1[] = [$r['Racines'], $r['Categorie']];
//klog::l(">>>1  $a_chercher  ".$r['Racines']."  ".$r['Categorie']);
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
						$t2_1[] = [$r['Racines'], $r['Categorie'], ''];
//klog::l(">>>2  $a_chercher  ".$r['Racines']."  ".$r['Categorie']);
					}
				}
			}
			$long = $longeur-1;
			$debut = 0;
			while($debut < $long) {
				$debut++;
				$reste = $longeur; //-$debut;
				while($reste--) {
					$a_chercher = substr($mot, $debut, $reste);
					$pdo->execute(array(':fix'=>$a_chercher));
//klog::l("+++$debut  $reste  $a_chercher  ".$pdo->rowCount());
					if($pdo->rowCount()) {
						$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
						foreach($rs as $r) {
							$t3_1[] = [$r['Racines'], $r['Categorie'], ''];
//klog::l(">>>3  $a_chercher  ".$r['Racines']."  ".$r['Categorie']);
						}
					}
				}
			}
			$t3_1[] = ['xxx', 'xxx', ''];
			
//klog::l(count($t1_1)."  ".count($t2_1)."  ".count($t3_1));
			usort($t1_1, 'self::sortC0C1');
			$o = null;
			foreach($t1_1 as $k=>&$l11) {
				if($o && $l11[0] == $o[0] && $l11[1] == $o[1]) unset($t1_1[$k]);
				else $o = $l11;
			}
			$t1_1 = array_values($t1_1);
			usort($t2_1, 'self::sortC0C1');
			$o = null;
			foreach($t2_1 as $k=>&$l11) {
				if($o && $l11[0] == $o[0] && $l11[1] == $o[1]) unset($t2_1[$k]);
				else $o = $l11;
			}
			$t2_1 = array_values($t2_1);
			usort($t3_1, 'self::sortC0C1');
			$o = null;
			foreach($t3_1 as $k=>&$l11) {
				if($o && $l11[0] == $o[0] && $l11[1] == $o[1]) unset($t3_1[$k]);
				else $o = $l11;
			}
			$t3_1 = array_values($t3_1);
klog::l(count($t1_1)."  ".count($t2_1)."  ".count($t3_1));


			$c1 = count($t1_1);
			$c2 = count($t2_1);
			$c3 = count($t3_1);
			for($i1 = 0; $i1 < $c1;) {
				$l11 = $t1_1[$i1++];
				$racine1 = $l11[0];
				$cat_1 = $l11[1];
				for($i2 = 0; $i2 < $c2;) {
					$l21 = $t2_1[$i2++];
					$racine2 = $l21[0];
					$cat_2 = $l21[1];
					for($i3 = 0; $i3 < $c3;) {
						$l31 = $t3_1[$i3++];
						$racine3 = $l31[0];
						$cat_3 = $l31[1];
						
						if($racine1 == $mot) {
							$decomposition = $racine1;
							$m_categorie = $cat_1;
							//$racine_1 = $racine1;
							$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
							$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;
							
							$v0 = $l7[3].$sepa_cat1.$m_categorie.$sepa_cat2.$l7[4];
							$v1 = $l7[0].$sepa_cat1.$decomposition;
							$resultat = self::arraySearch($t5, 2, $v0);
							if($resultat === false || strpos($t5[$resultat][0], $v1) === false) {
								$nb_entree++;
								$t5[] = [$v1.$sepa_cat2.$l7[2], "$i1 $i2 $i3", $v0, '', ''];
							}
						}
						
						if(strlen($racine2)) {
							if($racine1.$racine2 == $mot) {
								$decomposition = $racine1.'-'.$racine2;
								$m_categorie = $cat_1.'-'.$cat_2;
								//$racine_1 = $racine1;
								//$racine_2 = $racine2;
								$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
								$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;
								
								$v0 = $l7[3].$sepa_cat1.$m_categorie.$sepa_cat2.$l7[4];
								$v1 = $l7[0].$sepa_cat1.$decomposition;
								$resultat = self::arraySearch($t5, 2, $v0);
								if($resultat === false || strpos($t5[$resultat][0], $v1) === false) {
									$nb_entree++;
									$t5[] = [$v1.$sepa_cat2.$l7[2], "$i1 $i2 $i3", $v0, '', ''];
								}
							}
						}

						if($racine1.$racine3.$racine2 == $mot) {
							$decomposition = $racine1;
							$m_categorie = $cat_1;
							//$racine_1 = $racine1;
							//$racine_2 = $racine2;
							//$racine_3 = $racine3;
							$sepa_cat1 = empty($l7[0]) ? '' : $sep_cat;
							$sepa_cat2 = empty($l7[2]) ? '' : $sep_cat;
							
							$v0 = $l7[3].$sepa_cat1.$m_categorie.$sepa_cat2.$l7[4];
							$v1 = $l7[0].$sepa_cat1.$decomposition;
							$resultat = self::arraySearch($t5, 2, $v0);
							if($resultat === false || strpos($t5[$resultat][0], $v1) === false) {
								$nb_entree++;
								$t5[] = [$v1.$sepa_cat2.$l7[2], "$i1 $i2 $i3", $v0, '', ''];
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
			if($ensemble == self::$premier) $t5[] = [$l[0].$l[2], '', '', $l[3].$l[4], ''];
		}
		
		if(!count($t5)) {
			$t5[] = [$m_entree, '', '', '', ''];
			$t6[] = [$m_categorie, ''];
		}
		
		foreach($t5 as &$l) klog::l("8 t5  $l[0],  $l[1],  $l[2]");
	}
	
	static private function arraySearch(&$arr, $col, $val) {
		foreach($arr as $k=>&$v) {
			if($v[$col] == $val) return $k;
		}
		return false;
	}
	
	
	// PREFIXES ---------------------
	
	// 7_5
	static private function traitement7_5(&$t1, &$t7, &$t61) {
		$t61 = array();
		foreach($t7 as $l) $t61[] = [$l[0], $l[1], $l[2], $l[3], $l[4]];
		
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
				$ch = str_replace('-', '', $suf);
				$suf_sans = $ch;
				$taille_suf_sans = strlen($suf_sans);
				$taille_suf = strlen($suf);
				if(!$taille_pref) $t7[] = [$pref, $suf_reste, $suf, $pref_cat, $suf_cat];
				elseif($taille_suf) {
					if($taille_pref_reste >= $taille_suf_sans) {
						$reste_def = $pref_reste.' ';
						$reste_def = substr($reste_def, 0, strpos($reste_def, $suf_sans.' '));
						$t7[] = [$pref, $reste_def, $suf, $pref_cat, $suf_cat];
					}
					else {
						$t7[] = [$pref, $pref_reste, $suf, $pref_cat, $suf_cat];
						$t7[] = [$pref, $suf_reste, $suf, $pref_cat, $suf_cat];
					}
				}
			}
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
			$t7[] = ['', $reste_def, $suf, '', $suf_cat];
		}

		foreach($t7 as &$l) {
			klog::l("7_5 t7  $l[0], $l[1], $l[2], $l[3], $l[4]");
		}
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
				$t7[] = [$afx, $racines, $suffixes, $cat, $l[3]];
				$compteur++;
				klog::l("5_3 t7  $afx,  $racines,  $suffixes,  $cat,  $l[3]");
			}
		}
	}
	
	// 4_4
	static private function analyse4_2(&$t62, &$t60, &$t7) {
		$sql = "select Categorie,Decompo from `##_CEN-Prefixe` where Prefixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		$t60 = array();
		foreach($t62 as &$l) $t60[] = [$l[0], $l[1], '', ''];
		
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
				$ch = $t_analyse[$numcol];
				$numcol++;
				$nb_pref = 0;
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					foreach($rs as $r) {
						$cat_1 = $r['Categorie'];
						$m_prefixe1 = $r['Decompo'];
						$nb_pref++;
						$t5[$nb_tour][] = [$m_prefixe1, $m_reste, $cat_1.'-', $cat_suf];
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


	
	static private function conditionPref7_1(&$t60, $mode) {
		$col = $mode ? 2 : 3;

		foreach(self::$pre_suf as $cnd) {
			foreach($t60 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[$col], $cnd[0]);
					if($resultat1 !== false) $l[0] = '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[$col], $cnd[0]);
					$resultat2 = strpos($l[$col+1], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] = '***';
				}
				else if(empty($cnd[1]) && !empty($cnd[2])) {
					$resultat1 = strpos($l[$col], $cnd[0]);
					$resultat2 = strpos($l[$col+1], $cnd[2]);
					if($resultat1 !== false && $resultat2 === false) $l[0] = '***';
				}
			}
		}

		foreach($t60 as $k=> &$l) if($l[0] == '***') unset($t60[$k]);

		usort($t60, 'self::sortC0C2C3C4');
		foreach($t60 as $k=>&$l)
			klog::l("7_1 t60  $k: ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
	}

	// 6_2
	static private function conditionPref6_2(&$t62, $mode) {
		foreach(self::$pre_imp as $cnd) {
			foreach($t62 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[3], $cnd[0]);
					if($resultat1 !== false) $l[0] = '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[3], $cnd[0]);
					$resultat2 = strpos($l[3], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] = '***';
				}
			}
		}

		foreach($t62 as $k=> &$l) if($l[0] == '***') unset($t62[$k]);

		if($mode) usort($t62, 'self::sortC3C4');
		else {
			usort($t62, 'self::sortC0C2C3C4');
			$o = null;
			foreach($t62 as $k=>&$l) {
				if($o && $l[0] == $o[0] && $l[2] == $o[2] && $l[3] == $o[3] && $l[4] == $o[4]) unset($t62[$k]);
				else $o = $l;
			}
		}
		foreach($t62 as &$l)
			klog::l("6_2 t62  ".$l[0].",  ".$l[1].",  ".$l[2].",  ".$l[3]);
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
		foreach($t62 as &$l) {
			klog::l("5_7 t62  $l[0], $l[1], $l[2]");
		}
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
		foreach($t61 as &$l) {
			klog::l("5_2 t61  $l[0], $l[1], $l[2]");
		}
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
					$t61[] = [$ch, $m_reste, $s, '', ''];
					klog::l("4_1 t61  $ch, $m_reste, $s");
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
					$t62[] = [$ch, $m_reste, $s, '', '', ''];
					klog::l("4_1 t62  $ch, $m_reste, $s");
				}
			}
		}
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
			$t60[] = [$l[0], $l[1], $l[2], ''];
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
			$longueur = $long_entree - $ind_61;
			for($ind_62 = 0; $ind_62 < 6; $ind_62++) {
				$trouve = 0;
				$long_coupe = $ind_62;
				$point_coupe = 0;
				$m_coupe = substr($m_entree, $point_coupe, $long_coupe);
				$m_reste = substr($m_entree, $point_coupe + $long_coupe, $longueur);
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
				$t7[] = ['', $racines, $afx, '', $cat];
				$compteur++;
				klog::l("5_5 t7  $racines,  $afx, $cat");
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
						$t5[$nb_tour][] = [$m_prefixe1, $m_reste, '-'.$cat_1, $cat_suf];
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
	static private function redoublement($m_premier) {
		return $m_premier;
	}

//	static private function sort62c68($a, $b) {
//		return strcmp($a[0], $b[0]);
//	}
	
	static private function sortC0($a, $b) {
		return strcmp($a[0], $b[0]);
	}
	static private function sortC2($a, $b) {
		return strcmp($a[2], $b[2]);
	}
	static private function sortC0C1($a, $b) {
		$c = strcmp($a[0], $b[0]);
		return $c ? $c : strcmp($a[1], $b[1]);
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
		foreach($t62 as &$l) $t60[] = [$l[0], '', '', ''];
		
		usort($t60, 'self::sortC0C1');
foreach($t60 as &$l) klog::l("5_1 t60  ".$l[0]);
	}

	// 6_4
	static private function conditionsSuf6_4(&$t60, $mode) {
		$col = $mode ? 2 : 1;

		foreach(self::$suf_imp as $cnd) {
			foreach($t60 as &$l) {
				if(empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[$col], $cnd[0]);
					if($resultat1 !== false) $l[0] = '***';
				}
				else if(!empty($cnd[1]) && empty($cnd[2])) {
					$resultat1 = strpos($l[$col], $cnd[0]);
					$resultat2 = strpos($l[$col], $cnd[1]);
					if($resultat1 !== false && $resultat2 !== false) $l[0] = '***';
				}
			}
		}


		if($mode) {
			foreach($t60 as $k=> &$l) {
				if($l[0] == '***' || substr($l[0], 0, 1) == '-') unset($t60[$k]);
				else $l[1] = explode('-', $l[0])[0];
			}
		}
		else {
			foreach($t60 as $k=> &$l) if($l[0] == '***' || empty($l[1])) unset($t60[$k]);

			usort($t60, 'self::sortC0C1');
			$o = null;
			foreach($t60 as $k=>&$l) {
				if($o && $l[0] == $o[0] && $l[1] == $o[1]) unset($t60[$k]);
				else $o = $l;
			}
		}


		usort($t60, 'self::sortC2');
		foreach($t60 as &$l)
			klog::l("6_4-3_3 t60  ".$l[0].",  ".$l[1].",  ".$l[2]);

	}
	
	// 6_1
	static private function categorisation6_1(&$t62) {
		$sql = "select Categorie from `##_CEN-Prefixe` where Prefixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);

		usort($t62, 'self::sortC0C1');
		
		foreach($t62 as &$l) {
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
				$ch = $t_analyse[$numcol ];
				$numcol++;
				$nb_pref = 0;
//klog::l('6_3>>>'.$ch.':');
				$pdo->execute(array(':fix'=>$ch));
				if($pdo->rowCount()) {
					$rs = $pdo->fetchAll(PDO::FETCH_ASSOC);
					$m_categorie = '';
					foreach($rs as $r) {
						if(empty($m_categorie)) $m_categorie = trim($r['Categorie']);
						$nb_pref++;
					}
					if($nb_pref == 1) {
						if($nb_tour == 1) $tout_cat .= $m_categorie;
						else $tout_cat .= '-'.$m_categorie;
					} else $tout_cat .= '- ? ';
				} else $tout_cat = 'effacer';
			}
			$l[3] = $tout_cat;
//klog::l('6_3>>>'.$tout_cat);
		}
		foreach($t62 as $k => &$l) {
			if(strpos($l[3], 'effacer') !== false) unset($t60[$k]);
		}

		foreach($t62 as &$l) {
klog::l("6_1 t62  ".$l[0].",  ".$l[3]);
		}
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
		foreach($t60 as $k => &$l) {
			if(strpos($l[2], 'effacer') !== false) unset($t60[$k]);
		}

		foreach($t60 as &$l) {
klog::l("6_3 t60  ".$l[0].",  ".$l[2]);
		}
	}

	// 4_5
	static private function generation4_5(&$t60, &$t61, &$t62) {
		foreach($t60 as &$l0) {
			$mot_liste = $l0[0];
			$t62[] = [$mot_liste, '', '', '', '', ''];

			foreach($t61 as &$l1) {
				$m_suffixe = $l1[1];
				$debut = $cpteur = 0;
				$pos = [0, 0, 0, 0, 0];
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
			$pos = [0, 0, 0, 0, 0];
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
				$t60[] = [$ch, '', '', '', '', ''];
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
								$t62[] = ['-'.$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie, ''];
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
		$lang = ['.esp','.fra','.ang'][$lix];
		if(file_exists(getcwd().$dir.$text.$lang)) return $lang;
		if(file_exists(getcwd().$dir.$text.'.esp')) return '.esp';
		return false;
	}


	static public function GetGrammar($args) {
		$dir = '/Home/2/CEN/Chachalaca/grammaire/';
		
		switch($args['type']) {
			case 'grammar':
				$text = $args['text'];
				if(empty($text)) $text = 'liste'.self::CheckGrammar('liste', $args['lang']);
				$txt = file_get_contents(getcwd().$dir.$text);
				$txt = utf8_encode(nl2br($txt));
				return array('text'=>$txt);	
				
			case 'dict':
				break;
		}
		
	}
}
