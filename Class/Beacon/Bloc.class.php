<?php
class Bloc extends Beacon {
	//CLASS BLOC
	var $RawData;
	var $VarData;
	var $RawLoaded = false;
    var $Data = '';
	var $ContentLoaded = false;
	var $Content;
	var $BlObjects;
	var $BlData;
	var $PostData;
	var $PostObjects;
	var $PostBlData;
	var $ObjVars;
	var $PostBlObjects;
	var $ChildObjects;
	var $ChildData;
	var $Path;
	var $Twig = false;


	public function setFromVar($Var,$Data,$Beacon) {
		$this->Vars = $Var;
		$this->Data = $Data;
		$this->Beacon = $Beacon["BEACON"];
		//$this->init($Process);
	}

	public function init(){
		if (!$this->Init){
			switch ($this->Beacon) {
				case "MODULE":
					//$this->setModule();
					$this->INITOK="NB ".sizeof($this->ChildObjects);
				break;
				default:
				case "BLOC":
					$this->initBloc();
					$this->BlProcess();
				break;
			}
			$this->Process();
            $this->Init++;
		}
	}

	public function initBloc(){
		$TempVar = explode("|",$this->Vars);
		$NomBloc = $TempVar[0];
		if (!empty(Sys::$BlocLoaded[$NomBloc])) {
			$this->RawData = Sys::$BlocLoaded[$NomBloc];
			$this->RawLoaded = true;
			$this->ContentLoaded = true;
		} else {
			$this->loadTemplate($NomBloc);
			if (!$this->Twig) {
				Sys::$BlocLoaded[$NomBloc] = $this->RawData;
			}else{
				KeTwig::loadTemplate($this->Path);
			}
		}
	}

	function setModule() {
		$y=explode("?",$this->Vars);
		if (!sizeof($this->ChildObjects)){
  			if (!preg_match("#\[\!.*\!\]#",$y[0])){
				preg_match("#(.*?)[.\/](.*)#", $y[0], $Out);
				if ($Out[1]!=""){
					$Module = $Out[1];
				}else $Module = $Vars;
				preg_match("#".$Module."\/(.*)#", $y[0], $Out);
				$Module = str_replace(" ","",$Module);
				$Bloc=$GLOBALS['Systeme']->Modules[$Module]->setData($Out[1]);
				if (is_array($Bloc->ChildObjects))foreach ($Bloc->ChildObjects as $C)if (is_object($C))$this->ChildObjects[]=$C; else $this->ChildObjects[]=$C;
				$GLOBALS["Systeme"]->Log->Log("CHARGEMENT BLOC ".sizeof($Bloc->ChildObjects)." ".$Out[1]);
				$this->LOADED = "OK";
  			}else{
				 $GLOBALS["Systeme"]->Log->Log("BLOC A PAS CHARGE ".$y[0]);
				$this->NOTLOADED = "OK";
			}
		}else{
			$GLOBALS["Systeme"]->Log->Log("DEJA INITIALISE ".$y[0]);
			$this->ALREADYLOADED++;
		}
	}

	function loadData($Data) {
		//$this->Beacon = "BLOC";
		$this->Data = $Data;
		$this->RawLoaded = true;
	}

