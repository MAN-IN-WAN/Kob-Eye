<?php

class fileDriver extends ObjectClass {

	var $AUTO_INCREMENT = "AUTO_INCREMENT";
	/* ---------------------------------
	 |            PUBLIQUE            |
	 ---------------------------------*/

	function initData () {
		//Aucune data a initialiser pour dossiers et fichiers.
	}

	function file_exists_i($file)
	{
		$r = glob($file);
		if (empty($r)) return false;
		else return true;
	}

	function getCaseSensitiveName($file){
		$r = glob($file);
		$r = preg_replace("#^\.\/#","",$r);
		if (empty($r)) return false;
		else return $r[0];
	}

	function Purge() {
		//Aucun champ a purger, car on ne peut pas en creer
	}

	function LimitSize($OrigAdresse,$Dossier,$Nom,$Params,$Out)
	{
		session_write_close();
		//On verifie l'existence d'un fichier redimensionne
		//$TabNom = explode(".",$Nom);
		preg_match("#([A-z\/\-0-9\@\*\.]+)\.([A-z0-9]+)$#",$Nom,$out);
		$TabNom[0] = $out[1];
		$TabNom[1] = strtolower($out[2]);
		$d = explode("x",$Params);
		$Largeur = $d[0];
		$Hauteur = $d[1];
		$Sec = isset($d[2]) ? $d[2] : 4;
		$Dir = $Dossier;
		if (!file_exists($Dir)) Root::mk_dir($Dir);
		//$RenamedOAdr = str_replace("/","_",$Dossier);
		//$Name = "/".$TabNom[0].".mini.".$Largeur."x".$Hauteur.$RenamedOAdr.".".$GLOBALS["Systeme"]->type;
		$Name = "/".$Nom.".limit.".$Params.".".$TabNom[1];
		
		if($this->imageOverQuota($Dossier,'/'.$Nom."\.limit\.[0-9]+x[0-9]+\.".$Out.'/'))	
			return false;
		
		if (file_exists($Dir.$Name)) return $Dir.$Name;
		//On verifie l'existence du fichier original
		if (!file_exists($OrigAdresse)) return false;
		
//		if ($TabNom[1]=="jpg"||$TabNom[1]=="jpeg"){
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			$Img = imagecreatefromjpeg($OrigAdresse);
//		}elseif ($TabNom[1]=="gif"){
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			 $Img = imagecreatefromgif($OrigAdresse);
//		}elseif ($TabNom[1]=="bmp"){
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			 $Img = imagecreatefrombmp($OrigAdresse);
//		}elseif ($TabNom[1]=="png") {
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			$Img = imagecreatefrompng($OrigAdresse);
//		}
		if(strpos(',jpg,jpeg,gif,bmp,png,', ','.$TabNom[1].',') !== false) {
			$Img = false;
			$image_data = file_get_contents($OrigAdresse);
			try {
				$Img = imagecreatefromstring($image_data);
				$Dimensions = getimagesize($OrigAdresse);
			} catch (Exception $ex) {
				return false;
			}
		}
		elseif ($TabNom[1]=="flv"||$TabNom[1]=="f4v"||$TabNom[1]=="mov"||$TabNom[1]=="avi") {
			$FrameTemp = $OrigAdresse.".frame.".$Sec.".jpg";
			if (!file_exists($FrameTemp)){
				//Extraction de la frame
				exec("ffmpeg -i ".$OrigAdresse." -vframes 1 -an -vcodec mjpeg -f rawvideo -ss ".$Sec." ".$FrameTemp);
				return false;
			}else{
				$Dimensions = getimagesize($FrameTemp);
				$Img = imagecreatefromjpeg($FrameTemp);
			}
		}else return false;
		//On calcule le redimensionnement sans anamorphoser la photo
		if ($Dimensions[1]>$Hauteur || $Dimensions[0]>$Largeur){
			if ($Dimensions[1]<$Dimensions[0]){
				//Cas Paysage
				$Rapport = $Largeur/$Dimensions[0];
				if ($Rapport*$Dimensions[1]>$Hauteur){
					$Rapport = $Hauteur/$Dimensions[1];
				}
			}else{
				//Cas Portrait
				$Rapport = $Hauteur/$Dimensions[1];
				if ($Rapport*$Dimensions[0]>$Largeur){
					$Rapport = $Largeur/$Dimensions[0];
				}
			}
			$Width  = $Dimensions[0] * $Rapport;
			$Height  = $Dimensions[1] * $Rapport;
		}else{
			$Width  = $Dimensions[0];
			$Height  = $Dimensions[1];
		}
		$Caneva = imagecreatetruecolor($Width,$Height);
		if($Out=="png") {
			imageAlphaBlending($Caneva, false);
			imageSaveAlpha($Caneva, true);
		}
		imagecopyresampled($Caneva,$Img,0,0,0,0,$Width,$Height,$Dimensions[0],$Dimensions[1]);
		imagedestroy($Img);
		if ($Out=="jpg"||$Out=="jpeg") $Test = imagejpeg($Caneva,$Dir.$Name,95);
		elseif ($Out=="gif") $Test = imagegif($Caneva,$Dir.$Name);
		elseif ($Out=="png") {
			$Test = imagepng($Caneva,$Dir.$Name);
		}
		imagedestroy($Caneva);
		return ($Test) ? $Dir.$Name : false;
	}

