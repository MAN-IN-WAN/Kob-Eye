<?php
class Visite extends genericClass {

	function Save($mode = false) {
		$annee = Cadref::$Annee;
		if(!empty($this->Annee) && $this->Annee != $annee) {
			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
			return false;			
		}
		$id = $this->Id;
		if(! $id) { //if($mode) {
			$this->Annee = $annee;
			$this->Utilisateur = Sys::$User->Initiales;
		}
		$this->Attentes = Sys::getCount('Cadref','Visite/'.$this->Id.'/Reservation/Attente=1&Supprime=0');
		$this->Inscrits = Sys::getCount('Cadref','Visite/'.$this->Id.'/Reservation/Attente=0&Supprime=0');
		$this->Attachements = Sys::getCount('Cadref','Visite/'.$this->Id.'/Attachement');

		$ret = parent::Save();
		if(! $id) {
			$lx = Sys::getData('Cadref','Lieu/Type=R');
			foreach($lx as $l) {
				$d = genericClass::createInstance('Cadref', 'Depart');
				$d->addParent($this);
				$d->addParent($l);
				$d->Save();
			}
		}
		return $ret;
	}

	function Delete() {
		$res = $this->getChildren('Reservation');
		if(count($res)) {
			$this->addError(array("Message" => "Cette fiche ne peut être supprimée", "Prop" => ""));
			return false;
		}
		$rec = $this->getChildren('Lieu');
		foreach($rec as $r)
			$r->Delete();
		$rec = $this->getChildren('Enseignant');
		foreach($rec as $r)
			$r->Delete();
		
		return parent::Delete();
	}
	
	function GetFormInfo() {
		$t = array();
		$ens = Sys::getData('Cadref','Enseignant'); 
		foreach($ens as $e) $t[] = array('id'=>$e->Id, 'label'=>$e->Nom.' '.$e->Prenom);
		return array('Enseignants'=>$t);
	}

	function PrintVisite($obj) {
		$annee = Cadref::$Annee;
		$sql = "
select r.VisiteId, r.Prix+r.Assurance-r.Reduction as Montant, v.Visite, v.Libelle, v.DateVisite, e.Numero, e.Nom, e.Prenom, 
d.HeureDepart, l.Libelle as LibelleL, l.Lieu, r.Notes, e.Mail, e.Telephone1, e.Telephone2, r.Attente, r.Supprime,
r.DateAttente, r.DateSupprime
from `##_Cadref-Reservation` r
inner join `##_Cadref-Visite` v on v.Id=r.VisiteId
inner join `##_Cadref-Adherent` e on e.Id=r.AdherentId 
left join `##_Cadref-Depart` d on d.Id=r.DepartId
left join `##_Cadref-Lieu` l on l.Id=d.LieuId
";

		$id = $this->Id;
		if(!$id) {
			$debut = isset($obj['Debut']) ? $obj['Debut'] : '0';
			$fin = isset($obj['Fin']) ? $obj['Fin'] : '99999999999';
			if(isset($obj['Guide']) && $obj['Guide']) $mode = 0;
			elseif(isset($obj['Chauffeur']) && $obj['Chauffeur']) $mode = 1;
			else $mode = 2;

			$sql .= "where r.Annee=$annee and v.DateVisite>='$debut' and v.DateVisite<='$fin' ";
			if($mode == 1) $sql .= " and r.Supprime=0 and r.Attente=0 order by r.Visite, d.HeureDepart, e.Nom, e.Prenom";
			else $sql .= "order by r.Visite, r.Attente, r.DateAttente, r.Supprime, e.Nom, e.Prenom";

			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(! $pdo) return array('pdf'=>'', 'sql'=>$sql);
			
			$file = $this->imprimeVisite($pdo, $mode);
			return array('pdf'=>$file, 'sql'=>$sql);
		}
		else {
			if(!isset($obj['step'])) $obj['step'] = 0;
			switch($obj['step']) {
				case 0:
					return array(
						'step'=>1,
						'template'=>'printVisite',
						'callNext'=>array(
							'nom'=>'PrintVisite',
							'title'=>'Visite 2',
							'needConfirm'=>false
						)
					);
					break;
				case 1:
					if($obj['Print']['Guide']) $mode = 0;
					elseif($obj['Print']['Chauffeur']) $mode = 1;
					else $mode = 2; 
					
					$sql .= "where v.Id=$id ";
					if($mode == 1) $sql .= " and r.Supprime=0 and r.Attente=0 order by r.Visite, d.HeureDepart, e.Nom, e.Prenom";
					else $sql .= "order by r.Visite, r.Attente, r.DateAttente, r.Supprime, e.Nom, e.Prenom";

					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
					$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
					if(! $pdo) return array('pdf'=>'', 'sql'=>$sql);

					$file = $this->imprimeVisite($pdo, $mode);					
					return array(
						'step'=>2,
						'data'=>'<a id="displayVisite" href="'.$file.'" target="_blank" ng-click="visiteImpression(\''.$file.'\')">Visite imprimée</a>',
						'callBack'=>array(
							'nom'=>'displayVisite',
							'title'=>'Visite 3',
							'args'=>array()
						)
					);
					break;
			}
		}
	}
	
	private function imprimeVisite($pdo, $mode) {
		require_once ('PrintVisite.class.php');

		$pdf = new PrintVisite($mode);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT','Visites guidées'));

		$pdf->PrintLines($pdo);

		$file = 'Home/tmp/VisiteGuidee'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd() . '/' . $file);
		$pdf->Close();
		return $file;
	}

}


