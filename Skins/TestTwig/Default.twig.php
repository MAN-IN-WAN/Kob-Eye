<?php
/** DEFAULT CONTROLLER **/
$vars['currentSkin'] = Sys::$Skin;
$GLOBALS["Systeme"]->Header->addCss('Tools/Css/Bootstrap/3.0/css/bootstrap.min.css');
$GLOBALS["Systeme"]->Header->addCss('Tools/Css/Bootstrap/3.0/css/bootstrap-theme.min.css');
$GLOBALS["Systeme"]->Header->addCss('Skins/'.Sys::$Skin.'/Js/lightbox.min.css');
$GLOBALS["Systeme"]->Header->addJs('Tools/Js/Jquery/1.9.2/jquery.min.js');
$GLOBALS["Systeme"]->Header->addJs('/Tools/Js/Masonry/masonry.min.js');

$image=  '';
$im = Sys::getOneData('Systeme','Menu/'.$GLOBALS['Systeme']->CurrentMenu->Id.'/Donnee/Type=Image');
if (!is_object($im)){
    $im = Sys::getOneData('Systeme','Menu/'.$GLOBALS['Systeme']->DefaultMenu->Id.'/Donnee/Type=Image');
}
$vars['image'] = $im->Lien;

?>