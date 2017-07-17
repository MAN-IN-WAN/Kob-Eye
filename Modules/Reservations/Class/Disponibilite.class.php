<?php

class Disponibilite extends genericClass {

    public static function getDispo($dateDebut,$dateFin){

        //Cas des dispos classiques
        $dispos = Sys::getData('Reservations','Disponibilite/Debut>='.$dateDebut.'&Fin<='.$dateFin.'&Dispo=0',0,1000);


        //Cas des récurentes
        $recus = Sys::getData('Reservations','Disponibilite/RecurrenceHebdo=1&Dispo=0',0,1000);
        if(sizeof($recus)){
            $weekDay = date('D',$dateDebut);
            foreach ($recus as $recu){
                $wDay = date('D',$recu->Debut);
                if($weekDay != $wDay) continue;

                if($recu->Debut <= $dateDebut && $recu->DateFinRecurrence>=$dateFin)
                    array_push($dispos,$recu);
                if($recu->Debut >= $dateDebut && $recu->Debut <=$dateFin)
                    array_push($dispos,$recu);
                if($recu->DateFinRecurrence >= $dateDebut && $recu->DateFinRecurrence <=$dateFin)
                    array_push($dispos,$recu);
            }
        }

        //Cas des dispos forcées
        $forces = Sys::getData('Reservations','Disponibilite/Debut>'.$dateDebut.'&Debut<='.($dateFin+60).'&Dispo=1&RecurrenceHebdo=0',0,1000);
        $forcesBis = Sys::getData('Reservations','Disponibilite/Fin>'.$dateDebut.'&Fin<='.($dateFin+60).'&Dispo=1&RecurrenceHebdo=0',0,1000);
        foreach($forcesBis as $key=>$fbi){
            foreach($forces as $force){
                if($fbi->Id == $force->Id) unset($forcesBis[$key]);
            }
        }
        $forces = array_merge($forces,$forcesBis);
//        klog::l('$forces',$forces);

        if(sizeof($forces)){

            foreach($forces as $force){
                $heuredebforce = (int)date('H',$force->Debut);
                $heurefinforce = (int)date('H',$force->Fin);
                $minutedebforce = (int)date('i',$force->Debut);
                $minutefinforce = (int)date('i',$force->Fin);

                $jourdebforce = date('d',$force->Debut);
                $jourfinforce = date('d',$force->Fin);
                if($jourdebforce != $jourfinforce)  $heurefinforce +=24;

                foreach($dispos as $dispo){
                    $heuredeb = (int)date('H',$dispo->Debut);
                    $heurefin = (int)date('H',$dispo->Fin);
                    $minutedeb = (int)date('i',$dispo->Debut);
                    $minutefin = (int)date('i',$dispo->Fin);

                    $jourdeb = date('d',$dispo->Debut);
                    $jourfin = date('d',$dispo->Fin);
                    if($jourdeb != $jourfin)  $heurefin +=24;


//                    klog::l($heuredeb.':'.$minutedeb);
//                    klog::l($heurefin.':'.$minutefin);
//                    klog::l('--------');
//                    klog::l($heuredebforce.':'.$minutedebforce);
//                    klog::l($heurefinforce.':'.$minutefinforce);

                    //Cas ou la periode forcée "mange" la fin de la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce >= $minutedeb) || $heuredebforce > $heuredeb) &&
                        (($heuredebforce == $heurefin && $minutedebforce <= $minutefin) || $heuredebforce < $heurefin) &&
                        (($heurefinforce == $heurefin && $minutefinforce >= $minutefin) || $heurefinforce > $heurefin)
                    ){
                        $dispo->Fin = $force->Debut;
                    }
                    //Cas ou la periode forcée "mange" le debut de la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce <= $minutedeb) || $heuredebforce < $heuredeb) &&
                        (($heurefinforce == $heuredeb && $minutefinforce >= $minutedeb) || $heurefinforce > $heuredeb) &&
                        (($heurefinforce == $heurefin && $minutefinforce <= $minutefin) || $heurefinforce < $heurefin)
                    ){
                        $dispo->Debut = $force->Fin;
                    }

                    //Cas ou la periode forcée "mange" le milieu de la periode d'indisponobilité
                    if(
                        (($heuredebforce == $heuredeb && $minutedebforce > $minutedeb) || $heuredebforce > $heuredeb) &&
                        (($heuredebforce == $heurefin && $minutedebforce < $minutefin) || $heuredebforce < $heurefin) &&
                        (($heurefinforce == $heuredeb && $minutefinforce > $minutedeb) || $heurefinforce > $heuredeb) &&
                        (($heurefinforce == $heurefin && $minutefinforce < $minutefin) || $heurefinforce < $heurefin)
                    ){
                        $dispo2 = clone($dispo);
                        $dispo->Fin = $force->Debut;
                        $dispo2->Debut = $force->Fin;
                        array_push($dispos,$dispo2);
                    }
                }
            }
        }



//klog::l('$dispos',$dispos);

        return $dispos;
    }


}