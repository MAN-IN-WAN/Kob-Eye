<?php
$info = Info::getInfos($vars['Query']);
$vars['group'] = Sys::$User->getParents('Group')[0]->Nom;
$vars['adherent'] = Cadref::$Group == 'CADREF_ADH';

$vars['Type'] = isset($_GET['Type']) ? $_GET['Type'] : '';

//$vars['adherent'] = false;
//$groups = Sys::$User->getParents('Group');
//foreach($groups as $g) {
//	if($g->Nom == 'CADREF_ADH') $vars['adherent'] = true;
//}

//if(!$vars['adherent']) {
//	if(Cadref::$Enseignant) $vars['ensid'] = Cadref::$Enseignant->Id;
//	else $vars['ensid'] = 0;
//}
