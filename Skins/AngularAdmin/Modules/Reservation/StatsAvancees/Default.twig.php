<?php
// Nombre de spectacles dans l'année courante
$nbSpecCurrentYear = Reservations::getTabSpeEveAnneeEncours();
$nbSpec = 0 ;
$nbEve = 0 ;
foreach ($nbSpecCurrentYear as $elem){
    $nbSpec+=1;
    $nbEve += $elem['TotEve'];
}
$nbSpecCurrentYear = array($nbSpec, $nbEve);

$vars['ListSpecCurrent'] = $nbSpecCurrentYear;


// Nombre de spectacles dans l'année passée
$nbSpecEveOldYear = Reservations::getTabSpeEveAnneePasse();


$nbSpecOldYear = 0 ;
$nbEveOldYear = 0 ;
foreach ($nbSpecEveOldYear as $elem){
    $nbSpecOldYear+=1;
    $nbEveOldYear += $elem['TotEve'];
}
$nbSpecEveOldYear = array($nbSpecOldYear, $nbEveOldYear);

$vars['ListSpecOld'] = $nbSpecEveOldYear;



//$vars['ListSpec'] = $nbSpecCurrentYear;
// Recupere tous les spectacles qui n'ont pas d'evenements
$SpeNoEventCurrentYear = Reservations::getTabSpeEveAnneeEncours();
foreach($SpeNoEventCurrentYear as $item){
    if (intval($item->TotEve) > 0)
        unset($item);
}
$vars['ListSpecNoEventCurrentYear'] = $SpeNoEventCurrentYear;

$SpeNoEventOldYear = Reservations::getTabSpeEveAnneePasse();
foreach($SpeNoEventOldYear as $item){
    if (intval($item->TotEve) > 0)unset($item);
}
$vars['ListSpecNoEventOldYear'] = $SpeNoEventOldYear;

//Tableau Liste Activité structure sociales
//
//Nom structure / Cumul Réservations / cumul Personnes
setlocale(LC_TIME, "fr_FR", "French");
$vars['prevYear']  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-1);
$vars['thisYear']  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y"));

$vars['prevMonth']  = mktime(0, 0, 0, date("m")-1,   date("d"),   date("Y"));
$vars['thisMonth']  = mktime(0, 0, 0, date("m"),   date("d"),   date("Y"));

$vars['prevYear']  = date("Y",$vars['prevYear']);
$vars['thisYear']  = date("Y",$vars['thisYear']);

$vars['prevMonth']  = strftime( "%B", $vars['prevMonth']);
$vars['thisMonth']  = strftime( "%B", $vars['thisMonth']);

$statsNomStruc = Reservations::getTabStructures();
$vars['ActiviteStruct'] = $statsNomStruc;


// ------------------------------ MODIF JEREM 10/11/2020
$moisPass = mktime(0,0,0,date("m" )-1,date("d" ),date("Y" ));
$anneePass = mktime(0,0,0,date("m" ),date("d" ),date("Y" )-1);
$NbResaMoisCourant = 0;
$NbPlacesMoisCourant = 0;
$NbResaMoisPasse = 0;
$NbPlacesMoisPasse = 0;
$NbResaAnneeCourant = 0;
$NbPlacesAnneeCourant = 0;
$NbResaAnneePasse = 0;
$NbPlacesAnneePasse = 0;
$NbPlacesGenreCurrentMonth = 0;
$NbPlacesGenrePassMonth = 0;
$NbPlacesGenreCurrentYears = 0;
$NbPlacesGenrePassYears = 0;
$NbResGenreCurrentMonth = 0;
$NbResGenrePassMonth = 0;
$NbResGenreCurrentYear = 0;
$NbResGenrePassYear = 0;
$NbSpecCurrentYear = 0;
$NbSpecOldYear = 0;
$NbResaVilleCurrentYear = 0;
$NbResaVilleCurrentMonth = 0;
$NbResaVilleOldMonth = 0;
$NbResaVilleOldYear = 0;
$NbPersCurrentYear = 0;
$NbPersOldYear = 0;

$statsMoisCourant = Reservations::getStatsMensuelles();
$statsMoisPasse = Reservations::getStatsMensuelles($moisPass);

foreach ($statsMoisCourant as $elem){
    $NbResaMoisCourant += $elem['NbResa'];
    $NbPlacesMoisCourant += $elem['NbPlaces'];
}
foreach ($statsMoisPasse as $elem){
    $NbResaMoisPasse += $elem['NbResa'];
    $NbPlacesMoisPasse += $elem['NbPlaces'];
}
$vars['NbResaMoisCourant'] = $NbResaMoisCourant;
$vars['NbPlacesMoisCourant'] = $NbPlacesMoisCourant;

