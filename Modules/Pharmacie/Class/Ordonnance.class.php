<?php
class Ordonnance extends genericClass {
    function Save () {
        $id = $this->Id;
        parent::Save();
        if (!$id){
            //A la creation on force l'état 1
            $this->Etat=1;
            $usr='';
            $rol='USER';
            //alors c'une nouvelle ordonnance on envoie une notification
 	    AlertUser::addAlert('Nouvelle ordonnance à traiter pour monsieur '.$this->Nom.' '.$this->Prenom,'OR'.$this->Id,'Pharmacie','Ordonnance',$this->Id,$usr,$rol,'icon_contract');
        }
        parent::Save();
    }
}
?>