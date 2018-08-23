<?php
//décoration
$bc = new BashColors();
//connexion ancien serveur mysql
$db = new PDO('mysql:host=192.168.100.2;dbname=azkocms_common', 'root', '125iAS34470', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbke = new PDO('mysql:host=192.168.100.2;dbname=parc', 'root', '125iAS34470', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$dbke->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//On boucle sur les orgas contenant des sites
//$query = "SELECT o.*,COUNT(s.site_id) as NBSITE FROM organisation as o LEFT JOIN site as s ON o.org_id = s.site_org_id GROUP BY o.org_id HAVING NBSITE>0;";
$query = "SELECT o.* FROM organisation as o LIMIT 660,40";
$q = $db->query($query);

$servs = array(7,8,9);
//on remet le premier serveur en serveur web par défaut
/*$s = Sys::getOneData('Parc','Server/defaultWebServer=1');
$s->defaultWebServer = false;
$s->Save();
$srv = Sys::getOneData('Parc','Server/'.$servs[0]);
$srv->defaultWebServer = true;
$srv->Save();*/

$result = $q->fetchALL(PDO::FETCH_ASSOC);
$i=0;
foreach ($result as $org){
    $i++;
    //gestion server web par defaut
    /*if ($i==250||$i==500) {
        echo $bc->getColoredString("***** CHANGEMENT SERVEUR WEB PAR DEFAUT *****"."\n",'green');

        //on remet le premier serveur en serveur web par défaut
        $s = Sys::getOneData('Parc', 'Server/defaultWebServer=1');
        $s->defaultWebServer = false;
        $s->Save();
        $srv = Sys::getOneData('Parc', 'Server/' . $servs[($i==250)?1:2]);
        $srv->defaultWebServer = true;
        $srv->Save();
    }*/

    //test existence
    $nb = Sys::getCount('Parc','Instance/InstanceNom=azkocms_org_'.$org['org_id']);
    if ($nb) continue;
    echo $bc->getColoredString("-> [$i] ".$org["org_nom"]."\n",'green');
    //récupération des sites et domaines
//    $query = "SELECT *,COUNT(s.site_id) as NBDOM FROM site as s LEFT JOIN domaine as d ON s.site_id = d.dom_site_id WHERE s.site_org_id = '".$org['org_id']."' GROUP BY s.site_id HAVING NBDOM>1;";
    $query = "SELECT * FROM site as s LEFT JOIN domaine as d ON s.site_id = d.dom_site_id WHERE s.site_org_id = '".$org['org_id']."';";
    $q = $db->query($query);
    $resu = $q->fetchALL(PDO::FETCH_ASSOC);
    $doms = "";
    $search_doms = '';
    foreach ($resu as $dom){
        if (empty($dom["dom_name"]))continue;
        echo $bc->getColoredString("  -> ".$dom["dom_name"]."\n",'blue');
        $doms.=$dom['dom_name']."\r\n";
        $search_doms .=  (!empty($search_doms)?" OR ":" ")."`ApacheServerAlias` LIKE '%".$dom['dom_name']."%' OR `ApacheServerName` = '".$org['dom_name']."'";
    }
    $doms = trim($doms);
    $ssl = false;
    //recherche du vhost et des certifs
    echo $bc->getColoredString("    -> LOOKING FOR SSL CONFIG " . "\n", 'yellow');
    if (!empty($search_doms)) {
        $query = "SELECT *  FROM `parc-Parc-Apache` WHERE " . $search_doms . ";";
        //echo $bc->getColoredString("    -> SSL " . $query . "\n", 'yellow');
        $q = $dbke->query($query);
        $apaches = $q->fetchALL(PDO::FETCH_ASSOC);
        foreach ($apaches as $apache) {
            if ($apache["Ssl"] == 1) {
                echo $bc->getColoredString("    -> SSL FOUND " . $apache["ApacheServerName"] . "\n", 'yellow');
                $ssl = true;
                $cert = $apache["SslCertificate"];
                $key = $apache["SslCertificateKey"];
                $expire = $apache["SslExpiration"];
            }
        }
    }
    echo $bc->getColoredString("    -> CREATING INSTANCE ", 'red');
    //création de l'instance
    $inst = genericClass::createInstance('Parc','Instance');
    $inst->Nom = $org["org_nom"];
    $inst->InstanceNom = 'azkocms_org_'.$org['org_id'];
    $inst->ServerAlias = $doms;
    $inst->Type = 'prod';
    $inst->Password = $org['org_db_pass'];
    $inst->Actif = true;
    //$inst->Ssl = ($ssl)?true:false;
    $inst->PHPVersion = '7.0.29';
    $inst->Plugin = 'AzkoFront';
    if (!$inst->Save()) {
        continue;
        //print_r($inst->Error);
        //die('Erreur de création instance');
    }
    echo $bc->getColoredString(" OK \n", 'green');
    if ($ssl) {
        //recuperation du vhost
        $host = $inst->getOneParent('Host');
        $apache = $host->getOneChild('Apache');
        //injection du certif
        $apache->Ssl = true;
        $apache->SslCertificate = $cert;
        $apache->SslCertificateKey = $key;
        $apache->SslExpiration = $expire;
        $apache->SslMethod = 'Letsencrypt';
        $apache->Save(false,true);
        $inst->EnableSsl = true;
        echo $bc->getColoredString("    -> SSL ENABLED ". "\n", 'green');
    }else echo $bc->getColoredString("    -> SSL NOT DETECTED ". "\n", 'pink');
    if (!$inst->Save()) {
        print_r($inst->Error);
        die('Erreur de création instance');
    }


    echo $bc->getColoredString("  -> SQL DUMP ... ", 'red');
    //importation de la base de donnée
    $cmd = 'mysqldump -h 192.168.100.2 -u root -p125iAS34470 azkocms_org_'.$org['org_id'].' |  mysql -h 192.168.200.5 -u root -pwCENJbD9DUz76Ty4 azkocms_org_'.$org['org_id'];
    exec($cmd);
    echo $bc->getColoredString(" OK ". "\n", 'green');
}
