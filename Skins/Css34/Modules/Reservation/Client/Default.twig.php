<?php
$i = Info::getInfos($vars['Query']);
if ($i['TypeSearch']=='Direct'){
    $vars['interface'] = "Fiche";
}else $vars['interface'] = "List";