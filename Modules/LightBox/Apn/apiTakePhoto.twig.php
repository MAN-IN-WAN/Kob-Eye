<?php
$info= Info::getInfos($vars['Query']);
$obj = Apn::getCurrent();
$out = $obj->takePhoto();
$vars['obj'] = $obj;
$vars['url'] = $out;
$vars['success'] = (!empty($out))?1:0;