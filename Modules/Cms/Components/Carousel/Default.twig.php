<?php
$Requete = $vars['REQUETE'];
unset($vars['CurrentUser']);
print_r($vars);
$Requete = explode('/', $Requete,2);
//var_dump($vars['INDICATORS']);
$vars['spectacle'] = Sys::getData($Requete[0],$Requete[1],0,10);

//print_r($vars['spectacle']);

