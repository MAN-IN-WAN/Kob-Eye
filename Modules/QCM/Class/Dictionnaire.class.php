<?php
class Dictionnaire extends genericClass {

	function Save(){
		genericClass::Save();
		$key = 'Entree.DictionnaireEntreeId';
		$this->saveEntrees($this->$key);
	}

	private function saveEntrees($descr) {
		if(! $descr) return;
		$ord = 0;
		$old = $this->getChilds('Entree');
		foreach($descr as $desc) {
			$id = $desc->Id;
			$d = genericClass::createInstance('QCM','Entree');
			$d->addParent($this);
			$d->Id = $id;
			$d->Reponse = $desc->Reponse;
			$d->BonneReponse = $desc->BonneReponse;
			$d->Ordre = $ord++;
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

	function Delete() {
		$des = $this->getChilds('Entree');
		foreach($des as $d) $d->Delete();
		return genericClass::Delete();
	}

}
