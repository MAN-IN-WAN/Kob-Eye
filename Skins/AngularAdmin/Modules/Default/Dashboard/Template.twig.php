<?php
$vars['NbMyTickets'] = Sys::getCount('Parc','Ticket/Etat=10&UserNext='.$GLOBALS["Systeme"]->RegVars['ParcTechnicien']->IdGestion.'&Categorie=OPE');
$vars['NbDomaines'] = Sys::getCount('Parc','Domain');
$vars['NbDevices'] = Sys::getCount('Parc','Device');
$vars['NbEmails'] = Sys::getCount('Parc','CompteMail');