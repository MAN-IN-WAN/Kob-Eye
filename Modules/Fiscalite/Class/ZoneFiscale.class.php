<?php
class ZoneFiscale extends genericClass {
	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	//Pays (CODE)
	//Pays (CODEPOSTAL)
	static function getZone($Pays,$CodePostal=""){
		$Zp = Sys::$Modules["Fiscalite"]->callData("ZoneFiscale/Pays/Nom=".$Pays,false,0,1,"Id","DESC","m.*,j1p.Nom as Pays");
		if (is_array($Zp)){
			//ON renvoie la zone
			return Array(genericClass::createInstance('Fiscalite',$Zp[0]));
		}else{
			//Sinon on recherche le pays , le departement, la ville pour rechercher la zone
			$P = Sys::$Modules["Fiscalite"]->callData("Pays/Nom=".$Pays);
			if (is_array($P)&&$CodePostal!=""){
				//On recherche maintenant le code postal correspondant
				$Cp = Sys::$Modules["Fiscalite"]->callData("Pays/".$P[0]["Code"]."/Departement/*/Ville/*/CodePostal/Code=".$CodePostal,false,0,100,"Id","DESC","m.*,j0.Nom as Pays,j0.Code as PaysCode, j1.Id as DepartementCode, j2.Id as VilleCode");
				//Recherche de la zone correspondante avec le departement
				$Zd = Sys::$Modules["Fiscalite"]->callData("ZoneFiscale/Departement/".$Cp[0]["DepartementCode"],false,0,1,"Id","DESC","m.*,j1p.Nom as Pays");
				if (is_array($Zd))return Array(genericClass::createInstance('Fiscalite',$Zd[0]));
				//Recherche de la zone correspondante avec la ville
				$Zv = Sys::$Modules["Fiscalite"]->callData("ZoneFiscale/Ville/Code=".$Cp[0]["VilleCode"],false,0,1,"Id","DESC","m.*,j1p.Nom as Pays");
				if (is_array($Zv))return Array(genericClass::createInstance('Fiscalite',$Zv[0]));
			}
		}
		//Sinon renvoie la zone par défaut
		$Zd = Sys::$Modules["Fiscalite"]->callData("ZoneFiscale/Default=1",false,0,1);
		if (is_array($Zd))return Array(genericClass::createInstance('Fiscalite',$Zd[0]));
		//Sinon false
		return false;
	}
}
?>