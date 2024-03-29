<?php
session_write_close();
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
//$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['fichefields'] = $o->getElementsByAttribute('fiche','',true);
if (!is_object(Sys::$CurrentMenu) && Sys::$User->Admin){
    $vars['fichefields'] = $o->getElementsByAttribute('','',true);
}

foreach ($vars['fichefields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fichefields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);

        if ($vars['fichefields'][$k]['link']==$f['objectModule'].'/'.$f['objectName'])
            $vars['fichefields'][$k]['link'] = false;

        if($f['objectName'] == 'Contact')
            $vars['contactLink'] = $vars['fichefields'][$k]['link'];
        if($f['objectName'] == 'Contrat')
            $vars['contratLink'] = $vars['fichefields'][$k]['link'];
    }
}
$vars['fields'] = $vars['fichefields'];


$vars['formfields'] = $o->getElementsByAttribute('form','',true);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars["CurrentObj"] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars['operation'] = $vars['ObjectClass']->getOperations();
foreach($vars['operation'] as $k=>$op){
    if(is_array($op)){
        $ok = false;
        foreach ($op as $r){
            if(Sys::$User->isRole($r)){
                $ok = true;
                break;
            }
        }
        $vars['operation'][$k] = $ok;
    }
}
$vars['functions'] = $o->getFunctions();
foreach($vars['functions'] as $k=>$f){
    if(empty($vars['operation'][$f['Nom']]))
        unset($vars['functions'][$k]);
}
$vars['functions'] = array_values($vars['functions']);

$childs = $vars["ObjectClass"]->getChildElements();
$vars["ChildrenElements"] = array();

foreach ($childs as $child){
    if (
        //test role
         ((!isset($child['hasRole'])||Sys::$User->hasRole($child['hasRole']))&&
         //test hidden
        !isset($child['childrenHidden'])&&!isset($child['hidden']))
         //test admin
         || (!is_object(Sys::$CurrentMenu) && Sys::$User->Admin))
            array_push($vars["ChildrenElements"],$child);
}
$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
$vars['identifier'] = $info['Module'] . $info['ObjectType'];
if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];


$vars['browseable'] = $vars["ObjectClass"]->browseable;
$vars['CurrentObjQuery'] = $vars['Path'];

$vars['User'] = Sys::$User;


$vars['Abtel'] = array_key_exists('Abtel',Sys::$Modules);
