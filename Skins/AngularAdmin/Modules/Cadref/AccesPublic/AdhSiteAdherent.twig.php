<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Adherent';
$vars['identifier'] = $vars['module'].$vars['objecttype'];
$o = genericClass::createInstance($vars['module'],$vars['objecttype']);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
$vars["CurrentObj"] = $o;

