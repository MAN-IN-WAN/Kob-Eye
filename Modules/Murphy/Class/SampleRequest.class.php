<?php
class SampleRequest extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		$id = $this->Id;
		if(! $this->Reference) $this->Reference = $this->getReference();
		if(! $this->Status) $this->Status = SPR_OPEN;
		$buy = $this->GetParents('Third.SampleRequestBuyerId');
		if($buy[0]) $this->BuyerCompany = $buy[0]->Company;
		genericClass::Save();
		if(!$id) {
			$txt = 'NEW SAMPLE REQUEST ';
			$par = $this->getParents('Enquiry.SampleRequestEnquiryId');
			if(count($par)) {
				$txt .= 'on Enquiry '.$par[0]->Reference;
				//$buy = $par[0]->getParents('Third.EnquiryBuyerId');
			}
			else {
				$par = $this->getParents('Proposal.SampleRequestProposalId');
				if(count($par)) {
					$enq = $par[0]->getParents('Enquiry.ProposalEnquiryId');
					$txt .= 'on Proposal '.$par[0]->Reference;
					//if(count($enq)) $buy = $enq[0]->getParents('Third.EnquiryBuyerId');
				}
				else {
					$par = $this->getParents('Contract.SampleRequestContractId');
					$txt .= 'on Contract '.$par[0]->Reference;
					//if(count($par)) $buy = $par[0]->getParents('Third.ContractBuyerId');
				}
			}
			if(count($buy)) $txt .= ' - '.$buy[0]->Company;
			$rol = 'MWC_BROKER';
			AlertUser::addAlert($txt,'SR'.$this->Id,'Murphy','SampleRequest',$this->Id,$usr,$rol,'');
		}
		$res = array('Reference'=>$this->Reference,'BuyerCompany'=>BuyerCompany);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'SampleRequest', '', '', null, array('dataValues'=>$res)));
	}

	private function getReference() {
		$rec = Sys::$Modules['Murphy']->callData('SampleRequest', false, 0, 1, 'DESC', 'Reference', 'Reference');
		Sys::$Modules['Murphy']->Db->clearLiteCache();
		if(is_array($rec) && count($rec)) {
			$a = $rec[0]['Reference'];				
			return $key.sprintf('%06d', $a + 1);
		}
		return '000001';
	}
	
	public function saveSampleRequest($args) {
		$msg = array();
		if(! $args->SampleRequestBuyerId[0]) $msg[] = array('message'=>'Buyer is missing');
		if(! is_array($args->samples) || ! count($args->samples)) $msg[] = array('message'=>'No selected wine');
		if(! is_array($args->suppliers) || ! count($args->suppliers)) $msg[] = array('message'=>'No selected supplier');
		if(count($msg)) return WebService::WSStatus('method', 0, '', '', '', '', '', $msg, null);
		$this->addParent('Murphy/Third/'.$args->SampleRequestBuyerId[0]);
		if($args->SampleRequestEnquiryId) $this->addParent('Murphy/Enquiry/'.$args->SampleRequestEnquiryId[0]);
		if($args->SampleRequestProposalId) $this->addParent('Murphy/Proposal/'.$args->SampleRequestProposalId[0]);
		if($args->SampleRequestContractId) $this->addParent('Murphy/Contract/'.$args->SampleRequestContractId[0]);
		$this->Status = $args->Status;
		$this->Date = $args->Date;
		$this->BuyerRef = $args->BuyerRef;
		$this->DeadLine = $args->DeadLine;
		$this->Alert = $args->Alert;
		$this->Purpose = $args->Purpose;
		$this->Delivery = $args->Delivery;
		$this->Save();
		$sts = array();
		$sts[] = array('add', 1, $this->Id, 'Murphy', 'SampleRequest', '', '', null, null);
		foreach($args->suppliers as $sup) {
			foreach($args->samples as $spl) {
				$smp = genericClass::createInstance('Murphy', 'Sample');
				$smp->addParent($this);
				$smp->addParent('Murphy/Third/'.$sup->Id);
				$smp->BuyerCompany = $this->BuyerCompany;
				$smp->Status = SPL_SUP_RESP;
				$smp->Bottles = $spl->Bottles;
				$smp->CountryWine = $spl->CountryId;
				$smp->Varietal = $spl->VarietalId;
				$smp->Colour = $spl->ColourId;
				$smp->Vintage = $spl->Vintage;
				$smp->Filtration = $spl->FiltrationId;
				$smp->Save();
				$sts[] = array('add', 1, $smp->Id, 'Murphy', 'Sample', '', '', null, null);
			}
		}
		return WebService::WSStatusMulti($sts);
	}


	public function PrintRequest() {
		$file = $this->printDocument();
		$res = array('printFiles'=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	private function printDocument() {
		require_once('SamplePrint.class.php');
		$lines = Sys::$Modules['Murphy']->callData('SampleRequest/'.$this->Id.'/Sample', false, 0, 999, 'ASC', 'Id');
		$title = 'Sample_'.str_replace(' ', '_', $this->Reference);
		$file = "Home/Murphy/$title.pdf";
		$pdf = new SamplePrint($this, 'L', 'mm', 'A4');
		$pdf->SetAuthor("Murphy");
		$pdf->SetTitle($title);
		$pdf->AddPage();
		$pdf->PrintLines($lines);
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}

}
