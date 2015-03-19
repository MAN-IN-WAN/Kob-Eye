<?php
class User extends genericClass{
	function getRoles(){
		//Detection de la nature du pere (ne fonctionne que pour le niveau deux)
		$ROLE = Array();
		$Gr = Sys::$Modules["Systeme"]->callData("Group/*/Group/User/".$this->Id);
		if (is_array($Gr))foreach ($Gr as $g){
			$G = genericClass::createInstance("Systeme",$g);
			$Ro = $G->getParents("Role");
			if (sizeof($Ro)&&is_object($Ro[0])){
				$ROLE[] = $Ro[0]->Title;
			}
		}
		return $ROLE;
	}
	function isRole($role){
		//Detection de la nature du pere (ne fonctionne que pour le niveau deux)
		$ROLES = $this->getRoles();
		$GLOBALS["Systeme"]->Log->log("ROLES",$ROLES);
		if (in_array($role, $ROLES))
			return true;
		else 
			return false;
	}
	function backgroundImage() {
		$bg = $this->ArrierePlan;
		if($bg) return $bg;
		$par = $this->getParents('Group');
		while(! $bg && count($par)) {
			$bg = $par[0]->ArrierePlan;
			$par = $par[0]->getParents('Group');
		}
		return $bg;
	}
	function backgroundColor() {
		$bg = $this->Couleur;
		if($bg) return $bg;
		$par = $this->getParents('Group');
		while(! $bg && count($par)) {
			$bg = $par[0]->Couleur;
			$par = $par[0]->getParents('Group');
		}
		return $bg;
	}
	/**
	 * getMenus
	 * Renvoie l'ensemble des menus de l'utilisateur
	 */
	public function getMenus () {
		if (!isset($this->Menus)) $this->initUserVars();
		return $this->Menus;
	}
	/**
	 * initUserVars
	 * Initialise les variables utilisateurs
	 */
	public function initUserVars() {
		//Initialisation des groupes parents
		$Result = Sys::$Modules["Systeme"]->callData("Group/*/Group/User/".$this->Id);
		//Initialisation des genereicClass pour les groupes
		$T = array();
		if (is_array($Result))foreach ($Result as $g) {
			$T[] = genericClass::createInstance("Systeme",$g);
		}
		$this->Groups = $T;
		//Definition de la skin
		$g = $this->Groups;
		if (is_array($this->Groups))for ($i=sizeof($g)-1;$i>=0;$i--) {
			if ($g[$i]->Skin!=""&&$g[$i]->Skin) {
				$SkinGroup = $g[$i]->Skin;
			}
		}
		if ($this->Skin==""||$this->Skin=="0") $this->Skin = (isset($SkinGroup)&&$SkinGroup!="")?$SkinGroup:MAIN_SKIN_NUM;
		//Initialisation des menus
		$Menus = Sys::$Modules["Systeme"]->callData("Systeme/User/".$this->Id."/Menu/*");
		if (is_array($this->Groups))foreach ($this->Groups as $g) {
			$Mt = Sys::$Modules["Systeme"]->callData("Systeme/Group/".$g->Id."/Menu/*");
			if (is_array($Mt)&&sizeof($Mt)) {
				//On concatene les menus dans un seul tableau
				foreach ($Mt as $M) {
					$Menus[] = $M;
				}
			}
		}
		//Maintenant on reorganise les menus afin qu ils soient exploitables
		__autoload("Storproc");
		$Menus = StorProc::sortRecursivResult($Menus,"Menus");
		$Menus = Root::quickSort($Menus,"Ordre");
		
		//Initilisation des menus
		$R = Connection::recursivMenu($Menus);
		$this->Menus = $R;
		
		//Recherche des acces de l'utilisateur
		$Acces = Array();
		$Mt = Sys::$Modules["Systeme"]->callData("Systeme/User/".$this->Id."/Access");
		if (is_array($Mt)&&sizeof($Mt)) {
			//On concatene les acces dans un seul tableau
			foreach ($Mt as $M) {
				$Acces[] = genericClass::createInstance("Systeme",$M);
			}
		}
		//Recherche des acces des groupes parents
		if (is_array($this->Groups))foreach ($this->Groups as $g) {
			$Mt = Sys::$Modules["Systeme"]->callData("Systeme/Group/".$g->Id."/Access");
			if (is_array($Mt)&&sizeof($Mt)) {
				//On concatene les acces dans un seul tableau
				foreach ($Mt as $M) {
					$Acces[] = genericClass::createInstance("Systeme",$M);
				}
			}
		}
		if (!empty($Acces))$Acces = Storproc::SpBubbleSort($Acces,"Ordre","ASC");
			$this->Access = $Acces;
	}
}
?>