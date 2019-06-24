<?php
class Header extends Root{
	var $Content;
	var $Url;
	var $Tab;
	var $LastTab;
	var $Title;
    var $Html;
    var $Body;
	var $Type;
	private $Description;
	var $Keywords;
	var $Image;
	var $CssBegin = Array();
	var $CssEnd = Array();
	var $Css = Array();
	var $Js = Array();
	var $Files = Array();
    //force
    var $ForceTitle = false;
    var $ForceDescription = false;
    var $ForceKeywords = false;
    var $ForceImage = false;
    var $overwriteHeader = false;


    function Header ($Type="") {
        if ( (! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
            (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
            (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
            $proto = 'https';
        } else {
            $proto = 'http';
        }
		$this->Url=$proto.'://'.Sys::$domain.'/';
		$this->Title = $GLOBALS["Systeme"]->Titre;
		$this->Description = $GLOBALS["Systeme"]->Description;
		$this->Keywords = $GLOBALS["Systeme"]->MotsCles;
		$this->Type = $Type;
		$this->Body="";
	}

	public function addCss($css, $fin = false){
		if (!empty($css)) {
			//on vérifie l'existence du fichier
			if (in_array($css,$this->CssBegin)) return;
			if (in_array($css,$this->CssEnd)) return;
			if(!$fin) $this->CssBegin[] = $css;
			else array_unshift($this->CssEnd, $css);
		}
		//On regenrere le tableau de css
		$this->Css = array_merge($this->CssBegin,$this->CssEnd);
	}

	public function addJS($js, $fin = false){
		if (!empty($js)) {
			if(!$fin) $this->Js[] = $js;
			else array_unshift($this->Js, $js);
		}
	}
    function setTitle($Temp) {
        $this->Title = $Temp;
        $this->ForceTitle = true;
    }
    function setBody($Temp) {
        $this->Body = $Temp;
    }
    function setHtml($Temp) {
        $this->Html = $Temp;
    }
    function setImage($Temp) {
        $this->Image = $Temp;
        $this->ForceImage = true;
    }
    function setDescription($Temp) {
        $this->Description = $Temp;
        $this->ForceDescription = true;
    }
    function setClassification($Temp) {
        $this->Classification = $Temp;
    }
    function setReplyTo($Temp) {
        $this->ReplyTo = $Temp;
    }
    function setKeywords($Temp) {
        $this->Keywords = $Temp;
        $this->ForceKeywords = true;
    }
	function setFrameset($Tab,$Data="",$Name=0) {
		//Analyse du tableau et construction de la chaine
		if (is_array($Tab)) {
			$Data.="<frameset ";
			//Recuperation des attributs
			if (is_array($Tab["@"]))foreach ($Tab["@"] as $Key=>$Value) $Data.=$Key."=\"".$Value."\" ";
			$Data.=">";
			//Recuperation des frames
			if (is_array($Tab["#"]["FRAME"]))foreach ($Tab["#"]["FRAME"] as $Value) $Data.=$this->setFrame($Value);
			//Recuperation des frameset
			if (is_array($Tab["#"]["FRAMESET"]))foreach ($Tab["#"]["FRAMESET"] as $Value) $Data.=$this->setFrameset($Value);
			//Recuperation des noframes
			if (is_array($Tab["#"]["NOFRAME"]))foreach ($Tab["#"]["NOFRAME"] as $Value) $Data.=$this->setNoFrame($Value);
			$Data.="</frameset>";
			$this->Frameset[$Name] = $Data;
			return $Data;
		}
	}
	function setFrame($Tab) {
		//Analyse du tableau et construction de la chaine
		//Analyse du tableau et construction de la chaine
		$Data.="<frame ";
		//Recuperation des attributs
		if (is_array($Tab["@"]))foreach ($Tab["@"] as $Key=>$Value) {
			if ($Key=="name") $Name = $Value;
			if ($Key=="src") {
				$Value = str_replace("[URL]",$GLOBALS["Systeme"]->RegVars["Lien"],$Value);
				$Data.=$Key."=\"".$Value."\" ";
			}else{
				$Data.=$Key."=\"".$Value."\" ";
			}
		}
		//Recuperation des frameset
		if (is_array($Tab["#"]["FRAMESET"]))foreach ($Tab["#"]["FRAMESET"] as $Value) $this->setFrameset($Value,"",$Name);

		$Data.=" />";
		return $Data;
	}

	function setNoFrame($Tab) {
		//Analyse du tableau et construction de la chaine
		//Analyse du tableau et construction de la chaine
		$Data.="<noframe ";
		//Recuperation des attributs
		if (is_array($Tab["@"]))foreach ($Tab["@"] as $Key=>$Value) $Data.=$Key."=\"".$Value."\" ";
		$Data.=" >";
		$Data.="</noframe>";
		return $Data;
	}

	function isNotInTab($Data) {
		if (is_array($this->Tab) && count($this->Tab)>0) {
			foreach ($this->Tab as $Key) {
				if ($Key==$Data) return false;
			}
		}
		return true;
	}

	function isNotInLastTab($Data) {
		if (count($this->LastTab)>0) {
			foreach ($this->LastTab as $Key) {
				if ($Key==$Data) return false;
			}
		}
		return true;
	}

	function Add($Data,$Vars) {
		if ($Vars!="Last"){
			if ($this->isNotInTab($Data)) $this->Tab[] = $Data;
		}else{
			if ($this->isNotInLastTab($Data)) $this->LastTab[] = $Data;
		}
	}

	function getTab() {
		$Result="";
		if (is_array($this->Tab) && sizeof($this->Tab)) {
			foreach ($this->Tab as $Key) {
				$Result.="$Key\r\n";
			}
		}
		return $Result;
	}

	function getLastTab() {
        $Result="";
		if (is_array($this->LastTab) && sizeof($this->LastTab)) {
			foreach ($this->LastTab as $Key) {
				$Result.="$Key\r\n";
			}
		}
		return $Result;
	}

	function getFrame() {
		//Construction de la structure de frame
			$Result="<script>";
			$Result.="
				if (parent.frames.length<2){
					//Le cas ou c est la premiere page donc ecriture des frames
					document.write('".$this->Frameset[0]."');
				}";
			if (sizeof($this->Frameset)>1){
				$Result.="else{";
				$i=0;
				foreach ($this->Frameset as $Key=>$Value) {
					if ($Key!="0") {
						$Result.="
							if (this.name=='".$Key."'){	
								document.write('".$Value."');
							}
							";
					}
					$i++;
				}
				$Result.="}";
			}
			$Result.='</script>
			<body '.$this->Body.'>'; 
			return $Result;
		
	}


    function setMeta() {
        $uri = Sys::$link;
        if (!defined('SITEMAP_ALLOW_PARAMS') || !SITEMAP_ALLOW_PARAMS){
            $pos = strpos($uri, '?');
            if($pos !== FALSE) $uri = substr($uri, 0, $pos);
        }
        $code  = md5('http://'.Sys::$domain.'/'.$GLOBALS["Systeme"]->Lien);
        $code2  = md5('https://'.Sys::$domain.'/'.$GLOBALS["Systeme"]->Lien);
        $page = Sys::getOneData('Systeme','Page/MD5='.$code.'+MD5='.$code2);
        if ($page){
            if(!empty($page->Redirect)) {
                header('Location: ' . $page->Redirect, true, 301);
                exit();
            }
            //if (!$this->ForceTitle)
            $this->Title = $page->Title;
            if (!$this->ForceDescription)
                $this->Description = $page->Description;
            if (!$this->ForceKeywords)
                $this->Keywords = $page->Keywords;
            if (!$this->ForceImage)
                $this->Image = $page->Image;
        }else{
            //on prends le titre du menu par défaut
            $defmenu = Sys::$DefaultMenu;
            if ($defmenu) {
                $this->Title = $defmenu->Title;
                $this->Description = $defmenu->Description;
                $this->Keywords = $defmenu->Keywords;
            }
        }
    }
    function Affich() {
        //Récupréation de la page en cours
        $browser = $this->getBrowser() . ' ' . $this->getBrowser(true);
        $this->setMeta();

		//					<html ' . (empty($browser) ? '': 'class="'.$browser.'"') .'>
        $this->Content = '';
        if (!$this->overwriteHeader) {
            $this->Content .= '<!DOCTYPE HTML>
                            <!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 '.$browser.'" dir="ltr" lang="fr-FR"> <![endif]-->
                            <!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8 '.$browser.'" dir="ltr" lang="fr-FR"> <![endif]-->
                            <!--[if IE 8]>    <html class="no-js lt-ie9 '.$browser.'" dir="ltr" lang="fr-FR"> <![endif]-->
                            <!--[if IE 9]>    <html class="no-js lt-ie10 '.$browser.'" dir="ltr" lang="fr-FR"> <![endif]-->
                            <!--[if gt IE 8]><!--> <html class="no-js '.$browser.'" dir="ltr" lang="fr-FR" '.$this->Html.'> <!--<![endif]-->
                            <head>
                            <title>'.$this->Title.'</title>
                            <meta http-equiv="Content-Type" content="text/html; charset='.CHARSET_CODE.'" />
                            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
                            <meta name="description" content="'.$this->Description.'"/>
                            <meta name="keywords" content="'.$this->Keywords.'"/>
                            <meta name="robots" content="index,follow"/>
                            <meta name="rating" content="general"/>
                            <meta name="twitter:card" content="summary">

                            <!--<meta name="twitter:site" content="">-->
                            <meta name="twitter:title" content="'.$this->Title.'">
                            <meta name="twitter:description" content="'.$this->Description.'">
                            <!--<meta name="twitter:creator" content="">-->

                            <meta property="og:title" content="'.$this->Title.'" />
                            <meta property="og:type" content="article" />
                            <meta property="og:url" content="'.'http://'.Sys::$domain.'/'.$GLOBALS["Systeme"]->Lien.'" />';
        }else {
            $this->Content .= $this->getTab();
            $this->Content .= $this->getLastTab();
        }
        if (!empty($this->Image)){
            $this->Content .='
                        <meta property="og:image" content="'.'http://'.Sys::$domain.'/'.$this->Image.'.mini.600x600.jpg" />
                        <link rel="image_src" href="'.'http://'.Sys::$domain.'/'.$this->Image.'.mini.600x600.jpg" />
                        <meta name="twitter:image" content="'.'http://'.Sys::$domain.'/'.$this->Image.'.mini.250x250.jpg"> ';
        }

        $this->Content .='
                        <meta property="og:description" content="'.$this->Description.'" />
                        <meta property="og:site_name" content="'.Sys::$domain.'" />
                        
        ';

		if ($this->Type=="print"){
			$this->Content .= '<link type="text/css" rel="stylesheet" href="/Skins/'.Sys::$User->Skin.'/Css/print.css" />
';
		}
		if(defined('CSS_CACHE') && CSS_CACHE) {
			$this->checkCompressedFile( 'Css' );
			$this->Content .= '<link type="text/css" rel="stylesheet" href="/Skins/'.Sys::$User->Skin.'/Css/styles.gz.css" />
';
		}
		else {
			foreach($this->Css as $f) {
			    if(strpos($f,'/') === 0){
			        $f = substr($f,1);
                }
				$filename = (substr($f, 0, 4) == 'http') ? $f : '/'.$f;
				$this->Content .= '<link type="text/css" rel="stylesheet" href="'.$filename.'" />
';
			}
		}
		if(defined('JS_CACHE') && JS_CACHE) {
			$this->checkCompressedFile( 'Js' );
			$this->Content .= '<script type="text/javascript" src="'.$this->Url.'Skins/'.Sys::$User->Skin.'/Js/scripts.gz.js"></script>
';
		}
		else {
			foreach($this->Js as $f) {
                if(strpos($f,'/') === 0){
                    $f = substr($f,1);
                }
				$filename = (substr($f, 0, 4) == 'http') ? $f : '/'.$f;
				$this->Content .= '<script type="text/javascript" src="'.$filename.'"></script>
';
			}
		}
		if (!$this->overwriteHeader) {
            $this->Content .= $this->getTab();
            $this->Content .= $this->getLastTab();
        }
		if (DEBUG_DISPLAY) $this->Content.=KError::displayHeader();
		$this->Content .='</head>
';
        $this->Content.='<body '.$this->Body.'>';
		return $this->Content;
	}

	function getFooter(){
		
		return '</body></html>';
	}

	/**
	 * Vérifie les fichiers compressés CSS ou JS
	 * @param	string	Css | Js
	 * @return	void
	 */
	function checkCompressedFile( $type ) {
		// Type incorrect
		if($type != 'Js' && $type != 'Css') return;

		// Détection fichier
		$file = 'Skins/' . Sys::$User->Skin . '/' . $type . '/' . (($type == 'Js') ? 'scripts.gz.js' : 'styles.gz.css');
		$outOfDate = false;

		if(file_exists($file)) {
			// Fichier existe on vérifie qu'il est à jour
			// Pas de template modifié depuis
			$AT = Sys::$Modules['Systeme']->callData('ActiveTemplate/tmsEdit>'.filemtime($file));
			if(is_array($AT)) $outOfDate = true;
			// Pas de fichier modifié depuis
			if(file_exists($file.'.cache')) {
				// Controle de chaque fichier
				$allFiles = unserialize(file_get_contents($file.'.cache'));
				foreach($allFiles as $f) {
					if(is_array($f)) continue;
					if(!is_file($f['file']) || filemtime($f['file'])>$f['tms']) $outOfDate = true;
				}
			}
			else {
				// Pas de fichier info donc pas à jour
				$outOfDate = true;
			}
		}
		else {
			// Fichier n'existe pas
			$outOfDate = true;
		}

		// Demande de refaire le fichier
		if($outOfDate) {
			$AT = Sys::$Modules['Systeme']->callData('ActiveTemplate');
			if(is_array($AT)) foreach($AT as $k => $T) {
				$AT[$k] = genericClass::createInstance('Systeme', $T);
				$AT[$k] = Template::initFrom($AT[$k]);
				$this->Css = array_merge($AT[$k]->getList('Css'), $this->Css);
			}
			$this->createCompressedFile( $type, $file );
		}
	}

	/**
	 * Compression GZ des styles ou JS dans un fichier
	 * @param	string	Path du fichier
	 * @param	string	Css | Js
	 * @return	void
	 */
	function createCompressedFile ( $type, $file ) {
		// Type incorrect
		if($type != 'Js' && $type != 'Css') return;

		// Contenu du fichier
		$contenu = "";
		foreach($this->{$type} as $f) {
			$contenu .= $this->getFileContent( $f );
		}

		// Compression
		if($type == 'Js') {
			// $Packer = new JavaScriptPacker($contenu);
			// $contenu = $Packer->pack();
		}
		else {
			require_once('Class/Template/CompressorCSS.php');
			$contenu = Minify_CSS_Compressor::process($contenu);
		}

		// Ecriture dans le fichier
		$dir = substr($file, 0, strrpos($file, '/') + 1);
		if(!is_dir($dir)) mkdir($dir);
		file_put_contents($file, $contenu);

		// Ecriture infos
		foreach($this->Files as $k => $f) {
			if(is_array($f)) continue;
			$this->Files[$k] = array(
				'file' => $f,
				'tms' => (is_file($f) ? filemtime($f) : -1)
			);
		}
		file_put_contents($file.'.cache', serialize($this->Files));
	}

	/**
	 * Récupère le contenu d'un fichier
	 * -> Prise en charge des @import dans les CSS
	 * @param	string	Path du fichier
	 * @return	void
	 */
	function getFileContent( $file ) {
		// Fichier déjà inclus
		if(in_array($file, $this->Files)) return '';
		$this->Files[] = $file;

		// Fichier non existant
		if(!file_exists($file)) return;

		// Données
		$content = file_get_contents($file);
		$filePath = substr($file, 0, strrpos($file, '/') + 1);
		$fileParentPath = substr($filePath, 0, strrpos($filePath, '/', -2) + 1);

		// Gestion des import CSS
		preg_match_all('#\@import "(.*?)";#', $content, $matches);
		//$content = str_replace('../', $this->Url.$fileParentPath, $content);
		if(!empty($matches[1])) foreach($matches[1] as $m) {
			$content = str_replace('@import "'.$m.'";', $this->getFileContent($filePath . $m), $content);
		}

		// Retour
		return $content.'
		';
	}


	function getBrowser( $versionNum = false )	{

	if(isset($_GET['agent'])) echo $_SERVER['HTTP_USER_AGENT'];
		$browsers = array (
            'MSIE',
            'Epiphany',
            'Firefox',
            'Konqueror',
            'Chromium',
            'Chrome',
            'Opera',
            'Safari',
            'Netscape',
            'Wget',
            'SeaMonkey',
            'Lynx',
            'Links',
            'Minimo',
            'Flock',
            'Iceweasel'
        );

		$logiciel = '';
		$version = '';
	
		foreach($browsers as $chaine) {
			if(strpos(Sys::$user_agent, $chaine ) !== FALSE) {
				$logiciel = $chaine;
				$version = $this->browser_version($logiciel);
				break;
			}
		}

		return $versionNum ? $logiciel.$version : $logiciel;
	}
	

	function browser_version($browser)	{
		$sep = '/';
		if($browser == 'MSIE') $sep = ' ';
		$pos = strpos($_SERVER['HTTP_USER_AGENT'], $browser . $sep);
		if($pos !== FALSE) {
			$pos += strlen($browser . $sep);
			$posEnd = strpos($_SERVER['HTTP_USER_AGENT'], ' ', $pos);
			if($posEnd === FALSE) $posEnd = strlen($_SERVER['HTTP_USER_AGENT']);
			$version = substr($_SERVER['HTTP_USER_AGENT'], $pos, $posEnd-$pos);
			$v = explode('.', $version);
			return floor($v[0]);
		}
		return '';
	}
}

