<?php
$vars['user'] = Sys::$User;

//Definition des choix pour le formulaire de creation de ticket
$vars['services'] = array('Commercial', 'Administratif', 'Téléphonie', 'Serveur', 'Poste', 'Web', 'Autre');
$vars['urgences'] = array( 1 => 'Reportable', 2 => 'Normal', 3 => 'Perturbant', 4 => 'Bloquant');

$vars['Abtel'] = array_key_exists('Abtel',Sys::$Modules);