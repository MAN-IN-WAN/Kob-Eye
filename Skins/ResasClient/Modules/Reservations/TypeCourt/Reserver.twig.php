<?php

if(!isset($vars['Date']) || $vars['Date']=='' || !isset($vars['Court']) || $vars['Court']=='' || !isset($vars['HeureDebut']) || $vars['HeureDebut']==''){
    header('Location: '.$vars['Domaine'].'/Reserver');
    die();
}


$vars['Client'] = Reservations::getCurrentClient();
$vars['CPart'] = $vars['Client'] ? $vars['Client']->getOneParent('Partenaire') : null;
$vars['Partenaires'] = $vars['Client'] ? $vars['Client']->getChildren('Partenaire') : null;




$vars['Dispos'] = Sys::getData('Reservations','Partenaire/Client/Disponible=1');
if(isset($vars['NombreParticipant']) && $vars['NombreParticipant']<1) $vars['NombreParticipant'] =1;

$vars['DateDeb'] = $vars['Date'] + $vars['HeureDebut']*3600;
$vars['CourtObj'] = Sys::getOneData('Reservations','Court/'.$vars['Court']);
$vars['TypeCourt'] = $vars['CourtObj']->getOneParent('TypeCourt');
$srvCourt = $vars['CourtObj']->getChildren('Service/Type=Reservation');
$srvTypeCourt = $vars['TypeCourt']->getChildren('Service/Type=Reservation');
$vars['srvReserv'] = array_merge($srvCourt,$srvTypeCourt);
$vars['Prods'] = $vars['CourtObj']->getChildren('Service/Type=Produit');



if($vars['Action'] == 'Réserver'){
    $hasCli = (isset($vars['unregister']) &&  $vars['unregister'] == 1)? false:true;

    $res = Reservations::createReservation($vars['Date'],$vars['Court'],$vars['HeureDebut'],$vars['ServiceDuree'],$hasCli);
    $vars['Res'] = $res;
    switch($vars['TypeCourt']->GestionInvite){
        case 'Quantitatif':
            $res->setNombrePartenaires(vars['NombreParticipant'] - 1);
            break;
        case 'Nominatif' :
            if(isset($vars['PaiementParticipant']) && $vars['PaiementParticipant'] == 1){
                $res->Set('PaiementParticipant',1);
            }
            $res->setPartenairesBis($vars['Partenaire']);
            break;
    }
    if(isset($vars['Service']) && $vars['Service']!='')
        $res->setProduits($vars['Service']);

    $vars['ResSucces'] = false;
    //Traitement des cas sans client
    if(!$hasCli){
        $cli = null;
        if(isset($vars['unregisterMail']) && $vars['unregisterMail'] != '' ){
            $cli = Sys::getOneData('Reservations','Client/Mail='.$vars['unregisterMail']);
        }
        if(!$cli){
            $cli = genericClass::createInstance('Reservations','Client');
            $cli->Pass = 'fakeCli';
        }
        foreach ($vars as $k=>$v){
            if(strpos($k,'unregister') == 0){
                $prop = str_replace('unregister','',$k);
                $val = $v;
                $cli->{$prop} = $val;
            }
        }
        if($cli->Verify(0)){
            $cli->Save(0);
            $res->addParent($cli);
        } else{
            print_r($cli->Error);
            foreach ($cli->Error as $k=>$i){
                $vars['Error_'.$i->Prop] = 1;
            }
        }

    }

    if($res->Verify()){
        $GLOBALS["Systeme"]->Connection->addSessionVar('RES',$res);
        $vars['ResSucces'] = true;
        header('Location: ' . $vars['Domaine'] .'/'. Sys::getMenu('Reservations/Reservation') . '/Reserver');
    } else{
        print_r($res->Error);
        foreach ($res->Error as $k=>$i){
            $vars['Error_'.$i->Prop] = 1;
        }
    }




}


