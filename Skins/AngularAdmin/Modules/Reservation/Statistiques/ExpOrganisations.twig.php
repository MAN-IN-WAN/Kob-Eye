<?php
$data = json_decode(file_get_contents('php://input'),true);
if(empty($data)) $data = $_GET;
if (isset($data['state'])){
    $vars['state'] = $data['state'];
}else{
    $vars['state'] = 0;
}
if ($vars['state'] == 0){
    $vars['Organisation'] = Sys::getData('Reservation','Organisation',0,100000);
    $vars['Ville'] = array();
    $vars['Departement'] = array();
    $vars['CodePostal'] = array();
    foreach ($vars['Organisation'] as $items){
        $vars['Ville'][] = $items->Ville;
        $vars['Departement'][] = $items->Departement;
        $vars['CodePostal'][] = $items->CodPos;
    }
    $vars['Ville'] = array_unique($vars['Ville']);
    $vars['Departement'] = array_unique($vars['Departement']);
    $vars['CodePostal'] = array_unique($vars['CodePostal']);

    sort($vars['Ville']);
    sort($vars['Departement']);
    sort($vars['CodePostal']);

    $html='<div>
        <h2>Statut Etiquette : Cochez ce que vous voulez</h2>
        <input type="radio" ng-value="1" ng-model="ExpOrganisations.args.Etiq" name="EtiqBool">Etiquette à Oui<br/>
        <input type="radio" ng-value="0" ng-model="ExpOrganisations.args.Etiq" name="EtiqBool">Etiquette à Non<br/>
        <input type="radio" ng-value="3" ng-model="ExpOrganisations.args.Etiq" name="EtiqBool">Toutes<br/>
    </div>
    <div>
        <h2>Filtres</h2>
        <label for="CodePos">Code Postal </label>
        <select select2 ng-model="ExpOrganisations.args.CodePos" name="CodePos" id="CodePos" ng-options="val for val in Codes"></select>
        <label for="Ville">Ville </label>
        <select select2 ng-model="ExpOrganisations.args.Ville" name="Ville" id="Ville" ng-options="val for val in Villes"></select>
        <label for="Dep">Departement </label>
        <select select2 ng-model="ExpOrganisations.args.Dep" name="Dep" id="Dep" ng-options="val for val in Departements"></select>
    </div>
    <div>
        Exporter les structures culturelles
        <button ng-click="validExpOrg();">OK</button>
    </div>';

    $ret = array(
        'html'=>$html,
        'Villes'=>$vars['Ville'],
        'Departements'=>$vars['Departement'],
        'Codes'=>$vars['CodePostal']
    );
    $vars['return'] = json_encode($ret);

}
if ($vars['state'] == 1 || $vars['state'] == 2){
    $vars['V'] = $data['args']['Ville'];
    $vars['D'] = $data['args']['Dep'];
    $vars['CP'] = $data['args']['CodePos'];
    $vars['Etiq'] = $data['args']['Etiq'];
}
if ($vars['state'] == 3){
    Organisation::ImprimeEtiquette2($data[cp],$data[v],$data[d],null,$data[e]);
}







