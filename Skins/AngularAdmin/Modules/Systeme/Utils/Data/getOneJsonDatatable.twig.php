<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('fiche|form','',true);
$vars['row'] = Sys::getOneData($info['Module'],$vars['Query']);
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
    if ($f['type']=='text'){
        //transformation des timestamps en format js
        $vars['row']->{$f['name']} = str_replace("\n",'\\\n',$vars['row']->{$f['name']});
    }
    if ($f['type']=='fkey'&&$f['card']=='short'){
        if ($vars['row']->{$f['name']} > 0) {
            $kk = Sys::getOneData($f['objectModule'], $f['objectName'] . '/' . $vars['row']->{$f['name']});
            $vars['row']->{$f['name'].'label'} = $kk->getFirstSearchOrder();
        }else{
            $vars['row']->{$f['name'].'label'} = '';
        }
    }
    if ($f['type']=='rkey'){
        $kk = Sys::getData($f['objectModule'], $vars['Query'].'/'.$f['objectName']);
        $vars['row']->{$f['name']} = array();
        foreach ($kk as $k)$vars['row']->{$f['name']}[] = $k->Id;

    }
}
?>