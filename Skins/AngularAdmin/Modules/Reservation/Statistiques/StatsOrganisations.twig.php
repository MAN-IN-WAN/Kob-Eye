<?php
$data = json_decode(file_get_contents('php://input'),true);
if(empty($data)) $data = $_GET;
if (isset($data['state'])){
    $vars['state'] = $data['state'];
}else{
    $vars['state'] = 0;
}
if ($vars['state'] == 0){
    $vars['organisation'] = Sys::getData('Reservation','Organisation',0,100000);
//    $html = 'Année : <input type="text" class="form-control" ng-model="StatsStructures.args.Date"><br>';
    $html = 'Date Debut : <h2 style="color:red">Attention le format de date doit être jj/mm/aaaa pour être valide !</h2><input type="text" class="form-control" ng-model="StatsOrganisations.args.DateDebut"><br>';
    $html .= 'Date Fin : <h2 style="color:red">Attention le format de date doit être jj/mm/aaaa pour être valide !</h2><input type="text" class="form-control" ng-model="StatsOrganisations.args.DateFin"><br>';
    $tuc = '';
    foreach ($data['organisation'] as $items){
        $tuc.= $items['Nom'];
    }
    $html .=  KeTwig::processTemplates(KeTwig::callModule('Reservation/Statistiques/SelectOrganisations'));
    $html .=  '<button ng-click="onSelecOrga();">Valider</button>';
    $ret = array(
        'html'=>$html,
        'organisation'=>$vars['organisation'],
        'state'=>$data['state']
    );
    $vars['return'] = json_encode($ret);
}elseif ($vars['state'] == 1){
    $organisation = serialize($data['organisation']);
    $uid = uniqid();
    file_put_contents('/tmp/chouette'.$uid.'.cli',$organisation);

//    setcookie('clientSelect',$client,3600,'/',Sys::$domain);
    $html = '<button ng-click="reInitStatsOrganisations();">Retour à la selection</button>
    <iframe src="/Reservation/Statistiques/StatsOrganisations.pdf?state=3&Date='.$data['Date'].'&DateDebut='.$data['DateDebut'].'&DateFin='.$data['DateFin'].'&uid='.$uid.'" frameborder="0" style="width:100%;height:900px;"></iframe>';
    $ret = array(
        'html'=>$html,
        'organisation'=>$data['organisation'],
        'state'=>$data['state']
    );
    $vars['return'] = json_encode($ret);

}elseif ($vars['state'] == 3){
    $choix = file_get_contents('/tmp/chouette'.$data['uid'].'.cli');
    $choix = unserialize($choix);
    $Date = $data['Date'];
//    $DateDebut = mktime(0, 0, 0, 1, 1, $Date);
//    $DateFin = mktime(23, 59, 59, 12, 31, $Date);
    $DateDebut = $data['DateDebut'];
    $DateFin = $data['DateFin'];

    list($day, $month, $year) = explode('/', $DateDebut);
    $DateDebut = mktime(0, 0, 0, $month, $day, $year);

    list($day, $month, $year) = explode('/', $DateFin);
    $DateFin = mktime(0, 0, 0, $month, $day, $year);

    $html = '<table border="1" cellspacing="0" cellspadding="0" style="max-width:700px">';
    $html .= '<tr><th colspan="4" style="font-size:14px;font-weight:bold;text-align:center;">Liste des réservations du '.date('d/m/Y',$DateDebut).' au '.date('d/m/Y',$DateFin).'</th>   
                </tr>
                <tr>
                    <td style="text-align:center;font-weight:bold;">Structure</td>
                    <td style="text-align:center;font-weight:bold;">Ville</td>
                    <td style="text-align:center;font-weight:bold;">Nombre de réservation </td>
                    <td style="text-align:center;font-weight:bold;">Nombre de personnes </td>
                </tr>';

    $tab = array();
    $TotResa = 0;
    $TotPers = 0;
    $genre = array();
    foreach( $choix as $organisation ){
        $NbResa = 0;
        $NbPers = 0;
        $objCli = genericClass::createInstance('Reservation','Organisation');
        $objCli->initFromId($organisation);
        $tab = array();
        $spectacles = $objCli->getChildren('Spectacle');

        foreach ( $spectacles as $s) {
            $event = $s->getChildren('Evenement');
            foreach ($event as $items) {
                $resa = $items->getChildren('Reservations');
                foreach ( $resa as $reservations) {
                    if($items->DateDebut<$DateDebut || $items->DateDebut>$DateFin) continue;
                    $cpt = Sys::getCount('Reservation', 'Reservations/' . $reservations->Id . '/Personne');
                    $NbResa++;
                    $NbPers += $cpt;
                }
            }
        }
//        file_put_contents('/tmp/tutu',print_r($resa,true));
//        foreach ($reservations as $reserv) {
//            $evenements = $reserv->getParents('Evenement');
//            foreach ($evenements as $event) {
//                if($event->DateDebut<$DateDebut || $event->DateDebut>$DateFin) continue;
//                $sp = $event->getOneParent('Spectacle');
//                $cpt = Sys::getCount('Reservation', 'Reservations/' . $reserv->Id . '/Personne');
//                $NbResa++;
//                $NbPers += $cpt;
//                $genre[$sp->Genre] += $cpt;
//            }
//        }
        $html .=
            '<tr>
                <td style="width:300px">'.$objCli->Nom.'</td>
                <td>'.$objCli->Ville.'</td>
                <td>'.$NbResa.'</td>
                <td>'.$NbPers.'</td>
            </tr>';
        $TotResa += $NbResa;
        $TotPers += $NbPers;
    }

    $html .= '<tr style="width:190mm;font-size:10px;background-color:#ccc;">
                <td colspan="2" style="text-align:right;font-size:20px;font-weight:bold;color:#ff0000;padding:20px;">Total GÉNÉRAL</td>
                <td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">'.$TotResa.'</td>
                <td style="text-align:right;font-weight:bold;padding-right:10px;font-size:16px;">'.$TotPers.'</td>
            </tr>';

//    foreach($genre as $key => $value){
//        $html .= '<tr><td colspan="3" style="text-align:center;font-size:12px;font-weight:bold;color:#000;padding:5px;">'.$key.'</td><td>'.$value.'</td></tr>';
//    }
//    $sum = array_sum($genre);


//    $test = print_r($genre,true);
//    $totNbGenre = sizeof($nombre);
//    $test = print_r($genre,true);
//    $test = print_r($reservation,true);
//    $html .= '<tr><td>'.$test.'</td></tr>';

    $html .= '</table>';

    // Creation du pdf
    include $_SERVER['DOCUMENT_ROOT']."/Class/Lib/HTML2PDF.class.php";
    $tes = new HTML2PDF();
    $tes->writeHTML('<page pageset="old" backtop="14mm" backbottom="1mm" backleft="10mm" backright="10mm" style="font-size: 12pt">
				'.$html.'
			</page>');
    ob_get_clean();
    $tes->Output('MailsStatsOrganisations.pdf');
    ob_end_clean();
}


