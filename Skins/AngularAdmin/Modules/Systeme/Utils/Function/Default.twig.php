<?php
/**
 * TUTO !!!
 *
 *
 * Pour envoyer au step 2
 * return array('template'=>"Clone",'step'=>1,'callNext'=>array('nom'=>'cloneHost','title'=>'Progression'));
 *
 * Pour envoyer une barre de progression
 * return array('task'=>$task,'title'=>'Progression du clonage');
 *
 *
 */
    $data = json_decode(file_get_contents('php://input'),true);
    $funcCall =  json_decode($data['Func'],true);
    $path = $funcCall['query'];
    $name = $funcCall['name'];

    $info = Info::getInfos($path);

    $vars['toCall'] = false;
    $vars['Path'] = $path;
    $vars['Query'] = $path;
    $vars['toReturn'] = $funcCall;
    $vars['params'] = $funcCall['args'];
    $vars['params']['identifier'] = $info['Module'].$info['ObjectType'];
    $vars['toReturn']['args'] = array();

    //Fonction de chargement des fichier template pour retrocompat/wizardstyle
    $loadTemp = function($blinfo,$path) use (&$vars){
        //Suppression de l'extension
        $file = $blinfo;
        $blinfo = explode('.',$blinfo);
        $ext = array_pop($blinfo);
        $blinfo = implode('.',$blinfo);
        $blinfo = explode('Modules/',$blinfo);
        $blinfo = end($blinfo);

        if($ext == 'md'){ //Si c'est un md on a déjà tout interprété
            $params = '?Query='.$path;
            foreach($vars['params'] as $key=>$param){
                $params .= '&'.urlencode($key).'='.urlencode(json_encode($key));
            }
            $temp = KeTwig::callModule($blinfo.$params);
        } else{
            $params = array('Query'=>$path);
            $params = array_merge($params,$vars['params']);
            KeTwig::loadTemplate($file);
            $temp = KeTwig::render($file,$params);
        }
        $vars['toReturn']['data'] = $temp;
        $vars['toReturn']['success'] = true;
    };

    if(!isset($info['Functions'][$name]) /*|| !is_object(Sys::$CurrentMenu)*/){
        $error = "La fonction que vous essayez d'exectuer n'est pas accessible pour l'objet souhaité";
        $vars['toReturn']['errors'][] = array("Message"=>$error);
    } else{
        //On vérifie si il n'y a pas une fonction old style pour rétrocompat
        if (!isset($info['ObjectType'])) {
            $tab = explode('/', $info['Query']);
            array_push($tab, $name);
        } else {
            $tab = array($info['Module'], $info['ObjectType'], $name);
        }
        $blinfo = Bloc::getInterface($tab[0], $tab[1], $tab[2]);
        if($blinfo &&  (!strpos($blinfo,'Default.md') && !strpos($blinfo,'Default.twig'))) {
            //Si retrocompat on affiche
            $loadTemp($blinfo,$path);
        } else {
            //Si pas de retro compat on vérifie que l'objet ai une method portant ce nom
            if($info['TypeSearch'] == 'Indirect' || ($info['TypeSearch'] = 'Direct' && empty($info['LastId']))){
                $obj = genericClass::createInstance($info['Module'],$info['ObjectType']);
            } else{
                $obj = Sys::getOneData($info['Module'],explode('/',$path,2)[1]);
            }

            $methods = get_class_methods($obj);

            if(!in_array($name,$methods)){
                $error = "La fonction que vous essayez d'exectuer n'est pas définie pour l'objet souhaité";
                $vars['toReturn']['errors'][] = array("Message"=>$error);
            } else {
                $temp = $obj->{$name}($vars['params']);
                if(is_array($temp)){
                    if(isset($temp['template'])){
                        if(isset($temp['funcTempVars'])) {
                            $vars['params']['funcTempVars'] = $temp['funcTempVars']; //Si on souhaite passer des variables retournées pas la fonction au template.
                        }
                        $tabNext = $tab;
                        $tabNext[2] = $temp['template'];
                        $blinfoNext = Bloc::getInterface($tabNext[0], $tabNext[1], $tabNext[2]);
                        if($blinfoNext &&  (!strpos($blinfoNext,'Default.md') && !strpos($blinfoNext,'Default.twig'))) {
                            $loadTemp($blinfoNext,$path);
							if(isset($temp['step']))
								$vars['toReturn']['data'].='<input type="hidden" ng-init="'.$info['Module'].$info['ObjectType'].'function.args.step='.$temp['step'].'">';
                        } else{
                            $vars['toReturn']['data'] = "Template non trouvé";
                        }
                    }
                    //callbackJs
                    if(isset($temp['callBack'])){
                        $vars['toReturn']['callBack'] = $temp['callBack'];
                    }
                    //callbackPhp
                    if(isset($temp['callNext'])){
                        $vars['toReturn']['callNext'] = $temp['callNext'];
                    }
                    //Arguments pour callNext
					if(isset($temp['args'])){
                        $vars['toReturn']['args'] = $temp['args'];
                    }
					//Data a afficher (Prioritaire sur un eventuel template)
                    if(isset($temp['data'])){
                        $vars['toReturn']['data'] = $temp['data'];
                    }
                    //Affichage de task le cas echeant
                    if(isset($temp['task'])){
                        $path = $temp['task']->Module.'/'. $temp['task']->ObjectType.'/'. $temp['task']->Id.'/Activity';
                        $loadTemp('Skins/AngularAdmin/Modules/Systeme/Utils/Function/Tasks.twig',$path);
                    }
                    //gestion des erreurs
                    if(isset($temp['errors'])) {
                        $vars['toReturn']['errors'] = $temp['errors'];
                    }
                    //gestion des erreurs
                    if(isset($temp['infos'])) {
                        $vars['toReturn']['infos'] = $temp['infos'];
                    }
                } else{
                    $vars['toReturn']['data'] = $temp;
                }

                if(isset($vars['toReturn']['errors'])){
                    $vars['toReturn']['success'] = false;
                } else {
                    $vars['toReturn']['success'] = true;
                }

            }
        }
    }


    $vars['toReturn'] = json_encode($vars['toReturn']);



