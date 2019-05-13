<?php

//INPUT
//qi_NUM
//session // numéro de session
//equipe  // numéro d'équipe
//reception des réponses
$cur = $vars['CurrentSession'];
$session = $vars['session'];
$equipe = $vars['equipe'];
$ret = array('success'=>false);

if($cur->Id == $session){
    $res = $cur->saveResult($equipe);
    if($res !== false){
        $ret['success'] = true;
        //TODO retour en cas de goto !
    }
    if($res !== true && (is_int($res) || is_string($res))){
        $ret['next'] = $res;
    }
} else{
    $ret['reset'] = true;
    $ret['msg'] = 'Impossible de trouver la session '.$session.'.';
}


echo json_encode($ret);
