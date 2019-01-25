<?php

class AbtelTache extends AbtelGestionBase {
	/*
	 * nombre d'enregistrements
	 */
	static function getCount($Query) {
		$sql = "select count(*) as cnt from taches";
		if($Query) $sql .= "where ".$Query;
		$result = AbtelGestion::getRecord($sql);
		return $result['cnt'];
	}
	
	/*
	 * demande l'enregistrement id
	 */
	static function getOneData($id) {
			var_dump($id);
		$sql = "select 'AbtelGestion' as Module,'Tache' as ObjectType,
t.Id,t.NumeroTicket,t.CodeEntite,t.taclient as CodeClient,t.tatype as Type,t.tacateg as Categorie,t.tatitre as Titre
from taches t
where t.NumeroTicket='$id'";
		$sql1 = "select 'AbtelGestion' as Module,'Action' as ObjectType,
l.Id,l.NumeroTicket,l.accadre as Cadre,l.actitre as Titre,l.acdatecreation as Date,l.acheuredeb as HeureDebut,l.acheurefin as HeureFin,
l.TypeDuree
from actions l
where l.NumeroTicket='$id'
order by Id";
		$result = AbtelGestion::getRecord($sql, $sql1);
		return Array($result);
	}

	/*
	 * demande une liste d'enregistrements
	 */
	static function getData($Query, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy) {
		var_dump($Query);
		$sql = "select 'AbtelGestion' as Module,'Tache' as ObjectType,
t.Id,t.NumeroTicket,t.CodeEntite,t.taclient as CodeClient,t.tatype as Type,t.tacateg as Categorie,t.tatitre as Titre
from taches t\n";
		$where = '';
		$order = Empty($OrderVar) ? 't.NumeroTicket' : $OrderVar.' '.$OrderType;
		$offset = $Ofst;
		$limit = Empty($Limit) ? '100' : $Limit;
		$result = AbtelGestion::getRecords($sql, $where, $order, $offset, $limit);
		$result['pagination'] = Array('offset'=>$offset, 'limit'=>$limit, 'total'=>self::getCount($Query));
		return $result;
	}
	
	public function Save() {
		var_dump($this);
		die("save");
	}
	
	public function getChildren($Type) {
		die($Type);
	}

}
