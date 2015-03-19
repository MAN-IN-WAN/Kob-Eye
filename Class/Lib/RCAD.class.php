<?php
class RCAD {
	const RC_GROUP_ID = 4;
	/**
	 * Constructeur
	 */
	function __contruct() {
		
	}
	/**
	 * SynchroPays
	 * Synchronisation des pays.
	 * Recherche des pays dans le RCAD et extraction des pays différents
	 * Création des groupes inexistants dans le groupe RC_GROUP_ID
	 */
	 function SynchroPays() {
	 	$out="";
		include (dirname(__FILE__) . "/adldap/adLDAP.php");
		try {
		    $adldap = new adLDAP(Array(
				"account_suffix"=>AD_ACCOUNT_SUFFIX,
				"domain_controllers"=>Array(AD_DOMAIN_CONTROLLER),
				"admin_username"=>AD_ADMIN_USERNAME,
				"admin_password"=>AD_ADMIN_PASSWORD,
				"use_ssl"=>AD_SSL,
				"use_tls"=>AD_TLS,
				"base_dn"=>AD_BASE_DN
			));
		}
		catch (adLDAPException $e) {
		    echo $e;
		    exit();   
		}
		
		try {
			$Bureaux = Array();
			$Pays = Array();
			//FILTRE SUR UN CHAMP
			$f = Array("a");
			//$f = Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
			foreach ($f as $g){
				$collection = $adldap->user()->find(false,'cn',"$g*",true,Array("physicaldeliveryofficename"));
				foreach ($collection as $co){
					if (!in_array($co["physicaldeliveryofficename"][0],$Bureaux))
						$Bureaux[] = $co["physicaldeliveryofficename"][0];
					$p = explode("/",$co["physicaldeliveryofficename"][0]);
					if (!in_array(trim($p[0]),$Pays))
						$Pays[] = trim($p[0]);
				}
			}
			//recupération du groupes avec le role USER
			$G = Sys::$Modules["Systeme"]->callData("Role/USER/Group");
			$US = genericClass::createInstance("Systeme",$G[0]);
			//recupération du groupes avec le role LOCAL_MANAGER
			$G = Sys::$Modules["Systeme"]->callData("Role/LOCAL_MANAGER/Group");
			$LM = genericClass::createInstance("Systeme",$G[0]);
				
			$out.="<h2>USER => ".$US->Id."</h2>";					
			$out.="<h2>LOCAL_MANAGER => ".$LM->Id."</h2>";					
			$out.="<ul>";
			foreach ($Pays as $k){
				if ($k){
					$out.="<li><h3>$k</h3><ul>";
					//creation des groupes dans USERS
					$c = Sys::$Modules["Systeme"]->callData("Group/".$US->Id."/Group/Nom=".$k,false,0,1000,null,null,"count(distinct(m.Id))");
					$c = $c[0]["count(distinct(m.Id))"];
					$out.="<li>USER GROUP ".(($c)?"<span class='vert'>OK</span>":"<span class='rouge'>TO CREATE</span>")."";
					if (!$c){
						$out.="<ul>";
						//creation du groupe
						$out.="<li class='bleu'>creation du groupe";
						$gr = genericClass::createInstance("Systeme","Group");
						$gr->Nom = $k;
						$gr->addParent($US);
						$gr->Save();
						$out.="OK</li></ul>";
					}
					$out.="</li>";
					$out.="</ul></li>";
				}
			}
			$out.="</ul>";
		}
		catch (adLDAPException $e) {
		    echo $e;
		    exit();   
		}
		return $out;
	 }
	/**
	 * ResetPays
	 * Reinitialisation des pays groupes
	 */
	 function ResetPays() {
		//recupération du groupes avec le role USER
		$G = Sys::$Modules["Systeme"]->callData("Role/USER/Group");
		$US = genericClass::createInstance("Systeme",$G[0]);
		//recupération du groupes avec le role LOCAL_MANAGER
		$G = Sys::$Modules["Systeme"]->callData("Role/LOCAL_MANAGER/Group");
		$LM = genericClass::createInstance("Systeme",$G[0]);
			
		$out.="<h2>USER => ".$US->Id."</h2>";					
		$out.="<ul>";
		$Pays = $US->getChilds('Group');
		foreach ($Pays as $k){
			if (is_object($k)){
				$out.="<li><h3>".$k->Nom."</h3><ul>";
				$out.="<li>DELETE ".(($k->Delete())?"<span class='vert'>OK</span>":"<span class='rouge'>ERROR</span>")."";
				$out.="</ul></li>";
			}
		}
		$out.="</ul>";
		$out.="<h2>LOCAL_MANAGER => ".$LM->Id."</h2>";					
		$out.="<ul>";
		$Pays = $LM->getChilds('Group');
		foreach ($Pays as $k){
			if (is_object($k)){
				$out.="<li><h3>".$k->Nom."</h3><ul>";
				$out.="<li>DELETE ".(($k->Delete())?"<span class='vert'>OK</span>":"<span class='rouge'>ERROR</span>")."";
				$out.="</ul></li>";
			}
		}
		$out.="</ul>";
		//Suppression des databases
		$out.="<h2>SUPPRESION DES DATABASES</h2>";					
		$out.="<ul>";
		$Db = Sys::$Modules["Vitrine"]->callData("Database");
		foreach ($Db as $d){
			$LM = genericClass::createInstance("Vitrine",$d);
			$out.="<li>DELETE ".$LM->Nom."<span class='vert'>OK</span></li>";
			$LM->Delete();
		}
		$out.="</ul>";
		return $out;
	 }
}
?>