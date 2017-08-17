<?php
$info = Info::getInfos($vars['Chemin']);
$vars['identifier'] = $vars['Url'];
$vars['store'] = false;
$vars['name'] = $vars['Url'] ;
$vars['module'] = $info['Module'];
$vars['objecttype'] = $info['ObjectType'];
