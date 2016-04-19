<?php
class ZoneFiscale extends genericClass {
	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	//Pays (CODE)
	//Pays (CODEPOSTAL)
	static function getZone($Pays,$CodePostal=""){
		$Zp = Sys::getData("Fiscalite","ZoneFiscale/Pays/Nom=".$Pays,0,1,"Id","DESC","m.*,j1p.Nom as Pays");
		if (sizeof($Zp)){
			//ON renvoie la zone
			return $Zp[0];
		}else{
			//Sinon on recherche le pays , le departement, la ville pour rechercher la zone
			$P = Sys::getOneData("Fiscalite","Pays/Nom=".$Pays);
			if (is_object($P)&&$CodePostal!=""){
				//On recherche maintenant le code postal correspondant
				$Cp = Sys::getOneData("Fiscalite","Pays/".$P->Code."/Departement/*/Ville/*/CodePostal/Code=".$CodePostal,0,100,"Id","DESC","m.*,j0.Nom as Pays,j0.Code as PaysCode, j1.Id as DepartementCode, j2.Id as VilleCode");
               	if (!is_object($Cp))return;
				//Recherche de la zone correspondante avec le departement
				$Zd = Sys::getOneData("Fiscalite","ZoneFiscale/Departement/".$Cp->DepartementCode,0,1,"Id","DESC","m.*,j1p.Nom as Pays");
				if (is_object($Zd))return $Zd;
				//Recherche de la zone correspondante avec la ville
				$Zv = Sys::getOneData("Fiscalite","ZoneFiscale/Ville/Code=".$Cp->VilleCode,0,1,"Id","DESC","m.*,j1p.Nom as Pays");
				if (is_object($Zv))return $Zv;
			}
		}
		//Sinon renvoie la zone par défaut
		$Zd = Sys::getOneData('Fiscalite',"ZoneFiscale/Default=1",false,0,1);
		if (is_object($Zd))return $Zd;
		//Sinon false
		$zoneDefault =genericClass::createInstance('Fiscalite','ZoneFiscale');
		return $zoneDefault;
	}
}
?>