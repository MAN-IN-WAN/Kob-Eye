<?php
class Tournee extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function Save() {
		if($this->Reference == '') $this->Reference = $this->getNumber();
		if(! $this->Etat) $this->Etat = 30;
		genericClass::Save();

		$lines = $this->livraison;
		foreach($lines as $l) {
			$rec = Sys::$Modules['StockLogistique']->callData('BLTete/'.$l->Id, false, 0, 1);
			$liv = genericClass::createInstance('StockLogistique', $rec[0]);
			$liv->Ordre = 0;
			$liv->Tournee = '0';
			$liv->TourneeId = '0';
			$liv->Save();
		}
		$lines = $this->reprise;
		foreach($lines as $l) {
			$rec = Sys::$Modules['StockLogistique']->callData('RepriseTete/'.$l->Id, false, 0, 1);
			$liv = genericClass::createInstance('StockLogistique', $rec[0]);
			$liv->Ordre = 0;
			$liv->Tournee = '0';
			$liv->TourneeId = '0';
			$liv->Save();			
		}
		$ord = 0;
		$lines = $this->tournee;
		foreach($lines as $l) {
			$req = $l->Type == 'L' ? 'BLTete/' : 'RepriseTete/';
			$rec = Sys::$Modules['StockLogistique']->callData($req.$l->Id, false, 0, 1);
			$liv = genericClass::createInstance('StockLogistique', $rec[0]);
			$liv->TourneeId = $this->Id;
			$liv->Tournee = '1';
			$liv->Ordre = ++$ord;
			$liv->Save();
		}
		
		$sts = array();
		$sts[] = array('edit',1,'','StockLogistique','BLTete','','',null,null);
		$sts[] = array('edit',1,'','StockLogistique','RepriseTete','','',null,null);
		$res = array('dataValues'=>array('Reference'=>$this->Reference));
		$sts[] = array($id ? 'edit' : 'add', 1, $this->Id, 'StockLogistique', 'Tournee', '', '', null, $res);
		return $sts;
	}

	function Delete() {
		if($this->Etat > 30)
			return array(array('method',0,'','','','','',array(array('message'=>"Tournée en cours.\nSuppression impossible")),null));
		$sts = array();
		$liv = Sys::getData('StockLogistique', 'BLTete:NOVIEW/TourneeId='.$this->Id);
		foreach($liv as $lv) {
			$lv->Tournee = 0;
			$lv->TourneeId = 0;
			$lv->Save();
			$sts[] = array('edit',1,$lv->Id,'StockLogistique','BLTete','','',null,null);
		}
		$liv = Sys::getData('StockLogistique', 'RepriseTete:NOVIEW/TourneeId='.$this->Id);
		foreach($liv as $lv) {
			$lv->Tournee = 0;
			$lv->TourneeId = 0;
			$lv->Save();
			$sts[] = array('edit',1,$lv->Id,'StockLogistique','RepriseTete','','',null,null);
		}
		parent::Delete();
		$sts[] = array('delete',1,$this->Id,'StockLogistique','Tournee','','',null,null);
		return $sts;
	}

	
	/*
	 * numerotation
	 */
	private function getNumber() {
	 	$rec = Sys::$Modules['Devis']->callData('Constante/Code=TOURNEE');
		Sys::$Modules["Devis"]->Db->clearLiteCache(); 
		$cons = genericClass::createInstance('Devis', $rec[0]);
		$cons->Valeur = sprintf('%06d', $cons->Valeur + 1);
		$cons->Save();
		return $this->Societe.$cons->Valeur;
	}


	function SaveRetour($arg) {
		foreach ($arg as $k=>$v)
	 		$this->$k = $v;
	 	genericClass::Save();

		$lines = $this->tournee;
		foreach($lines as $l) {
			$rec = Sys::$Modules['StockLogistique']->callData('BLTete/'.$l->Id, false, 0, 1);
			$liv = genericClass::createInstance('StockLogistique', $rec[0]);
			$liv->Livre = $l->Livre;
			$liv->Repris = $l->Repris;
			$liv->Save();
		}

		$reps = $this->getChilds('Reprise');
		$lines = $this->elements;
		foreach($lines as $l) {
			$ok = false;
			foreach($reps as $rep) {
				if($rep->Reference == $l->Reference) {
					$rep->ok = true;
					$ok = true;
					break;
				}
			}
			if(! $ok) {
				$rep = genericClass::createInstance('StockLogistique', 'Reprise');
				$rep->addParent($this);
				$rep->Reference = $l->Reference;
				$rep->ReferenceId = $l->ReferenceId;
				$rep->ok = true;
				$reps[] = $rep;
			}
			if(! $rep->ReferenceId) {
				$rec = Sys::$Modules['StockLocatif']->callData('Reference/Reference='.$rep->Reference,false,0,1);
				if(count($rec)) {
					$rep->ReferenceId = $rec[0]['Id'];
					$ok = false;
				}
			}
			if(! $ok) $rep->Save();
		}
//$GLOBALS["Systeme"]->Log->log("vvvvvvvvvvvvvv".print_r($reps,true));
		foreach($reps as $rep)
			if(! $rep->ok) $rep->Delete();
		$reps = $this->controleRetour();
		$res = array(dataValues=>array(element=>$reps));
		return WebService::WSStatus('edit',1,$this->Id,'StockLogistique','Tournee','','',array(),$res);
	}


