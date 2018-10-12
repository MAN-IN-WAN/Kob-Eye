<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['functions'] = $o->getFunctions();
$vars['fichefields'] = $o->getElementsByAttribute('fiche','',true);
foreach ($vars['fichefields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fichefields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
    }
}
$vars['formfields'] = $o->getElementsByAttribute('form','',true);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars["CurrentObj"] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
$vars['identifier'] = $info['Module'] . $info['ObjectType'];
if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];


$vars['browseable'] = $vars["ObjectClass"]->browseable;
$q = $info['Module'].'/'.$info['ObjectType'].'/';
$vars['CurrentObjId'] = $GLOBALS["Systeme"]->getGetVars("CurrentObjId");
$vars['CurrentObjQuery'] = $q.$vars['CurrentObjId'];
$p = getcwd().'/Skins/'.Sys::$Skin.'/Modules/'.$q;
$vars['FichePath'] = file_exists($p.'Fiche.twig') ? $q : $info['Module'].'/Default/';

?>