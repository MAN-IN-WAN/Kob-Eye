<?php

class Ticket extends AbtelGestionBase {
	
	/*
	 * demande l'enregistrement id
	 */
	static function getOneData($id) {
		$sql = "
select t.NumeroTicket,t.CodeEntite,t.taclient as CodeClient,t.tatype as Type,t.tacateg as Categorie,t.tatitre as Titre
from taches t
where t.NumeroTicket='$id'";
		$sql1 = "
select l.accadre as Cadre,l.actitre as Titre,l.acdatecreation as Date,l.acheuredeb as HeureDebut,l.acheurefin as HeureFin,
l.actypeduree as TypeDuree
from actions l l 
where l.NumeroTicket='$id'
order by Id desc";
		$result = AbtelGestion::getRecord($sql, $sql1);
		return Array($sql, $sql1, $result);
	}

	/*
	 * demande une liste d'enregistrements
	 */
	static function getData($Filters, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy) {
		$sql = "
select t.NumeroTicket,t.CodeEntite,t.taclient as CodeClient,t.tatype as Type,t.tacateg as Categorie,t.tatitre as Titre
from taches t
order by t.NumeroTicket
linit 1000";
//		$where = $order = $limit = '';
		$result = AbtelGestion::getRecords($sql);
//		$result = self::getRecords($sql, $where, $order, $limit);
		return $result;
	}
	
	function Save() {
		
	}

}
