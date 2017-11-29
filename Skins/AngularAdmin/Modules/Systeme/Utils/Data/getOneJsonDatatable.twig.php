<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('fiche|form','',true);
$context = (isset($_GET['context']))?$_GET['context']:'default';
//souscription au push
Event::registerPush($info['Module'],$info['ObjectType'],$info['ObjectType'],'~',0,1,$context);
//requete
$vars['row'] = Sys::getOneData($info['Module'],$vars['Query']);
if (!$vars['row'])return;
$vars['row']->label = $vars['row']->getFirstSearchOrder();
$uc = Sys::getOneData('Systeme','User/'.$vars['row']->userCreate);
$ue = Sys::getOneData('Systeme','User/'.$vars['row']->userEdit);
if (is_object($uc))
    $vars['row']->userCreateName = $uc->Login;
else $vars['row']->userCreateName = 'inconnu';
if (is_object($ue))
    $vars['row']->userEditName = $ue->Login;
else $vars['row']->userEditName = 'inconnu';
foreach ($vars['fields'] as $f){
    if ($f['type']=='date'){
        //transformation des timestamps en format js
        $vars['row']->{$f['name']} = date('d/m/Y H:i',$vars['row']->{$f['name']});
    }
    if ($f['type']=='text'||$f['type']=='raw'||$f['type']=='varchar'||$f['type']=='html'||$f['type']=='titre'){
        //transformation des timestamps en format js
        $vars['row']->{$f['name']} = Utils::cleanJson($vars['row']->{$f['name']});
    }
    if ($f['type']=='fkey'&&$f['card']=='short'){
        if ($vars['row']->{$f['name']} > 0) {
            $kk = Sys::getOneData($f['objectModule'], $f['objectName'] . '/' . $vars['row']->{$f['name']});
            if ($kk)
                $vars['row']->{$f['name'].'label'} = $kk->getFirstSearchOrder();
        }else{
            $vars['row']->{$f['name'].'label'} = '';
        }
    }
    if ($f['type']=='fkey'&&$f['card']=='long'){
        $kk = $vars['row']->getParents($f['objectName']);
        $vars['row']->{$f['name']} = array();
        foreach ($kk as $k)
            $vars['row']->{$f['name']}[] = $k->Id;
    }
    if ($f['type']=='rkey'){
        $kk = Sys::getData($f['objectModule'], $vars['Query'].'/'.$f['objectName']);
        $vars['row']->{$f['name']} = array();
        foreach ($kk as $k)$vars['row']->{$f['name']}[] = $k->Id;

    }
    if (isset($f['Values'])&&isset($f['Values'][$vars['row']->{$f['name']}])){
        $vars['row']->{$f['name'].'Label'} = $f['Values'][$vars['row']->{$f['name']}];
    }else if (isset($f['query'])&&$vars['row']->{$f['name']}>0){
        //recherche de sa valeur
        $str = explode('::',$f['query']);
        $qry = explode('/',$str[0],2);
        $val = Sys::getOneData($qry[0],$qry[1].'/'.$vars['row']->{$f['name']});
        $vars['row']->{$f['name'].'Label'} = $val->getFirstSearchOrder();
    }else $vars['row']->{$f['name'].'Label'} = '';
}
?>