<?php
/**
* Kob-eye - MESSIN Enguerrand
* Gestionnaire d'erreur 
* 
*/
class KError extends Root{
	//Tableau d'erreur
	public static $Tableau = Array();

	//Variables statiques des priorites
	public static $INFO = 10;
	public static $WARNING = 5;
	public static $FATAL = 1;

	/**
	* Constructeur
	* void
	*/
	public function KError(){
	}
	
	/**
	* Ajoute une erreur dans le tableau erreur
	* @param $Type Type de l'erreur (MYSQL/PHP/KEML/XML...)
	* @param $Message Contenu du message
	* @param $Priority Priorite de l'erreur (INFO/WARNING/FATAL)
	* void
	*/
	public static function Set($Type,$Message,$Priority=10){
		KError::$Tableau[] = Array($Type,$Message,$Priority);
	}

	/**
	* detecte la presence d'erreur
	* @return Boolean 
	*/
	private function isError() {
		return sizeof($this->Tableau);
	}

	/**
	* renvoie les erreurs d'un certain niveau de priorite
	* @param $Priority Priorite des erreurs à selectionner pour l'affichage
	* @return un tableau à deux dimensions avec les erreurs demandees.
	*/
	private function getErrors($Priority=1){
		$o  = Array();
		foreach ($this->Tableau as $t):
			if ($t[2]<=$Priority) $o[]=$t;
		endforeach;
		return $o;
	}

    /**
     * renvoie les erreurs d'un certain type
     * @param $Type Type des erreurs à selectionner pour l'affichage
     * @return un tableau à deux dimensions avec les erreurs demandees.
     */
    public static function returnErrors($Type=null){
        $o  = Array();
        foreach (self::$Tableau as $t){
            if ($t[0]==$Type) {
                $o[]=$t;
            }
        }
        return $o;
    }

	/**
	* Affiche l'entete du gestionnaire d'erreur
	* Panneau en javascript permettant d'afficher les erreurs
	* @return String
	*/
	static public function displayHeader(){
		//CSS
		$o = '<style type="text/css">';
		$o .= '#top-panel{background:#990000;border-bottom:3px solid #990000;position:absolute;top:-80%;width:100%;z-index:100000;height:80%;}';
		$o .= '#sub-panel{text-align:center;opacity:0.9;}';
		$o .= '#sub-panel a#toggle{width:150px;float:right;color:#FFFFFF;text-decoration:none;margin-right:30px;font-weight:bold;background:#990000;position: absolute;right: 20px;outline:0;bottom:-25px;}';
		$o .= '#sub-panel a#toggle span{padding:6px;background:url(img/sub-right.png) right bottom no-repeat;display:block;}';
		$o .= '#sub-panel a#clear{width:150px;float:right;color:#FFFFFF;text-decoration:none;margin-right:200px;font-weight:bold;background:#660000;position: absolute;right: 20px;outline:0;bottom:-25px;}';
		$o .= '#sub-panel a#clear span{padding:6px;background:url(img/sub-right.png) right bottom no-repeat;display:block;}';
		$o .= '#content-panel{background:white;display:block;height:100%;width:100%;overflow:auto;}';
		$o .= '#content-panel h2,h1{display:block;padding:0 5px;background:#B5FFAE;margin:1px;}';
		$o .= '#content-panel h1{font-size:12px;background:#00FF91;margin-top:10px;}';
		$o .= '#content-panel h2{font-size:10px;margin-left:10px;}';
		$o .= '#content-panel .warning{background:#FFF79E;}';
		$o .= '#content-panel .fatal{background:#FF7C7C;}';
		$o .= '#content-panel p{background:white;display:block;overflow:hidden;padding:0 10px;margin:1px;margin-left:10px;}';
		$o .= '</style>';
		//JAVASCRIPT
		if (DEBUG_INCLUDE_MOOTOOLS)$o .= '<script type="text/javascript" src="/Skins/AdminV2/Js/mootools.js"></script>';
		$o .= '<script type="text/javascript">
				window.addEvent(\'domready\', function(){
					var mySlide = new Fx.Tween(\'top-panel\');
					mySlide.set(\'top\',-$("top-panel").height);
					$(\'toggle\').addEvent(\'click\', function(e){
						e = new Event(e);
						if ($(\'top-panel\').getPosition().y==0){
							mySlide.start("top",0,(-parseInt($("top-panel").getSize().y))+3);
						}else{
							mySlide.start("top",(-parseInt($("top-panel").getSize().y))+3,0);
						}
						e.stop();	
					});
					$(\'clear\').addEvent(\'click\', function(e){
						e = new Event(e);
						$("content-panel").innerHTML=\'\';
						e.stop();	
					});
				});
				function addError(titre,message,type){
					$("content-panel").innerHTML=\'<h2 class="\'+type+\'">\'+titre+\'</h2><p>\'+message+\'</p>\'+$("content-panel").innerHTML;
				}
				function addPage(titre){
					$("content-panel").innerHTML=\'<h1>\'+titre+\'</h1>\'+$("content-panel").innerHTML;
				}
			</script>';
		return $o;
	}

