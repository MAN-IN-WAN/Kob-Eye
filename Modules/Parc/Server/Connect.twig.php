<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
$out = $obj->Connect();
$vars['obj'] = $obj;
$vars['success'] = $out;