/*
	private function controleRetour() {
		$reps = $this->getChilds('Reprise');
		foreach($reps as $rep) {
			if($rep->Controle) continue;
			if(! $rep->ReferenceId) {
				$rep->Commentaire = 'Référence inconnue';
			}
			else {
				$s = '';
				$ref = genericClass::createInstance('StockLogistique','Reference');
				$ref->initFromId($rep->ReferenceId);
				if($ref->Vendu) $s  .= 'Référence vendue';
				if($ref->HS) $s  .= 'Référence hors service';
				$rep->Controle = 1;
				$rec = Sys::$Modules['StockLogistique']->callData("Element/ReferenceId=".$rep->ReferenceId."&DateRetour=0");
				if(is_array($cnt) && count($cnt)) {
					foreach($rec as $rc) {
						//$elm = genericClass::createInstance('StockLogistique',$rc);
						//$elm->DateRetour = $this->Date;
						//$elm->Etat = 3;
						//$elm->Save();
						$lv = Sys::$Modules['StockLogistique']->callData("BLTete/$elm->BLTeteId",false,0,1);
						$liv = $lv[0];
						if($liv['TourneeRepId'] == $this->Id)
							$s .= 'Reprise '.$liv['Reference'].' ';
						else if($liv['TourneeLivId'] == $this->Id)
							$s .= 'Livraison '.$liv['Reference'].' non effectuée ';
						else {
							$s .= 'Reprise '.$liv['Reference'].' hors tournée ';
						}
					}
					$rep->Commentaire = $s;
				}
				else {
					$rep->Commentaire = 'Pas de sortie pour cette référence';
				}
			}
			$rep->Save();
		}
		return $reps;
//		return WebService::WSStatus('edit',1,$this->Id,'StockLogistique','Tournee','','',array(),$res);
	}
*/

	function PrintDocuments($id,$tournee,$bl,$fond,$direct) {
		require_once('Class/Lib/fpdf_merge.php');
		$pdf = array();
		if(! isset($id)) $id = $this->Id;
		$rec = Sys::$Modules['StockLogistique']->callData("Tournee/$id",false,0,1,'','','');
		if(! sizeof($rec)) continue;
		if($tournee) {
			$doc = genericClass::createInstance('StockLogistique',$rec[0]);
			$pdf[] = $doc->PrintDocument($fond);
		}
		if($bl) {
			$lines = Sys::$Modules['StockLogistique']->callData("BLTete/TourneeId=$id",false,0,999);
			foreach($lines as $lv) {
				$rec = Sys::$Modules['StockLogistique']->callData("BLTete/".$lv['Id'],false,0,1,'','','');
				$liv = genericClass::createInstance('StockLogistique',$rec[0]);
				$pdf[] = $liv->PrintDocument($fond);
			}
			$lines = Sys::$Modules['StockLogistique']->callData("RepriseTete/TourneeId=$id",false,0,999);
			foreach($lines as $lv) {
				$rec = Sys::$Modules['StockLogistique']->callData("RepriseTete/".$lv['Id'],false,0,1,'','','');
				$rep = genericClass::createInstance('StockLogistique',$rec[0]);
				$pdf[] = $rep->PrintDocument($fond);
			}
		}
		if(sizeof($pdf) > 0) {
			$file = 'Home/tmp/doc'.rand(0, 2000);
			$merge = new FPDF_Merge();
			foreach($pdf as $doc)
				$merge->add($doc);
			$merge->output($file.'.pdf');
			if($direct) {
				//exec("pdf2swf  -z -T 9 -t -s insertstop $file.pdf -o $file.swf & chmod a+w $file");
				//exec("chmod a+w $file");
				return $file.'.pdf';
			}
			$res = array('printFiles'=>array($file.'.pdf'));
		}
		else $res = null;
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	private function PrintDocument($fond=true) {
		require_once('TourneeEtat.class.php');
		$id = $this->Id;
		$lines = Sys::$Modules['StockLogistique']->callData("BLTete/TourneeId=$id",false,0,999,'ASC','Ordre','Id,Ordre');
		$rec = Sys::$Modules['StockLogistique']->callData("RepriseTete/TourneeId=$id",false,0,999,'','','Id,Ordre');
		$lines = array_merge($lines, $rec);
		usort($lines, array('Tournee','sortTournee'));
		$pdf = new TourneeEtat($this,$fond,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Tournee_'.$this->Reference);
		
		$pdf->AddPage();
		$pdf->PrintLines($lines, false);
		$pdf->PrintBottom();
		// save pdf
		$file = 'Home/Devis/Tournee_'.$this->Reference.'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}


	function ValideTournee($id,$date,$print) {
		$sts = array();
		if(! isset($id)) $id = $this->Id;
		$rec = Sys::$Modules['StockLogistique']->callData("Tournee/$id",false,0,1,'','','');
		$trn = genericClass::createInstance('StockLogistique',$rec[0]);
		$trn->Date = $date;
		$trn->Valide = 1;
		$trn->Etat = 31; // validé
		$trn->Save();
		// livraisons
		$livs = Sys::$Modules['StockLogistique']->callData("BLTete/TourneeId=$id",false,0,999);
		foreach($livs as $lv) {
			$rec = Sys::$Modules['StockLogistique']->callData("BLTete/".$lv['Id'],false,0,1);
			$liv = genericClass::createInstance('StockLogistique',$rec[0]);
			$liv->Date = $date;
			$liv->DateLivraison = $date;
			$liv->Etat = 11; // validé
			$liv->Save();
			$sts[] = array('edit',1,$liv->Id,'StockLogistique','BLTete','','',null,null);
			// devis
			$dev = $liv->getParents('DevisTete');
			$dev = $dev[0];
			$dev->EtatDevis = 3; // Livrée
			$dev->Save(true);
			$sts[] = array('edit',1,$dev->Id,'Devis','DevisTete','','',null,null);
			// lignes
			$lines = $liv->getChildren('CommandeLigne');
			foreach($lines as $line) {
				$line->Livre = 1;
				$line->Save();
			}
			// elements
			$elms = $liv->getChildren('Element');
			foreach($elms as $elm) {
				$elm->DateDepart = $date;
				$elm->Etat = 2; // livré
				if($elm->ReferenceId) {
					$rec = Sys::$Modules['StockLocatif']->callData('Reference/'.$elm->ReferenceId,false,0,1);
					$ref = genericClass::createInstance('StockLocatif',$rec[0]);
					$ref->Sorti = 1;
					$ref->Save();
				}
				$elm->Save();
			}
		}
		// reprises
		$reps = Sys::$Modules['StockLogistique']->callData("RepriseTete/TourneeId=$id",false,0,999);
		foreach($reps as $rp) {
			$rec = Sys::$Modules['StockLogistique']->callData("RepriseTete/".$rp['Id'],false,0,1);
			$rep = genericClass::createInstance('StockLogistique',$rec[0]);
			$rep->Date = $date;
			$rep->DateReprise = $date;
			$rep->Etat = 21; // en-cours
			$rep->Save;
			$sts[] = array('edit',1,$rep->Id,'StockLogistique','RepriseTete','','',null,null);
		}
		$res = array();
		if($print) {
			$file = $this->PrintDocuments($id,1,1,1,true);
			$res['printFiles'] = array($file);
			$res['mode'] = 'validate';
			Event::addEvent('','','Edit','StockLogistique','Tournee',$id);
		}
		else $res['dataValues'] = array('Etat'=>31,'Date'=>$date);
		$sts[] = array('edit',1,$this->Id,'StockLogistique','Tournee','','',null,$res);
		return WebService::WSStatusMulti($sts);
	}


	function FindReference($ref) {
	 	$rec = Sys::$Modules['StockLocatif']->callData('Reference/Reference='.$ref,false,0,1);
	 	$id = 0;
		if(count($rec)) $id = $rec[0]['Id'];
		$res = array(Id=>$id,targets=>null);
		return WebService::WSStatus('method',1,'','','','','',null,$res);
	}


	function GeoAdresses($lines) {
		$adr = array();
		foreach($lines as $line) {
			$req = $line->Type == 'L' ? 'BLTete/' : 'RepriseTete/';
			$rec = Sys::$Modules['StockLogistique']->callData($req.$line->Id,false,0,1);
			$rec = Sys::$Modules['Repertoire']->callData('Tiers/'.$rec[0]['LivraisonId'],false,0,1);
			$l = genericClass::createInstance('Repertoire',$rec[0]);
			$a = '';
			if($l->Adresse1) $a = trim($l->Adresse1);
			if($l->Adresse2) $a .= ($a != '' ? ", " :  '').trim($l->Adresse2);
			if($l->Adresse3) $a .= ($a != '' ? ", " :  '').trim($l->Adresse3);
			$a .= ($a != '' ? ", " :  '').trim($l->CodPostal).' '.trim($l->Ville);
			$p = trim($l->Pays) != '' ? trim($l->Pays) : 'France';
			$a .= ($a != '' ? ", " :  '').$p;
			
			$adr[] = array($a, $l->GPS, trim($l->Intitule));
		}
		$res = array(dataValues=>array(map=>$adr));
		return WebService::WSStatus('method',1,'','','','','',null,$res);
	}
	

	function GetDeparts($dat=null,$mag='',$cp='',$vil='',$cli='') {
		if($dat) {
			if($dat[0]) $flt .= '&DateLivraison>='.$dat[0];
			if($dat[1]) $flt .= '&DateLivraison<='.$dat[1];
		}
		if($mag) $flt .= "&LivraisonId~%$mag";
		if($cp) $flt .= "&CodPostal~$cp";
		if($vil) $flt .= "&Ville~%$vil";
		if($cli) $flt .= "&ClientId~%$cli";
		$req = 'BLTete/TourneeId=0'.$flt;
		$items = array();
		$t = time();
		$rec = Sys::$Modules['StockLogistique']->callData($req,false,0,999,'ASC','DateLivraison');
		foreach($rec as $rc) {
			$lid = $rc['Id'];
			$lines = array();
			$elms = Sys::$Modules['StockLogistique']->callData("BLTete/$lid/CommandeLigne",false,0,999,'ASC','DateLivraison');
			foreach($elms as $el) {
				$lines[] = array(LivraisonId=>sprintf('%3d   ', $el['Quantite']).$el['Famille']);
			}
			$items[] = array(Id=>$lid,Reference=>$rc['Reference'],Type=>'L',DateLR=>$rc['DateLivraison'],
					ClientId=>$rc['ClientIntitule'],LivraisonId=>$rc['LivraisonIntitule'],CodPostal=>$rc['LivraisonCodPostal'],
					Ville=>$rc['LivraisonVille'],children=>$lines,'DateLR_Color'=>$rc['DateLivraison']<=$t?'0xff0000':'0x000000');
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$items);
	}

	function GetRetours($dat=null,$mag='',$cp='',$vil='',$cli='') {
		if($dat) {
			if($dat[0]) $flt .= '&DateReprise>='.$dat[0];
			if($dat[1]) $flt .= '&DateReprise<='.$dat[1];
		}
		if($mag) $flt .= "&LivraisonId~%$mag";
		if($cp) $flt .= "&CodPostal~$cp";
		if($vil) $flt .= "&Ville~%$vil";
		if($cli) $flt .= "&ClientId~%$cli";
		$req = 'RepriseTete/TourneeId=0'.$flt;
		$items = array();
		$t = time();
		$rec = Sys::$Modules['StockLogistique']->callData($req,false,0,999,'ASC','DateReprise');
		foreach($rec as $rc) {
			$lid = $rc['Id'];
			$lines = array();
			$elms = Sys::$Modules['StockLogistique']->callData("RepriseTete/$lid/Element",false,0,999,'ASC','Famille');
			foreach($elms as $el) {
				$lines[] = array(LivraisonId=>sprintf('%3d   ', $el['Quantite']).$el['Famille']);
			}
			$items[] = array(Id=>$lid,Reference=>$rc['Reference'],Type=>'R',DateLR=>$rc['DateReprise'],
					ClientId=>$rc['ClientIntitule'],LivraisonId=>$rc['LivraisonIntitule'],CodPostal=>$rc['LivraisonCodPostal'],
					Ville=>$rc['LivraisonVille'],children=>$lines,'DateLR_Color'=>$rc['DateReprise']<=$t?'0xff0000':'0x000000');
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$items);
	}


	function GetLignes($id) {
		if(! $id) return WebService::WSData('',0,0,0,'','','','','',null);
		$items = array();
		$t = time();
		$req = "BLTete/TourneeId=$id";
		$rec = Sys::$Modules['StockLogistique']->callData($req,false,0,999);
		foreach($rec as $rc) {
			$lid = $rc['Id'];
			$lines = array();
			$elms = Sys::$Modules['StockLogistique']->callData("BLTete/$lid/CommandeLigne",false,0,999,'ASC','Id');
			foreach($elms as $el)
				$lines[] = array('LivraisonId'=>sprintf('%3d   ', $el['Quantite']).$el['Famille']);
			$etat = $rc['Etat'] == 13 ? 1 : 0;
			$items[] = array('Id'=>$lid,'Reference'=>$rc['Reference'],'Type'=>'L','DateLR'=>$rc['DateLivraison'],
					'ClientId'=>$rc['ClientIntitule'],'LivraisonId'=>$rc['LivraisonIntitule'],'CodPostal'=>$rc['LivraisonCodPostal'],
					'Ville'=>$rc['LivraisonVille'],'children'=>$lines,'Ordre'=>$rc['Ordre'],'Status'=>$rc['Status'],
					'Status_Color'=>$rc['Status_Color'],'_etat'=>$etat,'Action'=>$rc['Action'],'ActionTexte'=>$rc['ActionTexte'],
					'ActionTraite'=>$rc['ActionTraite']);
		}

		$req = 'RepriseTete/TourneeId='.$id;
		$rec = Sys::$Modules['StockLogistique']->callData($req,false,0,999);
		foreach($rec as $rc) {
			$lid = $rc['Id'];
			$lines = array();
			$elms = Sys::$Modules['StockLogistique']->callData("RepriseTete/$lid/Element",false,0,999,'ASC','Famille');
			foreach($elms as $el)
				$lines[] = array('LivraisonId'=>sprintf('%3d   ', $el['Quantite']).$el['Famille'],'Ville'=>$el['Reference']);
			$etat = $rc['Etat'] == 23 ? 1 : 0;
			$items[] = array('Id'=>$lid,'Reference'=>$rc['Reference'],'Type'=>'R','DateLR'=>$rc['DateReprise'],
					'ClientId'=>$rc['ClientIntitule'],'LivraisonId'=>$rc['LivraisonIntitule'],'CodPostal'=>$rc['LivraisonCodPostal'],
					'Ville'=>$rc['LivraisonVille'],'children'=>$lines,'Ordre'=>$rc['Ordre'],'Status'=>$rc['Status'],
					'Status_Color'=>$rc['Status_Color'],'_etat'=>$etat,'Action'=>$rc['Action'],'ActionTexte'=>$rc['ActionTexte'],
					'ActionTraite'=>$rc['ActionTraite']);
		}
		usort($items, array('Tournee','sortTournee'));
		$c = count($items);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$items);
	}

	function sortTournee($a, $b) {
		return $a['Ordre'] - $b['Ordre'];
	}

	function getReprises($id) {
		if(! $id) return WebService::WSData('',0,0,0,'','','','','',null);
		$reps = Sys::$Modules['StockLogistique']->callData("Reprise/TourneeId=$id",false,0,999);
		foreach($reps as &$r) $r['Controle_ToolTip'] = 'Contrôle effectué';
		$c = count($reps);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$reps);
	}

	
	function ValideRetour($id) {
		if(! isset($id)) $id = $this->Id;
		$rec = Sys::$Modules['StockLogistique']->callData("Tournee/$id",false,0,1,'','','');
		$trn = genericClass::createInstance('StockLogistique',$rec[0]);
/*
		$ok = true;
		$lines = Sys::getData('StockLogistique',"BLTete/TourneeId=$id");
		foreach($lines as $line) {
			if($line->Etat != 12) {
				$ok = false;
				$line->Etat = 13;
				$line->Save();
			}
		}
		$lines = Sys::getData('StockLogistique',"RepriseTete/TourneeId=$id");
		foreach($lines as $line) {
			if($line->Etat != 22) {
				$ok = false;
				$line->Etat = 23;
				$line->Save();
			}
		}
*/
		$res = array('mode'=>'validate');
		if($this->Id) {
			$this->Effectue = 1;
			$res['dataValues'] = array('Effectue'=>1);
		}
		$this->controleRetour();
		// elements non repris
		$elr = Sys::getData('StockLogistique','Element/RepriseTete.RepriseTeteId(TourneeId='.$trn->Id.')');
		$rep = $trn->getChildren('Reprise');
		foreach($elr as $er) {
			//if(! $rp->Controle) $ok = false;
			$er->_repris = 0;
			foreach($rep as $rp) {
				if($er->Id == $rp->ElementId) {
					$er->_repris = 1;
					break;
				}
			}
		}
		foreach($elr as $er) {
			if(! $er->_repris) {
				$ok = false;
				$rp = genericClass::createInstance('StockLogistique','Reprise');
				$rp->addParent($trn);
				$rp->ReferenceId = $er->ReferenceId;
				$rp->ElementId = $er->Id;
				$rp->Reference = $er->Reference;
				$rp->Quantite = $er->Quantite;
				$rp->Date = $trn->Date;
				$rp->Commentaire = 'Reprise non effectuée';
				$rp->Controle = 0;
				$rp->NonRepris = 1;
				$rp->Anomalie = 3;
				$rp->Save();
			}
		}
		if($ok) {
			$trn->Etat = 32; // effectuée
			$trn->Save();
		}
		return WebService::WSStatus('edit',1,$id,'StockLogistique','Tournee','','',null,$res);
	}


	public function ControleReprise() {
		$reps = $this->controleRetour();
		return WebService::WSStatus('method',1,'','','','','',null,$res);
	}
	
	// controle les livraisons
	private function controleLivraisons($trn, $reps) {
		$bls = Sys::getData('StockLogistique','BLTete/TourneeId='.$trn->Id);
		foreach($bls as $bl) {
			$elms = $bl->getChildren('Element');
			$cnt = count($elms);
			$nlv = 0;
			foreach($elms as $elm) {
				foreach($reps as $rep)
					if(!$rep->_controle && $rep->ReferenceId == $elm->ReferenceId) {
						$nlv++;
						$rep->Anomalie = 1;
						$rep->Commentaire = 'Non livré';
						$rep->Save();
						$rep->_controle = 1;
					}
			}
			if(! $nlv) $bl->Etat = 12;  // livré
			else if($nlv < $cnt) $bl->Etat = 13;  // liv partielle
			else $bl->Etat = 14;  // non livré
			$bl->Save();
		}
	} 

	// controle des reprises
	private function controleReprises($trn, $reps) {
		$rps = Sys::getData('StockLogistique','RepriseTete/TourneeId='.$trn->Id);
		foreach($rps as $rp) {
			$elms = $rp->getChildren('Element');
			$cnt = count($elms);
			$rpr = 0;
			foreach($elms as $elm) {
				foreach($reps as $rep)
					if(!$rep->_controle && $rep->ReferenceId == $elm->ReferenceId) {
						$rpr++;
						$rep->Controle = 1;
						$rep->Commentaire = 'OK';
						$rep->Save();
						$rep->_controle = 1;
						$elm->_controle = 1;
					}
			}
			if(! $rpr) $bl->Etat = 24;  // non repris
			else if($rpr < $cnt) $bl->Etat = 23;  // rep partielle
			else $bl->Etat = 22;  // repris
 			$bl->Save();
		}
	}

	// controle de remise en stock
	private function controleRetour($id=0) {
		$reps = Sys::getData('StockLogistique','Reprise/Controle=0&NonRepris=0',0,9999,'ASC','TourneeId');
		foreach($reps as &$rep) {
			$s = '';
			if(! $rep->ReferenceId) {
				$ref = Sys::getData('StockLocatif','Reference/Reference='.$rep['Reference'],0,1);
				if(count($ref)) $rep->ReferenceId = $ref[0]->Id;
			}
			if(! $rep->ReferenceId) $s = 'Référence inconnue';
			else {
				// controle sur la reference
				$ref = genericClass::createInstance('StockLocatif','Reference');
				$ref->initFromId($rep->ReferenceId);
				if($ref->Vendu) $s .= 'Référence vendue.  ';
				if($ref->HS) $s .= 'Référence hors service.  ';
				if(! $ref->Sorti) $s .= 'Référence non sortie.  ';
				
				$trn = $rep->getParents('Tournee');
				$trn = $trn[0];
				
				// controle sur l'element
				if(! $rep->ElementId) {
					if($ref->Sorti) $s = 'Référence non sortie.  ';
				}
				else {
					$elm = Sys::getData('StockLogistique','Element/Id='.$rep->ElementId);
					$elm = $elm[0];
					$ok = false;
					// controle non livre
					$liv = Sys::getData('StockLogistique','BLTete/TourneeId='.$trn->Id);
					foreach($liv as $lv) {
						if($elm->BLTeteId == $lv->Id) {
							$s = 'Livraison non effectuée';
							$rep->Anomalie = 1;
							$ok = true;
							break;
						}
					}
					// controle reprise
					if(! $ok) {
						$brp = $elm->getParents('RepriseTete');
						if(count($brp) && $brp[0]->TourneeId == $trn->Id)
							$rep->Controle = 1;
						else {
							$s .= 'Reprise non attendue';
							$rep->Anomalie = 2;
						}
					}
				}
			}
			$rep->Commentaire = $s;
			$rep->Save();
		}
		return $reps;
//		return WebService::WSStatus('edit',1,$this->Id,'StockLogistique','Tournee','','',array(),$res);
	}

	public function SauveTournee($mode, $args) {
		$trn =  genericClass::createInstance('StockLogistique','Tournee');
		$trn->Id = $args->Id;
		$trn->Reference = $args->Reference;
		$trn->Date = $args->Date;
		$trn->ChauffeurId = $args->ChauffeurId;
		$trn->VehiculeId = $args->VehiculeId;
		$trn->Etat = $mode ? 32 : 33; // effectue / non effectue
		$trn->Save();
		foreach($args->lignes as $line) {
			if($line->Type == 'L') {
				$liv = genericClass::createInstance('StockLogistique','BLTete');
				$liv->initFromId($line->Id);
				$liv->Etat = (! $mode || $line->_etat == 1) ? 13 : 12;
				// reprise BL
				if($line->Action == 1 && ! $liv->ActionTraite) {
					$this->remiseEnLivraison($liv);
//					$elms = $liv->getChildren('Element');
//					foreach($args->elements as $elr) {
//						foreach($elms as $elm) {
//							if($elm->ReferenceId == $elr->ReferenceId) {
//								$elr->_action = 0;
//								$elr->Controle = 1;
//								break;
//							}
//						}
//					}
					$liv->Action = $line->Action;
					$liv->ActionTexte = $line->ActionTexte;
					$liv->ActionTraite = 1;
					$line->ActionTraite = 1;
				}
				$liv->Save();
			}
			else {
				$rep = genericClass::createInstance('StockLogistique','RepriseTete');
				$rep->initFromId($line->Id);
				$rep->Etat = (! $mode || $line->_etat == 1) ? 23 : 22;
				// reprise BR
				if($line->Action == 1 && ! $rep->ActionTraite) {
//					$this->remiseEnReprise($rep);
					$elms = $rep->getChildren('Element');
					foreach($elms as $elm) {
						foreach($args->elements as $elr) {
							if($elm->ReferenceId == $elr->ReferenceId) {
								$elm = genericClass::createInstance('StockLogistique','Element');
								$elm->initFromId($elr->ElementId);
								$elm->resetParents('RepriseTete');
								$elr->ActionTraite = 1;
								$elm->Save();
								$elr->Controle = 1;
								break;
							}
						}
					}
					$rep->Action = $line->Action;
					$rep->ActionTexte = $line->ActionTexte;
					$rep->ActionTraite = 1;
					$line->ActionTraite = 1;
				}
				$rep->Save();
			}
		}
		if($mode) {
			foreach($args->elements as $elr) {
				if($elr->Action == 1 && ! $elr->ActionTraite) {
					$elm = genericClass::createInstance('StockLogistique','Element');
					$elm->initFromId($elr->ElementId);
					$elm->resetParents('RepriseTete');
					$elr->ActionTraite = 1;
					$elm->Save();
				}
				if($elr->Action == 2 && ! $elr->ActionTraite) {
					foreach($args->elements as $el) {
						if($elr->ElementId == $el->_elementId) {
							$this->echangeReprise($elr->ElementId,$el->ElementId,$args->Date);
							$elr->ActionTraite = 1;
							$el->ActionTraite = 1;
						}
					}
				}
				if($elr->Action == 5 && ! $elr->ActionTraite) {
					$blt = genericClass::createInstance('StockLogistique', 'BLTete');
					$blt->RemiseEnStock($elr->ElementId,$args->Date,0);
					$elr->ActionTraite = 1;
				}
				$rep = genericClass::createInstance('StockLogistique','Reprise');
				$rep->initFromId($elr->Id);
				$rep->Action = $elr->Action;
				$rep->ActionTexte = $elr->ActionTexte;
				$rep->ActionTraite = $elr->ActionTraite;
				$rep->Save();
			}
		}
		$res = array('Etat'=>$trn->Etat,'lignes'=>$args->lignes,'elements'=>$args->elements);
		$sts = array();
		$sts[] = array('method', 1, '', 'StockLogistique', 'RepriseTete', '', '', null, null);
		$sts[] = array('method', 1, '', 'StockLogistique', 'BLTete', '', '', null, null);
		$sts[] = array('method', 1, '', 'StockLogistique', 'Element', '', '', null, null);
		$sts[] = array('method', 1, $id, 'StockLogistique', 'Tournee', '', '', null, array('dataValues'=>$res));
		return WebService::WSStatusMulti($sts);
	}

	private function remiseEnLivraison($liv) {
		$dev = $liv->getParents('DevisTete');
		$dev = $dev[0];
		$cli = Sys::getData('Repertoire', 'Tiers/'.$dev->ClientId);
		$cli = $cli[0];
		$liv = Sys::getData('Repertoire', 'Tiers/'.$dev->LivraisonId);
		$liv = $liv[0];
		$livr = genericClass::createInstance('StockLogistique','BLTete');
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
		$cls = $dev->getChildren('CommandeLigne:NOVIEW');
		foreach($cls as $c) {
			$cl = genericClass::createInstance('StockLogistique', 'CommandeLigne');
			$cl->addParent($dev);
			$cl->addParent($livr);
			$cl->addParent($cli,'ClientId');
			$cl->addParent($liv,'LivraisonId');
			$cl->FamilleId = $c->FamilleId;
			$cl->Designation = $c->Designation;
			$cl->Quantite = $c->Quantite;
			$cl->DateDebut = $c->DateDebut;
			$cl->DateFin = $c->DateFin;
			$cl->DateLivraison = $c->DateLivraison;
			$cl->DateReprise = $c->DateReprise;
			$cl->ModeTarif = $c->ModeTarif;
			$cl->Save(true);
			$qt = $cl->Quantite;
			if($cl->ModeTarif == 1) {
				for($i = 0; $i < $qt; $i++) {
					$elm = genericClass::createInstance('StockLogistique', 'Element');
					$elm->Quantite = 1;
					$elm->addParent($cl);
					$elm->addParent($dev);
					$elm->addParent($livr);
					$elm->DateLivraison = $c->DateLivraison;
					$elm->DateReprise = $c->DateReprise;
					$elm->DateDepart = null;
					$elm->DateRetour = null;
					$elm->Etat = $etat;
					$elm->Save(true);
				}
			} elseif($cl->ModeTarif == 2) {
				$elm = genericClass::createInstance('StockLogistique', 'Element');
				$elm->Quantite = $qt;
				$elm->addParent($cl);
				$elm->addParent($dev);
				$elm->addParent($livr);
				$elm->DateLivraison = $c->DateLivraison;
				$elm->DateReprise = $c->DateReprise;
				$elm->DateDepart = null;
				$elm->DateRetour = null;
				$elm->Save(true);
			}
		}
	}

	private function remiseEnReprise($rep) {
		$dev = $rep->getParents('DevisTete');
		$dev = $dev[0];
		$cli = Sys::getData('Repertoire', 'Tiers/'.$dev->ClientId);
		$cli = $cli[0];
		$liv = Sys::getData('Repertoire', 'Tiers/'.$dev->LivraisonId);
		$liv = $liv[0];
		$repr = genericClass::createInstance('StockLogistique','RepriseTete');
		$repr->addParent($dev);
		$repr->Reference = '';
		$repr->Societe =		$rep->Societe;
		$repr->ClientId = 		$rep->ClientId;
		$repr->LivraisonId =	$rep->LivraisonId;
		$repr->DateDebut = 		$rep->DateDebut;
		$repr->DateFin = 		$rep->DateFin;
		$repr->DateReprise = 	$rep->DateReprise;
		$repr->Date =			$rep->DateReprise;
		$repr->Etat = 			20; // prevu
		$repr->Save(true);
		$elms = $rep->getChildren('Element');
		foreach($elms as $elm) {
			$elm->addParent($repr);
			$elm->Save();
		}
	}
	
	private function echangeReprise($id0, $id1, $date) {
		// element d'origine 
		$el0 = genericClass::createInstance('StockLogistique','Element');
		$el0->initFromId($id0);
		//$el0->DateReprise = $date;
		$el0->DateRetour = $date;
		$el0->Etat = 4; // repris
		$el0->Save();		
		$c0 = $el0->getParents('CommandeLigne');
		$c0 = $c0[0];
		// element repris
		$el1 = genericClass::createInstance('StockLogistique','Element');
		$el1->initFromId($id1);
		$c1 = $el1->getParents('CommandeLigne');
		$c1 = $c1[0];
		$dev = $c1->getParents('DevisTete','DevisId');
		$dev = $dev[0];
		$cli = genericClass::createInstance('Repertoire','Tiers');
		$cli->initFromId($dev->ClientId);
		$liv = genericClass::createInstance('Repertoire','Tiers');
		$liv->initFromId($dev->LivraisonId);
		// nouvelle ligne de commande pour transfer
		$cl = genericClass::createInstance('StockLogistique','CommandeLigne');
		$cl->addParent($dev);
		//$cl->addParent($livr);
		$cl->addParent($cli,'ClientId');
		$cl->addParent($liv,'LivraisonId');
		$cl->FamilleId = $c0->FamilleId;
		$cl->Designation = $c0->Designation.' (Echange)';
		$cl->Quantite = 1;
		$cl->DateDebut = $c1->DateDebut;
		$cl->DateFin = $c1->DateFin;
		$cl->DateLivraison = $date;
		$cl->DateReprise = $c1->DateReprise;
		$cl->ModeTarif = $c1->ModeTarif;
		$cl->Livre = 1;
		$cl->Echange = 1;
		$cl->Save(true);
		// nouvel element d'origine pour transfer 
		$elm = genericClass::createInstance('StockLogistique', 'Element');
		$elm->Quantite = 1;
		$elm->addParent($cl);
		$elm->addParent($dev);
		//$elm->addParent($livr);
		$elm->ReferenceId = $el0->ReferenceId;
		$elm->DateLivraison = $date;
		$elm->DateReprise = $c1->DateReprise;
		$elm->DateDepart = $date;
		$elm->DateRetour = null;
		$elm->Etat = 2; // livre
		$elm->Save(true);
	}

}
