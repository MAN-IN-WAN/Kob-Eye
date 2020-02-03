<?php
$vars['CurrentObj'] = genericClass::createInstance('Reservation','Client');
$vars['identifier'] = $vars['Url'];

$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();

$usr = Sys::$User;
$grps = $usr->getParents('Group');
foreach($grps as $grp){
    $cli = Sys::getOneData('Reservation','Client/NumeroGroupe='.$grp->Id);
    if(!empty($cli)){
        $vars['cli'] = $cli->Id;
        break;
    }
}