	function ScaleSize($OrigAdresse,$Dossier,$Nom,$Params,$Out)
	{
		session_write_close();
		//On verifie l'existence d'un fichier redimensionne
		//$TabNom = explode(".",$Nom);
		preg_match("#([A-z\/\-0-9\@\*\.]+)\.([A-z0-9]+)$#",$Nom,$out);
		$TabNom[0] = $out[1];
		$TabNom[1] = strtolower($out[2]);
		$d = explode("x",$Params);
		$Largeur = $d[0];
		$Hauteur = $d[1];
		$Sec = isset($d[2]) ? $d[2] : 4;
		$Dir = $Dossier;
		if (!file_exists($Dir)) Root::mk_dir($Dir);
		//$RenamedOAdr = str_replace("/","_",$Dossier);
		//$Name = "/".$TabNom[0].".mini.".$Largeur."x".$Hauteur.$RenamedOAdr.".".$GLOBALS["Systeme"]->type;
		$Name = "/".$Nom.".scale.".$Params.".".$TabNom[1];
		
		if($this->imageOverQuota($Dossier,'/'.$Nom."\.scale\.[0-9]+x[0-9]+\.".$Out.'/'))	
			return false;
		
//		if (file_exists($Dir.$Name)) return $Dir.$Name;
//		//On verifie l'existence du fichier original
//		if (!file_exists($OrigAdresse)) return false;
//		if ($TabNom[1]=="jpg"||$TabNom[1]=="jpeg"){
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			$Img = imagecreatefromjpeg($OrigAdresse);
//		}elseif ($TabNom[1]=="gif"){
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			$Img = imagecreatefromgif($OrigAdresse);
//		}elseif ($TabNom[1]=="bmp"){
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			$Img = imagecreatefrombmp($OrigAdresse);
//		}elseif ($TabNom[1]=="png") {
//			//On commence le travail de l'image
//			$Dimensions = getimagesize($OrigAdresse);
//			$Img = imagecreatefrompng($OrigAdresse);
//		}
		if(strpos(',jpg,jpeg,gif,bmp,png,', ','.$TabNom[1].',') !== false) {
			$Img = false;
			$image_data = file_get_contents($OrigAdresse);
			try {
				$Img = imagecreatefromstring($image_data);
				$Dimensions = getimagesize($OrigAdresse);
			} catch (Exception $ex) {
				return false;
			}
		}
		elseif ($TabNom[1]=="flv"||$TabNom[1]=="f4v"||$TabNom[1]=="mov"||$TabNom[1]=="avi") {
			$FrameTemp = $OrigAdresse.".frame.".$Sec.".jpg";
			if (!file_exists($FrameTemp)){
				//Extraction de la frame
				exec("ffmpeg -i ".$OrigAdresse." -vframes 1 -an -vcodec mjpeg -f rawvideo -ss ".$Sec." ".$FrameTemp);
				return false;
			}else{
				$Dimensions = getimagesize($FrameTemp);
				$Img = imagecreatefromjpeg($FrameTemp);
			}
		}else return false;
		//On calcule le redimensionnement sans anamorphoser la photo
		if($Largeur>=$Hauteur) {
			$Rapport = $Hauteur / $Dimensions[1];
			if(($Dimensions[0] * $Rapport)>$Largeur) $Rapport = $Largeur / $Dimensions[0];
		}
		else {
			$Rapport = $Largeur / $Dimensions[0];
			if(($Dimensions[1] * $Rapport)>$Hauteur) $Rapport = $Hauteur / $Dimensions[1];
		}
		$w = $Dimensions[0] * $Rapport;
		$h = $Dimensions[1] * $Rapport;
		if ($Dimensions[0]>$w || $Dimensions[1]>$h){
			$Width  = $w;
			$Height  = $h;
		}else{
			$Width  = $Dimensions[0];
			$Height  = $Dimensions[1];
		}
		$Caneva = imagecreatetruecolor($Width,$Height);
		if($Out=="png") {
			imageAlphaBlending($Caneva, false);
			imageSaveAlpha($Caneva, true);
		}
		imagecopyresampled($Caneva,$Img,0,0,0,0,$Width,$Height,$Dimensions[0],$Dimensions[1]);
		imagedestroy($Img);
		if ($Out=="jpg"||$Out=="jpeg") $Test = imagejpeg($Caneva,$Dir.$Name,95);
		elseif ($Out=="gif") $Test = imagegif($Caneva,$Dir.$Name);
		elseif ($Out=="png") {
			$Test = imagepng($Caneva,$Dir.$Name);
		}
		imagedestroy($Caneva);
		return ($Test) ? $Dir.$Name : false;
	}



