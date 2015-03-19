<?php
/**
* This class is a util class in order to store zone informations 
**/
Class Zone extends Root{
	var $Name; 				//String
	var $Components = array();			//Array
	/**
	* Constructor
	*/
	public function __construct() {}
	/********************************
	* Setter
	*********************************/
	/**
	* Return the components list
	* @param String Name
	* @return Array
	*/
	public function setName($Name){
		$this->Name = $Name;
	}
	/**
	* Initialize the zone properties
	* @param Array Config
	* @void
	*/
	public function setConfig($Config){
		if (isset($Config["#"]["COMPONENT"]))foreach ($Config["#"]["COMPONENT"] as $C){
			if (!is_array($C))continue;
			if (!isset($C["@"]))continue;
			$Co = new Component();
			$Co->setConfig($C);
			$this->Components[] = $Co;
		}
	}
	/********************************
	* Public methods
	*********************************/
	/**
	* Return the components list
	* @param c identifiant d'un composant
	* @return Array
	*/
	public function getComponents($c=""){
		if (!empty($c))return $this->Components[$c];
		return $this->Components;
	}
}
?>