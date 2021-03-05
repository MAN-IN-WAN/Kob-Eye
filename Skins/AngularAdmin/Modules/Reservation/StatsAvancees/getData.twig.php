<?php
// Stats Mensuels Histogrammes
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

$alldata = array();
$statsMoisCourant = Reservations::getStatsMensuelles();
$alldata[] = array_values($statsMoisCourant);
$statsMoisPasse = Reservations::getStatsMensuelles($moisPass);
$alldata[] = array_values($statsMoisPasse);

foreach ($statsMoisCourant as $elem){
    $NbResaMoisCourant += $elem['NbResa'];
    $NbPlacesMoisCourant += $elem['NbPlaces'];
}
foreach ($statsMoisPasse as $elem){
    $NbResaMoisPasse += $elem['NbResa'];
    $NbPlacesMoisPasse += $elem['NbPlaces'];
}

// Stats Annuels Histogrammes
$statsAnneeCourant = Reservations::getStatsAnnuelles(); // Stats année courante
$alldata[] = array_values($statsAnneeCourant);
$statsAnneePasse = Reservations::getStatsAnnuelles($anneePass); // Stats année passée
$alldata[] = array_values($statsAnneePasse);

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
$alldata[] = $statsGenreCurrentMonth;

// Stats nombre reservations mois passé par Genre
$statsGenrePassMonth = Reservations::getStatsGenre($startGenreMoisPass,$stopGenreMoisPass);
$alldata[] = $statsGenrePassMonth;

// Stats nombre reservations année actuel par Genre
$statsGenreCurrentYears = Reservations::getStatsGenre($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
$alldata[] = $statsGenreCurrentYears;

// Stats nombre reservations année passée par Genre
$statsGenrePassYears = Reservations::getStatsGenre($startGenreAnneePass,$stopGenreAnneePass);
$alldata[] = $statsGenrePassYears;

// Nombre places reservées et disponibles restantes par Genre dans les dates renseignées
$resProgParGenreCurrentMonth = Reservations::getStatsGenrePartProgrammeesReservees($startGenreMoisCurrent,$stopGenreMoisCurrent);
$alldata[] = $resProgParGenreCurrentMonth;

// Nombre places reservées et disponibles restantes par Genre pour le mois précédent
$resProgParGenre = Reservations::getStatsGenrePartProgrammeesReservees($startGenreMoisPass,$stopGenreMoisPass);
$alldata[] = $resProgParGenre;

// Nombre places reservées et disponibles restantes par Genre dans l'année courante'
$resProgParGenreCurrentYear = Reservations::getStatsGenrePartProgrammeesReservees($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
$alldata[] = $resProgParGenreCurrentYear;

// Nombre places reservées et disponibles restantes par Genre pour l'année précédente
$resProgParGenreOldYear = Reservations::getStatsGenrePartProgrammeesReservees($startGenreAnneePass,$stopGenreAnneePass);
$alldata[] = $resProgParGenreOldYear;

// Nombre places spectacle programmés par genre pour l'année actuelle
$SpecCurrentYear = Reservations::getStatsGenreSpectacles($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
$alldata[] = array_values($SpecCurrentYear);
// Nombre places spectacle programmés par genre pour l'année passée
$SpecOldYear = Reservations::getStatsGenreSpectacles($startGenreAnneePass,$stopGenreAnneePass);
$alldata[] = $SpecOldYear;

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
$tabAccom['NbPersonneNonAccom'] = $tabAccom['NbPersonne']-$tabAccom['NbPersonneAccom'];
$tabRsa['NbPersonneNonRsa'] = $tabRsa['NbPersonne']-$tabRsa['NbPersonneRsa'];
$tabSexe['NbPersonneF'] = $tabSexe['NbPersonne']-$tabSexe['NbPersonneH'];

$statsResCurrentYearAccom = $tabAccom;
$statsResCurrentYearRsa = $tabRsa;
$statsResCurrentYearSexe = $tabSexe;

$alldata[] = $statsResCurrentYearAccom;



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
$tabAccom['NbPersonneNonAccom'] = $tabAccom['NbPersonne']-$tabAccom['NbPersonneAccom'];
$tabRsa['NbPersonneNonRsa'] = $tabRsa['NbPersonne']-$tabRsa['NbPersonneRsa'];
$tabSexe['NbPersonneF'] = $tabSexe['NbPersonne']-$tabSexe['NbPersonneH'];

$statsResOldYear = $tabAccom;
$statsResOldYearRsa = $tabRsa;
$statsResOldYearSexe = $tabSexe;

$alldata[] = $statsResOldYear;
$alldata[] = $statsResCurrentYearRsa;
$alldata[] = $statsResOldYearRsa;
$alldata[] = $statsResCurrentYearSexe;
$alldata[] = $statsResOldYearSexe;
//$alldata[] = $statsResCurrentYear;
// Recupere le nombre de place reservé pour le mois en cours par ville
$ResParVillesCurrentMonth = Reservations::getTabVille($startGenreMoisCurrent,$stopGenreMoisCurrent);
$alldata[] = $ResParVillesCurrentMonth;

// Recupere le nombre de place reservé pour l'année en cours par ville
$ResParVillesCurrentYear = Reservations::getTabVille($startGenreAnneeCurrent,$stopGenreAnneeCurrent);
$alldata[] = $ResParVillesCurrentYear;

// Recupere le nombre de place reservé pour l'année passée par ville
$ResParVillesOldYear = Reservations::getTabVille($startGenreAnneePass,$stopGenreAnneePass);
$alldata[] = $ResParVillesOldYear;

// Recupere le nombre de personnes par age de l'année courante
$ResPersParAgeCurrentYear = Reservations::getStatsPersonneReservantesAge($startGenreAnneeCurrent,$stopGenreAnneeCurrent);

$alldata[] = $ResPersParAgeCurrentYear;
// Recupere le nombre de personnes par age de l'année passée
$ResPersParAgeOldYear = Reservations::getStatsPersonneReservantesAge($startGenreAnneePass,$stopGenreAnneePass);
$alldata[] = $ResPersParAgeOldYear;

// Recupere le Nb de spectacles par personne
$NbSpeParPers = Reservations::getStatsNbSpectacles();
$alldata[] = $NbSpeParPers;

// Nombre de spectacles dans l'année courante
$nbSpecCurrentYear = Reservations::getTabSpeEveAnneeEncours();
$alldata[] = $nbSpecCurrentYear;

// Nombre de spectacles dans l'année passée
$nbSpecOldYear = Reservations::getTabSpeEveAnneePasse();
$alldata[] = $nbSpecOldYear;

// Recupere tous les spectacles qui n'ont pas d'evenements
$allSpeNoEvent = Reservations::getAllSpeNoEvent();
$alldata[] = $allSpeNoEvent;

$SpeNoEventCurrentYear = Reservations::getTabSpeEveAnneeEncours();
foreach($SpeNoEventCurrentYear as $item){
    if ($item['TotEve'] != "0" )
        unset($item);
}

//$SpeNoEventCurrentYear = Reservations::getTabSpeEveAnneeEncours();
$alldata[] = $SpeNoEventCurrentYear;
//Tableau Liste Activité structure sociales
//
//Nom structure / Cumul Réservations / cumul Personnes

$statsNomStruc = Reservations::getTabStructures();
$alldata[] = $statsNomStruc;

echo json_encode($alldata);
//$vars['test'] = print_r($statsMoisCourant,true);



