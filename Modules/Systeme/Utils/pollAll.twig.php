<?php
    $e = genericClass::createInstance('Systeme','Event');
    $pr = json_decode(file_get_contents('php://input'));
    $vars['items'] = $e->pollAll($pr->pollStart,$pr->pollInterval,$pr->pollDuration);
    $vars['EvCount'] = sizeof($vars['items']['Ev']);
    $vars['AlCount'] = sizeof($vars['items']['Au']);
    $vars['Now'] = (sizeof($vars['items']['Ev']))?$vars['items']['Ev'][sizeof($vars['items']['Ev'])-1]['tmsCreate']:time();
