<?php

//------- Teste_02
if($col15 != "***") {
	if(strpos($t70[$ii][4], "début i >") !== false && $t70[$ii][0] == "1" && strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii][4], "généré bases") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_03
if($col15 != "***") {
	if(strpos($t70[$ii][4], "début i >") !== false && ($t70[$ii - 1][2] != "préf. obj. non_hum. indéf." && $t70[$ii - 1][2] != "préf. indéf. non_hu." && $t70[$ii - 1][2] != "préf. réfl. indéf." && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "i" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "o" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "h")) {
		if(strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii][4], "généré bases") === false) {
			$col15 = "***";
			$test_fait = 1;
		}
	}
}
//------- Teste_04
if($col15 != "***") {
	if(strpos($t70[$ii][4], "début tl >") !== false && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_05
if($col15 != "***") {
	if(($t70[$ii][3] == "m" || $t70[$ii][3] == "n" || $t70[$ii][3] == "c" || $t70[$ii][3] == "t" || $t70[$ii][3] == "l" || $t70[$ii][3] == "p" || $t70[$ii][3] == "qu" || $t70[$ii][3] == "ch" || $t70[$ii][3] == "lp" || $t70[$ii][3] == "z" || $t70[$ii][3] == "tz" || $t70[$ii][3] == "x" || $t70[$ii][3] == "tqu") && ($t70[$ii][2] == "r.n." || $t70[$ii][2] == "r.v." || $t70[$ii][2] == "adv." || $t70[$ii][2] == "adj." || $t70[$ii][2] == "num.")) {
		$l5[5] = "ivertclair";
		$col15 = "*co";
		$test_fait = 1;
	}
}
//------- Teste_06
if($col15 != "***") {
	if(($t70[$ii][3] == "a" || $t70[$ii][3] == "o" || $t70[$ii][3] == "i" || $t70[$ii][3] == "e") && ($t70[$ii][2] == "r.n." || $t70[$ii][2] == "r.v." || $t70[$ii][2] == "adv." || $t70[$ii][2] == "adj." || $t70[$ii][2] == "num.")) {
		$l5[5] = "ivertclair";
		$col15 = "*vo";
		$test_fait = 1;
	}
}
//------- Teste_07
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin iz > ic") !== false && strlen(trim($t70[$ii][4])) == strlen("fin iz > ic") && ($t70[$ii + 1][3] != "e" || $t70[$ii + 1][3] != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_08
if($col15 != "***") {
	if($t70[$ii][2] == "préf. direc. d'éloign." && $t70[$ii][3] == "om" && (substr($t70[$ii + 1][3], 0, 1) != "p" && substr($t70[$ii + 1][3], 0, 1) != "m")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_09
if($col15 != "***") {
	if(($t70[$ii][2] == "préf. réfl. 1 sing." || $t70[$ii][2] == "préf. réfl. 1 plur.") && $t70[$ii][0] == "1") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_10
if($col15 != "***") {
	if($nb_pref_pos > 0 && $t70[($len70 - 1 - 1)][2] == "r.v.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_11
if($col15 != "***") {
	if($nb_pref_pos > 0 && $nb_suf_possed > 0 && $nb_suf_particip == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_12
if($col15 != "***") {
	if(($t70[$ii][2] == "suf. possed. (e)" || $t70[$ii][2] == "suf. possed. (hua)") && ($t70[$ii + 1][2] != "suf. plur. (que)" && $t70[$ii + 1][2] != "suf. particip. (ca)" && $t70[$ii + 1][2] != "suf. loc. (can)") && $ii < ($len70 - 1 - 1) && $t70[$ii + 1][2] != "suf. gent. (ca)") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_13
if($col15 != "***") {
	if($t70[$ii][0] == "1" && $t70[$ii][2] == "liga.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_14
if($col15 != "***") {
	if($ii == ($len70 - 1 - 1) && ($t70[$ii][2] == "lig. (ti)" || $t70[$ii][2] == "liga.")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_15
if($col15 != "***") {
	if($t70[$ii][3] == "n(i)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_16
if($col15 != "***") {
	if($t70[$ii][3] == "ni" && $t70[$ii + 1][4] == "début i >") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_17
if($col15 != "***") {
	if($t70[$ii][3] == "t(i)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_18
if($col15 != "***") {
	if($t70[$ii][3] == "n(o)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_19
if($col15 != "***") {
	if($t70[$ii][3] == "t(o)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_20
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr. (lo)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_21
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr. (zo)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "z") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_22
if($col15 != "***") {
	if($t70[$ii][3] == "ti" && strpos($t70[$ii + 1][4], "début i >") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_23
if($col15 != "***") {
	if(($t70[$ii][3] == "tla" || $t70[$ii][3] == "la") && $t70[$ii][2] == "r.n." && $col15 != "***") {
		$col15 = "*tla";
		$l5[5] = "irougeclair";
		$test_fait = 1;
	}
}
//------- Teste_24
if($col15 != "***") {
	if($t70[$ii][3] == "te" && $t70[$ii][2] == "r.n." && $col15 != "***") {
		$col15 = "*te";
		$l5[5] = "irougeclair";
		$test_fait = 1;
	}
}
//------- Teste_25
if($col15 != "***") {
	if($t70[$ii][2] == "préf. possessif" && ($ii == ($len70 - 1 - 1) || $t70[$ii][0] == 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_26
if($col15 != "***") {
	if($t70[$ii][5] == "3" && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_96_
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.i.") !== false && $t70[$ii][2] == "r.v." && $nb_passif == 0 && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		switch($nb_causatif_applicatif) {
			case 0:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.i. ok";
						break;
					case 1:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.i.ok modif.";
						} else {
							$col15 = $col15 + " v.i. non+";
							$l5[5] = "ibleuclair";
						}
						break;
					default:
						$col15 = $col15 + " v.i. non+";
						$l5[5] = "ibleuclair";
				}
				break;
			case 1:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.i. > v.t. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.i. > v.t. ok satur.";
						} else {
							$col15 = $col15 + " v.i. > v.t. ok";
						}
						break;
					default:
						$col15 = $col15 + " v.i. > v.t. non+";
						$l5[5] = "ibleuclair";
				}
				break;
			case 2:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.i. > bitrans. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_objet_def > 0) {
							$col15 = $col15 + " v.i. > bitrans. ok + réduc.";
						} else {
							$col15 = $col15 + " v.i. > bitrans. non-";
							$l5[5] = "ibleuclair";
						}
						break;
					case 2:
						if($nb_r_nominale == 0) {
							$col15 = $col15 + " v.i. > bitrans. ok";
						} else {
							if($nb_objet_def == 1 && $nb_r_nominale == 1) {
								$col15 = $col15 + " v.i. > bitrans. ok + modif. + réduc.";
							} else {
								$col15 = $col15 + " v.i. > bitrans. ok + satur.";
							}
						}
						break;
					default:
						$col15 = $col15 + " v.i. > bitrans. ok";
				}
				break;
		}
	}
}
//------- Teste_97_
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.t.") !== false && $t70[$ii][2] == "r.v." && $nb_passif == 0 && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		switch($nb_causatif_applicatif) {
			case 0:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.t. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.t. ok satur.";
						} else {
							$col15 = $col15 + " v.t. ok";
						}
						break;
					case 2:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.t. ok modif.";
						} else {
							$col15 = $col15 + " v.t. non+";
							$l5[5] = "ibleuclair";
						}
						break;
					default:
						$col15 = $col15 + " v.t. non+";
						$l5[5] = "ibleuclair";
				}
				break;
			case 1:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.t. > v.bi. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_objet_def > 0) {
							$col15 = $col15 + " v.t. > bitrans. ok + réduc.";
						} else {
							$col15 = $col15 + " v.t. > bitrans. non-";
							$l5[5] = "ibleuclair";
						}
						break;
					case 2:
						if($nb_r_nominale == 0) {
							$col15 = $col15 + " v.t. > bitrans. ok";
						} else {
							if($nb_objet_def == 1 && $nb_r_nominale == 1) {
								$col15 = $col15 + " v.t. > bitrans. ok + modif. + réduc.";
							} else {
								$col15 = $col15 + " v.t. > bitrans. ok + satur.";
							}
						}
						break;
					default:
						$col15 = $col15 + " v.t. > bitrans. ok";
				}
				break;
			case 2:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.t. > bitrans. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_objet_def > 0) {
							$col15 = $col15 + " v.t. > bitrans. ok + réduc.";
						} else {
							$col15 = $col15 + " v.t. > bitrans. non-";
							$l5[5] = "ibleuclair";
						}
						break;
					case 2:
						if($nb_r_nominale == 0) {
							$col15 = $col15 + " v.t. > bitrans. ok";
						} else {
							if($nb_objet_def == 1 && $nb_r_nominale == 1) {
								$col15 = $col15 + " v.t. > bitrans. ok + modif. + réduc.";
							} else {
								$col15 = $col15 + " v.t. > bitrans. ok + satur.";
							}
						}
						break;
					default:
						$col15 = $col15 + " v.t. > bitrans.";
				}
				break;
		}
	}
}
//------- Teste_98_
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.bi.") !== false && $t70[$ii][2] == "r.v." && $nb_passif == 0 && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		switch($nb_tot_objet) {
			case 0:
				$col15 = $col15 + " v.bi. non-";
				$l5[5] = "ibleuclair";
				break;
			case 1:
				if($nb_objet_def > 0) {
					$col15 = $col15 + " v.bi. ok + réduc.";
				} else {
					$col15 = $col15 + " v.bi. non-";
					$l5[5] = "ibleuclair";
				}
				break;
			case 2:
				if($nb_r_nominale == 0) {
					$col15 = $col15 + " v.bi. ok";
				} else {
					if($nb_objet_def == 1 && $nb_r_nominale == 1) {
						$col15 = $col15 + " v.bi. ok + modif. + réduc.";
					} else {
						$col15 = $col15 + " v.bi. ok + satur.";
					}
				}
				break;
			default:
				$col15 = $col15 + " v.bi. ok";
		}
	}
}
//------- Teste_32
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin ahui > am") !== false && substr($t70[$ii + 1][3], 0, 1) != "m") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_33
if($col15 != "***") {
	if(($t70[$ii][2] == "préf. réfléchi" || $t70[$ii][2] == "préf. possessif") && $t70[$ii][0] == "1") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_34
if($col15 != "***") {
	if($t70[$ii][3] == "m(o)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_35
if($col15 != "***") {
	if($t70[$ii][2] == "préf. possessif" && (substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "tl" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "li")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_36
if($col15 != "***") {
	if($t70[$ii][2] == "suf. loc. (can)" && $nb_objet == 0 && $nb_r_nominale == 0 && strpos($t70[$ii - 1][0], "v.i.") === false && strpos($t70[$ii - 1][2], "adj.") === false && strpos($t70[$ii - 1][2], "adv.") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_37
if($col15 != "***") {
	if($t70[$ii][2] == "préf. indéf. non_hu." && $t70[$ii][3] == "la" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_38
if($col15 != "***") {
	if($t70[$ii][2] == "suf. loc. ab. (la)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_39
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin omi > on") !== false && strlen(trim($t70[$ii][4])) == strlen("généré m-ph. fin omi > on") && (substr($t70[$ii + 1][3], 0, 1) == "a" || substr($t70[$ii + 1][3], 0, 1) == "e" || substr($t70[$ii + 1][3], 0, 1) == "i" || substr($t70[$ii + 1][3], 0, 1) == "o" || substr($t70[$ii + 1][3], 0, 1) == "u")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_40
if($col15 != "***") {
	if($ii == ($len70 - 1 - 1) && ($t70[$ii][2] == "participial")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_41
if($col15 != "***") {
	if($nb_pref_pos > 0 && $nb_r_nominale == 0 && $nb_suf_particip == 0 && $nb_suf_nomina == 0 && strpos("-pan-cpac-tlan-tech-ca-nahuac-tzalan-nepantla-pal-pampa-tloc-huan-huic-icampa-nehuan-cel-huic-copa-co-pa-tzin", $t70[2][3]) === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_42
if($col15 != "***") {
	if($t70[$ii][0] == "1" && trim($t70[$ii][2]) == "préf. 3 plur. (im)") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_43
if($col15 != "***") {
	if($t70[$ii][2] == "négation" && $t70[$ii + 1][4] = "début i >") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_44
if($col15 != "***") {
	if($ii == ($len70 - 1 - 1) && ($t70[$ii][2] == "préf. indéf. non_hu." || $t70[$ii][2] == "préf. indéf. ani.")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_45
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin -atl") !== false && ($nb_pref_pos == 0 && $nb_suf_possed == 0) && strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii + 1][2], "suf. nom. verbali") === false && strpos($t70[$ii + 1][2], "r.n.") === false && strpos($t70[$ii + 1][2], "r.v.") === false && strpos($t70[$ii - 1][2], "préf. obj. hum. indéf.") === false && $nb_hum == 0 && strpos($t70[$ii + 1][2], "suf. abs.") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_46
if($col15 != "***") {
	if($t70[$ii][0] == 1 && ($t70[$ii][2] == "participial" || $t70[$ii][2] == "liga." || $t70[$ii][2] == "suf. abstr.")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_48
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin auh > ap") !== false && substr($t70[$ii + 1][3], 0, 1) != "p") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_49
if($col15 != "***") {
	if($t70[$ii][2] == "liga." && $t70[$ii][3] == "t" && $t70[$ii + 1][3] == "i") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_50
if($col15 != "***") {
	if($t70[$ii][2] == "liga." && strpos($t70[$ii + 1][0], "aux.") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_51
if($col15 != "***") {
	if(strpos($t70[$ii][4], "az > ac") !== false && $t70[$ii + 1][3] != "i" && $t70[$ii + 1][3] != "e") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_52
if($col15 != "***") {
	if(strpos($t70[$ii][4], "az > ac") !== false && $t70[$ii + 1][3] != "i" && $t70[$ii + 1][3] != "e") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_53
if($col15 != "***") {
	if(strpos($t70[$ii][0], "aux.") !== false && $t70[$ii][3] == "uh" && $t70[$ii - 1][2] != "liga.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_54
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "tzo" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "tz") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_55
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "zo" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "z") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_56
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "cho" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "ch") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_57
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin -atl") !== false && ($nb_pref_pos == 0 && $nb_suf_possed == 0) && strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii + 1][2], "suf. nom. verbali") === false && strpos($t70[$ii + 1][2], "r.n.") === false && $nb_hum == 0 && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_58
if($col15 != "***") {
	if(strpos($t70[$ii][0], "aux.") !== false && strpos($t70[$ii][0], "v.t.") === false && strpos($t70[$ii][0], "v.i.") === false && strpos($t70[$ii - 1][4], "r.n.") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_59
if($col15 != "***") {
	if($t70[$ii][5] == "5" && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_60
if($col15 != "***") {
	if(($t70[$ii][3] == "oc" || $t70[$ii][3] == "oque") && strpos($t70[$ii][4], "injecté verbes irréguliers") !== false && $t70[$ii - 1][2] != "liga." && $t70[$ii - 1][3] != "t") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_61
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin iuh > im") !== false && (substr($t70[$ii + 1][3], 0, 1) != "p" && substr($t70[$ii + 1][3], 0, 1) != "m")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_62
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr. (cho)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "h") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_63
if($col15 != "***") {
	if(($t70[$ii][5] == "5" || $t70[$ii][5] == "5 / 5" ) && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_64
if($col15 != "***") {
	if($t70[$ii][3] == "to" && $t70[$ii][2] == "r.n." && $col15 != "***") {
		$col15 = "*to";
		$l5[5] = "irougeclair";
		$test_fait = 1;
	}
}
//------- Teste_66
if($col15 != "***") {
	if(strpos($t70[$ii][5], "5") === false && strpos($t70[$ii + 1][2], "suf. adj. (ctic)") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_67
if($col15 != "***") {
	if($t70[$ii][2] == "r.v." && strpos($t70[$ii][5], "5") === false && (strpos($t70[$ii + 1][2], "suf. adj. (tic)") !== false || strpos($t70[$ii + 1][2], "suf. adj. plur. (tique)") !== false )) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_68
if($col15 != "***") {
	if($t70[$ii][2] == "préf. direc. d'éloign." && $t70[$ii][3] == "on" && (substr($t70[$ii + 1][3], 0, 1) == "p")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_69
if($col15 != "***") {
	if($t70[$ii][2] == "suf. verb. apl. ((i)a)" && (substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "hui" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "qui")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_70
if($col15 != "***") {
	if($t70[$ii][2] == "liga." && $t70[$ii - 1][2] == "r.v." && strpos($t70[$ii - 1][5], "2") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_71
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "lo" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_72
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.r.") === false && $t70[$ii][2] == "r.v." && $nb_reflechi > 0) {
		$col15 = $col15 + " non v.r.";
		$l5[5] = "ibleuclair";
		$test_fait = 1;
	}
}
//------- Teste_73
if($col15 != "***") {
	if($t70[$ii][2] == "r.n." && strpos($t70[$ii][4], "généré m-ph. fin iz > ic") !== false && $t70[$ii + 1][2] != "suf. possed. (e)") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_74
if($col15 != "***") {
	if($t70[$ii][0] == "_v.r._" && $t70[$ii][2] == "r.v." && $nb_reflechi == 0 && $nb_passif == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_75
if($col15 != "***") {
	if(($t70[$ii][3] == "ch" || $t70[$ii][3] == "x" || $t70[$ii][3] == "tz" || $t70[$ii][3] == "cx" ) && strpos($t70[$ii - 1][2], "préf. pos. ") === false && $t70[$ii][2] == "r.n.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_76
if($col15 != "***") {
	if($t70[$ii][3] == "al" && $t70[$ii][2] == "r.n." && $t70[$ii + 1][3] != "tepe") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_77
if($col15 != "***") {
	if($t70[$ii][3] == "chi" && $t70[$ii][2] == "suf. loc. (chi)" && $t70[$ii - 1][3] != "tlal") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_78
if($col15 != "***") {
	if(strpos($t70[$ii][2], "pron.") !== false && strpos($t70[$ii + 1][2], "suf. abs.") !== false && $t70[$ii][3] != "nehua" && $t70[$ii][3] != "tehua" && $t70[$ii][3] != "yehua") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_79
if($col15 != "***") {
	if($t70[$ii][5] == "5" && strpos($t70[$ii + 1][2], "suf. adj.") !== false && $t70[$ii][4] == "généré bases na >" && $t70[$ii + 1][3] != "ctic") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_80
if($col15 != "***") {
	if($t70[$ii][3] == "tla" && $t70[$ii][2] == "suf. nom. verbali. (tla)" && ($t70[$ii - 1][3] != "tlazo" && $t70[$ii - 1][3] != "icniuh" && $t70[$ii - 1][3] != "yao" && $t70[$ii - 1][3] != "ilhui")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_81
if($col15 != "***") {
	if(($t70[$ii][3] == "ca" || $t70[$ii][3] == "cate" || $t70[$ii][3] == "catqui" || $t70[$ii][3] == "catca" || $t70[$ii][3] == "yez") && $t70[$ii][2] == "r.v." && ($t70[$ii - 1][2] == "r.n." || $t70[$ii - 1][2] == "préf. obj. non_hum. indéf." || $t70[$ii - 1][2] == "préf. obj. hum. indéf." || $t70[$ii + 1][2] == "suf. verb. pas. / impers. (l)")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_82
if($col15 != "***") {
	if(($t70[$ii][3] == "yauh" || $t70[$ii][3] == "hui" || $t70[$ii][3] == "yaya" || $t70[$ii][3] == "huiya" || $t70[$ii][3] == "ya" || $t70[$ii][3] == "yaz" || $t70[$ii][3] == "ihuiya" || $t70[$ii][3] == "huiyan" || $t70[$ii][3] == "huilohua" || $t70[$ii][3] == "uh") && $t70[$ii][2] == "r.v." && ($t70[$ii - 1][2] == "r.n." || $t70[$ii - 1][2] == "préf. obj. non_hum. indéf." || $t70[$ii - 1][2] == "préf. obj. hum. indéf." || $t70[$ii - 1][2] == "préf. réfl. 1 sing." )) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_83
if($col15 != "***") {
	if($t70[$ii][3] == "on" && $t70[$ii][2] == "num." && (substr($t70[$ii + 1][3], 0, 1) == "a" || substr($t70[$ii + 1][3], 0, 1) == "e" || substr($t70[$ii + 1][3], 0, 1) == "i" || substr($t70[$ii + 1][3], 0, 1) == "o" || substr($t70[$ii + 1][3], 0, 1) == "u")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_84
if($col15 != "***") {
	if($t70[$ii][2] == "suf. verb. parfait sing. (c)" && (substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "tta" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "za" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "tzi" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "tla" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "ca" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "na" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "hua" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "ya" && $t70[$ii - 1][3] != "i" && $t70[$ii - 1][3] != "cui" && $t70[$ii - 1][3] != "pi")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_85
if($col15 != "***") {
	if($t70[$ii][3] == "ni" && $t70[$ii][2] == "préf. suj. 1 sing." && $t70[($len70 - 1 - 1)][2] == "r.n." && strpos($t70[($len70 - 1 - 1)][4], "injecté supplémentaires") === false && $nb_pref_pos == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_86
if($col15 != "***") {
	if($t70[$ii][2] == "suf. verb. nomina. instrum. (ya)" && $nb_pref_pos == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_87
if($col15 != "***") {
	if($t70[$ii][2] == "préf. indéf. non_hu." && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_88
if($col15 != "***") {
	if($nb_pref_pos == 0 && strpos($t70[$ii][2], "suf. pos.") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_89
if($col15 != "***") {
	if($t70[$ii][2] == "suf. loc. (yan)" && $t70[$ii - 1][2] == "r.v." && strpos($t70[$ii - 1][5], "4") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_90
if($col15 != "***") {
	if($t70[$ii][2] == "adv." && $t70[$ii - 1][2] != "pref. pos." && $t70[$ii][3] == "el") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_91
if($col15 != "***") {
	if($t70[$ii][2] == "quant." && strpos($t70[$ii + 1][2], "suf. nom. verbali.") !== false && strpos($t70[$ii][3], "moch") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_93
if($col15 != "***") {
	if($t70[$ii][3] == "ne" && $t70[$ii][2] == "préf. indéf. réfl." && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_02
if($col15 != "***") {
	if(strpos($t70[$ii][4], "début i >") !== false && $t70[$ii][0] == "1" && strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii][4], "généré bases") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_03
if($col15 != "***") {
	if(strpos($t70[$ii][4], "début i >") !== false && ($t70[$ii - 1][2] != "préf. obj. non_hum. indéf." && $t70[$ii - 1][2] != "préf. indéf. non_hu." && $t70[$ii - 1][2] != "préf. réfl. indéf." && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "i" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "o" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "h")) {
		if(strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii][4], "généré bases") === false) {
			$col15 = "***";
			$test_fait = 1;
		}
	}
}
//------- Teste_04
if($col15 != "***") {
	if(strpos($t70[$ii][4], "début tl >") !== false && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_05
if($col15 != "***") {
	if(($t70[$ii][3] == "m" || $t70[$ii][3] == "n" || $t70[$ii][3] == "c" || $t70[$ii][3] == "t" || $t70[$ii][3] == "l" || $t70[$ii][3] == "p" || $t70[$ii][3] == "qu" || $t70[$ii][3] == "ch" || $t70[$ii][3] == "lp" || $t70[$ii][3] == "z" || $t70[$ii][3] == "tz" || $t70[$ii][3] == "x" || $t70[$ii][3] == "tqu") && ($t70[$ii][2] == "r.n." || $t70[$ii][2] == "r.v." || $t70[$ii][2] == "adv." || $t70[$ii][2] == "adj." || $t70[$ii][2] == "num.")) {
		$l5[5] = "ivertclair";
		$col15 = "*co";
		$test_fait = 1;
	}
}
//------- Teste_06
if($col15 != "***") {
	if(($t70[$ii][3] == "a" || $t70[$ii][3] == "o" || $t70[$ii][3] == "i" || $t70[$ii][3] == "e") && ($t70[$ii][2] == "r.n." || $t70[$ii][2] == "r.v." || $t70[$ii][2] == "adv." || $t70[$ii][2] == "adj." || $t70[$ii][2] == "num.")) {
		$l5[5] = "ivertclair";
		$col15 = "*vo";
		$test_fait = 1;
	}
}
//------- Teste_07
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin iz > ic") !== false && strlen(trim($t70[$ii][4])) == strlen("fin iz > ic") && ($t70[$ii + 1][3] != "e" || $t70[$ii + 1][3] != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_08
if($col15 != "***") {
	if($t70[$ii][2] == "préf. direc. d'éloign." && $t70[$ii][3] == "om" && (substr($t70[$ii + 1][3], 0, 1) != "p" && substr($t70[$ii + 1][3], 0, 1) != "m")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_09
if($col15 != "***") {
	if(($t70[$ii][2] == "préf. réfl. 1 sing." || $t70[$ii][2] == "préf. réfl. 1 plur.") && $t70[$ii][0] == "1") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_10
if($col15 != "***") {
	if($nb_pref_pos > 0 && $t70[($len70 - 1 - 1)][2] == "r.v.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_11
if($col15 != "***") {
	if($nb_pref_pos > 0 && $nb_suf_possed > 0 && $nb_suf_particip == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_12
if($col15 != "***") {
	if(($t70[$ii][2] == "suf. possed. (e)" || $t70[$ii][2] == "suf. possed. (hua)") && ($t70[$ii + 1][2] != "suf. plur. (que)" && $t70[$ii + 1][2] != "suf. particip. (ca)" && $t70[$ii + 1][2] != "suf. loc. (can)") && $ii < ($len70 - 1 - 1) && $t70[$ii + 1][2] != "suf. gent. (ca)") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_13
if($col15 != "***") {
	if($t70[$ii][0] == "1" && $t70[$ii][2] == "liga.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_14
if($col15 != "***") {
	if($ii == ($len70 - 1 - 1) && ($t70[$ii][2] == "lig. (ti)" || $t70[$ii][2] == "liga.")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_15
if($col15 != "***") {
	if($t70[$ii][3] == "n(i)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_16
if($col15 != "***") {
	if($t70[$ii][3] == "ni" && $t70[$ii + 1][4] == "début i >") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_17
if($col15 != "***") {
	if($t70[$ii][3] == "t(i)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_18
if($col15 != "***") {
	if($t70[$ii][3] == "n(o)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_19
if($col15 != "***") {
	if($t70[$ii][3] == "t(o)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_20
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr. (lo)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_21
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr. (zo)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "z") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_22
if($col15 != "***") {
	if($t70[$ii][3] == "ti" && strpos($t70[$ii + 1][4], "début i >") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_23
if($col15 != "***") {
	if(($t70[$ii][3] == "tla" || $t70[$ii][3] == "la") && $t70[$ii][2] == "r.n." && $col15 != "***") {
		$col15 = "*tla";
		$l5[5] = "irougeclair";
		$test_fait = 1;
	}
}
//------- Teste_24
if($col15 != "***") {
	if($t70[$ii][3] == "te" && $t70[$ii][2] == "r.n." && $col15 != "***") {
		$col15 = "*te";
		$l5[5] = "irougeclair";
		$test_fait = 1;
	}
}
//------- Teste_25
if($col15 != "***") {
	if($t70[$ii][2] == "préf. possessif" && ($ii == ($len70 - 1 - 1) || $t70[$ii][0] == 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_26
if($col15 != "***") {
	if($t70[$ii][5] == "3" && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_96_
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.i.") !== false && $t70[$ii][2] == "r.v." && $nb_passif == 0 && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		switch($nb_causatif_applicatif) {
			case 0:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.i. ok";
						break;
					case 1:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.i.ok modif.";
						} else {
							$col15 = $col15 + " v.i. non+";
							$l5[5] = "ibleuclair";
						}
						break;
					default:
						$col15 = $col15 + " v.i. non+";
						$l5[5] = "ibleuclair";
				}
				break;
			case 1:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.i. > v.t. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.i. > v.t. ok satur.";
						} else {
							$col15 = $col15 + " v.i. > v.t. ok";
						}
						break;
					default:
						$col15 = $col15 + " v.i. > v.t. non+";
						$l5[5] = "ibleuclair";
				}
				break;
			case 2:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.i. > bitrans. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_objet_def > 0) {
							$col15 = $col15 + " v.i. > bitrans. ok + réduc.";
						} else {
							$col15 = $col15 + " v.i. > bitrans. non-";
							$l5[5] = "ibleuclair";
						}
						break;
					case 2:
						if($nb_r_nominale == 0) {
							$col15 = $col15 + " v.i. > bitrans. ok";
						} else {
							if($nb_objet_def == 1 && $nb_r_nominale == 1) {
								$col15 = $col15 + " v.i. > bitrans. ok + modif. + réduc.";
							} else {
								$col15 = $col15 + " v.i. > bitrans. ok + satur.";
							}
						}
						break;
					default:
						$col15 = $col15 + " v.i. > bitrans. ok";
				}
				break;
		}
	}
}
//------- Teste_97_
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.t.") !== false && $t70[$ii][2] == "r.v." && $nb_passif == 0 && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		switch($nb_causatif_applicatif) {
			case 0:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.t. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.t. ok satur.";
						} else {
							$col15 = $col15 + " v.t. ok";
						}
						break;
					case 2:
						if($nb_r_nominale == 1) {
							$col15 = $col15 + " v.t. ok modif.";
						} else {
							$col15 = $col15 + " v.t. non+";
							$l5[5] = "ibleuclair";
						}
						break;
					default:
						$col15 = $col15 + " v.t. non+";
						$l5[5] = "ibleuclair";
				}
				break;
			case 1:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.t. > v.bi. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_objet_def > 0) {
							$col15 = $col15 + " v.t. > bitrans. ok + réduc.";
						} else {
							$col15 = $col15 + " v.t. > bitrans. non-";
							$l5[5] = "ibleuclair";
						}
						break;
					case 2:
						if($nb_r_nominale == 0) {
							$col15 = $col15 + " v.t. > bitrans. ok";
						} else {
							if($nb_objet_def == 1 && $nb_r_nominale == 1) {
								$col15 = $col15 + " v.t. > bitrans. ok + modif. + réduc.";
							} else {
								$col15 = $col15 + " v.t. > bitrans. ok + satur.";
							}
						}
						break;
					default:
						$col15 = $col15 + " v.t. > bitrans. ok";
				}
				break;
			case 2:
				switch($nb_tot_objet) {
					case 0:
						$col15 = $col15 + " v.t. > bitrans. non-";
						$l5[5] = "ibleuclair";
						break;
					case 1:
						if($nb_objet_def > 0) {
							$col15 = $col15 + " v.t. > bitrans. ok + réduc.";
						} else {
							$col15 = $col15 + " v.t. > bitrans. non-";
							$l5[5] = "ibleuclair";
						}
						break;
					case 2:
						if($nb_r_nominale == 0) {
							$col15 = $col15 + " v.t. > bitrans. ok";
						} else {
							if($nb_objet_def == 1 && $nb_r_nominale == 1) {
								$col15 = $col15 + " v.t. > bitrans. ok + modif. + réduc.";
							} else {
								$col15 = $col15 + " v.t. > bitrans. ok + satur.";
							}
						}
						break;
					default:
						$col15 = $col15 + " v.t. > bitrans.";
				}
				break;
		}
	}
}
//------- Teste_98_
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.bi.") !== false && $t70[$ii][2] == "r.v." && $nb_passif == 0 && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		switch($nb_tot_objet) {
			case 0:
				$col15 = $col15 + " v.bi. non-";
				$l5[5] = "ibleuclair";
				break;
			case 1:
				if($nb_objet_def > 0) {
					$col15 = $col15 + " v.bi. ok + réduc.";
				} else {
					$col15 = $col15 + " v.bi. non-";
					$l5[5] = "ibleuclair";
				}
				break;
			case 2:
				if($nb_r_nominale == 0) {
					$col15 = $col15 + " v.bi. ok";
				} else {
					if($nb_objet_def == 1 && $nb_r_nominale == 1) {
						$col15 = $col15 + " v.bi. ok + modif. + réduc.";
					} else {
						$col15 = $col15 + " v.bi. ok + satur.";
					}
				}
				break;
			default:
				$col15 = $col15 + " v.bi. ok";
		}
	}
}
//------- Teste_32
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin ahui > am") !== false && substr($t70[$ii + 1][3], 0, 1) != "m") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_33
if($col15 != "***") {
	if(($t70[$ii][2] == "préf. réfléchi" || $t70[$ii][2] == "préf. possessif") && $t70[$ii][0] == "1") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_34
if($col15 != "***") {
	if($t70[$ii][3] == "m(o)" && (substr($t70[$ii + 1][3], 0, 1) != "a" && substr($t70[$ii + 1][3], 0, 1) != "o" && substr($t70[$ii + 1][3], 0, 1) != "i" && substr($t70[$ii + 1][3], 0, 1) != "e")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_35
if($col15 != "***") {
	if($t70[$ii][2] == "préf. possessif" && (substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "tl" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "li")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_36
if($col15 != "***") {
	if($t70[$ii][2] == "suf. loc. (can)" && $nb_objet == 0 && $nb_r_nominale == 0 && strpos($t70[$ii - 1][0], "v.i.") === false && strpos($t70[$ii - 1][2], "adj.") === false && strpos($t70[$ii - 1][2], "adv.") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_37
if($col15 != "***") {
	if($t70[$ii][2] == "préf. indéf. non_hu." && $t70[$ii][3] == "la" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_38
if($col15 != "***") {
	if($t70[$ii][2] == "suf. loc. ab. (la)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_39
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin omi > on") !== false && strlen(trim($t70[$ii][4])) == strlen("généré m-ph. fin omi > on") && (substr($t70[$ii + 1][3], 0, 1) == "a" || substr($t70[$ii + 1][3], 0, 1) == "e" || substr($t70[$ii + 1][3], 0, 1) == "i" || substr($t70[$ii + 1][3], 0, 1) == "o" || substr($t70[$ii + 1][3], 0, 1) == "u")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_40
if($col15 != "***") {
	if($ii == ($len70 - 1 - 1) && ($t70[$ii][2] == "participial")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_41
if($col15 != "***") {
	if($nb_pref_pos > 0 && $nb_r_nominale == 0 && $nb_suf_particip == 0 && $nb_suf_nomina == 0 && strpos("-pan-cpac-tlan-tech-ca-nahuac-tzalan-nepantla-pal-pampa-tloc-huan-huic-icampa-nehuan-cel-huic-copa-co-pa-tzin", $t70[2][3]) === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_42
if($col15 != "***") {
	if($t70[$ii][0] == "1" && trim($t70[$ii][2]) == "préf. 3 plur. (im)") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_43
if($col15 != "***") {
	if($t70[$ii][2] == "négation" && $t70[$ii + 1][4] = "début i >") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_44
if($col15 != "***") {
	if($ii == ($len70 - 1 - 1) && ($t70[$ii][2] == "préf. indéf. non_hu." || $t70[$ii][2] == "préf. indéf. ani.")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_45
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin -atl") !== false && ($nb_pref_pos == 0 && $nb_suf_possed == 0) && strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii + 1][2], "suf. nom. verbali") === false && strpos($t70[$ii + 1][2], "r.n.") === false && strpos($t70[$ii + 1][2], "r.v.") === false && strpos($t70[$ii - 1][2], "préf. obj. hum. indéf.") === false && $nb_hum == 0 && strpos($t70[$ii + 1][2], "suf. abs.") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_46
if($col15 != "***") {
	if($t70[$ii][0] == 1 && ($t70[$ii][2] == "participial" || $t70[$ii][2] == "liga." || $t70[$ii][2] == "suf. abstr.")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_48
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin auh > ap") !== false && substr($t70[$ii + 1][3], 0, 1) != "p") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_49
if($col15 != "***") {
	if($t70[$ii][2] == "liga." && $t70[$ii][3] == "t" && $t70[$ii + 1][3] == "i") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_50
if($col15 != "***") {
	if($t70[$ii][2] == "liga." && strpos($t70[$ii + 1][0], "aux.") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_51
if($col15 != "***") {
	if(strpos($t70[$ii][4], "az > ac") !== false && $t70[$ii + 1][3] != "i" && $t70[$ii + 1][3] != "e") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_52
if($col15 != "***") {
	if(strpos($t70[$ii][4], "az > ac") !== false && $t70[$ii + 1][3] != "i" && $t70[$ii + 1][3] != "e") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_53
if($col15 != "***") {
	if(strpos($t70[$ii][0], "aux.") !== false && $t70[$ii][3] == "uh" && $t70[$ii - 1][2] != "liga.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_54
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "tzo" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "tz") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_55
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "zo" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "z") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_56
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "cho" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "ch") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_57
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin -atl") !== false && ($nb_pref_pos == 0 && $nb_suf_possed == 0) && strpos($t70[$ii][4], "injecté") === false && strpos($t70[$ii + 1][2], "suf. nom. verbali") === false && strpos($t70[$ii + 1][2], "r.n.") === false && $nb_hum == 0 && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_58
if($col15 != "***") {
	if(strpos($t70[$ii][0], "aux.") !== false && strpos($t70[$ii][0], "v.t.") === false && strpos($t70[$ii][0], "v.i.") === false && strpos($t70[$ii - 1][4], "r.n.") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_59
if($col15 != "***") {
	if($t70[$ii][5] == "5" && strpos($t70[$ii + 1][2], "suf. adj.") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_60
if($col15 != "***") {
	if(($t70[$ii][3] == "oc" || $t70[$ii][3] == "oque") && strpos($t70[$ii][4], "injecté verbes irréguliers") !== false && $t70[$ii - 1][2] != "liga." && $t70[$ii - 1][3] != "t") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_61
if($col15 != "***") {
	if(strpos($t70[$ii][4], "fin iuh > im") !== false && (substr($t70[$ii + 1][3], 0, 1) != "p" && substr($t70[$ii + 1][3], 0, 1) != "m")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_62
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr. (cho)" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "h") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_63
if($col15 != "***") {
	if(($t70[$ii][5] == "5" || $t70[$ii][5] == "5 / 5" ) && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_64
if($col15 != "***") {
	if($t70[$ii][3] == "to" && $t70[$ii][2] == "r.n." && $col15 != "***") {
		$col15 = "*to";
		$l5[5] = "irougeclair";
		$test_fait = 1;
	}
}
//------- Teste_66
if($col15 != "***") {
	if(strpos($t70[$ii][5], "5") === false && strpos($t70[$ii + 1][2], "suf. adj. (ctic)") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_67
if($col15 != "***") {
	if($t70[$ii][2] == "r.v." && strpos($t70[$ii][5], "5") === false && (strpos($t70[$ii + 1][2], "suf. adj. (tic)") !== false || strpos($t70[$ii + 1][2], "suf. adj. plur. (tique)") !== false )) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_68
if($col15 != "***") {
	if($t70[$ii][2] == "préf. direc. d'éloign." && $t70[$ii][3] == "on" && (substr($t70[$ii + 1][3], 0, 1) == "p")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_69
if($col15 != "***") {
	if($t70[$ii][2] == "suf. verb. apl. ((i)a)" && (substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "hui" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "qui")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_70
if($col15 != "***") {
	if($t70[$ii][2] == "liga." && $t70[$ii - 1][2] == "r.v." && strpos($t70[$ii - 1][5], "2") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_71
if($col15 != "***") {
	if($t70[$ii][2] == "suf. abstr." && $t70[$ii][3] == "lo" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1) != "l") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_72
if($col15 != "***") {
	if(strpos($t70[$ii][0], "v.r.") === false && $t70[$ii][2] == "r.v." && $nb_reflechi > 0) {
		$col15 = $col15 + " non v.r.";
		$l5[5] = "ibleuclair";
		$test_fait = 1;
	}
}
//------- Teste_73
if($col15 != "***") {
	if($t70[$ii][2] == "r.n." && strpos($t70[$ii][4], "généré m-ph. fin iz > ic") !== false && $t70[$ii + 1][2] != "suf. possed. (e)") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_74
if($col15 != "***") {
	if($t70[$ii][0] == "_v.r._" && $t70[$ii][2] == "r.v." && $nb_reflechi == 0 && $nb_passif == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_75
if($col15 != "***") {
	if(($t70[$ii][3] == "ch" || $t70[$ii][3] == "x" || $t70[$ii][3] == "tz" || $t70[$ii][3] == "cx" ) && strpos($t70[$ii - 1][2], "préf. pos. ") === false && $t70[$ii][2] == "r.n.") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_76
if($col15 != "***") {
	if($t70[$ii][3] == "al" && $t70[$ii][2] == "r.n." && $t70[$ii + 1][3] != "tepe") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_77
if($col15 != "***") {
	if($t70[$ii][3] == "chi" && $t70[$ii][2] == "suf. loc. (chi)" && $t70[$ii - 1][3] != "tlal") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_78
if($col15 != "***") {
	if(strpos($t70[$ii][2], "pron.") !== false && strpos($t70[$ii + 1][2], "suf. abs.") !== false && $t70[$ii][3] != "nehua" && $t70[$ii][3] != "tehua" && $t70[$ii][3] != "yehua") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_79
if($col15 != "***") {
	if($t70[$ii][5] == "5" && strpos($t70[$ii + 1][2], "suf. adj.") !== false && $t70[$ii][4] == "généré bases na >" && $t70[$ii + 1][3] != "ctic") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_80
if($col15 != "***") {
	if($t70[$ii][3] == "tla" && $t70[$ii][2] == "suf. nom. verbali. (tla)" && ($t70[$ii - 1][3] != "tlazo" && $t70[$ii - 1][3] != "icniuh" && $t70[$ii - 1][3] != "yao" && $t70[$ii - 1][3] != "ilhui")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_81
if($col15 != "***") {
	if(($t70[$ii][3] == "ca" || $t70[$ii][3] == "cate" || $t70[$ii][3] == "catqui" || $t70[$ii][3] == "catca" || $t70[$ii][3] == "yez") && $t70[$ii][2] == "r.v." && ($t70[$ii - 1][2] == "r.n." || $t70[$ii - 1][2] == "préf. obj. non_hum. indéf." || $t70[$ii - 1][2] == "préf. obj. hum. indéf." || $t70[$ii + 1][2] == "suf. verb. pas. / impers. (l)")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_82
if($col15 != "***") {
	if(($t70[$ii][3] == "yauh" || $t70[$ii][3] == "hui" || $t70[$ii][3] == "yaya" || $t70[$ii][3] == "huiya" || $t70[$ii][3] == "ya" || $t70[$ii][3] == "yaz" || $t70[$ii][3] == "ihuiya" || $t70[$ii][3] == "huiyan" || $t70[$ii][3] == "huilohua" || $t70[$ii][3] == "uh") && $t70[$ii][2] == "r.v." && ($t70[$ii - 1][2] == "r.n." || $t70[$ii - 1][2] == "préf. obj. non_hum. indéf." || $t70[$ii - 1][2] == "préf. obj. hum. indéf." || $t70[$ii - 1][2] == "préf. réfl. 1 sing." )) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_83
if($col15 != "***") {
	if($t70[$ii][3] == "on" && $t70[$ii][2] == "num." && (substr($t70[$ii + 1][3], 0, 1) == "a" || substr($t70[$ii + 1][3], 0, 1) == "e" || substr($t70[$ii + 1][3], 0, 1) == "i" || substr($t70[$ii + 1][3], 0, 1) == "o" || substr($t70[$ii + 1][3], 0, 1) == "u")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_84
if($col15 != "***") {
	if($t70[$ii][2] == "suf. verb. parfait sing. (c)" && (substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "tta" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "za" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "tzi" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "tla" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "ca" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "na" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 2 - 1) != "hua" && substr($t70[$ii - 1][3], strlen($t70[$ii - 1][3]) - 1 - 1) != "ya" && $t70[$ii - 1][3] != "i" && $t70[$ii - 1][3] != "cui" && $t70[$ii - 1][3] != "pi")) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_85
if($col15 != "***") {
	if($t70[$ii][3] == "ni" && $t70[$ii][2] == "préf. suj. 1 sing." && $t70[($len70 - 1 - 1)][2] == "r.n." && strpos($t70[($len70 - 1 - 1)][4], "injecté supplémentaires") === false && $nb_pref_pos == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_86
if($col15 != "***") {
	if($t70[$ii][2] == "suf. verb. nomina. instrum. (ya)" && $nb_pref_pos == 0) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_87
if($col15 != "***") {
	if($t70[$ii][2] == "préf. indéf. non_hu." && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_88
if($col15 != "***") {
	if($nb_pref_pos == 0 && strpos($t70[$ii][2], "suf. pos.") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_89
if($col15 != "***") {
	if($t70[$ii][2] == "suf. loc. (yan)" && $t70[$ii - 1][2] == "r.v." && strpos($t70[$ii - 1][5], "4") === false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_90
if($col15 != "***") {
	if($t70[$ii][2] == "adv." && $t70[$ii - 1][2] != "pref. pos." && $t70[$ii][3] == "el") {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_91
if($col15 != "***") {
	if($t70[$ii][2] == "quant." && strpos($t70[$ii + 1][2], "suf. nom. verbali.") !== false && strpos($t70[$ii][3], "moch") !== false) {
		$col15 = "***";
		$test_fait = 1;
	}
}
//------- Teste_93
if($col15 != "***") {
	if($t70[$ii][3] == "ne" && $t70[$ii][2] == "préf. indéf. réfl." && $ii == ($len70 - 1 - 1)) {
		$col15 = "***";
		$test_fait = 1;
	}
}