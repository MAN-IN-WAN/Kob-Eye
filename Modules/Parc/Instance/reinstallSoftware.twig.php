<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
$out = $obj->createInstallTask();
$vars['obj'] = $obj;
$vars['success'] = $out;