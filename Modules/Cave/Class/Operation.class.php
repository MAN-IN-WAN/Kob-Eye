<?php
class Operation extends genericClass {
	

	function SaveOperation($args) {
		$err = '';
		if(! $args->OperateurId) $err .= "- Opérateur\n";
		if(! $args->Date) $err .= "- Date\n";
		if(! $args->CuveId) $err .= "- Cuve origine\n";
		if($args->TypeId == 1) {
			if(! $args->LotId && (! $args->CategorieId || ! $args->CouleurId)) $err .= "- Lot - Catégorie - Couleur\n";
			if(! $args->TiersId) $err .= $args->SousTypeId == 1 ? "- Producteur\n" : "- Client\n";
			if(! $args->NumeroDae) $err .= "- Numéro DAE\n";
		}
		if(($args->TypeId == 2 || ($args->TypeId == 3 && $args->transfert == 1)) && ! $args->CuveIdB) $err .= "- Cuve destination\n";
		if($args->TypeId != 4 && $args->SousTypeId != 51 && $args->TypeId != 6 && $args->TypeId != 7 && ! $args->Volume) $err .= "- Volume\n";
		if($args->SousTypeId == 71 && ! $args->CategorieId) $err .= "- Catégorie\n";
		if($args->SousTypeId == 71 && ! $args->CouleurId) $err .= "- Couleur\n";
		if($args->SousTypeId == 72 && ! $args->CategorieId) $err .= "- Catégorie\n";
		if($err) {
			$err = "Données manquentes :\n".$err;
			return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>$err)), null);
		}

		$sts = array();
		$cuve = genericClass::createInstance('Cave','Cuve');
		$cuve->initFromId($args->CuveId);
		$this->addParent($cuve);
		if($args->LotId) {
			$lot = genericClass::createInstance('Cave','Lot');
			$lot->initFromId($args->LotId);
			$this->addParent($lot);
			$this->Degre = $lot->Degre;
		}
		$this->TypeId = $args->TypeId;
		$this->SousTypeId = $args->SousTypeId;
		$this->Date = $args->Date;
		$this->OperateurId = $args->OperateurId;
		$this->Notes = $args->Notes;
		$this->VolumeTheorique = $args->Volume;
		$this->VolumeReel = $args->Volume;
		$this->NumeroDae = $args->NumeroDae;
		$this->ProduitId = $args->ProduitId;
		$this->LotProduit = $args->LotProduit;
		$this->Dosage = $args->Dosage;
		
		switch($args->TypeId) {
		case 1: // Stock
			if($args->SousTypeId == 1) {  // entree en stock
				$cuve->EtatCuveId = 11; // utilisée
				if(! $args->LotId) {
					$lot = genericClass::createInstance('Cave','Lot');
					$lot->CategorieId = $args->CategorieId;
					$lot->CouleurId = $args->CouleurId;
					$lot->Date = $args->Date;
					$lot->EtatLotId = STL_PREPA;
					$lot->VolumeReel = $lot->VolumeRestant = $args->Volume;
					$lot->Save();
					$cuve->addParent($lot);
					AlertUser::addAlert('Nouveau lot (Entrée en stock) '.$lot->Lot,'LO'.$lot->Id,'Cave','Lot',$lot->Id,null,'CAVE','');
				}
				elseif($lot->EtatLotId == STL_ENCOURS) {
					$lot = $this->changeLot($lot, $cuve, $args->Volume, $sts);
					$cuve->addParent($lot);
				}
				else {
					$cvs = Sys::getData('Cave','Lot/'.$lot->Id.'/Cuve:NOVIEW/EtatCuveId='.STC_UTILISE);
					if(is_array($cvs) && count($cvs) > 1) $lot = $this->changeLot($lot, $cuve, $args->Volume, $sts);
					else {
						$lot->VolumeReel += $args->Volume;
						$lot->VolumeRestant += $args->Volume;
					}
				}
				$cuve->Vide = 0;
				$cuve->Volume += $args->Volume;
			}
			else if($args->SousTypeId == 2) {  // sortie de stock
				$cuve->Volume -= $args->Volume;
				$lot->VolumeRestant -= $args->Volume;
				$lot->EtatLotId = STL_ENCOURS; // en cours
				if($args->CuveVide == 1) $regul = $this->cuveVide($cuve, $lot, $args->Ecart, $sts);
			}
			$this->addParent($lot);
			$this->addParent('Cave/Tiers/'.$args->TiersId);
			$this->NumeroDae = $args->NumeroDae;
			break;
		case 3: // Traitement
			$this->SousTypeId = $args->SousTypeId; // sortie
			if(! $args->transfert) break;
		case 2: // Transfert
			$this->SousTypeId = $args->SousTypeId; // sortie
			// transfert
			if($args->CuveIdB) {
				$cuve->Volume -= $args->Volume;
				// cuve destination
				$cuveB = genericClass::createInstance('Cave','Cuve');
				$cuveB->initFromId($args->CuveIdB);
				$this->AutreCuveId = $args->CuveIdB;
				
				// operation d'entree
				$oper = genericClass::createInstance('Cave','Operation');
				$oper->addParent($cuveB);
				$oper->AutreCuveId = $args->CuveId;
				$oper->TypeId = 2; // transfert
				$oper->SousTypeId = 21; // entree
				$oper->Date = $this->Date;
				$oper->OperateurId = $this->OperateurId;
				$oper->Degre = $this->Degre;
				$oper->VolumeTheorique = $this->VolumeTheorique;
				$oper->VolumeReel = $this->VolumeReel;

				if($args->LotIdB && $args->LotIdB != $args->LotId) {
					$lot->VolumeRestant -= $args->Volume ;
					$lot->EtatLotId = STL_ENCOURS; // en cours

					$lotB = genericClass::createInstance('Cave','Lot');
					$lotB->initFromId($args->LotIdB);
					if($lotB->EtatLotId == STL_ENCOURS) {
						$lotC = $this->changeLot($lotB, $cuveB, $args->Volume, $sts);
					}
					else {
						$cvs = Sys::getData('Cave','Lot/'.$lot->Id.'/Cuve:NOVIEW/EtatCuveId='.STC_UTILISE);
						if(is_array($cvs) && count($cvs) > 1) $lotC = $this->changeLot($lotB, $cuveB, $args->Volume, $sts);
					}
					if($lotC) {
						$oper->addParent($lotC);
						$cuveB->addParent($lotC);
					}
					else {
						$oper->addParent($lotB);
						$oper->Degre = $lotB->Degre;
						$lotB->VolumeReel += $args->Volume;
						$lotB->VolumeRestant += $args->Volume;
					}
				}
				else {
					$oper->addParent($lot);
					$cuveB->addParent($lot);
				}
				if($args->CuveVide == 1) $regul = $this->cuveVide($cuve, $lot, $args->Ecart, $sts);
				$cuveB->Vide = 0;
				$cuveB->EtatCuveId = STC_UTILISE; // utilisée
				$cuveB->Volume += $args->Volume;
			}
			break;
		case 4: // nettoyage
			switch($this->SousTypeId) {
				case 41: $cuve->EtatCuveId = 13; break;
				case 42: $cuve->EtatCuveId = 14; break;
				case 43: $cuve->EtatCuveId = 15; break;
			}
			$cuve->resetParents('Lot');
			$cuve->Volume = 0;
			$cuve->Vide = 1;
			break;
		case 5: // Inventaire
			switch($this->SousTypeId) {
				case 51: 
					$cuve->Volume = $args->Volume;
					if($lot) $lot->VolumeRestant -= $args->Contenu - $args->Volume;
					break;
				case 52:
					$cuve->Volume += $args->Volume;
					if($lot) {
						$lot->VolumeRestant += $args->Volume;
						$lot->VolumeReel += $args->Volume;
					}
					break;
				case 53:
					$cuve->Volume -= $args->Volume;
					if($lot) $lot->VolumeRestant -= $args->Volume;
					break;
			}
			break;
		case 6: // Analyse
			$ana = genericClass::createInstance('Cave','Analyse');
			$ana->addParent($lot);
			$ana->addParent($cuve);
			$ana->CO2 = $args->CO2;
			$ana->D = $args->D;
			$ana->TAV = $args->TAV;
			$ana->SRinf = $args->SRinf;
			$ana->SRsup = $args->SRsup;
			$ana->GFinf = $args->GFinf;
			$ana->GFsup = $args->GFsup;
			$ana->AT = $args->AT;
			$ana->pH = $args->pH;
			$ana->AV = $args->AV;
			$ana->AM = $args->AM;
			$ana->AL = $args->AL;
			$ana->ATar = $args->ATar;
			$ana->IPC = $args->IPC;
			$ana->IC = $args->IC;
			$ana->SO2Lib = $args->SO2Lib;
			$ana->SO2Tot = $args->SO2Tot;
			$ana->Turbidite = $args->Turbidite;
			$ana->StabPro = $args->StabPro;
			if($ana->TAV) $this->Degre = $lot->Degre = $ana->TAV;
			if($ana->AV) $lot->AV = $ana->AV;
			break;
		case 7: // classement
			$lot = $this->changeLot($lot, $cuve, $args->Contenu, $sts);
klog::l("xxx".print_r($args,1));
			if($args->CategorieId) $lot->CategorieId = $args->CategorieId;
			if($args->CouleurId) $lot->CouleurId = $args->CouleurId;
klog::l("<<<".print_r($lot,1));
			$cuve->addParent($lot);
/*					
			switch($this->SousTypeId) {
				case 72: // labellisation
					$this->VolumeTheorique = $args->Contenu;
					$this->VolumeReel = $args->Contenu;
					$lot->CategorieId = $args->CategorieId;
					break;
			}
*/
			break;
		}

		if($lot) $lot->Save();
