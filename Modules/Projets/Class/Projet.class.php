<?php
	class Projets_Projet extends genericClass {
		/**
		* HACK SAVE
		*/
		function Save(){
			genericClass::Save();
			if ($this->Id>0){
				//Cas Edition
				
			}else{
				//Cas Creation
				
			}
			return true;
		}
		/**
		* HACK DELETE
		* Cas Suppression : Suppression des taches à venir (ne pas supprimer les taches passées)
		*/
		function Delete(){
			genericClass::Delete();
		}
		function getTaskProject($id, $offset, $limit, $sort, $order, $filter, $start, $end, $parentMod,$parentObj,$parentId){
			$out= Array();
			$tasks = Sys::getData('Projets','Projet/'.$parentId.'/Tache', 0, 1000, 'ASC', 'DateDebut');
			for ($i=0;$i<sizeof($tasks); $i++){
				//recupération du status
				$t = $tasks[$i];
				$status = Sys::getData('Projets','Status/'.$t->Status,0,1);
				$status = $status[0];
				//{id:1, title:"Test de projet 1", startDate:new Date(2013, 9, 2), endDate:new Date(2013, 9, 15), status:new StatusVO(1), percentComplete:15}
				$o = (Object) Array(
					'id' => $t->Id,
					'title' => $t->Nom,
					'startDate' => $t->DateDebut,
					'endDate' => $t->DateFin,
					'status' => (Object) Array('id' => $status->Code),
					'percentComplete' => $t->AvancementReel
				);
				$out[] = $o;
			}
			$c = sizeof ($out);
			return WebService::WSData('',0,$c,$c,'','','','','',$out);
		}
	}
?>