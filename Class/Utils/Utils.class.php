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
	static function  getTodayEvening($P) {
		$h=23;
		$m=59;
		$s=59;
		$M=date('m');
		$J=date('d');
		$A=date('Y');
		return mktime($h,$m,$s,$M,$J,$A);
	}
	static function  getTodayMorning($P) {
		$h=0;
		$m=0;
		$s=0;
		$M=date('m');
		$J=date('d');
		$A=date('Y');
		return mktime($h,$m,$s,$M,$J,$A);
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
		return json_decode($P,true);
	}
    static function jsonEncode($P){
	    if(!is_string($P[0]))
            return json_encode($P[0]);

        $P = implode(",",$P);
        return json_encode($P,true);
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
		if (is_array($P))$P=$P[0];
        $tmp =  str_replace('/', '@!#!@', $P);
        $tmp =  str_replace('&', '@!##!@', $tmp);
		$tmp =  str_replace(' ', '@!###!@', $tmp);
        $tmp =  str_replace('+', '@!####!@', $tmp);
		$tmp =  str_replace('=', '@!#####!@', $tmp);
        return $tmp;
	}

	static function KEStripSlashes($P) {
		if (is_array($P))$P=$P[0];
        $tmp =  str_replace('@!#!@', '/', $P);
        $tmp = str_replace('@!##!@', '&', $tmp);
		$tmp =  str_replace('@!###!@',' ', $tmp);
        $tmp =  str_replace('@!####!@','+', $tmp);
		$tmp =  str_replace('@!#####!@','+', $tmp);
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

    static function UrlEncode($P) {
	    $c = implode(',',$P);
        return urlencode($c);
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
        $chaine = preg_replace('`[\/]`', '-', trim($chaine));
        return $chaine;
    }
    static function strToCode($P){
        if (is_array($P))$chaine = $P[0];
        else $chaine = $P;
        $chaine=utf8_decode($chaine);
        $chaine=stripslashes($chaine);
        $chaine = preg_replace('`\s+`', '', trim($chaine));
        $chaine = str_replace("'", "", $chaine);
        $chaine = str_replace("&", "et", $chaine);
        $chaine = str_replace('"', "", $chaine);
        $chaine = str_replace("?", "", $chaine);
        $chaine = str_replace("!", "", $chaine);
        $chaine = str_replace(".", "", $chaine);
        $chaine = preg_replace('`[\,\ \(\)\+\'\/\:]`', '', trim($chaine));
        $chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-");
        $chaine = preg_replace('`[-]+`', '', trim($chaine));
        $chaine =  utf8_encode($chaine);
        $chaine = preg_replace('`[\/]`', '', trim($chaine));
        $chaine = strtolower(trim($chaine));
        return $chaine;
    }
    static function  addslashes($P) {
        return addslashes($P[0]);
    }
	//Nécéssaire pour pouvoir ajouter des fonctions à la classe "à la volée"
	public static function __callStatic($method, $args)
	{
		if (method_exists('UtilsExtends',$method)) { //Alternative : is_callable
		    $func = 'UtilsExtends::'.$method;
		    return call_user_func_array($func, $args);
		}
		return false;
	}
	public static function cleanJson($text){
        if (is_array($text))
            $text = implode(',',$text);
        $text = htmlspecialchars($text);
        $text = str_replace("\\" , "\\\\", $text);
        $text = str_replace("\r" , "\\\\r", $text);
        $text = str_replace("\n" , "\\\\n", $text);
        $text = str_replace("\t" , "\\t", $text);
        $text = str_replace("’" , "'", $text);
        $text = str_replace("‘" , "'", $text);
        $text = str_replace("“" , '"', $text);
        $text = str_replace("”" , '"', $text);
        $text = str_replace('"' , '\"', $text);
        $text = str_replace('\:' , ":", $text);
        //$text = str_replace("&" , '\u0026', $text);
        return $text;
    }
    public static function  isNull($P) {
        return is_null($P[0]);
    }
    public static function  sprintf($P) {
        return sprintf($P[0],$P[1]);
    }
    public static function genererCode(){
        $cars="az0erty2ui3op4qs_5df6gh7jk8lm9wxcvbn-";
        $wlong=strlen($cars);
        $wpas="";
        $taille=12;
        srand((double)microtime()*1000000);
        for($i=0;$i<$taille;$i++){
            $wpos=rand(0,$wlong-1);
            $wpas=$wpas.substr($cars,$wpos,1);
        }
        return $wpas;
    }
    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
    public static function unBBCode( $data){
        $BBCode = Array();
        $BBCode[] = Array("Name"=>"i","Class"=>"italic");
        $BBCode[] = Array("Name"=>"b","Class"=>"bold");
        $BBCode[] = Array("Name"=>"u","Class"=>"underline");
        $BBCode[] = Array("Name"=>"s","Class"=>"underline");
        $BBCode[] = Array("Name"=>"barre","Class"=>"strike");
        $BBCode[] = Array("Name"=>"quote","Class"=>"quote");
        $BBCode[] = Array("Name"=>"spoiler","Class"=>"spoiler");
        $BBCode[] = Array("Name"=>"uppercase","Class"=>"uppercase");
        $BBCode[] = Array("Name"=>"lowercase","Class"=>"lowercase");
        for ($i=0;$i<count($BBCode);$i++){
            $data = preg_replace('!\['.$BBCode[$i]["Name"].'\](.*?)\[/'.$BBCode[$i]["Name"].'\]!is', '<span class="bb_'.$BBCode[$i]["Class"].'">$1</span>', $data);
        }
        $BBCode = Array();
        $BBCode[] = Array("Name"=>"i","Beacon"=>"em");
        $BBCode[] = Array("Name"=>"b","Beacon"=>"strong");
        for ($i=0;$i<count($BBCode);$i++){
            $data = preg_replace('!\['.$BBCode[$i]["Name"].'\](.*?)\[/'.$BBCode[$i]["Name"].'\]!is', '<'.$BBCode[$i]["Beacon"].'>$1</'.$BBCode[$i]["Beacon"].'>', $data);
        }
        //Gestion des tables
        $data = preg_replace('!\[table(.*?)](.*?)\[/table\]!is', '<table$1>$2</table>', $data);
        $data = preg_replace('!\[tr(.*?)](.*?)\[/tr\]!is', '<tr$1>$2</tr>', $data);
        $data = preg_replace('!\[td(.*?)](.*?)\[/td\]!is', '<td$1>$2</td>', $data);
        $data = preg_replace('!\[thead(.*?)](.*?)\[/thead\]!is', '<thead$1>$2</thead>', $data);
        $data = preg_replace('!\[tbody(.*?)](.*?)\[/tbody\]!is', '<tbody$1>$2</tbody>', $data);
        $data = preg_replace('!\[p(.*?)](.*?)\[/p\]!is', '<p$1>$2</p>', $data);
        //Gestion des balises url
        #DEPRECATED
        $url = "urlt";
        $data = preg_replace('`\['.$url.'\=(.*?)\|(.*?)\](.*?)\[\/'.$url.'\]`s', '<a href="$2"  class="bb_a_url" title="$1">$3</a>', $data);
        #DEPRECATED
        $data = preg_replace('`\[url\=([^\]^\|]*?)\|([^\]^\|]*?)\|([^\]^\|]*?)\|([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url $4" title="$2" rel="$3" >$5</a>', $data);
        $data = preg_replace('`\[url\=([^\]^\|]*?)\|([^\]^\|]*?)\|([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" title="$2" rel="$3" >$4</a>', $data);
        $data = preg_replace('`\[url\=([^\]^\|]*?)\|([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" title="$2" >$3</a>', $data);
        $data = preg_replace('`\[url\=([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" >$2</a>', $data);
        $data = preg_replace('`\['.$url.'\](.*?)\[\/'.$url.'\]`s', '<a href="$1" class="bb_a_url">$1</a>', $data);
        $data = preg_replace('`\[url\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" target="_blank">$1</a>', $data);
        //Gestion des balises img
        $url = "img";
        $data = preg_replace('`\['.$url.'\](.*?)\[\/'.$url.'\]`', '<a href="$1" class="mb" style="text-decoration:none;display:block;margin-top:15px;"><img src="$1" class="bb_img" alt=""/></a>', $data);
        //Gestion des balises mail
        $mail = "email";
        $data = preg_replace('`\['.$mail.'=(.*?)\](.+?)\[\/'.$mail.'\]`', '<a class="bb_a_mail"  href="mailto:$1">$2</a>', $data);
        $data = preg_replace('`\['.$mail.'\](.*?)\[\/'.$mail.'\]`', '<a class="bb_a_mail" href="mailto:$1">$1</a>', $data);
        //Gestion des alignements
        $data = preg_replace('`\[align=(.+?)\](.*?)\[\/align\]`s', '<span class="bb_align_$1>$2</span>', $data);
        //Gestion des couleurs
        $data = preg_replace('`\[color=(.+?)\](.*?)\[\/color\]`s', '<span class="bb_coloured"  style="color:$1">$2</span>', $data);
        //Gestion des listes
        /*						$data = preg_replace('!\[list\=square\](.*?)\[/list\]!is', '<ul class="bb_ul">$1</ul>', $data);
                                $data = preg_replace('!\[list\=decimal\](.*?)\[/list\]!is', '<ol class="bb_ol">$1</ol>', $data);*/
        //$data = preg_replace('!\[list\](.+?)\[/list\]!is', '<ul class="bb_ul">$1</ul>', $data);
// 						$data = preg_replace('!\[numlist\](.*?)\[/numlist\]!is', '<ol class="bb_ol">$1</ol>', $data);
        $data = preg_replace('!\[list.*?\]!is', '<ul class="bb_ul">', $data);
        $data = preg_replace('!\[list\]!is', '<ul class="bb_ul">', $data);
        $data = preg_replace('!\[/list\]!is', '</ul>', $data);
        $data = preg_replace('!\[list\=square\]!is', '<ul class="bb_ul">', $data);
//						$data = preg_replace('!\[item\](.*?)\[/item\]!is', '<li class="bb_li"><span>$1</span></li>', $data);
        $data = preg_replace('#\[\*\](((?!\[\*\]|!\[\/list\]).)*)#is', '<li class="bb_li"><span>$1</span></li>', $data);
        $data = preg_replace('!\[item\]!is', '<li class="bb_li"><span>', $data);
        $data = preg_replace('!\[/item\]!is', '</span></li>', $data);
        //Gestion de la taille des caractères
        $data = preg_replace('`\[size=(.*?)\](.+?)\[\/size\]`s', '<span style="font-size:$1px;">$2</span>', $data);
        //Gestion de la couleur de la police
        //$data = preg_replace('`\[color=(.*?)\](.+?)\[\/color\]`s', '<span style="color:$1;">$2</span>', $data);
        //Gestion du texte: en exposant
        $data = preg_replace('`\[sup\](.*?)\[\/sup\]`', '<span style="vertical-align:80%;">$1</span>', $data);
        //Gestion du texte: en indice
        $data = preg_replace('`\[sub\](.*?)\[\/sub\]`', '<span style="vertical-align:sub;">$1</span>', $data);
        //Gestion des titres
        $data = preg_replace('`\[h1\](.*?)\[\/h1\]`', '<div class="bb_h1"><h1><span>$1</span></h1></div>', $data);
        $data = preg_replace('`\[h2\](.*?)\[\/h2\]`', '<div class="bb_h2"><h2><span>$1</span></h2></div>', $data);
        $data = preg_replace('`\[h3\](.*?)\[\/h3\]`', '<div class="bb_h3"><h3><span>$1</span></h3></div>', $data);
// 						//$data = str_replace('&', '&amp;', $data); //EM 14032013
        $data =nl2br($data);
        //$data = $this->char($data);

        return $data;
    }

}

@include 'Utils.extend.php';


