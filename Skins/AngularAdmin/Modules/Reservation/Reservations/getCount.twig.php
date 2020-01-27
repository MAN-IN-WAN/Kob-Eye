<?php
session_write_close();
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
//calcul offset / limit
$filters = (isset($_GET['filters']))?$_GET['filters']:'';
$path = explode('/',$vars['Path'],2);
$path = $path[1];

//requete
if(connection_aborted()){
    endPacket();
    exit;
}
if(!empty($filters) && $filters == 'Dashboard'){
    $inittms = strtotime("-1 year");
    /*//Recup du plus proche parent surlequel on a une date : l'evenement
    $evs = Sys::getData('Reservation','Evenement/DateDebut>'.$inittms);
    $vars['count'] = 0;
    foreach ($evs as $ev){
        $resas = $ev->getChildren('Reservations');
        if(!empty($resas))
            $vars['count'] += count($resas);
    }*/

    $vars['count'] = Sys::getCount($info['Module'],'Reservations/tmsCreate>' .$inittms);
} else {
    $vars['count'] = Sys::getCount($info['Module'],'Reservations/' . html_entity_decode($filters));
}