klog::l(">>>".print_r($lot,1));
		if($lotB) $lotB->Save();
		$this->Save();
		if($args->TypeId == 5) $cuve->InventaireId = $this->Id;
		else $cuve->OperationId = $this->Id;
		$cuve->Save();
		if($oper) {
			$oper->TransfertId = $this->Id;
			$oper->Save();
			$cuveB->OperationId = $oper->Id;
			$cuveB->Save();
			$this->TransfertId = $oper->Id;
			$this->Save();
			$sts[] = array('add', 1, $oper->Id, 'Cave', 'Operation', '', '', null, null);
		}
		if($ana) {
			$ana->OperationId = $this->Id;
			$ana->Save();
			$this->AnalyseId = $ana->Id;
			$this->Save();
			$sts[] = array('add', 1, $ana->Id, 'Cave', 'Analyse', '', '', null, null);
		}
		if($regul) {
			$regul->Save();
			$sts[] = array('add', 1, $regul->Id, 'Cave', 'Operation', '', '', null, null);
		}
		$sts[] = array('add', 1, $this->Id, 'Cave', 'Operation', '', '', null, null);
		if($lot) $sts[] = array($args->LotId ? 'edit' : 'add', 1, $lot->Id, 'Cave', 'Lot', '', '', null, null);
		if($lotB) $sts[] = array('edit', 1, $lotB->Id, 'Cave', 'Lot', '', '', null, null);
		if($lotC) $sts[] = array('add', 1, $lotC->Id, 'Cave', 'Lot', '', '', null, null);
		if($cuveB) $sts[] = array('edit', 1, $CuveB->Id, 'Cave', 'Cuve', '', '', null, null);
		$sts[] = array('edit', 1, $cuve->Id, 'Cave', 'Cuve', '', '', null, null);
		return WebService::WSStatusMulti($sts);
	}

	private function cuveVide($cuve, $lot, $volume, &$sts) {
		$cuve->EtatCuveId = STC_SALE; // sale
		$cuve->Volume = 0;
		AlertUser::addAlert('Cuve sale '.$cuve->Cuve,'CU'.$cuve->Id,'Cave','Cuve',$cuve->Id,null,'CAVE','');
		
		//$lot->VolumeReel -= $volume;
		$lot->VolumeRestant -= $volume;
		if($lot->VolumeRestant <= 0) {
			$lot->VolumeRestant = 0;
			$lot->EtatLotId = STL_SOLDE;
			AlertUser::addAlert('Lot soldé '.$lot->Lot,'LO'.$lot->Id,'Cave','Lot',$lot->Id,null,'CAVE','');
		}
		if(! $volume) return null;
		$regul = genericClass::createInstance('Cave','Operation');
		$regul->addParent($cuve);
		$regul->addParent($lot);
		$regul->Degre = $lot->Degre;
		$regul->TypeId = 5;
		$regul->SousTypeId = $volume < 0 ? 52 : 53;
		$regul->Date = $this->Date;
		$regul->OperateurId = $this->OperateurId;
		$regul->VolumeReel = abs($volume);
		return $regul;
	}

	// Lot en-cours ou lot en preparation sur plusieurs cuve
	// creation d'un nouveau lot et de mouvements de sortie et d'entree
	// entre l'ancien et le nouveau lot
	private function changeLot($lotB, $cuveB, $volume, &$sts) {
		// nouveau lot
		$lotC = genericClass::createInstance('Cave','Lot');
		$lotC->EtatId = STL_PREPA; // preparation
		$lotC->Date = $this->Date;
		$lotC->CategorieId = $lotB->CategorieId;
		$lotC->CouleurId = $lotB->CouleurId;
		//$lotC->Degre = $lotB->Degre;
		$lotC->VolumeReel = $lotC->VolumeRestant = $cuveB->Volume;
		$lotC->Save();
		AlertUser::addAlert('Nouveau lot (Mélange) '.$lotC->Lot,'LO'.$lotC->Id,'Cave','Lot',$lotC->Id,null,'CAVE','');
		// mouvements sortie
		$tran = genericClass::createInstance('Cave','Operation');
		$tran->addParent($cuveB);
		$tran->addParent($lotB);
		$tran->Degre = $lotB->Degre;
		$tran->AutreCuveId = $cuveB->Id;
		$tran->TypeId = 2;
		$tran->SousTypeId = 22; // sortie
		$tran->Date = $this->Date;
		$tran->OperateurId = $this->OperateurId;
		$tran->VolumeReel = $tran->VolumeTheorique = $cuveB->Volume;
		$tran->Save();
		$sts[] = array('add', 1, $tran->Id, 'Cave', 'Operation', '', '', null, null);
		// mouvements entree
		$tran = genericClass::createInstance('Cave','Operation');
		$tran->addParent($cuveB);
		$tran->addParent($lotC);
		$tran->Degre = $lotC->Degre;
		$tran->AutreCuveId = $cuveB->Id;
		$tran->TypeId = 2;
		$tran->SousTypeId = 21; // entree
		$tran->Date = $this->Date;
		$tran->OperateurId = $this->OperateurId;
		$tran->VolumeReel = $tran->VolumeTheorique = $cuveB->Volume;
		$tran->Save();
		$sts[] = array('add', 1, $tran->Id, 'Cave', 'Operation', '', '', null, null);
		// ancien lot
		$rest = $lotB->VolumeRestant - $cuveB->Volume;
		if($rest <= 0) {
			$lotB->EtatLotId = STL_SOLDE;
			$lotB->VolumeRestant = 0;
			//$lotB->VolumeReel -= $rest;
		}
		else {
			$lotB->EtatLotId = STL_ENCOURS;
			$lotB->VolumeRestant = $rest;
		}
		$lotB->Save();
		return $lotC;
	}

	// proxy action : bonus malus sur cuve vide
	public function ResteCuve($cuve, $volume, $vide, $transfert) {
		if($cuve) {
			$c = Sys::getData('Cave', 'Cuve/'.$cuve);
			$dif = $c[0]->Volume - $volume;
		}
		else $dif = -$volume;
		if(! $transfert) {
			$ret = array('Ecart'=>'', 'labelEcart'=>'','CuveVide'=>0,'Volume'=>($cuve ? $c[0]->Volume : ''));
			return WebService::WSStatus('method', 1, '', '', '', '', '', null, array('dataValues'=>$ret));
		}
		$ret = array('Ecart'=>$dif, 'labelEcart'=>'');
/*
		if($dif <= 0 && ! $vide) {
			$vide = 1;
			$ret['CuveVide'] = 1;
		}
*/
		if($vide && $dif != 0) {
			if($dif < 0) $ret['labelEcart'] = 'Bonus sur opération';
			else $ret['labelEcart'] = 'Perte sur opération';
		}
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, array('dataValues'=>$ret));
	}
}
