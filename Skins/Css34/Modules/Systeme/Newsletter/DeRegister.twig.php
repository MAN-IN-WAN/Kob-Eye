<?php

$add = $_GET['address'];
$grp = Sys::getOneData('Newsletter','GroupeEnvoi/Titre=INSCRIPTION_NEWSLETTER');
$obj =$grp->getOneChild('Contact/Email='.$add);
$res = 0;
if($obj){
    $res = $obj->Delete();
} else {
    echo json_encode(array('address'=>$add,'success'=>true));
    die();
}

echo json_encode(array('address'=>$add,'success'=>$res));