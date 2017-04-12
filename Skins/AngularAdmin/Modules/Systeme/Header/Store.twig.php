<?php
$stores = array();
foreach (Sys::$User->Menus as $m){
    foreach ($m->Menus as $menu) {
        $tmp = array();
        $info = Info::getInfos($menu->Alias);
        if ($info['TypeSearch']=="Child") {
            $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
            $tmp['module'] = $info['Module'];
            $tmp['objecttype'] = $info['ObjectType'];
            $o = genericClass::createInstance($info['Module'], $info['ObjectType']);
            $obj = $o->getObjectClass();
            $tmp['childrenelements'] = $obj->getChildElements();
            $stores[$tmp['identifier'].'Store'] = $tmp;
            foreach ($obj->getChildElements() as $sub){
                $tmp['identifier'] = $sub['objectModule'] . $sub['objectName'];
                $tmp['module'] = $sub['objectModule'];
                $tmp['objecttype'] = $sub['objectName'];
                $o = genericClass::createInstance($sub['objectModule'] , $sub['objectName']);
                $obj = $o->getObjectClass();
                $tmp['childrenelements'] = $obj->getChildElements();
                $stores[$tmp['identifier'].'StoreChild'] = $tmp;
            }
        }
    }
}
if (Sys::$User->Admin){
    foreach (Sys::$Modules as $k=>$mod){
        foreach ($mod->getObjectClass() as $ap){
            $tmp['identifier'] = $mod->Nom . $ap->Nom;
            $tmp['module'] = $mod->Nom;
            $tmp['objecttype'] = $ap->Nom;
            $o = genericClass::createInstance($mod->Nom, $ap->Nom);
            $obj = $o->getObjectClass();
            $tmp['childrenelements'] = $obj->getChildElements();
            $stores[$tmp['identifier']] = $tmp;
        }
    }
}
$vars['stores'] = $stores;
?>