	function loadTemplate($NomBloc){
		if (empty($NomBloc))return;

		if (file_exists("Skins/".Sys::$User->Skin."/".$NomBloc.".twig")){
			$this->Path="Skins/".Sys::$User->Skin."/".$NomBloc.".twig";
			$this->Twig = true;
		}elseif (file_exists("Skins/".Sys::$User->Skin."/".$NomBloc.".bl")){
			$this->Path="Skins/".Sys::$User->Skin."/".$NomBloc.".bl";
		}elseif (file_exists("Skins/".Sys::$DefaultSkin."/".$NomBloc.".twig")) {
			$this->Path="Skins/".Sys::$DefaultSkin."/".$NomBloc.".twig";
			$this->Twig = true;
		}elseif (file_exists("Skins/".Sys::$DefaultSkin."/".$NomBloc.".bl")){
			$this->Path="Skins/".Sys::$DefaultSkin."/".$NomBloc.".bl";
		}

		if (!$this->Twig) {
			if ($this->Path != "") {
				fopen($this->Path, 'r');
				$this->RawData = implode('', file($this->Path));
				$this->RawLoaded = true;
				$this->ContentLoaded = true;
				//$GLOBALS['Systeme']->BlockList[]=$this;
			} else {
				//c donc directement le contenu Xml
				return;
				throw new Exception("Impossible d ouvrir Le fichier : " . $NomBloc . " pour la skin: " . Sys::$Skin);
			}
		}
	}
    //Appel depuis la balise module pour affichage interface
    function setData($Lien,$recurs=0) {
        if (empty($Lien))return;
        //Analyse de l Url
        $Tab = Module::splitQuery($Lien,false);
        if (DEBUG_INTERFACE) print_r($Tab);
        //On appelle l interface concernee
        if (isset($Tab[0])) {
            //test twig
            if (preg_match('#\.twig$#',$Tab[0]["InterfacePath"])) {
                $this->Twig = true;
                $this->Path = $Tab[0]["InterfacePath"];
                KeTwig::loadTemplate($this->Path);
                $this->Content = $this->Data = KeTwig::render($this->Path,Process::$TempVar);
            }else
                $this->loadInterface($Tab[0]["InterfacePath"]);
        }
	}



    /**
     * Recherche recursivement l'interface correspondante à l'url
     * @param tab tableau comprenant chacun des niveaux de la requete
     * @param path base de recherche
     * @param i niveau de recherche
     * trois cas de figure
     * - le dossier existe
     * - le dossier default existe
     * - dans le cas de la derniere boucle il peut s'agir un fichier(.md) ou du dossier(/Default.md)
     */
    public static function lookForInterface($tab,$path,$strict=false,$i=0){
        if (DEBUG_INTERFACE)echo "LFI $path	|$i\r\n";
        $pd = $p = $path;
        if ($tab[$i]==""){
            if ($i<sizeof($tab)-1)return Bloc::lookForInterface($tab,$p,$strict,$i+1);
            //else return $p;
        } else {
            $p.=(($p!="")?"/":"").$tab[$i];
            $pd = $path;
            $pd.=(($p!="")?"/":"")."Default";
        }
        if (DEBUG_INTERFACE)echo "PATH $p FILE ".file_exists(ROOT_DIR.$p)."  DIR ".is_dir(ROOT_DIR.$p)."\r\n";
        if (file_exists(ROOT_DIR.$p)&&is_dir(ROOT_DIR.$p)&&$i<sizeof($tab)-1){
            //C 'est un dossier
            if (DEBUG_INTERFACE)echo "	- $p\r\n";
            return Bloc::lookForInterface($tab,$p,$strict,$i+1);
        }
        if (file_exists(ROOT_DIR.$pd)&&is_dir(ROOT_DIR.$pd)&&$i<sizeof($tab)-1){
            if (DEBUG_INTERFACE)echo "	- $pd\r\n";
            //C 'est un dossier default
            return Bloc::lookForInterface($tab,$pd,$strict,$i+1);
        }
        if (file_exists(ROOT_DIR.$p.".twig")){
            //C 'est un fichier twig
            if (DEBUG_INTERFACE)echo "	- $p.twig\r\n";
            return $p.".twig";
        }
        if (file_exists(ROOT_DIR.$p.".md")){
            //C 'est un fichier
            if (DEBUG_INTERFACE)echo "	- $p.md\r\n";
            return $p.".md";
        }
        if ($i==sizeof($tab)-1){
            if (DEBUG_INTERFACE)echo "	LAST PASS $p/Default.twig // $pd/Default.ywig // $pd.twig\r\n";
            if (!$strict&&is_dir(ROOT_DIR.$p)&&file_exists(ROOT_DIR.$p."/Default.twig")){
                //C 'est un fichier twig
                if (DEBUG_INTERFACE)echo "	- $p/Default.twig\r\n";
                return $p."/Default.twig";
            }
            if (DEBUG_INTERFACE)echo "	LAST PASS $p/Default.md // $pd/Default.md // $pd.md\r\n";
            if (!$strict&&is_dir(ROOT_DIR.$p)&&file_exists(ROOT_DIR.$p."/Default.md")){
                //C 'est un fichier
                if (DEBUG_INTERFACE)echo "	- $p/Default.md\r\n";
                return $p."/Default.md";
            }
            if (!$strict&&is_dir(ROOT_DIR.$pd)&&file_exists(ROOT_DIR.$pd."/Default.twig")){
                //C 'est un fichier
                if (DEBUG_INTERFACE)echo "	- $pd/Default.twig\r\n";
                return $pd."/Default.twig";
            }
            if (!$strict&&is_dir(ROOT_DIR.$pd)&&file_exists(ROOT_DIR.$pd."/Default.md")){
                //C 'est un fichier
                if (DEBUG_INTERFACE)echo "	- $pd/Default.md\r\n";
                return $pd."/Default.md";
            }
            if (!$strict&&file_exists(ROOT_DIR.$pd.".twig")){
                //C 'est un fichier
                if (DEBUG_INTERFACE)echo "	- $pd.twig\r\n";
                return $pd.".twig";
            }
            if (!$strict&&file_exists(ROOT_DIR.$pd.".md")){
                //C 'est un fichier
                if (DEBUG_INTERFACE)echo "	- $pd.md\r\n";
                return $pd.".md";
            }
        }
        if (DEBUG_INTERFACE)echo "FAILED !! \r\n";
        return false;
    }
    function writeCacheFile($Data,$Url,$Name) {
        if (!$File=@fopen (ROOT_DIR.$Url.$Name,"w")){
            $this->mk_dir($Url);
            if (!$File=fopen (ROOT_DIR.$Url.$Name,"w")) return false;
        }
        fwrite($File,$Data);
        fclose($File);
    }

