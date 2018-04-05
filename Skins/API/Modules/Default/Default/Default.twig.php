<?php


header("Content-type: text/json; charset=".CHARSET_CODE."");
header("Accept-Ranges:bytes");
header("Access-Control-Allow-Headers:Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token");
header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE, PATCH");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD'];
$query = $GLOBALS['Systeme']->getRegVars('Query');
$lien = $GLOBALS['Systeme']->getRegVars('Lien');

$info = Info::getInfos($query);


if(strpos( $query,$lien) == 0 || count($info['Historique']) > 2){
    sendResult(403,null,'Vous n\'avez pas le droit d\'acceder à cette ressource depuis cette route.');
    die();
}

//Recup des données
$data = array();

$request = array();
parse_str(file_get_contents("php://input"),$request);

$data = json_decode($request['params'],true);






switch($info['TypeSearch']){
    case 'Interface':
        switch($method){
            case 'GET':
                //On retourne la liste des types objets enfant
                //TODO
                break;
            case 'POST':
                sendResult(405);
                break;
            case 'PUT':
                sendResult(405);
                break;
            case 'DELETE':
                sendResult(405);
                break;
            case 'PATCH':
                sendResult(405);
                break;
        }
        break;

    case 'Child' :
        //Verif de la validité des datas envoyés
        $generic = genericClass::createInstance($info['Module'],$info['ObjectType']);
        $props = $generic->getElementsByAttribute('','',true);
        array_walk($props,function (&$i){
            $i=$i['name'];
        });
        $offset = '';


        if(isset($data['offset'])){
            $offset = $data['offset'];
            unset($data['offset']);
        }
        $limit = '';
        if(isset($data['limit'])){
            $limit = $data['limit'];
            unset($data['limit']);
        }
        $newProps = array();
        if(isset($data['data'])){
            $newProps = $data['data'];
            unset($data['data']);
        }
        $orderVar = '';
        if(isset($data['orderBy'])){
            $orderVar = $data['orderBy'];
            unset($data['orderBy']);
        }
        $orderType = '';
        if(isset($data['order'])){
            $orderType = $data['order'];
            unset($data['order']);
        }
        foreach ($data as $k=>$d){
            if(!in_array($k,$props)){
                sendResult(400,null,'Paramètre invalide : '.$k);
                break 2;
            }
        }
        foreach ($newProps as $k=>$n){
            //TODO : check validité type props
            if(!in_array($k,$props)){
                sendResult(400,null,'Propriété invalide : '.$k);
                break 2;
            }
        }

        switch($method){
            case 'GET':
                $req = explode('/',$info['Query'],2);
                $req = $req[1].'/';
                if($info['Reflexive']) $req .= '*/';
                foreach($data as $k=>$d){
                    $req .= '&'.$k.'='.$d;
                }
                $total = Sys::getCount($info['Module'],$req);
                $items = Sys::getData($info['Module'],$req,$offset,$limit,$orderType,$orderVar);
                //On retourne la liste de ces objets
                sendResult(206,$items,array('offset'=>$offset,'limit'=>$limit, 'total'=>$total));
                break;
            case 'POST':
                if(count($info['Historique'])>=2){
                    sendResult(403,null,'Vous n\'avez pas le droit de créer cette entité depuis cette route.');
                    die();
                }

                $parent = false;
                $item = $generic;
                $tempLegacy = explode('/',$info['LastDirect'],2);
                if(isset($tempLegacy[1]) && $tempLegacy[1] != ''){
                    $parent = Sys::getOneData($tempLegacy[0],$tempLegacy[1]);
                    $item->addParent($parent);
                }
                foreach ($info['typesParent'] as $tp){
                    if(isset($newProps[$tp['Nom']])){
                        if(!is_array($newProps[$tp['Nom']]))
                            $newProps[$tp['Nom']] = array($newProps[$tp['Nom']]);

                        foreach($newProps[$tp['Nom']] as $par){
                            $pmodule =  isset($tp['Module']) ? $tp['Module'] : $info['Module'];
                            $pobjectType = $tp['Titre'];
                            $pid = $par;

                            $item->addParent($pmodule.'/'.$pobjectType.'/'.$pid);
                        }
                    }
                }

                foreach($newProps as $k=>$n){
                    $item->Set($k,$n);
                }
                //On crée l'objet
                if($item->Verify()){
                    $item->Save();
                    sendResult(201,$item);
                } else{
                    sendResult(400,$item,$item->Error);
                }

                break;
            case 'PUT':
                //TODO definir si l'on peut put un array de child
                sendResult(405);
                break;
            case 'DELETE':
                //TODO definir si l'on peut del un array de child
                sendResult(405);
                break;
            case 'PATCH':
                //TODO definir si l'on peut patch un array de child
                sendResult(405);
                break;
        }
        break;

    case 'Direct':
        //Verif de la validité des datas envoyés
        $generic = genericClass::createInstance($info['Module'],$info['ObjectType']);
        $props = $generic->getProperties();
        array_walk($props,function (&$i){
            $i=$i['name'];
        });        $offset = '';
        if(isset($data['offset'])){
            $offset = $data['offset'];
            unset($data['offset']);
        }
        $limit = '';
        if(isset($data['limit'])){
            $limit = $data['limit'];
            unset($data['limit']);
        }
        $newProps = array();
        if(isset($data['data'])){
            $newProps = $data['data'];
            unset($data['data']);
        }
        $orderVar = '';
        if(isset($data['orderBy'])){
            $orderVar = $data['orderBy'];
            unset($data['orderBy']);
        }
        $orderType = '';
        if(isset($data['order'])){
            $orderType = $data['order'];
            unset($data['order']);
        }
        foreach ($data as $k=>$d){
            if(!in_array($k,$props)){
                sendResult(400,null,'Paramètre invalide : '.$k);
                break 2;
            }
        }
        foreach ($newProps as $k=>$n){
            //TODO : check validité type props
            if(!in_array($k,$props)){
                sendResult(400,null,'Propriété invalide : '.$k);
                break 2;
            }
        }



        if($method != 'POST'){
            $req = explode('/',$info['Query'],2);
            $req = $req[1];
            foreach($data as $k=>$d){
                $req .= '&'.$k.'='.$d;
            }
            $item = Sys::getOneData($info['Module'],$req);
            if(!$item) {
                sendResult(404);
                break;
            }

        }

        switch($method){
            case 'GET':
                //On retourne l'objet fourni par l'id
                sendResult(200,$item);
                break;
            case 'POST':
                //TODO definir si l'on peut modif obj avec post
                sendResult(405);
                break;
            case 'PUT':
                //TODO définir si on autorise la creation d'objet en PUT
                //On remplace l'objet ou on le crée
                if(count($info['Historique'])>=2){
                    sendResult(403,null,'Vous n\'avez pas le droit de créer/modifier cette entité depuis cette route.');
                    die();
                }

                $parent = false;
                $item = $generic;
                $tempLegacy = explode('/',$info['LastDirect'],2);
                if(isset($tempLegacy[1]) && $tempLegacy[1] != ''){
                    $parent = Sys::getOneData($tempLegacy[0],$tempLegacy[1]);
                    $item->addParent($parent);
                }
                foreach ($info['typesParent'] as $tp){
                    if(isset($newProps[$tp['Nom']])){
                        if(!is_array($newProps[$tp['Nom']]))
                            $newProps[$tp['Nom']] = array($newProps[$tp['Nom']]);

                        foreach($newProps[$tp['Nom']] as $par){
                            $pmodule =  isset($tp['Module']) ? $tp['Module'] : $info['Module'];
                            $pobjectType = $tp['Titre'];
                            $pid = $par;
                            $item->addParent($pmodule.'/'.$pobjectType.'/'.$pid);
                        }
                    }
                }
                $item->Set('Id',$info['LastId']);
                foreach($newProps as $k=>$n){
                    $item->Set($k,$n);
                }
                //On crée l'objet
                if($item->Verify()){
                    if($parent) $item->addParent($parent);
                    $item->Save();
                    sendResult(201,$item);
                } else{
                    sendResult(400,$item,$item->Error);
                }
                break;
            case 'DELETE':
                //On supprime l'objet
                if(count($info['Historique'])>=2){
                    sendResult(403,null,'Vous n\'avez pas le droit de modifier cette entité depuis cette route.');
                    die();
                }
                $item->Delete();
                sendResult(204);
                break;
            case 'PATCH':
                //On modifie l'objet
                if(count($info['Historique'])>=2){
                    sendResult(403,null,'Vous n\'avez pas le droit de modifier cette entité depuis cette route.');
                    die();
                }
                $parent = false;
                $item = $generic;
                $tempLegacy = explode('/',$info['LastDirect'],2);
                if(isset($tempLegacy[1]) && $tempLegacy[1] != ''){
                    $parent = Sys::getOneData($tempLegacy[0],$tempLegacy[1]);
                    $item->addParent($parent);
                }
                foreach ($info['typesParent'] as $tp){
                    if(isset($newProps[$tp['Nom']])){
                        if(!is_array($newProps[$tp['Nom']]))
                            $newProps[$tp['Nom']] = array($newProps[$tp['Nom']]);

                        foreach($newProps[$tp['Nom']] as $par){
                            $pmodule =  isset($tp['Module']) ? $tp['Module'] : $info['Module'];
                            $pobjectType = $tp['Titre'];
                            $pid = $par;
                            $item->addParent($pmodule.'/'.$pobjectType.'/'.$pid);
                        }
                    }
                }
                $item->Set('Id',$info['LastId']);
                foreach($newProps as $k=>$n){
                    $item->Set($k,$n);
                }
                //On crée l'objet
                if($item->Verify()){
                    if($parent) $item->addParent($parent);
                    $item->Save();
                    sendResult(201,$item);
                } else{
                    sendResult(400,$item,$item->Error);
                }


                break;
        }
        break;

    default :
        //TODO ERROR unknown
        return false;

}


