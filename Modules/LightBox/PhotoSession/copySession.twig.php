<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
$out = $obj->copySession();
$vars['obj'] = $obj;
$vars['success'] = $out;