    public static function isInterface($Module,$ObjectClass,$Interface,$strict=true) {
        if (DEBUG_INTERFACE)echo "-----------TEST---------------\r\n";
        return (!Bloc::getInterface($Module,$ObjectClass,$Interface,$strict))?false:true;
    }
    public static function getInterface($Module,$ObjectClass,$Interface,$Strict=true){
        //Calcul tab
        $t = explode('/',$ObjectClass);
        $t = array_merge($t,explode('/',$Interface));
        //Controle tableau;
        $t2 = Array();
        foreach ($t as $t3)if (!empty($t3))$t2[] = $t3;
        $t=$t2;
        $Mod = array_merge(Array($Module),$t);
        if (DEBUG_INTERFACE)echo "-----------$ObjectClass,$Interface-$Strict---------------\r\n";
        if (DEBUG_INTERFACE)print_r($Mod);
        if (Sys::$DefaultSkin==""&&Sys::$User->Skin=="")return true;
        if (isset(Sys::$User)&&is_object(Sys::$User)){
            //Skin en cours
            $Chemin="Skins/".Sys::$User->Skin."/Modules";
            if (DEBUG_INTERFACE)echo "TEST $Chemin \r\n";
            if (file_exists(ROOT_DIR.$Chemin)&&$I=Bloc::lookForInterface($Mod,$Chemin,true)) return $I;
        }
        //SKin partagée
        $Chemin="Skins/".Sys::$DefaultSkin."/Modules";
        if (DEBUG_INTERFACE)echo "TEST $Chemin \r\n";
        if (file_exists(ROOT_DIR.$Chemin)&&$I=Bloc::lookForInterface($Mod,$Chemin,true)) return $I;
        if (DEBUG_INTERFACE)echo "/////////////////////////////\r\n";
        //Fichier du module
        $Chemin="Modules";
        if (DEBUG_INTERFACE)echo "TEST $Chemin \r\n";
// 		if (file_exists($Chemin)&&$I=$this->testInterface($ObjectClass,$Interface,$Chemin)) return $I;
        if (file_exists(ROOT_DIR.$Chemin)&&$I=Bloc::lookForInterface($Mod,$Chemin,true)) return $I;
        //Skin en cours sans mode strict
        if (!$Strict){
            if (isset(Sys::$User)&&is_object(Sys::$User)){
                $Chemin="Skins/".Sys::$User->Skin."/Modules";
                if (DEBUG_INTERFACE)echo "TEST $Chemin \r\n";
                if (file_exists(ROOT_DIR.$Chemin)&&$I=Bloc::lookForInterface($Mod,$Chemin,false)) return $I;
            }
            //SKin partagée
            $Chemin="Skins/".Sys::$DefaultSkin."/Modules";
            if (DEBUG_INTERFACE)echo "TEST $Chemin \r\n";
            if (file_exists(ROOT_DIR.$Chemin)&&$I=Bloc::lookForInterface($Mod,$Chemin,false)) return $I;
            //Fichier du module
            $Chemin="Modules";
            if (DEBUG_INTERFACE)echo "TEST $Chemin \r\n";
            if (file_exists(ROOT_DIR.$Chemin)&&$I=Bloc::lookForInterface($Mod,$Chemin,false)) return $I;
        }
        if (DEBUG_INTERFACE) echo "$ObjectClass $Interface FAILED !!!!";
        return false;
    }

