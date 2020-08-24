<?php


$srv = Sys::getData('Parc', 'Server/Mail=1&MailType=Mbx');
$data = array();
$quotas =array();
$allCC = 0;
foreach ($srv as $s) {
    // Connexion API Zimbra

    $zimbra = new \Zimbra\ZCS\Admin($s->IP, $s->mailAdminPort);
    $zimbra->auth($s->mailAdminUser, $s->mailAdminPassword);
    $quotas = array_merge($quotas,$zimbra->getQuotas(array()));
}
foreach ($srv as $s) {

    echo "Serveur : ".$s->Nom." ";
    // Connexion API Zimbra
    $zimbra = new \Zimbra\ZCS\Admin($s->IP, $s->mailAdminPort);
    $zimbra->auth($s->mailAdminUser, $s->mailAdminPassword);

    $domaine = $zimbra->getDomains();

    $cosesTemp = $zimbra->getAllCos();
    $coses = array();
    foreach ($cosesTemp as $cosTemp){
        $coses[$cosTemp->get('id')]=$cosTemp->get('name');
    }

    foreach ($domaine as $d) {

        $clientId = 0;
        $clientNom = 'INDEFINI';
        //Pour chaque domaine Zimbra on recupere domaine Kobeye
        $kDom = Sys::getOneData('Parc', 'Domain/Url=' . $d);
        if ($kDom) { // Si domaine Kobeye
            // On recherche son client Kobeye
            $kCli = $kDom->getOneParent('Client');
            if ($kCli) { // Si client Kobeye
                // On recupere son ID et son nom
                $clientId = $kCli->Id;
                $clientNom = $kCli->Nom;
            }
        }


        // On verifie si le nom de domaines existe deja dans le tableau client
        if (!empty($data[$clientId]) && empty($data[$clientId]['Domaines'][$d->get('name')])) {
            $data[$clientId]['Domaines'][$d->get('name')] = array(
                'QuotasLimitDomain' => 0,
                'QuotasUsedDomain' => 0,
                'NbBoiteActive' => 0);
        } else {
            $data[$clientId] = array(
                'Nom' => $clientNom,
                'Domaines' => array(
                    $d->get('name') => array(
                        'QuotasLimitDomain' => 0,
                        'QuotasUsedDomain' => 0,
                        'NbBoiteActive' => 0,
                        'percentageDom' => 0
                    )

                ),
                'NbBoiteAllDomain' => 0,
                'NbBoiteActiveAllDomain' => 0,
                'QuotasLimitAllDomain' => 0,
                'QuotasUsedAllDomain' => 0,
                'NbBoiteAllDomainMailPro' => 0,
                'NbBoiteMailProActive' => 0,
                'QuotasUsedMailProActive' => 0,
                'QuotasLimitMailProActive' => 0,
                'NbBoiteAllDomainMailPop' => 0,
                'NbBoiteMailPopActive' => 0,
                'QuotasUsedMailPopActive' => 0,
                'QuotasLimitMailPopActive' => 0,
                'NbBoiteAllDomainMailBus' => 0,
                'NbBoiteMailBusActive' => 0,
                'QuotasUsedMailBusActive' => 0,
                'QuotasLimitMailBusActive' => 0,
                'percentageCli' => 0
            );
        }
        $accList = $zimbra->getAllAccounts($d);$cosesTemp = array();

        $nbPro = 0;
        $nbPop = 0;
        $nbBus = 0;
        $maxLimit = 0;
        $useQuota = 0;

        foreach ($accList as $account) {



            $cosId = $account->get('zimbraCOSId');
            $cos ='NULL';
            if(isset($cosId) && $cosId != '')
                $cos = $coses[$cosId];

            $accId = $account->get('id');
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['NomCompte'] = $account->get('name');
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['NomUser'] = $account->get('sn');
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['PrenomUser'] = $account->get('givenName');
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'] = $quotas[$accId]['limit'];
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'] = $quotas[$accId]['used'];
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['Status'] = $account->get('zimbraAccountStatus');
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['COS'] = $cos;
            $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['externe'] = $account->get('zimbraIsExternalVirtualAccount');

            // cos par domaines
            if (isset($nbPro)){
                if ($cos == 'MAIL_PRO'){
                    $nbPro += 1;
                    $data[$clientId]['Domaines'][$d->get('name')]['MAIL_PRO'] = $nbPro;
                }
            }
            if (isset($nbPop)){
                if ($cos == 'MAIL_POP'){
                    $nbPop += 1;
                    $data[$clientId]['Domaines'][$d->get('name')]['MAIL_POP'] = $nbPop;
                }
            }
            if (isset($nbBus)){
                if ($cos == 'MAIL_BUS'){
                    $nbBus += 1;
                    $data[$clientId]['Domaines'][$d->get('name')]['MAIL_BUS'] = $nbBus;
                }
            }

            if (isset($maxLimit))
                $maxLimit += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'];
            else
                $maxLimit = $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'];

            if (isset($useQuota))
                $useQuota += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'];
            else
                $useQuota = $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'];

            $data[$clientId]['Domaines'][$d->get('name')]['QuotasLimitDomain'] += $maxLimit;
            $data[$clientId]['Domaines'][$d->get('name')]['QuotasUsedDomain'] += $useQuota;

            $maxLimit = 0;
            $useQuota = 0;

            if ($data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['Status'] != 'closed'){
                $data[$clientId]['Domaines'][$d->get('name')]['NbBoiteActive']++;
                $data[$clientId]['NbBoiteActiveAllDomain']++;
            }
            $data[$clientId]['NbBoiteAllDomain'] +=1;
            $allCC+=$data[$clientId]['NbBoiteAllDomain'];

            if ($cos == 'MAIL_PRO'){
                $data[$clientId]['NbBoiteAllDomainMailPro'] +=1;
                if ($data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['Status'] != 'closed'){
                    $data[$clientId]['NbBoiteMailProActive'] +=1;
                    $data[$clientId]['QuotasUsedMailProActive'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'];
                    $data[$clientId]['QuotasLimitMailProActive'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'];
                }
            }elseif ($cos == 'MAIL_BUS'){
                $data[$clientId]['NbBoiteAllDomainMailBus'] +=1;
                if ($data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['Status'] != 'closed'){
                    $data[$clientId]['NbBoiteMailBusActive'] +=1;
                    $data[$clientId]['QuotasUsedMailBusActive'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'];
                    $data[$clientId]['QuotasLimitMailBusActive'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'];
                }

            }elseif ($cos == 'MAIL_POP'){
                $data[$clientId]['NbBoiteAllDomainMailPop'] +=1;
                if ($data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['Status'] != 'closed'){
                    $data[$clientId]['NbBoiteMailPopActive'] +=1;
                    $data[$clientId]['QuotasUsedMailPopActive'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'];
                    $data[$clientId]['QuotasLimitMailPopActive'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'];
                }
            }
            $data[$clientId]['QuotasLimitAllDomain'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaLimit'];
            $data[$clientId]['QuotasUsedAllDomain'] += $data[$clientId]['Domaines'][$d->get('name')]['Boites'][$accId]['QuotaUsed'];
        }

    }
}
//print_r($data[258]);

usort($data, function ($a, $b) {
    if (strtolower($a['Nom']) > strtolower($b['Nom'])) return 1;
    if (strtolower($a['Nom']) < strtolower($b['Nom'])) return -1;
    return 0;
});

foreach ($data as &$client){
    $client['percentageCli'] = ($client['QuotasUsedAllDomain']/$client['QuotasLimitAllDomain'])*100;
    $client['QuotasLimitAllDomain'] = formatSize($client['QuotasLimitAllDomain']);
    $client['QuotasUsedAllDomain'] = formatSize($client['QuotasUsedAllDomain']);
    $client['QuotasUsedMailProActive'] = formatSize($client['QuotasUsedMailProActive']);
    $client['QuotasLimitMailProActive'] = formatSize($client['QuotasLimitMailProActive']);
    $client['QuotasUsedMailPopActive'] = formatSize($client['QuotasUsedMailPopActive']);
    $client['QuotasLimitMailPopActive'] = formatSize($client['QuotasLimitMailPopActive']);
    $client['QuotasUsedMailBusActive'] = formatSize($client['QuotasUsedMailBusActive']);
    $client['QuotasLimitMailBusActive'] = formatSize($client['QuotasLimitMailBusActive']);
    foreach ($client['Domaines'] as &$domain){
        $domain['percentageDom'] = ($client['QuotasUsedDomain']/$client['QuotasLimitDomain'])*100;
        $domain['QuotasLimitDomain'] = formatSize($domain['QuotasLimitDomain']);
        $domain['QuotasUsedDomain'] = formatSize($domain['QuotasUsedDomain']);
        foreach ($domain['Boites'] as &$account){
            $account['QuotaLimit'] = formatSize($account['QuotaLimit']);
            $account['QuotaUsed'] = formatSize($account['QuotaUsed']);
        }
    }
}
echo 'total compte : '. $allCC;
$vars['data'] = $data;
//print_r($data);
//print_r($accList);

function formatSize($v,$count = 0){
    $unit = array(
        'o','Ko','Mo','Go','To'
    );
    if ($v <= 1024){
        return $v . ' '.$unit[$count];
    }
    return formatSize(floor($v/1024),++$count);
}

$data = serialize($data);
file_put_contents('/tmp/dataSerial.log', $data);