<?php
$vars['dates'] = array();
$now = time();
for ($i=0;$i<31;$i++){
    $now+=86400;
    $vars['dates'][] = array('num'=>date('d',$now),'day'=>date('D',$now));
}