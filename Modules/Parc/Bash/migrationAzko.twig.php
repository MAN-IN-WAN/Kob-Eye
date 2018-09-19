<?php
//décoration
$bc = new BashColors();

//connexion ancien serveur mysql
$db = new PDO('mysql:host=192.168.100.50;dbname=parc', 'root', 'zH34Y6u5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$csv = "
acei;acei;acei
acrotaille;acrotaille;acrotaille
ah-avocats;ah-avocats;ah-avocats
aimetti-auteur;aimetti-auteur;aimetti-auteur
alex;engsystems;alex
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
cap-ocean;cap-ocean;cap-ocean
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
driveo;mwsolutions;driveo
dronerealisation;dronerealisa;dronerealisation
dumartinetj;dumartinet;dumartinetj
easypanneau;easypanneau;easypanneau
ecolederaseteurs;ecolederaset;ecolederaseteurs
ecopub;ecopub;ecopub
editionsmo;editions-monemvassia;editionsmo
enguer;enguer;enguer
epconsulting;ep.consultin;epconsulting
espace-proprete;espace-propr;espace-proprete
ethique-perfusi;ethique-perf;ethique-perfusi
expert-comptable;expert-compt;expert-comptable
funkyfurnish;funkyfurnish;funkyfurnish
geodev;geodev;geodev
gestion;enguer;enguergest
gestion2;enguer;enguergest2
grandcreme;grandcreme;grandcreme+grandcreme_pydio
groupevet;groupevet;groupevet
hconsulting;hconsulting;hconsulting
hhealth-group;hhealth-grou;hhealth-group
hydrosol;hydrosol;hydrosol
idh-montpellier;idh-montpell;idh-montpellier
insightcom;insightcom;insightcom
intrasens;intrasens;intrasens
iplusmedia.eu;iplusmedia.eu;iplusmedia.eu
jsaavocats;jsaavocats;jsaavocats
labanane;labanane;labanane
laboratoire-val;laboratoire-;laboratoire-val
lace-restaurant;lace-restaurant.fr;lace-restaurant
lagrignotte;lagrignotte;lagrignotte
laprimavera;laprimavera-;laprimavera
lemasdemestre;lemasdemestr;lemasdemestre
lepasseurdemots;lepasseurdem;lepasseurdemots
lexnot;ws1;lexnot.fr
maf82;montaubanath;maf82
maformation;maformation;maformation
mara-pro;mara-pro;mara-pro
mcsini;mcsini;mcsini
mediation-consommation;mediationmon;mediationconso
mediationmontpellier;mediationmon;mediationmontpel
mldurand;mldurand;mldurand
mobilygo;mobilygo;mobilygo+mobilygo_2014
montpellierfans;montpellierf;montpellierfans
murviel;murviel;murviel
mw;mwsolutions;mw+mwsolutions
nino-robotics;nino-robotic;nino-robotics+nino-robotics-dev+nino-robotics-sup
nutrition-expert;nutrition-ex;nutrition-expert
ouiche-lorraine;ouiche-lorra;ouiche-lorraine
permis-bateau;permis-batea;permis-bateau
perseides-courta;perseides-co;perseides-courta
pianoconcertino;pianoconcert;pianoconcertino
pizzajerome;pizzajerome;pizzajerome
prescriptionnature;prescriptionnature;prescriptionnature
privilegeberricar;privilegeber;privilegeberrica
pronotrot;pronotrot;pronotrot
psychotherapieintegrative;psychotherap;psychotherapiein
psychotherapiesatm;christinebuo;psychotherapiesat
pushrdv;pushrdv;pushrdv
quentinmultiservices;quentinmultiservices;quentinmultiserv
racinespubliques;racinespubliques;racinespubliques
rdksolutions;rdksolutions;rdksolutions
residencelehome;residenceleh;residencelehome
rws-relocation;rws-relocati;rws-relocation
safetygreen;safetygreen;safetygreen
scproux;scproux;scproux
secreteam;secreteam;secreteam
selfcopy;selfcopy;selfcopy
serenity-services;serenity-services;serenity-service
sexologue;sexologue;sexologue
sinergiasud;sinergiasud;sinergiasud
snap-pole-emploi;snap-pole-em;snap-pole-emploi
spiruline;spirulineala;spiruline
sudmarquage;sudmarquage;sudmarquage
sudvtc;sudvtc;sudvtc
sweet-home34;sweet-home34;sweet-home34
technifer;technifer;technifer
tennisforever;tennisforeve;tennisforever+tennisforever_reservation
terre2sens;terre2sens;terre2sens
tisseyre-avocats;tisseyre-avo;tisseyre-avocats
travaux-speciaux;travaux-spec;travaux-speciaux
veterinaire-lang;veterinaire-languedocia;veterinaire-lang
veterinaire-veto;veterinaire-vetocia;veterinaire-veto
vmid;vmid;vmid";

$result = explode(PHP_EOL,$csv);
$i=0;
foreach ($result as $org){
    if (empty(trim($org)))continue;
    $i++;
    $fields = explode(';',$org);
    $fields[2] = explode('+',$fields[2]);
    list($host,$cli,$bdds) = $fields;
    //test existence
    $nb = Sys::getOneData('Parc','Instance/InstanceNom=instance-'.$host);
    echo $bc->getColoredString("-> [$i] ".$host."\n",'green');

    if (!$nb){
        echo $bc->getColoredString("    -> CREATING INSTANCE ".$host, 'red');
        //recherche server Web par defaut
        $srv = Sys::getOneData('Parc','Server/defaultWebServer=1');
        //création de l'instance
        $inst = genericClass::createInstance('Parc', 'Instance');
        //définition du client
        $client = Sys::getOneData('Parc','Client/NomLDAP='.$host);
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
        if ($base->tmsEdit<time()-3600) {
            echo $bc->getColoredString("      -> SQL DUMP ... ", 'red');
            //importation de la base de donnée
            $cmd = 'mysqldump -h 192.168.100.50 -u root -pzH34Y6u5 ' . $bdd . ' | sed -e "s/MyISAM/InnoDB/i"  |  mysql -h 192.168.160.4 -u root -pzH34Y6u5 ' . $bdd;
            exec($cmd);
            echo $bc->getColoredString(" OK " . "\n", 'green');
            $base->Save();
        }
    }

    //excution rsync
    //importation de la base de donnée
    $cmd = 'rsync -avz -e \'ssh -i /root/.ssh/id_rsa\' root@ws1.eng.systems:/home/'.$host.'/ /home/instance-'.$host.'/ --exclude backups --exclude logs --exclude cgi-bin';
    try {
        echo $bc->getColoredString("       -> RUN RSYNC " , 'yellow');
        $out = $srv->remoteExec($cmd);
        echo $bc->getColoredString(" OK "."\n".$out."\n", 'green');
        echo $bc->getColoredString("       -> SETTING RIGHTS " , 'yellow');
        $out = $srv->remoteExec('chown instance-'.$host.':users /home/instance-'.$host.' -R');
        echo $bc->getColoredString(" OK "."\n".$out."\n", 'green');
    }catch(Exception $e){
        echo $bc->getColoredString(" ERREUR" . "\n".$e->getMessage()."\n".$cmd."\n", 'red');
        die();
    }
    echo $bc->getColoredString(" OK " . "\n", 'green');

    //si c'est un wordpress on refait la conf
    $conf = $srv->getFileContent('/home/instance-'.$host.'/www/wp-config.php');
    if (!empty($conf)){
        echo $bc->getColoredString("       -> CONFIG WORDPRESS \n" , 'yellow');
        $conf = preg_replace('#define\(\'DB_USER\', \'(.*)\'\);#','define(\'DB_USER\', \'instance-'.$host.'\');',$conf);
        $conf = preg_replace('#define\(\'DB_PASSWORD\', \'(.*)\'\);#','define(\'DB_PASSWORD\', \''.$hos->Password.'\');',$conf);
        $conf = preg_replace('#define\(\'DB_HOST\', \'(.*)\'\);#','define(\'DB_HOST\', \'db.maninwan.fr'.'\');',$conf);
        $srv->putFileContent('/home/instance-'.$host.'/www/wp-config.php',$conf."\r\nif(\$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    \$_SERVER['HTTPS'] = 'on';
    \$_SERVER['SERVER_PORT'] = 443;
}
");
        $htaccess = $srv->getFileContent('/home/instance-'.$host.'/www/.htaccess');
        $htaccess = preg_replace('#RewriteCond %\{HTTPS\} off#','RewriteCond %{HTTP:X-Forwarded-Proto} !https',$htaccess);
        $srv->putFileContent('/home/instance-'.$host.'/www/.htaccess',$htaccess);
    }
}
