<?php
class RepriseTete extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function Save() {
		if($this->Reference == '') $this->Reference = $this->getNumber();
		genericClass::Save();
	}
	
	/*
	 * numerotation
	 */
	private function getNumber() {
	 	$code = 'REPR'; 
	 	$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
		Sys::$Modules["Devis"]->Db->clearLiteCache(); 
		$cons = genericClass::createInstance('Devis', $rec[0]);
		$cons->Valeur++;
		$cons->Save();
		return sprintf('%06d', $cons->Valeur);
	}

	// elements a reprendre
	function GetRecuperation($id, $offset, $limit, $sort, $order, $filter) {
		$req = 'Element';
		if($id) $req .= "/Id=$id";
		$req .= '/Etat=2&RepriseTeteId=0';
		if($filter) $req .= "&$filter";
		$ls = Sys::$Modules['StockLogistique']->callData($req,false,$offset,$limit,$sort,$order);
		$t = time();
		foreach($ls as &$l) {
			$l['DateDepart_Color'] = '0x808080';
			$l['DateReprise_Color'] = $l['DateReprise']<=$t ? '0xff0000' : '0x000000';
		}
		$c = count($livr);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$ls);
	}


	// liste des bons de reprise
	function GetRepriseTete($id, $offset, $limit, $sort, $order, $filter) {
		$t = time();
		$req = 'RepriseTete';
		if($id) $req .= "/$id";
		if($filter) $req .= "/$filter";
		$livr = Sys::$Modules['StockLogistique']->callData($req,false,$offset,$limit,$sort,$order);
		foreach($livr as &$lv) {
			$lv['DateReprise_Color'] = $lv['DateReprise']<=$t && $lv['Repris']==0 ? '0xff0000' : '0x000000';
			$ch = array();
			$lin = Sys::$Modules['StockLogistique']->callData('RepriseTete/'.$lv['Id'].'/Element');
			foreach($lin as $lc) {
				$ch[] = array(LivraisonIntitule=>sprintf('%3d   ', $lc['Quantite']).$lc['Famille'],LivraisonVille=>$lc['Reference']);
			}
			$lv['children'] = $ch;
		}
		$c = count($livr);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$livr);
	}


	function PrintDocuments($ids, $fond=true) {
		require_once('Class/Lib/fpdf_merge.php');
		$pdf = array();
		if(! isset($ids)) $ids = array($this->Id);
		foreach($ids as $id) {
			$rec = Sys::$Modules['StockLogistique']->callData("RepriseTete/$id",false,0,1,'','','');
			if(! sizeof($rec)) continue;
			$doc = genericClass::createInstance('StockLogistique',$rec[0]);
			$pdf[] = $doc->PrintDocument($fond);
		}
		if(sizeof($pdf) > 0) {
			$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
			$merge = new FPDF_Merge();
			foreach($pdf as $doc)
				$merge->add($doc);
			$merge->output($file);
			$res = array(printFiles=>array($file));
		}
		else $res = null;
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
	function PrintDocument($fond=true) {
		require_once('LivraisonEtat.class.php');
		$id = $this->Id;
		$lines = Sys::$Modules['StockLogistique']->callData("RepriseTete/$id/Element",false,0,999);
		
		$pdf = new LivraisonEtat($this,false,$fond,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Reprise_'.$this->Reference);
		
		$pdf->AddPage();
		$pdf->PrintLines($lines, false);
		$pdf->PrintBottom();
		// save pdf
		$file = 'Home/Devis/Reprise_'.$this->Reference.'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}
	
	// creation des bons de reprises
	function SaveRecuperation($lines) {
		$rup = '';
		foreach($lines as $line) {
			if($line->Devis != $rup) {
				$rup = $line->Devis;
				$dev = genericClass::createInstance('Devis', 'DevisTete');
				$dev->initFromId($line->DevisId);
				$repr = genericClass::createInstance('StockLogistique', 'RepriseTete');
				$repr->addParent($dev);
				$repr->Societe =		$dev->Societe;
				$repr->ClientId = 		$dev->ClientId;
				$repr->LivraisonId =	$dev->LivraisonId;
				$repr->DateDebut = 		$dev->DateDebut;
				$repr->DateFin = 		$dev->DateFin;
				$repr->DateReprise = 	$line->DateReprise;
				$repr->Date =			$line->DateReprise;
				$repr->Etat = 			20; // prevu
				$repr->Save(true);
			}
			$lin = genericClass::createInstance('StockLogistique', 'Element');
			$lin->initFromId($line->Id);
			$lin->addParent($repr);
			$lin->Save();
		}
		$sts = array();
		$sts[] = array('method', 1, $repr->Id, 'StockLogistique', 'RepriseTete', '', '', null, null);
		$sts[] = array('method', 1, '', 'StockLogistique', 'Element', '', '', null, null);
		return WebService::WSStatusMulti($sts);
	}

	// retour de tournee : BR
	function ReadRetour($id) {
		$rec = Sys::$Modules['StockLogistique']->callData('Tournee/Id='.$id, false, 0, 1);
		if(! is_array($rec) || ! count($rec)) {
			$msg = 'TOURNEE NON TROUVEE';
			return WebService::WSStatus('method',0,'','','','','',array(array('message'=>$msg)),null);
		}
		$trn = genericClass::createInstance('StockLogistique', $rec[0]);
		$veh = $trn->VehiculeId ? $trn->VehiculeId : "";
		$cha = $trn->ChauffeurId ? $trn->ChauffeurId : "";
		$reprs = $trn->getChildren('Reprise/NonRepris=0');
		$retour['tournee'] = array('Id'=>$trn->Id,'Reference'=>$trn->Reference,'Date'=>date('d/m/Y',$trn->Date),
									'Vehicule'=>$veh,'Chauffeur'=>$cha,'status'=>$trn->Effectuee,'lines'=>$reprs);
		return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'read','data'=>$retour));
	}

	// retour de tournee : Reference
	function SaveRetour($ref, $tid) {
		if($ref == '$RP') {
			$rep = genericClass::createInstance('StockLogistique', 'RepriseTete');
			$rep->initFromId($tid);
			//$rep->Repris = 1;
			$rep->Etat = 22; // repris
			$rep->Save();
			$res = array('Id'=>'','Reference'=>'REPRISE '.$rep->Reference,'Article'=>'');
			return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'save','data'=>$res));
		}
		if($ref == '$LV') {
			$rep = genericClass::createInstance('StockLogistique', 'BLTete');
			$rep->initFromId($tid);
			//$rep->Livre = 1;
			$rep->Etat = 12; // livré
			$rep->Save();
			$res = array('Id'=>'','Reference'=>'LIVRAISON '.$rep->Reference,'Article'=>'');
			return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'save','data'=>$res));
		}
		$trn = genericClass::createInstance('StockLogistique', 'Tournee');
		$trn->initFromId($tid);
		$rep = $trn->getChildren('Reprise');
		foreach($rep as $r) {
			if($r->Reference == $ref) {
				return WebService::WSStatus('method',1,'','','','','',null,null);
			}
		}
		$fam = '';
		$rec = Sys::$Modules['StockLocatif']->callData('Reference/Reference='.$ref);
		if(is_array($rec) && count($rec)) {
			$refe = genericClass::createInstance('StockLocatif', $rec[0]);
			$art = $refe->Article;
			$rec = Sys::$Modules['StockLogistique']->callData('Element/ReferenceId='.$refe->Id.'&Etat=2');
			if(is_array($rec) && count($rec)) {
				$elem = genericClass::createInstance('StockLogistique', $rec[0]);
				$elem->DateRetour = $trn->Date;
				$elem->Etat = 3; // controle
				$elem->Save();
			}
		}
		$r = genericClass::createInstance('StockLogistique', 'Reprise');
		$r->addParent($trn);
		$r->Reference = $ref;
		$r->Quantite = 1;
		$r->Date = $trn->Date;
		if($refe) $r->ReferenceId = $refe->Id;
		if($elem) $r->ElementId = $elem->Id;
		$r->Save();
		$res = array('Id'=>$r->Id,'Reference'=>$ref,'Article'=>$art);
		return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'save','data'=>$res));
		
	}

	// retour de tournee
	function RemoveRetour($rid) {
		$elm = genericClass::createInstance('StockLogistique', 'Reprise');
		$elm->initFromId($rid);
		$elm->Delete();
		$data = array('Id'=>$rid);
		return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'remove','data'=>$data));
	}
	
	function createAlerts($time) {
		$d = strtotime(date('Ymd',$time));
		$rec = Sys::getData('StockLogistique',"RepriseTete/DateReprise<$d&Etat=20");
		foreach($rec as $rc)
			AlertUser::addAlert('Reprise en retard : '.$rc->Client.' / '.$rc->Magasin,'','StockLogistique','RepriseTete',$rc->Id,null,'LOC_LOGISTIQUE',null);
	}

}
