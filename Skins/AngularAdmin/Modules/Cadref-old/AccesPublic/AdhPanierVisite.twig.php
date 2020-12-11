<?php
$vars['Annee'] = Cadref::$Annee;
$vars['Annees'] = Cadref::$Annee.'-'.(Cadref::$Annee+1);
$vars['UTL'] = Cadref::$UTL;
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Adherent';
$vars['identifier'] = $vars['module'].$vars['objecttype'];
$o = genericClass::createInstance($vars['module'],$vars['objecttype']);
$temp = $o->getElementsByAttribute('','',true);
$fields = Array();
foreach ($temp as $k=>$field){
    if($info['TypeSearch'] == 'Direct' && ($field['type'] == 'metak' || $field['type'] == 'metad' || $field['type'] == 'metat' || $field['name'] == 'ImgMeta'  )){
        continue;
    }
    if (isset($field['query'])&&!empty($field['query'])){
        $t = explode('::',$field["query"]);
        if (sizeof($t)==2)$t[2] = $t[1];
        $q = explode('/',$t[0],2);
        $vals = Sys::getData($q[0],$q[1]);
        $field['query'] = array();
        foreach ($vals as $v) {
            $field['query'][$v->{$t[1]}] = $v->{$t[2]};
        }
    }
    if(isset($field['help']) && $field['help']){
        $field['helpLang'] = strtoupper("__".$info["Module"]."_".$info['ObjectType']."_".$vars['formfields'][$k]['name']."_HELP__");
    }
	$fields[$field['name']] = $field;
}
$vars['fields'] = $fields;

$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
$vars["CurrentObj"] = $o;
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
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

$vars['browseable'] = $vars["ObjectClass"]->browseable;
$vars['CurrentObjQuery'] = $vars['Path'];

?>
