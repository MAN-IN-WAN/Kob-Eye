<?php
class BLTete extends genericClass {

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
	 	$code = 'LIVR';
		$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
		Sys::$Modules["Devis"]->Db->clearLiteCache(); 
		$cons = genericClass::createInstance('Devis', $rec[0]);
		$cons->Valeur = $cons->Valeur + 1;
		$cons->Save();
		return sprintf('%06d', $cons->Valeur);
	}

/*
	function GetLivrList($id, $offset, $limit, $sort, $order, $filter, $mode,$type='',$liv=array(null,null),$mag='',$cp='',$vil='',$cli='',$dsk='') {
		if($mode != 4) {
			if($type == 'L') {
				if($liv[0]) $flt .= '&DateLivraison>='.$liv[0];
				if($liv[1]) $flt .= '&DateLivraison<='.$liv[1];
				$ord = 'DateLivraison';
			}
			else {
				if($liv[0]) $flt = '&DateReprise>='.$liv[0];
				if($liv[1]) $flt .= '&DateReprise<='.$liv[1];
				$ord = 'DateReprise';
			}
			if($mag) $flt .= "&LivraisonId~%$mag";
			if($cp) $flt .= "&CodPostal~$cp";
			if($vil) $flt .= "&Ville~%$vil";
			if($cli) $flt .= "&ClientId~%$cli";
			if($mode == '0' && $dsk !== '') $flt .= "&Destockage=$dsk";
			// 0:destockage, 1:preparation, 2:tournee liv 3:tournee rep
			switch($mode) {
				case 0: $req = "$flt"; break;
				case 1: $req = "Livre=0$flt"; break;
				case 2: $req = "Livre=0&TourneeLiv=0$flt"; break;
				case 3: $req = "Repris=0&TourneeLiv=1&TourneeRep=0$flt"; break;
			}
		}
		else {
			$id = empty($type) ? '0' : $type;
			if($id === '0')
				return WebService::WSData('',0,0,0,'','','','','',array());
			$req = "TourneeLivId=$id+TourneeRepId=$id";
			$ord = 'Ordre';
		}
		if($sort) $ord = $sort;
		else $order = 'ASC';

		$req = 'BLTete/'.($id ? "Id=$id&" : '').$req;
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:$req");
		$rec = Sys::$Modules['StockLogistique']->callData($req, false, $offset, $limit, $order, $ord);

		$items = array();
		foreach($rec as $rc) {
			$bl = genericClass::createInstance('StockLogistique', $rc);
			$lines = array();
			$cls = $bl->getChilds('CommandeLigne');
			foreach($cls as $cl)
				$lines[] = array('LigneId'=>$cl->Id,'Famille'=>$cl->Famille,'Designation'=>$cl->Designation,'Quantite'=>$cl->Quantite);
			
			if($mode == 4) $type = $bl->TourneeLivId == $id ? 'L' : 'R';
			$dlr = $type == 'L' ? $bl->DateLivraison : $bl->DateReprise;
			$items[] = array('Id'=>$bl->Id,'Reference'=>$bl->Reference,'Type'=>$type,'DateLR'=>$dlr,
					'DateLivraison'=>$bl->DateLivraison,'DateReprise'=>$bl->DateReprise,
					'ClientId'=>$bl->ClientId,'LivraisonId'=>$bl->LivraisonId,'CodPostal'=>$bl->CodPostal,'Ville'=>$bl->Ville,
					'Destockage'=>$bl->Destockage,'Prepare'=>$bl->Prepare,'Tournee'=>$bl->Tournee,
					'Livre'=>$bl->Livre,'Repris'=>$bl->Repris,'children'=>$lines);
		}
		$c = count($items);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:$c");
		return WebService::WSData('',0,$c,$c,$req,'','','','',$items);
	}



	function GetLivraison($mode,$type='',$deb=0,$fin=0,$mag='',$cp='',$vil='',$cli='',$dsk='') {
		if($mode != 4) {
			$req = 'BLTete/';
			if($type == 'L') {
				if($deb) $flt .= "&DateLivraison>=$deb";
				if($fin) $flt .= "&DateLivraison<=$fin";
				$ord = 'DateLivraison';
			}
			else {
				if($deb) $flt = "&DateReprise>=$deb";
				if($fin) $flt .= "&DateReprise<=$fin";
				$ord = 'DateReprise';
			}
			if($mag) $flt .= "&LivraisonId~%$mag";
			if($cp) $flt .= "&CodPostal~$cp";
			if($vil) $flt .= "&Ville~%$vil";
			if($cli) $flt .= "&ClientId~%$cli";
			if($mode == '0' && $dsk !== '') $flt .= "&Destockage=$dsk";
			// 0:destockage, 1:preparation, 2:tournee liv 3:tournee rep
			switch($mode) {
				case 0: $req .= "$flt"; break;
				case 1: $req .= "Livre=0$flt"; break;
				case 2: $req .= "Livre=0&TourneeLiv=0$flt"; break;
				case 3: $req .= "Repris=0&TourneeLiv=1&TourneeRep=0$flt"; break;
			}
		}
		else {
			$id = empty($type) ? '0' : $type;
			if($id === '0')
				return WebService::WSData('',0,0,0,'','','','','',array());
			$req = "BLTete/TourneeLivId=$id+TourneeRepId=$id";
			$ord = 'Ordre';
		}
		$items = array();
		$rec = Sys::$Modules['StockLogistique']->callData($req, false, 0, 9999, 'ASC', $ord);
		foreach($rec as $rc) {
			$lid = $rc['Id'];
			$lines = array();
			$elms = Sys::$Modules['StockLogistique']->callData("BLTete/$lid/CommandeLigne", false, 0, 9999, 'ASC', 'Id');
			foreach($elms as $el) {
				$lines[] = array(Famille=>$el['Famille'],Designation=>$el['Designation'],Quantite=>$el['Quantite']);
			}
			if($mode == 4) $type = $rc['TourneeLivId'] == $id ? 'L' : 'R';
			$dlr = $type == 'L' ? $rc['DateLivraison'] : $rc['DateReprise'];
			$items[] = array(Id=>$lid,Reference=>$rc['Reference'],Type=>$type,DateLR=>$dlr,
					DateLivraison=>$rc['DateLivraison'],DateReprise=>$rc['DateReprise'],
					ClientId=>$rc['ClientId'],LivraisonId=>$rc['LivraisonId'],CodPostal=>$rc['CodPostal'],Ville=>$rc['Ville'],
					Destockage=>$rc['Destockage'],Prepare=>$rc['Prepare'],Tournee=>$rc['Tournee'],
					Livre=>$rc['Livre'],Repris=>$rc['Repris'],children=>$lines);
		}
		$c = count($items);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:$c");
		return WebService::WSData('',0,$c,$c,$req,'','','','',$items);
	}
*/

	function GetBLTete($id, $offset, $limit, $sort, $order, $filter) {
		$now = floor(strtotime("now") / 86400) * 86400;
		$req = 'BLTete';
		if($id) $req .= "/Id=$id";
		if($filter) $req .= "/$filter";
		$livr = Sys::$Modules['StockLogistique']->callData($req,false,$offset,$limit,$sort,$order);
		foreach($livr as &$lv) {
			$lv['DateLivraison_Color'] = $lv['DateLivraison']<=$now && $lv['Etat']<12 ? '0xff0000' : '0x000000';
			$ch = array();
			$lin = Sys::$Modules['StockLogistique']->callData('BLTete/'.$lv['Id'].'/CommandeLigne');
			foreach($lin as $lc) 
				$ch[] = array(LivraisonIntitule=>sprintf('%3d   ', $lc['Quantite']).$lc['Famille']);
			$lv['children'] = $ch;
		}
		$c = count($livr);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$livr);
	}


	function GetDestockage($id, $offset, $limit, $sort, $order, $filter) {
		$req = 'CommandeLigne';
		if($id) $req .= "/$id";
		$req .= '/Confirme=1&Preparation=0';
		if($filter) $req .= "&$filter";
		$ls = Sys::$Modules['StockLogistique']->callData($req,false,$offset,$limit,$sort,$order);
		$t = time();
		foreach($ls as &$l) {
			$l['DateLivraison_Color'] = $l['DateLivraison']<=$t ? '0xff0000' : '0x000000';
			$l['DateReprise_Color'] = '0x808080';
		}
		$c = count($livr);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$ls);
	}


	function SaveDestockage($ids,$fiche,$etiq,$laser) {
		$livrs = array();
		$sts = array();
		foreach($ids as $id) {
			$cmdl = genericClass::createInstance('StockLogistique', 'CommandeLigne');
			$cmdl->initFromId($id);
			// devis
			$dev = $cmdl->getParents('DevisTete','DevisId');
			$dev = $dev[0];
			if($dev->Preparation) continue;
			// livraison
			$livr = genericClass::createInstance('StockLogistique', 'BLTete');
			$livr->addParent($dev);
			$livr->Reference = '';
			$livr->Societe =		$dev->Societe;
			$livr->ClientId = 		$dev->ClientId;
			$livr->LivraisonId =	$dev->LivraisonId;
			$livr->DateDebut = 		$dev->DateDebut;
			$livr->DateFin = 		$dev->DateFin;
			$livr->DateLivraison = 	$dev->DateLivraison;
			$livr->Date =			$dev->DateLivraison;
			$livr->Etat = 			10; // preparation
			$livr->Save(true);
			$livrs[] = $livr->Id;
			//
			$rol = 'LOC_LOGISTIQUE';
			$usr = null;
			AlertUser::addAlert('BL en préparation : '.$dev->ClientIntitule.' / '.$dev->LivraisonIntitule,'FR0','StockLogistique','Tournee','',$usr,$rol,null);
			
			$cls = $dev->getChildren('CommandeLigne:NOVIEW');
			foreach($cls as $cl) {
				$cl->addParent($livr);
				$cl->Save();
				$elms = $cl->getChildren('Element');
				foreach($elms as $elm) {
					$elm->addParent($livr);
					$elm->Save();
				}
			}
			$dev->Preparation = 1;
			$dev->Save(true);
		}
		$sts[] = array('method', 1, 0, 'StockLogistique', 'BLTete', '', '', null, null);
		$sts[] = array('method', 1, 0, 'StockLogistique', 'CommandeLigne', '', '', null, null);
		if($fiche || $etiq) {
			$res = $this->PrintPreparations($livrs,$fiche,$etiq,$laser,true);
			$sts[] = array('method', 1, 0, '', '', '', '', null, $res);
		}
		return WebService::WSStatusMulti($sts);
	}

	function FindReference($ref) {
	 	$rec = Sys::$Modules['StockLocatif']->callData('Reference/Reference='.$ref);
		if(is_array($rec) && count($rec)) {
			$id = $rec[0]['Id'];
			$art = $rec[0]["ArticleId"];
			$rec = Sys::$Modules['StockLogistique']->callData("Element/ReferenceId=$id&DateRetour=0");
			if(is_array($rec) && count($rec)) return WebService::WSStatus('method',0,'','','','','',array(array("Référence en cours d'utilisation : $ref")),null);
			$rec = Sys::$Modules['StockLocatif']->callData('Famille/Article/'.$art);
			$fam = array();
			foreach($rec as $rc)
				$fam[] = $rc['Famille'];
			$res = array(Id=>$id,targets=>$fam);
			return WebService::WSStatus('method',1,'','','','','',array(),$res);
		}
		return WebService::WSStatus('method',0,'','','','','',array(array("Référence non trouvée : $ref")),null);
	}
	
	
	function ReadPrepa($id, $type) {
		if($type) {
			$rec = Sys::$Modules['StockLogistique']->callData("Tournee/Id=$id");
			if(! is_array($rec) || ! count($rec)) {
				$msg = 'TOURNEE NON TROUVEE';
				return WebService::WSStatus('method',0,'','','','','',array(array('message'=>$msg)),null);
			}
			$trn = genericClass::createInstance('StockLogistique', $rec[0]);
			$veh = $trn->VehiculeId ? $trn->VehiculeId : "";
			$cha = $trn->ChauffeurId ? $trn->ChauffeurId : "";
			$rec = Sys::$Modules['StockLogistique']->callData("BLTete/TourneeId=".$trn->Id);
			$livr = array();
			$status = 1;
			foreach($rec as $rc) {
				$liv = genericClass::createInstance('StockLogistique', $rc);
				$item = $this->readPrepaItem($liv);
				if(! $item['status']) $status = 0;
				$livr[] = $item;
			}
			$prepa = array();
			$prepa['livraisons'] = '';
			$prepa['tournee'] = array('Id'=>$trn->Id,'Reference'=>$trn->Reference,'Date'=>date('d/m/Y',$trn->Date),
									'Vehicule'=>$veh,'Chauffeur'=>$cha,'status'=>$status);
			return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'read','data'=>$prepa));
		}
		$rec = Sys::$Modules['StockLogistique']->callData('BLTete/'.$id, false, 0, 1);
		if(! is_array($rec) || ! count($rec)) {
			$msg = 'BL NON TROUVEE';
			return WebService::WSStatus('method',0,'','','','','',array(array('message'=>$msg)),null);
		}
		$liv = genericClass::createInstance('StockLogistique', $rec[0]);
		$prepa = array('BLTeteId'=>$id);
		if(! $liv->TourneeId)
			$prepa['livraisons'] = array($this->readPrepaItem($liv));
		else {
			$rec = Sys::$Modules['StockLogistique']->callData("Tournee/Id=".$liv->TourneeId);
			$trn = genericClass::createInstance('StockLogistique', $rec[0]);
			$veh = $trn->VehiculeId ? $trn->VehiculeId : "";
			$cha = $trn->ChauffeurId ? $trn->ChauffeurId : "";
			$rec = Sys::$Modules['StockLogistique']->callData("BLTete/TourneeId=".$trn->Id);
			$livr = array();
			$status = 1;
			foreach($rec as $rc) {
				$liv = genericClass::createInstance('StockLogistique', $rc);
				$item = $this->readPrepaItem($liv);
				if(! $item['status']) $status = 0;
				$livr[] = $item;
			}
			$prepa['livraisons'] = $livr;
			$prepa['tournee'] = array('Id'=>$trn->Id,'Reference'=>$trn->Reference,'Date'=>date('d/m/Y',$trn->Date),
									'Vehicule'=>$veh,'Chauffeur'=>$cha,'status'=>$status);
		}
		return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'read','data'=>$prepa));
	}

	private function readPrepaItem($l) {
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$l->ClientId);
		$cli = genericClass::createInstance('Repertoire', $rec[0]);
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$l->LivraisonId);
		$mag = genericClass::createInstance('Repertoire', $rec[0]);
		$status = 1;
		$lines = array();
		$lin = $l->getChilds('CommandeLigne');
		foreach($lin as $li) {
			$fam = genericClass::createInstance('StockLocatif', 'Famille');
			$fam->initFromId($li->FamilleId);
			$elements = array();
			$elm = $li->getChilds('Element');
			$qte = 0;
			foreach($elm as $el) {
				$element = array('Id'=>$el->Id,'ReferenceId'=>$el->ReferenceId,'Reference'=>'');
				if($el->ReferenceId) {
					$rec = Sys::$Modules['StockLocatif']->callData("Reference/".$el->ReferenceId);
					$element['Reference'] = $rec[0]['Reference'];
					$qte++;
				}
				$elements[] = $element;
			}
			$lines[] = array('Id'=>$li->Id,'FamilleId'=>$fam->Id,'Famille'=>$fam->Famille,'Designation'=>$li->Designation,'Quantite'=>$li->Quantite,'elements'=>$elements,'affecte'=>$qte);
			if($qte < $li->Quantite) $status = 0;
		}
		return array('Id'=>$l->Id,'Reference'=>$l->Reference,'Date'=>date('d/m/Y',$l->DateLivraison),'Client'=>$cli->Intitule,'Magasin'=>$mag->Intitule,'lines'=>$lines,'status'=>$status);
	}

	function SavePrepa($ref, $lid, $lin) {
	 	$rec = Sys::$Modules['StockLocatif']->callData('Reference/Reference='.$ref);
		if(! is_array($rec) || ! count($rec)) $msg = 'ARTICLE INCONNUE';
		else {
			$refe = genericClass::createInstance('StockLocatif', $rec[0]);
			if($refe->Sortie) $msg = 'ARTICLE DEJA SORTIE';
			elseif($refe->Panne) $msg = 'ARTICLE EN PANNE';
			elseif($refe->HS) $msg = 'ARTICLE HORS SERVICE';
			elseif($refe->Vendu) $msg = 'ARTICLE VENDU';
			else {
				$id = $refe->Id;
				$rec = Sys::$Modules['StockLogistique']->callData("Element/ReferenceId=$id&Etat<4");
				if(is_array($rec) && count($rec)) {
					if($rec[0]['Etat'] == 3) $msg = 'ARTICLE NON REMIS EN STOCK';
					else $msg = "ARTICLE DEJA SORTIE";
				}
			}
		}
		if(! $msg) {
			$rec = Sys::$Modules['StockLocatif']->callData('Famille/Article/'.$refe->ArticleId);
			if(! is_array($rec) || ! count($rec)) $msg = "PAS DE FAMILLE\nPOUR CET ARTICLE";
		}
		if($msg) return WebService::WSStatus('method',0,'','','','','',array(array('message'=>$ref."\n\n".$msg)),null);
		
		if($lin) {
			$rec = Sys::$Modules['StockLogistique']->callData("CommandeLigne/$lin",false,0,1);
			$lin = genericClass::createInstance('StockLogistique', $rec[0]);
			$elm = $lin->getChilds('Element');
			foreach($elm as $el) {
				if(! $el->ReferenceId) {
					$el->ReferenceId = $refe->Id;
					$el->Save();
					$refe = array('ElementId'=>$el->Id,'Reference'=>$ref,'CommandeLigneId'=>$lin->Id);
					return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'save','data'=>$refe));
				}
			}
			return WebService::WSStatus('method',0,'','','','','',array(array('message'=>'ERREUR FAMILLE')),null);
		}

		$fams = array();
		foreach($rec as $rc) $fams[] = $rc['Id'];
		$rec = Sys::$Modules['StockLogistique']->callData("BLTete/$lid/CommandeLigne");
		foreach($rec as $rc) {
			$lin = genericClass::createInstance('StockLogistique', $rc);
			if(in_array($lin->FamilleId, $fams)) {
				$okFam= true;
				$elm = $lin->getChilds('Element');
				foreach($elm as $el) {
					if(! $el->ReferenceId) {
						$el->ReferenceId = $refe->Id;
						$el->Save();
						$refe = array('ElementId'=>$el->Id,'Reference'=>$ref,'CommandeLigneId'=>$lin->Id);
						return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'save','data'=>$refe));
					}
				}
			}
		}
		$msg = $okFam ? "FAMILLE\nENTIEREMENT AFFECTEE" : 'FAMILLE NON TROUVEE';
		return WebService::WSStatus('method',0,'','','','','',array(array('message'=>$msg)),null);
	}

	function RemovePrepa($eid, $lid) {
		$elm = genericClass::createInstance('StockLogistique', 'Element');
		$elm->initFromId($eid);
		$elm->ReferenceId = 0;
		$elm->Save();
		$data = array('ElementId'=>$eid,'CommandeLigneId'=>$lid);
		return WebService::WSStatus('method',1,'','','','','',null,array('mode'=>'remove','data'=>$data));
	}
	
	
	function GetPreparation() {
		$p = $this;
		// client
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$p->ClientId);
		$cli = genericClass::createInstance('Repertoire', $rec[0]);
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$p->LivraisonId);
		$liv = genericClass::createInstance('Repertoire', $rec[0]);
		// elements