	/**
	* Affiche le html du gestionnaire d'erreur
	* Panneau en javascript permettant d'afficher les erreurs
	* @return String
	*/
	static public function displayHtml(){
		//HTML
		$o = '<div id="top-panel"><div id="content-panel"></div><div id="sub-panel"><a href="#" rel="noMoreAjax" id="toggle"><span>Debug</span></a><a href="#" rel="noMoreAjax" id="clear"><span>Clear</span></a></div></div>';
		$o .= KError::displayErrors();
		return $o;
	}

	/**
	* Affiche les erreurs pour mettre à jour en mode javascript
	* @return String
	*/
	static public function displayErrors(){
		$o='<script type="text/javascript">';
		foreach (KError::$Tableau as $t):
			$type = ($t[2]==KError::$FATAL)?'fatal':(($t[2]==KError::$WARNING)?'warning':'info');
			$o.='addError("'.addslashes($t[0]).'","'.addslashes($t[1]).'","'.$type.'");';
		endforeach;
		$o.='addPage("GET '.$_SERVER["REQUEST_URI"].'");';
		$o.='</script>';
		return $o;
	}
	
	/**
	* Affiche une erreur fatale
	* @param string erreur
	*/
	static public function fatalError($erreur){
//		echo "<h1>ERREUR FATALE</h1>";
//		echo "<p>$erreur</p>";
//		die();
	}
	/**
	 * Affiche un message
	 * @param Error
	 */
	public function sendUserMsg($E,$M){
		echo "<div class='Error'>$E - $M</div>";
	}
	
	/**
	 * Static création de event error particulier
	 * @param String Message de l'erreur
	 * @param Object Objet de creation de l'erreur
	 * @param String Code de l'erreur
	 * @void
	 */
	 public static function addError($msg,$obj,$code="0"){
	 	$e = genericClass::createInstance('Systeme','Event');
		$e->Name="ERROR: ".$msg;
		$e->Type = "Error";
		$e->EventModule = $obj->Module;
		$e->EventObjectClass = $obj->ObjectClass;
		$e->EventId = $obj->Id;
		$e->Save();
	 }
	/**
	 * Static création de event success particulier
	 * @param String Message du succes
	 * @param Object Objet de creation du succes
	 * @param String Code de succes
	 * @void
	 */
	 public static function addSuccess($msg,$obj,$code="0"){
	 	$e = genericClass::createInstance('Systeme','Event');
		$e->Name="SUCCESS: ".$msg;
		$e->Type = "Success";
		$e->EventModule = $obj->Module;
		$e->EventObjectClass = $obj->ObjectClass;
		$e->EventId = $obj->Id;
		$e->Save();
	 }
	/**
	 * Static création de event informatif
	 * @param String Message d'information
	 * @param Object Objet d'information
	 * @param String Code d'information
	 * @void
	 */
	 public static function addInformation($msg,$obj,$code="0"){
	 	$e = genericClass::createInstance('Systeme','Event');
		$e->Name="INFO: ".$msg;
		$e->Type = "Information";
		$e->EventModule = $obj->Module;
		$e->EventObjectClass = $obj->ObjectClass;
		$e->EventId = $obj->Id;
		$e->Save();
	 }
}