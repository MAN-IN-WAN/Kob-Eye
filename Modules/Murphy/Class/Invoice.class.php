<?php
class Invoice extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		$id = $this->Id;
		if($id && $this->Paid) {
			$old = genericClass::createInstance('Murphy', 'Invoice');
			$old->initFromId($id);
			if(! $old->Paid) {
				$sup = $this->getParents('Third.InvoiceSupplierId');
				$rol = 'MWC_BROKER';
				AlertUser::addAlert('INVOICE PAYED '.$this->Reference.' - '.$sup[0]->Company,'IN'.$this->Id,'Murphy','Invoice',$this->Id,$usr,$rol,'');
			}
		}
		if($this->Valid && ! $this->Reference) $this->Reference = $this->getReference();
		genericClass::Save();
		if(! $id) {
			$sup = $this->getParents('Third.InvoiceSupplierId');
			$rol = 'MWC_BROKER';
			AlertUser::addAlert('INVOICE '.$this->Reference.' - '.$sup[0]->Company,'IN'.$this->Id,'Murphy','Invoice',$this->Id,$usr,$rol,'');
		}
		else {
			
		}

		$res = array('Reference'=>$this->Reference);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Invoice', '', '', null, array('dataValues'=>$res)));
	}

	private function getReference() {
		$acc = $this->getParents('Account');
		$pfx = $acc[0]->InvoicePrefix;
		$year = date('y', $this->Date) % 26;
		$pfx .= chr(65 + $year).date('m', $this->Date);
		$rec = Sys::$Modules['Murphy']->callData("Invoice/Reference~$pfx&Valid=1", false, 0, 1, 'DESC', 'Reference', 'Reference');
		Sys::$Modules['Murphy']->Db->clearLiteCache();
		if(is_array($rec) && count($rec)) {
			$num = substr($rec[0]['Reference'],4) ;
			return $pfx.sprintf('%04d', $num + 1);
		}
		return $pfx.'0001';
	}

	
	public function ValidateInvoice() {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxx:",$this);
		$this->Valid = 1;
		$this->Save();
		$this->printInvoice();
		$res = array('Reference'=>$this->Reference,'Valid'=>1);
		return WebService::WSStatus('edit', 1, $this->Id, 'Murphy', 'Invoice', '', '', null, array('dataValues'=>$res));
	}
	
	
	public function GetShipments($dat,$ctr,$sup,$buy) {
		$req = 'Shipment/(!StatusId='.SHP_LOADED.'+StatusId='.SHP_DELIVERED.'!)&LoadingDate=!&Invoice=0';
		if($dat) {
			if($dat[0]) $req .= '&LoadingDate>='.$dat[0];
			if($dat[1]) $req .= '&LoadingDate<='.$dat[1];
		}
		if($ctr) $req .= '&Contract~'.$ctr;
		if($sup) $req .= '&Supplier~%'.$sup;
		if($buy) $req .= '&Buyer~%'.$buy;
		$rec = Sys::$Modules['Murphy']->callData($req, false, 0, 999, 'ASC', 'ContractSupplierId');
		$items = array();
		foreach($rec as $rc) {
			$chk = $rc['Retained'] ? 0 : 1;
			$items[] = array('Id'=>$rc['Id'],'Contract'=>$rc['Contract'],'LoadingDate'=>$rc['LoadingDate'],'DeliveryDate'=>$rc['DeliveryDate'],
						'Volume'=>$rc['Volume'],'SupplierContract'=>$rc['SupplierContract'],'SupplierInvoice'=>$rc['SupplierInvoice'],
						'Supplier'=>$rc['Supplier'],'Buyer'=>$rc['Buyer'],'Varietal'=>$rc['Varietal'],'Retained'=>$rc['Retained'],
						'_selected'=>$chk);
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,'','','','','',$items);
	}
	
	function CreateInvoices($ids) {
		$items = array();
		foreach($ids as $id) {
			$shp = genericClass::createInstance('Murphy', 'Shipment');
			$shp->initFromId($id);
			$ctr = $shp->getParents('Contract.ShipmentContractId');
			$ctr = $ctr[0];
			$shp->Contract = $ctr;
			$acc = $ctr->getParents('Account.ContractAccountId');
			$shp->Account = $acc[0];
			if($ctr->BuyerBilled) $sup = $ctr->getParents('Third.ContractBuyerId');
			else $sup = $ctr->getParents('Third.ContractSupplierId');
			$shp->Supplier = $sup[0];
			$items[] = $shp;
		}
		usort($items, array('Invoice','sortShipment'));
		$inv = null;
		foreach($items as $shp) {
			$sup = $shp->Supplier->Id;
			$acc = $shp->Account->Id;
			$cur = $shp->Contract->Currency;
			$pay = $shp->Contract->Payment;
			if("$sup:$acc:$cur:$pay" != $rup) {
				if($inv) $this->invoiceFooter($inv, $tot);
				$rup = "$sup:$acc:$cur:$pay";
				$inv = $this->invoiceHeader($shp); //$sup, $acc, $cur, $pay);
				$tot = 0;
			}
			$shp->addParent($inv);
			$shp->Invoice = 1;
			$shp->Save(true);
			$tot += round($shp->Volume * $shp->Contract->UnitPrice * $shp->Contract->ComMWC / 100, 2);
		}
		if($inv) $this->invoiceFooter($inv, $tot);
		$st = array(array('edit', 1, '', 'Murphy', 'Shipment', '', '', null, null),
					array('edit', 1, '', 'Murphy', 'Invoice', '', '', null, null));
		return WebService::WSStatusMulti($st);
	}

	function sortShipment($a, $b) {
		return strcmp($a->Account->Id.':'.$a->Supplier->Id.':'.$a->Contract->Currency.':'.$a->Contract->Payement,
					  $b->Account->Id.':'.$b->Supplier->Id.':'.$b->Contract->Currency.':'.$b->Contract->Payement);
//		return strcmp($a->ContractAccountId.':'.$a->ContractSupplierId.':'.$a->Currency.':'.$a->Payement,
//					  $b->ContractAccountId.':'.$b->ContractSupplierId.':'.$b->Currency.':'.$b->Payement);
	}

	
	private function invoiceHeader($shp) {
		$sup = $shp->Supplier;
		$acc = $shp->Account;
		$inv = genericClass::createInstance('Murphy', 'Invoice');
		$inv->addParent($shp->Supplier);
		$inv->addParent($shp->Account);
		$inv->Date = time();
		if($acc->Country == $sup->Country) $inv->VATRate = $acc->VATRate;
		$inv->Currency = $shp->Contract->Currency;
		$pym = genericClass::createInstance('Murphy', 'PaymentTerm');
		$pym->initFromId($shp->Contract->Payment);
		$day = $pym->Duration;
		$inv->Payment = $shp->Contract->Payment;
		$inv->DueDate = strtotime("+$day days", $inv->Date);
		$inv->PaymentDate = null;
		$inv->Save();
		return $inv;
	}
