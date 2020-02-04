<?php
//config
$nb = 20;


//recherche
$filters = '';
//floue
if (!empty($_GET['search'])) $filters = '~'.$_GET['search'];
//default
if (empty($filters)) $filters =  'AlaUne=1&DateDebut>'.time();

$vars['news'] = Sys::getData('News','Nouvelle/'.$filters,0,$nb);

