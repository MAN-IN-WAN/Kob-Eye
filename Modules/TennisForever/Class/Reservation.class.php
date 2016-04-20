<?php
class Reservation extends genericClass {
    var $_partenaires = array();
    var $_produits = array();
    var $_date = null;
    var $_heuredebut = null;

    /**
     * UTILS
     */
    function addLigneFacture() {

    }

    /**
     * GETTER
     */
    function getFacture() {
        $total = $this->getTotal();
        $fact = Sys::getOneData('TennisForever','Reservation/'.$this->Id.'/Facture');
        if ($fact) return $fact;
        elseif ($total >0){
            //création de la facture
            $fact = genericClass::createInstance('TennisForever','Facture');
            $fact->MontantTTC = $total;
            $fact->MontantHT = $total/1.20;
            $fact->addParent($this->getClient());
            $fact->Save();
            
            //on attache aussi toutes les lignes facture
            $lf = Sys::getData('TennisForever','Reservation/'.$this->Id.'/LigneFacture');
            foreach ($lf as $l) {
                $l -> addParent($fact);
                $l->Save();
            }
            
            return $fact;
        }
        return false;
    }
    function getClient() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getOneData('TennisForever','Client/Reservation/'.$this->Id);
            if ($out) return $out;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Client') {
                    return Sys::getOneData('TennisForever','Client/'.$p["Id"]);
                }
            }
        }
        return false;
    }
    function getService() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getOneData('TennisForever','Service/Reservation/'.$this->Id);
            if ($out) return $out;
        }
        if (!$out){
            //on cherche dans le tableau parent temporaire.
            if (is_array($this->Parents))foreach ($this->Parents as $p){
                if ($p["Titre"]=='Service') {
                    return Sys::getOneData('TennisForever','Service/'.$p["Id"]);
                }
            }
        }
        return false;
    }
    function getCourt() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getOneData('TennisForever','Court/Reservation/'.$this->Id);
            if ($out) return $out;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Court') {
                    return Sys::getOneData('TennisForever','Court/'.$p["Id"]);
                }
            }
        }
        return false;
    }

    /**
     * FRONT SETTER
     */
    function setPartenaires($parts){
        if (is_array($parts))foreach ($parts as $p){
            if ($p['Client']>0) {
                //recherche du client
                $cli = Sys::getOneData('TennisForever','Client/'.$p['Client']);
                $part = $cli->getChildren('Partenaire');
                if (!sizeof($part)){
                    //il faut le créer
                    $pa = genericClass::createInstance('TennisForever', 'Partenaire');
                    $pa->Nom = $cli->Nom;
                    $pa->Email = $cli->Email;
                    $pa->Prenom = $cli->Prenom;
                    $pa->addParent($cli);
                    $pa->Save();
                }else{
                    $pa = $part[0];
                }
            }else{
                $pa = genericClass::createInstance('TennisForever', 'Partenaire');
                $pa->Nom = (empty($p['Nom']))?'Invité':$p['Nom'];
                $pa->Email = (empty($p['Email']))?'Invité':$p['Email'];
                $pa->Prenom = (empty($p['Prenom']))?'Invité':$p['Prenom'];
            }
            array_push($this->_partenaires, $pa);
        }
        if (!sizeof($parts)){
            //on ajoute un particpant par défaut
            $pa = genericClass::createInstance('TennisForever', 'Partenaire');
            $pa->Nom = 'Invité';
            $pa->Prenom = 'Invité';
            $pa->Email = 'Invité';
            array_push($this->_partenaires, $pa);
        }
    }

    function setProduits($produit){
        if (is_array($produit))foreach ($produit as $i=>$p){
            if ($p>0) {
                //récupération du produit
                $prod = Sys::getOneData('TennisForever', 'Service/' . $i);
                $ser = genericClass::createInstance('TennisForever', 'LigneFacture');
                $ser->Libelle = $prod->Titre;
                $ser->Type = 'Service';
                $ser->Quantite = $p;
                $ser->MontantTTC = $prod->Tarif*$p;
                $ser->addParent($prod);
                array_push($this->_produits, $ser);
            }
        }
    }
    function setClient($cli) {
        $this->addParent($cli);
    }
    function setCourt($court) {
        $this->addParent($court);
    }
    function setService($service) {
        $this->addParent($service);
    }
    function setDate($date){
        $this->_date = $date;
    }
    function setHeureDebut($heure){
        //transformation de l'heure
        $t = explode(':',$heure);
        $sec = $t[0]*3600;
        $sec+= $t[1]*60;
        $this->_heuredebut = $sec;
    }

    /**
     * CHECKER
     */
    function checkClient() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getCount('TennisForever','Client/Reservation/'.$this->Id);
            if ($out) return true;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Client') return true;
            }
        }
        return false;
    }
    function checkCourt() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getCount('TennisForever','Court/Reservation/'.$this->Id);
            if ($out) return true;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Court') return true;
            }
        }
        return false;
    }
    function checkService() {
        $out=false;
        if ($this->Id){
            //on cherche dans les parents en base
            $out = Sys::getCount('TennisForever','Service/Reservation/'.$this->Id);
            if ($out) return true;
        }
        if (!$out){
            //on cherche dans le tableau parrent temporaire.
            foreach ($this->Parents as $p){
                if ($p["Titre"]=='Service') return true;
            }
        }
        return false;
    }
    function checkDate() {
        if ($this->_date&&$this->_heuredebut){
            //récupération du service pour la durée
            $service = $this->getService();
            if (!$service) return false;

            //definition des heuredebut et heurefin
            $this->DateDebut = $this->_date + $this->_heuredebut;
            $this->DateFin = $this->DateDebut + $service->Duree*60;
        }
        //il faut également le court
        $court = $this->getCourt();
        if (!$court) return false;

        if ($this->DateDebut && $this->DateFin){
            return true;
        }else return false;
    }
    function checkDateJour() {
        if ($this->_date){
            //definition des heuredebut et heurefin
            $this->DateDebut = $this->_date;
            $this->DateFin = $this->DateDebut + 86400;
        }
        //il faut également le court
        $court = $this->getCourt();
        if (!$court) return false;

        if ($this->DateDebut && $this->DateFin){
            return true;
        }else return false;
    }
    function checkDispo() {
        //il faut également le court
        $court = $this->getCourt();
        if (!$court) return false;

        if ($this->DateDebut && $this->DateFin){
            //vérification de la disponibilité
            //test du debut à cheval
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Reservation/Valide=1&DateDebut>='.$this->DateDebut.'&DateDebut<'.$this->DateFin);
            if ($out) return false;
            //test de la fin à cheval
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Reservation/Valide=1&DateFin>'.$this->DateDebut.'&DateFin<='.$this->DateFin);
            if ($out) return false;
            //test des deux en encadrement intérieur
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Reservation/Valide=1&DateFin<='.$this->DateFin.'&DateDebut>='.$this->DateDebut);
            if ($out) return false;
            //test les deux en encadrement extérieur
            $out = Sys::getCount('TennisForever','Court/'.$court->Id.'/Reservation/Valide=1&DateFin>='.$this->DateFin.'&DateDebut<='.$this->DateDebut);
            if ($out) return false;
            return true;
        }else return false;
    }

    function Verify() {
        if (!$this->checkClient()){
            $this->addError(array("Message"=>"Veuillez saisir le client pour cette réservation"));
        }
        if (!$this->checkCourt()){
            $this->addError(array("Message"=>"Veuillez saisir le court pour cette réservation"));
        }
        //on récupère le type de court
        $court= $this->getCourt();
        $tc = $court->getParents('TypeCourt');
        $tc = $tc[0];
        if ($tc->Reservation=='Horaire') {
            if (!$this->checkDate()) {
                $this->addError(array("Message" => "Veuillez vérifier la saisie des dates"));
            }
            //vérifie le client
            if (!$this->checkService()) {
                $this->addError(array("Message" => "Veuillez saisir le service pour cette réservation"));
            }
            if (!$this->checkDispo()) {
                $this->addError(array("Message" => "Cette horaire n'est pas disponible"));
                $this->Valide = false;
            }
        }else{
            if (!$this->checkDateJour()) {
                $this->addError(array("Message" => "Veuillez vérifier la saisie de la date"));
            }
        }
        if (sizeof($this->Error)) return false;

        if (!$this->Id){
            //nouvelle réservation
            
        }
        
        return parent::Verify();
    }

    function Save(){
        $new = false;
        if (!$this->Id) {
            $new = true;
        }
        parent::Save();
        if ($new){
            //Ajout d'une ligne pour la réservation
            $service = $this->getService();
            $client = $this->getClient();
            if ($service) {
                $ser = genericClass::createInstance('TennisForever', 'LigneFacture');
                $ser->Libelle = $service->Titre;
                $ser->Type = "Reservation";
                $ser->Quantite = 1;
                $ser->MontantUnitaireTTC = $service->getTarif($client->isSubscriber(), $this->DateDebut, $this->DateFin);
                $ser->MontantTTC = $ser->MontantUnitaireTTC;
                $ser->addParent($this);
                $ser->Save();
            }


            //Saisie des partenaires.
            if (is_array($this->_partenaires)) foreach ($this->_partenaires as $p) {
                //on vérifie que le partenaire n'est pas abonne
                if ($p->Id >0) {
                    //utilisation du partenaire existant
                    $p->AddParent($this);
                    $p->Save();
                } else {
                    //creation du partenaire
                    $p->AddParent($this);
                    $p->Save();
                }
                if ($client->isSubscriber()) {
                    //si l'invité est abonné alors il ne paye pas
                    $ab = Sys::getOneData('TennisForever', 'Client/Partenaire/' . $p->Id);
                    if ($p && (!$ab || !$ab->isSubscriber() && $service->TarifInvite > 0)) {
                        //on ajoute la ligne de facture
                        $ser = genericClass::createInstance('TennisForever', 'LigneFacture');
                        $ser->Libelle = 'Partenaire ' . $p->Nom . ' - ' . $service->Titre;
                        $ser->Type = "Partenaire";
                        $ser->Quantite = 1;
                        $ser->MontantTTC = $service->TarifInvite;
                        $ser->addParent($this);
                        $ser->Save();
                    }
                }
            }

            //Saisie des produits
            if (is_array($this->_produits)) foreach ($this->_produits as $p) {
                //creation du partenaire
                $p->AddParent($this);
                $p->Save();
            }
        }

        //enregistrement de la réservation
        parent::Save();
    }

    function Delete() {
        if ($this->Id) {
            //suppression des lignesfactures
            $prods = Sys::getData('TennisForever', 'Reservation/'.$this->Id.'/LigneFacture');
            foreach($prods as $p) $p->Delete();

            //suppression de la facture
            $facts = Sys::getData('TennisForever', 'Reservation/'.$this->Id.'/Facture');
            foreach($facts as $f) $f->Delete();

            parent::Delete();
        }
    }
    function getTotal() {
        $lf = Sys::getData('TennisForever','Reservation/'.$this->Id.'/LigneFacture');
        $total = 0;
        foreach ($lf as $l){
            $total+= $l->MontantTTC;
        }
        return $total;
    }

    /**
     * Validation de la réservation
     */
    function setValide() {
        $this->Valide=1;
        $this->Save();
    }
}