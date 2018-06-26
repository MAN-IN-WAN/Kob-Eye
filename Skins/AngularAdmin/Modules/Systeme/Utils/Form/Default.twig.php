<?php
$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$vars['scopeObj'] = isset($vars['scopeObj']) ? $vars['scopeObj'] : 'modalObj';
$info = Info::getInfos($query);

$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['formfields'] = $o->getElementsByAttribute('form','',true);
if (!is_object(Sys::$CurrentMenu) && Sys::$User->Admin){
    $vars['formfields'] = $o->getElementsByAttribute('','',true);
}
foreach ($vars['formfields'] as $k=>$field){
    if($info['TypeSearch'] == 'Direct' && ($field['type'] == 'metak' || $field['type'] == 'metad' || $field['type'] == 'metat' || $field['name'] == 'ImgMeta'  )){
        unset($vars['formfields'][$k]);
        continue;
    }
    if (isset($field['query'])&&!empty($field['query'])){
        $t = explode('::',$field["query"]);
        if (sizeof($t)==2)$t[2] = $t[1];
        $q = explode('/',$t[0],2);
        $vals = Sys::getData($q[0],$q[1]);
        $vars['formfields'][$k]['query'] = array();
        foreach ($vals as $v) {
            $vars['formfields'][$k]['query'][$v->{$t[1]}] = $v->{$t[2]};
        }
    }
    if(isset($field['help']) && $field['help']){
        $vars['formfields'][$k]['helpLang'] = strtoupper("__".$info["Module"]."_".$info['ObjectType']."_".$vars['formfields'][$k]['name']."_HELP__");
    }
}

$vars["CurrentObj"] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars["Module"] = $info["Module"];
$vars['ObjectType'] = $info['ObjectType'];
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
?>