	function Miniature($OrigAdresse,$Dossier,$Nom,$Params,$Out){
		session_write_close();
		//On verifie l'existence d'un fichier redimensionne
		preg_match("#([A-z\/\-0-9\@\*\.]+)\.([A-z0-9]+)$#",$Nom,$out);
		$TabNom[0] = $out[1];
		$d = explode("x",$Params);
		$Largeur = $d[0];
		$Hauteur = $d[1];
		$Sec = isset($d[2]) ? $d[2] : 4;
		$TabNom[1] = strtolower($out[2]);
		for ($i=1;$i<count($TabNom)-1;$i++) $TabNom[0] .= $TabNom[$i];
		$TabNom[1] = $TabNom[count($TabNom)-1];
		$Dir = $Dossier;
		if (!file_exists($Dir)) Root::mk_dir($Dir);
		$Name = "/".$Nom.".mini.".$Params.".".$Out;
		
		if($this->imageOverQuota($Dossier,'/'.$Nom."\.mini\.[0-9]+x[0-9]+\.".$Out.'/'))	
			return false;
		
		if (file_exists($Dir.$Name)) return $Dir.$Name;
		//On verifie l'existence du fichier original
		if (!file_exists($OrigAdresse)) return false;
		//On commence le travail de l'image
		if (!file_exists($OrigAdresse)) return false;
		//On prend l'image selon le type
		$Caneva = imagecreatetruecolor($Largeur,$Hauteur);
		$TabNom[1] = strtolower($TabNom[1]);
		if (exif_imagetype($OrigAdresse)==IMAGETYPE_JPEG){
			//On commence le travail de l'image
			$Dimensions = getimagesize($OrigAdresse);
			$Img = imagecreatefromjpeg($OrigAdresse);
            //$Img = imagecreatetruecolor($Largeur, $Hauteur);
        }elseif (exif_imagetype($OrigAdresse)==IMAGETYPE_GIF){
			//On commence le travail de l'image
			$Dimensions = getimagesize($OrigAdresse);
			 $Img = imagecreatefromgif($OrigAdresse);
		}elseif (exif_imagetype($OrigAdresse)==IMAGETYPE_PNG) {
			//On commence le travail de l'image
			$Dimensions = getimagesize($OrigAdresse);
			$Img = imagecreatefrompng($OrigAdresse);
		}elseif ($TabNom[1]=="flv"||$TabNom[1]=="f4v"||$TabNom[1]=="mp4"||$TabNom[1]=="mov"||$TabNom[1]=="avi") {
			$FrameTemp = $OrigAdresse.".frame.".$Sec.".jpg";
			if (!file_exists($FrameTemp)){
				//Extraction de la frame
				exec("ffmpeg -i ".$OrigAdresse." -vframes 1 -an -vcodec mjpeg -f rawvideo -ss ".$Sec." ".$FrameTemp);
				return false;
			}else{
				$Dimensions = getimagesize($FrameTemp);
				$Img = imagecreatefromjpeg($FrameTemp);
			}
		}else return false;

		//On calcule le redimensionnement sans anamorphoser la photo
        if ($Dimensions[1]>$Dimensions[0]){
            //Cas du paysage
            $Height = $Hauteur;
            $Width  = $Dimensions[0] * ($Hauteur/$Dimensions[1]);
            if ($Width<$Largeur)
            {
                $Width = $Largeur;
                $Height = $Dimensions[1] * ($Largeur/$Dimensions[0]);
            }
        }else{
            //Cas du portrait
            $Width  = $Largeur;
            $Height  = $Dimensions[1] * ($Largeur / $Dimensions[0]);
            if ($Height<$Hauteur)
            {
                $Height = $Hauteur;
                $Width = $Dimensions[0] * ($Hauteur/$Dimensions[1]);
            }
        }
		$TempCaneva = imagecreatetruecolor($Width,$Height);
		if($Out=="png") {
			imageAlphaBlending($TempCaneva, false);
			imageSaveAlpha($TempCaneva, true);
			imageAlphaBlending($Caneva, false);
			imageSaveAlpha($Caneva, true);
		}else{
			$kek=imagecolorallocate($TempCaneva, 255, 255, 255);
			imagefill($TempCaneva,0,0,$kek);
		}
		imagecopyresampled($TempCaneva,$Img,0,0,0,0,$Width,$Height,$Dimensions[0],$Dimensions[1]);
        //$Caneva = $TempCaneva;
		imagecopy($Caneva,$TempCaneva,0,0,($Width/2)-($Largeur/2),($Height/2)-($Hauteur/2),$Width,$Height);
		if ($Out=="jpg"||$Out=="jpeg") $Test = imagejpeg($Caneva,$Dir.$Name,95);
		elseif ($Out=="gif") $Test = imagegif($Caneva,$Dir.$Name);
		elseif ($Out=="png") $Test = imagepng($Caneva,$Dir.$Name);
		return ($Test) ? $Dir.$Name : false;
	}


