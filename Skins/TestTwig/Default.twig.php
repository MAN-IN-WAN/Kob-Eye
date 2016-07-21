<?php
/** DEFAULT CONTROLLER **/
$vars['currentSkin'] = Sys::$Skin;
$GLOBALS["Systeme"]->Header->addCss('Tools/Css/Bootstrap/3.0/css/bootstrap.min.css');
$GLOBALS["Systeme"]->Header->addCss('Tools/Css/Bootstrap/3.0/css/bootstrap-theme.min.css');
$GLOBALS["Systeme"]->Header->addCss('Skins/'.Sys::$Skin.'/Js/lightbox.min.css');
$GLOBALS["Systeme"]->Header->addJs('Tools/Js/Jquery/1.9.2/jquery.min.js');
$GLOBALS["Systeme"]->Header->addJs('/Tools/Js/Masonry/masonry.min.js');
?>