<?php
$db = new PDO('mysql:host=192.168.100.2;dbname=parc', 'root', '125iAS34470', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//les revendeurs
/*$query = "SELECT * FROM `parc-Parc-Revendeur` LIMIT 0,1000";
$q = $db->query($query);
$revendeurs = $q->fetchALL(PDO::FETCH_ASSOC);
foreach ($revendeurs as $revendeur){
    //creation du revendeur
    $rev = Sys::getOneData('Parc','Revendeur/Nom='.$revendeur['Nom']);
    echo "-> Revendeur: " . $revendeur['Nom'] . " ****** \r\n";
    if (!$rev) {
        $rev = genericClass::createInstance('Parc', 'Revendeur');
        $rev->initFromArray($revendeur);
        unset($rev->Id);
        $rev->Save();
    }
    //les clients
    $query = "SELECT * FROM `parc-Parc-Client` WHERE RevendeurId=".$rev->Id." LIMIT 0,1000";
    $qclient = $db->query($query);
    $clients = $qclient->fetchALL(PDO::FETCH_ASSOC);
    foreach ($clients as $client){
        //creation du client
        $cli = Sys::getOneData('Parc','Client/NomLDAP='.$client['NomLDAP']);
        echo "    -> Client: " . $client['NomLDAP'] . " ****** \r\n";
        if (!$cli) {
            $cli = genericClass::createInstance('Parc', 'Client');
            $cli->initFromArray($client);
            unset($cli->Id);
            $cli->addParent($rev);
            $cli->Save();
        }
    }
}*/

//les domaines
$query = "SELECT * FROM `parc-Parc-Domain` LIMIT 0,1000";
$q = $db->query($query);
$result = $q->fetchALL(PDO::FETCH_ASSOC);
foreach ($result as $domain){
    //recherche du client
    /*$query = "SELECT * FROM `parc-Parc-Client` WHERE Id=".$domain['ClientId']." LIMIT 0,1";
    $q = $db->query($query);*/
    $client=false;
    //$clients = $q->fetchALL(PDO::FETCH_ASSOC);
    //foreach ($clients as $client){}

    //creation de l'instance
    $dom = Sys::getOneData('Parc','Domain/Url='.$domain['Url']);
    echo "-> " . $domain['Url'] . " ****** \r\n";
    if (!$dom) {
        continue;
        $dom = genericClass::createInstance('Parc', 'Domain');
        $dom->initFromArray($domain);
        unset($dom->Id);
        $dom->LdapID = "";
        $dom->LdapDN = "";
        $dom->LdapTms = "";
        $dom->updateOnSave = false;
    }
    //$dom->addParent('Parc/DomainTemplate/1');
    if (is_array($client))
        $dom->addParent('Parc/Client/'.$client['Id']);
    //$dom->Save();
    //A
    /*$queryA= "SELECT * FROM `parc-Parc-Subdomain` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qA = $db->query($queryA);
    $resultA = $qA->fetchALL(PDO::FETCH_ASSOC);
    $sexists = array();
    foreach ($resultA as $subdomain){
        echo "---> ".$subdomain['Url']."\r\n";
        $pref = substr($subdomain['Url'],2,100);
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/Subdomain/Nom='.$subdomain['Url']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'Subdomain');
            $s->initFromArray($subdomain);
            $s->addParent($dom);
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            unset($s->Id);
        }
        $s->Nom = $subdomain['Url'];
        if ($s->Url=='A:'){
            $s->Url = '';
        }else{
            $s->Url = $pref;
        }
        $s->Save();
    }

    //AAA
    $queryAAA= "SELECT * FROM `parc-Parc-AAA` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qAAA = $db->query($queryAAA);
    $resultAAA = $qAAA->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultAAA as $r){
        echo "---> ".$r['Url']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/AAA/Url='.$r['Url']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'AAA');
            $s->initFromArray($r);
            $s->addParent($dom);
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            unset($s->Id);
        }
        if (empty($s->Url)){
            $s->Url = $dom->Url.'.';
        }
        $s->Save();
    }*/

    //TXT
    $queryTXT= "SELECT * FROM `parc-Parc-TXT` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qTXT = $db->query($queryTXT);
    $resultTXT = $qTXT->fetchALL(PDO::FETCH_ASSOC);
    //foreach ($dom->getChildren('TXT') as $txt) $txt->Delete();
    foreach ($resultTXT as $r){
        echo "---> ".$r['Nom']." ".$r['Dnsdomainname']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/TXT/Dnstxt='.Utils::KEAddSlashes($r['Dnstxt']));
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'TXT');
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            $s->initFromArray($r);
            $s->addParent($dom);
            unset($s->Id);
        }
        if (!$s->Save()){
            print_r($s);
            die('ERREUR');
        }
    }

    //CNAME
    /*$queryCNAME= "SELECT * FROM `parc-Parc-CNAME` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qCNAME = $db->query($queryCNAME);
    $resultCNAME = $qCNAME->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultCNAME as $r){
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/CNAME/Dnsdomainname='.$r['Dnsdomainname']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'CNAME');
            $s->initFromArray($r);
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            $s->addParent($dom);
            unset($s->Id);
        }
        $s->Save();
    }*/

    //MX
    /*$queryMX= "SELECT * FROM `parc-Parc-MX` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qMX = $db->query($queryMX);
    $resultMX = $qMX->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultMX as $r){
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/MX/Dnscname='.$r['Dnscname']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'MX');
            $s->initFromArray($r);
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            $s->addParent($dom);
            unset($s->Id);
        }
        if (empty($s->Dnsdomainname)){
            $s->Dnsdomainname = $dom->Url.'.';
        }
        $s->Save();
    }

    //NS
    $queryNS= "SELECT * FROM `parc-Parc-NS` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qNS = $db->query($queryNS);
    $resultNS = $qNS->fetchALL(PDO::FETCH_ASSOC);
    $z=0;
    foreach ($resultNS as $r){
        $z++;
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/NS/Nom='.$r['Nom']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'NS');
            $s->initFromArray($r);
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            $s->addParent($dom);
            unset($s->Id);
        }
        if (empty($s->Dnsdomainname)){
            $s->Dnsdomainname = $dom->Url.'.';
        }
        if (empty($s->Dnscname)){
            $s->Dnscname = 'ns'.$z.'.maninwan.fr.';
        }
        $s->Save();
    }

    //SRV
    $querySRV= "SELECT * FROM `parc-Parc-SRV` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qSRV = $db->query($querySRV);
    $resultSRV = $qSRV->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultSRV as $r){
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/SRV/Nom='.$r['Nom']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'SRV');
            $s->initFromArray($r);
            $s->LdapID = "";
            $s->LdapDN = "";
            $s->LdapTms = "";
            $s->addParent($dom);
            unset($s->Id);
        }
        $s->Save();
    }*/
}


