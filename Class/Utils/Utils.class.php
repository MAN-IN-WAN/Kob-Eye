<?php
class Utils {
	static function isMail($P){
		if (is_array($P))$address = $P[0];
		else $address = $P;
		if( preg_match( "#.*<(.+)>#", $address, $regs ) )$address = $regs[1];
		return preg_match( "#^[^@]+@([a-zA-Z0-9\-]+?)\.([a-zA-Z0-9\-\.]+)\$#",$address,$z);
	}
	static function  isArray($P) {
		return (isset($P[0])&&is_array($P[0]));
	}
	static function  parseInt($P) {
		return intval($P[0]);
	}
	static function  getLines($P) {
		return nl2br($P[0]);
	}
	static function  nl2br($P) {
		return nl2br($P[0]);
	}
	static function  noHtml($P) {
		return strip_tags(implode(",",$P));
	}
	static function  Clean($P) {
		$Params = trim($P[0]);
		$Params = strtolower($Params);
		return $Params;	
	}
	static function  getDate($P) {
		if (!isset($P[1]))return 0;
		if (!$P[1]>0)$P[1]=time();
		return date($P[0],$P[1]);
	}
	static function  getTms($P) {
		preg_match("#^([0-9]+?)\/([0-9]+?)\/([0-9]+?)\ ([0-9]+?)\:([0-9]+?)$#",$P[0],$D);
		if (sizeof($D)<=1)preg_match("#^([0-9]+?)\/([0-9]+?)\/([0-9]+?)$#",$P[0],$D);
		if (sizeof($D)<=1) return 0;
		$h=(isset($D[4]))?$D[4]:0;
		$m=(isset($D[5]))?$D[5]:0;
		$s=(isset($D[6]))?$D[6]:0;
		$M=(isset($D[2]))?$D[2]:0;
		$J=(isset($D[1]))?$D[1]:0;
		$A=(isset($D[3]))?$D[3]:0;;
		return mktime($h,$m,$s,$M,$J,$A);
	}

	static function strToTime($P) {
        	return strtotime($P[0]);
    	}
	static function getTmsSencha($P) {
		// Transforme les données POST fournies par sencha (YYYY-mm-ddTHH:ii:ss) en timestamp
		// Si le 2nd parametre est true on prend la fin de journée (23h59:59)
		if(empty($P[0])) return '';
		preg_match("#^([0-9]+?)-([0-9]+?)-([0-9]+?)T([0-9]+?)\:([0-9]+?)\:([0-9]+?)$#",$P[0],$D);
		if($P[1]) return mktime(23,59,59,$D[2],$D[3],$D[1]);
		else return mktime(0,0,0,$D[2],$D[3],$D[1]);
	}
	static function  Random($P) {
		return rand(0, $P[0]);
	}
	static function  getPrice($P) {
		return sprintf('%.2f',floor($P[0]*100)/100);
	}
	static function  Canonic($P) {
		if (is_array($P))$mc = trim($P[0]);
		else $mc = trim($P);
		// Modif pour Unibio le 14/08/2012 // On garde tous les mots // if (strlen($mc)<3&&!is_numeric($mc))return "";
		$chaine=utf8_decode($mc);
		$chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
		if (strlen($chaine)>3) $chaine = preg_replace("#(e|s|es)$#","",$chaine);
		return utf8_encode($chaine);
	}
	static function  isPair($P){
		$Params = ( floor($P[0]/2)== $P[0]/2 ) ? true:false  ;
		return $Params;
	}
	static function modulo($P) {
		return $P[0]%$P[1];
	}
	static function md5($P){
		return md5($P[0]);
	}
	static function isDate($P) {
		if (is_array($P))$date = $P[0];
		else $date = $P;
		if( preg_match( "#[0-9]{2}\/[0-9]{2}\/[0-9]{4}#", $date, $regs ) ) {
			return true;
		}else{
			return false;
		}
	}
	static function setSearch($P) {
		if (is_array($P))$s = trim($P[0]);
		else $s = trim($P);
		$s = explode(" ",$s);
		$bl = (isset($P[1]))?$P[1]:false;
		$o="";
		if (is_array($s))foreach ($s as $m){
			if (strlen($m)<3&&!is_numeric($m))continue;
			$m=Utils::Canonic(Array($m));
			if ($bl){
				$Module = explode("/",$bl,2);
				$Module = $Module[0];
				$Tab = $GLOBALS["Systeme"]->Modules[$Module]->callData($bl."/Titre=".$Mc,"",0,1,"","","COUNT(DISTINCT(m.Id))");
				if ($Tab[0]["COUNT(DISTINCT(m.Id))"]>0)continue;
			}
			$o.=((strlen($o)>0&&strlen($m)>0)?" ":"").$m;
		}
		return $o;
	}
	static function AddToTab($P) {
		if(!is_array($P[0])) $P[0] = array();
		$P[0][] = $P[1];
		return $P[0];
	}
	static function getFileName($P){
		//$t = $this->processVars($Params[0]);
		$t = explode("/",trim($P[0]));
		return $t[sizeof($t)-1];
	}
	static function jsonDecode($P){
		$P = implode(",",$P);
		return json_decode($P);
	}
	static function  Calc_Reduction($P){
		// 1 - type de renvoi : Pourcentage, float
		// 2- nb de decimales
		// 3 - Tarif
		// 4 - Tarif reduit
		switch ($P[0]) {
		case 'P':
			// pourcentage
			if ($P[1]== '0' ){
			// valeur entiere
			$t = $P[2]-$P[3];
			$Params = ( floor(($t*100)/ floor($P[2])))   ;
			} else {
			// valeur arrondi au nombre de décimales
			$Params = sprintf('%.2f',floor(($P[3]/$P[2])*100));
			}
		break;
		}
		return $Params;
	}

