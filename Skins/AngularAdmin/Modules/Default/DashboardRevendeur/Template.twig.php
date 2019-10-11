<?php
$vars['NbHost'] = Sys::getCount('Parc','Host');
$vars['NbDomain'] = Sys::getCount('Parc','Domain');
$vars['NbClients'] = Sys::getCount('Parc','Client');
$vars['NbEmails'] = Sys::getCount('Parc','CompteMail');
$vars['NbMyTickets'] = Sys::getCount('Parc','Ticket/Etat=10&Categorie=OPE');
