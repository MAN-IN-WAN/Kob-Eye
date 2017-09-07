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
function humanReadable($Mo){
    $units = array('Mo','Go','To','Po');

    $pow = 0;
    while($Mo > 1024){
        $Mo /= 1024;
        $pow++;
    }
    $Mo = number_format($Mo,2);
    return $Mo . $units[$pow];
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
//function getSize($path){
//   return AbtelBackup::localExec('du -s -BG '.$path.' | sed \'s/[^0-9]*//g\''); //pour passe en ko virer -BG et mettre -k ou -BK
//}
//function getSubFolders($path){
//    return glob($path.'/*',GLOB_ONLYDIR);
//}


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

///////////////////Space Pie

//traitement
//if(is_dir($baseVolume)){
//    $tempSize = AbtelBackup::localExec('df -BG --output=size '.$baseVolume.' | tail -n 1'); //pour passe en ko virer -BG
//
//    //Recup la liste des sous-dossiers
//    $subs = AbtelBackup::getSubFolders($baseVolume);
//
//    $subsInfo = array();
//    foreach($subs as $sub){
//        $subsInfo[basename($sub)] = AbtelBackup::getSize($sub);
//        $tempSize -= $subsInfo[basename($sub)];
//    }
//    $subsInfo['Libre'] = $tempSize;
//
//
//    $labels = implode('","',array_keys($subsInfo));
//    $data = implode(',',$subsInfo);
//    $usedColors = array_splice($baseColors,0,sizeof($subsInfo));
//    array_splice($usedColors,-1,1,'#a0a0a0');
//    $colors= implode('","',$usedColors);
//
//    $vars['pieHtml'] =
//        '
//            <canvas id="chart-pie" width="400" height="300"></canvas>
//            <script type="text/javascript">
//                    // PIE CHART
//                    var pieCtx = document.getElementById("chart-pie").getContext("2d");
//
//                    var dataPie = {
//                            labels: [
//                                "'.$labels.'"
//                            ],
//                            datasets: [{
//                                data: ['.$data.'],
//                                backgroundColor: [
//                                   "'.$colors.'"
//                                ],
//                                hoverBackgroundColor: [
//                                    "'.$colors.'"
//                                ]
//                            }]
//                    };
//
//                    new Chart(pieCtx, {
//                        type: "pie",
//                        data: dataPie
//                    });
//            </script>
//    ';
//}

$store = Sys::getOneData('AbtelBackup','BackupStore/Titre=Sauvegarde Locale');
$nfsSize = $store->NfsSize != null ? $store->NfsSize:0;
$borgSize = $store->BorgSize != null ? $store->BorgSize:0;
$totalSize = $store->Size != null ? $store->Size:0;
$freeSize = $totalSize - $borgSize - $nfsSize;
$labels = implode('","',array('Nfs '.humanReadable($nfsSize),'Backup '.humanReadable($borgSize),'Disponible '.humanReadable($freeSize)));
$data = implode(',',array($nfsSize,$borgSize,$freeSize));
$colors = implode('","',array('#e6194b','#3cb44b','#c0c0c0'));

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

///////////////////Space Bars
$vms = Sys::getData('AbtelBackup','EsxVm');
$dataSetVm = array();
$dataSetBorg = array();
$labels = array();
$nbvm =0;
foreach($vms as $vm){
    if($vm->BackupSize <= 1) continue;
    $borg = $vm->getOneParent('BorgRepo');
    $dataSetVm[]=$vm->BackupSize;
    $dataSetBorg[]=$borg->Size;
    $labels[] = '["'.implode('","',explode(' ',$vm->Titre)).'"]';
    $nbvm++;
}

$data1 = implode(',',$dataSetVm);
$data2 = implode(',',$dataSetBorg);
$labels = implode(',',$labels);
$colors1a =  implode(',',array_fill(0,$nbvm,'"rgba(255, 99, 132, 0.2)"'));
$colors1b =  implode(',',array_fill(0,$nbvm,'"rgb(255, 99, 132)"'));
$colors2a =  implode(',',array_fill(0,$nbvm,'"rgba(54, 162, 235, 0.2)"'));
$colors2b =  implode(',',array_fill(0,$nbvm,'"rgb(54, 162, 235)"'));
$nbvm*=75;

$vars['barsHtml'] =
    '    
            <canvas id="chart-bar" width="400" height="250" style="height:250px;"></canvas>
            <script type="text/javascript">
                    // BAR CHART
                    var barCtx = document.getElementById("chart-bar").getContext("2d");
                    var dataBar = {
                            labels: [
                                '.$labels.'
                            ],
                            datasets: [{
                                label: "Taille Vm (Mo)",
                                data: ['.$data1.'],
                                "fill": false,
                                "backgroundColor": ['.$colors1a.'],
                                "borderColor": ['.$colors1b.'],
                                "borderWidth": 2
                            },{
                                label: "Taille Backup (Mo)",
                                data: ['.$data2.'],
                                "fill": false,
                                "backgroundColor": ['.$colors2a.'],
                                "borderColor": ['.$colors2b.'],
                                "borderWidth": 2
                            }]
                    };
    
                    new Chart(barCtx, {
                        type: "bar",
                        data: dataBar,
                        options: {
                            responsive: true, 
                            maintainAspectRatio: false,
                            scales: {
                                xAxes: [{
                                    /*barThickness : 20,*/
                                    categorySpacing: 5/*,
                                    stacked: true*/
                                }]
                            },
                            tooltips: {
                                custom: function(tooltipModel) {
                                    // Tooltip Element
                                    var tooltipEl = document.getElementById(\'chartjs-tooltip\');
                    
                                    // Create element on first render
                                    if (!tooltipEl) {
                                        tooltipEl = document.createElement(\'div\');
                                        tooltipEl.id = \'chartjs-tooltip\';
                                        tooltipEl.innerHTML = "<table></table>"
                                        document.body.appendChild(tooltipEl);
                                    }
                    
                                    // Hide if no tooltip
                                    if (tooltipModel.opacity === 0) {
                                        tooltipEl.style.opacity = 0;
                                        return;
                                    }
                    
                                    // Set caret Position
                                    tooltipEl.classList.remove(\'above\', \'below\', \'no-transform\');
                                    if (tooltipModel.yAlign) {
                                        tooltipEl.classList.add(tooltipModel.yAlign);
                                    } else {
                                        tooltipEl.classList.add(\'no-transform\');
                                    }
                    
                                    function getBody(bodyItem) {
                                        return bodyItem.lines;
                                    }
                    
                                    // Set Text
                                    if (tooltipModel.body) {
                                        var titleLines = tooltipModel.title || [];
                                        var bodyLines = tooltipModel.body.map(getBody);
                    
                                        var innerHtml = \'<thead>\';
                    
                                        titleLines.forEach(function(title) {
                                            innerHtml += \'<tr><th>\' + title +  \'aaaaaaaa</th></tr>\';
                                        });
                                        innerHtml += \'</thead><tbody>\';
                    
                                        bodyLines.forEach(function(body, i) {
                                            var colors = tooltipModel.labelColors[i];
                                            var style = \'background:\' + colors.backgroundColor;
                                            style += \'; border-color:\' + colors.borderColor;
                                            style += \'; border-width: 2px\';
                                            var span = \'<span class="chartjs-tooltip-key" style="\' + style + \'"></span>\';
                                            innerHtml += \'<tr><td>\' + span + body + \'</td></tr>\';
                                        });
                                        innerHtml += \'</tbody>\';
                    
                                        var tableRoot = tooltipEl.querySelector(\'table\');
                                        tableRoot.innerHTML = innerHtml;
                                    }
                    
                                    // `this` will be the overall tooltip
                                    var position = this._chart.canvas.getBoundingClientRect();
                    
                                    // Display, position, and set styles for font
                                    tooltipEl.style.opacity = 1;
                                    tooltipEl.style.left = position.left + tooltipModel.caretX + \'px\';
                                    tooltipEl.style.top = position.top + tooltipModel.caretY + \'px\';
                                    tooltipEl.style.fontFamily = tooltipModel._fontFamily;
                                    tooltipEl.style.fontSize = tooltipModel.fontSize;
                                    tooltipEl.style.fontStyle = tooltipModel._fontStyle;
                                    tooltipEl.style.padding = tooltipModel.yPadding + \'px \' + tooltipModel.xPadding + \'px\';
                                }
                            }
                        }
                    });
            </script>
    ';