	static function  getMontantTTC($P) {
		$montant= $P[0] + (($P[0] *$P[1] )/100);
		return sprintf('%.2f',round($montant*100)/100);
	}

	static function KEAddSlashes($P) {
        $tmp =  str_replace('/', '\\!#!\\', $P[0]);
        $tmp =  str_replace('&', '\\!##!\\', $tmp);
        return $tmp;
	}

	static function KEStripSlashes($P) {
        $tmp =  str_replace('\\!#!\\', '/', $P[0]);
        $tmp = str_replace('\\!##!\\', '&', $tmp);
		return $tmp;
	}

	static function  strReplace($P) {
		return str_replace($P[0], $P[1], $P[2]);
	}
	
	static function substr($P) {
		return substr($P[0],0,$P[1]);
	}
	
    static function  Implode($P) {
        return is_array($P[1]) ? implode($P[0], $P[1]) : "";
    }

    static function  Explode($P) {
        return explode($P[0], $P[1]);
    }

    static function UrlDecode($P) {
        return urldecode($P[0]);
    }

    static function escape($P) {
    	if (is_array($P))$s = $P[0];
		else $s = $P;
		$s = str_replace("+","\+",$s);
		$s = str_replace("&","\&",$s);
		$s = str_replace("=","\=",$s);
		$s = str_replace("!","\!",$s);
        return $s;
    }
    static function unescape($P) {
    	if (is_array($P))$s = $P[0];
		else $s = $P;
		$s = str_replace("\+","+",$s);
		$s = str_replace("\&","&",$s);
		$s = str_replace("\=","=",$s);
		$s = str_replace("\!","!",$s);
        return $s;
    }
    static function checkSyntaxe($P){
    	if (is_array($P))$chaine = $P[0];
	else $chaine = $P;
	$chaine=utf8_decode($chaine);
	$chaine=stripslashes($chaine);
	$chaine = preg_replace('`\s+`', '-', trim($chaine));
	$chaine = str_replace("'", "-", $chaine);
	$chaine = str_replace("&", "et", $chaine);
	$chaine = str_replace('"', "-", $chaine);
	$chaine = str_replace("?", "", $chaine);
	$chaine = str_replace("!", "", $chaine);
	$chaine = str_replace(".", "", $chaine);
	$chaine = preg_replace('`[\,\ \(\)\+\'\/\:]`', '-', trim($chaine));
	$chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-");
	$chaine = preg_replace('`[-]+`', '-', trim($chaine));
	$chaine =  utf8_encode($chaine);
	//ON verifie qu il n existe pas deja une entité avec la meme url
	$Suffixe=(isset($Obj["Id"]))?"&Id!=".$Obj["Id"]:"";
	$modif = false;
	$chaine = preg_replace('`[\/]`', '-', trim($chaine));
	return $chaine;
    }
    static function  addslashes($P) {
	return addslashes($P[0]);
}
}

