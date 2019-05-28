<?php

$info = Info::getInfos($vars['Query']);

$vars['spec'] = Sys::getOneData($info['Module'],$info['Query']);