$vars['NbResaMoisPasse'] = $NbResaMoisPasse;
$vars['NbPlacesMoisPasse'] = $NbPlacesMoisPasse;

// Stats Annuels Histogrammes
$statsAnneeCourant = Reservations::getStatsAnnuelles(); // Stats année courante
$statsAnneePasse = Reservations::getStatsAnnuelles($anneePass); // Stats année passée

foreach ($statsAnneeCourant as $elem){
    $NbResaAnneeCourant += $elem['NbResa'];
    $NbPlacesAnneeCourant += $elem['NbPlaces'];
}

foreach ($statsAnneePasse as $elem){
    $NbResaAnneePasse += $elem['NbResa'];
    $NbPlacesAnneePasse += $elem['NbPlaces'];
}

$vars['NbResaAnneeCourant'] = $NbResaAnneeCourant;
$vars['NbResaAnneePasse'] = $NbResaAnneePasse;

$vars['NbPlacesAnneeCourant'] = $NbPlacesAnneeCourant;
$vars['NbPlacesAnneePasse'] = $NbPlacesAnneePasse;

// Stats Camemberts
// Répartition des reservations par genre mois en cours et mois précédent
// Répartition des reservations par genre année en cours et année précédente

$moisCurrent = date('m');
$yearCurrent = date('Y');

$startGenreMoisCurrent = mktime(0,0,0,$moisCurrent,1,date("Y" ));
$stopGenreMoisCurrent = mktime(0,0,0,$moisCurrent,cal_days_in_month(CAL_GREGORIAN, $moisCurrent, $yearCurrent),date("Y" ));

$startGenreAnneeCurrent = mktime(0,0,0,1,1,date("Y" ));
$stopGenreAnneeCurrent = mktime(0,0,0,12,31,date("Y" ));

$startGenreMoisPass = mktime(0,0,0,$moisCurrent-1,1,date("Y" ));
$stopGenreMoisPass = mktime(0,0,0,$moisCurrent-1,cal_days_in_month(CAL_GREGORIAN, $moisCurrent-1, $yearCurrent),date("Y" ));

$startGenreAnneePass = mktime(0,0,0,1,1,date("Y" )-1);
$stopGenreAnneePass = mktime(0,0,0,12,31,date("Y" )-1);

// Stats nombre reservations mois actuel par Genre
$statsGenreCurrentMonth = Reservations::getStatsGenre($startGenreMoisCurrent,$stopGenreMoisCurrent);
foreach ($statsGenreCurrentMonth as $elem){
    $NbPlacesGenreCurrentMonth += $elem['NbPlaces'];
}
$vars['NbPlacesGenreCurrentMonth'] = $NbPlacesGenreCurrentMonth;

// Stats nombre reservations mois passé par Genre
$statsGenrePassMonth = Reservations::getStatsGenre($startGenreMoisPass,$stopGenreMoisPass);
foreach ($statsGenrePassMonth as $elem){
    $NbPlacesGenrePassMonth += $elem['NbPlaces'];
}
$vars['NbPlacesGenrePassMonth'] = $NbPlacesGenrePassMonth;

