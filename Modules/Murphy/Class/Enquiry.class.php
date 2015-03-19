<?php
class Enquiry extends genericClass {

	
	
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	



	
	function Save() {
		$id = $this->Id;
		if(! $this->Status) $this->Status = STE_DRAFT;
		if(! $this->Date) $this->Date = time();
		if($this->Quantity) {
			$q = genericClass::createInstance('Murphy', 'Quantity');
			$q->initFromId($this->Quantity);
			$this->Litres = $this->Volume * $q->Coefficient;
		}
		$buy = $this->getParents('Third.EnquiryBuyerId');
		$buy = $buy[0];
		if(! $this->Reference) {
			$this->Reference = $this->getReference('E'.$buy->Reference.'-'.date('ym', $this->Date));
			if(! $this->ContactId) $this->ContactId = $buy->ContactId;
			if(! $this->BrokerId) $this->BrokerId = $buy->BrokerId;
		}
		$this->BuyerCompany = $buy->Company;
		$this->Unit = $this->Quantity;
		genericClass::Save();
		if(! $id) {
			//creation de l'alerte de creation
			if($this->BrokerId) $usr = array($this->BrokerId);
			else $rol = 'MWC_BROKER';
			AlertUser::addAlert('NEW INQUIRY '.$this->Reference.' - '.$buy->Company,'EN'.$this->Id,'Murphy','Enquiry',$this->Id,$usr,$rol,'icon_enquiry');
			//creation de la tache de rappel en deadline
			if($this->BrokerId){
				 $rol = null;
				$usr = $this->BrokerId;
			}else{
				 $rol = 'MWC_BROKER';
				$usr = null;
			}
			AlertTask::addTask("ENQUIRY (".$this->Reference."): Deadline in 2 days !","Please check this one before the end of the offer.",$this->OfferDeadline,$this->OfferDeadline,$this->Module,$this->ObjectType,$this->Id,$usr,$rol,172800);
		}
		$res = array('Reference'=>$this->Reference,'BuyerCompany'=>$this->BuyerCompany,'Litres'=>$this->Litres,'BrokerId'=>$this->BokerId,'ContactId'=>$this->ContactId,'BuyerCompany'=>BuyerCompany);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Enquiry', '', '', array(), array('dataValues'=>$res)));
	}


	function Verify() {
		$ret = genericClass::Verify();
		if(! $ret) return false;
		if($this->Status > STE_DRAFT && $this->TotalCom <= 0) {
			$this->addError(array('Message'=>'Commission is empty', 'Prop'=>'TotalCom'));
			return false;
		}
		return true;
	}

	
	private function getReference($key) {
		$rec = Sys::$Modules['Murphy']->callData('Enquiry/Reference~'.$key, false, 0, 1, 'DESC', 'Reference', 'Reference');
		Sys::$Modules['Murphy']->Db->clearLiteCache();
		if(is_array($rec) && count($rec)) {
			$a = explode('-', $rec[0]['Reference']);				
			return $key.sprintf('-%02d', $a[2] + 1);
		}
		return $key.'-01';
	}
	