	function ConvertImg($OrigAdresse,$Dossier,$Nom,$Params,$Out){
		session_write_close();
		//On verifie l'existence d'un fichier converti
		preg_match("#([A-z\/\-0-9\@\*\.]+)\.([A-z0-9]+)$#",$Nom,$out);
		$TabNom[0] = $out[1];
		$d = explode("x",$Params);
		$Largeur = $d[0];
		$Hauteur = $d[1];
		$Sec = isset($d[2]) ? $d[2] : 4;
		$TabNom[1] = strtolower($out[2]);
		for ($i=1;$i<count($TabNom)-1;$i++) $TabNom[0] .= $TabNom[$i];
		$TabNom[1] = $TabNom[count($TabNom)-1];
		$Dir = $Dossier;
		if (!file_exists($Dir)) Root::mk_dir($Dir);
		$Name = "/".$Nom.".convert.".$Params.".".$Out;
		
		if($this->imageOverQuota($Dossier,'/'.$Nom."\.convert\.[0-9]+x[0-9]+\.".$Out.'/'))	
			return false;
		
		if (file_exists($Dir.$Name)) return $Dir.$Name;
		//On verifie l'existence du fichier original
		if (!file_exists($OrigAdresse)) return false;

		$i = new imagick($OrigAdresse);
		$i->setImageFormat('jpg');
		$i->setIteratorIndex(0);
		$i->writeImage($Dir.$Name);
		
		return ($Test) ? $Dir.$Name : false;
	}
	

	
	function DriverSearch($Analyse,$Select,$GroupBy){
		$Results = Array();
		$Adresse = $SavAdresse = $this->getAddress($Analyse);
		$Recherche = $Analyse[sizeof($Analyse)-1]["Recherche"];
		if ($Recherche){
			$Adresse .= "/".$Recherche;
			if (preg_match("#([A-z\/\-0-9\_\@\*\.]+)\.([A-z0-9]+)\.([A-z]+)\.([0-9x]+?)\.([A-z0-9]*)#",$Recherche,$out)) {
				$NomFichier = $out[1].".".$out[2];
				$Action = $out[3];
				$Out=$out[5];
				$Dim = $out[4];
				//On verifie que le fichier d'origine existe
				if (!file_exists($SavAdresse."/".$NomFichier)) return false;
				switch ($Action)
				{
					case "mini":
						$Adr = $this->Miniature($SavAdresse."/".$NomFichier,$SavAdresse,$NomFichier,$Dim,$Out);
						break;
					case "limit":
						$Adr = $this->LimitSize($SavAdresse."/".$NomFichier,$SavAdresse,$NomFichier,$Dim,$Out);
						break;
					case "scale":
						$Adr = $this->ScaleSize($SavAdresse."/".$NomFichier,$SavAdresse,$NomFichier,$Dim,$Out);
						break;
					case "convert":
						$Adr = $this->ConvertImg($SavAdresse."/".$NomFichier,$SavAdresse,$NomFichier,$Dim,$Out);
						break;
					default:
						die("Ce type de decoupe ($Action) n'existe pas.");
						break;
				}
				if (!$Adr) return false;
				$Results[0] = $this->getAttributes($Adr,$Recherche,"_Fichier",true);
			}elseif(file_exists($Adresse)||$this->file_exists_i($Adresse)){
				$Adresse = $this->getCaseSensitiveName($Adresse);
				$Results[0] = $this->getAttributes($Adresse,$Recherche,$Analyse[sizeof($Analyse)-1]["Nom"],true);
			}
			else {
				return false;
			}
		}else{
			//Recherche d enfants
			if ($Analyse[sizeof($Analyse)-1]["Nom"]=="_Dossier"){
				$Path=$Adresse;
				if (is_dir($Path)) {
					if ($dh = opendir($Path)) {
						while (($file = readdir($dh)) !== false) {
							if(is_dir($Path."/".$file) && $file!="." && $file!=".."){
								$Results[] = $this->getAttributes($Path."/".$file,$file);
							}
						}
						closedir($dh);
					}
				}
			}else{
				$Path=$Adresse;
				if (is_dir($Path)) {
					if ($dh = opendir($Path)) {
						while (($file = readdir($dh)) !== false) {
							if(!is_dir($Path."/".$file) && $file!="." && $file!=".."){
								$Results[] = $this->getAttributes($Path."/".$file,$file);
							}
						}
						closedir($dh);
					}
				}
			}
		}
		//----------------------------------------------//
		//Gestion des Historiques	 		//
		//----------------------------------------------//
		/*if (is_array($Results)){
			for($i=0;$i<sizeof($Results);$i++){
				$Results[$i]["ObjectType"] = $this->titre;
				for ($j=0;$j<sizeof($Analyse)-1;$j++){
					$Results[$i]["Historique"][] = Array(
						"Id" => $Analyse[$j]["Recherche"],
						"ObjectType" => $Analyse[$j]["Nom"]
					);
				}
			}
		}*/
		//----------------------------------------------//
		//Gestion du count
		//----------------------------------------------//
		if (preg_match("#COUNT\(\DISTINCT\((.*?)\)\)#",$Select,$Out)){
			$R[0]["COUNT(DISTINCT(".$Out[1]."))"] = sizeof($Results);
			unset($Results);
			$Results=$R;
		}
/*		echo "********************\r\n";
		print_r($Analyse);
		print_r($Results);*/
		return $Results;
	}

