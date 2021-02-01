<?php

class Tonalpohua extends genericClass {
	
	static public function InitApp($args) {
		$lang = $args['lang'];
		$codex = $args['codex'];
		
		$sql = "select Id,Type,Nahuatl,Maya,$lang,Regles from `##_CEN-Tonalpohua` where Type is not null order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$ton = array();
		foreach($rs as $r) $ton[$r['Id']] = ['id'=>$r['Id'],'type'=>$r['Type'],'nahuatl'=>$r['Nahuatl'],'maya'=>$r['Maya'],'trans'=>$r[$lang],'rules'=>$r['Regles']];
		
		$sql = "select Id,Xihuitl,MoisInitial,Parametres from `##_CEN-ToXihuitl` order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$xih = array();
		foreach($rs as $r) $xih[] = ['id'=>$r['Id'],'xihuitl'=>$r['Xihuitl'],'mois'=>$r['MoisInitial'],'params'=>$r['Parametres']];
		
		$sql = "select Id,Codex,Repertoire,Tonalli from `##_CEN-ToCodex` order by Ordre";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cod = array();
		foreach($rs as $r) $cod[] = ['id'=>$r['Id'],'codex'=>$r['Codex'],'directory'=>$r['Repertoire'],'tonalli'=>$r['Tonalli']];
		
		$sql = "select Id,Nahuatl,Regles from `##_CEN-Tonalpohua` where Type=5 order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$nem = array();
		foreach($rs as $r) $nem[$r['Id']] = ['id'=>$r['Id'],'nahuatl'=>$r['Nahuatl'],'rules'=>$r['Regles']];
		
		$sql = "select Id,Nahuatl,Regles from `##_CEN-Tonalpohua` where Type=6 order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$epo = array();
		foreach($rs as $r) $epo[$r['Id']] = ['id'=>$r['Id'],'nahuatl'=>$r['Nahuatl'],'rules'=>$r['Regles']];
		
		$sql = "select e.Id,Exemple,Regles,ToCodexId from `##_CEN-ToExemple` e "
			."left join `##_CEN-ToCodex` c on c.Id=e.ToCodexId order by c.Ordre,e.Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$exp = array();
		foreach($rs as $r) $exp[$r['Id']] = ['id'=>$r['Id'],'exemple'=>$r['Exemple'],'rules'=>$r['Regles'],'codex'=>$r['ToCodexId']];

		
		$days = ToCodex::GetImageTable(['codex'=>$codex, 'type'=>0]);
		$months = ToCodex::GetImageTable(['codex'=>$codex, 'type'=>1]);
		$number = ToCodex::GetImageTable(['codex'=>$codex, 'type'=>2]);
		
		$type2role = ['Jour'=>'Jour','Mois'=>'Mois','Nombre'=>'Nombre','A'=>'Dieu du jour','B'=>'Seigneur diurne','C'=>'Seigneur nocturne',
			'D'=>'Dieu de la treizaine','O'=>'Volatile','P'=>'Arbre','M'=>'Glyphe de la vingtaine'];


		return array('success'=>true,data=>['tonalpohua'=>$ton,'xihuitl'=>$xih,'codex'=>$cod,'days'=>$days['images'],'months'=>$months['images'],
			'roles'=>$type2role,'nemontemi'=>$nem,'eponymes'=>$epo,'exemples'=>$exp,'translation'=>[],'directory'=>'/Home/2/CEN/ToCodex/']);
	}

	static public function GetGodText($args) {
		$lang = $args['lang'];
		$god = strtoupper($args['god']);
	
		$cwd = getcwd();
		$file = "$cwd/Home/2/CEN/Tonalpohua/text/Dioses/$god.rtf";
		$html = self::importRtf($file);
		
		return ['success'=>$html != '', 'text'=>$html, 'file'=>$file];
	}
	
	static private function importRtf($file) {
		require_once ('Class/Lib/rtf-html-php.php');
		
		$html = '';
		if(file_exists($file)) {
			$reader = new RtfReader();
			$rtf = file_get_contents($file);
			$result = $reader->Parse($rtf);
			if($result) {
				$formatter = new RtfHtml();
				$html = $formatter->Format($reader->root);
			}
		} 
		return $html;
	}

}