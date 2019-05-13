<?php
class Question extends genericClass{
    /**
     * getCategoriebloquante
     * Recherche dela categorie bloquante d'une question
     */
    function getCategorieBloquante() {
        $c = Sys::getOneData('Formation','Categorie/Question/'.$this->Id);
        if (is_object($c))
            return $c->getCategorieBloquante();
        else return false;
    }
    /**
     *
     */
    function getCategoryBreadcrumb() {
        $out = array();
        $c = Sys::getOneData('Formation','Categorie/Question/'.$this->Id);
        if (is_object($c))
           $out = array_merge($out,$c->getCategoryBreadcrumb());
        array_push($out,$this);
        return $out;
    }

    function Delete() {
        $questions = $this->getChildren('TypeQuestion');
        foreach ($questions as $q) $q->Delete();
        parent::Delete();
    }



    static function traiterTypeReponse($tr,$session,$TypeQuestionId){
        $html = '';
        $data = Sys::getData('Formation','Session/'.$session.'/Equipe/*/Reponse/TypeQuestionId='.$TypeQuestionId);
        $tp = Sys::getOneData('Formation','TypeQuestion/'.$TypeQuestionId);
        switch($tr){
            case 8:
                $params = json_decode($tp->Parametres,true);
                $titles = $params['Titres'];
                $color = '';
                $bgColor = '#eee';
                if(!empty($params['Couleur'])) {
                        $bgColor = $params['Couleur'];
                        $color= 'textColor:"'.$params['Couleur'].'",';
                }

                $vals = array();
                foreach($data as $v){
                    $vals[] = json_decode($v->Valeur);
                }
                $qty = count($vals);
                $init = array_fill(0,count($vals[0]),0);

                $res = array_reduce($vals,function($carry,$item){
                    for($n =0;$n < count($carry); $n++ ){
                        $carry[$n] += $item[$n];
                    }
                    return $carry;
                },$init);
                $moy = array_map(function($a) use ($qty){
                    return round($a / $qty);
                },$res);

                $values = json_encode($moy);
                $titres =json_encode($titles);

                $html = '<div id="spidergraphcontainer" class="spidergraphcontainer"></div>
                    
                    <script>
                    $(document).ready( function() {
                    
                        $(\'#spidergraphcontainer\').spidergraph({
                            \'fields\': '.$titres.',
                            \'gridcolor\': \'rgba(20,20,20,1)\',
                            '.$color.'
                            \'increments\':'.$params['Max'].',
                            \'overIncrements\':true,
                            \'minrad\':0,
                            \'axisValuesType\':2,
                            \'gridBackground\':\''.$bgColor.'\'
                        });
                         $(\'#spidergraphcontainer\').spidergraph(\'addlayer\', {
                            \'strokecolor\': \'rgba(230,104,0,0.8)\',
                            \'fillcolor\': \'rgba(230,104,0,0.6)\',
                            \'data\': '.$values.',
                            \'linear\': true
                        });
                    
                    });
                    </script>';

            break;
            case 9:
                $len = 400;
                $valueCount = 5;// Echelle allant de 0 a 4

                $params = json_decode($tp->Parametres,true);
                $titles = $params['Titres'];
                $color = '';
                if(!empty($params['Couleur']))
                    $color = 'background-color:'.$params['Couleur'].';';

                $vals = array();
                foreach($data as $v){
                    $vals[] = json_decode($v->Valeur);
                }
                $qty = count($vals);
                $init = array_fill(0,count($vals[0]),0);

                $res = array_reduce($vals,function($carry,$item){
                    for($n =0;$n < count($carry); $n++ ){
                        $carry[$n] += $item[$n];
                    }
                    return $carry;
                },$init);
                $moy = array_map(function($a) use ($qty){
                    return $a / $qty;
                },$res);
                $fLen = array_map(function($a) use($valueCount,$len){
                    return $a/$valueCount*$len;
                },$moy);

                $html .= '<div class="well">';
                foreach($fLen as $key=>$val){
                    $html .= '<h3>'.$titles[$key].'</h3>
                              <div class="plusmoinswrapper" style="width: '.$len.'px;">
                                  <div class="colorBar" style="width: '.$val.'px;'.$color.'"></div>
                                  <div class="symbBar">
                                      <span>--</span><span>-</span><span>=</span><span>+</span><span>++</span>
                                  </div>
                              </div>';
                }
                $html .= '</div>';
                break;
            case 10:
                $params = json_decode($tp->Parametres,true);
                $titles = $params['Titres'];
                $colors = array_fill(0,count($titles),'');
                if(!empty($params['Couleurs'])) {
                    foreach($params['Couleurs'] as $k=>$c) {
                        $colors[$k] = 'color:"'.$c.'",';
                    }
                }

                $vals = array();
                foreach($data as $v){
                    $values = json_decode($v->Valeur);
                    foreach ($values as $vs){
                        $vals[] = $vs;
                    }
                }
                $qty = count($vals);
                $init = array_fill(0,count($vals[0]),0);

                $res = array_reduce($vals,function($carry,$item){
                    for($n =0;$n < count($carry); $n++ ){
                        $carry[$n] += $item[$n];
                    }
                    return $carry;
                },$init);
                $moy = array_map(function($a) use ($qty){
                    return $a / $qty;
                },$res);

                $values = json_encode($moy);
                $titres =json_encode($titles);
                $incr = $params['Max'] - $params['Min'];

                $html = '<div id="spidergraphcontainer" class="spidergraphcontainer"></div>
                    
