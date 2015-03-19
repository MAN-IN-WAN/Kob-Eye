<?php
class Tiers extends genericClass{

	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	
	
//	function Save() {
//		if($this->)
//	}

	function SelectMerge($ids) {
		$c = count($ids);
		if($c == 0) $msg = "Aucun tiers sélectionné";
		if($c > 3) $msg = "Trop de tiers sélectionnés";
		$rec = Sys::$Modules['Repertoire'] -> callData('Tiers/'.$ids[0]);
		$t0 = $rec[0];
		$r = substr($t0['CodeTiers'],0,1);
		if($r != '[' && $r != ']') $n = 1;
		if($c > 1) {
			$rec = Sys::$Modules['Repertoire'] -> callData('Tiers/'.$ids[1]);
			$t1 = $rec[0];
			$r = substr($t1['CodeTiers'],0,1);
			if($r != '[' && $r != ']') $n = 2;
		}
		if($c > 2) {
			$rec = Sys::$Modules['Repertoire'] -> callData('Tiers/'.$ids[2]);
			$t2 = $rec[0];
			$r = substr($t2['CodeTiers'],0,1);
			if($r != '[' && $r != ']') $n = 3;
		}
		if(! isset($n)) {
			if($c > 2) $msg = "Pas de tiers de base";
			else if($c == 1) $t1 = $t0;
			else $t2 = $t0;
		}
		if($msg) return WebService::WSStatus('method',0,'','','','','',array(array('message'=>$msg)), null);

		
		switch($n) {
			case 1:
				$m = $t0;
				break;
			case 2:
				$m = $t1;
				$t1 = $t0;
				break;
			case 3:
				$m = $t2;
				$t2 = $t0;
		}
		$data = array('Id'=>$m['Id'],'Intitule'=>$m['Intitule'],'CodeTiers'=>$m['CodeTiers'],'Adresse1'=>$m['Adresse1'],'Adresse2'=>$m['Adresse2'],
					'CodPostal'=>$m['CodPostal'],'Ville'=>$m['Ville'],'Fax'=>$m['Fax'],'Telepone'=>$m['Telephone'],'Pays'=>$m['Pays']);
		if($t1) $data = array_merge($data, array('Id1'=>$t1['Id'],'Intitule1'=>$t1['Intitule'],'CodeTiers1'=>$t1['CodeTiers'],'Adresse11'=>$t1['Adresse1'],'Adresse21'=>$t1['Adresse2'],
					'CodPostal1'=>$t1['CodPostal'],'Ville1'=>$t1['Ville'],'Fax1'=>$t1['Fax'],'Telepone1'=>$t1['Telephone'],'Pays1'=>$t1['Pays']));
		if($t2) $data = array_merge($data, array('Id2'=>$t2['Id'],'Intitule2'=>$t2['Intitule'],'CodeTiers2'=>$t2['CodeTiers'],'Adresse12'=>$t2['Adresse1'],'Adresse22'=>$t2['Adresse2'],
					'CodPostal2'=>$t2['CodPostal'],'Ville2'=>$t2['Ville'],'Fax2'=>$t2['Fax'],'Telepone2'=>$t2['Telephone'],'Pays2'=>$t2['Pays']));
		$res = array(dataValues => $data);
		
		return WebService::WSStatus('method',1,'','','','','',null,$res);
	}


	function UpdateMerge($val) {
		$id = $val->Id;
		$t = genericClass::createInstance('Repertoire', 'Tiers');
		if($id) $t->initFromId($id);
		$t->CodeTiers = $val->CodeTiers;
		$t->Intitule = $val->Intitule;
		$t->Adresse1 = $val->Adresse1;
		$t->Adresse2 = $val->Adresse2;
		$t->CodPostal = $val->CodPostal;
		$t->Ville = $val->Ville;
		$t->Pays = $val->Pays;
		$t->Telepone = $val->Telepone;
		$t->Fax = $val->Fax;
		$t->Save();
		$this->updateDocuments($t, $val->Id1, $val->CodeTiers1);
		$this->updateDocuments($t, $val->Id2, $val->CodeTiers2);
		return WebService::WSStatus('edit',1,'','Repertoire','Tiers','','',null,null);
	}

	private function updateDocuments($t, $oid, $ref) {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx", $sql);
		$nid = $t->Id;
		$r = substr($ref, 0, 1);
		if($r == '[') {
			$fld = 'ClientId';
			$set = "ClientId=$nid,ClientIntitule='%s',ClientAdresse1='%s',ClientAdresse2='%s',ClientCodPostal='%s',ClientVille='%s',ClientPays='%s'";
		}
		elseif($r == ']') {
			$fld = 'LivraisonId';
			$set = "LivraisonId=$nid,LivraisonIntitule='%s',LivraisonAdresse1='%s',LivraisonAdresse2='%s',LivraisonCodPostal='%s',LivraisonVille='%s',LivraisonPays='%s'";
		}
		else return;
		$set = sprintf($set,
				mysql_real_escape_string($t->Intitule),
				mysql_real_escape_string($t->Adresse1),
				mysql_real_escape_string($t->Adresse2),
				mysql_real_escape_string($t->CodPostal),
				mysql_real_escape_string($t->Ville),
				mysql_real_escape_string($t->Pays));

		$GLOBALS["Systeme"]->ConnectSql();
		$sql = "update `##_Devis-DevisTete` set $set where $fld=$oid";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx", $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$sql = "update `##_StockLogistique-CommandeTete` set $fld=$nid where $fld=$oid";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$sql = "update `##_StockLogistique-BLTete` set $fld=$nid where $fld=$oid";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$sql = "update `##_Repertoire-Contact` set TiersId=$nid where TiersId=$oid";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$sql = "delete from `##_Repertoire-Tiers` where Id=$oid";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
	}
}
