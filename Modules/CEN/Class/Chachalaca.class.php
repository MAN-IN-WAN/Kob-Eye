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
	
	static function Suff($args) {
		$word = $args['word'];
		
		$m_premier = $word;
		
		// 38_1
		$m_premier = self::redoublement($m_premier);
		// 2_1 + 3_2 
		$t61 = array();
		$t62 = array();
		self::suffixes($m_premier, $t61, $t62);
//klog::l(">>>>>>>>>>2_1 61",$t61);
//klog::l(">>>>>>>>>>2_1 62",$t62);
		// 3_2
		$t61 = array();
		foreach($t62 as $k => &$l) {
			$t61[] = [$l[0], $l[1], '', '', ''];
		}
//klog::l(">>>>>>>>>>3_2 61 ns",$t61);
		usort($t61, 'self::sort61c64');
//klog::l(">>>>>>>>>>3_2 61 st",$t61);
		// 4_3
		$t60 = array();
		self::generation($m_premier, $t61, $t60);
//klog::l(">>>>>>>>>>4_3 60",$t60);
		// 6_3
		self::categorisation($t60);
//klog::l(">>>>>>>>>>6_3 60",$t60);
//		// 6_4
		self::nettoyageSuff($t60);
//klog::l(">>>>>>>>>>6-4 60",$t60);
		
		return array('suf'=>$t60);
	}
	
	// 38_1
	static private function redoublement($m_premier) {
		return $m_premier;
	}
	
	static private function sort61c64($a, $b) {
		return strcmp($a[0], $b[0]);
	}
	static private function sort60c63($a, $b) {
		return strcmp($a[2], $b[2]);
	}
	
	// 6_4
	static private function nettoyageSuff(&$t60) {
		foreach($t60 as $k => $l) if(strpos($l[2], 'effacer') !== false) unset($t60[$k]);
		
		$r = Sys::getOneData('CEN', 'REGLE/Code=CHACHALACA&Regle=SUF_IMPOSSIBLE');
		$suf_imp = explode("\r\n", file_get_contents($f->FilePath));
		
		foreach($suf_imp as $si) {
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
		$n = 0;
		foreach($t60 as $l) if($l[0] == '***') $n++;
		foreach($t60 as &$l) {
			foreach($suf_imp as $si) {
				$cnd = explode("\t", $si);
				foreach($cnd as &$c) $c = trim($c);
				
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
		$n = 0;
		foreach($t60 as $l) if($l[0] == '***') $n++;
		
		usort($t60, 'self::sort60c61');
		foreach($t60 as $k => &$l) {
			if(substr($l[0], 0, 1) == '-' || strpos($l[0], '***') !== false) unset($t60[$k]);
			else $l[1] = explode('-', $l[0])[0];
			klog::l("t60+++  ".$l[0].",  ".$l[2].",  ".$l[3]);
		}	
	}
	
	
	// 6_3
	static private function categorisation(&$t60) {
		$sql = "select Categorie from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);	

		foreach($t60 as &$l) {
			$debut = 0;
			$cpteur = 0;
			$m_analyse = trim($l[0]);
			klog::l("t60+  $m_analyse");
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
				$numcol++;
				$ch = $t_analyse[$numcol];
				$nb_pref = 0;				
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
					else $tout_cat .= ' - ?';
				}
				else $tout_cat = 'effacer';				
			}
			$l[2] = $tout_cat;
		}
		foreach($t60 as &$l) {
			klog::l("t60++  ".$l[0].",  ".$l[2]);
		}
	}
	
	// 4_3
	static private function generation($m_premier, &$t61, &$t60) {
		foreach($t61 as $l) {
klog::l("t61  ".$l[0].', '.$l[1]);

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
klog::l("t60  ".$l[0]);
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
		if($p !== false) $ch = substr($ch, 0, $pos).$chremp.substr($ch, $pos+strlen($chini));
		return $pos;
	}
	
	// 2_1
	static private function suffixes($m_premier, &$t61, &$t62) {
		$sql = "select Decompo from `##_CEN-Suffixe` where Suffixe=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql);	

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

					//HLitRecherche("suffixes","suffixe",Complète(m_coupe,20))
					$pdo->execute(array(':fix'=>$m_coupe));
					if($pdo->rowCount()) {
						//$pdo->fetch(PDO::FETCH_ASSOC);
						//TableAjoute("table62","-"+m_coupe+ TAB + m_coupe + TAB + m_avant + TAB + m_avant+ m_coupe + TAB + m_categorie)
						$t61[] = ['-'.$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie];
						if(!empty($m_avant.$m_coupe)) $t62["-$m_coupe\t$m_coupe\t$m_avant.$m_coupe"] = ['-'.$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie];
klog::l("-$m_coupe, $m_coupe, $m_avant, $m_avant.$m_coupe, $m_categorie");
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
	
	static private function fixes($word, $pre) {
		$sql = "select Decompo from `##_CEN-Chachalaca` where ChachalacaId=:id and ".($pre ? 'prefixe' : 'Suffixe')."=:fix";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$smt = $GLOBALS['Systeme']->Db[0]->prepare($sql);
		
		$fix = array();
		if($pre) {
			for($l = strlen($word); $l; ) {
				$s = substr($word, --$l);
				$pdo->execute(array(':fix'=>$s));
				if($pdo->rowCount()) {
					$pdo->fetch(PDO::FETCH_ASSOC);
					$fix[$s] = $dpo['Decompo'];
				}
			}
			for($l = strlen($word), $i = 0; $i < $l; ) {
				$s = substr($word, $l++);
				$pdo->execute(array(':fix'=>$s));
				if($pdo->rowCount()) {
					$pdo->fetch(PDO::FETCH_ASSOC);
					$fix[$s] = $dpo['Decompo'];
				}
			}
		}
		return $fix;
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
