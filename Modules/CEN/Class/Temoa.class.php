<?php

class Temoa extends genericClass {

	// importation d'un dictionnaire
	function Save() {
		$fok = $this->ZipFile != '';
		$id = $this->Id;
		if($id) {
			$old = Sys::getOneData('CEN', "Temoa/$id");
			if($this->ZipFile == $old->ZipFile) $fok = false;
		}
		
		if($fok) {
			$uid = Sys::$User->Id;
			$dir = "./Home/$uid/CEN/Temoa/".$this->Code;
			mkdir($dir);
			$zip = new ZipArchive;
			$res = $zip->open($this->ZipFile);
			if($res === TRUE) {
				$zip->extractTo($dir);
				$zip->close();
				$f = "$dir/$this->Code";
				//$this->Nahuatl = file_get_contents("$f.rtf");
				//$this->Trad1 = file_exists("$f".'trad.rtf') ? file_get_contents("$f".'trad.rtf') : '';
				//$this->Trad2 = file_exists("$f".'trad2.rtf') ? file_get_contents("$f".'trad2.rtf') : '';
				$this->importRtf($f.'_esp.rtf', 'PresentationEs'); 
				$this->importRtf($f.'_fra.rtf', 'PresentationFr'); 
				$this->importRtf($f.'_ang.rtf', 'PresentationEn'); 
				//unlink($this->ZipFile);
				//$this->ZipFile = "";
			}
			else $zip->Close();
		}
		return parent::Save();
	}
	
	private function importRtf($file, $field) {
		require_once ('Class/Lib/rtf-html-php.php');
		
		$this->{$field} = '';
		if(file_exists($file)) {
			$reader = new RtfReader();
			$rtf = file_get_contents($file);
			$result = $reader->Parse($rtf);
			if($result) {
				$formatter = new RtfHtml();
				$this->{$field} = $formatter->Format($reader->root);
			}
		} 
	}
	
	static function GetList($args) {
		$corpus = $args['corpus'];
		if($corpus == 'all') $corpus = '';
		else $corpus = "and Id in ($corpus)";

		$sql = "select Code,ZipFile from `##_CEN-Temoa` where 1 $corpus";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$corpus = '';
		foreach($pdo as $p) {
			$t = explode('/', $p['ZipFile']);
			$c = $p['Code'];
			$corpus .= getcwd()."/Home/$t[1]/CEN/Temoa/$c/$c.rtf;";
		}
		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');

		$temoa = new temoa2\Temoa();
		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
		$ret = $temoa->SetCorpus($corpus);

		$temoa->AddArrow($args['word']);
		if($temoa->Search()) {
			$s = $temoa->GetTargetsJson();
		}

		unset($temoa);

		$o = json_decode($s, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$words = count($o);
		$occur = 0;
		$docs = array();
		if($o) {
			foreach($o as $a) {
				$t = Sys::getOneData('CEN', 'Temoa/Code='.$a->doc);
				$a->id = $t->Id;
				$docs[$t->Id] = '';
				$a->title = $t->Nom;
				$occur += $a->count;
			}
		}
		return array('temoa'=>$o,'words'=>$words,'occur'=>$occur,'docs'=>count($docs));				
	}

	static function GetDocs() {
		$docs = Sys::getData('CEN', 'Temoa');
		$docId= array();
		$doc = array();
		foreach($docs as $d) {
			$id = $d->Id;
			$doc[] = array('id'=>$d->Id, 'title'=>$d->Nom, 'selected'=>1);
			$docId[$d->Id] = $d->Nom;
		}
		return array('documentsId'=>$docId, 'documents'=>$doc);		
	}
	
	static function getDocument($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		$c = $t->Code;
		$a = explode('/', $t->ZipFile);
		$f = getcwd()."/Home/$a[1]/CEN/Temoa/$c/$c";
		$trd = file_exists("$f.trad") ? 1 : 0;
		$tr2 = file_exists("$f.trad2") ? 1 : 0;
		
		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
		$temoa = new temoa2\Temoa();
		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
		$doc = $temoa->GetHTML("$f.rtf");
		$not = $temoa->GetNotesJson();
		$mrk = $temoa->GetMarksJson();
		unset($temoa);
		$note = json_decode($not, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$mrk = json_decode($mrk, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);

		return array('doc'=>utf8_encode($doc),'notes'=>count($note),'marks'=>$mrk,'trad'=>$trad,'trad2'=>$trad2 );
	}
	
	static function getNotes($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		$c = $t->Code;
		$a = explode('/', $t->ZipFile);
		$f = getcwd()."/Home/$a[1]/CEN/Temoa/$c/$c.rtf";
		
		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
		$temoa = new temoa2\Temoa();
		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
		$doc = $temoa->GetHTML($f);
		$not = $temoa->GetNotesJson();
		unset($temoa);
		$note = json_decode($not, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		foreach($note as &$n) $n->text = utf8_encode($n->text);
		return array('notes'=>$note);
	}

}