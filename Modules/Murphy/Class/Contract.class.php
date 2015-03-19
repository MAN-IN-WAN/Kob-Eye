<?php
class Contract extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	
	function Save($mode=false) {
		$id = $this->Id;
		if(! $this->Status) $this->Status = STC_DRAFT;
		$this->Total = $this->Volume * $this->UnitPrice;
		switch($this->ComMode) {
			case 0: $this->TotalCom = round($this->Total * $this->ComMWC / 100, 2); break;
			case 1: $this->TotalCom = $this->ComMWC; break;
			case 2: $this->TotalCom = round($this->Volume * $this->ComMWC, 2); break;
		}
		
		if(! $this->Reference) $this->Reference = $this->getReference('CA');
		if($id) {
			$shs = $this->getChilds('Shipment');
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",$shs);
			foreach($shs as $sh) {
				if($sh->StatusId >= SHP_PLANNED && $sh->StatusId < SHP_CANCELLED) $vol += $sh->Volume;
			}
			$this->Delivered = $vol;
		}
		$this->Remains = $this->Volume - $this->Delivered;
		if($this->Remains <= 0 && $this->Status < STC_COMPLETED) {
			$this->Status = STC_COMPLETED;
			//AlertUser::addAlert('CONTRACT COMPLETED '.$this->Reference,'CA'.$this->Id,'Murphy','Contract',$this->Id,array($this->BrokerId),null,'icon_contract');
		}
		$this->Unit = $this->Quantity;
		$buy = $this->getParents('Third.ContractBuyerId');
		$buy = $buy[0];
		$sup = $this->getParents('Third.ContractSupplierId');
		$sup = $sup[0];
		$this->SupplierCompany = $sup ? $sup->Company : '';
		$this->BuyerCompany = $buy ? $buy->Company : '';
		genericClass::Save();
		if(! $mode) {
			if($this->BrokerId) $usr = array($this->BrokerId);
			else $rol = 'MWC_BROKER';
			AlertUser::addAlert('CONTRACT '.$this->Reference.' - '.$this->BuyerCompany.'/'.$this->SupplierCompany,'CA'.$this->Id,'Murphy','Contract',$this->Id,$usr,$rol,'icon_contract');
		}
		$res = array('Reference'=>$this->Reference,'Delivered'=>$this->Delivered,'Remains'=>$this->Remains,'Total'=>$this->Total,'TotalCom'=>$this->TotalCom,'BuyerCompany'=>BuyerCompany,'SupplierCompany'=>SupplierCompany);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res)));
	}

	private function getReference($key) {
		$rec = Sys::$Modules['Murphy']->callData('Contract:NOVIEW/Reference~'.$key, false, 0, 1, 'DESC', 'Reference', 'Reference');
		Sys::$Modules['Murphy']->Db->clearLiteCache();
		if(is_array($rec) && count($rec)) {
			$a = substr($rec[0]['Reference'], 2);
			return $key.sprintf('%06d', $a + 1);
		}
		return $key.'000001';
	}

	function Verify() {
		$ret = genericClass::Verify();
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx:$ret",$this);
		if(! $ret) return false;
		return true;
	}


	
	function ValidateContract() {
		if($this->Status != STC_DRAFT) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->Status = STC_VALIDATED;
		$this->Save();
		$t = $this->getParents('Third.ContractBuyerId');
		$t = $t[0];
		$t->SendMail('Contract confirmation', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract');
		$t = $this->getParents('Third.ContractSupplierId');
		$t = $t[0];
		$t->SendMail('Contract confirmation', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract');
		$res = array('Status'=>$this->Status);
		return WebService::WSStatus('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res));
	}

	function SendToBuyer() {
		if($this->BuyerStatus) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->BuyerStatus = STC_SENT;
		$this->checkStatus();
		$this->Save();
		$doc = $this->printDocument();
		$t = $this->getParents('Third.ContractBuyerId');
		$t = $t[0];
		$t->SendMail('Contract confirmation', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract', true, $doc);
		$res = array('Status'=>$this->Status,'BuyerStatus'=>$this->BuyerStatus);
		return WebService::WSStatus('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res));
	}


	function SendToSupplier() {
		if($this->SupplierStatus) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->SupplierStatus = STC_SENT;
		$this->checkStatus();
		$this->Save();
		$doc = $this->printDocument();
		$t = $this->getParents('Third.ContractSupplierId');
		$t = $t[0];
		$t->SendMail('Contract confirmation', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract', true, $doc);
		$res = array('Status'=>$this->Status,'SupplierStatus'=>$this->SupplierStatus);
		return WebService::WSStatus('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res));
	}

	private function checkStatus() {
		if($this->Status == STC_DRAFT) {
			if($this->BuyerStatus == STC_SENT && $this->SupplierStatus == STC_SENT) $this->Status = STC_VALIDATED;
		}
		if($this->Status <= STC_VALIDATED) {
			if($this->BuyerStatus == STC_REJECTED || $this->SupplierStatus == STC_REJECTED) {
				$this->Status = STC_CANCELLED;
			}
			elseif ($this->BuyerStatus == STC_ACCEPTED && $this->SupplierStatus == STC_ACCEPTED) {
				$this->Status = STC_CONFIRMED;
				if($e->Status == STE_HAGGLING) {
					$e = $this->getparents('Enquiry.ContractEnquiryId');
					$e = $e[0];
					$e->Status = STE_CONFIRMED;
					$e->Save();
					return array('edit', 1, $this->Id, 'Murphy', 'Enquiry', '', '', null, null);
				}
			}
		}
		return null;
	}
	
	function BuyerAcceptance() {
		if($this->BuyerStatus > STC_SENT) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->BuyerStatus = STC_ACCEPTED;
		$sts = $this->checkStatus();
		$this->Save();
		$sup = $this->getParents('Third.ContractBuyerId');
		$sup = $sup[0];
		AlertUser::addAlert('Buyer accepted contract '.$this->Reference.' - '.$sup->Company,'CA'.$this->Id,'Murphy','Contract',$this->Id,array($this->BrokerId),null,'icon_contract');
		$t = $this->getParents('Third.ContractSupplierId');
		$t = $t[0];
		$t->SendMail('Contract accepted by buyer', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract');
		$res = array('Status'=>$this->Status,'BuyerStatus'=>$this->BuyerStatus);
		$ret = array(array('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res)));
		if($sts) $ret[] = $sts;
		return WebService::WSStatusMulti($ret);
	}

	function BuyerRefusal() {
		if($this->BuyerStatus > STC_SENT) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->BuyerStatus = STC_REJECTED;
		$sts = $this->checkStatus();
		$this->Save();
		$sup = $this->getParents('Third.ProposalBuyerId');
		$sup = $sup[0];
		AlertUser::addAlert('Buyer refused contract '.$this->Reference.' - '.$sup->Company,'CA'.$this->Id,'Murphy','Contract',$this->Id,array($this->BrokerId),null,'icon_contract');
		$t = $this->getParents('Third.ContractSupplierId');
		$t = $t[0];
//		$t->SendMail('Contract refused by buyer', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract');
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Contract refused by buyer '.$this->Reference.' - '.$this->BuyerCompany.'/'.$this->SupplierCompany,'CA'.$this->Id,'Murphy','Contract',$this->Id,$usr,$rol,'icon_contract');

		$res = array('Status'=>$this->Status,'BuyerStatus'=>$this->BuyerStatus);
		$ret = array(array('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res)));
		if($sts) $ret[] = $sts;
		return WebService::WSStatusMulti($ret);
	}

	function SupplierAcceptance() {
		if($this->SupplierStatus > STC_SENT) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->printDocument();
		$this->SupplierStatus = STC_ACCEPTED;
		$sts = $this->checkStatus();
		$this->Save();
		$sup = $this->getParents('Third.ContractSupplierId');
		$sup = $sup[0];
		AlertUser::addAlert('Supplier accepted contract '.$this->Reference.' - '.$sup->Company,'CA'.$this->Id,'Murphy','Contract',$this->Id,array($this->BrokerId),null,'icon_contract');
		$t = $this->getParents('Third.ContractBuyerId');
		$t = $t[0];
		$t->SendMail('Contract accepted by supplier', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract');
		$res = array('Status'=>$this->Status,'SupplierStatus'=>$this->SupplierStatus);
		$ret = array(array('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res)));
		if($sts) $ret[] = $sts;
		return WebService::WSStatusMulti($ret);
	}

	function SupplierRefusal() {
		if($this->SupplierStatus > STC_SENT) return WebService::WSStatus('edit', 1, '', '', '', '', '', null, null);
		$this->printDocument();
		$this->SupplierStatus = STC_REJECTED;
		$sts = $this->checkStatus();
		$this->Save();
		$sup = $this->getParents('Third.ContractSupplierId');
		$sup = $sup[0];
		AlertUser::addAlert('Supplier refused contract '.$this->Reference.' - '.$sup->Company,'CA'.$this->Id,'Murphy','Contract',$this->Id,array($this->BrokerId),null,'icon_contract');
		$t = $this->getParents('Third.ContractBuyerId');
		$t = $t[0];
//		$t->SendMail('Contract refused by supplier', 'returnUrl='.urlencode('Murphy/Contract/'.$this->Id), 'Contract');
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Contract refused by supplier '.$this->Reference.' - '.$this->BuyerCompany.'/'.$this->SupplierCompany,'CA'.$this->Id,'Murphy','Contract',$this->Id,$usr,$rol,'icon_contract');

		$res = array('Status'=>$this->Status,'SupplierStatus'=>$this->SupplierStatus);
		$ret = array(array('edit', 1, $this->Id, 'Murphy', 'Contract', '', '', null, array('dataValues'=>$res)));
		if($sts) $ret[] = $sts;
		return WebService::WSStatusMulti($ret);
	}
	
	function PrintContract() {
		$file = $this->printDocument();
		$res = array('printFiles'=>array($file.'?'.microtime(true)));
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	private function printDocument() {
		require_once('ContractPrint.class.php');
		$title = 'Contract_'.str_replace('/', '_', $this->Reference);
		$file = "Home/Murphy/$title.pdf";
		$pdf = new ContractPrint($this,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle($title);
		$pdf->AddPage();
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}
	
	function createAlerts($time) {
		$b = strtotime(date('Ymd',$time));
		$e = strtotime('+1 day', $b);
		$rec = Sys::$Modules['Murphy']->callData("Contract/Alert>=$b&Alert<$e&StatusId<".STC_COMPLETED);
		if(! is_array($rec) || ! count($rec)) return;
		foreach($rec as $rc) {
			$ctr = genericClass::createInstance('Murphy', $rc);
			$txt = 'CONTRACT '.$ctr->Reference.'  '.$ctr->AlertMessage;
			$tag = 'CA'.$ctr->Id;
			AlertUser::addAlert($txt,$tag,'Murphy','Contract',$t->Id,array($ctr->BrokerId),null,'icon_contract');
		}
	}

	// sample request
	public function NewSampleRequest() {
		$buy = $this->getParents('Third.ContractBuyerId');
		$buy = $buy[0]->Id;
		$sup = $this->getParents('Third.ContractSupplierId');
		$sup = $sup[0];
		$cnt = genericClass::createInstance('Murphy', 'Country');
		$cnt->initFromId($sup->Country);
		$cty = genericClass::createInstance('Murphy', 'Country');
		$cty->initFromId($this->CountryWine);
		$var = genericClass::createInstance('Murphy', 'Varietal');
		$var->initFromId($this->Varietal);
		$col = genericClass::createInstance('Murphy', 'Colour');
		$col->initFromId($this->Colour);
		$app = genericClass::createInstance('Murphy', 'Appellation');
		$app->initFromId($this->Appellation);
		$flt = genericClass::createInstance('Murphy', 'Filtration');
		$flt->initFromId($this->Filtration);
		$item = array('Id'=>1,'Acronym'=>$cty->Acronym,'Varietal'=>$var->Varietal,'ColourIcon'=>$col->Icon,'Appellation'=>$app->Appellation,
					'Vintage'=>$this->Vintage,'Filtration'=>$flt->Filtration,'Bottles'=>1,
					'CountryId'=>$this->CountryWine,'VarietalId'=>$this->Varietal,'ColourId'=>$this->Colour,'AppellationId'=>$this->Appellation,
					'FiltrationId'=>$this->Filtration);
		$supp = array('Id'=>$sup->Id,'Company'=>$sup->Company,'Town'=>$sup->Town,'Acronym'=>$cnt->Acronym);
		$data = array('SampleRequestContractId'=>$this->Id,'SampleRequestBuyerId'=>$buy,
					'CountryId'=>$this->CountryWine,'VarietalId'=>$this->Varietal,'ColourId'=>$this->Colour,
					'AppellationId'=>$this->Appellation,'Vintage'=>$this->Vintage,'FiltrationId'=>$this->Filtration,
					'samples'=>array($item),'suppliers'=>array($supp));
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	public function CreateSampleRequest($ref='',$date=0,$bottles=1) {
		if(!$date) $date = time();
		$buy = $this->getParents('Third.ContractBuyerId');
		$sup = $this->getParents('Third.ContractSupplierId');
		$spr = genericClass::createInstance('Murphy', 'SampleRequest');
		$spr->addParent($this);
		$spr->appParent($buy[0]);
		$spr->Date = time();
		$spr->Alert = null;
		$spr->Status = SPR_DRAFT;
		$spr->Purpose = 1;
		$spr->BuyerRef = $ref;
		$spr->DeadLine = $date;
		$spr->Save();
		$spl = genericClass::createInstance('Murphy', 'Sample');
		$spl->addParent($spr);
		$spl->addParent($sup[0]);
		$spl->Status = SPL_DRAFT;
		$spl->CountryWine = $this->CountryWine;
		$spl->Varietal = $this->Varietal;
		$spl->Colour = $this->Colour;
		$spl->Appellation = $this->Appellation;
		$spl->Vintage = $this->Vintage;
		$spl->Filtration = $this->Filtration;
		$spl->Bottles = $bottles;
		$spl->Save();
		$sts = array(array('add', 1, $spr->Id, 'Murphy', 'SampleRequest', '', '', null, null),
					array('add', 1, $spl->Id, 'Murphy', 'Sample', '', '', null, null));
		return WebService::WSStatusMulti($sts);
	}
	/**
	 * getMailList
	 * Recupere la liste des mails pour un tiers tout contact confondus
	 * Permet la négociation en liste bufferisée avec les paramètres standards getData.
	 * @return Array[Array[String]]
	 */
	function GetMailList($id, $offset, $limit, $sort, $order, $filter, $thirdId) {
		//Requete
		$req = 'Contract/'.$thirdId.'/MailContact';
		$items = Sys::$Modules['Murphy']->callData($req, false, $offset, $limit, $order, $ord);
		$c = sizeof($items);
		return WebService::WSData('',0,$c,$c,$req,'','','','',(!$c)?Array():$items);
	}

}
