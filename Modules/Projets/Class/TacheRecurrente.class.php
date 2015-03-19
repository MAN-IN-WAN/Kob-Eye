<?php
	class TacheRecurrente extends genericClass {
		/**
		* HACK SAVE
		* Cas creation : Generation des tache correspondante sur le même projet en fonction de la date de départ et de la periodicite jusqu'a l'acheance
		* Cas Edition : Modification et/ou creation des taches existantes à venir (ne pas modifier les taches existantes)
		*/
		function Save(){
			genericClass::Save();
			if ($this->Id!=""){
				//Cas Edition
				//Calcul du nombre detache à creer et comparaison avec l'existant (à venir)
		
			}else{
				//Cas Creation
				//Calcul du nombre de tache à créer
				
			}
		}
		/**
		* HACK DELETE
		* Cas Suppression : Suppression des taches à venir (ne pas supprimer les taches passées)
		*/
		function Delete(){
			genericClass::Delete();
		}
	}
?>