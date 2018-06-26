<?php
$info= Info::getInfos($vars['Query']);
$obj = Apn::getCurrent();
$out = $obj->takePhoto();
$vars['obj'] = $obj;
$vars['success'] = $out;