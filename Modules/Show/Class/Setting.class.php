<?php
class Setting extends genericClass {
	
	public function LoadData($args) {
		if($args['mode'] == 'domain') 
			$sql = "select distinct Domaine as name from `##_Show-Setting` order by Domain";
		else {
			$dom = $args['domain'];
			$sql = "select distinct SubDomain as name from `##_Show-Setting` where Domain='$dom' order by SubDomain";
		}
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tmp = [];
		foreach($pdo as $p) $tmp[] = $p['name'];
		return $tmp;
	}
}

