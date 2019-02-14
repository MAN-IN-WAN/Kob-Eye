<?php
class Classe extends genericClass {
	
	function Save() {
		Cadref::$Annee;
		if(!empty($this->Annee) && $this->Annee = $annee) {
			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
			return false;			
		}
		$id = $this->Id;
		if(! $id) {
			$n = $this->getOneParent('Niveau');
			$this->Antenne = $n->Antenne;
			$this->Section = $n->Section;
			$this->Discipline = $n->Discipline;
			$this->Niveau = $n->Niveau;
			$p = $n->getOneParent('Antenne');
			$this->addParent($p);
			$p = $n->getOneParent('Section');
			$this->addParent($p);
			$p = $n->getOneParent('Discipline');
			$this->addParent($p);
			$this->CodeClasse = $this->Antenne.$this->Section.$this->Discipline.$this->Niveau.$this->Classe;
			$this->Annee = $annee;
		}
		$this->Attentes = Sys::getCount('Cadref','Classe/'.$this->Id.'/Inscription/Attente=1&Supprime=0');
		$this->Inscrits = Sys::getCount('Cadref','Classe/'.$this->Id.'/Inscription/Attente=0&Supprime=0');
		$this->Attachements = Sys::getCount('Cadref','Classe/'.$this->Id.'/Attachement');

		return parent::Save();
	}
	
	function Delete() {
		$rec = $this->getChildren('Inscription');
		if(count($rec)) throw new Exception('Cette classe ne peut être supprimée');

		return parent::Delete();
	}
	
	function GetFormInfo() {
		$a = $this->getOneParent('Antenne');
		$s = $this->getOneParent('Section');
		$d = $this->getOneParent('Discipline');
		$n = $this->getOneParent('Niveau');
		$l = $this->getOneParent('Lieu');
		return array('LibelleA'=>$a->Libelle, 'LibelleS'=>$s->Libelle, 'LibelleD'=>$d->Libelle, 'LibelleN'=>$n->Libelle, 'LibelleL'=>$l ? $l->Libelle : '');
	}
	
	function PrintPresence($obj) {
		require_once ('PrintPresence.class.php');

		$annee = Cadref::$Annee;
		$debut = $obj['Debut'];
		$fin = $obj['Fin'];
		
		$sql = "
select i.CodeClasse, i.ClasseId, d.Libelle as LibelleD, n.Libelle as LibelleN, e.Numero, e.Nom, e.Prenom, 
a.Libelle as LibelleA, c.HeureDebut, c.HeureFin, j.Jour
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
inner join `##_Cadref-Adherent` e on e.Id=i.AdherentId 
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId 
left join `##_Cadref-Jour` j on j.Id=c.JourId 
where i.Annee=$annee and i.Supprime=0 and i.Attente=0 ";
		if($debut != '') $sql .= "and i.CodeClasse>='$debut' ";
		if($fin != '') $sql .= "and i.CodeClasse<='$fin' ";
		$sql .= "order by i.CodeClasse, e.Nom, e.Prenom";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return false;
		
		$pdf = new PrintPresence($debut, $fin, $obj['Mois']);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Fiches de presence');

		$pdf->PrintLines($pdo);

		$file = 'Home/tmp/FichePresence_'.date('YmdHi').'.pdf';
		$pdf->Output(getcwd() . '/' . $file);
		$pdf->Close();

		return array('pdf'=>$file, 'sql'=>$sql);
	}

}
