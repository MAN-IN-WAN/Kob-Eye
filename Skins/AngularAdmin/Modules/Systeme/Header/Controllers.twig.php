<?php

$vars["controllers"] = array();
foreach (Sys::$User->Menus as $m){
    //test de l'existence d'une surcharge de controller
    if (isset($m->Menus)&&sizeof($m->Menus))foreach ($m->Menus as $menu) {
        $info = Info::getInfos($menu->Alias);
        //$tab = array($info['Module'], $info['ObjectType'],'Controllers');
        /*$blinfo = Bloc::lookForInterface($tab,'Skins/AngularAdmin',true);
        print_r($tab);
        print_r($blinfo);
        die('test');
        /*if (){
            //surcharge de controller

            die('ok interface');
        }*/
            $tmp = array();
        if ($info['TypeSearch']=="Child") {
            $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
            $tmp['store'] = true;
            $tmp['name'] = $m->Url . $menu->Url;
            $tmp['module'] = $info['Module'];
            $tmp['objecttype'] = $info['ObjectType'];
            $o = genericClass::createInstance($info['Module'], $info['ObjectType']);
            $obj = $o->getObjectClass();
            $tmp['description'] = $o->getDescription();
            $tmp['childrenelements'] = $obj->getChildElements();
            $tmp['parentelements'] = $obj->getParentElements();
            $vars["controllers"][$tmp['identifier']] = $tmp;
        }
    }else{
        //controller tableau de bord ou page single
        $tmp = array();
        $info = Info::getInfos($m->Alias);
        $tmp['identifier'] = $m->Url;
        $tmp['store'] = false;
        $tmp['name'] = $m->Url ;
        $tmp['module'] = $info['Module'];
        $tmp['objecttype'] = $info['ObjectType'];
        /*$o = genericClass::createInstance($info['Module'], $info['ObjectType']);
        $obj = $o->getObjectClass();
        $tmp['description'] = $o->getDescription();*/
        $vars["controllers"][$tmp['identifier']] = $tmp;
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
                $tmp['description'] = $o->getDescription();
                $tmp['childrenelements'] = $obj->getChildElements();
                $vars["controllers"][$tmp['identifier']] = $tmp;
            }
        }
    }
}

?>