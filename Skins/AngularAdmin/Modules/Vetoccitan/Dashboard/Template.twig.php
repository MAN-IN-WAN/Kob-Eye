<?php
$vars['NbAdherent'] = Sys::getCount('Vetoccitan','Adherent');
$vars['NbMinisite'] = Sys::getCount('Parc','MiniSite');
$vars['AdUrl']=Sys::getMenu('Vetoccitan/Adherent');
$vars['MSUrl']=Sys::getMenu('Parc/MiniSite');

