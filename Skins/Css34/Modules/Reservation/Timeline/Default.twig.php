<?php
$vars['dates'] = array();
$now = time();

$vars['Jour']=array();
$vars['Jour']["Mon"]="Lun.";
$vars['Jour']["Tue"]="Mar.";
$vars['Jour']["Wed"]="Mer.";
$vars['Jour']["Thu"]="Jeu.";
$vars['Jour']["Fri"]="Ven.";
$vars['Jour']["Sat"]="Sam.";
$vars['Jour']["Sun"]="Dim.";

$vars['Mois']=array();
$vars['Mois']["01"]="Jan.";
$vars['Mois']["02"]="Fév.";
$vars['Mois']["03"]="Mar.";
$vars['Mois']["04"]="Avr.";
$vars['Mois']["05"]="Mai.";
$vars['Mois']["06"]="Jui.";
$vars['Mois']["07"]="Juil.";
$vars['Mois']["08"]="Aoû.";
$vars['Mois']["09"]="Sep.";
$vars['Mois']["10"]="Oct.";
$vars['Mois']["11"]="Nov.";
$vars['Mois']["12"]="Déc.";

for ($i=0;$i<31;$i++){
    $now+=86400;
    $value = mktime(0,0,0,date('m',$now),date('d',$now),date('Y',$now));
    $selected = ($_GET['date']==$value)?true:false;
    $vars['dates'][] = array('num'=>date('d',$now),'day'=>date('D',$now),'jour'=>$vars['Jour'][date('D',$now)],'mois'=>$vars['Mois'][date('m',$now)],'value'=>$value,'selected'=>$selected);
}
$vars['search'] = $_GET['search'];
$vars['date'] = $_GET['date'];
$vars['genre'] = $_GET['genre'];