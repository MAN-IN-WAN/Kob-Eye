<?php
$vars['user'] = Sys::$User->Id;
$g = Sys::$User->getParents('Group')[0];
$vars['group'] = $g->Nom;
