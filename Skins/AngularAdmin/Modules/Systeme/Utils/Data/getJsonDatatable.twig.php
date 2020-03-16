<?php
session_write_close();
$info = Info::getInfos($vars['Path']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$o->setView();
$vars['fields'] = $o->getElementsByAttribute('list','',true);
//calcul offset / limit
$offset = (isset($_GET['offset']))?$_GET['offset']:0;
$limit = (isset($_GET['limit']))?$_GET['limit']:30;
$filters = (isset($_GET['filters']))?$_GET['filters']:'';
$context = (isset($_GET['context']))?$_GET['context']:'default';
$sort = (isset($_GET['sort']))?json_decode($_GET['sort']):array();
$path = explode('/',$vars['Path'],2);
$path = $path[1];
if(strpos($context,'recursivchildren-') === 0){
    $limit = 100000;
}

//requete
if(connection_aborted()){
    endPacket();
    exit;
}
if(count($sort)) {
    $vars['rows'] = Sys::getData($info['Module'], $path . '/' . $filters, $offset, $limit, $sort[1], $sort[0]);
}else {
    $vars['rows'] = Sys::getData($info['Module'], $path . '/' . $filters, $offset, $limit);
}

$ids= array_map(function($i){return $i->Id;},$vars['rows']);
//souscription au push
Event::registerPush($info['Module'],$info['ObjectType'],$path,$filters,$offset,$limit,$context,$ids,$sort);


if(connection_aborted()){
    endPacket();
    exit;
}
$interfaces = $o->getInterfaces();
$children = array();
foreach ($interfaces as $i){
    foreach ($i as $form) {
        if (isset($form['child'])) {
            array_push($children, $form['child']);
        }
    }
}

$oc = $o->getObjectClass();
$childrenelements = $oc->getChildElements();
$getCchild = array();
foreach ($childrenelements as $childelem){
    //test role                                                             //test hidden                                               //test admin
    if (((!isset($childelem['hasRole'])||Sys::$User->hasRole($childelem['hasRole'])) && !isset($childelem['childrenHidden'])&&!isset($childelem['hidden'])) || (!is_object(Sys::$CurrentMenu) && Sys::$User->Admin)){
        if(isset($childelem['listParent']) && $childelem['card']=='short' ){
            array_push($getCchild,$childelem);
        }
    }
}
$vars['children'] = $getCchild;

foreach ($vars['rows'] as $k=>$v){
//    $uc = Sys::getOneData('Systeme','User/'.$v->userCreate);
//    $ue = Sys::getOneData('Systeme','User/'.$v->userEdit);
//    if (is_object($uc))
//        $v->userCreateName = $uc->Login;
//    else $v->userCreateName = 'inconnu';
//    if (is_object($ue))
//        $v->userEditName = $ue->Login;
//    else $v->userEditName = 'inconnu';
    $v->label = Utils::cleanJson($v->getFirstSearchOrder());
    if ($v->getSecondSearchOrder())
        $v->description = $v->getSecondSearchOrder();
    foreach ($vars['fields'] as $f){
        switch ($f['type']){
            case 'date':
                //transformation des timestamps en format js
                if($v->{$f['name']} > 0)
                    $v->{$f['name']} = date('d/m/Y',$v->{$f['name']});
                else
                    $v->{$f['name']} = '';
                break;
            case 'datetime':
                //transformation des timestamps en format js
                if($v->{$f['name']} > 0)
                    $v->{$f['name']} = date('d/m/Y H:i',$v->{$f['name']});
                else
                    $v->{$f['name']} = '';
                break;
            case 'text':
            case 'varchar':
            case 'titre':
            case 'html':
            case 'raw':
                //transformation des timestamps en format js
                $v->{$f['name']} = Utils::cleanJson($v->{$f['name']});
                //Clean des symboles twig
                $v->{$f['name']} = str_replace('{{','{&zwnj;{', $v->{$f['name']});
                $v->{$f['name']} = str_replace('}}','}&zwnj;}', $v->{$f['name']});
                $v->{$f['name']} = str_replace('{#','{&zwnj;#', $v->{$f['name']});
                $v->{$f['name']} = str_replace('#}','#&zwnj;}', $v->{$f['name']});
                $v->{$f['name']} = str_replace('{%','{&zwnj;%', $v->{$f['name']});
                $v->{$f['name']} = str_replace('%}','%&zwnj;}', $v->{$f['name']});
                break;
        }
        if (isset($f['Values'])&&isset($f['Values'][$v->{$f['name']}])){
            $v->{$f['name'].'Label'} = $f['Values'][$v->{$f['name']}];
        }else if (isset($f['query'])&&$v->{$f['name']}>0){
            //recherche de sa valeur
            $str = explode('::',$f['query']);
            $qry = explode('/',$str[0],3);
			$val = Sys::getOneData($qry[0],$qry[1].'/'.$v->{$f['name']});
			if ($val){
	            $v->{$f['name'].'Label'} = $val->getFirstSearchOrder();
			}else{
				$v->{$f['name'].'Label'} = '';
			}
        }else $v->{$f['name'].'Label'} = '';
        if ($f['type']=='fkey'&&$f['card']=='short'){
            if ($v->{$f['name']} > 0) {
                $kk = Sys::getOneData($f['objectModule'], $f['objectName'] . '/' . $v->{$f['name']});
                if ($kk) {
                    $v->{$f['name'] . 'label'} = $kk->getFirstSearchOrder() .' '. $kk->getSecondSearchOrder();
                    if(isset($kk->Couleur))
                        $v->{$f['name'].'color'} = $kk->Couleur;
                }
            }else{
                $v->{$f['name'].'label'} = '';
            }
        }
    }

    foreach($getCchild as $gc){
        $vc = $v->getOneChild($gc['objectName']);
        if($vc)
            $v->{$gc['objectName'].'Clabel'} = $vc->getFirstSearchOrder();
    }




    //cas widget
    if (sizeof($children)){
        foreach ($children as $c)
            $v->{$c} = array_reverse($v->getChildren($c));
    }
    //recursivity
    if ($o->isRecursiv()){
        $v->isTail = ($v->isTail()) ? '1':'0';
    }
}

if ($o->isRecursiv()) {
    $vars['recursiv'] = true;
}
if (sizeof($children)){
    foreach ($children as $c)
        array_push($vars['fields'],array('type'=>'children','name'=>$c));
}


$vars['total'] = Sys::getCount($info['Module'],$path.'/'.$filters);


function endPacket(){
    echo "0\r\n\r\n";
    ob_flush();
    flush();
}
//echo $GLOBALS['Chrono']->total();
?>