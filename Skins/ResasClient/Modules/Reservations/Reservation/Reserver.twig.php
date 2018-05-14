<?php

$hasCli = (isset($vars['unregister']) &&  $vars['unregister'] == 1)? false:true;

//Validation de la réservation
if($vars['Valider'] == 'Valider la réservation'){
    $vars['RES']->setPending();
    $vars['RES']->Save();
    $GLOBALS["Systeme"]->Connection->addSessionVar('RES',$vars['RES']);
}

if($vars['Valider'] == 'Payer en carte bleue'){
    $vars['RES']->Save();
    $GLOBALS["Systeme"]->Connection->addSessionVar('RES',$vars['RES']);
    header('Location: ' . $vars['Domaine'] .'/'.Sys::getMenu('Reservations/Reservation').'/'.$vars['RES']->Id.'/Payer');
}

$vars['CHECK'] = true;
if(!$vars['RES']->Verify($hasCli)){
    $vars['CHECK'] = false;
}

$vars['Service'] = $vars['RES']->getService();
$vars['Court'] = $vars['RES']->getCourt();
$vars['TypeCourt'] = $vars['Court']->getOneParent('TypeCourt');
$vars['Partenaires'] = $vars['RES']->getPartenaires();
$vars['LigneFacture'] = $vars['RES']->getLigneFacture();
$vars['Total'] = $vars['RES']->getTotal();


