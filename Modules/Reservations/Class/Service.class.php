<?php
class Service extends genericClass {

    public function getHoraires(){
        //Default 9h-21h
        $hDeb = '9:00';
        $hFin = '21:00';
        if(isset($this->HeureOuverture)) $hDeb=$this->HeureOuverture;
        if(isset($this->HeureFermeture)) $hFin=$this->HeureFermeture;

        $horaires = array();
        sscanf($hDeb, "%d:%d", $heuredeb, $minutedeb);
        sscanf($hFin, "%d:%d", $heurefin, $minutefin);

        for($i = $heuredeb;$i< $heurefin; $i++){
            array_push($horaires,(string)$i.':00');
            array_push($horaires,(string)$i.':30');
        }
        if($minutedeb != '00') array_shift($horaires);
        if($minutefin >= 30) array_push($horaires,$heurefin.':30');

        return $horaires;
    }

    function getTarif($ab,$deb,$fin){
        if (!is_object($ab))die('pas un objet client');
        //test tranche horaire
        $heuredeb = (int)date('H',$deb);
        $heurefin = (int)date('H',$fin);
        $minutedeb = (int)date('i',$deb);
        $minutefin = (int)date('i',$fin);

        //Tarif+heure debut + heure fin nécéssaire sinon tarif invalide
        if(isset($this->Tarif4) && $this->Tarif4>0 && $this->HeureDebutTarif4 != '' && $this->HeureFinTarif4 != ''){
            //week end
            $date = date("l", $deb);
            $date = strtolower($date);
            if ($date == "saturday" || $date == "sunday") return $this->Tarif4;

            // jours fériés
            $jour = date('d',$deb);
            $mois = date('m',$deb);

            //01/01
            if ($jour=="01"&&$mois=="01") return $this->Tarif4;
            //01/05
            if ($jour=="01"&&$mois=="05") return $this->Tarif4;
            //08/05
            if ($jour=="08"&&$mois=="05") return $this->Tarif4;
            //14/07
            if ($jour=="14"&&$mois=="07") return $this->Tarif4;
            //15/08
            if ($jour=="15"&&$mois=="08") return $this->Tarif4;
            //01/11
            if ($jour=="01"&&$mois=="11") return $this->Tarif4;
            //11/11
            if ($jour=="11"&&$mois=="11") return $this->Tarif4;
            //25/12
            if ($jour=="25"&&$mois=="12") return $this->Tarif4;

        }
        if(isset($this->Tarif3) && $this->Tarif3>0 && $this->HeureDebutTarif3 != '' && $this->HeureFinTarif3 != ''){
            sscanf($this->HeureDebutTarif3, "%d:%d", $hdt3, $mdt3);
            sscanf($this->HeureFinTarif3, "%d:%d", $hft3, $mft3);
            if(($heuredeb >= (int)$hdt3 && $minutedeb >= (int)$mdt3) && ($heuredeb <= (int)$hft3 && $minutedeb >= (int)$mft3))
                return $this->Tarif3;
        }
        if(isset($this->Tarif2) && $this->Tarif2>0 && $this->HeureDebutTarif2 != '' && $this->HeureFinTarif2 != ''){
            sscanf($this->HeureDebutTarif2, "%d:%d", $hdt2, $mdt2);
            sscanf($this->HeureFinTarif2, "%d:%d", $hft2, $mft2);
            if(($heuredeb >= (int)$hdt2 && $minutedeb >= (int)$mdt2) && ($heuredeb <= (int)$hft2 && $minutedeb >= (int)$mft2))
                return $this->Tarif2;
        }
        if(isset($this->Tarif1) && $this->Tarif1>0 && $this->HeureDebutTarif1 != '' && $this->HeureFinTarif1 != ''){
            sscanf($this->HeureDebutTarif1, "%d:%d", $hdt1, $mdt1);
            sscanf($this->HeureFinTarif1, "%d:%d", $hft1, $mft1);
            if(($heuredeb >= (int)$hdt1 && $minutedeb >= (int)$mdt1) && ($heuredeb <= (int)$hft1 && $minutedeb >= (int)$mft1))
                return $this->Tarif1;
        }



        return $this->Tarif;
    }

    function isHeurePleine($deb,$fin) {
        $heuredeb = (int)date('H',$deb);
        $heurefin = (int)date('H',$fin);
        $minutedeb = (int)date('i',$deb);
        $minutefin = (int)date('i',$fin);
        //créneau 12-14
        if (($heuredeb<14&&$heuredeb>=12)
            || ($heurefin<=14&&($heurefin>12||($heurefin==12&$minutefin>0)))) return true;

        //créneau 18-21
        if (($heuredeb<21&&$heuredeb>=18)
            || ($heurefin<=21&&($heurefin>18||($heurefin==18&$minutefin>0)))) return true;
        //week end
        $date = date("l", $deb);
        $date = strtolower($date);
        if ($date == "saturday" || $date == "sunday") return "true";

        // jours fériés
        $jour = date('d',$deb);
        $mois = date('m',$deb);

        //01/01
        if ($jour=="01"&&$mois=="01") return true;
        //01/05
        if ($jour=="01"&&$mois=="05") return true;
        //08/05
        if ($jour=="08"&&$mois=="05") return true;
        //14/07
        if ($jour=="14"&&$mois=="07") return true;
        //15/08
        if ($jour=="15"&&$mois=="08") return true;
        //01/11
        if ($jour=="01"&&$mois=="11") return true;
        //11/11
        if ($jour=="11"&&$mois=="11") return true;
        //25/12
        if ($jour=="25"&&$mois=="12") return true;

        return false;
    }
}