//Get Interfce child Type List
function getChildType(){

}

//Get child List
function getChildList(){

}

//Create new object
function postNewObject(){

}

//Get object defined by url
function getObject(){

}

//Create or replace object
function putObject(){

}

//Delete object
function delObject(){

}

//update object
function patchObject(){

}

//Send the result json
function sendResult($code,$obj=null,$more=null){
    setcookie(PHP_SESSION_NAME, '', time() - 42000, '/');
    http_response_code($code);

    switch ($code){
        case 200 : //Tout est OK
            $return = array(
                'success'=> 'operation_complete',
                'error'=> false,
                'data'=> $obj
            );
            break;
        case 201 : //Creation OK
            $return = array(
                'success'=> 'entity_created',
                'error'=> false,
                'data'=> $obj
            );
            break;
        case 202 : //Operation acceptée et en cours mais retour delayé
            $return = array(
                'success'=> 'operation_processing',
                'error'=> false,
                'data'=> $obj
            );
            break;
        case 204 : //OK mais rien à retourner
//            $return = array(
//                'success'=> 'operation_complete',
//                'error'=> false,
//                'data'=> $obj
//            );
            break;
        case 206 : //Ok mais retour avec pagination
            $return = array(
                'success'=> 'operation_complete_partial_result',
                'error'=> false,
                'data'=> $obj,
                'pagination'=> $more
            );
            break;
        case 400 : //requete mal formatée ou parametre invalide
            $return = array(
                'success'=> false,
                'error'=> 'invalid_request',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
        case 401 : //probleme d'auth
            $return = array(
                'success'=> false,
                'error'=> 'invalid_credentials',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
        case 403 : //probleme de droits
            $return = array(
                'success'=> false,
                'error'=> 'protected_ressource',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
        case 404 : //non trouvé
            $return = array(
                'success'=> false,
                'error'=> 'not_found',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
        case 405 : //methode non implémentée ou prohibée
            $return = array(
                'success'=> false,
                'error'=> 'not_implemented',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
        case 406 : // langue/format/mime non pris en compte
            $return = array(
                'success'=> false,
                'error'=> 'not_acceptable',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
        case 500 :
            $return = array(
                'success'=> false,
                'error'=> 'internal_server_error',
                'data'=> $obj,
                'error_description'=> $more
            );
            break;
    }

    echo json_encode($return);
}

//function flatten($arrayIn, &$arrayOut){
//    foreach($arrayIn as $ki=>$ai){
//        if(!is_array($ai))$arrayOut[$ki] = $ai;
//        flatten($ai,$arrayOut);
//    }
//}