	function getAddress($Analyse){
		//On collecte les accés de l'utlisateur en cours.
		//On vérifie que la requete n'est pas vide
			//On verifie que la requete précise l'acces demandé 
			//Sinon on vérifi qu'il ait à la cible en acces ou utilisateur admin
				//Si oui on renvoie l'adresse complete
				//Si non => FAUX
		//Sinon on renvoie la liste des accés
		$Adresse = ".";
		foreach ($Analyse as $o=>$Rep){
			if ($o<sizeof($Analyse)-1){  //||sizeof($Analyse)==1
				//Cas ou le dossier s'appelle "Fichier"
				if ($Rep["Recherche"]==""&&$Rep["Parent"]=="_Fichier"){
					$Adresse.="/".$Rep["Parent"];
				}else $Adresse.="/".$Rep["Recherche"];
			}
		}
		return $Adresse;
	}

	function getAttributes($Adresse,$Nom,$Type=false,$getData=false){
		$Adresse = $this->getCaseSensitiveName($Adresse);
		//$Tab["atime"] = @fileatime($Adresse);
		//$Tab["ctime"] = @filectime($Adresse);
		//$Tab["mtime"] = @filemtime($Adresse);
		$Tab["Size"] = @filesize($Adresse);
		$Type=(is_dir($Adresse)?"_Dossier":"_Fichier");
		$Tab['Id'] = $Nom;
		$Tab['Path'] = $Adresse;
		if ($Type=="_Fichier"&&$getData){
			$Tab['Contenu'] = @file_get_contents($Adresse);
		}
		if ($Type=="_Fichier"){
			$Temp = explode(".",$Nom);                        
			$Tab['Type'] = $Temp[count($Temp)-1];
			$Tab['Mod'] = $Temp[0];
			$Tab['Mime'] = (!empty($adresse))?mime_content_type($Adresse):'';
		}
		$Tab['Nom'] = $Nom;
		$Tab['Url'] = $Adresse;
		$Tab['ObjectType'] = $Type;
		return $Tab;
	}

