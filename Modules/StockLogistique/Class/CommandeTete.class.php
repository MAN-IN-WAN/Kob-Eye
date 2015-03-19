<?php
class CommandeTete extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
/*
	function Save() {
		if($this->Reference == '') {
			$rec = Sys::$Modules['StockLogistique']->callData('CommandeTete', false, 0, 1, 'DESC', 'Reference', 'Reference');
			if(! $rec) $this->Reference = '000001'; 
			else $this->Reference = sprintf('%06d', $rec[0]['Reference'] + 1);
		}
		genericClass::Save();
	}
*/	
	
	function GetDestockage($id,$offset,$limit,$sort,$order,$filter,$mag='',$cp='',$vil='',$cli='',$liv=array(null,null)) {
		if($id) {
			$rec = Sys::$Modules['StockLogistique']->callData('CommandeTete/Id='.$id, false, 0, 1);
		}
		else {
			// filtre
			$req = 'CommandeTete/Destockage=0';
			if($liv[0]) $flt .= '&DateLivraison>='.$liv[0];
			if($liv[1]) $flt .= '&DateLivraison<='.$liv[1];
			if($mag) $flt .= "&LivraisonId~%$mag";
			if($cp) $flt .= "&CodPostal~$cp";
			if($vil) $flt .= "&Ville~%$vil";
			if($cli) $flt .= "&ClientId~%$cli";
	//		if($dsk !== '') $flt .= "&Destockage=$dsk";
			$req .= $flt;
			// entetes
			$items = array();
			if(! $sort) {
				$sort = 'DateLivraison';
				$order = 'ASC';
			}
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:$req:$offset:$limit:$order:$sort:");
			$rec = Sys::$Modules['StockLogistique']->callData($req, false, $offset, $limit, $order, $sort);
		}
		foreach($rec as $rc) {
			$cmde = genericClass::createInstance('StockLogistique', $rc);
			$lines = array();
			$clins = Sys::$Modules['StockLogistique']->callData("CommandeTete/$cmde->Id/CommandeLigne/BLTeteId=0", false, 0, 999, 'ASC', 'Id');
			foreach($clins as $clin)
				$lines[] = array(Famille=>$clin['Famille'],Designation=>$clin['Designation'],Quantite=>$clin['Quantite']);
			$items[] = array('Id'=>$cmde->Id,'Reference'=>$cmde->Reference,'Type'=>'L',
					'DateLivraison'=>$cmde->DateLivraison,'DateReprise'=>$cmde->DateReprise,
					'ClientId'=>$cmde->ClientId,'LivraisonId'=>$cmde->LivraisonId,'CodPostal'=>$cmde->CodPostal,'Ville'=>$cmde->Ville,
					'Destockage'=>$cmde->Destockage,'children'=>$lines);
		}
		$c = count($items);
		$rows = Sys::$Modules['StockLogistique']->callData($req,false,0,1,'','','COUNT(DISTINCT(m.Id))');
		$rows = $rows[0]['COUNT(DISTINCT(m.Id))'];
		return WebService::WSData('',$offset,$c,$rows,'StockLogistique/'.$req,'','','','',$items);
	}

	function SaveDestockage($ids) {
		$sts = array();
		foreach($ids as $id) {
			// commande
			$rec = Sys::$Modules['StockLogistique']->callData('CommandeTete/'.$id->Id);
			$cmde = genericClass::createInstance('StockLogistique', $rec[0]);
			if($cmde->Destockage) continue;
			$dev = $cmde->getParents('DevisTete');
			$dev = $dev[0];
			// livraison
			$livr = genericClass::createInstance('StockLogistique', 'BLTete');
			$livr->addParent($cmde);
			$livr->addParent($dev);
			$livr->Societe =		$cmde->Societe;
			$livr->ClientId = 		$cmde->ClientId;
			$livr->LivraisonId =	$cmde->LivraisonId;
			$livr->DateDebut = 		$cmde->DateDebut;
			$livr->DateFin = 		$cmde->DateFin;
			$livr->DateLivraison = 	$cmde->DateLivraison;
			$livr->DateReprise = 	$cmde->DateReprise;
			$livr->Save(true);
			$cls = $cmde->getChilds('CommandeLigne');
			foreach($cls as $cl) {
				$cl->addParent($livr);
				//$cl->Livre = 1;
				$cl->Save();
				$els = $cl->getChilds('Element');
				foreach($els as $el) {
					$el->addParent($livr);
					$el->Save();
				}
			}
			// reprise
			$repr = genericClass::createInstance('StockLogistique', 'RepriseTete');
			$repr->addParent($cmde);
			$repr->addParent($dev);
			$repr->Societe =		$cmde->Societe;
			$repr->ClientId = 		$cmde->ClientId;
			$repr->LivraisonId =	$cmde->LivraisonId;
			$repr->DateDebut = 		$cmde->DateDebut;
			$repr->DateFin = 		$cmde->DateFin;
			$repr->DateLivraison = 	$cmde->DateLivraison;
			$repr->DateReprise = 	$cmde->DateReprise;
			$repr->Save(true);
			foreach($cls as $cl) {
				$cl->addParent($repr);
				$cl->Save();
				$els = $cl->getChilds('Element');
				foreach($els as $el) {
					$el->addParent($repr);
					$el->Save();
				}
			}
			// commande
			$cmde->Destockage = 1;
			$cmde->Save();
		}
		$sts[] = array('method', 1, 0, 'StockLogistique', 'CommandeTete', '', '', null, null);
		$sts[] = array('method', 1, 0, 'StockLogistique', 'BLTete', '', '', null, null);
		$sts[] = array('method', 1, 0, 'StockLogistique', 'RepriseTete', '', '', null, null);
		return WebService::WSStatusMulti($sts);
	}

	function Disponibilite($fam,$deb,$fin,$jour) {
		if(!$fam) return WebService::WSStatus('method',1,0,'','','','',null,null);
		
		// quantite en stock
		$sql = "select count(*) as cnt from `loc-StockLocatif-Reference` r
				left join `loc-StockLocatif-ArticleFamilleId` f on f.Article=r.Articleid
				where f.FamilleId=$fam and r.HS<>1 and r.Vendu<>1";
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$qty = $rec[0]['cnt'];
		
		// tableaux de jours
		if(! $deb) $deb = strtotime("now");
		
		if($fin < $deb) $fin = strtotime('+2 month', $deb);
		if($jour) $d0 = $d1 = $jour;
		else {
			$d0 = $deb;
			$d1 = $fin;
		}
		$n = $this->dateDiff($d0, $d1) + 1;
		$blt = array();
		$res = array();
		for($i = 0; $i < $n; $i++)
			$res[] = array(0,0);
		// elements affectes
		$sql = "select e.DateLivraison,e.DateReprise,e.DateDepart,e.DateRetour,e.Quantite,e.BLTeteId
				from `loc-StockLogistique-Element` e
				left join `loc-StockLocatif-Reference` r on r.id=e.ReferenceId
				left join `loc-StockLocatif-ArticleFamilleId` f on f.Article=r.ArticleId
				where e.ReferenceId<>0
				and if(e.DateRetour,e.DateRetour,e.DateReprise)>=$d0
				and if(e.DateDepart>0,e.DateDepart,e.DateLivraison)<=$d1
				and f.FamilleId=$fam
				order by e.BLTeteId";	
//$GLOBALS["Systeme"]->Log->log("ttttttttttt:".str_replace("\n",'',$sql));
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$bl = '';
		foreach($rec as $rc) {
			if($bl != $rc['BLTeteId']) {
				$bl != $rc['BLTeteId'];
				$blt[] = $bl;
			}
			$dd = $rc['DateDepart'] ? $rc['DateDepart'] : $rc['DateLivraison'];
			if($dd < $d0) $dd = $d0;
			$df = $rc['DateRetour'] ? $rc['DateRetour'] : $rc['DateReprise'];
			if($df > $d1) $df = $d1;
			$d = $this->dateDiff($d0, $dd);
			$n = $this->dateDiff($dd, $df) + 1;
			$q = $rc['Quantite'];
			for($i = 0; $i < $n; $i++) $res[$d + $i][1] += $q;
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:$d $n $q ".date('d-m-y',$dd).' '.date('d-m-y',$df));
		}
		// elements non affectes
		$sql = "select e.DateLivraison,e.DateReprise,e.Quantite,e.BLTeteId
				from `loc-StockLogistique-CommandeLigne` l
				left join `loc-StockLogistique-Element` e on e.CommandeLigneId=l.Id
				where l.FamilleId=$fam and e.ReferenceId=0
				and e.DateReprise>=$d0
				and e.DateLivraison<=$d1
				order by e.BLTeteId";
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:".str_replace("\n",'',$sql));
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$bl = '';
		foreach($rec as $rc) {
			if($bl != $rc['BLTeteId']) {
				$bl != $rc['BLTeteId'];
				if(array_search($bl, $blt) !== false)
					$blt[] = $bl;
			}
			$dd = $rc['DateLivraison'];
			if($dd < $d0) $dd = $d0;
			$df = $rc['DateReprise'];
			if($df > $d1) $df = $d1;
			$d = $this->dateDiff($d0, $dd);
			$n = $this->dateDiff($dd, $df) + 1;
			$q = $rc['Quantite'];
			for($i = 0; $i < $n; $i++) $res[$d + $i][0] += $q;
//$GLOBALS["Systeme"]->Log->log("vvvvvvvvvvvvv:$d $n $q ".date('d-m-y',$dd).' '.date('d-m-y',$df));
		}
		$srt = 0;
		$rsv = 0;
		foreach($res as $ar) {
			if($srt < $ar[1]) $srt = $ar[1];
			if($rsv < $ar[0] + $ar[1]) $rsv = $ar[0] + $ar[1];
		}
		$rsv = $rsv - $srt;
		$dis = $qty - $srt;
		$items = $this->getReservations($fam, $d0, $d1, $jour);

		if(!$jour) {
			$val = array('debut'=>$deb,'fin'=>$fin,'quantite'=>$qty,'reservations'=>$res);
			$res = array('dataValues'=>array('begin'=>$deb,'finish'=>$fin,'Stock'=>$qty,
					'Disponible'=>$dis,'Reserve'=>$rsv,
					'DisponibleJ'=>null,'ReserveJ'=>null,'Jour'=>null,
					'Disponibilite'=>$val,'Reservations'=>$items));
		}
		else {
			$res = array('dataValues'=>array('DisponibleJ'=>$dis,'ReserveJ'=>$rsv,'Jour'=>$jour,'Reservations'=>$items));
		}
		return WebService::WSStatus('method',1,0,'','','','',null,$res);
	}
	
	private function getReservations($fam, $deb, $fin, $jour) {
		if(! $fam || ! $deb || ! $fin) return WebService::WSData('',0,0,0,'','','','','',array());
		if($sel) {
			$deb = $sel;
			$fin = $sel;
		}
		$sql = "select d.Reference,d.LivraisonIntitule,d.LivraisonVille,
				d.LivraisonCodPostal,d.ClientIntitule,sum(l.Quantite) as Quantite,l.Livre,
				if(e.DateRetour,e.DateRetour,e.DateReprise) as DateReprise,
				if(e.DateDepart>0,e.DateDepart,e.DateLivraison) as DateLivraison
				from `loc-StockLogistique-CommandeLigne` l
				left join `loc-StockLogistique-Element` e on e.CommandeLigneId=l.Id
				left join `loc-StockLogistique-CommandeTete` c on c.Id=l.CommandeId
				left join `loc-Devis-DevisLigne` dl on dl.Id=l.DevisLigneId
				left join `loc-Devis-DevisTete` d on d.Id=dl.DevisId
				where l.FamilleId=$fam ";
		if(! $jour)
			$sql .= "and if(e.DateRetour,e.DateRetour,e.DateReprise)>=$deb
					 and if(e.DateDepart>0,e.DateDepart,e.DateLivraison)<=$fin ";
		else
			$sql .= "and ((if(e.DateRetour,e.DateRetour,e.DateReprise)=$deb)
					 or (if(e.DateDepart>0,e.DateDepart,e.DateLivraison)=$fin)) ";
		$sql .= "group by d.Id
				 order by l.Livre,
				 if(e.DateRetour,e.DateRetour,e.DateReprise),
				 if(e.DateDepart>0,e.DateDepart,e.DateLivraison)";
