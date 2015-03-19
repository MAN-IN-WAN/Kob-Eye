<?php 
/**
 * Plugin class 
 * - A plugin define a set of configurable options within an objectclass
 * - Some comportment will be added by a specific class
 * - A plugin give also acces to specific html interfaces
 * 
 * A plugin implement an interface defined in the folder Modules/MODULE_NAME/Plugins/OBJECTCLASS_NAME/
 */
class Plugin extends Root{
	var $Config;
	var $Params;
	
	/**
	 * constructor private (abstract class)
	 */
	private function __construct() {}
	/**
	 * createInstance
	 * static function giving the right instance of a plugin
	 * @param Module string 
	 * @param ObjectClass string
	 * @param Plugin string plugin name
	 * @return Object instance of class plugin
	 */
	static function createInstance($Module,$ObjectClass,$Plugin){
		//verification de l'existence du dossier plugin et du fichier de class
		if (!file_exists('Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$Plugin)
			||!file_exists('Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$Plugin.'/'.$Plugin.'.class.php')
			||!file_exists('Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$Plugin.'/Plugin.conf')
			) return false;
		//Inclusion des interfaces si existante
		if (file_exists('Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$ObjectClass.'.interface.php'))
			require_once('Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$ObjectClass.'.interface.php');
		//inlusion de la classe
		require_once('Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$Plugin.'/'.$Plugin.'.class.php');
		//instanciation de l'objet
		$className = $Module.$ObjectClass.$Plugin;
		$O = new $className();
		//Construction du chemin vers le fichier de configuration par defaut
		$path =  'Modules/'.$Module.'/Plugins/'.$ObjectClass.'/'.$Plugin.'/Plugin.conf';		
		//initialisation de la configuration par defaut
		$O->setConfig(file_get_contents($path));
		//retour de l'objet
		return $O;
	}
	/**
	 * setConfig
	 * initialisation de la configuration à partir du fichier xml
	 * @void
	 */
	public function setConfig($Config) {
		$x = new xml2array($Config);
		$this->Config = $x->Tableau["PLUGIN"]["#"];
		if (isset($this->Config["PARAMS"][0]["#"]['PARAM']))foreach ($this->Config["PARAMS"][0]["#"]['PARAM'] as $P){
			$this->Params[$P["@"]["name"]] = $P["#"];
		}
	}
	/**
	* Return a template initialized from a genericClass object
	* @param genericClass Object required
	* @return Template
	*/
	static function initFrom($t){
		if (is_array($t))$Object = $t[0];
		else $Object = $t;
		$Module=$Object->Module;
		$ObjectClass=$Object->ObjectType;
		$Plugin=$Object->Plugin;
		$temp=Plugin::createInstance($Module,$ObjectClass,$Plugin);
		$temp->setConfig($Object->PluginConfig);
		return $temp;
	}
	/**
	 * getPlugins
	 * Retourne la liste des plugins d'un module et d'un objectclass donné
	 * @param Module string
	 * @param ObjectClass string
	 * @return Array of String
	 */
	static function getPlugins($Module,$ObjectClass){
		$dir = 'Modules/'.$Module.'/Plugins/'.$ObjectClass;
		$out=Array();
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir($dir.'/'.$file) && !preg_match("#^\..*#",$file)) {
					$out[] = $file;
				}
			}
			closedir($handle);
		}
		return $out;
	}
}
?>