<?php


//$date_debut = $vars['date'];
//$date_fin = $vars['date']+86400;

$res= array();
//On recup les 180jours suivants
for($i=0;$i<=180;$i++){
    $date_debut = $vars['date']+$i*86400;
    $date_fin = $vars['date']+($i+1)*86400;

    $court = Sys::getOneData('Reservations','Court/'.$vars['court']);
    $resas = $court->getChildren('Reservation/Valide=1&DateDebut>'.$date_debut.'&DateFin<'.$date_fin);

    $tempDispos = Disponibilite::getDispo($date_debut+1,$date_fin-1);

    $dispos = array();
    foreach($tempDispos as $k=>$td){
        $flag= false;
        foreach ($td->_courts as $c) if ($c->Id==$court->Id) $flag = true;
        if ($flag){
            array_push($dispos,$td);
        }
    }


    $typeCourt = $court->getOneParent('TypeCourt');
    $service = $typeCourt->getOneChild('Service');

    $hDeb = $service->HeureOuverture;
    $hFin = $service->HeureFermeture;

    sscanf($hDeb, "%d:%d", $heuredeb, $minutedeb);
    sscanf($hFin, "%d:%d", $heurefin, $minutefin);

    $totalTime = ($heurefin-$heuredeb)*3600;

    if($minutedeb != 0) $totalTime -= $minutedeb*60;
    if($minutefin != 0) $totalTime += $minutefin*60;

/*    foreach($resas as $r){
        $duree = $r->DateFin - $r->DateDebut;
        $totalTime -= $duree;
    }*/

    foreach($dispos as $d){
        $duree = $d->Fin - $d->Debut;
        $totalTime -= $duree;
    }
    $res[$date_debut] = array('full'=>($totalTime<=0));
}

echo json_encode($res);