<?php


$vars['User'] = Sys::$User;
if(!$vars['User']->Avatar)
    $vars['User']->Avatar = 'Skins/AngularAdmin/assets/common/img/neutral_avatar.png';

$vars['Client'] = $vars['User']->getOneChild('Client');
$vars['displayName'] = '';

if (is_object($vars['Client']) && isset($vars['Client']->Nom) && $vars['Client']->Nom != ''){
    $vars['displayName'] =  'de la société '.$vars['Client']->Nom;
} else{
    if(isset($vars['User']->Nom) && $vars['User']->Nom != ''){
        $login = $vars['User']->Nom;
    } else{
        $login = $vars['User']->Login;
    }
    //TODO : prendre en compte les caractères accentués (strpos éèà....)
    if(in_array(substr($login,0,1),array('a','e','i','o','u','y')) ){
        $vars['displayName'] =  'd\''.$login;
    }else{
        $vars['displayName'] =  'de '.$login;
    }
}
if(Sys::$User->isRole('PARC_TECHNICIEN'))
    $vars['RoleSpace'] = 'technicien';
else if(Sys::$User->isRole('CADREF_ADMIN')) $vars['RoleSpace'] = 'administrateur';
else if(Sys::$User->isRole('CADREF_ENS')) $vars['RoleSpace'] = 'enseignant';
else if(Sys::$User->isRole('CADREF_ADH')) $vars['RoleSpace'] = 'adhérent';
else $vars['RoleSpace'] = 'client';


