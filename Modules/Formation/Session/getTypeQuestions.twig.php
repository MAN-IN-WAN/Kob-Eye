<?php

$ques = genericClass::createInstance('Formation','TypeQuestion');
$vars['fields'] = $ques->getElementsByAttribute('mobile','',true);

$sess = $vars['CurrentSession'];
$tquests = $sess->getTypeQuestions();
$vars['rows'] = $tquests;

foreach ($vars['rows'] as $k=>$v){
    $v->label = Utils::cleanJson($v->getFirstSearchOrder());
    if ($v->getSecondSearchOrder())
        $v->description = $v->getSecondSearchOrder();
    foreach ($vars['fields'] as $f){
        if($f['name'] == 'Parametres'){
            $v->{$f['name']} = json_decode($v->{$f['name']},true);
            continue;
        }
        switch ($f['type']){
            case 'date':
                //transformation des timestamps en format js
                if($v->{$f['name']} > 0)
                    $v->{$f['name']} = date('d/m/Y',$v->{$f['name']});
                else
                    $v->{$f['name']} = '';
                break;
            case 'datetime':
                //transformation des timestamps en format js
                if($v->{$f['name']} > 0)
                    $v->{$f['name']} = date('d/m/Y H:i',$v->{$f['name']});
                else
                    $v->{$f['name']} = '';
                break;
            case 'text':
            case 'varchar':
            case 'titre':
            case 'html':
            case 'raw':
                //transformation des timestamps en format js
                $v->{$f['name']} = Utils::cleanJson($v->{$f['name']});
                break;
        }
        if (isset($f['Values'])&&isset($f['Values'][$v->{$f['name']}])){
            $v->{$f['name'].'Label'} = $f['Values'][$v->{$f['name']}];
        }else if (isset($f['query'])&&$v->{$f['name']}>0){
            //recherche de sa valeur
            $str = explode('::',$f['query']);
            $qry = explode('/',$str[0],3);
            $val = Sys::getOneData($qry[0],$qry[1].'/'.$v->{$f['name']});
            if ($val){
                $v->{$f['name'].'Label'} = $val->getFirstSearchOrder();
            }else{
                $v->{$f['name'].'Label'} = '';
            }
        }else $v->{$f['name'].'Label'} = '';
        if ($f['type']=='fkey'&&$f['card']=='short'){
            if ($v->{$f['name']} > 0) {
                $kk = Sys::getOneData($f['objectModule'], $f['objectName'] . '/' . $v->{$f['name']});
                if ($kk) {
                    $v->{$f['name'] . 'label'} = $kk->getFirstSearchOrder() .' '. $kk->getSecondSearchOrder();
                    if(isset($kk->Couleur))
                        $v->{$f['name'].'color'} = $kk->Couleur;
                }
            }else{
                $v->{$f['name'].'label'} = '';
            }
        }
    }
}

