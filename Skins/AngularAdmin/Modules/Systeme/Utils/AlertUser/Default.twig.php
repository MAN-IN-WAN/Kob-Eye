<?php
$au =Sys::getData('Systeme','AlertUser/Read=0');
foreach($au as &$d)
	$d->Time = date('d/m H:i', $d->Time);
$vars['data'] = json_encode($au);
