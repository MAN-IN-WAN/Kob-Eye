<?php

$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
$vars['res'] =  $obj->unDelete();