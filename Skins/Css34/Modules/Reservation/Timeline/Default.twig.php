<?php
$vars['dates'] = array();
$now = time();
for ($i=0;$i<31;$i++){
    $now+=86400;
    $value = mktime(0,0,0,date('m',$now),date('d',$now),date('Y',$now));
    $selected = ($_GET['date']==$value)?true:false;
    $vars['dates'][] = array('num'=>date('d',$now),'day'=>date('D',$now),'value'=>$value,'selected'=>$selected);
}
$vars['search'] = $_GET['search'];
$vars['date'] = $_GET['date'];
$vars['genre'] = $_GET['genre'];