// Stats nombre reservations année actuel par Genre
$statsGenreCurrentYears = Reservations::getStatsGenre($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
foreach ($statsGenreCurrentYears as $elem){
    $NbPlacesGenreCurrentYears += $elem['NbPlaces'];
}
$vars['NbPlacesGenreCurrentYears'] = $NbPlacesGenreCurrentYears;

// Stats nombre reservations année passée par Genre
$statsGenrePassYears = Reservations::getStatsGenre($startGenreAnneePass,$stopGenreAnneePass);
foreach ($statsGenrePassYears as $elem){
    $NbPlacesGenrePassYears += $elem['NbPlaces'];
}
$vars['NbPlacesGenrePassYears'] = $NbPlacesGenrePassYears;

// Nombre places reservées et disponibles restantes par Genre dans les dates renseignées
$resProgParGenreCurrentMonth = Reservations::getStatsGenrePartProgrammeesReservees($startGenreMoisCurrent,$stopGenreMoisCurrent);
foreach ($resProgParGenreCurrentMonth as $elem){
    $NbResGenreCurrentMonth += $elem['NbProgrammees'];
}
$vars['NbResGenreCurrentMonth'] = $NbResGenreCurrentMonth;

// Nombre places reservées et disponibles restantes par Genre pour le mois précédent
$resProgParGenre = Reservations::getStatsGenrePartProgrammeesReservees($startGenreMoisPass,$stopGenreMoisPass);
foreach ($resProgParGenre as $elem){
    $NbResGenrePassMonth += $elem['NbProgrammees'];
}
$vars['NbResGenrePassMonth'] = $NbResGenrePassMonth;

// Nombre places reservées et disponibles restantes par Genre dans l'année courante'
$resProgParGenreCurrentYear = Reservations::getStatsGenrePartProgrammeesReservees($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
foreach ($resProgParGenreCurrentYear as $elem){
    $NbResGenreCurrentYear += $elem['NbProgrammees'];
}
$vars['NbResGenreCurrentYear'] = $NbResGenreCurrentYear;

// Nombre places reservées et disponibles restantes par Genre pour l'année précédente
$resProgParGenreOldYear = Reservations::getStatsGenrePartProgrammeesReservees($startGenreAnneePass,$stopGenreAnneePass);
foreach ($resProgParGenreOldYear as $elem){
    $NbResGenrePassYear += $elem['NbProgrammees'];
}
$vars['NbResGenrePassYear'] = $NbResGenrePassYear;

// Nombre places spectacle programmés par genre pour l'année actuelle
$SpecCurrentYear = Reservations::getStatsGenreSpectacles($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
foreach ($SpecCurrentYear as $elem){
    $NbSpecCurrentYear += $elem['NbSpec'];
}
$vars['NbSpecCurrentYear'] = $NbSpecCurrentYear;
// Nombre places spectacle programmés par genre pour l'année passée
$SpecOldYear = Reservations::getStatsGenreSpectacles($startGenreAnneePass,$stopGenreAnneePass);
foreach ($SpecOldYear as $elem){
    $NbSpecOldYear += $elem['NbSpec'];
}
$vars['NbSpecOldYear'] = $NbSpecOldYear;

// Recupere le nombre de personnes par sexe si RSA / si Accompagnateur / tranche d'age pour l'année courante
$statsResCurrentYear = Reservations::getStatsPersonneReservantes($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
$tabAccom = array();
$tabRsa = array();
$tabSexe = array();
foreach ($statsResCurrentYear as $elem){
    if ($elem['Accompagnateur'] == '1'){
        $tabAccom['NbPersonneAccom'] += intval($elem['NbPersonne']);
    }
    if ($elem['Rsa'] == '1'){
        $tabRsa['NbPersonneRsa'] += intval($elem['NbPersonne']);
    }
    if ($elem['Sexe'] == 'Homme'){
        $tabSexe['NbPersonneH'] += intval($elem['NbPersonne']);
    }
    $tabAccom['NbPersonne'] += intval($elem['NbPersonne']);
    $tabRsa['NbPersonne'] += intval($elem['NbPersonne']);
    $tabSexe['NbPersonne'] += intval($elem['NbPersonne']);
}
$vars['NbPersonneAccom'] = $tabAccom['NbPersonneAccom'];
$vars['NbPersonneNonAccom'] = $tabAccom['NbPersonne']-$tabAccom['NbPersonneAccom'];
$vars['NbPersonneNonRsa'] = $tabRsa['NbPersonne']-$tabRsa['NbPersonneRsa'];
$vars['NbPersonneRsa'] = $tabRsa['NbPersonneRsa'];
$vars['NbPersonneF'] = $tabSexe['NbPersonne']-$tabSexe['NbPersonneH'];
$vars['NbPersonneH'] = $tabSexe['NbPersonneH'];

// Recupere le nombre de personnes par sexe si RSA / si Accompagnateur / tranche d'age pour l'année passée
$statsResOldYear = Reservations::getStatsPersonneReservantes($startGenreAnneePass,$stopGenreAnneePass);

$tabAccom = array();
$tabRsa = array();
$tabSexe = array();
foreach ($statsResOldYear as $elem){
    if ($elem['Accompagnateur'] == '1'){
        $tabAccom['NbPersonneAccom'] += intval($elem['NbPersonne']);
    }
    if ($elem['Rsa'] == '1'){
        $tabRsa['NbPersonneRsa'] += intval($elem['NbPersonne']);
    }
    if ($elem['Sexe'] === 'Homme'){
        $tabSexe['NbPersonneH'] += intval($elem['NbPersonne']);
    }
    $tabAccom['NbPersonne'] += intval($elem['NbPersonne']);
    $tabRsa['NbPersonne'] += intval($elem['NbPersonne']);
    $tabSexe['NbPersonne'] += intval($elem['NbPersonne']);
}
$vars['NbPersonneAccomOldYear'] = $tabAccom['NbPersonneAccom'];
$vars['NbPersonneNonAccomOldYear'] = $tabAccom['NbPersonne']-$tabAccom['NbPersonneAccom'];
$vars['NbPersonneNonRsaOldYear'] = $tabRsa['NbPersonne']-$tabRsa['NbPersonneRsa'];
$vars['NbPersonneRsaOldYear'] = $tabRsa['NbPersonneRsa'];
$vars['NbPersonneFOldYear'] = $tabSexe['NbPersonne']-$tabSexe['NbPersonneH'];
$vars['NbPersonneHOldYear'] = $tabSexe['NbPersonneH'];

// Recupere le nombre de place reservé pour le mois en cours par ville
$ResParVillesCurrentMonth = Reservations::getTabVille($startGenreMoisCurrent,$stopGenreMoisCurrent);
foreach ($ResParVillesCurrentMonth as $elem){
    $NbResaVilleCurrentMonth += $elem['NbResa'];
}
$vars['NbResaVilleCurrentMonth'] = $NbResaVilleCurrentMonth;
// Recupere le nombre de place reservé pour le mois passée par ville
$ResParVillesOldMonth = Reservations::getTabVille($startGenreMoisPass,$stopGenreMoisPass);
foreach ($ResParVillesOldMonth as $elem){
    $NbResaVilleOldMonth += $elem['NbResa'];
}
$vars['NbResaVilleOldMonth'] = $NbResaVilleOldMonth;

// Recupere le nombre de place reservé pour l'année en cours par ville
$ResParVillesCurrentYear = Reservations::getTabVille($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
foreach ($ResParVillesCurrentYear as $elem){
    $NbResaVilleCurrentYear += $elem['NbResa'];
}
$vars['NbResaVilleCurrentYear'] = $NbResaVilleCurrentYear;
// Recupere le nombre de place reservé pour l'année passée par ville
$ResParVillesOldYear = Reservations::getTabVille($startGenreAnneePass,$stopGenreAnneePass);
foreach ($ResParVillesOldYear as $elem){
    $NbResaVilleOldYear += $elem['NbResa'];
}
$vars['NbResaVilleOldYear'] = $NbResaVilleOldYear;

// Recupere le nombre de personnes par age de l'année courante
$ResPersParAgeCurrentYear = Reservations::getStatsPersonneReservantesAge($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
foreach ($ResPersParAgeCurrentYear as $elem){
    $NbPersCurrentYear += $elem['NbPersonne'];
}
$vars['NbPersCurrentYear'] = $NbPersCurrentYear;
// Recupere le nombre de personnes par age de l'année passée
$ResPersParAgeOldYear = Reservations::getStatsPersonneReservantesAge($startGenreAnneePass,$stopGenreAnneePass);
foreach ($ResPersParAgeOldYear as $elem){
    $NbPersOldYear += $elem['NbPersonne'];
}
$vars['NbPersOldYear'] = $NbPersOldYear;
// Recupere le Nb de spectacles par personne
$NbSpeParPers = Reservations::getStatsNbSpectacles();
foreach ($NbSpeParPers as $elem){
    $NbSpeParPersTot += $elem['Val'];
}
$vars['NbSpeParPersTot'] = $NbSpeParPersTot;
//// Nombre de spectacles dans l'année courante
//$nbSpecCurrentYear = Reservations::getTabSpeEveAnneeEncours();
//$alldata[] = $nbSpecCurrentYear;
//
//// Nombre de spectacles dans l'année passée
//$nbSpecOldYear = Reservations::getTabSpeEveAnneePasse();
//$alldata[] = $nbSpecOldYear;
//
//// Recupere tous les spectacles qui n'ont pas d'evenements
//$allSpeNoEvent = Reservations::getAllSpeNoEvent();
//$alldata[] = $allSpeNoEvent;
//
//$SpeNoEventCurrentYear = Reservations::getTabSpeEveAnneeEncours();
//foreach($SpeNoEventCurrentYear as $item){
//    if ($item['TotEve'] != "0" )
//        unset($item);
//}
//
////$SpeNoEventCurrentYear = Reservations::getTabSpeEveAnneeEncours();
//$alldata[] = $SpeNoEventCurrentYear;
////Tableau Liste Activité structure sociales
////
////Nom structure / Cumul Réservations / cumul Personnes
//
//$statsNomStruc = Reservations::getTabStructures();
//$alldata[] = $statsNomStruc;
//
//echo json_encode($alldata);
////$vars['test'] = print_r($statsMoisCourant,true);
//

