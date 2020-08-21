<?php

$info=Info::getInfos($vars["Query"]);

if ($info["TypeSearch"]=="Direct") {
    $vars ['Direct']=true;
} else {
    $vars ['Direct']=false;

}
