<?php
//décoration
$bc = new BashColors();

//connexion ancien serveur mysql
$db = new PDO('mysql:host=192.168.100.50;dbname=parc', 'root', '*********', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$serveur='192.168.120.55';
/**
 * MIGRE
 * aurelphotographe;aurelphotographe;aurelphotographe
 * classequine-expert:classequine-expert:classequine2017
 * courtepointe;courtepointe;courtepointe
 * ffpm;ffpm;ffpm+ffpm_dev+ffpm_intranet
 * gedales;gedales;gedales+thebestprice ///a voir avec alex ... 2 boutiques sur un mutu
 * protemporis;protemporis;protemporis
 * pescatore.fr;boxsete;pescatore_updated+pescatore+pescatore_dev
 * magickart;magickart;magickart;sql2.eng.systems
 * keole2;keole;keole+blogkeole+keole_support *
 * DOMAINE A MODIFIER
 * brumizeo;brumizeo;brumizeo_dev
 * cimenterie2;cimenterie2;cimenterie2
 * descapsules;descapsules;descapsules
 * greenkub;greenkub;greenkub+greenkub_app+greenkub_callc+greenkub_dev+greenkub_logeen+greenkub_vivasof
 * trouvetonvigneron.com;trouvetonvigneron;trouvetonv_wordpress+trouvetonvignero
 * supersoignant;supersoignant;supersoignant
 * inrisonline;autoecoleinris;inris-online+inris-online-dev
 *
 * TO DELETE
 * bioccinelle;bioccinelle;bioccinelle
 * cimenterie;cimenterie;cimenterie
 * coursicab;coursicab;coursicab
 * dev-keole;dev-keole;dev-keole
 * distillerie;distillerie;distillerie
 */
//syntaxe NOM_HEB;NOM_CLIENT;BDD1[+BDD2...][;SRV_SQL]
//blogleboeuf;legoutduboeuf;wdp;192.168.120.55
$csv= "
legoutduboeuf;legoutduboeuf;lgdb_dolibarr+lgdb_prestashop+lgdb_prestashop_1.7;192.168.120.55
";
$result = explode(PHP_EOL,$csv);
$total = sizeof($result)-2;
$i=0;
foreach ($result as $org){
    if (empty(trim($org)))continue;
    $i++;
    //if ($i!=45)continue;
    $fields = explode(';',$org);
    $fields[2] = explode('+',$fields[2]);
    list($host,$cli,$bdds,$mysqlsrv) = $fields;
    //test existence
    $nb = Sys::getOneData('Parc','Instance/InstanceNom='.substr('inst-'.$host,0,32));
    echo $bc->getColoredString("-> [$i / $total] ".$host."\n",'green');

    if (!$nb){
        echo $bc->getColoredString("    -> CREATING INSTANCE ".$host, 'red');
        //recherche server Web par defaut
        $srv = Sys::getOneData('Parc','Server/defaultWebServer=1');
        //création de l'instance
        $inst = genericClass::createInstance('Parc', 'Instance');
        //définition du client
        $client = Sys::getOneData('Parc','Client/NomLDAP='.$cli);
        if ($client) $inst->addParent($client);
        $inst->Nom = $host;
        $inst->InstanceNom = 'inst-' . $host;
        $inst->Type = 'prod';
        $inst->Actif = true;
        //$inst->Ssl = ($ssl)?true:false;
        $inst->PHPVersion = '7.0.29';
        $inst->Plugin = 'Vide';
        echo $bc->getColoredString("    -> SAVING ... ", 'red');
        if (!$inst->Save()) {
            //continue;
            print_r($inst);
            die('Erreur de création instance');
        }
        echo $bc->getColoredString(" OK \n", 'green');
    }else {
        $inst = $nb;
        //définition du client
        $client = Sys::getOneData('Parc','Client/NomLDAP='.$cli);
        if ($client) $inst->addParent($client);
        $inst->softSave();
    }
    //récupération de l'host
    $hos = $inst->getOneParent('Host');
    //récupération du serveur
    $srv = $hos->getOneParent('Server');

    //récupération des vhosts et certifs
    $query = "SELECT * FROM `parc-Parc-Host` as hs LEFT JOIN `parc-Parc-Apache` as ap ON hs.Id = ap.HostId WHERE ap.Actif = 1 and hs.Nom='".$host."';";
    $q = $db->query($query);
    $aps = $q->fetchALL(PDO::FETCH_ASSOC);
    foreach ($aps as $ap){
        //recuperation du vhost
        $apache = Sys::getOneData('Parc','Host/'.$hos->Id.'/Apache/ApacheServerName='.$ap['ApacheServerName'],0,1,'ASC','Id');
        if (!$apache){
            echo $bc->getColoredString("    -> CREATION VHOST ".$ap['ApacheServerName']."\n", 'green');
            $apache = genericClass::createInstance('Parc','Apache');
            $apache->initFromArray($ap);
            $apache->DocumentRoot = str_replace('/home/'.$host.'/','',$apache->DocumentRoot);
            $apache->DocumentRoot = str_replace('/home/'.$host,'',$apache->DocumentRoot);
            $apache->ProxyCache = true;
            $apache->addParent($hos);
            unset($apache->Id);
            unset($apache->LdapID);
            unset($apache->LdapDN);
            unset($apache->LdapTms);
            $apache->Save();
        }else{
            $apache->SslCertificate = $ap['SslCertificate'];
            $apache->SslCertificateKey = $ap['SslCertificateKey'];
            $apache->SslExpiration = $ap['SslExpiration'];
            $apache->Save();
            //echo 'instance ok ';
        }
    }
    //récupération des accès ftps
    $query = "SELECT * FROM `parc-Parc-Host` as hs LEFT JOIN `parc-Parc-Ftpuser` as fu ON hs.Id = fu.HostId WHERE hs.Nom='".$host."';";
    $q = $db->query($query);
    $ftps = $q->fetchALL(PDO::FETCH_ASSOC);
    foreach ($ftps as $ftp){
        //recuperation du vhost
        $ftpuser = Sys::getOneData('Parc','Host/'.$hos->Id.'/Ftpuser/Identifiant='.$ftp['Identifiant'],0,1,'ASC','Id');
        if (!$ftpuser){
            echo $bc->getColoredString("    -> CREATION FTPUSER ".$ftp['Identifiant']."\n", 'green');
            $ftpuser = genericClass::createInstance('Parc','Ftpuser');
            $ftpuser->initFromArray($ftp);
            $ftpuser->addParent($hos);
            $ftpuser->DocumentRoot = str_replace('/home/'.$host.'/','',$ftpuser->DocumentRoot);
            $ftpuser->DocumentRoot = str_replace('/home/'.$host,'',$ftpuser->DocumentRoot);
            unset($ftpuser->Id);
            unset($ftpuser->LdapID);
            unset($ftpuser->LdapDN);
            unset($ftpuser->LdapTms);
            $ftpuser->Save();
        }
    }

    //creation des bdds
    foreach ($bdds as $bdd){
        $base = Sys::getOneData('Parc','Host/'.$hos->Id.'/Bdd/Nom='.$bdd,0,1,'ASC','Id');
        if (!$base){
            echo $bc->getColoredString("    -> BDD ".$bdd."\n", 'green');
            $base = genericClass::createInstance('Parc','Bdd');
            $base->Nom = $bdd;
            $base->addParent($hos);
            $base->Save();
        }
        //if ($base->tmsEdit<time()-3600) {
            echo $bc->getColoredString("      -> SQL DUMP ".$bdd." ... ", 'red');
            //importation de la base de donnée
            if ($mysqlsrv=='192.168.120.55'){
                $cmd = 'mysqldump -h 192.168.120.55 -u root -p"*********" ' . $bdd . ' | sed -e "s/^UNLOCK.*\$//"   | sed -e "s/^LOCK TABLE.*\$//"  | sed -e "s/MyISAM/InnoDB/i"  > /tmp/' . $bdd.'.sql';
                echo $bc->getColoredString(" DUMP ... ", 'red');
                exec($cmd);
                $cmd = 'cat /tmp/'.$bdd.'.sql  |  mysql -h 10.100.210.19 -u root -p********* ' . $bdd;
                //echo $cmd."\n";
                exec($cmd);
                echo $bc->getColoredString(" INSERT OK " . "\n", 'green');
                $base->Save();
            }else {
                $cmd = 'mysqldump -h 192.168.100.50 -u root -p********* ' . $bdd . '  | sed -e "s/^UNLOCK.*\$//"   | sed -e "s/^LOCK TABLE.*\$//" | sed -e "s/MyISAM/InnoDB/i" > /tmp/'.$bdd.'.sql';
                echo $bc->getColoredString(" DUMP ... ", 'red');
                exec($cmd);
                $cmd = 'cat /tmp/'.$bdd.'.sql  |  mysql -h 10.100.210.5 -u root -p********* ' . $bdd;
                //echo $cmd."\n";
                exec($cmd);
                echo $bc->getColoredString(" INSERT OK " . "\n", 'green');
                $base->Save();
            }
        //}
    }

    //excution rsync
    //importation de la base de donnée
    try {
        //$cmd = 'rsync -avz -e \'ssh -i /root/.ssh/id_rsa\' root@'.$serveur.':/home/'.$host.'/ /home/'.$hos->NomLDAP.'/ --exclude backups --exclude logs --exclude cgi-bin';
        $cmd = 'ssh -R localhost:50000:'.$srv->InternalIP.':22 root@'.$serveur.' \'rsync -e "ssh -p 50000 -i /root/.ssh/id_rsa" -vuar /home/'.$host.'/ localhost:/home/'.$hos->NomLDAP.'/ --exclude backups --exclude logs --exclude cgi-bin\'';
        echo $bc->getColoredString("       -> RUN RSYNC ".$cmd , 'yellow');
        $out = Parc::localExec($cmd);
        echo $bc->getColoredString(" OK "."\n", 'green');
        echo $bc->getColoredString("       -> SETTING RIGHTS " , 'yellow');
        $out = $srv->remoteExec('chown '.$hos->NomLDAP.':users /home/'.$hos->NomLDAP.' -R');
        echo $bc->getColoredString(" OK "."\n", 'green');
    }catch(Exception $e){
        echo $bc->getColoredString(" ERREUR" . "\n".$e->getMessage()."\n".$cmd."\n", 'red');
    }

    //détection du cms
    if ($srv->fileExists('/home/'.$hos->NomLDAP.'/www/wp-config.php')){
        //c'est un wordpress
        $inst->Plugin = 'Wordpress';
        echo $bc->getColoredString("    -> Wordpress\n",'green');
    }else if ($srv->fileExists('/home/'.$hos->NomLDAP.'/www/Conf/General.conf')){
        //c'est un kobeye
        $inst->Plugin = 'KobEye';
        echo $bc->getColoredString("    -> KobEye\n",'green');
    }else if ($srv->fileExists('/home/'.$hos->NomLDAP.'/www/config/settings.inc.php')){
        //c'est un prestashop
        $inst->Plugin = 'Prestashop';
        echo $bc->getColoredString("    -> Prestashop\n",'green');
    }

    $inst->softSave();
    $inst->rewriteConfig();
}
