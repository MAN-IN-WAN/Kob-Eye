<?php
$stores = array();
$children = array();
foreach (Sys::$Modules as $k=>$mod){
    foreach ($mod->getObjectClass() as $ap){
        $tmp['identifier'] = $mod->Nom . $ap->titre;
        $tmp['module'] = $mod->Nom;
        $tmp['objecttype'] = $ap->titre;
        $o = genericClass::createInstance($mod->Nom, $ap->titre);
        $obj = $o->getObjectClass();
        $formfields = $o->getElementsByAttribute('form','',true);
        $out = array();
        if (is_array($formfields))foreach ($formfields as $f){
            if (isset( $o->{$f['name']}))
                $out[$f['name']] = $o->{$f['name']};
        }
        $tmp['newData'] = json_encode($out);
        $tmp['childrenelements'] = array();
        foreach ($obj->getChildElements() as $sub){
            if ($sub['objectModule'] . $sub['objectName']!=$tmp['identifier']) {
                $tmpchildren = $sub;
                $tmpchildren['identifier'] = $sub['objectModule'] . $sub['objectName'];
                $tmp['childrenelements'][] = $tmpchildren;
            }
        }
        $stores[$tmp['identifier'].'Store'] = $tmp;
/*        foreach ($obj->getChildElements() as $sub){
            if (in_array($children,$sub['objectModule'] . $sub['objectName'])) continue;
            $children = array_push($children,$sub['objectModule'] . $sub['objectName']);
            $tmp['identifier'] = $sub['objectModule'] . $sub['objectName'];
            $tmp['module'] = $sub['objectModule'];
            $tmp['objecttype'] = $sub['objectName'];
            $o = genericClass::createInstance($sub['objectModule'] , $sub['objectName']);
            $obj = $o->getObjectClass();
            $tmp['childrenelements'] = $obj->getChildElements();
            $stores[$tmp['identifier'].'StoreChild'] = $tmp;
        }*/
    }
}
$vars['stores'] = $stores;
?>