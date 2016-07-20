<?php
class charUtils extends Beacon {

	function charUtils() {
	}
	function Affich() {
		$this->Data = (!empty($this->Data)||$this->Data===0)?$this->Data:Parser::getContent($this->ChildObjects);
		switch ($this->Beacon) {
			case "URL":
			 $this->Data = str_replace("'","&quot;",$this->Data);
			 if($this->Vars=="UN") $this->Data = urldecode($this->Data);
			 else{
			 	if ($this->Vars!="HTML")$this->Data = urlencode($this->Data);
				else $this->Data = urlencode(utf8_encode(htmlentities($this->Data)));

				//$this->Data = str_replace("%20","+",$this->Data);
			}
			break;
			case "JS":
				$this->Data = Parser::PostProcessing($this->Data);
				$this->Data = str_replace("’" , "'", $this->Data);
				$this->Data = str_replace("‘" , "'", $this->Data);
				$this->Data = str_replace("“" , '"', $this->Data);
				$this->Data = str_replace("”" , '"', $this->Data);
				$this->Data = utf8_encode(addslashes(htmlentities(utf8_decode($this->Data))));
			break;
			case "JSON":
				$this->Data = Parser::PostProcessing($this->Data);
				$this->Data = str_replace("\r" , ' ', $this->Data);
				$this->Data = str_replace("\n" , ' ', $this->Data);
				$this->Data = str_replace("\t" , ' ', $this->Data);
				$this->Data = str_replace("’" , "'", $this->Data);
				$this->Data = str_replace("‘" , "'", $this->Data);
				$this->Data = str_replace("“" , '"', $this->Data);
				$this->Data = str_replace("”" , '"', $this->Data);
				$this->Data = str_replace('"' , '\"', $this->Data);
			break;
			case "CONCAT":
				$this->Data = Parser::PostProcessing($this->Data);
				$this->Data = str_replace("\r\n" , " ", $this->Data);
				$this->Data = str_replace("\n" , " ", $this->Data);
				$this->Data = str_replace("\r" , ' ', $this->Data);
			break;
			case "SUBSTR":
				// Limite
				$temp = explode("|",$this->Vars);
				$limit_chars = $temp[0];
				$this->Data = strip_tags($this->Data);
				$size = mb_strlen($this->Data);
				$DataTemp = $this->Data;
				if($limit_chars != 0&&$limit_chars<$size) :
					$pos = strpos($this->Data, ' ', $limit_chars);
					if($pos !== FALSE) :
						$DataTemp = mb_substr($this->Data, 0, $pos + 1);
					endif;
					if ($DataTemp!=$this->Data) $DataTemp .= (isset($temp[1]) ? $temp[1] : '...');
				endif;
				$this->Data = $DataTemp;
			break;
			case "RANDOM":
				$password='';
				// Initialisation des caract�res utilisables
				$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
				for($i=0;$i<$this->Data;$i++)
				{
					$password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : 	$characters[array_rand($characters)];
				}
				$this->Data = $password;
			break;
			case "DATE":
				// Initialisation des caract�res utilisables
				if ($this->Data=="")$this->Data=0;
				if ($this->Vars)$this->Data;
				if (strlen($this->Data))$this->Data = @date($this->Vars,$this->Data);
				//if (strlen($this->Data))$this->Data = @date("d/m/Y",$this->Data);
			break;

			case "HIGHLIGHT":
				$this->Data = preg_replace("#(".$this->Vars.")#i","<span class='searchHighlight'>$1</span>",$this->Data);
				//$this->Data = str_ireplace($this->Vars,"<span class='searchHighlight'>".$this->Vars."</span>",$this->Data);
			break;
			case "UTIL":
				// Initialisation des caract�res utilisables
				switch ($this->Vars) {
					case "ADDSLASHES":
						$this->Data = addslashes($this->Data);
					break;
					case "STRIPSLASHES":
						$this->Data = stripslashes($this->Data);
					break;
					case "URL":
						$this->Data = urlencode($this->Data);
						break;
					case "MD5":
						$this->Data = md5($this->Data);
					break;
					case "PARSE":
						$this->Data = str_replace('[#','[!',$this->Data);
						$this->Data = str_replace('#]','!]',$this->Data);
						$this->Data = Process::processingVars($this->Data);
					break;
					case "LIGHTDATEFR":
					case "FULLDATEFR":
						$Day = date("w",$this->Data);
						switch ($Day)
						{
							case 0: $Day="Dimanche";
							break;
							case 1: $Day="Lundi";
							break;
							case 2: $Day="Mardi";
							break;
							case 3: $Day="Mercredi";
							break;
							case 4: $Day="Jeudi";
							break;
							case 5: $Day="Vendredi";
							break;
							case 6: $Day="Samedi";
							break;
						}
						$Month = date("m",$this->Data);
						switch ($Month)
						{
							case "01": $Month="Janvier";
							break;
							case "02": $Month="F&eacute;vrier";
							break;
							case "03": $Month="Mars";
							break;
							case "04": $Month="Avril";
							break;
							case "05": $Month="Mai";
							break;
							case "06": $Month="Juin";
							break;
							case "07": $Month="Juillet";
							break;
							case "08": $Month="Ao&ucirc;t";
							break;
							case "09": $Month="Septembre";
							break;
							case "10": $Month="Octobre";
							break;
							case "11": $Month="Novembre";
							break;
							case "12": $Month="Decembre";
							break;
						}
						$NumDay = date("d",$this->Data);
						$Year = date("Y",$this->Data);
						if($this->Vars == "LIGHTDATEFR") $this->Data = $NumDay." ".$Month." ".$Year;
						else $this->Data = $Day." ".$NumDay." ".$Month." ".$Year;
					break;
					case "NUMERICDATE":
						$this->Data = date("d/m/y", $this->Data);
					break;
					case "TOTMS":
						$TabDate = explode("/",$this->Data);
							$this->Data = mktime(0,0,0,$TabDate[1],$TabDate[0],$TabDate[2]);
					break;
					case "HOUR":
						$this->Data = date("G:i",$this->Data);
					break;
					case "MONTH":
						switch ($this->Data)
						{
							case 1: $Month="Janvier";
							break;
							case 2: $Month="Février";
							break;
							case 3: $Month="Mars";
							break;
							case 4: $Month="Avril";
							break;
							case 5: $Month="Mai";
							break;
							case 6: $Month="Juin";
							break;
							case 7: $Month="Juillet";
							break;
							case 8: $Month="Août";
							break;
							case 9: $Month="Septembre";
							break;
							case 10: $Month="Octobre";
							break;
							case 11: $Month="Novembre";
							break;
							case 12: $Month="Décembre";
							break;
						}
						$this->Data=$Month;
					break;
					case "BBCODE":
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
							$this->Data = preg_replace('!\['.$BBCode[$i]["Name"].'\](.*?)\[/'.$BBCode[$i]["Name"].'\]!is', '<span class="bb_'.$BBCode[$i]["Class"].'">$1</span>', $this->Data);
						}
						$BBCode = Array();
						$BBCode[] = Array("Name"=>"i","Beacon"=>"em");
						$BBCode[] = Array("Name"=>"b","Beacon"=>"strong");
						for ($i=0;$i<count($BBCode);$i++){
							$this->Data = preg_replace('!\['.$BBCode[$i]["Name"].'\](.*?)\[/'.$BBCode[$i]["Name"].'\]!is', '<'.$BBCode[$i]["Beacon"].'>$1</'.$BBCode[$i]["Beacon"].'>', $this->Data);
						}
						//Gestion des tables
						$this->Data = preg_replace('!\[table(.*?)](.*?)\[/table\]!is', '<table$1>$2</table>', $this->Data);
						$this->Data = preg_replace('!\[tr(.*?)](.*?)\[/tr\]!is', '<tr$1>$2</tr>', $this->Data);
						$this->Data = preg_replace('!\[td(.*?)](.*?)\[/td\]!is', '<td$1>$2</td>', $this->Data);
						$this->Data = preg_replace('!\[thead(.*?)](.*?)\[/thead\]!is', '<thead$1>$2</thead>', $this->Data);
						$this->Data = preg_replace('!\[tbody(.*?)](.*?)\[/tbody\]!is', '<tbody$1>$2</tbody>', $this->Data);
						$this->Data = preg_replace('!\[p(.*?)](.*?)\[/p\]!is', '<p$1>$2</p>', $this->Data);
						//Gestion des balises url
						#DEPRECATED
						$url = "urlt";
						$this->Data = preg_replace('`\['.$url.'\=(.*?)\|(.*?)\](.*?)\[\/'.$url.'\]`s', '<a href="$2"  class="bb_a_url" title="$1">$3</a>', $this->Data);
						#DEPRECATED
						$this->Data = preg_replace('`\[url\=([^\]^\|]*?)\|([^\]^\|]*?)\|([^\]^\|]*?)\|([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url $4" title="$2" rel="$3" >$5</a>', $this->Data);
						$this->Data = preg_replace('`\[url\=([^\]^\|]*?)\|([^\]^\|]*?)\|([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" title="$2" rel="$3" >$4</a>', $this->Data);
						$this->Data = preg_replace('`\[url\=([^\]^\|]*?)\|([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" title="$2" >$3</a>', $this->Data);
						$this->Data = preg_replace('`\[url\=([^\]^\|]*?)\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" >$2</a>', $this->Data);
						$this->Data = preg_replace('`\['.$url.'\](.*?)\[\/'.$url.'\]`s', '<a href="$1" class="bb_a_url">$1</a>', $this->Data);
						$this->Data = preg_replace('`\[url\](.*?)\[\/url\]`s', '<a href="$1" class="bb_a_url" target="_blank">$1</a>', $this->Data);
						//Gestion des balises img
						$url = "img";
						$this->Data = preg_replace('`\['.$url.'\](.*?)\[\/'.$url.'\]`', '<a href="$1" class="mb" style="text-decoration:none;display:block;margin-top:15px;"><img src="$1" class="bb_img" alt=""/></a>', $this->Data);
						//Gestion des balises mail
						$mail = "email";
						$this->Data = preg_replace('`\['.$mail.'=(.*?)\](.+?)\[\/'.$mail.'\]`', '<a class="bb_a_mail"  href="mailto:$1">$2</a>', $this->Data);
						$this->Data = preg_replace('`\['.$mail.'\](.*?)\[\/'.$mail.'\]`', '<a class="bb_a_mail" href="mailto:$1">$1</a>', $this->Data);
						//Gestion des alignements
						$this->Data = preg_replace('`\[align=(.+?)\](.*?)\[\/align\]`s', '<span class="bb_align_$1>$2</span>', $this->Data);
						//Gestion des couleurs
						$this->Data = preg_replace('`\[color=(.+?)\](.*?)\[\/color\]`s', '<span class="bb_coloured"  style="color:$1">$2</span>', $this->Data);
						//Gestion des listes
/*						$this->Data = preg_replace('!\[list\=square\](.*?)\[/list\]!is', '<ul class="bb_ul">$1</ul>', $this->Data);
						$this->Data = preg_replace('!\[list\=decimal\](.*?)\[/list\]!is', '<ol class="bb_ol">$1</ol>', $this->Data);*/
						//$this->Data = preg_replace('!\[list\](.+?)\[/list\]!is', '<ul class="bb_ul">$1</ul>', $this->Data);
// 						$this->Data = preg_replace('!\[numlist\](.*?)\[/numlist\]!is', '<ol class="bb_ol">$1</ol>', $this->Data);
						$this->Data = preg_replace('!\[list.*?\]!is', '<ul class="bb_ul">', $this->Data);
						$this->Data = preg_replace('!\[list\]!is', '<ul class="bb_ul">', $this->Data);
						$this->Data = preg_replace('!\[/list\]!is', '</ul>', $this->Data);
						$this->Data = preg_replace('!\[list\=square\]!is', '<ul class="bb_ul">', $this->Data);
//						$this->Data = preg_replace('!\[item\](.*?)\[/item\]!is', '<li class="bb_li"><span>$1</span></li>', $this->Data);
						$this->Data = preg_replace('#\[\*\](((?!\[\*\]|!\[\/list\]).)*)#is', '<li class="bb_li"><span>$1</span></li>', $this->Data);
						$this->Data = preg_replace('!\[item\]!is', '<li class="bb_li"><span>', $this->Data);
						$this->Data = preg_replace('!\[/item\]!is', '</span></li>', $this->Data);
						 //Gestion de la taille des caractères
						$this->Data = preg_replace('`\[size=(.*?)\](.+?)\[\/size\]`s', '<span style="font-size:$1px;">$2</span>', $this->Data);
						//Gestion de la couleur de la police
						//$this->Data = preg_replace('`\[color=(.*?)\](.+?)\[\/color\]`s', '<span style="color:$1;">$2</span>', $this->Data);
						//Gestion du texte: en exposant
						$this->Data = preg_replace('`\[sup\](.*?)\[\/sup\]`', '<span style="vertical-align:80%;">$1</span>', $this->Data);
						//Gestion du texte: en indice
						$this->Data = preg_replace('`\[sub\](.*?)\[\/sub\]`', '<span style="vertical-align:sub;">$1</span>', $this->Data);
						//Gestion des titres
						$this->Data = preg_replace('`\[h1\](.*?)\[\/h1\]`', '<div class="bb_h1"><h1><span>$1</span></h1></div>', $this->Data);
						$this->Data = preg_replace('`\[h2\](.*?)\[\/h2\]`', '<div class="bb_h2"><h2><span>$1</span></h2></div>', $this->Data);
						$this->Data = preg_replace('`\[h3\](.*?)\[\/h3\]`', '<div class="bb_h3"><h3><span>$1</span></h3></div>', $this->Data);
// 						//$this->Data = str_replace('&', '&amp;', $this->Data); //EM 14032013
						$this->Data =nl2br($this->Data);
						//$this->Data = $this->char($this->Data);
					break;
					case "CodeCom":
						$Tab=$GLOBALS['Systeme']->Modules["Boutique"]->callData("Boutique/Commande","",0,1,"","","COUNT(DISTINCT(m.Id))");
						$Nb=$Tab[0]["COUNT(DISTINCT(m.Id))"];
						$ref = "000000".$Nb;
						$ref = mb_substr($ref,-5,5);
						$this->Data =  'COM-'.mb_substr($ref,0,3)."-".mb_substr($ref,3,2);
					break;
					case "CodeFac":
						$Tab=$GLOBALS['Systeme']->Modules["Boutique"]->callData("Boutique/Facture","",0,1,"","","COUNT(DISTINCT(m.Id))");
						$Nb=$Tab[0]["COUNT(DISTINCT(m.Id))"];
						$ref = "000000".$Nb;
						$ref = mb_substr($ref,-5,5);
						$this->Data =  'FAC-'.mb_substr($ref,0,3)."-".mb_substr($ref,3,2);
					break;
					case "HTML":
						$this->Data = Parser::PostProcessing($this->Data);
						$this->Data = html_entity_decode($this->Data);
					break;
					case "NOHTML":
                        $this->Data = preg_replace("#p {.*?}#","",$this->Data);
                        $this->Data = preg_replace("#a:link {.*?}#","",$this->Data);
						$this->Data = Parser::PostProcessing($this->Data);
						$this->Data = utf8_encode(html_entity_decode(strip_tags($this->Data)));
					break;
					case "NOHTTP":
						$this->Data = preg_replace('#^http://(.*)$#','$1',$this->Data);
						$this->Data = preg_replace('#\/$#','',$this->Data);
					break;
					case "SPECIALCHARS":
						$this->Data = htmlspecialchars($this->Data);
					break;
					case "UTF8DECODE":
						$this->Data = stripslashes(utf8_decode($this->Data));
					break;

					case "SANSCOTEESPACE":
						$this->Data = str_replace(' ',"_",str_replace('"',"_",str_replace("'","_",$this->Data)));
					break;
					case "SANSCOTE":
						$this->Data = str_replace('"'," ",$this->Data);
					break;

					case "NUMBER":
						$this->Data = $this->format_number($this->Data,' ','.',0);
					break;

					case "PRICE":
						$this->Data = $this->format_number($this->Data,',','.',3);
					break;
					case "NL2BR":
						$this->Data = nl2br($this->Data);
					break;

					case "STRTOUPPER":
						$chaine=utf8_decode($this->Data);
						$this->Data=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
					break;

				}
			break;
			case "LOG":

				$GLOBALS["Systeme"]->Log->log($this->Data,$this->Vars);
				$this->Data = "";
			break;
		}
		return $this->Data;
	}
	function format_number($nb,$dec,$sep,$nbdec){
		$nombre = number_format($nb, $nbdec, $dec, $sep);
		return ("$nombre");
	}


}


?>
