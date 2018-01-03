<?php
$vars['NbDomaines'] = Sys::getCount('Parc','Domain');
$vars['NbDevices'] = Sys::getCount('Parc','Device');
$vars['NbEmails'] = Sys::getCount('Parc','CompteMail');