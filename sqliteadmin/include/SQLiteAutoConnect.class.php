<?php
include_once INCLUDE_LIB . 'sqlite.class.php';

class SQLiteAutoConnect {
    function __construct() {

    }
    
    function sqliteGetInstance($dbPath, $forceVersion = null) {
      error_reporting(E_ALL & ~(E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE | E_STRICT));
    	if(!$forceVersion) {
    		if($dbPath == ':memory:') $dbVersion = min($GLOBALS['sqliteVersionAvailable']);
    		else $dbVersion = sqlite::getDbVersion($dbPath);
    	} else {
    		$dbVersion = $forceVersion;
    	}
		if($dbVersion && (($dbVersion == 2) || ($dbVersion == 3)) ) {
			include_once INCLUDE_LIB . 'sqlite_'.$dbVersion.'.class.php';
	    	$classObj = 'sqlite_' . $dbVersion;
			return new $classObj($dbPath);
    	} else return false;
    }
}
?>