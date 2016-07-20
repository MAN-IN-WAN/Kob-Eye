<?php
class Post extends genericClass{
	
	function Save(){
		parent::Save();
		if (empty($this->Resume))
			$this->SaveResume();
		parent::Save();
	}
	/**
	 * Enregistre le resume du post
	 * Si le resume est vide
	 */
	function SaveResume() {
		$t = strip_tags($this->Contenu);
		$t = substr($this->Contenu, 0,350);
		$this->Resume =$t." [...]"; 
	}
	
	function getClone()  {
		//creéation d'un clone avec clonage spécifique
		$o = parent::getClone();
		$o->Save();
		//ASSOCIATION
		//clonage des données
		$dons = $this->getChildren('Donnee');
		foreach ($dons as $d){
			$d->addParent($o);
			$d->Save();
		}
		//CLONAGE
		//clonage des punchtext
		$dons = $this->getChildren('Block');
		foreach ($dons as $d){
			$pt = $d->getClone();
			$pt->addParent($o);
			$pt->Save();
		}
		//clonage des illustrations
		$dons = $this->getChildren('Video');
		foreach ($dons as $d){
			$pt = $d->getClone();
			$pt->addParent($o);
			$pt->Save();
		}
		//clonage des illustrations
		$dons = $this->getChildren('Donnees');
		foreach ($dons as $d){
			$pt = $d->getClone();
			$pt->addParent($o);
			$pt->Save();
		}
		return $o;
	}
}
?>