<?php
//décoration
$bc = new BashColors();

//connexion ancien serveur mysql
$db = new PDO('mysql:host=192.168.100.50;dbname=parc', 'root', 'zH34Y6u5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$serveur='ws1.eng.systems';
/*$csv = "
acei;acei;acei
acrotaille;acrotaille;acrotaille
aimetti-auteur;aimetti-auteur;aimetti-auteur
ama;rgsystem;ama+wp_amaassurances+wp_amaassurancesblog
amisdufondsmedar;amisdufondsmedar;amisdufondsmedar
appartementcabourg;appartementcabourg;appartement-cabo
aqualiss;aqualiss;aqualiss
aquazurservices;aquazurservi;aquazurservices
atelier-corps;atelier-corps-et-mouvemen;atelier-corps
autoecole;autoecoledef;autoecole
autoecoleamd;autoecoleamd;autoecoleamd
autoecole-easy;autoecole-ea;autoecole-easy
autoecoleinris;autoecoleinr;autoecoleinris
automotoecole;automotoecol;automotoecole
bacotec;bacotec;bacotec
barocante;barocante;barocante
bertrand;bertrandimmo;bertrand+bertrand2017
bgelr;bgelr;bgelr
boxsete;pescatore;boxsete_boxlocation+boxsete_chezpescatore
byvirginie;byvirginie;byvirginie
camarguevtc;camargue-vtc;camarguevtc
camassel;camassel;camassel
cap-ocean;cap-ocean;cap-ocean;sql2.eng.systems
carrosserie-reis;carrosserie-;carrosserie-reis
cdefi;cdefi;cdefi
ce3d;ce3d;ce3d
ceciledesserle;ceciledesserle;ceciledesserle
permis-bateau;permis-batea;cercle-nautique
chape-liquide;chape-liquide;chape-liquide
chefsdoc;chefsdoc;chefsdoc
citeoingenierie;citeo;citeo-ingenierie
clamousemetaller;clamousemeta;clamousemetaller
code23;code23;code23
collectif-saint;collectif-sa;collectif-saint
dagobafilms;dagobafilms;dagobafilms
dm-detect;dm-detect;dm-detect
dronerealisation;dronerealisa;dronerealisation
dumartinetj;dumartinet;dumartinetj
easypanneau;easypanneau;easypanneau
ecolederaseteurs;ecolederaset;ecolederaseteurs
ecopub;ecopub;ecopub
editionsmo;editions-monemvassia;editionsmo
epconsulting;ep.consultin;epconsulting
espace-proprete;espace-propr;espace-proprete
ethique-perfusi;ethique-perf;ethique-perfusi
expert-comptable;expert-compt;expert-comptable
geodev;geodev;geodev
grandcreme;grandcreme;grandcreme+grandcreme_pydio
groupevet;groupevet;groupevet
hydrosol;hydrosol;hydrosol
idh-montpellier;idh-montpell;idh-montpellier
insightcom;insightcom;insightcom
intrasens;intrasens;intrasens
iplusmedia.eu;iplusmedia.eu;iplusmedia.eu
labanane;labanane;labanane
laboratoire-val;laboratoire-;laboratoire-val
lace-restaurant;lace-restaurant.fr;lace-restaurant
laprimavera;laprimavera-;laprimavera
lepasseurdemots;lepasseurdem;lepasseurdemots
maf82;montaubanath;maf82
maformation;maformation;maformation
mara-pro;mara-pro;mara-pro
mediationmontpellier;mediationmon;mediationmontpel
mldurand;mldurand;mldurand
mobilygo;mobilygo;mobilygo+mobilygo_2014
montpellierfans;montpellierf;montpellierfans
murviel;murviel;murviel
nino-robotics;nino-robotic;nino-robotics+nino-robotics-dev+nino-robotics-sup
nutrition-expert;nutrition-ex;nutrition-expert
permis-bateau;permis-batea;permis-bateau
perseides-courta;perseides-co;perseides-courta
pianoconcertino;pianoconcert;pianoconcertino
pizzajerome;pizzajerome;pizzajerome
privilegeberricar;privilegeber;privilegeberrica
psychotherapiesatm;christinebuo;psychotherapiesat
pushrdv;pushrdv;pushrdv
quentinmultiservices;quentinmultiservices;quentinmultiserv
racinespubliques;racinespubliques;racinespubliques
rdksolutions;rdksolutions;rdksolutions
residencelehome;residenceleh;residencelehome
rws-relocation;rws-relocati;rws-relocation
safetygreen;safetygreen;safetygreen
secreteam;secreteam;secreteam
selfcopy;selfcopy;selfcopy
serenity-services;serenity-services;serenity-service
sexologue;sexologue;sexologue
sinergiasud;sinergiasud;sinergiasud
snap-pole-emploi;snap-pole-em;snap-pole-emploi
spiruline;spirulineala;spiruline
sudmarquage;sudmarquage;sudmarquage
sudvtc;sudvtc;sudvtc
technifer;technifer;technifer
tennisforever;tennisforeve;tennisforever+tennisforever_reservation
tisseyre-avocats;tisseyre-avo;tisseyre-avocats
travaux-speciaux;travaux-spec;travaux-speciaux
veterinaire-lang;veterinaire-languedocia;veterinaire-lang
veterinaire-veto;veterinaire-vetocia;veterinaire-veto
vmid;vmid;vmid";
*/
$csv= "
psychotherapieintegrative;psychotherap;psychotherapiein
";
$result = explode(PHP_EOL,$csv);
$total = sizeof($result);
$i=0;
foreach ($result as $org){
    if (empty(trim($org)))continue;
    $i++;
    //if ($i!=45)continue;
    $fields = explode(';',$org);
    $fields[2] = explode('+',$fields[2]);
    list($host,$cli,$bdds,$mysqlsrv) = $fields;
    //test existence
    $nb = Sys::getOneData('Parc','Instance/InstanceNom=instance-'.$host);
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
        $inst->InstanceNom = 'instance-' . $host;
        $inst->Type = 'prod';
        $inst->Actif = true;
        //$inst->Ssl = ($ssl)?true:false;
        $inst->PHPVersion = '7.0.29';
        $inst->Plugin = 'Vide';
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
            echo $bc->getColoredString("      -> SQL DUMP ... ", 'red');
            //importation de la base de donnée
            if ($mysqlsrv=='sql2.eng.systems'){
                $cmd = 'mysqldump -h 192.168.100.53 -u root -p"zH34Y6u5;" ' . $bdd . ' | sed -e "s/^UNLOCK.*\$//"   | sed -e "s/^LOCK TABLE.*\$//"  | sed -e "s/MyISAM/InnoDB/i"  |  mysql -h 192.168.160.5 -u root -pzH34Y6u5 ' . $bdd;
            }else $cmd = 'mysqldump -h 192.168.100.50 -u root -pzH34Y6u5 ' . $bdd . '  | sed -e "s/^UNLOCK.*\$//"   | sed -e "s/^LOCK TABLE.*\$//" | sed -e "s/MyISAM/InnoDB/i"  |  mysql -h 192.168.160.5 -u root -pzH34Y6u5 ' . $bdd;
            echo $cmd."\n";
            exec($cmd);
            echo $bc->getColoredString(" OK " . "\n", 'green');
            $base->Save();
        //}
    }

    //excution rsync
    //importation de la base de donnée
    try {
        $cmd = 'rsync -avz -e \'ssh -i /root/.ssh/id_rsa\' root@'.$serveur.':/home/'.$host.'/ /home/'.$hos->NomLDAP.'/ --exclude backups --exclude logs --exclude cgi-bin';
        echo $bc->getColoredString("       -> RUN RSYNC " , 'yellow');
        $out = $srv->remoteExec($cmd);
        echo $bc->getColoredString(" OK "."\n", 'green');
        echo $bc->getColoredString("       -> SETTING RIGHTS " , 'yellow');
        $out = $srv->remoteExec('chown '.$hos->NomLDAP.':users /home/'.$hos->NomLDAP.' -R');
        echo $bc->getColoredString(" OK "."\n", 'green');
    }catch(Exception $e){
        echo $bc->getColoredString(" ERREUR" . "\n".$e->getMessage()."\n".$cmd."\n", 'red');
        die();
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
