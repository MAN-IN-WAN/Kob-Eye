<?php
$info = Info::getInfos('Systeme/Tache/1');

$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = 'ganttSystemeActivity';

$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();

?>