    function checkIfModified($URLINFO){
        //RENVOIE VRAI SI MODIFICATON OU NON ACCES, FAUX SINON
        if (!file_exists(ROOT_DIR.$URLINFO)) return 1;
        $Content=file(ROOT_DIR.$URLINFO);
        for ($i=0,$c = count($Content);$i<$c;$i++){
            $Ligne=rtrim($Content[$i]);
            $TabLigne=explode("||",$Ligne);
            //echo "new:".filemtime($TabLigne[0]),"orig:".$TabLigne[1],"<br>";
            if(@filemtime(ROOT_DIR.$TabLigne[0])>$TabLigne[1]) return 1;
        }
        return 0;
    }
    //_________________________________________________________________________________
    //									INTERFACES
    /**
     * Chargement de module interface
     */
    function loadInterface($Lien) {
        if (DEBUG_INTERFACE)
            echo "----------LOAD INTERFACE-$Lien---------------\r\n";
        $Lien2 = str_replace("/","_",$Lien);
        $Lien2 = str_replace("__","_",$Lien2);
        $Lien2 = $this->Nom.(($Lien2!="")?"_":"").$Lien2;
        if ($Lien2=="") $Lien2 = "Default";
        $URL = "Home/".Sys::$User->Id."/.cache/".$Lien2.'.modCache';
        $URLCACHE = "Home/".Sys::$User->Id."/.cache/";
        $FILENAME = $Lien2.'.modCache';
        $URLINFO = "Home/".Sys::$User->Id."/.cache/".$Lien2.'.modInfo';
        $FILENAMEINFO = $Lien2.'.modInfo';
        if ((!file_exists(ROOT_DIR.$URLCACHE))||(!MODULE_CACHE)||$this->checkIfModified($URLINFO)){
            //On charge le fichier
            //echo "//////////////////////////////////////////////////////////////\r\n";
            if (DEBUG_INTERFACE) echo "PAS DE CACHE $Lien \r\n";
            if (file_exists(ROOT_DIR.$Lien)) {
                $Chemin = $Lien;
                $Data=@file_get_contents(ROOT_DIR.$Lien);
            }else{
                echo "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!LOADING ERROR $Lien\r\n";
                #TODO ERREUR DE CHARGEMENT
                return false;
            }
            $this->loadData($Data);
            $this->Path = $Chemin;
            $this->init();
            $this->Process();
            if (MODULE_CACHE) {
                $GLOBALS["Systeme"]->Log->Log("-=>WRITE MODULE CACHE ".$URL);
                $ModulesLoaded = $this->getModulesLoaded();
                $this->writeCacheFile(serialize($this),$URLCACHE,$FILENAME);
                $Entree ="";
                foreach ($ModulesLoaded as $Key){
                    $Entree.= $Key.'||';
                    $Entree.=@filemtime(ROOT_DIR.$Key);
                    $Entree.="\n";
                }
                $this->writeCacheFile($Entree,$URLCACHE,$FILENAMEINFO);
            }
        }else{
            if (!empty($this->QuickCache[$URL])){
                $Bloc = unserialize($this->QuickCache[$URL]);
            }else{
                $Bloc = file_get_contents(ROOT_DIR.$URL);
                $Bloc = unserialize($Bloc);
                $this->QuickCache[$URL] = serialize($Bloc);
            }
        }
    }
    function Process() {
        $GLOBALS["Chrono"]->start("BEACON Parser ");
        if (isset($this->Data))
            $this->ChildObjects= Parser::Processing($this->Data,false);
        //if ($this->Twig)KeTwig::loadTemplate($this->Path);
        $GLOBALS["Chrono"]->stop("BEACON Parser ");
        unset($this->Data);
    }
    // 	PROCESSUS
	function BlProcess() {
		if ($this->Vars!="") $this->RawData = $this->loadVars($this->Vars,$this->RawData);
		if (!$this->Twig)$this->BlObjects = Parser::Processing($this->RawData,false);
	}