	public function GetSuppliers($id,$offset,$limit,$sortfld,$order,$filter,$flt,$ctw,$var,$col,$app,$vin,$sal,$ctr) {
		if($flt) {
			if($ctw) $sel = "p.CountryWine=$ctw ";
			if($var) $sel .= ($sel ? 'and ' : '') . "p.Varietal=$var ";
			if($col) $sel .= ($sel ? 'and ' : '') . "p.Colour=$col ";
			if($app) $sel .= ($sel ? 'and ' : '') . "p.Appellation='$app' ";
			if($vin) $sel .= ($sel ? 'and ' : '') . "p.Vintage='$vin' ";
			if($sel) $prod = true;
		}
		if($sal) $sel .= ($sel ? 'and ' : '') . "t.Company like '%".addslashes($sal)."%' ";
		if($ctr) $sel .= ($sel ? 'and ' : '') . "c.Country like '%".addslashes($ctr)."%' ";
		$sel .= ($sel ? 'and ' : '') . 'Supplier=1';

		if($prod) 
			$sql = "from `##_Murphy-Product` p
					left join `##_Murphy-SupplierProduct` s on s.SupplierProductProductId=p.Id
					left join `##_Murphy-Third` t on t.Id=s.SupplierProductSupplierId
					left join `##_Murphy-Country` c on c.Id=t.Country
					left join `##_Murphy-Contact` o on o.Id=t.ContactId
					where $sel order by t.Company";
		else
			$sql = "from `##_Murphy-Third` t
					left join `##_Murphy-Country` c on c.Id=t.Country
					left join `##_Murphy-Contact` o on o.Id=t.ContactId
					where $sel order by t.Company";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$GLOBALS["Systeme"]->ConnectSql();
		$tmp = "select COUNT(*) ".$sql;
		$pdo = $GLOBALS['Systeme']->Db[0]->query($tmp);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$cnt = $rec[0]['COUNT(*)'];

		if($offset || $limit) $sql .= " limit $offset,$limit";
		$tmp = "select distinct t.Id,t.Company,t.Town,c.Country,o.FullName,o.Email ".$sql;
		$pdo = $GLOBALS['Systeme']->Db[0]->query($tmp);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$items = array();
		foreach($rec as $rc) {
			$m = $rc['Email'] != '' ? 1 : 0;
			$items[] = array('Id'=>$rc['Id'],'Company'=>$rc['Company'],'Town'=>$rc['Town'],
					'Country'=>$rc['Country'],'Contact'=>$rc['FullName'],'Mail'=>$m,'ShowBuyer'=>0);
		}
		$c = count($items);
		return WebService::WSData('',$offset,$c,$cnt,'','','','','',$items);
	}	


	public function SupplierProposal($items) {
		if(! $this->Verify()) return WebService::WSStatus('edit',0,'','','','','',$this->Error,null);
		$sts = array();
		$ok = false;
		foreach($items as $item) {
			$id = $item->Id;
			// third
			$s = genericClass::createInstance('Murphy', 'Third');
			$s->initFromId($id);
			$cry = genericClass::createInstance('Murphy', 'Country');
			$cry->initFromId($s->Country);
			
			// proposal
			$p = genericClass::createInstance('Murphy', 'Proposal');
			$p->Status = STP_DRAFT;
			$p->addParent($this, 'ProposalEnquiryId');
			$p->addParent($s, 'ProposalSupplierId');
			$buy = $this->getParents('Third.EnquiryBuyerId');
			if(count($buy)) $p->addParent($buy[0], 'ProposalBuyerId');
			$p->ContactId = $s->ContactId;
			$p->Date = time();
			$p->SupplierDate = null;
			$p->BuyerDate = null;
			$p->ValidateDate = null;
			$p->BrokerId = $this->BrokerId;
			$p->CountryWine = $this->CountryWine; 
			$p->Varietal = $this->Varietal;
			$p->Colour = $this->Colour;
			$p->Appellation = $this->Appellation;
			$p->Vintage = $this->Vintage; 
			$p->Filtration = $this->Filtration; 
			$p->Quantity = $this->Quantity; 
			$p->Volume = $this->Volume;
// TODO copy or not
//			$p->UnitPrice = $this->UnitPrice;
			$p->Currency = $this->Currency ? $this->Currency : $cry->Currency;
			$p->Unit = $this->Unit;
			$p->Payment = $this->Payment;
			$p->ShippingDate = $this->ShippingDate;
			$p->Transportation = $this->Transportation;
			$p->Inco = $this->Inco;
			$p->ShowBuyer = $item->ShowBuyer;
			$p->Save();
			$sts[] = array('add', 1, $p->Id, 'Murphy', 'Proposal', '', '', null, null);
			try {
				$s->SendMail('Offer request', 'returnUrl='.urlencode('Proposals/'.$p->Id), 'Offer');
			} catch(Exception $e) {}
			$ok = true;
		}
		if($this->Status < STE_ANSWERED) {
			$this->Status = STE_HAGGLING;
			$this->Save();
			$res = array('dataValues'=>array('Stauts'=>$this->Status));
			$sts[] = array('edit', 1, $this->Id, 'Murphy', 'Enquiry', '', '', null, $res);
		} 
		return WebService::WSStatusMulti($sts);
	}
	
