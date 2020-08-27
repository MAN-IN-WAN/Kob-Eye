<?php
$info=Info::getInfos($vars["Query"]);

$LaNews= Sys::getOneData($info["Module"],$info["Query"]);
$LaNews->Media= $LaNews->getOneChild("Media");
$vars["UneNews"]= $LaNews;
$vars['Retour']=Sys::$CurrentMenu->Url;