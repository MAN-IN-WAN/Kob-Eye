<?php
$db = new PDO('mysql:host=192.168.100.2;dbname=parc', 'root', '125iAS34470', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT * FROM `parc-Parc-Domain` LIMIT 0,1000";
$q = $db->query($query);
$result = $q->fetchALL(PDO::FETCH_ASSOC);
foreach ($result as $domain){
    $dom = Sys::getOneData('Parc','Domain/Url='.$domain['Url']);
    echo "-> " . $domain['Url'] . " ****** \r\n";
    if (!$dom) {
        $dom = genericClass::createInstance('Parc', 'Domain');
        $dom->initFromArray($domain);
        unset($dom->Id);
        $dom->updateOnSave = false;
    }
    $dom->LdapID = "";
    $dom->LdapDN = "";
    $dom->LdapTms = "";
    $dom->Save();
    //A
    $queryA= "SELECT * FROM `parc-Parc-Subdomain` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
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
            unset($s->Id);
        }
        $s->Nom = $subdomain['Url'];
        if ($s->Url=='A:'){
            $s->Url = '';
        }else{
            $s->Url = $pref;
        }
        $s->LdapID = "";
        $s->LdapDN = "";
        $s->LdapTms = "";
        $s->Save();
    }

    //AAA
    $queryAAA= "SELECT * FROM `parc-Parc-AAA` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qAAA = $db->query($queryAAA);
    $resultAAA = $qAAA->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultAAA as $r){
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/AAA/Nom='.$r['Nom']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'AAA');
            $s->initFromArray($r);
            $s->addParent($dom);
            unset($s->Id);
        }
        if (empty($s->Url)){
            $s->Url = $dom->Url.'.';
        }
        $s->LdapID = "";
        $s->LdapDN = "";
        $s->LdapTms = "";
        $s->Save();
    }

    //CNAME
    $queryCNAME= "SELECT * FROM `parc-Parc-CNAME` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qCNAME = $db->query($queryCNAME);
    $resultCNAME = $qCNAME->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultCNAME as $r){
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/CNAME/Dnsdomainname='.$r['Dnsdomainname']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'CNAME');
            $s->initFromArray($r);
            $s->addParent($dom);
            unset($s->Id);
        }
        $s->LdapID = "";
        $s->LdapDN = "";
        $s->LdapTms = "";
        $s->Save();
    }

    //MX
    $queryMX= "SELECT * FROM `parc-Parc-MX` WHERE DomainId=".$domain['Id']." LIMIT 0,1000";
    $qMX = $db->query($queryMX);
    $resultMX = $qMX->fetchALL(PDO::FETCH_ASSOC);
    foreach ($resultMX as $r){
        echo "---> ".$r['Nom']."\r\n";
        $s = Sys::getOneData('Parc','Domain/'.$dom->Id.'/MX/Dnscname='.$r['Dnscname']);
        if (!$s) {
            $s = genericClass::createInstance('Parc', 'MX');
            $s->initFromArray($r);
            $s->addParent($dom);
            unset($s->Id);
        }
        if (empty($s->Dnsdomainname)){
            $s->Dnsdomainname = $dom->Url.'.';
        }
        $s->LdapID = "";
        $s->LdapDN = "";
        $s->LdapTms = "";
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
            $s->addParent($dom);
            unset($s->Id);
        }
        if (empty($s->Dnsdomainname)){
            $s->Dnsdomainname = $dom->Url.'.';
        }
        if (empty($s->Dnscname)){
            $s->Dnscname = 'ns'.$z.'.azko.fr.';
        }
        $s->LdapID = "";
        $s->LdapDN = "";
        $s->LdapTms = "";
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
            $s->addParent($dom);
            unset($s->Id);
        }
        $s->LdapID = "";
        $s->LdapDN = "";
        $s->LdapTms = "";
        $s->Save();
    }
}