	public function GetProposals() {
		$rec = Sys::$Modules['Murphy']->callData('Varietal/Id='.$this->Varietal, false, 0, 1);
		if(is_array($rec) && count($rec)) $var = $rec[0]['Varietal']; 
		$rec = Sys::$Modules['Murphy']->callData('Appellation/Id='.$this->Appellation, false, 0, 1);
		if(is_array($rec) && count($rec)) $app = $rec[0]['Appellation']; 
		$rec = Sys::$Modules['Murphy']->callData('Quantity/Id='.$this->Quantity, false, 0, 1);
		if(is_array($rec) && count($rec)) $qty = $rec[0]['Quantity']; 
		$rec = Sys::$Modules['Murphy']->callData('Filtration/Id='.$this->Filtration, false, 0, 1);
		if(is_array($rec) && count($rec)) $flt = $rec[0]['Filtration'];
		$items = array(); 
		$rec = $this->getChilds('Proposal');
		foreach($rec as $rc) {
			$items[] = array('Id'=>$rc->Id,'Name'=>$rc->Name,'Town'=>$rc->Town,'Country'=>$rc->Country,
				'Varietal'=>$rc->Varietal,'Appellation'=>$rc->Appellation,'Vintage'=>$rc->Vintage,
				'Quantity'=>$rc->Quantity,'Filtration'=>$rc->Filtration,'Volume'=>$rc->Volume);
		}
		$data = array('Reference'=>$this->Reference,'Varietal'=>$var,'Appellation'=>$app,'Vintage'=>$this->Vintage,
					'Quantity'=>$qty,'Filtration'=>$flt,'Volume'=>$this->Volume,'proposal'=>$items);
		$res = array(dataValues=>$data);
		return WebService::WSStatus('edit',1,$this->Id,'Murphy','Enquiry','','',null,$res);
	}	

	public function BuyerOffer($proposals) {
		if(! is_array($proposals) || ! count($proposals))
			return WebService::WSStatus('add', 0, '', '', '', '', '', array(array('No proposal selected')), null);
		$o = genericClass::createInstance('Murphy', 'Offer');
		$rec = Sys::$Modules['Murphy']->callData('Offer/Reference~'.Utils::KEAddSlashes(array($this->Reference)), false, 0, 1, 'DESC', 'Reference');
		if(is_array($rec) && count($rec)) {
			$a = explode('/', $rec[0]['Reference']);				
			$o->Reference = $this->Reference.sprintf('/%02d', $a[1] + 1);
		}
		else $o->Reference = $this->Reference.'/01'; 
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx:$this->Reference", $a);	
		$o->addParent($this);
		$rec = $this->getParents('Third'); 
		$o->addParent($rec[0]);
		$o->SendDate = time(); 
		$o->Save();
		foreach($proposals as $id) {
			$p = genericClass::createInstance('Murphy', 'Proposal');
			$p->initFromId($id);
			$p->addParent($o);
			$p->Save();
		}
		$sts = 1;
		$err = null;
		try {
			$s->SendMail('Seller offer', 'offer='.$o->Id, 'Offer');
		} catch(Exception $e) {
			$sts = 0;
			$err = array(array('message'=>$e->getMessage()));
		}
		return WebService::WSStatus('add', $sts, '', 'Murphy', 'Offer', '', '', $err, null);
	}


	public function NewSampleRequest() {
		$buy = $this->getParents('Third.EnquiryBuyerId');
		$buy = $buy[0]->Id;
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
		$data = array('SampleRequestEnquiryId'=>$this->Id,'SampleRequestBuyerId'=>$buy,
					'CountryId'=>$this->CountryWine,'VarietalId'=>$this->Varietal,'ColourId'=>$this->Colour,
					'AppellationId'=>$this->Appellation,'Vintage'=>$this->Vintage,'FiltrationId'=>$this->Filtration,
					'samples'=>array($item));
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}


}
