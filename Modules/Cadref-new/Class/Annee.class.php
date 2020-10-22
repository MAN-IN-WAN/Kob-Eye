<?php
class Annee extends genericClass {
	
	function SelectAnnee($args) {
		$an = Sys::getOneData('Cadref', 'Annee/EnCours=1');
		$old = $an->Annee;
		$an->EnCours = 0;
		$an->Save();

		$new = $args['Annee'];
		$an = Sys::getOneData('Cadref', 'Annee/Annee='.$new);
		$an->EnCours = 1;
		$an->Save();

		Cadref::$Annee = $new;
		return array('old'=>$old, 'new'=>$new);
	}
	
	public static function CreateAnnee($args) {
		$annee = $args['Annee'];
		$an = genericClass::createInstance('Cadref', 'Annee');
		$an->Annee = $annee;
		$an->EnCours = 0;
		$an->Cotisation = $args['Cotisation'];
		$an->Reduction = $args['Reduction'];
		$an->Save();
		$id = $an->Id;
		
		$cls = Sys::getData('Cadref','Classe/Annee='.$args['Last']);
		$i = 0;
		foreach($cls as $cl) {
			$nc = genericClass::createInstance('Cadref', 'Classe');
			$nc->Annee = $annee;
			$nc->addParent($cl->getOneParent('Niveau'));
			$nc->Classe = $cl->Classe;
			$nc->JourId = $cl->JourId;
			$nc->HeureDebut = $cl->HeureDebut;
			$nc->HeureFin = $cl->HeureFin;
			$nc->CycleDebut = $cl->CycleDebut;
			$nc->CycleFin = $cl->CycleFin;
			$nc->Seances = $cl->Seances;
			$nc->Programmation = $cl->Programmation;
			$nc->addParent($cl->getOneParent('Lieu'));
			$nc->Places = $cl->Places;
			$nc->Prix = $cl->Prix;
			$nc->Reduction1 = $cl->Reduction1;
			$nc->Reduction2 = $cl->Reduction2;
			$nc->Notes = $cl->Notes;
			$nc->AccesWeb = 0;
			$nc->DateReduction1 = strtotime(($annee+1).'-01-01');
			$nc->DateReduction2 = strtotime(($annee+1).'-03-01');
			$ens = $cl->getParents('Enseignant');
			foreach($ens as $en) $nc->addParent($en);
			$nc->Save();
		}
		return true;
	}
	

}

