<?php
//$vars['partenaires'] = Sys::getData('Reservation','Partenaire/Actif=1');
$vars['partenaires'] = Sys::getData('Reservation','Partenaire/Actif=1',0,30,'ASC','Ordre');
$vars['organisations'] = Sys::getData('Reservation','Organisation');