//		$lines = array();
//		$rec = Sys::$Modules['StockLogistique']->callData("BLTete/$this->Id/Element", false, 0, 9999, 'ASC', 'Id', 'Id');
//		foreach($rec as $el) {
//			$lines[] = array(Famille=>$el['Famille'],Designation=>$el['Designation'],Quantite=>$el['Quantite'],
//					Id=>$el['Id'],ReferenceId=>$el['ReferenceId'],Reference=>$el['Reference']);
//		}
		$items = array();
		$items[] = array(Reference=>$p->Reference,Prepare=>$p->Prepare,
				DateLivraison=>$p->DateLivraison,DateReprise=>$p->DateReprise,
				Livraison=>$liv->Intitule,CPLivr=>$liv->CodPostal,VilleLivr=>$liv->Ville,
				Adr1Livr=>$liv->Adresse1,Adr2Livr=>$liv->Adresse2,Adr3Livr=>$liv->Adresse3,TelLivr=>$liv->Telephone,
				Client=>$cli->Intitule,CPClient=>$cli->CodPostal,VilleClient=>$cli->Ville,
				Adr1Client=>$cli->Adresse1,Adr2Client=>$cli->Adresse2,Adr3Client=>$cli->Adresse3,TelClient=>$cli->Telephone);
//				Elements=>$lines);
		return WebService::WSData('',0,1,1,'StockLocatif/BLTete/'.$p->Id,'','','','',$items);
	}

	function SavePreparation($lines) {
		$prep = true;
		foreach($lines as $l) {
			$rec = Sys::$Modules['StockLogistique']->callData('Element/'.$l->Id, false, 0, 1);
			$elm = genericClass::createInstance('StockLogistique', $rec[0]);
			if($l->_updated) {
				if($l->ReferenceId) {
//					$rec = Sys::$Modules['StockLogistique']->callData('Reference/'.$l->ReferenceId, false, 0, 1);
//					$ref = genericClass::createInstance('StockLogistique', $rec[0]);
//					$elm->addParent($ref);
					$elm->ReferenceId = $l->ReferenceId;
				}
				else {
//					$ref = genericClass::createInstance('StockLogistique', 'Reference');
//					$elm->delParent($ref);
					$elm->ReferenceId = 0;
				}
				$elm->Save();
			}
			if(! $elm->ReferenceId) $prep = false;
		}
		$this->Prepare = $prep ? "1" : "0";
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx".$prep, $this);
		$this->Save();
		$res = array(dataValues=>array('Prepare'=>$this->Prepare));
		return WebService::WSStatus('edit',1,$this->Id,'StockLogistique','BLTete','','',null,$res);
	}
	
	
	function PrintPreparations($ids,$fiche,$etiq,$laser,$local=false) {
		$files = array();
		if($fiche) {
			require_once('Class/Lib/fpdf_merge.php');
			$pdf = array();
			if(! isset($ids)) $ids = array($this->Id);
			foreach($ids as $id) {
				$rec = Sys::$Modules['StockLogistique']->callData("BLTete/$id",false,0,1);
				if(! sizeof($rec)) continue;
				$doc = genericClass::createInstance('StockLogistique',$rec[0]);
				$pdf[] = $doc->PrintPreparation($id);
			}
			if(sizeof($pdf) > 0) {
				$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
				$merge = new FPDF_Merge();
				foreach($pdf as $doc)
					$merge->add($doc);
				$merge->output($file);
				$files[] = $file;
			}
		}
		if($etiq) {
			$files[] = $this->PrintLabels($ids,$laser);
		}
		$res = array(printFiles=>$files);
		if($local) return $res;
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	private function PrintPreparation($id) {
		require_once('PreparationEtat.class.php');
		$lines = Sys::$Modules['StockLogistique']->callData("BLTete/$id/CommandeLigne", false, 0, 9999, 'ASC', 'Id');
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $lines);
		
		$pdf = new PreparationEtat($this,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Prepa_'.$this->Reference);
		
		$pdf->AddPage();
		$pdf->PrintLines($lines, false);
		// save pdf
		$file = 'Home/tmp/Prepa_'.$this->Reference.'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}

	function PrintLabels($ids,$laser) {
		require_once('Class/Lib/pdfb1/pdfb.php');
		//if($mode == 1 && ! count($ids)) return WebService::WSStatus('method', 1, '', '', '', '', '', null, null);

		$pdf = new PDFB('P','mm', $laser ? array(210,297) : array(100,150));
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Etiquettes');
		$pdf->SetFillColor(200,200,200);
		
		$i = 99;
		foreach($ids as $id) {
			$bl = genericClass::createInstance('StockLogistique', 'BLTete');
			$bl->initFromId($id);
			$dev = $bl->getParents('DevisTete');
			$dev = $dev[0];
			$elm = Sys::$Modules['StockLogistique']->callData("BLTete/$id/Element",false,0,9999);
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$bl->ClientId);
			$cli = genericClass::createInstance('Repertoire', $rec[0]);
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$bl->LivraisonId);
			$liv = genericClass::createInstance('Repertoire', $rec[0]);
	
			foreach($elm as $el) {
				//if($mode == 1 && array_search($el['Id'], $ids) === false) continue;
				if($laser) {
					if($i >= 4) {
						$pdf->SetAutoPagebreak(false);
						$pdf->AddPage();
						$i = 0;
					}
					$x = ($i % 2) * 105 + 6;
					$y = floor($i / 2) * (297 / 2) + 5;
				}
				else {
					$pdf->SetAutoPagebreak(false);
					$pdf->AddPage();
					$x = 2;
					$y = 2;
				}
				$pdf->BarCode(':'.sprintf('%06d',$bl->Id), "C128B", $x+50, $y, 200, 30, 0.46, 0.6, 2, 5, "", "PNG");
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',13);
				$pdf->Cell(95,5,"LOC'ANIM");
				$y += 6;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(95,4.5,"Livraison N° ".$bl->Reference);
				$y += 4.5;
				$pdf->SetXY($x, $y);
				$pdf->Cell(95,4.5,"Commande N° ".$dev->Reference);
				$y += 4.5;
				$pdf->SetXY($x, $y);
				$pdf->Cell(95,4.5,"Livraison le ".date('d.m.Y',$el['DateLivraison']));
				//$y += 4.5;
				$pdf->SetXY($x+50, $y);
				$pdf->Cell(95,4.5,"Reprise le ".date('d.m.Y',$el['DateReprise']));
				$y += 6;
				// delivery address
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(95,4.5,'Lieu de Livraison',1,0,'C',true);
				$y += 4.5;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(95,4.5,$liv->Intitule,'LR');
				$y += 4.5;
				$pdf->SetXY($x,$y);
				$pdf->SetFont('Arial','',10);
				$pdf->MultiCell(95,4.5,$liv->Adresse1."\n".$liv->Adresse2."\n".$liv->Adresse3."\n",'LR');
				$y += 3*4.5;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(95,4.5,$liv->CodPostal.'  '.$liv->Ville.' '.$liv->Cedex,'LR');
				$y += 4.5;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(95,4.5,'Tél. :'.$liv->Telephone,'LRB');
				$y += 5.5;
				// client address
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(95,4.5,'Client',1,0,'C',true);
				$y += 4.5;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(95,4.5,$cli->Intitule,'LR');
				$y += 4.5;
				$pdf->SetXY($x,$y);
				$pdf->SetFont('Arial','',10);
				$pdf->MultiCell(95,4.5,$cli->Adresse1."\n".$cli->Adresse2."\n".$cli->Adresse3."\n",'LR');
				$y += 3*4.5;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(95,4.5,$cli->CodPostal.'  '.$cli->Ville.' '.$cli->Cedex,'LR');
				$y += 4.5;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(95,4.5,'Tél. :'.$cli->Telephone,'LRB');
				$y += 5;
				//
				$pdf->SetXY($x, $y+2);
				$pdf->SetFont('Arial','B',13);
				$pdf->Cell(95,5,$el['Famille']);
				$pdf->BarCode('-'.$el['Famille'], "C128B", $x+50, $y, 200, 30, 0.46, 0.6, 2, 5, "", "PNG");
				$y += 15;
				$pdf->SetXY($x, $y);
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(95,5,$el['Designation']);
	
				if($laser) $i++;
			}
		}
		$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file; //WebService::WSStatus('method', 1, '', '', '', '', '', array(), array(printFiles=>array($file)));
	}


	function PrintDocuments($ids, $fond=true) {
		require_once('Class/Lib/fpdf_merge.php');
		$pdf = array();
		if(! isset($ids)) $ids = array($this->Id);
		foreach($ids as $id) {
			$rec = Sys::$Modules['StockLogistique']->callData("BLTete/$id",false,0,1,'','','');
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
		$lines = Sys::$Modules['StockLogistique']->callData("BLTete/$id/Element",false,0,999);
		
		$pdf = new LivraisonEtat($this,! $this->TourneeRepId,$fond,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Livraison_'.$this->Reference);
		
		$pdf->AddPage();
		$pdf->PrintLines($lines, false);
		$pdf->PrintBottom();
		// save pdf
		$file = 'Home/Devis/Livraison_'.$this->Reference.'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}

	/*******************************************
	 * DISPONIBILITE
	 *******************************************/
	function Disponibilite($fam,$deb,$fin,$jour) {
		if(!$fam) return WebService::WSStatus('method',1,0,'','','','',null,null);
		
		// quantite en stock
		$sql = "select count(*) as cnt from `##_StockLocatif-Reference` r
				left join `##_StockLocatif-ArticleFamilleId` f on f.Article=r.Articleid
				where f.FamilleId=$fam and r.HS=0 and r.Vendu=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$qty = $rec[0]['cnt'];
		
		// tableaux de jours
		$now = floor(strtotime("now") / 86400) * 86400;
		if(! $deb) $deb = strtotime("now");
		
		if($fin < $deb) $fin = strtotime('+1 month', $deb);
		if($jour) $d0 = $d1 = $jour;
		else {
			$d0 = $deb;
			$d1 = $fin;
		}
		$n = $this->dateDiff($d0, $d1) + 1;
		$blt = array();
		$res = array();
		for($i = 0; $i < $n; $i++)
			$res[] = array(0,0,0);
		// elements affectes
		$sql = "select if(e.Etat>1,e.DateDepart,e.DateLivraison) as Depart,
				if(e.Etat>2,e.DateRetour,if(e.DateReprise<$now,$now,e.DateReprise)) as Retour,
				e.DateLivraison,e.DateReprise,e.DateDepart,e.DateRetour,
				sum(e.Quantite) as Quantite,e.ReferenceId,d.Confirme
				from `##_StockLogistique-CommandeLigne` c
				left join `##_StockLogistique-Element` e on e.CommandeLigneId=c.Id
				left join `##_Devis-DevisTete` d on d.Id=c.DevisId
				where c.FamilleId=$fam and d.EtatDevis<4 ";
		//if($jour)
		//	$sql .= "and if(e.Etat>2,e.DateRetour,e.DateReprise)=$jour
		//			and if(e.Etat>1,e.DateDepart,e.DateLivraison)=$jour "; 
		//else
			$sql .= "and if(e.Etat>2,e.DateRetour,if(e.DateReprise<$now,$now,e.DateReprise))>=$d0
					and if(e.Etat>1,e.DateDepart,e.DateLivraison)<=$d1 ";
		$sql .= "group by d.Id,Depart,Retour order by d.Id";	
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$srt = 0;
		$cmd = 0;
		$rsv = 0;
		foreach($rec as $rc) {
			$dd = $rc['Depart'];  //$rc['Etat']>1 ? $rc['DateDepart'] : $rc['DateLivraison'];
			if($dd < $d0) $dd = $d0;
			$df = $rc['Retour'];  //$rc['Etat']>2 ? $rc['DateRetour'] : $rc['DateReprise'];
			if($df > $d1) $df = $d1;
			$d = $this->dateDiff($d0, $dd);
			$n = $this->dateDiff($dd, $df) + 1;
			$q = $rc['Quantite'];
			if($rc['ReferenceId'] != 0) {
				$j = 2;
				//$srt += $q;
			}
			else if($rc['Confirme'] == 1) {
				$j = 1;
				//$cmd += $q;
			}
			else {
				$j = 0;
				$rsv += $q;
			}
			for($i = 0; $i < $n; $i++) $res[$d + $i][$j] += $q;
		}

		foreach($res as $ar) {
			$out = $ar[1] + $ar[2];
			if($srt < $out) $srt = $out;
		}

/*
		foreach($res as $ar) {
			if($srt < $ar[2]) $srt = $ar[2];
			if($cmd < $ar[1]) $cmd = $ar[1];
			if($rsv > $ar[0]) $rsv = $ar[0];
		}
*/
		$dis = $qty - $cmd - $srt;
		$items = $this->getReservations($fam, $d0, $d1, $jour, $now);

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
	
	private function getReservations($fam, $deb, $fin, $jour, $now) {
		if(! $fam || ! $deb || ! $fin) return WebService::WSData('',0,0,0,'','','','','',array());
		if($jour) {
			$deb = $jour;
			$fin = $jour;
		}
		$sql = "select d.Reference,d.LivraisonIntitule,d.LivraisonVille,
				d.LivraisonCodPostal,d.ClientIntitule,sum(e.Quantite) as Quantite,l.Livre,d.Confirme,
				if(e.Etat>2,e.DateRetour,e.DateReprise) as Retour,
				if(e.Etat>1,e.DateDepart,e.DateLivraison) as Depart
				from `##_StockLogistique-Element` e
				left join `##_StockLogistique-CommandeLigne` l on l.Id=e.CommandeLigneId
				left join `##_Devis-DevisTete` d on d.Id=l.DevisId
				where d.EtatDevis<4 and ";
		//if(! $jour)
			$sql .= "if(e.Etat>2,e.DateRetour,if(e.DateReprise<$now,$now,e.DateReprise))>=$deb
					 and if(e.Etat>1,e.DateDepart,e.DateLivraison)<=$fin ";
		//else
		//	$sql .= "((if(e.Etat>2,e.DateRetour,e.DateReprise)=$deb)
		//			 or (if(e.Etat>1,e.DateDepart,e.DateLivraison)=$fin)) ";
		$sql .= "and l.FamilleId=$fam 
				group by d.Id,Depart,Retour
				order by l.Livre,Depart,Retour";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$items = array();
		foreach($rec as $rc) {
			$livre = $rc['Livre'];
			$item = array('Reference'=>$rc['Reference'],'Depart'=>$rc['Depart'],
					'Retour'=>$rc['Retour'],'Livraison'=>$rc['LivraisonIntitule'],
					'Ville'=>$rc['LivraisonVille'],'CodPostal'=>$rc['LivraisonCodPostal'],
					'Client'=>$rc['ClientIntitule'],'Quantite'=>$rc['Quantite'],'Livre'=>$livre,'Confirme'=>$rc['Confirme']);
			if($jour) {
				if($deb == $rc['Depart']) $item['Depart_Color'] = 'blue';
				if($fin == $rc['Retour']) $item['Retour_Color'] = 'blue';
			}
			if($livre && $now > $rc['Retour']) $item['Retour_Color'] = 'red';
			$items[] = $item;
		}
		return $items;
	}

	private function dateDiff($start, $end) {
		return round(($end - $start) / 86400);
	}



	function GetStockTiers($id,$type,$periode,$livre=0) { //,$etat) {
		if(! $id) return WebService::WSStatus('method',1,0,'','','','',null,array());
		$now = floor(strtotime("now") / 86400) * 86400;
		$sql = "select e.Id,d.ClientIntitule as Client,d.LivraisonIntitule as Livraison,
				d.LivraisonCodPostal as CodPostal,d.LivraisonVille as Ville,
				f.Famille,r.Reference,e.Quantite,l.Echange,e.Etat,
				if(e.Etat>1,e.DateDepart,e.DateLivraison) as DateDebut,
				if(e.Etat>2,e.DateRetour,e.DateReprise) as DateFin,
				e.DateStock,t.Etat as Status,t.Couleur as Status_Color,
				if(e.Etat<=2 and e.DateReprise<=$now,'0xff0000','0x000000') as DateFin_Color,
				if(e.Etat<2 and e.DateLivraison<=$now,'0xff0000','0x000000') as DateDebut_Color,
				d.Reference as Devis
				from `##_StockLogistique-Element` e
				left join `##_StockLogistique-Status` t on t.Code=e.Etat
				left join `##_StockLogistique-CommandeLigne` l on l.Id=e.CommandeLigneId
				left join `##_Devis-DevisTete` d on d.Id=e.DevisId
				left join `##_StockLocatif-Reference` r on r.id=e.ReferenceId
				left join `##_StockLocatif-Famille` f on f.id=l.FamilleId
				where ";
		$sql .= ($type ? 'l.ClientId=' : 'l.LivraisonId=').$id;
		if($livre) $sql .= ' and e.Etat'.($livre==1 ? '=' : '<>').'2';
		$per = $periode[0];
		if($per) $sql .= " and if(e.Etat>2,e.DateRetour,if(e.DateReprise<$now,$now,e.DateReprise))>=".$per;
		$per = $periode[1];
		if($per) $sql .= ' and if(e.Etat>1,e.DateDepart,e.DateLivraison)<='.$per;
		//if($etat) $sql .= ' and e.Etat='.($etat - 1);
		$sql .= ' order by if(e.Etat>1,e.DateDepart,e.DateLivraison) desc,f.Famille,r.Reference';
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return WebService::WSStatus('method',0,0,'','','','',array(array('Message'=>"BLTete.stockSql:\n$sql")),null);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$res = array(dataValues=>array('items'=>$rec));
		return WebService::WSStatus('method',1,0,'','','','',null,$res);
	}

	function GetStockFamille($fam,$ref,$periode,$livre=0) { //, $etat) {
		if(! $fam && ! $ref) return WebService::WSStatus('method',1,0,'','','','',null,array());
		$now = floor(strtotime("now") / 86400) * 86400;
		$sql = "select e.Id,d.ClientIntitule as Client,d.LivraisonIntitule as Livraison,
				d.LivraisonCodPostal as CodPostal,d.LivraisonVille as Ville,
				d.Societe,l.Echange,e.Etat,
				f.Famille,r.Reference,e.Quantite,
				if(e.Etat>1,e.DateDepart,e.DateLivraison) as DateDebut,
				if(e.Etat>2,e.DateRetour,e.DateReprise) as DateFin,
				e.DateStock,t.Etat as Status,t.Couleur as Status_Color,
				if(e.Etat<=2 and e.DateReprise<=$now,'0xff0000','0x000000') as DateFin_Color,
				if(e.Etat<2 and e.DateLivraison<=$now,'0xff0000','0x000000') as DateDebut_Color,
				d.Reference as Devis
				from `##_StockLogistique-Element` e
				left join `##_StockLogistique-Status` t on t.Code=e.Etat
				left join `##_StockLogistique-CommandeLigne` l on l.Id=e.CommandeLigneId
				left join `##_Devis-DevisTete` d on d.Id=e.DevisId
				left join `##_StockLocatif-Reference` r on r.id=e.ReferenceId
				left join `##_StockLocatif-Famille` f on f.id=l.FamilleId
				where ";
		$sql .= $ref ? "r.Reference like '$ref%'" : "l.FamilleId='$fam'";
		if($livre) $sql .= ' and e.Etat'.($livre==1 ? '=' : '<>').'2';
		$per = $periode[0];
		if($per) $sql .= " and if(e.Etat>2,e.DateRetour,if(e.DateReprise<$now,$now,e.DateReprise))>=".$per;
		$per = $periode[1];
		if($per) $sql .= ' and if(e.Etat>1,e.DateDepart,e.DateLivraison)<='.$per;
		//if($etat) $sql .= ' and e.Etat='.($etat - 1);
		$sql .= ' order by if(e.Etat>1,e.DateDepart,e.DateLivraison) desc,f.Famille,r.Reference';
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return WebService::WSStatus('method',0,0,'','','','',array(array('Message'=>"BLTete.stockSql:\n$sql")),null);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$res = array(dataValues=>array('items'=>$rec));
		return WebService::WSStatus('method',1,0,'','','','',null,$res);
	}
	
	function EchangePanne($id,$date) {
		$elm = Sys::getData('StockLogistique',"Element/Id=$id");
		$elm = $elm[0];
		$dev = $elm->getParents('DevisTete','DevisId');
		$dev = $dev[0];
		$lin = $elm->getParents('CommandeLigne');
		$lin = $lin[0];
		$cli = genericClass::createInstance('Repertoire','Tiers');
		$cli->initFromId($dev->ClientId);
		$mag = genericClass::createInstance('Repertoire','Tiers');
		$mag->initFromId($dev->LivraisonId);
		// reprise
		$rep = genericClass::createInstance('StockLogistique', 'RepriseTete');
		$rep->addParent($dev);
		$rep->Societe =		$dev->Societe;
		$rep->ClientId = 	$dev->ClientId;
		$rep->LivraisonId =	$dev->LivraisonId;
		$rep->DateDebut = 	$dev->DateDebut;
		$rep->DateFin = 	$dev->DateFin;
		$rep->DateReprise = $date;
		$rep->Date =		$date;
		$rep->Etat =		20;
		$rep->Save(true);
		// element a reprendre
		$elm->addParent($rep);
		$elm->Save();
		// livraison
		$liv = genericClass::createInstance('StockLogistique', 'BLTete');
		$liv->addParent($dev);
		$liv->Reference = 		'';
		$liv->Societe =			$dev->Societe;
		$liv->ClientId = 		$dev->ClientId;
		$liv->LivraisonId =		$dev->LivraisonId;
		$liv->DateDebut = 		$date;
		$liv->DateFin = 		$dev->DateFin;
		$liv->DateLivraison = 	$date;
		$liv->Date =			$date;
		$liv->Etat = 			10;
		$liv->Save(true);
		// ligne commande
		$col = genericClass::createInstance('StockLogistique','CommandeLigne');
		$col->Designation =		'ECHANGE : '.$lin->Designation;
		$col->Quantite = 		1;
		$col->DateDebut = 		$date;
		$col->DateFin =			$dev->DateFin;
		$col->DateLivraison = 	$date;
		$col->DateReprise = 	$dev->DateReprise;
		$col->Echange = 		1;
		$col->FamilleId = 		$lin->FamilleId;
		$col->addParent($cli,'ClientId');
		$col->addParent($mag,'LivraisonId');
		$col->addParent($dev);
		$col->addParent($liv);
		$col->Save();
		// element a livre
		$nel = genericClass::createInstance('StockLogistique', 'Element');
		$nel->addParent($col);
		$nel->addParent($dev);
		$nel->addParent($liv);
		$nel->Quantite = 		1;
		$nel->DateLivraison = 	$date;
		$nel->DateReprise = 	$elm->DateReprise;
		$nel->DateDepart = 		null;
		$nel->DateRetour = 		null;
		$nel->Etat = 			1;
		$nel->Save(true);
		$sts = array();
		$sts[] = array('add', 1, $rep->Id, 'StockLogistique', 'RepriseTete', '', '', null, null);
		$sts[] = array('add', 1, $liv->Id, 'StockLogistique', 'BLTete', '', '', null, null);
		$sts[] = array('edit', 1, $elm->Id, 'StockLogistique', 'Element', '', '', null, null);
		$sts[] = array('add', 1, $nel->Id, 'StockLogistique', 'Element', '', '', null, null);
		return WebService::WSStatusMulti($sts);
	}

	function RemiseEnStock($id,$date,$controle) {
		$sts = array();
		$elm = Sys::getData('StockLogistique',"Element/Id=$id");
		$elm = $elm[0];
		if($elm->Etat == 2) {
			$ref = genericClass::createInstance('StockLocatif', 'Reference');
			$ref->initFromId($elm->ReferenceId);
			$ref->Sorti = $controle ? 0 : 1;
			$ref->Save();
			$elm->Etat = $controle ? 3 : 4; // controle ou repris
			$elm->DateRetour = $date;
			$elm->Save();
			$sts[] = array('edit', 1, $ref->Id, 'StockLocatif', 'Reference', '', '', null, null);
			$sts[] = array('edit', 1, $elm->Id, 'StockLogistique', 'Element', '', '', null, null);
		}
		else $sts[] = array('method', 1, '', '', '', '', '', null, null);
		return WebService::WSStatusMulti($sts);
	}

	function createAlerts($time) {
		$d = strtotime(date('Ymd',$time));
		$b = strtotime('+1 day',$d);
		$rec = Sys::getData('StockLogistique',"BLTete/DateLivraison<$b&Etat=10");
		foreach($rec as $rc)
			AlertUser::addAlert('Livraison en retard : '.$rc->Client.' / '.$rc->Magasin,'','StockLogistique','BLTete',$rc->Id,null,'LOC_LOGISTIQUE',null);
	}
}
