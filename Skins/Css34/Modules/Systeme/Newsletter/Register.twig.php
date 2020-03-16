<?php
$add = $_GET['address'];
$grp = Sys::getOneData('Newsletter','GroupeEnvoi/Titre=INSCRIPTION_NEWSLETTER');
$obj = Sys::getOneData('Newsletter','Contact/Email='.$add);
if(!$obj){
    $obj = genericClass::createInstance('Newsletter','Contact');
    $obj->Email = $add;
    $obj->Libelle = 'Inscription Site le '.date('d/m/Y à H:i:s');
} /*else {
    echo json_encode(array('address'=>$add,'success'=>false));
    die();
}*/

$obj->addParent($grp);
$res = 0;
if($obj->Verify())
    $res = $obj->Save();

echo json_encode(array('address'=>$add,'success'=>$res));