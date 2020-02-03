<?php
session_write_close();


$o = genericClass::createInstance('Reservation','Client');
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
    }
}
$vars['fields'] = $vars['fichefields'];


//$vars['formfields'] = $o->getElementsByAttribute('form','',true);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars["CurrentObj"] = genericClass::createInstance('Reservation','Client');
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
/**
 * fields by categories
 */
$ocats = $o->getCategories();
$cats = array();
foreach ($ocats as $k=>$cat){
    $fields = [];
    foreach ($vars['fichefields'] as $field){
        if ($field['category']==$cat){
            array_push($fields,$field);
        }
    }
    if (sizeof($fields)){
        $cats[$cat] = $fields;
    }
}
$vars['categories'] = $cats;


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
if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];


$vars['browseable'] = $vars["ObjectClass"]->browseable;
$vars['CurrentObjQuery'] = 'Reservation/Client';


