<?php
class Lot extends genericClass {
	
	function Save() {
		$id = $this->Id;
		if(! $id) {
			$an = date('y', $this->Date);
			$rec = Sys::getData('Cave', 'Lot/Lot~'.$an.'-', 0, 1, 'DESC', 'Lot', 'Lot');
			if(! count($rec)) $this->Lot = $an.'-0001';
			else $this->Lot = $an.sprintf('-%04d', substr($rec[0]->Lot, 3) + 1);
		}
		if(! $id) $this->EtatLotId = 1;
		parent::Save();
		$res = array('Lot'=>$this->Lot,'EtatLotId'=>$this->EtatLotId);
		$sts[] = array($id ? 'edit' : 'add', 1, $this->Id, 'Cave', 'Lot', '', '', null, array('dataValues'=>$res));
		return $sts;
	}
}
