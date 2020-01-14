<?php

$now = time();
$now+=86400;
$value = mktime(0,0,0,date('m',$now),date('d',$now),date('Y',$now));
$vars['initDate'] = $value;