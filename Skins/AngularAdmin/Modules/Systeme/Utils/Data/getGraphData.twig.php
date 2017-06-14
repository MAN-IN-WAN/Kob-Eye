<?php
require_once ROOT_DIR."Class/Lib/Zabbix.class.php";

if(!isset($itemId)) return false;
if(!isset($valuesCount)) $valuesCount = 10;

//On recupère les données et on assigne le tout à twig
$vars['graphData'] = array();

//X dernières valeurs
$data = Zabbix::getLastGraphData($itemId,$valuesCount);
$vars['graphData'] = array_reverse($data);
$vars['total'] = count($data);





?>