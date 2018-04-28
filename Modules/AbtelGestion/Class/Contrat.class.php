<?php

class Contrat extends AbtelGestionBase {
	private $articles;
	private $contrat;
	
	/*
	 * demande l'enregistrement id
	 */
	static function getOneData($id) {
		$sql = "
select c.Code,c.CodeEntite,c.CodeTiers,c.Cadre,c.Libelle,c.DateDebut,c.DateFin
from contrats c
where c.Code='$id'";
		$sql1 = "
select l.NumeroLigne,l.TypeLigne,l.TypeContrat,l.CodeArticle,l.Designation,l.Description,l.PrixVente
from contrats_l l 
where l.CodeContrat='$id'";
		$result = AbtelGestion::getRecord($sql, $sql1);
		return Array($sql, $sql1, $result);
	}

	/*
	 * demande une liste d'enregistrements
	 */
	static function getData($Filters, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy) {
		$sql = "
select c.Code,c.CodeEntite,c.CodeTiers,c.Cadre,c.Libelle,c.DateDebut,c.DateFin
from contrats c";
//		$where = $order = $limit = '';
		$result = AbtelGestion::getRecords($sql);
//		$result = self::getRecords($sql, $where, $order, $limit);
		return $result;
	}

	/*
	 * envoie un objet détail sur un contrat
	 */
	public function setDetail($input) {
		// controle les propriétés importantes
		if(!isset($input['type']) || empty($input['type'])) throw new Exception('Type non spécifié.');
		if(!isset($input['tiers']) || empty($input['tiers'])) throw new Exception('Tiers non spécifié.');
		if(!isset($input['data']) || empty($input['data'])) throw new Exception('Data non spécifié.');
		$this->type = $input['type'];
		$this->tiers = $input['tiers'];
		$this->data = $input['data'];
		// controle le tiers
		if(!$this->checkTiers()) throw new Exception('Tiers non trouvé.');
		// traite la requete
		$ret = array();
		$ret[] = $this->setDetail2();
		if($this->type == 'CompteMail') {
			$this->type = 'Quota';
			$ctrt = $this->setDetail2();
			if($ctrt !== false) $ret[] = $ctrt;
		}
		return $ret;
	}
		
	/*
	 * envoie un objet détail sur un contrat (suite)
	 */
	private function setDetail2() {
		$warning = '';
		$ctrt = false;
		
		$this->contrat = Array('Type'=>$this->type, 'Code'=>microtime(true), 'CodeArticle'=>$this->type);
		
		// récupère les propriétés
		$this->readTypeProperties();
		if(!$this->proprietes) throw new Exception('Type inconnu.');
		// recherche les articles correspondant aux propriétés
		$this->findArticles();
		if($this->articles == '') {
			$warning = 'Article non trouvé.';
		}
		else {
			// recherche le contrat et le code article
			$ctrt = $this->findContrat();
			if($ctrt === false) $warning = 'Contrat non trouvé';
			else {
				$this->contrat['Code'] = $ctrt['Code'];
				$this->contrat['CodeArticle'] = $ctrt['CodeArticle'];
			}
		}
		// article ou contrat non trouvé
		if(!$ctrt && $this->optionnel) return null;
		// recherche une ligne de detail existante
		$id = 0;
		$clef = $this->identifiant == '' ? $this->type : $this->data[$this->identifiant];
		if($ctrt !== false) $id = $this->findDetail($clef);
		// écrit le détail
		$qte = $this->quantite == '' ? 1 : $this->data[$this->quantite];
		$this->writeDetail($id, $clef, $qte, $warning);
		
		return $this->contrat;
	}
	
	/*
	 * charge une ligne de detail
	 */
	private function findDetail($clef) {
		$ctr = $this->contrat['Code'];
		$art = $this->contrat['CodeArticle'];
		$sql = "select Id from contrats_detail where CodeContrat='$ctr' and CodeArticle='$art' and Code='$clef'";
		$rec = $this->getSQLData($sql);
		return $rec === false ? 0 : $data['Id'];
	}
	
	/*
	 * ecrit une ligne de détail
	 */
	private function writeDetail($id, $clef, $qte, $warning) {
		$new = $id == 0;
		$ctr = $this->contrat['Code'];
		$art = $this->contrat['CodeArticle'];
		$wrn = AbtelGestion::$DB->quote($warning);
		
		// ligne de détail
		if(!$new) $sql = "update contrats_detail set Qte=$qte,Anomalie=$wrn where Id=$id";
		else $sql = "
insert into contrats_detail (CodeContrat,CodeArticle,Code,Qte,CodeClient,Anomalie)
values ('$ctr','$art','$clef',$qte,'$tiers',$wrn)";
		AbtelGestion::$DB->Exec($sql);
		if($new) $id = AbtelGestion::$DB->lastInsertId();

		// propriétés de la ligne
		foreach($this->proprietes as $p) {
			$n = $p['Nom'];
			if($n == 'PROPRIETE_TYPE') continue;
			
			$v = $this->data[$n];
			$pid = 0;
			if(!$new) {
				$sql = "select Id,Valeur from proprietes where TypeObjet='CONTRAT_DETAIL' and Cle_1='$ctr' and Cle_2='$art' and Nom='$n' and isDeleted=0";
				$rec = $this->getSQLData($sql);
				if($rec !== false) {
					$pid = $rec['Id'];
					if($rec['Valeur'] != $v) {
						$sql = "update proprietes set isDeleted=1 where Id=$pid";
						AbtelGestion::$DB->Exec($sql);
						$pid = 0;
					}
				}
			}
			if($new || $pid == 0) {
				$ord = $p['Ordre'];
				$sql = "insert into proprietes (TypeObjet,Cle_1,Cle_2,Nom,Valeur,Ordre) values ('CONTRAT_DETAIL','$ctr','$art','$n','$v',$ord)";
				AbtelGestion::$DB->Exec($sql);
			}
		}
	}

	/*
	 * recherche les articles correspondant aux propriétés
	 */
	protected function findArticles() {
		$sql = "select a.Code from articles a ";
		$i = 0;
		foreach($this->proprietes as $p) {
			$n = $p['Nom'];
			if($n == 'PROPRIETE_TYPE' || intVal($p['isKey'])) {
				if($n == 'PROPRIETE_TYPE') {
					$sql .= "inner join proprietes p$i on p$i.TypeObjet='ARTICLE' and p$i.Cle_1=a.Code and p$i.Nom='PROPRIETE_TYPE' and p$i.Valeur='".$this->type."' ";
				} else {
					if(! isset($this->data[$n])) throw new Exception ('Propriété absente : '.$n);
					$sql .= "inner join proprietes p$i on p$i.TypeObjet='ARTICLE' and p$i.Cle_1=a.Code and p$i.Nom='".$n."' and p$i.Valeur='".$this->data[$n]."' ";
				}
				$i++;
			}
		}
		$recs = $this->getSQLData($sql, true);
		$arts = '';
		foreach($recs as $r) {
			if($arts != '') $arts .=',';
			$arts .= "'".$r['Code']."'";
		}
		$this->articles = $arts;
	}
	
	/*
	 * recherche les lignes de contrats correspondant aux articles
	 */
	protected function findContrat() {
		$sql = "
select c.Code,l.CodeArticle
from contrats c
inner join contrats_l l on l.CodeContrat=c.Code
where c.CodeTiers='$this->tiers' and c.FlagResilie=0 and l.CodeArticle in ($this->articles)
order by c.ContratDebut desc limit 1";
		$rec = $this->getSQLData($sql);
		return $rec;
	}

}
