<?php
$vars['group'] = Sys::$User->getParents('Group')[0]->Nom;
$vars['adherent'] = false;
$groups = Sys::$User->getParents('Group');
foreach($groups as $g) {
	if($g->Nom == 'CADREF_ADH') $vars['adherent'] = true;
}

	
