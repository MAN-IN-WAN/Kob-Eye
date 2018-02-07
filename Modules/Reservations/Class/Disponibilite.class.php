<?php

class Disponibilite extends genericClass {

    public static function getDispo($dateDebut,$dateFin){
        klog::l($dateDebut .' - '.$dateFin);

        //Cas des dispos classiques
        $dispos = Sys::getData('Reservations','Disponibilite/Debut>='.$dateDebut.'&Fin<='.$dateFin.'&Dispo=0',0,1000);

        //Cas des récurentes
        $recus = Sys::getData('Reservations','Disponibilite/RecurrenceHebdo=1&Dispo=0',0,1000);
        if(sizeof($recus)){
            $weekDay = date('D',$dateDebut);
            foreach ($recus as $recu){
                $wDay = date('D',$recu->Debut);
                if($weekDay != $wDay) continue;

                if($recu->DateFinRecurrence <= $recu->Fin) $recu->DateFinRecurrence = 99999999999;

                if($recu->Debut <= $dateDebut && $recu->DateFinRecurrence>=$dateFin){
                    array_push($dispos,$recu);
                }
                if($recu->Debut >= $dateDebut && $recu->Debut <=$dateFin){
                    array_push($dispos,$recu);
                }
                if($recu->DateFinRecurrence >= $dateDebut && $recu->DateFinRecurrence <=$dateFin){
                    array_push($dispos,$recu);
                }
            }
        }
        foreach ($dispos as $d){
            $d->_courts = $d->getParents('Court');
        }

        //klog::l('$dispos',$dispos);

        //Cas des dispos forcées
        $forces = Sys::getData('Reservations','Disponibilite/Debut>'.$dateDebut.'&Debut<='.($dateFin+60).'&Dispo=1&RecurrenceHebdo=0',0,1000);
        $forcesBis = Sys::getData('Reservations','Disponibilite/Fin>'.$dateDebut.'&Fin<='.($dateFin+60).'&Dispo=1&RecurrenceHebdo=0',0,1000);
        foreach($forcesBis as $key=>$fbi){
            foreach($forces as $force){
                if($fbi->Id == $force->Id) unset($forcesBis[$key]);
            }
        }
        $forces = array_merge($forces,$forcesBis);
        //klog::l('$forces',$forces);


        //On ne travaille pas sur les timestamps a cause des recurentes..
        if(sizeof($forces)){

            foreach($forces as $force){
                $courtsForce = $force->getParents('Court');

                $heuredebforce = (int)date('H',$force->Debut);
                $heurefinforce = (int)date('H',$force->Fin);
                $minutedebforce = (int)date('i',$force->Debut);
                $minutefinforce = (int)date('i',$force->Fin);

                $jourdebforce = date('d',$force->Debut);
                $jourfinforce = date('d',$force->Fin);
                if($jourdebforce != $jourfinforce)  $heurefinforce +=24;

                foreach($dispos as $k=>$dispo){
                    $courtsDispo = $dispo->_courts;
                    //On verifie que la disponibilité et le forcage aient des courts en commun sinon on zappe
                    $courts = array_uintersect($courtsDispo,$courtsForce,function($a,$b){
                        if($a->Id > $b->Id) return 1;
                        if($a->Id < $b->Id) return -1;
                        if($a->Id == $b->Id) return 0;
                    });
                    if(!sizeof($courts)) continue;


                    $heuredeb = (int)date('H',$dispo->Debut);
                    $heurefin = (int)date('H',$dispo->Fin);
                    $minutedeb = (int)date('i',$dispo->Debut);
                    $minutefin = (int)date('i',$dispo->Fin);

                    $jourdeb = date('d',$dispo->Debut);
                    $jourfin = date('d',$dispo->Fin);
                    if($jourdeb != $jourfin)  $heurefin +=24;

//                    klog::l('dispo',$dispo);
//                    klog::l('+++++++++++');
//                    klog::l('DDeb '.$heuredeb.':'.$minutedeb);
//                    klog::l('DFin '.$heurefin.':'.$minutefin);
//                    klog::l('--------');
//                    klog::l('FDeb '.$heuredebforce.':'.$minutedebforce);
//                    klog::l('FFin '.$heurefinforce.':'.$minutefinforce);
//                    klog::l('+++++++++++');



                    //Cas ou la periode englobe à la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce <= $minutedeb) || $heuredebforce < $heuredeb) &&
                        (($heurefinforce == $heurefin && $minutefinforce >= $minutefin) || $heurefinforce > $heurefin)
                    ){

                        $dispo->_courts = array_udiff($dispo->_courts,$courts,function($a,$b){
                            if($a->Id > $b->Id) return 1;
                            if($a->Id < $b->Id) return -1;
                            if($a->Id == $b->Id) return 0;
                        }); //On elimine les courts qui sont forcés dispo
                        if(!sizeof($dispo->_courts)) unset($dispos[$k]); //Si la dispo n'a plus de courts on la vire
                        continue;
                    }

                    //Cas ou la periode forcée "mange" la fin de la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce > $minutedeb) || $heuredebforce > $heuredeb) &&
                        (($heuredebforce == $heurefin && $minutedebforce < $minutefin) || $heuredebforce < $heurefin) &&
                        (($heurefinforce == $heurefin && $minutefinforce >= $minutefin) || $heurefinforce > $heurefin)
                    ){
                        $dispo2 = clone($dispo); // On duplique la dispo pour ne garder que la partie valide sur les courts communs
                        $dispo2->Fin = $force->Debut;
                        $dispo2->_courts = $courts;
                        array_push($dispos,$dispo2);

                        $dispo->_courts = array_udiff($dispo->_courts,$courts,function($a,$b){
                            if($a->Id > $b->Id) return 1;
                            if($a->Id < $b->Id) return -1;
                            if($a->Id == $b->Id) return 0;
                        }); //On elimine les courts qui sont forcés dispo
                        if(!sizeof($dispo->_courts)) unset($dispos[$k]); //Si la dispo n'a plus de courts on la vire
                        continue;
                    }
                    //Cas ou la periode forcée "mange" le debut de la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce <= $minutedeb) || $heuredebforce < $heuredeb) &&
                        (($heurefinforce == $heuredeb && $minutefinforce > $minutedeb) || $heurefinforce > $heuredeb) &&
                        (($heurefinforce == $heurefin && $minutefinforce < $minutefin) || $heurefinforce < $heurefin)
                    ){
                        $dispo2 = clone($dispo); // On duplique la dispo pour ne garder que la partie valide sur les courts communs
                        $dispo2->Debut = $force->Fin;
                        $dispo2->_courts = $courts;
                        array_push($dispos,$dispo2);

                        $dispo->_courts = array_udiff($dispo->_courts,$courts,function($a,$b){
                            if($a->Id > $b->Id) return 1;
                            if($a->Id < $b->Id) return -1;
                            if($a->Id == $b->Id) return 0;
                        }); //On elimine les courts qui sont forcés dispo
                        if(!sizeof($dispo->_courts)) unset($dispos[$k]); //Si la dispo n'a plus de courts on la vire
                        continue;
                    }

                    //Cas ou la periode forcée "mange" le milieu de la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce > $minutedeb) || $heuredebforce > $heuredeb) &&
                        (($heuredebforce == $heurefin && $minutedebforce < $minutefin) || $heuredebforce < $heurefin) &&
                        (($heurefinforce == $heuredeb && $minutefinforce > $minutedeb) || $heurefinforce > $heuredeb) &&
                        (($heurefinforce == $heurefin && $minutefinforce < $minutefin) || $heurefinforce < $heurefin)
                    ){
                        $dispo2 = clone($dispo); // On duplique la dispo pour ne garder que la partie valide sur les courts communs (avant force)
                        $dispo2->Fin = $force->Debut;
                        $dispo2->_courts = $courts;
                        array_push($dispos,$dispo2);

                        $dispo3 = clone($dispo); // On duplique la dispo pour ne garder que la partie valide sur les courts communs (apres force)
                        $dispo3->Debut = $force->Fin;
                        $dispo3->_courts = $courts;
                        array_push($dispos,$dispo3);

                        $dispo->_courts = array_udiff($dispo->_courts,$courts,function($a,$b){
                            if($a->Id > $b->Id) return 1;
                            if($a->Id < $b->Id) return -1;
                            if($a->Id == $b->Id) return 0;
                        }); //On elimine les courts qui sont forcés dispo
                        if(!sizeof($dispo->_courts)) unset($dispos[$k]); //Si la dispo n'a plus de courts on la vire
                        continue;
                    }
                }
            }
        }


        return $dispos;
    }


}