<?php
$vars['user'] = Sys::$User->Id;
$g = Sys::$User->getParents('Group')[0];
$vars['group'] = $g->Nom;
if(Sys::$User->isRole('CADREF_ADH')) $vars['role'] = 'CADREF_ADH';
else if(Sys::$User->isRole('CADREF_ADMIN')) $vars['role'] = 'CADREF_ADMIN';
else if(Sys::$User->isRole('CADREF_ENS')) $vars['role'] = 'CADREF_ENS';
