<?php

$vars["controllers"] = array();
foreach (Sys::$User->Menus as $m){
    //test de l'existence d'une surcharge de controller
    if (isset($m->Menus)&&sizeof($m->Menus))foreach ($m->Menus as $menu) {
        $info = Info::getInfos($menu->Alias);
        $tab = array($info['Module'], $info['ObjectType'],'Controllers');
        $blinfo = Bloc::lookForInterface($tab,'Skins/AngularAdmin/Modules',true);
        $tmp = array();
        if (!empty($blinfo)){
            //surcharge de controller
            $tmp['overload'] = $info['Module'].'/'.$info['ObjectType'].'/Controllers?Chemin='.$info['Module'].'/'.$info['ObjectType'].'/Controllers&Url='.$m->Url . $menu->Url;
            $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
            $vars["controllers"][$tmp['identifier']] = $tmp;
        }else if ($info['TypeSearch']=="Child") {
            $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
            $tmp['store'] = true;
            $tmp['name'] = $m->Url . $menu->Url;
            $tmp['module'] = $info['Module'];
            $tmp['objecttype'] = $info['ObjectType'];
            $o = genericClass::createInstance($info['Module'], $info['ObjectType']);
            $obj = $o->getObjectClass();
            $tmp['description'] = $o->getDescription();
            $tmp['childrenelements'] = $obj->getChildElements();
            //for ($i=0; $i<sizeof($tmp['childrenelements']);$i++)if (!isset($tmp['childrenelements'][$i]['form'])) unset($tmp['childrenelements'][$i]);
            $tmp['parentelements'] = $obj->getParentElements();
            $vars["controllers"][$tmp['identifier']] = $tmp;
        }
    }else{
        //controller tableau de bord ou page single
        $tmp = array();
        $info = Info::getInfos($m->Alias);
        if(!isset($info['ObjectType'])) {
            $tab = array($info['Query'], 'Controllers');
        }else{
            $tab = array($info['Module'], $info['ObjectType'],'Controllers');
        }

        $blinfo = Bloc::lookForInterface($tab,'Skins/AngularAdmin/Modules',true);
        if (!empty($blinfo)){
            //surcharge de controller
            $tmp['overload'] = $info['Module'].'/'.$info['ObjectType'].'/Controllers?Chemin='.$info['Module'].'/'.$info['ObjectType'].'/Controllers&Url='.$m->Url;
            $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
            $vars["controllers"][$tmp['identifier']] = $tmp;
        }else{
            $tmp['identifier'] = $m->Url;
            $tmp['store'] = false;
            $tmp['name'] = $m->Url ;
            $tmp['module'] = $info['Module'];
            if(isset($info['ObjectType']))
                $tmp['objecttype'] = $info['ObjectType'];
            /*$o = genericClass::createInstance($info['Module'], $info['ObjectType']);
            $obj = $o->getObjectClass();
            $tmp['description'] = $o->getDescription();*/
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
                $tmp['description'] = $o->getDescription();
                $tmp['childrenelements'] = $obj->getChildElements();
                $vars["controllers"][$tmp['identifier']] = $tmp;
            }
        }
    }
}

?>