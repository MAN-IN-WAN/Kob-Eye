<?php
class Shipment extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save($mode=false) {
		$id = $this->Id;
		if(! $this->Status) $this->Status = SHP_PLANNED;
		genericClass::Save();

		$key = 'Container.ContainerShipmentId';
		$this->saveContainer($this->$key);

		$ct = $this->getParents('Contract.ShipmentContractId');
		if(!count($ct)) return;
		$ct = $ct[0];
		$ct->Save(true);
		if(! $mode) {
			$buy = $ct->getParents('Third.ContractBuyerId');
			$sup = $ct->getParents('Third.ContractSupplierId');
			if($ct->BrokerId) $usr = array($ct->BrokerId);
			else $rol = 'MWC_BROKER';
			AlertUser::addAlert('SHIPMENT '.$ct->Reference.' - '.$buy[0]->Company.'/'.$sup[0]->Company,'SH'.$this->Id,'Murphy','Shipment',$this->Id,$usr,$rol,'icon_shipment');
		}
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Shipment', '', '', null, null),
					array('edit', 1, $ct->Id, 'Murphy', 'Contract', '', '', null, null));
	}

	private function saveContainer($cont) {
		if(! $cont) return;
		$old = $this->getChilds('Container');
		foreach($cont as $c) {
			$id = $c->Id;
			$d = genericClass::createInstance('Murphy','Container');
			$d->addParent($this);
			$d->Id = $id;
			$d->ContainerNumber = $c->ContainerNumber;
			$d->Volume = $c->Volume;
			$d->Save();
			if($id) {
				foreach($old as $i=>$o) {
					if($o->Id == $id) {
						unset($old[$i]);
						break;
					}
				}
			}
		}
		foreach($old as $i=>$o) $o->Delete();
	}

	
	function Verify() {
		$ret = genericClass::Verify();
		if(! $ret) return false;
		return true;
	}
	
	function Delete() {
		$ct = $this->getParents('Contract.ShipmentContractId');
		$cont = $this->getChilds('Container');
		foreach($cont as $c) $c->Delete();
		$ret = genericClass::Delete();
		if(count($ct)) $ct[0]->Save();
		return $ret;
	}
	
	function GetCalendarList($id, $first, $last) {
		Connection::CloseSession();
		if($id) $flt = 'Id='.$id.'&';
		$flt .= 'LoadingDate>='.$first.'&LoadingDate<='.$last;
		$rec = Sys::$Modules['Murphy']->callData('Shipment/'.$flt, false, 0, 9999);
		$items = array();
		foreach($rec as $rc) {
			$evt = 'Contract : '.$rc['Contract']."\n";
			$evt .= 'P/O : '.$rc['PurchaseOrder']."\n";
			$evt .= 'Buyer : '.$rc['Buyer']."\nSupplier : ".$rc['Supplier']."\n";
			$evt .= 'Wine : '.$rc['Varietal']."\nVolume : ".$rc['Volume']."\n";
			$evt .= 'Loading : '.date('d/m/Y', $rc['LoadingDate'])."\nDelivery : ";
			$evt .= empty($rc['DeliveryDate']) ? "" : date('d/m/Y', $rc['DeliveryDate']);
			$evt .= "\nStatus : ".$rc['Status'];
			$items[] = array('Id'=>$rc['Id'],'label'=>$rc['Buyer'],'date'=>$rc['LoadingDate'],'event'=>$evt,'color'=>$rc['StatusColor'],'icon'=>$rc['StatusIcon']);
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,'','','','','',$items);
	}

}