	function loadVars($Vars,$Data){
		$Out=explode("|",$Vars);

		for ($i=1;$i<BLOC_MAX_PARAMS;$i++) {
			$DataTemp=str_replace('['.$i.']',(isset($Out[$i]))?$Out[$i]:'',$Data);
			if ($DataTemp!=$Data) $Data=$DataTemp; else $i=BLOC_MAX_PARAMS;
		}
		return $Data;
	}

	function addHeader() {
		if (sizeof($this->ObjVars)&&is_object($GLOBALS["Systeme"]->Header)) {
			foreach ($this->ObjVars as $Key){
				switch ($Key["Type"]) {
					case "TITLE":
						$GLOBALS["Systeme"]->Header->setTitle($Key["Data"]);
					break;
					case "DESCRIPTION":
						$GLOBALS["Systeme"]->Header->setDescription($Key["Data"]);
					break;
					case "KEYWORDS":
						$GLOBALS["Systeme"]->Header->setKeywords($Key["Data"]);
					break;
					default:
						if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->Add($Key["Data"],$Key["Var"]);
					break;
				}
			}
		}
	}
	//GENERATION
	function Generate(){
        if ($this->Twig){
			//$this->Content = KeTwig::render($this->Path,Process::$TempVar);
            $this->Data = $this->ChildObjects[0];
		}else{
            if ($this->Beacon=="MODULE") {
                //Extraction des variables GET internes
                $TabTemp = explode("|",$this->Vars);
                $Test = explode("?",$TabTemp[0]);
                $Lien = $Test[0];
                //On genere le module si il n 'est pas generé
                $Vars = Process::processingVars($Lien);
                $Module = explode('/',$Vars,2);
                $Query = $Vars;//(isset($Module[1]))?$Module[1]:"";
                //$Query = (isset($Module[1]))?$Module[1]:"";
                $Module = trim($Module[0]);
                //test de l'existence du module
                if (empty($Module)||!is_object(Sys::$Modules[$Module])){
                    return "<h1>LE MODULE $Module N'est pas installé pour la requete ".$this->Vars."</h1>";
                }
                //Gestion des variables temporaires
                $TempVar = Process::$TempVar;
                $Temp=Array("Query"=>$TempVar["Query"]);
                if (isset($Test[1])){
                    $Vars = $Test[1];
                    preg_match_all("#([^&=|]*?)=([^&|=]*)#",$Vars,$Vars);
                    for ($i=0;$i<sizeof($Vars[0]);$i++){
                        $Temp[Process::processingVars($Vars[1][$i])] = Process::processingVars($Vars[2][$i]);
                    }
                    Process::$TempVar = $Temp;
                }
                //execution requete
                $this->setData($Query);
            }
            if (!$this->Twig)
                parent::Generate();

            if   ($this->Beacon=="MODULE")Process::$TempVar = $TempVar;
        }
	}

	// 	AFFICHAGE
	function Affich() {
		//On traite les element d entete
		$this->addHeader();
		if (!empty($this->Content))
            $this->Data = $this->parseData($this->Data,$this->Content);
        return (isset($this->Data))?$this->Data:'';
	}
}
?>