	function searchAll()
	{
		return $this->getReflexiveRelatives(0,"c","");
	}

	function multiSearch($Recherche){
	}


	function getReference(){
	}


	function getReflexiveRelatives($id,$typeSearch,$Analyse){
		if (!$Analyse)
		$Path = "./";
		else $Path = $this->getAddress($Analyse);
		if ($typeSearch=='p') return false;
		if (is_dir($Path)) {
			if ($dh = opendir($Path)) {
				while (($file = readdir($dh)) !== false) {
					if(is_dir($Path."/".$file) && $file!="." && $file!=".."){
						$Results[] = $this->getAttributes($Path."/".$file,$file,"_Dossier");
					}
				}
				closedir($dh);
			}
		}
		return $Results;
	}


	function getFkeyRelatives($id,$typeSearch,$Unused,$Unused2,$Analyse){
		$Path = $this->getAddress($Analyse);
		if ($typeSearch=='p') return false;
		if (is_dir($Path)) {
			if ($dh = opendir($Path)) {
				while (($file = readdir($dh)) !== false) {
					if(is_file($Path."/".$file) && $file!="." && $file!=".."){
						$Results[] = $this->getAttributes($Path."/".$file,$file,$this->titre);
					}
				}
				closedir($dh);
			}
		}
		$GLOBALS["Chrono"]->stop("RECH FICHIERS");
		return $Results;
	}

