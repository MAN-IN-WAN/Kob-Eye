<?php
$vars['Annee'] = Cadref::$Annee;
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$o->setView();
$vars['CurrentObj'] =  $o;
$vars['filters'] = $o->getCustomFilters();
$vars['recursiv'] = $o->isRecursiv();
foreach ($vars['filters'] as $k=>$f){
    if (empty($f->icon))$vars['filters'][$k]->icon = 'stats-growth';
    $vars['filters'][$k]->count = Sys::getCount($info['Module'],$info['ObjectType'].'/'.$f->filter);
}
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = $info['Module'] . $info['ObjectType'];

// PGF 20180912
if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];

$file = $o->isRecursiv() ? 'Tree' : 'List';

$q = $info['Module'].'/'.$info['ObjectType'].'/';
$p = getcwd().'/Skins/'.Sys::$Skin.'/Modules/'.$q;
$vars['listPath'] = (file_exists($p.'List.twig') ? $q : $info['Module'].'/Default/').$file;

$t = isset($_GET['hideBtn']) ? $_GET['hideBtn'] : '';
$vars['hideBtn'] = array(
	'selection' => strpos($t, 'selection') !== false,
	'add' => strpos($t, 'add') !== false,
	'delete' => strpos($t, 'delete') !== false,
	'export' => strpos($t, 'export') !== false,
	'functions' => strpos($t, 'functions') !== false
);
$vars['showCheckboxes'] = false;

?>