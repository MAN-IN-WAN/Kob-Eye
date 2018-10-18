<?php

$info = Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$info['Query']);


$vars['Cli'] = $obj;
$vars['Domains'] = $obj->getChildren('Domain');
$vars['Hosts'] = $obj->getChildren('Host');


array_walk($vars['Domains'], function ($i) {
    $children = array();
    $children['Subdomain'] = $i->getChildren('SubDomain');
    $children['CNAME'] = $i->getChildren('CNAME');
    $children['MX'] = $i->getChildren('MX');
    $children['NS'] = $i->getChildren('NS');
    $children['TXT'] = $i->getChildren('TXT');
    $i->aChildren = $children;
});

array_walk($vars['Hosts'], function ($i) {
    $parent = $i->getOneParent('Server');
    $i->SrvName = $parent->Nom;
    $children = array();
    $children['Apache'] = $i->getChildren('Apache');
    $children['FtpUser'] = $i->getChildren('FtpUser');
    $i->aChildren = $children;
});


$vars['SUBJECT'] = "[ABTEL.FR] - ".$vars['Cli']->Nom." - Récapitulatif de compte hébergement";
$vars['SUBJECT'] = addslashes($vars['SUBJECT']);


$vars['CONTENT'] = '<h1>DETAILS</h1>
<p>
Configuration du domaine (whois):<br />
Name server 1: <b>ns1.abtel.fr</b>
Name server 2: <b>ns1.abtel.fr</b>
</p>

<h1>DOMAINE</h1>';
if(count($vars['Domains'])){
    $vars['CONTENT'] .= '<ul>';

    foreach($vars['Domains'] as $Do){
        $vars['CONTENT'] .= '<li>
        <h2>'.$Do->Url .'</h2>
        <h3>Détail de la zone</h3>
        <table style="border:1px solid #d6d6d6" width="80%">
        <thead style="background-color:#5c5c5c;color:white">
            <th colspan="2">Type A</th>
        </thead>
        <thead style="background-color:#cdcdcd">
            <th>Nom de domaine</th>
            <th>IP</th>
        </thead>';


        $i = 0;
        foreach($Do->aChildren['SubDomain'] as $Sd){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
            <td>'.$Sd->Url.'</td>
            <td>'.$Sd->IP.'</td>
        </tr>';
        }

        $vars['CONTENT'] .= '<thead style="background-color:#5c5c5c;color:white">
            <th colspan="2">Type CNAME</th>
        </thead>
        <thead style="background-color:#cdcdcd">
            <th>Nom de domaine</th>
            <th>Alias</th>
        </thead>';

        $i = 0;
        foreach($Do->aChildren['CNAME'] as $Cn){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
            <td>'.$Cn->Dnsdomainname.'</td>
            <td>'.$Cn->Dnscname.'</td>
        </tr>';
        }

        $vars['CONTENT'] .= '<thead style="background-color:#5c5c5c;color:white">
            <th colspan="2">Type MX</th>
        </thead>
        <thead style="background-color:#cdcdcd">
            <th></th>
            <th>Alias</th>
        </thead>';

        $i = 0;
        foreach($Do->aChildren['MX'] as $Mx){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
            <td>'.$Mx->Nom.'</td>
            <td>'.$Mx->Dnscname.'</td>
        </tr>';
        }

        $vars['CONTENT'] .= '<thead style="background-color:#5c5c5c;color:white">
            <th colspan="2">Type NS</th>
        </thead>
        <thead style="background-color:#cdcdcd">
            <th></th>
            <th>Serveur dns</th>
        </thead>';

        $i = 0;
        foreach($Do->aChildren['NS'] as $Ns){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
            <td>'.$Ns->Dnsdomainname.'</td>
            <td>'.$Ns->Dnscname.'</td>
        </tr>';
        }

        $vars['CONTENT'] .= '<thead style="background-color:#5c5c5c;color:white">
            <th colspan="2">Type TXT</th>
        </thead>
        <thead style="background-color:#cdcdcd">
            <th>Nom de domaine</th>
            <th>Texte</th>
        </thead>';

        $i = 0;
        foreach($Do->aChildren['TXT'] as $Txt){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
            <td>'.$Txt->Dnsdomainname.'</td>
            <td>'.$Txt->Dnstxt.'</td>
        </tr>';
        }
        $vars['CONTENT'] .= '</table>
        </li>';
    }

    $vars['CONTENT'] .= '</ul>';
} else{
    $vars['CONTENT'] .= '<p class="warning"> Aucun Domaine</p>';
}

$vars['CONTENT'] .= '<h1>HEBERGEMENT</h1>';
if(count($vars['Hosts'])){
    $vars['CONTENT'] .= '<ul>';

    foreach($vars['Domains'] as $Do){
        $vars['CONTENT'] .= ' <li>
<h2>'.$Ho->Nom.'  sur '. $Ho->SrvName.'</h2>
<h3>Détail de l\'hébergement</h3>
<table style="border:1px solid #d6d6d6" width="80%">
        <thead style="background-color:#5c5c5c;color:white">
            <th colspan="3">Virtualhosts</th>
        </thead>
        <thead style="background-color:#cdcdcd">
            <th>Chemin</th>
            <th>Nom de domaine principal</th>
            <th>Nom(s) de domaine alias</th>
        </thead>';

        $i = 0;
        foreach($Ho->aChildren['Apache'] as $Ap){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
                <td>'.$Ap->DocumentRoot.'</td>
                <td>'.$Ap->ApacheServerName.'</td>
                <td>'.$Ap->ApacheServerAlias.'</td>
            </tr>';
        }
        $vars['CONTENT'] .= '<thead style="background-color:#5c5c5c;color:white">
                <th colspan="3">Comptes FTP</th>
            </thead>
            <thead style="background-color:#cdcdcd">
                <th>Identifiant</th>
                <th>Mot de passe</th>
                <th>Chemin</th>
            </thead>';

        $i = 0;
        foreach($Ho->aChildren['Ftpuser'] as $Ft){
            $i++;
            $vars['CONTENT'] .= '<tr '.($i % 2 )?'':'style="background-color:#dedede"'.'>
                <td>'.$Ft->Identifiant.'</td>
                <td>'.$Ft->Password.'</td>
                <td>'.$Ft->DocumentRoot.'</td>
            </tr>';
        }

        $vars['CONTENT'] .= '</table>
        </li>';

    }

    $vars['CONTENT'] .= '</ul>';
} else{
    $vars['CONTENT'] .= '<p class="warning"> Aucun Hébergement</p>';
}






