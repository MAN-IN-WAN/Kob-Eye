<?php
session_write_close();
$Path = $GLOBALS["Systeme"]->getGetVars("Module").'/'.$GLOBALS["Systeme"]->getGetVars("ObjectName");

$info = Info::getInfos($Path);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'].'Link';
$vars['context'] = 'link';
$vars['ObjectClass'] = $o->getObjectClass();
$vars['ObjectType'] = $info['ObjectType'];
$vars['Module'] = $info['Module'];
$vars['functions'] = $o->getFunctions();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['searchfields'] = $o->getElementsByAttribute('searchOrder|search','',true);
$vars['ObjectType'] = $info['ObjectType'];

foreach ($vars['fields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
    }
}

foreach ($vars['searchfields'] as $k=>$f){
    if (isset($f['query'])&&!empty($f['query'])){
        $t = explode('::',$f["query"]);
        if (sizeof($t)==2)$t[2] = $t[1];
        $q = explode('/',$t[0],2);
        $vals = Sys::getData($q[0],$q[1]);
        $vars['searchfields'][$k]['query'] = array();
        foreach ($vals as $v) {
            $vars['searchfields'][$k]['query'][$v->{$t[1]}] = $v->{$t[2]};
        }
    }
    if(isset($f['help']) && $f['help']){
        $vars['searchfields'][$k]['helpLang'] = strtoupper("__".$info["Module"]."_".$info['ObjectType']."_".$vars['searchfields'][$k]['name']."_HELP__");
    }
}
$vars['filters'] = $o->getCustomFilters();
if (is_object(Sys::$CurrentMenu)) {
    if (isset($vars['Type'])&&$vars['Type']=='Children') {
        $vars['CurrentUrl'] = Sys::getMenu($info['Module'] . '/' . $info['ObjectType']);
    }else {
        $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
    }
}else $vars['CurrentUrl'] = $Path;
if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";

$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
if (isset($vars["Interfaces"]['list']))
    $vars["Interfaces"] = $vars["Interfaces"]['list'];


$childs = $vars["ObjectClass"]->getChildElements();
foreach ($childs as $child){
    //test role                                                             //test hidden                                               //test admin
    if (((!isset($child['hasRole'])||Sys::$User->hasRole($child['hasRole'])) && !isset($child['childrenHidden'])&&!isset($child['hidden'])) || (!is_object(Sys::$CurrentMenu) && Sys::$User->Admin)){
        if($child['listParent']){
            array_push($vars['fields'],$child);
        }
    }
}

?>