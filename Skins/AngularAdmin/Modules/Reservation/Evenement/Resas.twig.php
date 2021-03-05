<?php

$info = Info::getInfos($vars['Query']);

$vars['evt'] = Sys::getOneData($info['Module'],$info['Query']);
$test = Sys::getOneData("Reservation","Spectacle/Evenement/".$vars['evt']->Id);
//$test = $test->getOneChild("Spectacle");
$vars["spectacleInfo"] = $test->InformationReservation;
$personne = genericClass::createInstance('Reservation','Personne');
$fields = $personne->getElementsByAttribute('','',true);
$temp = array();
foreach($fields as $f){
    $temp[$f['name']] = $f;
}
$vars['elems']=$temp;

//print_r($vars['funcTempVars']);