<?php
$info = Info::getInfos('Systeme/Tache');
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = 'ganttSystemeActivity';
