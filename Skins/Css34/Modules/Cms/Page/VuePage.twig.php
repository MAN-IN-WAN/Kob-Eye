<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance('Cms','Page');
$o->initFromId($info['LastId']);
$vars['page'] = $o;
$vars['content'] = $o->Display();