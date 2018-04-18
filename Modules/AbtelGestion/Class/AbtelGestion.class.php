<?php

include "Modules/AbtelGestion/Class/AbtelGestionBase.class.php";

class AbtelGestion extends Module {

	static $DB = NULL;  // handle sur la base de données
	static $Input = ''; // données envoyées lors de l'appel;

	function init() {
		parent::init();
		self::OpenDB();
		self::$Input = json_decode(file_get_contents("php://input"), 1);
	}

	/**
	 * ouverture de la base de données
	 */
	static function OpenDB() {
		if(!self::$DB) {
			try {
				self::$DB = new PDO('mysql:host=10.0.3.130;dbname=gestion;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
			} catch(Exception $e) {
				self::$DB = NULL;
				klog::l("ERREUR BASE DE DONNEES", $e);
			}
		}
		return self::$DB;
	}

	/**
	 * paramètres de liste standard envoyés lors de l'appel
	 */
	static function getListParam(&$where, &$order, &$offset, &$limit) {
		$where = isset(self::$Input['filter']) ? self::$Input['filter'] : '';
		$order = isset(self::$Input['order']) ? self::$Input['order'] : '';
		$offset = isset(self::$Input['offset']) ? self::$Input['offset'] : '';
		$limit = isset(self::$Input['limit']) ? self::$Input['limit'] : '';
	}

	/*
	 * exécute use requete SQL
	 */
	static function getSQLData($sql, $all = false) {
		try {
			$sts = AbtelGestion::$DB->query($sql);
			if($all) $result = $sts->fetchAll(PDO::FETCH_ASSOC);
			else $result = $sts->fetch(PDO::FETCH_ASSOC);
		} catch(Exception $e) {
			throw $e;
		}
		return $result;
	}
	
	/*
	 * demande l'enregistrement id
	 */
	static function getRecord($sql, $sql1 = '') {
		try {
			$result = self::getSQLData($sql);
			if($result !== FALSE && !empty($sql1)) $result['_LIGNES_'] = self::getSQLData($sql1, true);
		} catch(Exception $e) {
			throw $e;
		}
		return $result;
	}

	/*
	 * demande une liste d'enregistrements type kobeye
	 */
	static function getRecords($sql, $where='', $order='', &$offset=0, &$limit=0) {
		if($where) $sql .= "where $where ";
		if($order) $sql .= "order by $order ";
		if($limit) {
			$sql .= "limit $limit ";
			if($offset) $sql .= "offset $offset ";
		}
		try {
			$result = self::getSQLData($sql, true);
			$limit = count($result);
		} catch(Exception $e) {
			throw $e;
		}
		return $result;
	}

}

?>