	function checkPath($Path){
		//$GLOBALS['Systeme']->Log->log('CHECKPATH:'.$Path);
		$pastUrl="";
		$Emplacement="";
		//Url si pas definie alors stockage dans le dossier utilisateur par defaut
		//$GLOBALS['Systeme']->Log->log('CALLDATA '.$Path,$this->Module);
		if(empty($Path))return "Home/".Sys::$User->Id."/";
		$Tab = Sys::$Modules[$this->Module]->callData($Path);
		//$GLOBALS['Systeme']->Log->log('CHECKPATH ',$Tab);
		//Analyse,validation et creation de l emplacement de stockage
		if (!sizeof($Tab)) $createFolder = true;
		$Url = explode("/",$Path);
		for ($i=0;$i<count($Url);$i++){
			$pastUrl .= $Url[$i]."/";
			if(!file_exists($pastUrl)) mkdir($pastUrl,0777);
			$Emplacement.=$Url[$i].'/';
		}
		return $Emplacement;
	}

	function checkName($chaine){
		//On enleve les accents
		$chaine= strtr($chaine,  "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿ#Ññ","aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn" );
		//On remplace tous ce qui n est pas une lettre ou un chiffre par un _
		$chaine = preg_replace('/([^.a-z0-9]+)/i', '_', $chaine);
		//On ajoute le timestamp au nom de fichier
		// 		$chaine = preg_replace('#(.+)\.[a-z]{3})#i','$1-'.time().'.$2',$chaine);
		return $chaine;
	}

        function getFinalName($P,$N,$Num){
		/* P est le chemin, N le nom, Num le numéro en test */
		$E = explode('.',$N);
		$ext = array_pop($E);
		$E = implode('.',$E);
		if ( file_exists($P.$E."_".$Num.".".$ext) ) {
			return $this->getFinalName($P,$N,$Num+1);
		}
		else {
			return $E."_".$Num.".".$ext;
		}
	}

                        
	function insertObject($Properties){
		$BLa = false;
		//Adresse de stockage
		$Properties['Url'] = $this->checkPath($Properties['Url']);
		//Nom , on verifie que le nom est bien conforme aux specifications url
		$Properties['Nom'] = $this->checkName($Properties['Nom']);
		//Verification et Definition des Informations
		//$GLOBALS['Systeme']->Log->log('SAVE FILE ',$Properties);
		//Temp , on verifie si il s agit d un fichier upload� , dans ce cas on recupere la variable $_FILE  et on injecte le contenu dans le fichier definitif
		if ($Properties['Temp']!=""&&!isset($_POST["Flashdata"])){
			//Donc fichier present dans le dossier temporaire
			if (empty($Properties['Nom'])) {
				$Properties['Nom'] = $this->checkName($_FILES[$Properties['Temp']]['name']);
			}
			// 				echo "EMPLACEMENT --> ".$Path.$No
			$Path = $Properties['Url'];
          $Properties['Nom'] = $this->getFinalName($Path,$Properties['Nom'],0);
            copy($_FILES[$Properties['Temp']]['tmp_name'],$Path . $Properties['Nom']);
			if(!move_uploaded_file($_FILES[$Properties['Temp']]['tmp_name'], $Path . $Properties['Nom']) ){
			    $BLa=true;
			    die('impossibel d\'uploader le fichier dans le dossier '.$Path . $Properties['Nom'].' pour le fichier temporaire '.$_FILES[$Properties['Temp']]['tmp_name'].' >>>> '.file_exists($_FILES[$Properties['Temp']]['tmp_name']));
            }
		}elseif (isset($_POST["Flashdata"])){
			//UPLOAD FLASH BASE 64
			//preparation du tableau properties
			$Properties['Nom'] = $this->checkName($_POST["Flashname"]);
			$Path = $Properties['Url'];
			if(! isset($_POST["Flashoverride"]) || ! $_POST["Flashoverride"]) $Properties['Nom'] = $this->getFinalName($Path,$Properties['Nom'],0);
			//else $Properties['Nom'] = $Path.$Properties['Nom'];
			//sauvegarde flash en post
			$img = imagecreatefromstring(base64_decode($_POST["Flashdata"]));
			if($img != false){
			   imagejpeg($img, $Path.$Properties['Nom']);
			}else{
				file_put_contents($Path.$Properties['Nom'], base64_decode($_POST["Flashdata"]));
			}
		}else{
			//On cree le fichier , si impossible alors on retourne faux
			if (!$Fichier = fopen($Properties['Url'].$Properties['Nom'],"w+")) {
				return false;
			}
			//Gestion du contenu
			if (is_array ($Properties['Contenu'])) $Properties['Contenu'] = serialize ($Properties['Contenu']);
			//Remplissage du fichier
			//$this->WriteFile($Fichier,$Properties['Contenu'],$Properties['Type']);
                        
			fwrite($Fichier,$Properties['Contenu']);
			//Fermeture
			fclose($Fichier);
		}
		//On redefinie les proprietes afin de mettre l objet a jour
		$Properties = $this->getAttributes($Properties['Url'],$Properties['Nom'],"_Fichier",true);
		if (!$BLa) $Properties["Url"] = $Properties['Url'].$Properties['Nom'];
		

		return $Properties;
	}


