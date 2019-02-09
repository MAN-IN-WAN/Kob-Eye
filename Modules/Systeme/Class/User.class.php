<?php
class User extends genericClass{
	function getRoles(){
		//Detection de la nature du pere (ne fonctionne que pour le niveau deux)
		$ROLE = Array();
		$Gr = Sys::$Modules["Systeme"]->callData("Group/*/Group/User/".$this->Id);
		if (is_array($Gr))foreach ($Gr as $g){
			$G = genericClass::createInstance("Systeme",$g);
			$Ro = $G->getParents("Role");
// PGF 20190209
//			if (sizeof($Ro)&&is_object($Ro[0])){
//				$ROLE[] = $Ro[0]->Title;
//			}
			foreach($Ro as $r) $ROLE[] = $r->Title;
		}
		return $ROLE;
	}

    /**
     * Détecte les roles depuis plusieurs roles délimités par un |.
     * ex: PARC_TECHNICIEN|PARC_CLIENT
     * @param $role
     * @return bool
     */
    function hasRole($role){
        if($this->Admin) return true;
	    $roles = explode(',',$role);
        $USR_ROLES = $this->getRoles();

	    foreach ($roles as $r) {
            //Detection de la nature du pere (ne fonctionne que pour le niveau deux)
            if (in_array(trim($r), $USR_ROLES)) {
                return true;
            }
        }
        return false;
    }
	function isRole($role){
        if($this->Admin) return true;
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


		if($this->Admin) return true;


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
        //Recherche des acces des roles de l'utilisateur
        $this->Roles = $this->getRoles();
        if (is_array($this->Roles))foreach ($this->Roles as $g) {
            $Mt = Sys::$Modules["Systeme"]->callData("Systeme/Role/Title=".$g."/Access");
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



		 /**
	  * exportMenus
	  *export menus as xml
	  */
	  public function exportMenus(){
		$Mt = Sys::$Modules["Systeme"]->callData("Systeme/User/".$this->Id."/Menu/*");
		if (is_array($Mt)&&sizeof($Mt)) {
			//On concatene les menus dans un seul tableau
			foreach ($Mt as $M) {
				//unset($M["Id"]);
				$Menus[] = $M;
			}
		}
		//Maintenant on reorganise les menus afin qu ils soient exploitables
		$Menus = StorProc::sortRecursivResult($Menus,"Menus");
		$Menus = StorProc::cleanRecursivArrays($Menus,"Menus");
		$Menus = $this->quickSort($Menus,"Ordre");
 /* 		require_once 'Class/Utils/Serializer.class.php';
	  	$xml = '';
		$options = array(
                    XML_SERIALIZER_OPTION_INDENT      => '    ',
                    XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
                    XML_SERIALIZER_OPTION_DEFAULT_TAG => 'unnamedItem',
                    XML_SERIALIZER_OPTION_TYPEHINTS   => true
                );
		$serializer = new XML_Serializer($options);
		$result = $serializer->serialize($Menus);
		if( $result === true ) {
			$xml = $serializer->getSerializedData();
		}

		return htmlspecialchars($xml);*/

		return base64_encode(serialize($Menus));
	  }
	  public function importMenus($xml){
		/*require_once 'Class/Utils/Unserializer.class.php';
		$options = array(
                    XML_SERIALIZER_OPTION_INDENT      => '    ',
                    XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
                    XML_SERIALIZER_OPTION_DEFAULT_TAG => 'unnamedItem',
                    XML_SERIALIZER_OPTION_TYPEHINTS   => true
                );
	  	$unserializer = new XML_Unserializer();
		$status = $unserializer->unserialize($xml);
		$data = $unserializer->getUnserializedData();
		print_r($data);*/
		$data = unserialize(base64_decode(trim($xml)));
		return $this->importRecursivMenu($data,$this);
	  }
	  private function importRecursivMenu($data,$parent){
	  	$out="";
	  	foreach ($data as $d){
	  		$out.="-> Creation du menu \r\n";
	  		$t = genericClass::createInstance('Systeme',$d);
			$t->addParent($parent);
			$t->Save();
			if (is_array($d["Menus"]))$out.=$this->importRecursivMenu($d["Menus"], $t);
	  	}
		return $out;
	  }


    /**
     * set
     * Define properties of this object
     * @param String Name of the property to define
     * @param Mixed Value of the property to define
     */
    public function Set($Prop, $newValue) {
        if (empty($Prop)) return;
        parent::Set($Prop, $newValue);

        $Props = $this -> Proprietes(false, true);
        for ($i = 0; $i < sizeof($Props); $i++) {
            if ($Props[$i]["Nom"] == $Prop) {
                if ($Props[$i]["Type"] == "password" ) {
                    if ($newValue != "*******" && strpos($newValue,'[md5]') === false && strlen($newValue) <= 20) {
                            $newValue = '[md5]'.md5(trim($newValue));
                    } elseif (strlen($newValue)> 20 && strpos($newValue,'[md5]') === false){
                        $newValue = '[md5]'.trim($newValue);
                    } elseif($newValue == "*******") {
                        //print_r($newValue);
                        return false;
                    }
                }
            }
        }
        if (is_string($newValue))
            $newValue = trim($newValue);
        $this -> {$Prop} = $newValue;
        return true;
    }
    function Save() {

        return parent::Save();

    }
}
?>