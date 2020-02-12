<?php
$vars['dates'] = array();
$month = date('m');
$year = date('Y');

/*for ($i=0;$i<12;$i++){

    $first = mktime(0,0,0,$month,1,$year);
    if($i == 0)
        $last = date('d');
    else
        $last = date('t',$first);

    $value = mktime(0,0,0,$month,$last,$year);


    $selected = ($_GET['date']==$value)?true:false;
    $vars['dates'][] = array('num'=>date('m',$value),'day'=>date('M',$value),'value'=>$value,'selected'=>$selected);


    $month--;
    if($month == 0){
        $month = 12;
        $year--;
    }
}*/
for ($i=0;$i<5;$i++){

    $selected = ($_GET['date']==$value)?true:false;

    if($i == 0){
        $value = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $vars['dates'][] = array('num'=>date('Y'),'day'=>'Jan. - '.date('M').'.','value'=>$value,'selected'=>$selected);
    }else{
        $value = mktime(0,0,0,12,31,$year);
        $vars['dates'][] = array('num'=>$year,'day'=>'Jan. - Dec.','value'=>$value,'selected'=>$selected);
    }




    $year--;
}
$vars['date'] = $_GET['date'];
