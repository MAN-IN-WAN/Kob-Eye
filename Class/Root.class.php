<?php
class Root {
	static function classFolder($className, $folder = 'Class') {
		$T = Array("Sys" => "Class/Systeme", "Connection" => "Class/Systeme", "Module" => "Class/Systeme", "Beacon" => "Class/Beacon", "Bloc" => "Class/Beacon", "Condition" => "Class/Beacon", "Info" => "Class/Beacon", "Lib" => "Class/Beacon", "Stats" => "Class/Beacon", "Storproc" => "Class/Beacon", "charUtils" => "Class/Beacon", "editStruct" => "Class/Beacon", "Conf" => "Class/Conf", "Association" => "Class/DataBase", "DbAnalyzer" => "Class/DataBase", "ObjectClass" => "Class/DataBase", "genericClass" => "Class/DataBase", "mysqlDriver" => "Class/DataBase/Drivers", "sqliteDriver" => "Class/DataBase/Drivers", "fileDriver" => "Class/DataBase/Drivers", "ldapDriver" => "Class/DataBase/Drivers", "Chrono" => "Class/Debug", "Error" => "Class/Debug", "Klog" => "Class/Debug", "Process" => "Class/Process", "Trigger" => "Class/Process", "Classement" => "Class/Process/Trigger", "Journal" => "Class/Process/Trigger", "Total" => "Class/Process/Trigger", "TriggerFunction" => "Class/Process/Trigger", "WebService" => "Class/Rpc", "Header" => "Class/Template", "Skin" => "Class/Template", "Utils" => "Class/Utils");
		if (array_key_exists($className, $T))
			return $T[$className];
		$dir = dir(ROOT_DIR . $folder);
		if ($folder == 'Class' && file_exists(ROOT_DIR . $folder . '/' . $className . '.class.php'))
			return $folder;
		else {
			while (false !== ($entry = $dir -> read())) {
				$checkFolder = $folder . '/' . $entry;
				if (strlen($entry) > 2) {
					if (is_dir(ROOT_DIR . $checkFolder)) {
						if (file_exists(ROOT_DIR . $checkFolder . '/' . $className . '.class.php'))
							return $checkFolder;
						else {
							$subFolder = Root::classFolder($className, $checkFolder);
							if ($subFolder)
								return $subFolder;
						}
					}
				}
			}
		}
		$dir -> close();
		return 0;
	}

	/**
	 * Return a clone of this object
	 */
	function getClone() {
		return unserialize(serialize($this));
	}

	final public function __clone_array($d) {
		$t = "";
		if (is_array($d))
			foreach ($d as $k2 => $d2) {
				switch (gettype($d2)) {
					case "int" :
					case "string" :
						$t[$k2] = $d2;
						break;
					case "array" :
						$t[$k2] = $this -> __clone_array($d2);
						break;
					case "object" :
						$t[$k2] = $d2 -> __deepclone();
						break;
					default :
						break;
				}
			}
		return $t;
	}

	final public function __deepclone() {
		//Methode de clonage
		//On instancie un nouvel objet reflection pour avoir les information sur l objet a cloner.
		$class = get_class($this);
		$r = new ReflectionObject($this);
		//On cree un nouvel objet vide
		$temp = new $class(($this instanceOf genericClass) ? $this -> Module : 0);
		$Props = $r -> getProperties();
		//On copie l ensemble des proprietes
		foreach ($Props as $p) {
			$n = $p -> name;
			if (isset($this ->{$n}))
				switch (gettype($this->{$n})) {
					case "int" :
					case "string" :
						$temp -> {$n} = $this -> {$n};
						break;
					case "array" :
						$temp -> {$n} = $this -> __clone_array($this -> {$n});
						break;
					case "object" :
						$temp -> {$n} = clone $this -> {$n};
						break;
					default :
						break;
				}
		}
		unset($temp -> Id);
		return $temp;
	}

	static function mk_dir($path, $rights = 0777) {
		$folder_path = array($path);
		while (!@is_dir(dirname(end($folder_path))) && dirname(end($folder_path)) != '/' && dirname(end($folder_path)) != '.' && dirname(end($folder_path)) != '')
			array_push($folder_path, dirname(end($folder_path)));
		while ($parent_folder_path = array_pop($folder_path)) {
			if (@mkdir($parent_folder_path, $rights))
				//user_error("Can't create folder \"$parent_folder_path\".");
				chmod($parent_folder_path, $rights);
		}
	}

	function bubbleSort($tableau, $triChamp) {
		//Range tableau selon le champ triChamp
		$nbEnregistrement = count($tableau);
		for ($bubble = 1; $bubble < $nbEnregistrement; $bubble++) {
			for ($position = $nbEnregistrement - 1; $position > 0; $position--) {
				if ($tableau[$position][$triChamp] > $tableau[$position - 1][$triChamp]) {
					$temp = $tableau[$position];
					$tableau[$position] = $tableau[$position - 1];
					$tableau[$position - 1] = $temp;
				}
			}
		}
		return $tableau;
	}

	//Fred :: plus rapide et plus customisable, utilisé dans Connection
	static public function quickSort($seq, $field = "") {
		if (!count($seq))
			return $seq;

		$k = $seq[0];
		$x = $y = array();

		$length = count($seq);

		for ($i = 1; $i < $length; $i++) {
			if ($field != "") {
				if (is_array($seq[$i]))
					$test = $seq[$i][$field] <= $k[$field];
				if (is_object($seq[$i]))
					$test = $seq[$i]->{$field} <= $k->{$field};
			} else {
				$test = $seq[$i] <= $k;
			}
			if ($test) {
				$x[] = $seq[$i];
			} else {
				$y[] = $seq[$i];
			}
		}

		return array_merge(Root::quickSort($x, $field), array($k), Root::quickSort($y, $field));
	}

	/**
	 * Renvoie la variable d'une variable POST,GET,COOKIE
	 *
	 * @param String $Name Nom de la variable
	 * @return Any
	 */
	function getHtmlVar($Name) {
		if (!empty($_COOKIE[$Name])) {
			return $_COOKIE[$Name];
		}
		if (!empty($_POST[$Name])) {
			return $_POST[$Name];
		}
		if (!empty($_GET[$Name])) {
			return $_GET[$Name];
		}
		return false;
	}

	function doQuery($M, $Q, $A = "", $B = "", $C = "", $D = "", $E = "") {
		return $GLOBALS['Systeme'] -> Modules[$M] -> callData($Q, $A, $B, $C, $D, $E);
	}

	/**
	 * throwError
	 */
	static public function fatalError($Mess) {
		throw new Exception($Mess);
		debug_backtrace();
	}

}
?>