                    <script>
                    $(document).ready( function() {
                    
                        $(\'#spidergraphcontainer\').spidergraph({
                            \'fields\': '.$titres.',
                            \'gridcolor\': \'rgba(20,20,20,1)\',
                            \'increments\':'.$incr.',
                            \'overIncrements\':false,
                            \'minrad\':0,
                            \'axisValuesType\':1
                        });
                         $(\'#spidergraphcontainer\').spidergraph(\'addlayer\', {
                            \'strokecolor\': \'rgba(230,104,0,0.8)\',
                            \'fillcolor\': \'rgba(230,104,0,0.6)\',
                            \'data\': '.$values.',
                            \'linear\': true
                        });
                    
                    });
                    </script>';


                break;
            case 12:
                $params = json_decode($tp->Parametres,true);
                $titles = $params['Titres'];
                $colors = array_fill(0,count($titles),'');
                if(!empty($params['Couleurs'])) {
                   foreach($params['Couleurs'] as $k=>$c) {
                       $colors[$k] = 'color:"'.$c.'",';
                   }
                }

                $vals = array();
                foreach($data as $v){
                    $values = json_decode($v->Valeur);
                    foreach ($values as $vs){
                        $vals[] = $vs;
                    }
                }
                $qty = count($vals);
                $init = array_fill(0,count($vals[0]),0);

                $res = array_reduce($vals,function($carry,$item){
                    for($n =0;$n < count($carry); $n++ ){
                        $carry[$n] += $item[$n];
                    }
                    return $carry;
                },$init);
                $moy = array_map(function($a) use ($qty){
                    return $a / $qty;
                },$res);

                $pjs = '';
                foreach($moy as $key=>$m){
                    $pjs .= '{
                                value: '.$m.',
                                        '.$colors[$key].'
                                        label: "'.$titles[$key].'"
                              },';
                }
                $pjs = trim($pjs,',');

                $html .= '  <canvas id="myChart" width="500" height="500" style="width: 55%;margin-left: 12%"></canvas>
                    
                            <script>
                                // Get context with jQuery - using jQuery\'s .get() method.
                                var ctx = $("#myChart").get(0).getContext("2d");
                                var data = ['.$pjs.'];
                    
                    
                                var myNewChart = new Chart(ctx).Pie(data, {
                                    //Boolean - Whether we should show a stroke on each segment
                                    segmentShowStroke : true,
                                    //String - The colour of each segment stroke
                                    segmentStrokeColor : "#fff",
                                    //Number - The width of each segment stroke
                                    segmentStrokeWidth : 2,
                                    //Number - The percentage of the chart that we cut out of the middle
                                    percentageInnerCutout : 0, // This is 0 for Pie charts
                                    //Number - Amount of animation steps
                                    animationSteps : 100,
                                    //String - Animation easing effect
                                    animationEasing : "easeOutBounce",
                                    //Boolean - Whether we animate the rotation of the Doughnut
                                    animateRotate : true,
                                    //Boolean - Whether we animate scaling the Doughnut from the centre
                                    animateScale : false,
                                    //String - A legend template
                                    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
                                });
                    
                            </script>';



                break;
            case 13:
                $qty = 3;
                $params = json_decode($tp->Parametres,true);
                if(!empty($params) && !empty($params['Quantite'])) $qty = $params['Quantite'];
                $NbR = Sys::getCount('Formation','Session/'.$session.'/Equipe/*/Reponse/TypeQuestionId='.$TypeQuestionId);
                $NbR *= $qty;

                $vals = $tp->getChildren('TypeQuestionValeur');
                $labels = array();
                $ds = array();

                $reps = array();
                foreach($data as $v){
                    $values = json_decode($v->Valeur);
                    foreach ($values as $vas){
                        if(empty($reps[$vas])){
                            $reps[$vas] = 1;
                        } else {
                            $reps[$vas] +=1;
                        }
                    }
                }

                foreach($vals as $vs){
                    $labels[] = $vs->Valeur;
                    $ds[] = floor($reps[$vs->Id]/$NbR*100);
                }
                $ds = array('label'=> 'toto',
                    'fillColor'=> "rgba(151,187,205,0.5)",
                    'strokeColor'=> "rgba(151,187,205,0.8)",
                    'highlightFill'=> "rgba(151,187,205,0.75)",
                    'highlightStroke'=> "rgba(151,187,205,1)",
                    'data'=> $ds
                );
                        //print_r($ds);
        $html .= '
                <canvas id="myChart" width="500" height="500" style="width: 75%;margin-left: 12%"></canvas>

                <script>
            
                        // Get context with jQuery - using jQuery\'s .get() method.
                        var ctx = $("#myChart").get(0).getContext("2d");
                        var data = {
                            labels: '.json_encode($labels).',
                            datasets: ['.json_encode($ds).']
                        };
                        var myNewChart = new Chart(ctx).Bar(data, {
                        scaleBeginAtZero : true,
                        
                            //Boolean - Whether grid lines are shown across the chart
                        scaleShowGridLines : true,
                        
                            //String - Colour of the grid lines
                        scaleGridLineColor : "rgba(0,0,0,.05)",
                        
                            //Number - Width of the grid lines
                        scaleGridLineWidth : 1,
                        
                            //Boolean - Whether to show horizontal lines (except X axis)
                        scaleShowHorizontalLines: true,
                        
                            //Boolean - Whether to show vertical lines (except Y axis)
                        scaleShowVerticalLines: true,
                        
                            //Boolean - If there is a stroke on each bar
                        barShowStroke : true,
                        
                            //Number - Pixel width of the bar stroke
                        barStrokeWidth : 2,
                        
                            //Number - Spacing between each of the X value sets
                        barValueSpacing : 5,
                        
                            //Number - Spacing between data sets within X values
                        barDatasetSpacing : 1,
                        
                            //String - A legend template
                        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
                        });
                        
                 </script>';

                break;
        }

        return $html;
    }
}