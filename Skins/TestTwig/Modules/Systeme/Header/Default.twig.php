<?php
$image=  '';
$im = Sys::getOneData('Systeme','Menu/'.$GLOBALS['Systeme']->CurrentMenu->Id.'/Donnee/Type=Image');
if (!is_object($im)){
    $im = Sys::getOneData('Systeme','Menu/'.$GLOBALS['Systeme']->DefaultMenu->Id.'/Donnee/Type=Image');
}
$vars['image'] = $im->Lien;

?>