//$GLOBALS["Systeme"]->Log->log("ggggggggggggg:".str_replace("\n",'',$sql));
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$items = array();
		foreach($rec as $rc) {
			$items[] = array('Reference'=>$rc['Reference'],'DateLivraison'=>$rc['DateLivraison'],
				'DateReprise'=>$rc['DateReprise'],'Livraison'=>$rc['LivraisonIntitule'],
				'Ville'=>$rc['LivraisonVille'],'CodPostal'=>$rc['LivraisonCodPostal'],
				'Client'=>$rc['ClientIntitule'],'Quantite'=>$rc['Quantite'],'Livre'=>$rc['Livre']);
		}
		return $items;
	}

	private function dateDiff($start, $end) {
		return round(($end - $start) / 86400);
	}

/*
	function GetReservation($fam, $deb, $fin, $sel) {
		if(! $fam || ! $deb || ! $fin) return WebService::WSData('',0,0,0,'','','','','',array());
		if($sel) {
			$deb = $sel;
			$fin = $sel;
		}
		$sql = "select d.Reference,l.DateLivraison,l.DateReprise,d.LivraisonIntitule,d.LivraisonVille,
				d.LivraisonCodPostal,d.ClientIntitule,sum(l.Quantite) as Quantite,l.Livre
				from `loc-StockLogistique-CommandeLigne` l
				left join `loc-StockLogistique-Element` e on e.CommandeLigneId=l.Id
				left join `loc-StockLogistique-CommandeTete` c on c.Id=l.CommandeId
				left join `loc-Devis-DevisLigne` dl on dl.Id=l.DevisLigneId
				left join `loc-Devis-DevisTete` d on d.Id=dl.DevisId
				where l.FamilleId=$fam
				and e.ReferenceId=0
				and e.DateReprise>=$deb
				and e.DateLivraison<=$fin
				group by d.Id
				order by l.Livre,l.DateLivraison,l.DateReprise";
//$GLOBALS["Systeme"]->Log->log("ggggggggggggg:".str_replace("\n",'',$sql));
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$items = array();
		foreach($rec as $rc) {
			$items[] = array('Reference'=>$rc['Reference'],'DateLivraison'=>$rc['DateLivraison'],
				'DateReprise'=>$rc['DateReprise'],'Livraison'=>$rc['LivraisonIntitule'],
				'Ville'=>$rc['LivraisonVille'],'CodPostal'=>$rc['LivraisonCodPostal'],
				'Client'=>$rc['ClientIntitule'],'Quantite'=>$rc['Quantite'],'Livre'=>$rc['Livre']);
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,'','','','','',$items);
	}
*/

	function GetStockFamille($fam, $ref, $periode, $etat) {
		if(! $fam && ! $ref) return WebService::WSStatus('method',1,0,'','','','',null,null);
		
		$sql = 'select li.Intitule as Magasin,li.Ville,li.CodPostal,cl.Intitule as Client,
			    b.Reference as Numero,e.DateLivraison,e.DateReprise,r.Reference,e.DateDepart,e.DateRetour,e.Etat
				from `loc-StockLogistique-CommandeLigne` l
				left join `loc-StockLogistique-Element` e on e.CommandeLigneId=l.Id
				left join `loc-StockLogistique-CommandeTete` c on c.Id=l.CommandeId
				left join `loc-Repertoire-Tiers` li on li.Id=c.LivraisonId
				left join `loc-Repertoire-Tiers` cl on cl.Id=c.ClientId
				left join `loc-StockLogistique-BLTete` b on b.Id=e.BLTeteId
				left join `loc-StockLocatif-Reference` r on r.Id=e.ReferenceId
				where ';
		$sql .= $ref ? "r.Reference like '$ref%'" : "l.FamilleId='$fam'";
		$per = $periode[0];
		if($per) $sql .= ' and if(e.Etat=0,e.DateRetour,e.DateReprise)<='.$per;
		$per = $periode[1];
		if($per) $sql .= ' and if(e.Etat=2,e.DateLivraison,e.DateDepart)>='.$per;
		if($etat) $sql .= ' and e.Etat='.($etat - 1);
/*		
union all
select '*** PANNE ***' as Magasin,'' as Ville,'' as CodPostal,'' as Client,'*** PANNE ***' as Numero,
0 as DateLivraison,0 as DateReprise,r.Reference,p.Date as DateDepart,p.DateFin as DateRetour,3 as Etat
from `loc-StockLocatif-Panne` p
left join `loc-StockLocatif-Reference` r on r.Id=p.ReferenceId
left join `loc-StockLocatif-ArticleFamilleId` f on f.Article=r.ArticleId
where f.FamilleId=42		
 */
//		$sql .= ' order by li.Intitule,cl.Intitule';	
		return $this->stockSql($sql);
	}


	function GetStockTiers($id,$client,$periode,$etat) {
		if(! $id) return WebService::WSStatus('method',1,0,'','','','',null,null);
		
		$sql = 'select li.Intitule as Magasin,li.Ville,li.CodPostal,cl.Intitule as Client,f.Famille,
			    b.Reference as Numero,e.DateLivraison,e.DateReprise,r.Reference,e.DateDepart,e.DateRetour,e.Etat
				from `loc-StockLogistique-CommandeTete` c
				left join `loc-StockLogistique-CommandeLigne` l on l.CommandeId=c.Id
				left join `loc-StockLogistique-Element` e on e.CommandeLigneId=l.Id
				left join `loc-StockLocatif-Reference` r on r.Id=e.ReferenceId
				left join `loc-Repertoire-Tiers` li on li.Id=c.LivraisonId
				left join `loc-Repertoire-Tiers` cl on cl.Id=c.ClientId
				left join `loc-StockLogistique-BLTete` b on b.Id=e.BLTeteId
				left join `loc-StockLocatif-Famille` f on f.Id=l.FamilleId
				where ';
		$sql .= ($client ? 'c.ClientId=' : 'c.LivraisonId=') . $id;
		$per = $periode[0];
		if($per) $sql .= ' and if(e.Etat=0,e.DateRetour,e.DateReprise)<='.$per;
		$per = $periode[1];
		if($per) $sql .= ' and if(e.Etat=2,e.DateLivraison,e.DateDepart)>='.$per;
		if($etat) $sql .= ' and e.Etat='.($etat - 1);
//		$sql .= " order by li.Intitule,cl.Intitule,f.Famille";
		return $this->stockSql($sql);
	}

	private function stockSql($sql) {
//$GLOBALS["Systeme"]->Log->log("-----------:".str_replace("\n",'',$sql));
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return WebService::WSStatus('method',0,0,'','','','',array(array("CommandeTete.stockSql:\n$sql")),null);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);

		$items = array();
		foreach($rec as $rc) {
			switch($rc['Etat']) {
				case 0:
					$eta = 'Réservé';
					$deb = $rc['DateLivraison'];
					$fin = $rc['DateReprise'];
					$col = 'white';
					break;
				case 1:
					$eta = 'Livré';
					$deb = $rc['DateDepart'];
					$fin = $rc['DateReprise'];
					$col = 'yellow';
					break;
				case 2:
					$eta = 'Repris';
					$deb = $rc['DateDepart'];
					$fin = $rc['DateRetour'];
					$col = 'cyan';
					break;
			}
			$items[] = array('Magasin'=>$rc['Magasin'],'Ville'=>$rc['Ville'],
				'CodPostal'=>$rc['CodPostal'],'Client'=>$rc['Client'],'Famille'=>$rc['Famille'],
				'Reference'=>$rc['Reference'],'Etat'=>$eta,'DateDebut'=>$deb,'DateFin'=>$fin,
				'Numero'=>$rc['Numero'],'_backgroundColor'=>$col);
		}		
		$res = array(dataValues=>array('list'=>$items));
		return WebService::WSStatus('method',1,0,'','','','',null,$res);
	}	
}
?>