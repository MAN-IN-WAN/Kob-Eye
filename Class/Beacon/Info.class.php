<?php
class Info extends Beacon {
	function Info (){
	}
	function Generate() {
		switch($this->Beacon) {
			case "INFO":
				Beacon::Generate();
				$Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$Vars);
				$this->Attributes = $Vars;
				$Var = (isset($this->Attributes[1]))?$this->Attributes[1]:"INFO";
				Process::$TempVar[$Var]=Info::getInfos($this->Attributes[0],$Var);
			break;
			case "COUNT":
				$Vars = explode("|",$this->Vars);
				$this->Attributes = $Vars;
				$this->getCount();
			break;
		}
	}
	static function getInfos($Query,$Var="INFO"){
		if (!is_string($Query)||empty($Query)) return;
		$q = explode("::",$Query);
		if ($q[0]=="GENERAL"||$q[0]=="MODULE"){
			$Out["TypeSearch"]="conf";
			$Out["Query"] = $Query;
			return $Out;
		}
		if (sizeof($q)>1){
			//extraction des identifier et labels (cas combo)
			$Query = $RealQuery = $q[0];
			$Out["Identifier"] = $q[1];
			$Out["Label"] = (isset($q[2]))?$q[2]:$q[1];
		}else
			$RealQuery=$Query;
		$Out["Query"] = $RealQuery;
		//On extrait le nom du module
		$Mod = explode("/",$Query,2);
		$FirstModule = $Mod[0];
		if (!$FirstModule) return ;
		if ($GLOBALS["Systeme"]->isModule($FirstModule)){
			//$Query = (isset($Mod[1]))?$Mod[1]:'';
			//On recolte les infos sur la requete
			$Result = Sys::$Modules[$FirstModule]->splitQuery($Query);
			//On definit le module
			$Module = $Out["Module"] = $FirstModule;
			//On récupère le module de la requete de sortie
			if (isset($Result[0]["Out"])){
				$ObjOut = $Result[$Result[0]["Out"]];
				//On recupere le type de la reponse
				$Type = $ObjOut["DataSource"];
				$Module= $ObjOut["Module"];
			}else $Type=false;
			//On supprime les valeurs de la variable précedente
			Process::UnRegisterTempVar($Var);
			if ($Type){
				Sys::$Modules[$Module]->loadSchema();
				$R = Sys::$Modules[$Module]->Db->getByTitleOrFkey($Type);
				if (!$Obj = genericClass::createInstance($Module,$Type))return false;
				$Out["Reflexive"] =  Sys::$Modules[$Module]->Db->isReflexive($Type);
				$Out["Historique"] = $Result;
				$Out["Module"] = $Module;
				$Out["QueryModule"] = $FirstModule;
				$Out["NbHisto"] = (isset($Result[0]["Value"]))?sizeof($Result):0;
				$Out["TypeSearch"] = $Result[0]["Type"];
				$Out["TypeChild"] = $Type;
				$Out["ObjectType"]= $Type;
				$Out["typesEnfant"] = $Obj->getChildTypes();
				$Out["typesParent"] = $Obj->getParentTypes();
				//Fonctions
				$Functions = $R->Functions;
				$Out["Functions"] = $Functions;
				$Child = $Direct = $FirstModule;
				$x = (isset($Result[0]["Interface"])&&is_array($Result[0]["Interface"]))?0:1;
				if (!$x)$Out["TypeSearch"] = "Interface";
				$LastId = "";
				$LastDirectObjectClass = "";
				for ($i=0;$i<sizeof($Result);$i++) {
					if ($i<sizeof($Result)-$x)$Direct.="/".$Result[$i]["DataSource"].((isset($Result[$i]["Value"]))?"/".$Result[$i]["Value"]:"");
					if ($i==sizeof($Result)-1)
						$Child.="/".$Result[$i]["DataSource"];
					else $Child.="/".$Result[$i]["DataSource"].((isset($Result[$i]["Value"]))?"/".$Result[$i]["Value"]:"");
					$LastId = (isset($Result[$i]["Value"])&&!empty($Result[$i]["Value"]))?$Result[$i]["Value"]:$LastId;
					if ($i<sizeof($Result))$LastDirectObjectClass = $Result[$i]["DataSource"];
				}
				$Out["LastId"]= $LastId;
				$Out["LastDirectObjectClass"]= $LastDirectObjectClass;
				$Out["ObjectType"]= $LastDirectObjectClass;
				$Out["LastDirect"] = $Direct;
				$Out["LastChild"] = $Child;
			}else{
				//Cas dune url menu
				if (!empty($Query))
					$R = explode("/",$Module."/".$Query);
				else 
					$R = Array($Module);
				if (is_array($R))foreach ($R as $k=>$r){
					$T["Value"] = $r;
					$Result[$k] = $T;
				}
				$Out["Historique"] = $Result;
				$Out["TypeSearch"] = "Interface";
				$Out["NbHisto"] = sizeof($Result);
				$Out["Query"] = $RealQuery;
			}
		}else{
			//Cas dune url menu
			$R = explode("/",$Query);
			if (is_array($R))foreach ($R as $r){
				$T["Value"] = $r;
				$Result[] = $T;
			}
			$Out["Historique"] = $Result;
			$Out["TypeSearch"] = "Menu";
			$Out["NbHisto"] = sizeof($Result);
		}
		return $Out;
	}
    function getCount(){
        //Le premier parametre est la requete
        $Query = explode("[!",$this->Attributes[0]);
        if (sizeof($Query)>1){
            $Query = Process::processingVars($this->Attributes[0]);
        }else{
            $Query=$Query[0];
        }
        //Le second est le nom de la variable dans laquelle stoquer l'info (COUNT par defaut)
        $Var = (isset($this->Attributes[1])&&!empty($this->Attributes[1]))?$this->Attributes[1]:"COUNT";
        if (is_string($Query)) {
            $V = (isset($this->Attributes[2]) && $this->Attributes[2] != '') ? "m." . $this->Attributes[2] : "m.Id";
            //On extrait le nom du module
            $Module = explode("/", $Query, 2);
            $Query = (isset($Module[1])) ? $Module[1] : "";
            $Module = $Module[0];
            //On recolte les infos sur la requete
            //Execution de la requete
            unset(Process::$TempVar[$Var]);
            if (is_array($Query)) {
                Process::$TempVar[$Var] = sizeof($Query);
            } else {
                $Count=0;
                if ($Module && isset(Sys::$Modules[$Module]) && is_object(Sys::$Modules[$Module])) $Count = Sys::getCount($Module, $Query);
                Process::$TempVar[$Var] = '0';
                if (!$Count)
                    Process::$TempVar[$Var] = '0';
                else Process::$TempVar[$Var] = $Count;
            }
        }
		if (is_array($Query)){
			Process::$TempVar[$Var] = sizeof($Query);
		}
        return ;
    }
    function Affich() {
		//return "INFO BEACON";
	}
}
?>