<?php
$vars['group'] = Sys::$User->getParents('Group')[0]->Nom;
$vars['adherent'] = $vars['group'] == 'CADREF_ADH';

	
