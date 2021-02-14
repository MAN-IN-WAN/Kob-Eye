<?php

$o = Sys::getOneData('Systeme', 'MailSMTP/Selected=1');
$vars['SMTP'] = $o ? $o->Id : 0;
$vars['module'] = 'Systeme';
$vars['objecttype'] = 'MailSMTP';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'SelectSMTP';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];
?>