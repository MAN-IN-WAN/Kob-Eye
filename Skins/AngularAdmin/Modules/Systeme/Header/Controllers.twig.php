<?php

$vars["controllers"] = array();
foreach (Sys::$User->Menus as $m){
    //test de l'existence d'une surcharge de controller
    if (isset($m->Menus)&&sizeof($m->Menus)){
        foreach ($m->Menus as $menu) {
            $info = Info::getInfos($menu->Alias);
            if (!isset($info['ObjectType'])) {
                $tab = explode('/', $info['Query']);
                array_push($tab, 'Controllers');
            } else {
                $tab = array($info['Module'], $info['ObjectType'], 'Controllers');
            }
            $blinfo = Bloc::lookForInterface($tab, 'Skins/AngularAdmin/Modules', true);
            $tmp = array();

            if (!empty($blinfo)) {
                //surcharge de controller
                if (!isset($info['ObjectType'])) {
                    $tmp['overload'] = $info['Query'] . '/Controllers?Chemin=' . $info['Query'] . '/Controllers&Url=' . $m->Url . $menu->Url;
					$tmp['identifier'] = $m->Url . $menu->Url;
                } else {
                    $tmp['overload'] = $info['Module'] . '/' . $info['ObjectType'] . '/Controllers?Chemin=' . $info['Module'] . '/' . $info['ObjectType'] . '/Controllers&Url=' . $m->Url . $menu->Url;
					$tmp['identifier'] = $info['Module'] . $info['ObjectType'];
				}
                
                $vars["controllers"][$tmp['identifier']] = $tmp;
            } else if ($info['TypeSearch'] == "Child") {

                $tmp['identifier'] = $info['Module'] . $info['ObjectType'];
                $tmp['store'] = true;
                $tmp['name'] = $m->Url . $menu->Url;
                $tmp['url'] = $m->Url . '/' . $menu->Url;
                $tmp['module'] = $info['Module'];
                $tmp['objecttype'] = $info['ObjectType'];
                $o = genericClass::createInstance($info['Module'], $info['ObjectType']);
                $obj = $o->getObjectClass();
                $tmp['description'] = $o->getDescription();
                $tmp['Interfaces'] = $obj->getInterfaces();
                $tmp['filters'] = $o->getCustomFilters();
                $tmp['childrenelements'] = $obj->getChildElements();
                $tmp['searchOrders'] = $o->getSearchOrder();
                foreach($tmp['searchOrders'] as $so){
                    if(isset($so['list']) && $so['list'] == 1) {
                        $tmp['listSO'] = $so;
                        break;
                    }
                }
                if(!isset($tmp['listSO'])) $tmp['listSO'] = $tmp['searchOrders'][0];


                for ($i = 0; $i < sizeof($tmp['childrenelements']); $i++) {
                    $child = $tmp['childrenelements'][$i];
                        //test role                                                             //test hidden                                                  //test admin
                    if (((isset($child['hasRole'])&&!Sys::$User->hasRole($child['hasRole'])) || isset($child['childrenHidden']) || isset($child['hidden'])) && ( is_object(Sys::$CurrentMenu) || !Sys::$User->Admin) ){
                        $tmp['childrenelements'][$i]=null;
                        continue;
                    }



                    //if (!isset($tmp['childrenelements'][$i]['form'])) unset($tmp['childrenelements'][$i]);
                    //recherche des parents de l'element
                    $co = genericClass::createInstance($tmp['childrenelements'][$i]['objectModule'], $tmp['childrenelements'][$i]['objectName']);
                    $cobj = $co->getObjectClass();
                    if ($cobj->isReflexive())
                        $tmp['childrenelements'][$i]['reflexive'] = true;
                    $tmp['childrenelements'][$i]['parentelements'] = $cobj->getParentElements();
                    for ($j = 0; $j < sizeof($tmp['childrenelements'][$i]['parentelements']); $j++) {
                        //print_r($tmp['childrenelements'][$i]['parentelements'][$j]);
                        if (!isset($tmp['childrenelements'][$i]['parentelements'][$j]['form'])) unset($tmp['childrenelements'][$i]['parentelements'][$j]);
                    }
                    $tmp['childrenelements'][$i]['childrenelements'] = $cobj->getChildElements();
                    for ($j = 0; $j < sizeof($tmp['childrenelements'][$i]['childrenelements']); $j++) {
                        //print_r($tmp['childrenelements'][$i]['childrenelements'][$j]);
                        if (!isset($tmp['childrenelements'][$i]['childrenelements'][$j]['listParent'])) unset($tmp['childrenelements'][$i]['childrenelements'][$j]);
                    }
                }

                foreach($tmp['childrenelements'] as $k=>$ce){
                    if($ce===null) unset($tmp['childrenelements'][$k]);
                }


                $tmp['parentelements'] = $obj->getParentElements();


                if($info['NbHisto'] > 1){
                    $tmp['initQuery'] = $info['Query'];
                    if(!empty($info['LastDirect'])){
                        $tmp['initParent'] = $info['ObjectType'];
                        $tmpVals = explode('/',$info['LastDirect'],2);
                        $tmpParent = Sys::getOneData($tmpVals[0],$tmpVals[1]);
                        $tmp['initParentId'] = $tmpParent->Id;
                        foreach($tmp['parentelements'] as $padre){
                            if($padre['objectModule'] == $tmpParent->Module && $padre['objectName'] == $tmpParent->ObjectType){
                                $tmp['initParent'] .= $padre['field'];
                                break;
                            }
                        }
                    }
                }

                $vars["controllers"][$tmp['identifier']] = $tmp;
            }


            if (!isset($info['ObjectType'])) {
                $tab = explode('/', $info['Query']);
                array_push($tab, 'ControllersExtends');
            } else {
                $tab = array($info['Module'], $info['ObjectType'], 'ControllersExtends');
            }
            $blinfo = Bloc::lookForInterface($tab, 'Skins/AngularAdmin/Modules', true);
            if (!empty($blinfo)) {
                if (!isset($info['ObjectType'])) {
                    $vars["controllers"][$tmp['identifier']]['extends'] = $info['Query'] . '/ControllersExtends?Chemin=' . $info['Query'] . '/Controllers&Url=' . $m->Url . $menu->Url;
                } else {
                    $vars["controllers"][$tmp['identifier']]['extends'] = $info['Module'] . '/' . $info['ObjectType'] . '/ControllersExtends?Chemin=' . $info['Module'] . '/' . $info['ObjectType'] . '/Controllers&Url=' . $m->Url . $menu->Url;
                }
            }
            if($obj){
                $vars["controllers"][$tmp['identifier']]['browseable'] = $obj->browseable;
            }else{
                $vars["controllers"][$tmp['identifier']]['browseable'] = false;
            }
        }
    }else{
        //controller tableau de bord ou page single
        $tmp = array();
        $info = Info::getInfos($m->Alias);

        if(!isset($info['ObjectType'])) {
            $tab = explode('/',$info['Query']);
            array_push($tab,'Controllers');
        }else{
            $tab = array($info['Module'], $info['ObjectType'],'Controllers');
        }
        $blinfo = Bloc::lookForInterface($tab,'Skins/AngularAdmin/Modules',true);

        if (!empty($blinfo)){
            //surcharge de controller
            if(!isset($info['ObjectType'])) {
                $tmp['overload'] = $info['Query'].'/Controllers?Chemin='.$info['Query'].'/ControllersExtends&Url='.$m->Url;
            }else{
                $tmp['overload'] = $info['Module'].'/'.$info['ObjectType'].'/Controllers?Chemin='.$info['Module'].'/'.$info['ObjectType'].'/ControllersExtends&Url='.$m->Url;
            }

            $tmp['identifier'] = $m->Url;
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

        if(!isset($info['ObjectType'])) {
            $tab = explode('/',$info['Query']);
            array_push($tab,'ControllersExtends');
        }else{
            $tab = array($info['Module'], $info['ObjectType'],'ControllersExtends');
        }
        $blinfo = Bloc::lookForInterface($tab,'Skins/AngularAdmin/Modules',true);
        if (!empty($blinfo)) {
            if(!isset($info['ObjectType'])) {
                $vars["controllers"][$tmp['identifier']]['extends'] = $info['Query'].'/ControllersExtends?Chemin='.$info['Query'].'/ControllersExtends&Url='.$m->Url.$menu->Url;
            }else{
                $vars["controllers"][$tmp['identifier']]['extends'] = $info['Module'].'/'.$info['ObjectType'].'/ControllersExtends?Chemin='.$info['Module'].'/'.$info['ObjectType'].'/ControllersExtends&Url='.$m->Url.$menu->Url;
            }
        }
    }

}


if (Sys::$User->Admin) {
    $modules = Sys::$Modules;
    foreach ($modules as $mod) {
        foreach ($mod->getObjectClass() as $o) {
            $tmp = array();
            $tmp['identifier'] = $mod->Nom . $o->titre ;

            $tmp['name'] = $mod->Nom . $o->titre;
            $tmp['module'] = $mod->Nom;
            $tmp['objecttype'] = $o->titre;
            $tmp['store'] = true;
            $o = genericClass::createInstance($mod->Nom, $o->titre);
            $obj = $o->getObjectClass();
            $tmp['description'] = $o->getDescription();
            $tmp['childrenelements'] = $obj->getChildElements();
            for ($i = 0; $i < sizeof($tmp['childrenelements']); $i++) {
                //if (!isset($tmp['childrenelements'][$i]['form'])) unset($tmp['childrenelements'][$i]);
                //recherche des parents de l'element
                $co = genericClass::createInstance($tmp['childrenelements'][$i]['objectModule'], $tmp['childrenelements'][$i]['objectName']);
                $cobj = $co->getObjectClass();
                if ($cobj->isReflexive())
                    $tmp['childrenelements'][$i]['reflexive'] = true;
                $tmp['childrenelements'][$i]['parentelements'] = $cobj->getParentElements();
                for ($j = 0; $j < sizeof($tmp['childrenelements'][$i]['parentelements']); $j++) {
                    //print_r($tmp['childrenelements'][$i]['parentelements'][$j]);
                    if (!isset($tmp['childrenelements'][$i]['parentelements'][$j]['form'])) unset($tmp['childrenelements'][$i]['parentelements'][$j]);
                }
            }
            $tmp['parentelements'] = $obj->getParentElements();
            $vars["controllers"][$tmp['identifier'].'Admin'] = $tmp;
        }
    }
}




?>