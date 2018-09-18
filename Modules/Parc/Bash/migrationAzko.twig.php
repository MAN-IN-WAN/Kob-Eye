<?php
//décoration
$bc = new BashColors();

//connexion ancien serveur mysql
$dbke = new PDO('mysql:host=192.168.100.50;dbname=parc', 'root', 'zH34Y6u5', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$dbke->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    $i++;
    $fields = explode(';',$org);
    $fields[3] = explode('+',$fields[3]);
    list($host,$client,$bdds) = $fields;

    //test existence
    $nb = Sys::getOneData('Parc','Instance/InstanceNom=instance-'.$host);
    if ($nb) continue;
    echo $bc->getColoredString("-> [$i] ".$host."\n",'green');

    if (!$nb){
        echo $bc->getColoredString("    -> CREATING INSTANCE ".$org['org_id'], 'red');
        //création de l'instance
        $inst = genericClass::createInstance('Parc', 'Instance');
        $inst->Nom = $org["org_nom"];
        $inst->InstanceNom = 'azkocms_org_' . $org['org_id'];
        $inst->ServerAlias = $doms;
        $inst->Type = 'prod';
        $inst->Password = $org['org_db_pass'];
        $inst->Actif = true;
        //$inst->Ssl = ($ssl)?true:false;
        $inst->PHPVersion = '7.0.29';
        $inst->Plugin = 'Vide';
        if (!$inst->Save()) {
            //continue;
            print_r($inst->Error);
            die('Erreur de création instance');
        }
        echo $bc->getColoredString(" OK \n", 'green');
    }else $inst = $nb;


    //récupération des vhosts et certifs
    $query = "SELECT * FROM `parc-Parc-Host` as hs LEFT JOIN `parc-Parc-Apache` as ap ON hs.Id = ap.HostId WHERE ap.Enabled = 1;";
    $q = $db->query($query);
    $aps = $q->fetchALL(PDO::FETCH_ASSOC);
    foreach ($aps as $dom){
        //TEST SSL ORGA
        if ($ssl) {
            //recuperation du vhost
            $host = $inst->getOneParent('Host');
            $apache = Sys::getOneData('Parc','Host/'.$host->Id.'/Apache',0,1,'ASC','Id');
            //injection du certif
            $apache->Ssl = true;
            $apache->SslCertificate = $cert;
            $apache->SslCertificateKey = $key;
            $apache->SslExpiration = $expire;
            $apache->SslMethod = 'Letsencrypt';
            $apache->Save(false,true);
            $inst->EnableSsl = true;
            echo $bc->getColoredString("    -> SSL ENABLED ". "\n", 'green');
        }
    }


    if ($inst->tmsEdit<time()-86400) {
        echo $bc->getColoredString("  -> SQL DUMP ... ", 'red');
        //importation de la base de donnée
        $cmd = 'mysqldump -h 192.168.100.2 -u root -p125iAS34470 azkocms_org_' . $org['org_id'] . ' |  mysql -h 192.168.200.5 -u root -pwCENJbD9DUz76Ty4 azkocms_org_' . $org['org_id'];
        exec($cmd);
        echo $bc->getColoredString(" OK " . "\n", 'green');
    }

    /*if (!$inst->Save()) {
        print_r($inst->Error);
        die('Erreur de création instance');
    }*/
}
