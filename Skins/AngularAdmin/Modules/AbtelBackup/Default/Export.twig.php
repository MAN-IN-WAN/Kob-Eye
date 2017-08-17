<?php
$vars['Path'] = $vars['Query'];
/*if (Sys::$User->isRole('PARC_CLIENT')){
    switch ($vars['Query']){
        case 'Parc/Device':
            $vars['Path'] = 'Parc/Client/'.$vars['ParcClient']->Id.'/Device';
            break;
    }
}*/
$GLOBALS["Systeme"]->registerVar('Path',$vars['Path']);
?>