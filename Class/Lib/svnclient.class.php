<?php
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once('svn' . DS . 'phpsvnclient.php');
require_once('svn' . DS . 'xml2Array.php');

class svnclient {

	private $svn;
	private $url;
	private $root;
	private $time;
	private $listFiles;


	/**
	 * Constructeur
	 * -> Initialisation de l'objet phpsvnclient
	 * -> Définition de l'adresse du SVN
	 */
	public function __construct() {
		$this->url = 'http://svn.kob-eye.com/svn/project_kobeye/';
		$this->root = dirname(dirname(dirname(__FILE__)));
		$this->svn = new phpsvnclient();
		$this->svn->setRepository( $this->url );
		$this->listFiles = array();
		$this->time = time();
	}

	/**
	 * Retourne la liste des fichiers qui a été mise à jour
	 * @return	Array	La liste
	 */
	public function getFilesUpdated() {
		return $this->listFiles;
	}

	/**
	 * Récupère la liste des sous dossiers
	 * @param	string	Skins ? Modules ?
	 * @return	Array	Liste des répertoires
	 */
	public function getAll( $type ) {
		$return = array();
		$dir = 'trunk/'.$type;
		$files = $this->svn->getDirectoryFiles($dir);
		foreach($files as $f) $return[] = substr($f['path'],strlen($dir)+1);
		return $return;
	}

	/**
	 * Mise à jour
	 * -> Class
	 * -> Skins/AdminV2
	 */
	public function update( $dossiers, $forcer ) {
		$this->ForceUpdate = $forcer;
		if(is_array($dossiers)) foreach($dossiers as $d)  $this->getDirectory('trunk/' . $d, $this->root . DS . $d);
		$this->getFile('trunk/.htaccess', $this->root);
		$this->getFile('trunk/index.php', $this->root);
		$this->getFile('trunk/cron.php', $this->root);
		file_put_contents($this->root . '/version', $this->svn->getVersion());
	}

	/**
	 * Créé un dossier local et récupère tous les fichiers (récursif)
	 * @param	string	Répertoire que l'on veut récupérer sur le svn
	 * @param	string	Répertoire équivalent local où l'on veut récupérer
	 * @return 	void
	 */
	private function getDirectory( $dir, $to ) {
		// Création de l'arborescence locale si elle n'existe pas
		if(!file_exists($to) and !is_dir($to)) {
			$path = explode(DS, $to);
			$limit = sizeof($path);
			for($i=1; $i<$limit; $i++) {
				$partArr = array_slice($path, 0, $i+1);
				$partPath = implode(DS, $partArr);
				if(!file_exists($partPath) and !is_dir($partPath)) {
					mkdir($partPath);
					chmod($partPath, 0705);
				}
			}
		}

		// Récupération des fichiers (seulement ceux qui ont besoin d'une mise à jour)
		$files = $this->svn->getDirectoryFiles($dir);
		unset($files[0]);
		$updated = array();
		foreach($files as $file) {
			$filepath = substr($file['path'], 6);
			if($file['type'] == 'file' && ($this->ForceUpdate == 1 || !file_exists($filepath) || filemtime($filepath) < strtotime($file['last-mod']))) {
				$this->getFile($file['path'], $to);
			}
			elseif($file['type'] == 'directory' && (!file_exists($filepath))) {
				// Dossier n'existe pas -> il sera créé dans getDirectory
				$this->listFiles[] = substr($file['path'], 6);
			}
			// Si c'est un répertoire on le parcours récursivement
			$dirname = substr($file['path'], strrpos($file['path'], '/') + 1);
			if($file['type'] == 'directory') $this->getDirectory($file['path'], $to . DS . $dirname);
		}
	}

	/**
	 * Récupère un fichier
	 * @param	string	Fichier que l'on veut récupérer
	 * @param	string	Dossier local dans lequel on veut créer le fichier
	 * @return 	void
	 */
	private function getFile( $from, $to ) {
		$filepath = substr($from, 6);
		// Exceptions
		if(in_array($filepath, array("Conf/General.conf"))) return;
		$this->listFiles[] = $filepath;
		// Fichier n'existe pas ou est obsolète -> on renome l'existant et on récupère le nouveau
		if(file_exists($filepath)) rename($filepath, $filepath . '-' . date('Y-m-d', filemtime($filepath)));
		// Création du fichier
		$filename = substr($from, strrpos($from, '/') + 1);
		$content = $this->svn->getFile($from);
		file_put_contents($to . DS . $filename, $content);
		chmod($to . DS . $filename, 0705);
	}

}
