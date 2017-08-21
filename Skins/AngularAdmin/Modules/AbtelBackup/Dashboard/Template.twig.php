<?php

function jobsToday(){
    $weekDays =array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');

    $curDay = $weekDays[(int)date('w')];
    $curMonthDay = (int)date('d');
    $curMonth = (int)date('m');

    $nbTodayVm = Sys::getCount('AbtelBackup','VmJob/Enabled=1&(!Mois=*+Mois='.$curMonth.'!)&(!Jour=*+Jour='.$curMonthDay.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$curDay.'=1!)!)');
    if(!$nbTodayVm) $nbTodayVm =0;

    $nbTodaySamba = 0; //Sys::getCount('AbtelBackup','SambaJob/Enabled=1&(!Mois=*+Mois='.$curMonth.'!)&(!Jour=*+Jour='.$curMonthDay.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$curDay.'=1!)!)');
    if(!$nbTodaySamba) $nbTodaySamba =0;

    $nbTodayRemote = 0; //Sys::getCount('AbtelBackup','RemoteJob/Enabled=1&(!Mois=*+Mois='.$curMonth.'!)&(!Jour=*+Jour='.$curMonthDay.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$curDay.'=1!)!)');
    if(!$nbTodayRemote) $nbTodayRemote =0;

    return $nbTodayVm+$nbTodayRemote+$nbTodaySamba;
}

$vars['NbRunning'] = Sys::getCount('AbtelBackup','Activity/Started=1&Success=0&Errors=0');
$vars['NbToday'] = jobsToday();
$vars['NbVm'] = Sys::getCount('AbtelBackup','EsxVm');
$vars['NbSharing'] = Sys::getCount('AbtelBackup','SambaShare');

$vars['Urls'] = array(
        'Activity'=>Sys::getMenu('AbtelBackup/Activity'),
        'EsxVm'=>Sys::getMenu('AbtelBackup/EsxVm'),
        'SambaShare'=>Sys::getMenu('AbtelBackup/SambaShare')
);

//Retourne la taille en Ko d'un dossier/fichier
function getSize($path){
    return AbtelBackup::localExec('du -s -BG '.$path.' | sed \'s/[^0-9]*//g\''); //pour passe en ko virer -BG et mettre -k ou -BK
}
function getSubFolders($path){
    return glob($path.'/*',GLOB_ONLYDIR);
}


$baseColors = array(
    '#e6194b',
    '#3cb44b',
    '#ffe119',
    '#0082c8',
    '#f58231',
    '#911eb4',
    '#46f0f0',
    '#f032e6',
    '#d2f53c',
    '#fabebe',
    '#008080',
    '#e6beff',
    '#aa6e28',
    '#fffac8',
    '#800000',
    '#aaffc3',
    '#808000',
    '#ffd8b1',
    '#000080',
    '#FFFFFF',
    '#000000');

//Space Pie
$baseVolume = '/backup';

//traitement
if(is_dir($baseVolume)){
    $tempSize = AbtelBackup::localExec('df -BG --output=size '.$baseVolume.' | tail -n 1'); //pour passe en ko virer -BG

    //Recup la liste des sous-dossiers
    $subs = getSubFolders($baseVolume);

    $subsInfo = array();
    foreach($subs as $sub){
        $subsInfo[basename($sub)] = getSize($sub);
        $tempSize -= $subsInfo[basename($sub)];
    }
    $subsInfo['Libre'] = $tempSize;


    $labels = implode('","',array_keys($subsInfo));
    $data = implode(',',$subsInfo);
    $usedColors = array_splice($baseColors,0,sizeof($subsInfo));
    array_splice($usedColors,-1,1,'#a0a0a0');
    $colors= implode('","',$usedColors);

    $vars['pieHtml'] =
        '    
            <canvas id="chart-pie" width="400" height="300"></canvas>
            <script type="text/javascript">
                    // PIE CHART
                    var pieCtx = document.getElementById("chart-pie").getContext("2d");
    
                    var dataPie = {
                            labels: [
                                "'.$labels.'"
                            ],
                            datasets: [{
                                data: ['.$data.'],
                                backgroundColor: [
                                   "'.$colors.'"
                                ],
                                hoverBackgroundColor: [
                                    "'.$colors.'"
                                ]
                            }]
                    };
    
                    new Chart(pieCtx, {
                        type: "pie",
                        data: dataPie
                    });
            </script>
    ';

}


