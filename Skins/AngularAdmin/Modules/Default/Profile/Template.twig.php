<?php
$vars['NbDomaines'] = Sys::getCount('Parc','Domain');
$vars['NbDevices'] = Sys::getCount('Parc','Device');
$vars['NbEmails'] = Sys::getCount('Parc','CompteMail');
$vars['User'] = Sys::$User;
if(!$vars['User']->Avatar)
    $vars['User']->Avatar = 'Skins/AngularAdmin/assets/common/img/neutral_avatar.png';

$vars['Client'] = $vars['User']->getOneChild('Client');
$vars['formfields'] = $vars['User']->getElementsByAttribute('form','',true);
$vars['fichefields'] = $vars['User']->getElementsByAttribute('fiche','',true);

if(isset($vars['User']->Adresse) && $vars['User']->Adresse != '')
    $vars['adresse'] = $vars['User']->Adresse;

if(isset($vars['User']->CodPos) && $vars['User']->CodPos != '')
    $vars['adresse'] = $vars['adresse']? $vars['adresse'].' ,'.$vars['User']->CodPos : $vars['User']->CodPos;

if(isset($vars['User']->Ville) && $vars['User']->Ville != '')
    $vars['adresse'] = $vars['adresse']? $vars['adresse'].' ,'.$vars['User']->Ville : $vars['User']->Ville;

if(isset($vars['User']->Pays) && $vars['User']->Pays != '')
    $vars['adresse'] = $vars['adresse']? $vars['adresse'].' ,'.$vars['User']->Pays : $vars['User']->Pays;

if($vars['Client']){
    $vars["ObjectClass"] = $vars["Client"]->getObjectClass();
    $vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
}