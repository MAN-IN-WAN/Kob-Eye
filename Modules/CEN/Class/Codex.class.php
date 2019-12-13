<?php

class Codex extends genericClass {
	
	static function GetCodex($args) {
		$type = $args['type'];
		$id = isset($args['id']) ? $args['id'] : '';
		$ext = isset($args['ext']) ? $args['ext'] : '';
		$ln = strlen($ext);

		$dicId= array();
		$dic = array();		
		switch($type) {
			case 'codex':
				$dics = Sys::getData('CEN', 'Codex', 0, 999, 'ASC', 'Code');
				foreach($dics as $d) {
					$id = $d->Id;
					$dic[] = array('id'=>$id, 'code'=>$d->Code, 'title'=>$d->Titre, 'selected'=>true, 'imgSel'=>false, 'dir'=>'/Home/'.$d->userCreate.'/CEN/Codex/'.$d->Repertoire);
					$dicId[$id] = $d->Titre;
				}
				return array('codexId'=>$dicId, 'codex'=>$dic);
				
			case 'planche':
				$dics = Sys::getData('CEN', 'Planche/CodexId='.$id, 0, 999, 'ASC', 'Cote');
				foreach($dics as $d) {
					$dic[] = array('id'=>$d->Id, 'cote'=>$d->Cote);
				}
				return array('planches'=>$dic);
				
			case 'zone':
				$sql = "select Id,Cote from `##_CEN-Zone` where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$dic = array();
				foreach($pdo as $d) {
					$dic[] = array('id'=>$d['Id'], 'cote'=>trim($d['Cote']));
				}
				return array('zones'=>$dic);
		}
		
	}

	static function GetPlanches($args) {
		$id = $args['codex'];
	}
	
	static function GetZones($args) {
		$id = $args['codex'];
		$pl = $args['planche'];
		$l = strlen($pl);
	}
	
}