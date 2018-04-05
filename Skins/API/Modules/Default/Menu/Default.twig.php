<?php
/** DEFAULT CONTROLLER **/
/*$mods = array();
foreach (Sys::$Modules as $k=>$mod){
    $mods[$k] = array_keys($obj->getObjectClass());
}*/
//$vars['modules'] = Sys::$Modules;
//if(count(Sys::$User->Menus))
//    $vars['menus'] = Sys::$User->Menus[0]->getMainMenus();
////print_r(Sys::$User->Menus);
//$vars['user'] = Sys::$User;

$vars['anchors'] = array(
    array(
        'Url'=>'#Prereq',
        'Icon'=>'icmn-bubble-notification',
        'Titre'=>'Prérequis'
    ),
    array(
        'Url'=>'#Auth',
        'Icon'=>'icmn-user-check',
        'Titre'=>'Authentification'
    ),
    array(
        'Url'=>'#Methods',
        'Icon'=>'icmn-cog2',
        'Titre'=>'Méthodes'
    ),
    array(
        'Url'=>'#Props',
        'Icon'=>'icmn-zoom-in3',
        'Titre'=>'Propriétés des entités'
    ),
    array(
        'Url'=>'#Codes',
        'Icon'=>'icmn-html-five2',
        'Titre'=>'Codes de retour'
    ),
);

?>