/*
	private function invoiceHeader($sup,$acc,$cur,$pay) {
		$rec = Sys::$Modules['Murphy']->callData("Third/$sup", false, 0, 1);
		$sup = genericClass::createInstance('Murphy', $rec[0]);
		$rec = Sys::$Modules['Murphy']->callData("Account/$acc", false, 0, 1);
		$acc = genericClass::createInstance('Murphy', $rec[0]);
		$inv = genericClass::createInstance('Murphy', 'Invoice');
		$inv->addParent($sup);
		$inv->addParent($acc);
		$inv->Date = time();
		if($acc->Country == $sup->Country) $inv->VATRate = $acc->VATRate;
		$inv->Currency = $cur;
		$pym = genericClass::createInstance('Murphy', 'PaymentTerm');
		$pym->initFromId($pay);
		$day = $pym->Duration;
		$inv->Payment = $pay;
		$inv->DueDate = strtotime("+$day days", $inv->Date);
		$inv->PaymentDate = null;
		$inv->Save();
		return $inv;
	}
*/
	private function invoiceFooter($inv, $tot) {
		$inv->TotalTE = $tot;
		$inv->VATAmount = round($tot * $inv->VATRate / 100, 2);
		$inv->TotalTI = $tot + $inv->VATAmount;
		$inv->Valid = 1;
		$inv->Save();
		$inv->printInvoice();
	}


	function PrintInvoices($ids) {
		require_once ('Class/Lib/fpdf_merge.php');
		$pdf = array();
		if(! isset($ids)) $ids = array($this->Id);
		foreach($ids as $id) {
			$rec = Sys::$Modules['Murphy']->callData("Invoice/$id", false, 0, 1);
			if(! is_array($rec) || ! count($rec)) continue;
			$inv = genericClass::createInstance('Murphy', $rec[0]);
			if(! $inv->Valid) continue;
			$pdf[] = 'Home/Murphy/Invoice_'.str_replace(' ', '_', $inv->Reference).'.pdf';
			$inv->Printed = 1;
			$inv->Save();
//			$doc = genericClass::createInstance('Murphy', $rec[0]);
//			$pdf[] = $doc->printInvoice();
		}
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx:",$pdf);
		if(sizeof($pdf) > 0) {
			$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
			$merge = new FPDF_Merge();
			foreach($pdf as $doc) $merge->add($doc);
			$merge->output($file);
			$res = array('printFiles'=> array($file.'?'.microtime(true)));
			if($this->Id) $res['dataValues'] = array('Printed'=>1);
		}
		else $res = null;
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	private function printInvoice() {
		require_once ('InvoicePrint.class.php');
		$lines = Sys::$Modules['Murphy']->callData("Invoice/$this->Id/Shipment", false, 0, 999, 'ASC', 'Id');
		$title = 'Invoice_'.str_replace(' ', '_', $this->Reference);
		$file = "Home/Murphy/$title.pdf";
		$pdf = new InvoicePrint($this, 'L', 'mm', 'A4');
		$pdf->SetAuthor("Murphy");
		$pdf->SetTitle($title);
		$pdf->AddPage();
		$pdf->PrintLines($lines);
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}


	function SendInvoices($ids) {
		if(! isset($ids)) $ids = array($this->Id);
		foreach($ids as $id) {
			$rec = Sys::$Modules['Murphy']->callData("Invoice/$id", false, 0, 1);
			if(! is_array($rec) || ! count($rec)) continue;
			$inv = genericClass::createInstance('Murphy', $rec[0]);
			if(! $inv->Valid) continue;
			$doc = 'Home/Murphy/Invoice_'.str_replace(' ', '_', $inv->Reference).'.pdf';
			$sup = $inv->GetParents('Third.InvoiceSupplierId');
			$sup[0]->SendMail('New invoice', null, 'New invoice', false, $doc);
			$inv->Sent = 1;
			$inv->Save();
		}
		if($this->Id) $res = array('dataValues'=>array('Sent'=>1));
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	function InvoicePaid($paid, $date) {
		if($paid) {
			if(! $date) $date = time();
		}
		else $date = null;
		$res = array('dataValues'=>array('PaymentDate'=>$date));
		return WebService::WSStatus('method',1,'','','','','',null,$res);
	}
	
	function ComputeInvoice($tte,$vat) {
		$vam = round($tte * $vat / 100, 2);
		$res = array('dataValues'=>array('VATAmount'=>$vam,'TotalTI'=>$tte+$vam));
		return WebService::WSStatus('method',1,'','','','','',null,$res);
	}

	function SalesJournal($first,$last) {
		require_once('SalesJournal.class.php');
		$req = "Invoice/Date>=$first&Date<=$last";
		$lines = Sys::$Modules['Murphy']->callData($req,false,0,9999);
		if(is_array($lines) && count($lines)) {
			usort($lines, array('Invoice','sortJournal'));
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxx $req", $lines);
		
			$pdf = new SalesJournal($first,$last,'P','mm','A4');
			$pdf->SetAuthor('Appaloosa');
			$pdf->SetTitle('Sales Journal '.date('ymd ',$first).date('ymd',$last));
			
			$pdf->PrintLines($lines);
			// save pdf
			$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
			$pdf->Output($file);
			$pdf->Close();
			$res = array(printFiles=>array($file.'?'.microtime(true)));
		}
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	function sortJournal($a, $b) {
		return strcmp($a['Account'].':'.$a['Currency'].':'.$a['Reference'], $b['Account'].':'.$b['Currency'].':'.$b['Reference']);
	}


	function UnpaidInvoices($date) {
		require_once('UnpaidInvoices.class.php');
		$req = "Invoice/Paid=0&(!DueDate!!+DueDate<=$date!)";
		$lines = Sys::$Modules['Murphy']->callData($req,false,0,9999);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxx:$date", $lines);
		if(is_array($lines) && count($lines)) {
			usort($lines, array('Invoice','sortUnpaid'));
			
			$pdf = new UnpaidInvoices($date,'P','mm','A4');
			$pdf->SetAuthor('Appaloosa');
			$pdf->SetTitle('Sales Journal '.date('ymd ',$first).date('ymd',$last));
			
			$pdf->PrintLines($lines);
			// save pdf
			$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
			$pdf->Output($file);
			$pdf->Close();
			$res = array(printFiles=>array($file));
		}
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	function sortUnpaid($a, $b) {
		return strcmp($a['Account'].':'.$a['Currency'].':'.$a['DueDate'], $b['Account'].':'.$b['Currency'].':'.$b['DueDate']);
	}
	
}
