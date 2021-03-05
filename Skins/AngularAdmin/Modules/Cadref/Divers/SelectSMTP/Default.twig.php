<?php
// fiche Selectsmtp
$o = Sys::getOneData('Systeme', 'MailSMTP/Selected=1');
$vars['SMTP'] = $o ? $o->Id : 0;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'SystemeMailSMTP';

$tmp = array();
$ss = Sys::getData('Systeme', 'MailSMTP');
foreach($ss as $s) {
	$tmp[$s->Id] = $s->Nom;
}
$vars['smtps'] = $tmp;

