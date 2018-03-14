<?php
/**
* This class define and display a component
**/
class Component extends Beacon{

	var $Name;
	var $Module;
	var $Screen;
	var $Zones;
	var $Css;
	var $Params;
	var $Title;
	var $Proprietes;
	var $Path;
	var $Twig;
	/**
	* Constructor
	*/
	public function __construct() {
		//Initialisation de la liste des proprietes
		$this->Proprietes = Array();
	}
	/********************************
	* Public methods
	*********************************/
	/**
	* @override
	* Initialize a component instance from KEML beacon
	* @param String Var
	* @param String Data
	* @param String Beacon
	* @void
	*/	
	public function setFromVar($Var,$Data,$Beacon) {
		$this->Vars = $Var;
		$this->Data = $Data;
		$this->Beacon = $Beacon["BEACON"];
		//Recuperation du contenu
		if (!preg_match("#([^\/]*?)\/([^\/]*?)\/(.*)#",$this->Vars,$Out))
			preg_match("#([^\/]*?)\/(.*)#",$this->Vars,$Out);
		$this->Module = $Out[1];
		$this->Title = $Out[2];
		if (isset($Out[3])){
			$Fichier = explode("?",$Out[3]);
			$this->Vars = (isset($Fichier[1]))?$Fichier[1]:"";
			$Fichier = $Fichier[0]!="" ? $Fichier[0] : "Default";
		}else{
			$this->Vars ="";
			 $Fichier = "Default";
		}
		$this->Css = $this->getFilePath("style.css");
		$Chemin = $this->getFilePath($Fichier.'.md');
		$this->Path = $Chemin;
		if (preg_match('#\.twig$#',$this->Path)) {
			$this->Twig = true;
			KeTwig::loadTemplate($this->Path);
		}else{
			$this->Data = @file_get_contents($this->Path);
		}
	}
	/**
	* @Override
	* Delaying the generation precess
	* @void
	*/
	public function init(){
		//Nothing
	}
	/**
	* Initialize the config of a component from a conf file
	* @param Array Config
	* @void
	*/	
	public function setConfig($Config){
		//Cas du chargement du xml en chaine
		if (is_string($Config)){
			$x = new xml2array($Config);
			$Config = $x->Tableau["COMPONENT"];
		}
		//Recuperation de la definition du composant
		$this->Title = $Config["@"]["title"];
		$this->Module = $Config["@"]["module"];
		$Config = $Config["#"];
		//Sion il s'agit d'un tableau.
		if(!empty($Config)) {
			$this->Name = (isset($Config["TITLE"]))?$Config["TITLE"][0]["#"]:"*** sans titre ***";
			$this->Screen = $Config["SCREEN"][0]["#"];
			if (isset($Config["PARAMS"][0]["#"]['PARAM'])&&is_array($Config["PARAMS"][0]["#"]['PARAM']))foreach ($Config["PARAMS"][0]["#"]['PARAM'] as $P){
				$this->Proprietes[] = array("Type" => $P["@"]["type"], "Nom" => $P["@"]["name"], "Valeur" => $P["#"],"description" => (empty($P["@"]["description"]) ? $P["@"]["name"] : $P["@"]["description"]));
			}
			if (isset($Config["CSS"])){
				$this->Css = $this->getFilePath($Config["CSS"][0]["#"]);
			}
			$Chemin = $this->getFilePath("Default.md");
			$this->Path = $Chemin;
			if (preg_match('#\.twig$#',$this->Path)) {
				$this->Twig = true;
				KeTwig::loadTemplate($this->Path);
			}else{
				$this->Data = @file_get_contents($this->Path);
			}
		}
	}
	/**
	 * retrieve a file for overload config
	 * @param String filename
	 * @return String
	 */
	 private function getFilePath($filename){
		 $mod="";
		if (preg_match('#\.md$#',$filename)) {
			$mod='md';
		}
		$Chemin = "";
		//Chargement des données
		if (sizeof(explode('/',$filename))<=1){
			$CheminSkin = "Skins/".Sys::$Skin."/Modules/".$this->Module."/Components/".$this->Title."/".$filename;
			$CheminShared = "Skins/".Skin::$SharedSkin."/Modules/".$this->Module."/Components/".$this->Title."/".$filename;
			$CheminModule = "Modules/".$this->Module."/Components/".$this->Title."/".$filename;
			if ($mod=='md') {
                $filenametwig = str_replace('.md','.twig',$filename);
				$CheminSkinTwig = "Skins/" . Sys::$Skin . "/Modules/" . $this->Module . "/Components/" . $this->Title . "/" . $filenametwig;
				$CheminSharedTwig = "Skins/" . Skin::$SharedSkin . "/Modules/" . $this->Module . "/Components/" . $this->Title . "/" . $filenametwig;
				$CheminModuleTwig = "Modules/" . $this->Module . "/Components/" . $this->Title . "/" . $filenametwig;
			}
		}else{
			$CheminSkin = "Skins/".Sys::$Skin."/".$filename;
			$CheminShared = "Skins/".Skin::$SharedSkin."/".$filename;
			$CheminModule = $filename;
			if ($mod=='md') {
                $filenametwig = str_replace('.md','.twig',$filename);
				$CheminSkinTwig = "Skins/".Sys::$Skin."/".$filenametwig;
				$CheminSharedTwig = "Skins/".Skin::$SharedSkin."/".$filenametwig;
				$CheminModuleTwig = $filenametwig;
			}
		}
		//Test des chemins
		if ($mod=='md'&&file_exists($CheminSkinTwig))$Chemin = $CheminSkinTwig;
		elseif (file_exists($CheminSkin))$Chemin = $CheminSkin;
		elseif ($mod=='md'&&file_exists($CheminSharedTwig))$Chemin = $CheminSharedTwig;
        elseif (file_exists($CheminShared))$Chemin = $CheminShared;
		elseif ($mod=='md'&&file_exists($CheminModuleTwig))$Chemin = $CheminModuleTwig;
        elseif (file_exists($CheminModule))$Chemin = $CheminModule;

		return $Chemin;
	 }
	/**
	* Define the name 
	* @param String Name
	* @return Array
	*/
	public function setName($Name){
		$this->Name = $Name;
	}
	/**
	* Define the module 
	* @param String Name
	* @return Array
	*/
	public function setModule($Name){
		$this->Module = $Name;
	}

