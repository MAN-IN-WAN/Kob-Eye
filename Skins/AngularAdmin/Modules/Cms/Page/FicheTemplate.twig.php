<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['functions'] = $o->getFunctions();
$vars['fichefields'] = $o->getElementsByAttribute('fiche','',false);

foreach ($vars['fichefields'] as $kc=>$fc){
    foreach ($fc['elements'] as $k=>$f) {
        if ($f['hidden'] == 1) {
            unset($vars['fichefields'][$kc]['elements'][$k]);
            continue;
        }
        if ($f['type'] == 'fkey' && $f['card'] == 'short') {
            $vars['fichefields'][$kc]['elements'][$k]['link'] = Sys::getMenu($f['objectModule'] . '/' . $f['objectName']);
        }
    }
    if(!count($vars['fichefields'][$kc]['elements'])) unset($vars['fichefields'][$kc]);
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

?>