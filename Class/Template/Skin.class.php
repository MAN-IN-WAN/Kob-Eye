<?php

class Skin extends Root{
	var $Data;
	var $Beacon=0;
	var $BeaconTab;
	var $LangFile;
	var $DefaultPage = "";
	var $DefaultSkin = "";
	var $Template = 1;
	var $Css;
	var $Nom="";
    static $SharedSkin;

	function Skin(){
		$Num=Sys::$Skin;
		if (empty($Num)){
			$Num=$GLOBALS["Systeme"]->Conf->get("GENERAL::AUTH::MAIN_SKIN_NUM");
			Sys::$User->Skin = $Num;
		}
		$this->URL = "Skins/".$Num."/.cache/Skin.cache";
		$this->URLHEADER = "Skins/".$Num."/.cache/Header.cache";
		$this->URLINFO = "Skins/".$Num."/.cache/Skin.info";
		if (!$this->setConf($Num)) return false;
		$this->initSkin();
	}
	function get($Name){
		return $this->{$Name};
	}
	function checkIfModified(){
		//RENVOIE VRAI SI MODIFICATON OU NON ACCES, FAUX SINON
		if (!file_exists(ROOT_DIR.$this->URLINFO)) return true;
		$Content=file(ROOT_DIR.$this->URLINFO);
		for ($i=0;$i<count($Content);$i++){
			$Ligne=rtrim($Content[$i]);
			$TabLigne=explode("||",$Ligne);
			if(@filemtime(ROOT_DIR.'Skins/'.$this->Nom.'/'.$TabLigne[0].".bl")>$TabLigne[1]) return true;
		}
		return false;
	}

