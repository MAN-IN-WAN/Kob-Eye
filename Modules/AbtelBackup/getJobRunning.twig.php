<?php

$cpt = Sys::getCount('Systeme','Tache/Demarre=1&&Termine=0&&Erreur=0');


echo  $cpt;