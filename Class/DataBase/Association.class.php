<?php


class Association {
	var $Links;				//Tableau à deux dimensions reliant les associations aux objectclass, portant ainsi la cardinalite.
	var $titre;
	var $Default=false;
	var $Module;
	var $Prefix;
	var $attributes;
	var $_description = "";
	//mysql default
	var $AUTO_INCREMENT = "AUTO_INCREMENT";
	var $Bdd = 0;
	var $Owner = 0;
	/*********************
	*   INITIALISATION
	**********************/
	/**
	* Constructeur
	*/
	function Association ($M, $T) {
		$this->_description = $this->titre= $T;
		$this->Module = $M;
		$this->Prefix=MAIN_DB_PREFIX.$M."-";
	}
	/**
	* add Link to the association
	* @param Obj ObjectClass target
	* @param Card Cardinalite
	* #TODO Add this association to the ObjectClass related Association Array
	*/
	function addLink ($O,$C,$T='Id'){
		//Stockage des informations de liaison
		$C = explode(',',$C);
		$this->Links[] = Array(
			"Min" => $C[0],
			"Max" => $C[1],
			"Target" => $T,
			"ObjectClass" => $O
		);
		//Ajout de l'association sur l'objet
		if (is_object($O))$O->addAssociation($this);
	}
	/**
	 * toString
	 * Renvoie les informations de l'association
	 */
	function toString() {
		return "ASSOCIATION -> ".$this->titre." ".$this->Links[0]['ObjectClass']->titre." => ".$this->Links[1]['ObjectClass']->titre." card ".$this->getCard()."\r\n";
	}
	/**
	 * setDescription
	 */
	public function setDescription($d){
		$this->_description = $d;
	}
	/**
	 * getDescription
	 */
	public function getDescription(){
		return $this->_description;
	}
	/**
	 * initCustomAttributes
	 * initialisation des attributs personnalisés
	 * @param Array tableau d'attributs
	 * @void
	 */
	public function initCustomAttributes($t){
		$this->attributes = $t;
		foreach ($t as $n => $att){
			//echo "SET $n =>  $att\r\n";
			$this->$n = $att;
		}
	}
	/**
	* setOwner
	* Define the owner of this association
	* @param integer key of links Array
	*/
	public function setOwner($N){
		$this->Owner = $N;
	}
	/**
	* Check
	* Check the table structure in database
	*/
	function Check(){
		sqlCheck::generateForeignKeySqlTable($this,$this->getAssociationOwner());
		//sqlCheck::verifyIndex($this);
	}
	/*********************
	*   INTERROGATION
	**********************/
	/**
	* isRecursiv
	* is this association recursiv
	* @return Number 
	*	0 - is not recursiv or not binary
	*	1 - is short cardinality
	*	2 - is long cardinality
	*/
	public function isRecursiv() {
		if ($this->isBinary()){
			if ($this->Links[0]['ObjectClass']==$this->Links[1]['ObjectClass']) {
				if ($this->isShort()) return 1;
				if ($this->isLong()) return 2;
			}
		}
		return false;
	}
	/**
	 * hasModule
	 * check if this association is linnked to this module
	 * @return String
	 */
	public function hasModule($Module) {
		return ($this->Links[0]['ObjectClass']->Module ==  $Module || $this->Links[1]['ObjectClass']->Module ==  $Module) ?true : false;
	}
	/**
	 * getForeignModule
	 * get the module of the foreign objectclass (not the objectclass owner)
	 * @return String
	 */
	public function getForeignModule() {
		return $this->Links[$this->Owner ? 0 : 1]['ObjectClass']->Module;
	}
	/**
	* isChild
	* is the association a child of the given object
	* @param String ObjectClass name
	* @return Boolean if this association is a child of this object.
	*/
	public function isChild($O){
		if ($this->Links[0]['ObjectClass']->titre==$O)return true;
	}
	/**
	* isParent
	* is this association a parent of the given objectclass
	* @param String ObjectClass name
	* @return Boolean if this association is a parent of this object
	*/
	public function isParent($O) {
		for ($i=1;$i<sizeof($this->Links);$i++)if ($this->Links[$i]['ObjectClass']->titre==$O)return true;
	}
	/**
	* isBinary
	* Check if this association is binary (two links)
	* @return Boolean true if this association is binary
	*/
	public function isBinary() {
		if (sizeof($this->Links)==2)return true;
		else return false;
	}
	/**
	* isShort
	* Check the cardinality 
	* @return Boolean true if one cardinality is 1,0 or 1,1 and binary
	*/
	public function isShort() {
		if ($this->isBinary()){
			if ($this->Links[0]['Max']=='1'||$this->Links[1]['Max']=='1')return true;
		}
		return false;
	}
	/**
	* isLong
	* Check the cardinality 
	* @return Boolean true if both cardinality is 0,n or 1,n and binary
	*/
	public function isLong() {
		if ($this->isBinary()){
			if ($this->Links[0]['Max']=='n'&&$this->Links[1]['Max']=='n')return true;
		}
		return false;
	}
	/**
	* isMandatory
	* Check the mandatory 
	* @return Boolean true if both cardinality is 1,n or 1,1 and binary
	*/
	public function isMandatory() {
		if ($this->isBinary()){
			if ($this->Links[0]['Min']=='1')return true;
		}
		return false;
	}
	/**
	* isLinked
	* Check if this association is linked to an objectclass
	* @param String Near ObjectClass Name
	* @param String Distant ObjectClass Name
	* @return Associatino Name
	*/
	public function isLinked($N,$D){
		$f=false;
		foreach ($this->Links as $L) if ($L['ObjectClass']->titre==$N)$f=true;
		$g=false;
		foreach ($this->Links as $L)if ($L['ObjectClass']->titre==$D)$g=true;
		if ($f&&$g)return true;
	}
	/**
	* isInterModule
	* Check if this association is between two modules
	* @return Boolean
	*/
	public function isInterModule(){
		return ($this->Links[0]['ObjectClass']->Module!=$this->Links[1]['ObjectClass']->Module);
	}
	/**
	* isLinkedWithModule
	* Check if this association is between two modules
	* @return Boolean
	*/
	public function isLinkedWithModule($Module){
		return ($this->Links[0]['ObjectClass']->Module==$Module||$this->Links[1]['ObjectClass']->Module==$Module);
	}
	/**
	* getAssociationOwner
	* Return the objectClass wich own this association
	* Or the Objectclass with the lowest cardinality
	* @return Object ObjectClass
	*/
	public function getAssociationOwner() {
		//Sinon le proprietaire par defaut
		return $this->Links[$this->Owner]['ObjectClass'];
	}
	/**
	* getTable
	* Return a table name concerned by the key 
	* @return String Table Name
	*/
	public function getTable() {
		if ($this->isShort()){
			//Courte cardinalite
			if ($this->isRecursiv()) return $this->Links[0]['ObjectClass']->titre;
			else {
				$O = $this->getAssociationOwner();
				return $O->titre;
			}
		}
		if ($this->isLong()){
			//Longue cardinalite
			$O = $this->getAssociationOwner();
			return $O->titre.$this->titre;
		}
	}
	/**
	* getField
	* Return the key field name
	* @return String field name
	*/
	public function getField($Type="child") {
		if ($this->isShort()){
			//Courte cardinalite
			return $this->titre;
		}
		$O = $this->getAssociationOwner();
		if ($this->isLong()){
			if ($Type=="child")
				//Longue cardinalite
				return $this->Links[1]['ObjectClass']->titre."Id";
			else 
				return $this->Links[0]['ObjectClass']->titre;
		}
	}
	/**
	* getCard
	* Return the cardinality defined by a string
	* @return String Cardinality
	*/
	public function getCard($T="child") {
		return $this->Links[$T == 'parent' ? 0 : 1]['Min'].','.$this->Links[$T == 'parent' ? 0 : 1]['Max'];
	}
	/**
	* getDriver
	* Return the driver name
	* @return String Driver Name
	*/
	public function getDriver() {
		$O = $this->getAssociationOwner();
		return $O->driver;
	}
	/**
	* getTarget
	* Return the field name to join with the association
	* @return String Field Name
	*/
	public function getTarget() {
		return $this->Links[1]['Target'];
	}
	/**
	* getChildObjectClass
	* Return the child objectclass
	* @return String Field Name
	*/
	public function getChildObjectClass() {
		return $this->Links[0]['ObjectClass'];
	}
	/**
	* getParentObjectClass
	* Return the child objectclass
	* @return String Field Name
	*/
	public function getParentObjectClass() {
		return $this->Links[1]['ObjectClass'];
	}
}
?>