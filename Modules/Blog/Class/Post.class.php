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
}
?>