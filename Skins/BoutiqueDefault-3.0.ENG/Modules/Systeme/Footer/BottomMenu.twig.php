<?php
    $men = genericClass::createInstance('Systeme','Menu');
    $mens = $men->getBottomMenus();
    $tmp = array();
    for ($i=$vars['Num']*3; $i<($vars['Num']+1)*3&&$i<sizeof($mens);$i++){
        $tmp[] = $mens[$i];
    }
    $vars['menus'] = $tmp;
?>