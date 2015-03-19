<?php
class Proposal extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	
	function Save() {
		$id = $this->Id;
		genericClass::Save();
		if(! $this->Status) $this->Status = STP_DRAF;
		$this->Total = $this->Volume * $this->UnitPrice;
		
		$enq = $this->getParents('Enquiry.ProposalEnquiryId');
		$enq = $enq[0];
		$sup = $this->getParents('Third.ProposalSupplierId');
		$sup = $sup[0];
		$buy = $this->getParents('Third.ProposalBuyerId');
		$buy = $buy[0];
		
		if(! $this->Reference) $this->Reference = $this->getReference('P'.substr($enq->Reference, 1));
		if(! $this->ContactId) $this->ContactId = $sup->ContactId;
		$this->Trucks = '';
		if($this->Quantity == 1) {
			$cry = genericClass::createInstance('Murphy', 'Country');
			$cry->initFromId($buy->Country);
			$trk = $cry->Trucks;
			$cry = genericClass::createInstance('Murphy', 'Country');
			$cry->initFromId($sup->Country);
			if($trk || $cry->Trucks) $this->Trucks = $this->Volume / 24000;
		}
		$this->Unit = $this->Quantity;
		$this->SupplierCompany = $sup ? $sup->Company : '';
		$this->BuyerCompany = $buy ? $buy->Company : '';
		genericClass::Save();
		if(! $id) {
			if($this->BrokerId) $usr = array($this->BrokerId);
			else $rol = 'MWC_BROKER';
			AlertUser::addAlert('NEW OFFER '.$this->Reference.' - '.$sup->Company,'PR'.$this->Id,'Murphy','Proposal',$this->Id,$usr,$rol,'icon_proposal');
		}
		$res = array('Reference'=>$this->Reference,'Total'=>$this->Total,'Status'=>$this->Status,'ContactId'=>$this->ContactId,'Trucks'=>$this->Trucks,'BuyerCompany'=>BuyerCompany,'SupplierCompany'=>SupplierCompany);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Proposal', '', '', array(), array('dataValues'=>$res)));
	}


	private function getReference($key) {
		$rec = Sys::$Modules['Murphy']->callData('Proposal/Reference~'.$key,false,0,1,'DESC','Reference','Reference');
		Sys::$Modules['Murphy']->Db->clearLiteCache();
		if(is_array($rec) && count($rec)) {
			$a = explode('-', $rec[0]['Reference']);
			return $key.sprintf('-%02d', $a[3] + 1);
		}
		return $key.'-01';
	}


	private function getTrucks() {
		$cry = genericClass::createInstance('Murphy', 'Country');
		$cry->initFromId($buy->Country);
		$trk = $cry->Trucks;
		$cry = genericClass::createInstance('Murphy', 'Country');
		$cry->initFromId($this->Country);
		if($this->Litres == $this->Volume && ($trk || $cry->Trucks)) {
			$trk = $this->Litres / 24000;
			$this->Trucks = $trk.($trk > 1 ? ' Trucks' : 'Truck');
		}
	}



	// supplier refusal
	function RefuseProposal($comments) {
		$this->Status = STP_REFUSED;
		$this->SupplierDate = time();
		$this->Comments = $comments;
		$this->Save();
		$sup = $this->getParents('Third.ProposalSupplierId');
		$sup = $sup[0];
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Supplier refused offer '.$this->Reference.' - '.$sup->Company,'PR'.$this->Id,'Murphy','Proposal',$this->Id,$usr,$rol,'icon_proposal');
		$res = array('Status'=>$this->Status,'Comments'=>$this->Comments);
		return WebService::WSStatus('edit', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}

	// supplier answer
	function AnswerProposal() {
		$this->Status = STP_ANSWERED;
		$this->SupplierDate = time();
		$this->Save();
		$sup = $this->getParents('Third.ProposalSupplierId');
		$sup = $sup[0];
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Supplier answered offer '.$this->Reference.' - '.$sup->Company,'PR'.$this->Id,'Murphy','Proposal',$this->Id,$usr,$rol,'icon_proposal');
		$res = array('Status'=>$this->Status,'SupplierDate'=>$this->SupplierDate);
		return WebService::WSStatus('edit', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}
	
	// mwc validation
	function ValidateProposal($showSupp) {
		$this->Status = STP_VALIDATED;
		$this->ValidateDate = time();
		$this->ShowSupplier = $showSupp;
		$this->Save();
		$e = $this->getParents('Enquiry.ProposalEnquiryId');
		$e = $e[0];
		$b = $e->getParents('Third.EnquiryBuyerId');
		$b = $b[0];
		try {
			$b->SendMail('Supplier offer', 'returnUrl='.urlencode('Enquiries/'.$e->Id), 'Offer', true, null, $e->ContactId);
		} catch(Exception $e) {}
		$res = array('Status'=>$this->Status,'ValidateDate'=>$this->ValidateDate,'ShowSupplier'=>$this->ShowSupplier);
		return WebService::WSStatus('edit', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}

	// buyer rejection
	function RejectProposal($comments) {
		$this->Status = STP_REJECTED;
		$this->BuyerDate = time();
		$this->Save();
		$sup = $this->getParents('Third.ProposalBuyerId');
		$sup = $sup[0];
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Buyer rejected offer '.$this->Reference.' - '.$sup->Company,'PR'.$this->Id,'Murphy','Proposal',$this->Id,$usr,$rol,'icon_proposal');
		$res = array('Status'=>$this->Status);
		return WebService::WSStatus('edit', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}

	// buyer revision
	function ReviseProposal($comments) {
		$this->Status = STP_REVISED;
		$this->BuyerDate = time();
		$this->BuyerComments = $comments;
		$this->Save();
		$s = $this->getParents('ProposalSupplierId');
		$s = $s[0];
//		$s->SendMail('Resquest for proposal', 'returnUrl='.urlencode('Murphy/Proposal/'.$p->Id.'/Edit'), 'Proposal');
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx:4");	
		$res = array('BuyerDate'=>$this->BuyerDate,'BuyerComments'=>$this->BuyerComments,'Status'=>$this->Status);
		return WebService::WSStatus('add', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}

	// buyer acceptance
	function AcceptProposal() {
		$this->Status = STP_ACCEPTED;
		$this->BuyerDate = time();
		$this->Save();
		$sup = $this->getParents('Third.ProposalBuyerId');
		$sup = $sup[0];
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Buyer accepted offer '.$this->Reference.' - '.$sup->Company,'PR'.$this->Id,'Murphy','Proposal',$this->Id,$usr,$rol,'icon_proposal');
		$res = array('Status'=>$this->Status,'BuyerDate'=>$this->BuyerDate);
		return WebService::WSStatus('edit', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}

	// buyer acceptance
	function AcceptWithApproval() {
		$this->Status = STP_APPROVAL;
		$this->BuyerDate = time();
		$this->Save();
		$sup = $this->getParents('Third.ProposalBuyerId');
		$sup = $sup[0];
		if($this->BrokerId) $usr = array($this->BrokerId);
		else $rol = 'MWC_BROKER';
		AlertUser::addAlert('Buyer accepted offer '.$this->Reference.' - '.$sup->Company,'PR'.$this->Id,'Murphy','Proposal',$this->Id,$usr,$rol,'icon_proposal');
		$res = array('Status'=>$this->Status,'BuyerDate'=>$this->BuyerDate);
		return WebService::WSStatus('edit', 1, '', 'Murphy', 'Proposal', '', '', null, array('dataValues'=>$res));
	}


	function CreateContract() {
		$ref = 'C'.substr($this->Reference, 1);
		$rec = Sys::$Modules['Murphy']->callData('Contract/Reference='.$ref, false, 0, 1, '', '', 'Id');
		if(is_array($rec) && count($rec)) {
			$err = array(array('message'=>"Contract $ref already exists"));
			$ret = array(array('method', 0, '', '', '', '', '', $err, null));
			return WebService::WSStatusMulti($ret);
		}
		$e = $this->getParents('Enquiry.ProposalEnquiryId');
		$e = $e[0];
		$s = $this->getParents('Third.ProposalSupplierId');
		$s = $s[0];
		$b = $e->getParents('Third.EnquiryBuyerId');
		$b = $b[0];
		$l = $e->getParents('Third.EnquiryBillingId');
		$c = genericClass::createInstance('Murphy', 'Contract');
		$c->addParent($e, 'ContractEnquiryId'); 
		$c->addParent($s, 'ContractSupplierId');
		$c->addParent($b, 'ContractBuyerId');
		if(is_array($l) && count($l)) $c->addParent($l[0], 'ContractBillingId');
		$c->Status = STC_DRAFT;
		$c->Reference = $ref;
		$c->BuyerRef = $e->BuyerRef;
		$c->SupplierRef = $this->SupplierRef;
		$c->BuyerContactId = $e->ContactId;
		$c->SupplierContactId = $this->ContactId;
		$c->BrokerId = $this->BrokerId;
		$c->Date = time(); 
		$c->Revised = null;
		$c->CountryWine = $this->CountryWine;
		$c->Varietal = $this->Varietal; 
		$c->Appellation = $this->Appellation; 
		$c->Colour = $this->Colour;
		$c->Vintage = $this->Vintage; 
		$c->Quantity = $this->Quantity; 
		$c->Filtration = $this->Filtration; 
		$c->SampleRef = $this->SampleRef;
		$c->Volume = $this->Volume;
		$c->EndDate = $this->EndDate;
		$c->UnitPrice = $this->UnitPrice;
		$c->Currency = $this->Currency;
		$c->Unit = $this->Unit;
		$c->Total = $this->Total;
		$c->Payment = $this->Payment;
		$c->ComMWC = $e->TotalCom;
		$c->BuyerBilled = $e->BuyerBilled;
		$c->ShippingDate = $e->ShippingDate;
		$c->Transportation = $e->Transportation;
		$c->Inco = $this->Inco;
		$c->Comments = $e->PublicNotes;
		if($this->Comments) {
			if($c->Comments) $c->Comments .= "\n";
			$c->Comments .= 'Supplier comments : '.$this->Comments;
		}
		if($this->BuyerComments) {
			if($c->Comments) $c->Comments .= "\n";
			$c->Comments .= 'Buyer comments : '.$this->BuyerComments;
		}
		if(! $c->Verify()) $ret = array(array('add', 0, $c->Id, 'Murphy', 'Contract', '', '', $c->Error, null));
		else {
			$c->Save();
			$ret = array(array('add', 1, $c->Id, 'Murphy', 'Contract', '', '', null, null));
		}
		return WebService::WSStatusMulti($ret);
	}
	
	// sample request
	public function NewSampleRequest() {
		$enq = $this->getParents('Enquiry');
		$enq = $enq[0];
		$buy = $enq->getParents('Third.EnquiryBuyerId');
		$buy = $buy[0]->Id;
		$sup = $this->getParents('Third.ProposalSupplierId');
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
		$data = array('SampleRequestProposalId'=>$this->Id,'SampleRequestBuyerId'=>$buy,
					'CountryId'=>$this->CountryWine,'VarietalId'=>$this->Varietal,'ColourId'=>$this->Colour,
					'AppellationId'=>$this->Appellation,'Vintage'=>$this->Vintage,'FiltrationId'=>$this->Filtration,
					'samples'=>array($item),'suppliers'=>array($supp));
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	public function CreateSampleRequest($ref='',$date=0,$bottles=1) {
		if(!$date) $date = time();
		$enq = $this->getParents('Enquiry.ProposalEnquiryId');
		$buy = $enq[0]->getParents('Third.EnquiryBuyerId');
		$sup = $this->getParents('Third.ProposalSupplierId');
		$spr = genericClass::createInstance('Murphy', 'SampleRequest');
		$spr->addParent($this);
		$spr->addParent($buy[0]);
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
	 * getClone
	 * Define an other proposal based on this one
	 */
	public function getClone($noreset = false) {
		$O = parent::getClone();
		//recupération de l'enquiry
		$Enq = $this->getParents('Enquiry.ProposalEnquiryId');
		$Enq = $Enq[0];
		$O->addParent($Enq);
		//définition de l'acheteur
		$Buyer = $this->getParents('Third.ProposalBuyerId');
		$Buyer = $Buyer[0];
		$O->addParent($Buyer,'ProposalBuyerId');
		//définition du vendeur
		$Supplier = $this->getParents('Third.ProposalSupplierId');
		$Supplier = $Supplier[0];
		$O->addParent($Supplier,'ProposalSupplierId');
		//definition du status
		$O->Reference = '';
		$O->Status = STP_DRAFT;
		$O->Save();
		return $O;
	}
}
