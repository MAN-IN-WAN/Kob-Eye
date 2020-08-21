<?php
$info=Info::getInfos($vars["Query"]);

$LeService= Sys::getOneData($info["Module"],$info["Query"]);
$LeService->Media= $LeService->getOneChild("Media");
$vars["UnService"]= $LeService;
$vars['Retour']=Sys::$CurrentMenu->Url;