<?php

//print_r($GLOBALS['Systeme']->Conf);
$vars['Societe'] = $GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::SOCIETE');
$vars['info'] = $GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::INFO');
$vars['tel'] = $GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::TEL');
$vars['adresse'] = $GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::ADRESSE');
$vars['ville'] = $GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::VILLE');
$vars['Resa'] = $vars['funcTempVars']['Resa'];
$vars['Resa']->Imprimer = 1;
$vars['Evenement']= $vars['Resa']->getOneParent('Evenement');
$vars['Spectacle'] = $vars['Evenement']->getOneParent('Spectacle');
$vars['Client'] = $vars['Resa']->getOneParent('Client');
$vars['Salle'] = $vars['Evenement']->getOneParent('Salle');
$vars['Organisation'] = $vars['Spectacle']->getOneParent('Organisation');
$vars['Personne'] = $vars['Resa']->getChildren('Personne');