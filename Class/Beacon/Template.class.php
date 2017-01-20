<?php
class Template extends Beacon {

	var $Config;
	var $Titre;
	var $Screen;
	var $Zones;
	var $Name;
	var $Css;
	var $Params;
	var $Object;
	/********************************
	* Constructor
	*********************************/
	function __construct() {}
	/********************************
	* Setter
	*********************************/
	/**
	* Set the name property
	* @param String Name
	*/
	public function setName($Name){
		$this->Name = $Name;
	}
	/**
	* Set the object linked in database
	* @param genericClass Object
	*/
	public function setObject($Object){
		$this->Object = $Object;
	}
	/********************************
	* Public methods
	*********************************/
	/**
	* Set the config from xml raw informations
	* @param String Config
	* @void
	*/
	public function setConfig($Config,$Name=""){
		$x = new xml2array($Config);
		$this->Config = $x->Tableau["TEMPLATE"]["#"];
		$this->Titre = $this->Config["TITLE"][0]["#"];
		$this->Screen = $this->Config["SCREEN"][0]["#"];
		if (isset($this->Config["PARAMS"][0]["#"]['PARAM']))foreach ($this->Config["PARAMS"][0]["#"]['PARAM'] as $P){
			$this->Params[$P["@"]["title"]] = $P["#"];
		}
		if (isset($this->Config["ZONES"][0]["#"]['ZONE']))foreach ($this->Config["ZONES"][0]["#"]['ZONE'] as $Z){
			if (!isset($Z))continue;
			if (!isset($Z["@"]))continue;
			$Zo = new Zone();
			$Zo->setName($Z["@"]["tag"]);
			$Zo->setConfig($Z);
			$this->Zones[$Z["@"]["tag"]] = $Zo;
		}
		if (isset($this->Config["CSS"])){
			$this->Css = $this->Config["CSS"][0]["#"];
		}
		$this->Name = $Name;
		$this->loadData(@file_get_contents(ROOT_DIR.$this->getFilePath("Default.md")));
	}
	/**
	* @override
	* Trigger the generate command in order to initialize all zones and components
	* @void
	*/
	public function Generate() {
		//Gestion des parametres
		//Mise ajour des entetes
		if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->addCss($this->Css);
		//Gestion des zones
		if (is_array($this->Zones))foreach ($this->Zones as $k=>$Z){
			$D = "";
			//On génère tous les composants
			$comps = $Z->getComponents();
			if (is_array($comps))foreach ($comps as $c){
				$c->init();
				$c->Generate();
				$D.=$c->Affich();
			}
			//On place le resultat à la place de la balise en question
			//$this->Data = str_replace("[".$Z."]",$D,$this->Data);
			$this->Zones[$k] = $D;
		}
		//Generation du code
		$this->Vars = Parser::PostProcessing($this->Vars);
		if (isset($this->ChildObjects)&&sizeof($this->ChildObjects)) for ($i=0;$i<sizeof($this->ChildObjects);$i++){
			if (is_object($this->ChildObjects[$i])){
				$this->ChildObjects[$i]->Generate();
			}else{
				$this->ChildObjects[$i] = Process::processingVars($this->ChildObjects[$i]);
				$this->ChildObjects[$i] = Parser::PostProcessing($this->ChildObjects[$i]);
			}
		}
	}
	/**
	* @override
	* implode all array and return generated content
	* @return String
	*/
	public function Affich() {
		//Le contenu du fichier retravaill�
		$this->Data = Parser::getContent($this->ChildObjects);
		if (is_array($this->Zones))foreach ($this->Zones as $k=>$Z)$this->Data = str_replace("[".$k."]",$Z,$this->Data);
		return $this->Data;
	}
	/**
	* Return the zone list
	* @param tag definit une zone par son tag
	* @return Array
	*/
	public function getZones($tag=""){
		if (!empty($tag)&&isset($this->Zones[$tag]))return $this->Zones[$tag];
		else return $this->Zones;
	}
	/**
	* Return Components list by zone or not
	* @param String Zone optional
	* @return Array
	*/
	public function getComponents($t) {
		if (isset($t[0]))$Zone = $t[0];// else 
		if (is_array($this->Zones))foreach ($this->Zones as $k=>$Z){
			$D = "";
			//On génère tous les composants
			if (isset($Z["COMPONENT"])&&is_array($Z["COMPONENT"]))foreach ($Z["COMPONENT"] as $c){
				$Comp=new Component();
				$Comp->setConfig($c);
				$Comp->init();
				$Comp->Generate();
				$D.=$Comp->Affich();
			}
			//On place le resultat à la place de la balise en question
			//$this->Data = str_replace("[".$Z."]",$D,$this->Data);
			$this->Zones[$k] = $D;
		}
	}
	/**
	 * retrieve a file for overload config
	 * @param String filename
	 * @return String
	 */
	 private function getFilePath($filename){
		//Chargement des données
		$CheminSkin = "Skins/".Sys::$Skin."/Templates/".$this->Name."/".$filename;
		$CheminRoot = "Templates/".$this->Name."/".$filename;
		if (file_exists(ROOT_DIR.$CheminSkin))$Chemin = $CheminSkin;
		elseif (file_exists(ROOT_DIR.$CheminRoot))$Chemin = $CheminRoot;
		return $Chemin;
	 }
	/**
	* Export Xml
	* Génère le fichier xml de configuration de template
	* @return Xml 
	*/
	public function ExportXml() {
		#TODO
		return $O;
	}

	/********************************
	* Statiques
	* the params are stored in array
	* Enabling KEML Access by adding "Template" case in process.class.php
	*********************************/
	/**
	* Return a template list defined by filters
	* @param String Nom optional
	* @return array 
	*/
	static function getTemplates(){
		$Nom=isset($t[0]) ? $t[0] : "";
		//Recuperation de l'objectclass
		$te=$GLOBALS['Systeme']->getTemplates();
		if (is_array($te))foreach ($te as $tem){
			$Chemin = "Templates/".$tem."/Template.conf";
			$Data=@file_get_contents(ROOT_DIR.$Chemin);
			$temp=new Template();
			$temp->setName($tem);
			$temp->setConfig($Data);
			$out[] = $temp;
		}
		return $out;
	}
	/**
	* Return a template initialized from a genericClass object
	* @param genericClass Object required 
	* @return Template 
	*/
	static function initFrom($t){
		if (is_array($t))$Object = $t[0];
		else $Object = $t;
		$Module=$Object->Module;$ObjectClass=$Object->ObjectType;
		$temp=new Template();
		$temp->setName($Object->Template);
		$temp->setObject($Object);
		$temp->setConfig($Object->TemplateConfig);
		return $temp;
	}

	/**
	* Return the JS / CSS list potentially used by the website
	* @param string Type of resource (Css/Js)
	* @return Array 
	*/
	function getList( $type ) {
		$t = array();
		if($type != 'Css' && $type != 'Js') return $t;
		foreach($this->Zones as $z) {
			foreach($z->getComponents() as $c) {
				$t[] = $c->{$type};
			}
		}
		return $t;
	}
}
?>