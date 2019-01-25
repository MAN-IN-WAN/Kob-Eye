<?php

$ftps = Sys::getData('Parc','Ftpuser',0,2000,null,null,null,null,true);
$temp = array();
foreach($ftps as $ftp){
    $host = $ftp->getOneParent('Host');
    $cli = $host->getOneParent('Client');

    if(!isset($temp[$cli->Id]))
        $temp[$cli->Id] = array(
                'Nom'=> $cli->Nom,
                'Hosts'=>array()
        );

    if(!isset($temp[$cli->Id]['Hosts'][$host->Id]))
        $temp[$cli->Id]['Hosts'][$host->Id] = array(
            'Nom'=> $host->Nom,
            'Ftps'=>array()
        );

    $temp[$cli->Id]['Hosts'][$host->Id]['Ftps'][] = $ftp->Identifiant;
}

$res = '<section class="panel"><div class="panel-heading"><h3>Liste des utilisateurs ftp</h3><hr></div><div class="panel-body"><ul>';
foreach($temp as $c){
    $res .= '<li>
                <h4 style="margin: 0;">'.$c['Nom'].'</h4>
                <ul>';
    foreach($c['Hosts'] as $h){
        $res .= '    <li>
                        <h5  style="margin: 0;">'.$h['Nom'].'</h5>
                        <ul>';
        foreach($h['Ftps'] as $f){
            $res .= '       <li>'.$f.'</li>';
        }
        $res .= '       </ul>
                     </li>';
    }
    $res .= '   </ul>
             </li><br>';
}
$res .= '</ul></div></section>';


echo $res;