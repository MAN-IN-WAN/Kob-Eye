<?php
$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$info = Info::getInfos($query);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['formfields'] = $o->getElementsByAttribute('form','',true);
foreach ($vars['formfields'] as $k=>$field){
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
}
$vars["CurrentObj"] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars["Module"] = $info["Module"];
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
?>