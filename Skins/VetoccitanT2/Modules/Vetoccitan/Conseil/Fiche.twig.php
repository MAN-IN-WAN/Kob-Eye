<?php
$info=Info::getInfos($vars["Query"]);

$LeConseil= Sys::getOneData($info["Module"],$info["Query"]);
$LeConseil->Media= $LeConseil->getOneChild("Media");
$vars["UnConseil"]= $LeConseil;
$vars['Retour']=Sys::$CurrentMenu->Url;