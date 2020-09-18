<?php
class Setting extends genericClass {
	
	public function LoadData($args) {
		if($args['mode'] == 'domaine') 
			$sql = "select distinct Domaine as name from `##_Cadref-Parametre` order by Domaine";
		else {
			$dom = $args['domaine'];
			$sql = "select distinct SousDomaine as name from `##_Cadref-Parametre` where Domaine='$dom' order by SousDomaine";
		}
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tmp = [];
		foreach($pdo as $p) $tmp[] = $p['name'];
		return $tmp;
	}

}
