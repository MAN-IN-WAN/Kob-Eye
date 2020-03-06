<?php

$src = 'Teste_96_01	// pour les verbes intransitifs qui ont un objet ou plus. Le traitement des causatifs est fait  Les formes passives sont exclues
Teste_96_01	si col15[j] <> "***"
Teste_96_02	si position(transitifs[ii],"v.i.") > 0 et categories[ii] = "r.v." et nb_passif = 0 et position(categories[ii+1],"suf. adj.") = 0
Teste_96_03	selon nb_causatif_applicatif
Teste_96_04	Cas 0
Teste_96_05	Selon nb_tot_objet
Teste_96_06	cas 0
Teste_96_07	col15[j] = col15[j] + " v.i. ok"
Teste_96_08	cas 1
Teste_96_09	si nb_r_nominale = 1
Teste_96_10	col15[j] = col15[j] + " v.i.ok Modif."
Teste_96_11	sinon
Teste_96_12	col15[j] = col15[j] + " v.i. non+"
Teste_96_13	table5[j]..Couleur = iBleuclair 
Teste_96_14	fin
Teste_96_15	autre cas
Teste_96_16	col15[j] = col15[j] + " v.i. non+"
Teste_96_17	table5[j]..Couleur = iBleuclair 
Teste_96_18	fin
Teste_96_19	cas 1
Teste_96_20	Selon nb_tot_objet
Teste_96_21	cas 0
Teste_96_22	col15[j] = col15[j] + " v.i. > v.t. non-"
Teste_96_23	table5[j]..Couleur = iBleuclair 
Teste_96_24	cas 1
Teste_96_25	si nb_r_nominale = 1
Teste_96_26	col15[j] = col15[j] + " v.i. > v.t. ok Satur."
Teste_96_27	sinon
Teste_96_28	col15[j] = col15[j] + " v.i. > v.t. ok"
Teste_96_29	fin
Teste_96_30	autre cas
Teste_96_31	col15[j] = col15[j] + " v.i. > v.t. non+"
Teste_96_32	table5[j]..Couleur = iBleuclair 
Teste_96_33	fin
Teste_96_34	cas 2
Teste_96_35	Selon nb_tot_objet
Teste_96_36	cas 0
Teste_96_37	col15[j] = col15[j] + " v.i. > bitrans. non-"
Teste_96_38	table5[j]..Couleur = iBleuclair 
Teste_96_39	cas 1
Teste_96_40	si nb_objet_def > 0
Teste_96_41	col15[j] = col15[j] + " v.i. > bitrans. ok + Réduc."
Teste_96_42	sinon
Teste_96_43	col15[j] = col15[j] + " v.i. > bitrans. non-"
Teste_96_44	table5[j]..Couleur = iBleuclair 
Teste_96_45	fin
Teste_96_46	cas 2
Teste_96_47	si nb_r_nominale = 0
Teste_96_48	col15[j] = col15[j] + " v.i. > bitrans. ok"
Teste_96_49	sinon
Teste_96_50	si nb_objet_def = 1 et nb_r_nominale = 1
Teste_96_51	col15[j] = col15[j] + " v.i. > bitrans. ok + Modif. + Réduc."
Teste_96_52	sinon
Teste_96_53	col15[j] = col15[j] + " v.i. > bitrans. ok + Satur."
Teste_96_54	fin
Teste_96_55	fin
Teste_96_56	autre cas 
Teste_96_57	col15[j] = col15[j] + " v.i. > bitrans. ok"
Teste_96_58	fin
Teste_96_59	fin
Teste_96_60	fin
Teste_96_61	fin';
//var_dump($src);

function wd2php($w) {
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
		$s = str_replace(' <> ', ' != ', $s);
		$s = str_replace(') > 0', ') !== false', $s);
		$s = str_replace(') = 0', ') === false', $s);
		$s = str_replace(') =0', ') === false', $s);
		$s = str_replace(')= 0', ') === false', $s);
		$s = str_replace(' = ', ' == ', $s);
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



$tsts = array();
$ls = explode("\n", $src); //utf8_encode
foreach($ls as &$l) {
	$cnd = explode("\t", $l);
	foreach($cnd as &$c) $c = trim($c);
	$tsts[] = $cnd;
}

//var_dump($tsts);

$source = '';
$par = array();
$npar = -1;
$ltst = count($tsts);
for($j = 0; $j < $ltst; $j++) {
	$tst = $tsts[$j];
	$s = $tst[1];
	if(substr($s, 0, 2) != '//') {
		$s = trim(wd2php($s));
		if(substr($s, 0, 1) == '}')  {
			echo "$npar "; foreach($par as $p) echo ":$p"; echo "\n";
			if($npar >= 0 && $par[$npar]) {
				$source .= str_repeat('    ', $npar)."break;\n"; 
				echo "break\n";
			}
			array_splice($par, $npar--, 1);
			echo "$npar "; foreach($par as $p) echo ":$p"; echo "\n";
		}
		if(substr($s, -1, 1) == '{') {
			$par[++$npar] = 0;
			echo "$npar "; foreach($par as $p) echo ":$p"; echo "\n";
		}
		elseif($npar >= 0 && $par[$npar] && (substr($s, 0, 5) == 'case ' || $s == 'default:')) {
			$source .= str_repeat('    ', $npar)."break;\n";
			echo "break\n";
			$par[$npar]--;
			echo "$npar "; foreach($par as $p) echo ":$p"; echo "\n";
		}
		if(substr($s, 0, 5) == 'case ') {
			$par[$npar]++;
			echo "$npar "; foreach($par as $p) echo ":$p"; echo "\n";
		}
				echo "$s\n";

		$source .= str_repeat('    ', $npar)."$s\n";
	}
}
echo "\n\n\n$source";

