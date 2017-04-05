<?php

$vars["controllers"] = array();
foreach (Sys::$User->Menus as $m){
    if (isset($m->Menus))foreach ($m->Menus as $menu) {
        $tmp = array();
        $info = Info::getInfos($menu->Alias);
        if ($info['TypeSearch']=="Child") {
            $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
            $tmp['name'] = $m->Url . $menu->Url;
            $tmp['module'] = $info['Module'];
            $tmp['objecttype'] = $info['ObjectType'];
            $o = genericClass::createInstance($info['Module'], $info['ObjectType']);
            $obj = $o->getObjectClass();
            $tmp['childrenelements'] = $obj->getChildElements();
            $vars["controllers"][$tmp['identifier']] = $tmp;
        }
    }
}
if (Sys::$User->Admin) {
    foreach ($modules as $mod) {
        foreach ($mod->getAccessPoint() as $ap) {
            $tmp = array();
            $info = Info::getInfos($menu->Alias);
            if ($info['TypeSearch']=="Child") {
                $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
                $tmp['name'] = $info['Module'] . $info['ObjectType'];
                $tmp['module'] = $info['Module'];
                $tmp['objecttype'] = $info['ObjectType'];
                $o = genericClass::createInstance($info['Module'], $info['ObjectType']);
                $obj = $o->getObjectClass();
                $tmp['childrenelements'] = $obj->getChildElements();
                $vars["controllers"][$tmp['identifier']] = $tmp;
            }
        }
    }
}

?>