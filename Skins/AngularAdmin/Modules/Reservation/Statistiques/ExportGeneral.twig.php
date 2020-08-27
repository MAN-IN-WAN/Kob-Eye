<?php
$data = json_decode(file_get_contents('php://input'), true);
if (empty($data)) $data = $_GET;
if (isset($data['state'])) {
    $vars['state'] = $data['state'];
} else {
    $vars['state'] = 0;
}
if ($vars['state'] == 0) {

    $vars['Client'] = Sys::getData('Reservation','Client',0,100000);
    $vars['Ville'] = array();

    foreach ($vars['Client'] as $items){
        $vars['Ville'][] = $items->Ville;
    }

    $vars['Ville'] = array_unique($vars['Ville']);

    sort($vars['Ville']);

    $html = '
	<label for="ville">Ville des structures</label><br />
				<select select2 ng-model="ExportGeneral.args.Ville" name="Ville" id="Ville" ng-options="val for val in Villes"></select>
    <label for="start">Date début</label>
    <label class="input-group datepicker-only datepicker-only-init">
        <input type="text"  id="start" class="form-control"  ng-model="ExportGeneral.args.start"/>
        <span class="input-group-addon">
            <i class="icmn-calendar"></i>
        </span>
    </label>
    <label for="stop">Date fin</label>	
    <label class="input-group datepicker-only datepicker-only-init">
        <input type="text"  id="start" class="form-control"  ng-model="ExportGeneral.args.stop"/>
        <span class="input-group-addon">
            <i class="icmn-calendar"></i>
        </span>
    </label>
	<button ng-click="onExportGen();">Valider</button>';

    $ret = array(
        'html' => $html,
        'ville'=>$vars['Ville']
    );
    $vars['return'] = json_encode($ret);

} elseif ($vars['state'] == 1) {

    $ville = $data['args']['Ville'];
    $start = $data['args']['start'];
    $stop = $data['args']['stop'];

    $stop = explode('/', $stop);
    $stop = array_reverse($stop);
    $stop = implode('/',$stop);
    $stop = strtotime($stop);

    $start = explode('/', $start);
    $start = array_reverse($start);
    $start = implode('/',$start);
    $start = strtotime($start);

    if ($start > $stop ){
        $error = '<p>La date de fin ne peut pas être antérieur à la date de début</p><button ng-click="reInitExportGeneral();">Retour à la séléction</button>';
            $ret = array(
                'html' => $error
            );
            $vars['return'] = json_encode($ret);
    }else{
        $clients = Sys::getData('Reservation', 'Client/Ville=' . $ville);
        usort($clients,function ($a,$b){
            if (strtolower($a->Nom) > strtolower($b->Nom)) return 1;
            if (strtolower($a->Nom) < strtolower($b->Nom)) return -1;
            return 0;
        });
//    var_dump($vars['Reservations']);

        $html = '<div>
                <p>Votre recherche pour la ville : '.$ville.'</p>
                <p>Pour la période du '.date('d/m/Y',$start).' au '.date('d/m/Y',$stop).'</p>
                <button ng-click="reInitExportGeneral();">Retour à la séléction</button>
                </div><div style="font-family:Arial;font-size:12px">';
        $totalResa = 0;
        $totalPers = 0;

        foreach ($clients as $client) {
            $NbResa = 0;
            $NbPers = 0;
            $html .= '<div style="font-weight:bold">' . $client->Nom . '</div>';
            $reservations = $client->getChildren('Reservations');
            foreach ($reservations as $reserv) {
                $evenements = $reserv->getParents('Evenement');
                foreach ($evenements as $event) {
                    if($event->DateDebut<$start || $event->DateDebut>$stop) continue;
                    $cpt = Sys::getCount('Reservation', 'Reservations/' . $reserv->Id . '/Personne');
                    $NbResa++;
                    $NbPers += $cpt;
                }
            }
            $totalResa += $NbResa;
            $totalPers += $NbPers;
            $html .= '<div style="color:#826">' . $NbResa . ' réservation(s)</div><div style="color:#268">' . $NbPers . ' personnes(s)</div>';
        }
        $html .= '<div> TOTAL</div><div style="color:#826">' . $totalResa . ' réservation(s)</div><div style="color:#268">' . $totalPers . ' personnes(s)</div>';
        $ret = array(
            'html' => $html
        );
        $vars['return'] = json_encode($ret);
    }

}