	/**
	 * Récupère la valeur d'une propriété
	 * @param	Array	La propriété
	 * @return	La propriété avec sa valeur
	 */
	public function getPropAvecValeur($Prop) {
		foreach($this->Proprietes as $p) if($Prop['Nom'] == $p['Nom']) return $p;
		return $Prop;
	}

	/**
	* @override
	* Trigger the generate command in order to initialize all zones and components
	* @void
	*/
	public function Generate(){
		parent::init();
 		//Mise ajour des entetes
		if (is_object($GLOBALS["Systeme"] -> Header))
			$GLOBALS["Systeme"] -> Header -> addCss($this -> Css);
		//Sauvegarde de la pile
		$TempVar = Process::$TempVar;
		//Chargement des parametres
		$Vars = Process::processingVars($this -> Vars);
		preg_match_all("#([^&=|]*?)=([^&|=]*)#", $Vars, $Vars);
		for ($i = 0; $i < sizeof($Vars[0]); $i++) {
			$this -> Params[$Vars[1][$i]] = $Vars[2][$i];
			Process::$TempVar[$Vars[1][$i]] = Process::processingVars($Vars[2][$i]);
		}
		//On remplace les parametres avant
		if (is_array($this -> Proprietes))
			foreach ($this->Proprietes as $T => $P) {
				Process::$TempVar[Process::processingVars($P["Nom"])] = Process::processingVars($P["Valeur"]);
			}
		if (!$this->Twig)
			parent::Generate();
		else{
			$this->Data = KeTwig::render($this->Path,Process::$TempVar);
		}
		//Restauration de la pile
		Process::$TempVar = $TempVar;
	}
	/**
	* @override
	* implode all array and return generated content
	* @return String
	*/
	function Affich($test=false) {
		//Le contenu du fichier retravaill�
		//$this->Data = Parser::getContent($this->ChildObjects);
		return $this->Data;
	}

	/**
	 * Retourne la liste de tous les composants
	 * ( va piocher dans les dossiers Modules/.../Components )
	 * @return	array	La liste des composants sous la forme Module/Component
	 */
	public static function getAll() {
		$cpts = array();
		$modules = array_keys(Sys::$Modules);
		foreach($modules as $m) :
			$path = dirname(dirname(dirname(__FILE__))).'/Modules/'.$m.'/Components';
			if(is_dir($path)) :
				$dir = opendir($path);
				while($d = readdir($dir)) :
					if(is_dir($path.'/'.$d) and $d != '.' and $d != '..') :
						$cpts[] = $m . '/' . $d;
					endif;
				endwhile;
			endif;
		endforeach;
		return $cpts;
	}

	/**
	 * Retourne un composant avec sa config de base
	 * @param	array	[0] => "Path du composant sous la forme Module/Component"
	 * @return	array	Un objet Component
	 */
	public static function getInstance( $p ) {
        if(!is_array($p))$p = array($p);
		$params = explode('/',$p[0]);
		$path = dirname(dirname(dirname(__FILE__))).'/Modules/'.$params[0].'/Components/'.$params[1].'/Component.conf';
		if(is_file($path)) :
			$data = file_get_contents($path);
			$cpt = new Component();
			$cpt->setConfig($data);
			return $cpt;
		else :
			echo 'ERREUR : Fichier de configuration introuvable !'; die;
		endif;
	}
}
?>