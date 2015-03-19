<?php
class Famille extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	

	function UploadTarif($code, $tarif) {
		if(! $tarif) return WebService::WSStatus('method',0,'','','','','',array(array("Fichier tarif non renseigné")),null);
		$file = file($tarif);
		$n = count($file);
		if(! $n) return WebService::WSStatus('method',0,'','','','','',array(array("Fichier vide")),null);
		$line = explode(';', $file[0],2);
		if(substr($line[0],0,3) != 'REF' || substr($line[1],0,3) != 'FAM')
			return WebService::WSStatus('method',0,'','','','','',array(array("Format de fichier incorrect")),null);

		$GLOBALS["Systeme"]->ConnectSql();
		if(! $code) $code = 0;
		for($i = 1; $i < $n; $i++) {
			$line = explode(';', $file[$i]);
			$m = count($line);
			$rec = Sys::$Modules['StockLocatif']->callData('Famille/Famille='.$line[1], false, 0, 1);
			if(! is_array($rec) || ! count($rec)) continue;
			$fam = $rec[0]['Id'];
			for($j = 2; $j < $m && $j < 18; $j++) {
				$tar = number_format($line[$j], 2, '.', '');
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx:$j:$tar",$line);
				$dur = $j - 1;
				$whr = "FamilleId=$fam and CodeTarifId=$code and DureeId=$dur";
				$sql = "select Id from `##_StockLocatif-Tarif` where $whr";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
				$sql = '';
				if(count($rec)) {
					if($tar) $sql = "update `##_StockLocatif-Tarif` set Prix=$tar where $whr";
					else $sql = "delete from `##_StockLocatif-Tarif` where $whr";
				}
				elseif($tar) $sql = "insert into `##_StockLocatif-Tarif` (FamilleId,CodeTarifId,DureeId,Prix,umod,gmod,omod) values ($fam,$code,$dur,$tar,7,7,7)";
				if($sql) {
					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
					$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				}
			}
		}
		return WebService::WSStatus('method',1,'','','','','',null,null);
	}

}
