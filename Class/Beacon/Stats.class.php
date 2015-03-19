<?php
class Stats extends Beacon{

	function Generate() {
		//Initialisation;
		$Function=null;
		$Req="";
		$Fold="";
		//Definition des parametres
		$this->Vars = Process::ProcessingVars($this->Vars);
		$Vars = explode("|",$this->Vars);
		$Query = $Vars[0];
		$Var = $Vars[1];
		//Dates
		$DateDepart = (isset($Vars[2]))?$Vars[2]:time()-2592000;
		$DateFin = (isset($Vars[3]))?$Vars[3]:time();
		//Analyse de la requete
		$QTab = explode("/",$Query);
		$Module = $QTab[0];
		if (sizeof($QTab)>1)$Trigger = $QTab[1];
		if (sizeof($QTab)>2)$Function = $QTab[2];
		if ($Function=="Info") {
			//Requete de dossier
			Sys::$Modules[$Module]->loadSchema();
			$Tr = Sys::$Modules[$Module]->getTriggers($Trigger);
			$Result = $Tr->getInfos($QTab[3]);
		}elseif ($Function=="Folder") {
			//Requete de dossier
			Sys::$Modules[$Module]->loadSchema();
			for ($i=3;$i<sizeof($QTab);$i++) $Fold.=(($i>3)?"/":"").$QTab[$i];
			$Tr = Sys::$Modules[$Module]->getTriggers($Trigger);
			$Result = $Tr->getFolders($Fold);
		}else{
			//Requete statistique
			switch (sizeof($QTab)) {
				case 1:
					//Cas liste trigger
					$Result = Sys::$Modules[$Module]->getTriggers();
				break;
				case 2:
					//Cas liste function 
					Sys::$Modules[$Module]->loadSchema();
					$Tr = Sys::$Modules[$Module]->getTriggers($Trigger);
					$Result = $Tr->getInfos($Req);
				break;
				case 3:
					//Cas resultat
					Sys::$Modules[$Module]->loadSchema();
					$Tr = Sys::$Modules[$Module]->getTriggers($Trigger);
					$Result = $Tr->getDatas($Function,$DateDepart,$DateFin);
				break;
			}
		}
		Process::registerTempVar($Var,$Result);
	}	


}