	function setConf($Num) {
		$this->Nom = $Num;
		if (file_exists(ROOT_DIR.'Skins/'.$Num.'/Skin.conf')){
			//On met le XML sous forme de tableau.
			$Skin = new xml2array('Skins/'.$Num.'/Skin.conf');
			$tabSkin=$Skin->getResult();
			//On donne a la classe le titre+ le commentaire+ le bloc principal;
			if (isset($tabSkin['SKIN']['#']['TITRE'][0]['#']))$this->Titre = $tabSkin['SKIN']['#']['TITRE'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['TITLE'][0]['#']))$this->Title = $tabSkin['SKIN']['#']['TITLE'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['COMMENTAIRE'][0]['#']))$this->Commentaire = $tabSkin['SKIN']['#']['COMMENTAIRE'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['REPLY-TO'][0]['#']))$this->ReplyTo = $tabSkin['SKIN']['#']['REPLY-TO'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['DESCRIPTION'][0]['#']))$this->Description = $tabSkin['SKIN']['#']['DESCRIPTION'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['KEYWORDS'][0]['#']))$this->Keywords = $tabSkin['SKIN']['#']['KEYWORDS'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['CLASSIFICATION'][0]['#']))$this->Classification = $tabSkin['SKIN']['#']['CLASSIFICATION'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['BLOC'][0]['#']))$this->bloc = $tabSkin['SKIN']['#']['BLOC'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['FRAMESET'][0]))$this->Frameset = $tabSkin['SKIN']['#']['FRAMESET'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['TEMPLATE'][0]))$this->Template = $tabSkin['SKIN']['#']['TEMPLATE'][0]['#'];
			if (isset($tabSkin['SKIN']['#']['CSS'][0]))$this->Css = $tabSkin['SKIN']['#']['CSS'][0]['#'];
			else $this->Css = 'Skins/'.$Num.'/Css/style.css';
			if (isset($tabSkin['SKIN']['#']['SHARED_SKIN'][0])){
                $this->DefaultSkin = $tabSkin['SKIN']['#']['SHARED_SKIN'][0]['#'];
                Skin::$SharedSkin = $this->DefaultSkin;
            }
			if ($GLOBALS["Systeme"]->CurrentLanguage!=$GLOBALS["Systeme"]->DefaultLanguage){
				if(isset($tabSkin['SKIN']['#'][$GLOBALS["Systeme"]->Language[$GLOBALS["Systeme"]->CurrentLanguage].'-DEFAULT_PAGE']))$this->DefaultPage = $tabSkin['SKIN']['#'][$GLOBALS["Systeme"]->Language[$GLOBALS["Systeme"]->CurrentLanguage].'-DEFAULT_PAGE'][0]['#'];
			}elseif (isset($tabSkin['SKIN']['#']['DEFAULT_PAGE'][0]['#'])) $this->DefaultPage = $tabSkin['SKIN']['#']['DEFAULT_PAGE'][0]['#'];
			if(isset($tabSkin['SKIN']['#'][$GLOBALS["Systeme"]->Language[$GLOBALS["Systeme"]->CurrentLanguage].'-LANG']))$this->LangFile =$tabSkin['SKIN']['#'][$GLOBALS["Systeme"]->Language[$GLOBALS["Systeme"]->CurrentLanguage].'-LANG'][0]['#'];
			if (is_object($GLOBALS["Systeme"]->Header)){
				//Configuration de l entete
				if (isset($this->Titre))$GLOBALS["Systeme"]->Header->setTitle($this->Titre);
				if (isset($this->Css))$GLOBALS["Systeme"]->Header->addCss($this->Css,true);
				if (isset($this->Keywords))$GLOBALS["Systeme"]->Header->setKeywords($this->Keywords);
				if (isset($this->Description))$GLOBALS["Systeme"]->Header->setDescription($this->Description);
				if (isset($this->Frameset))$GLOBALS["Systeme"]->Header->setFrameset($this->Frameset);
				if (isset($this->Classification))$GLOBALS["Systeme"]->Header->setClassification($this->Classification);
				if (isset($this->ReplyTo))$GLOBALS["Systeme"]->Header->setReplyTo($this->ReplyTo);
			}
			//Configuration page par defaut si definie
			if ($this->DefaultPage!=NULL)$GLOBALS["Systeme"]->setDefault($this->DefaultPage);
			//Definition de la skin partage si définie
			if (!empty($this->DefaultSkin)){
				Sys::$DefaultSkin = $this->DefaultSkin;
			}
		}else{
//		    echo '<pre>';
//		    debug_print_backtrace();
//            echo '</pre>';
			echo 'Le fichier de la skin '.$Num.' n\'a pas &#233;t&#233; ouvert.<BR/>';
			//Si ce n'est la skin par defaut, on la charge (si ok: -1, sinon 0). Si c'est la skin par defaut, on renvoie 0 car son fichier de conf n'est pas ouvrable.
			if ($Num !=Sys::$DefaultSkin){
				if ($this->initSkin(Sys::$DefaultSkin)){
					Sys::$Skin = Sys::$DefaultSkin;
					return -1;
				}else return 0;
			}else return 0;
		}
		return 1;
	}

	function initSkin(){
		if (!SKIN_CACHE||$this->checkIfModified()){
			//On genere le html
			$SkinBloc=new Bloc();
			$Beacon["BEACON"] = "BLOC";
			$SkinBloc->setFromVar($this->bloc,"[DATA]",$Beacon);
			$SkinBloc->init();
 			$this->SkinObjects = $SkinBloc;
			if (SKIN_CACHE&&$this->checkIfModified()) {
				$Url="Skins/".Sys::$User->Skin."/.cache";
				//Creation des fichiers du cache de la skin
				if (!is_dir(ROOT_DIR.$Url)){
					mkdir(ROOT_DIR.$Url);
				}
				$this->WriteHeaderCache($Url . "/Header");
				$this->SkinObjects->writeCache($Url);
				$this->WriteSkinInfo();
			}

		}else{
			//Donc on charge la skin
			$this->SkinObjects= unserialize(file_get_contents(ROOT_DIR.$this->URL));
		}
	}
	
	function loadSkin() {
		$this->SkinObjects = new Bloc();
		$Data = $this->Data;
		$this->SkinObjects->loadData($Data,"SkinBase");
		return true;
	}

	function writeSkinInfo(){
		if (!$File=fopen (ROOT_DIR.$this->URLINFO,"w"))return false;
		foreach (Sys::$BlocLoaded as $k=>$p){
			if (file_exists(ROOT_DIR.'Skins/'.$this->Nom.'/'.$k.".bl")) {
				$Entree = $k . '||';
				$Entree .= filemtime(ROOT_DIR . 'Skins/' . $this->Nom . '/' . $k . ".bl");
				$Entree .= "\n";
				fwrite($File, $Entree);
			}
		}
		fclose($File);

		return true;
	}

	function WriteHeaderCache($Url){
		//Recuperation de l entete
		$Entete = $GLOBALS["Systeme"]->Header;
		//On ecrit les fichiers 
		$this->writeCacheFile(serialize($Entete),$Url.".cache");
	}


	function processCache() {
		//On ecrit le fichier de cache
			$Cache = $this->Data;
			$Cache = $this->ProcessingCache($Cache);
			$this->BeaconTab=Array();
			$this->Beacon = 0;
			$this->writeCacheFile($Cache,$this->URL);
 	}


	function Generate() {
		$this->SkinObjects->Generate();
		$this->Data = $this->SkinObjects->Affich();
		return ;
	}

	//AJOUTER UN FICHIER NOM_DE_LA_LANGUE.lang a la racine de la skin et specifier le fichier dans la config ex:<EN-LANG>Skins/Pragma/Anglais.lang</EN-LANG>
	function ProcessLang($data) {
		//On ouvre le fichier de langue si il existe
		if (!empty($this->LangFile)&&file_exists(ROOT_DIR.$this->LangFile)){
			$f =fopen(ROOT_DIR.$this->LangFile,"r");
			while (!feof($f)){
				$p = fgets($f,4096);
				$p = preg_replace("#//.*$#","",$p);
				$p = preg_replace("#\r\n#","",$p);
				$p = explode("|",$p);
//				print_r($p);
				if(sizeof($p)>1) $data = str_replace($p[0],trim($p[1]),$data);
			}
		}
		//Pour chaque paire on remplace l'exuivalent dans le contenu
		return $data;
	}

	function writeCacheFile($Data,$Url) {
//		$Url = str_replace("/","_",$Url);
		if (!$File=fopen (ROOT_DIR.$Url,"w"))return false;
		fwrite($File,$Data);
		fclose($File);
	}

	function Affich($Data="") {
		return $this->Data;
	}
}

?>