	function createData(){
	}

	function insertKey($Tab,$Type){
	}

	function WriteFile(){
		switch ($Properties['Type']){
			default:
				//On cree le fichier , si impossible alors on retourne faux
				if (!$Fichier = fopen($Properties['Url'].$Properties['Nom'],"w+")) {
					return false;
				}
				//Gestion du contenu
				if (is_array ($Properties['Contenu'])) $Properties['Contenu'] = serialize ($Properties['Contenu']);
				//Remplissage du fichier
				fwrite($Fichier,$Properties['Contenu']);
				//Fermeture
				fclose($Fichier);
				break;
		}
	}
	function Erase($Object){
                if ( empty($Object->Url) ) return false;
		if (!file_exists($Object->Url)) return false;
		if (!is_dir($Object->Url))return (unlink($Object->Url)) ? true:false;
		else {
			#TODO
			//On verifie que le dossier n'est pas vide
			return (rmdir($Object->Url)) ? true:false;
		}
	}


	function EraseAssociation($Relative,$ObjId,$Type){
	}


	/*---------------------------------
	 |           PRIVEE               |
	 ---------------------------------*/
	
	private function imageOverQuota($dossier,$regex){
		$quota = 20; //TODO : Parametrer dans la conf
		$dirFiles = scandir($dossier);
		$made = 0;
		
		foreach($dirFiles as $dirFile){
			if( preg_match($regex,$dirFile)){
				$made += 1;
			}
		}
		//$GLOBALS['Systeme']->Log->log($made);
		
		if ($made > $quota) return true;
		
		return false;
	}

	/*---------------------------------
	 |           CREATION              |
	 ---------------------------------*/

	function init() {
	}

	function initAssoc() {
	}

	function saveData(){
	}


	/*---------------------------------
	 |           RECHERCHE             |
	 ---------------------------------*/


	/* Cette fonction range et classe dans un tableau les donnees trouvees.
	 Renvoi: le tableau de resultat.
	 Parametres: les donnees trouvees dans la base de donnees, la recherche effectuee*/
	function analyzeSearch($Donnees, $Recherche) {
		$Resultat= Array();
		$compteur=0;
		$totalCibles=count($this->Cibles);
		//On procede au calcul de la note que l'on enregistre, avec le reste, dans le tableau final
		while($Enregistrement=mysql_fetch_assoc($Donnees)){
			foreach ($this->Cibles as $valeurCible){
				foreach ($Enregistrement as $clefEnr=>$valeurEnr){
					$Resultat[$compteur][$clefEnr] = $valeurEnr;
					//Calcul de la note
					$note= (preg_match('!'.$Recherche.'!i', $valeurEnr) && $clefEnr== $valeurCible['nom']) ? $this->calcNote($valeurEnr,$Recherche,$valeurCible['searchorder']): 10;
				}
			}
			$Resultat[$compteur]['note'] = $note;
			$compteur++;
		}
		$Resultat = $this->bubbleSort($Resultat,'note');
		$Resultat=$this->setSearchOrder($Resultat);
		return $Resultat;
	}

	/*---------------------------------
	 |          TEST/AUTO              |
	 ---------------------------------*/

	function getTableName(){
	}

	function findReflexive(){
	}

	function getTime(){
	}
	function executeSql(){
	}


}
