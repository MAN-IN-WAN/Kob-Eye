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
			if($res === TRUE) $zip->extractTo($dir);
			$zip->close();
		}
		$tmp = explode('/', $this->ZipFile);
		$dir = "./Home/".$tmp[1]."/CEN/Temoa/".$this->Code;
		$f = "$dir/$this->Code";
		
		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
		$temoa = new temoa2\Temoa();
		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
		$this->DocText = $temoa->GetText($f.".rtf");
		
		$this->DocHtml = $temoa->GetHTML($f.".rtf");
		$this->DocNoteCount = $temoa->GetNoteCount();
		$this->DocNotes = $temoa->GetNotesJson();
		$this->DocMarks = $temoa->GetMarksJson();
		$this->DocLines = $temoa->GetLinesJson();
		
		if(!$temoa->GetPicts($f.'_esp.rtf')) $temoa->GetPicts($f.'_fra.rtf');
		$this->DocPictCount = $temoa->GetPictCount();
		$this->DocPictures = $temoa->GetPictsJson();

		$this->Trad1Html = $temoa->GetHTML($f."_trad.rtf");
		if($this->Trad1Html) {
			$this->Trad1NoteCount = $temoa->GetNoteCount();
			$this->Trad1Notes = $temoa->GetNotesJson();
			$this->Trad1Marks = $temoa->GetMarksJson();
			$this->Trad1Lines = $temoa->GetLinesJson();
		}

		$this->Trad2Html = $temoa->GetHTML($f."_trad2.rtf");
		if($this->Trad2Html) {
			$this->Trad2NoteCount = $temoa->GetNoteCount();
			$this->Trad2Notes = $temoa->GetNotesJson();
			$this->Trad2Marks = $temoa->GetMarksJson();
			$this->Trad2Lines = $temoa->GetLinesJson();
		}
		unset($temoa);

		$this->importRtf($f.'_esp.rtf', 'PresentationEs'); 
		$this->importRtf($f.'_fra.rtf', 'PresentationFr'); 
		$this->importRtf($f.'_ang.rtf', 'PresentationEn'); 
		
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
		$orto = json_decode($args['ortho']);

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
				$a->text = strtolower($a->text);
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
		
		$filt = array();
		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
		$rul = file_get_contents(getcwd().'/'.$rule->FilePath);
		$p = strpos($rul, '[Filters]');
		$p = strpos($rul, "\n", $p);
		$rul = substr($rul, $p+1);
		while(substr($rul, 0, 1) != '[') {
			$p = strpos($rul, "\n");
			$f = trim(substr($rul, 0, $p));
			if($f) {
				$q = strpos($f, '=');
				$filt[] = substr($f, 0, $q);
			}
			$rul = substr($rul, $p+1);
		}
			
		return array('documentsId'=>$docId, 'documents'=>$doc, 'filters'=>$filt);		
	}
	
	static function GetDocument($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		$doc = $t->DocHtml;
		$not = $t->DocNoteCount;
		$mrk = $t->DocMarks;
		$lin = $t->DocLines;
		$pic = $t->DocPictCount;
		$trd = $t->Trad1Html != '';
		$tr2 = $t->Trad2Html != '';
		
//		$c = $t->Code;
//		$a = explode('/', $t->ZipFile);
//		$f = getcwd()."/Home/$a[1]/CEN/Temoa/$c/$c";
//		$trd = file_exists($f."_trad.rtf");
//		$tr2 = file_exists($f."_trad2.rtf");
//		
//		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
//		$temoa = new temoa2\Temoa();
//		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
//		$doc = $temoa->GetHTML($f.".rtf");
//		$not = $temoa->GetNoteCount();
//		$mrk = $temoa->GetMarksJson();
//		$lin = $temoa->GetLinesJson();
//		if(!$temoa->GetPicts($f.'_esp.rtf')) $temoa->GetPicts($f.'_fra.rtf');
//		$pic = $temoa->GetPictCount();
//		unset($temoa);

		$mark = json_decode($mrk, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$line = json_decode($lin, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		
		return array('doc'=>utf8_encode($doc),'marks'=>$mark,'lines'=>$line,'trad'=>$trd,'trad2'=>$tr2,'notes'=>$not,'picts'=>$pic);
	}
	
	static function GetNotes($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		switch($args['num']) {
			case 0: $not .= $t->DocNotes; break;
			case 1: $not .= $t->Trad1Notes; break;
			case 2: $not .= $t->Trad2Notes; break;
		}
		
		
//		$c = $t->Code;
//		$a = explode('/', $t->ZipFile);
//		$f = getcwd()."/Home/$a[1]/CEN/Temoa/$c/$c";
//
//		switch($args['num']) {
//			case 0: $f .= ".rtf"; break;
//			case 1: $f .= "_trad.rtf"; break;
//			case 2: $f .= "_trad2.rtf"; break;
//			case 3: $f .= "_trad3.rtf"; break;
//		}
//		
//		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
//		$temoa = new temoa2\Temoa();
//		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
//		$doc = $temoa->GetHTML($f);
//		$not = $temoa->GetNotesJson();
//		unset($temoa);
	
		$note = json_decode($not, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		foreach($note as &$n) $n->text = utf8_encode($n->text);
		return array('notes'=>$note);
	}
	
	static function GetPicts($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		$pic = $t->DocPictures;
		
		$c = $t->Code;
		$a = explode('/', $t->ZipFile);
		$d = "/Home/$a[1]/CEN/Temoa/$c/";
//		$f = getcwd()."$d$c";
//		
//		$file = $f.'_esp.rtf';
//		if(!file_exists($file)) {
//			$file = $f.'_fra.rtf';
//			if(!file_exists($file)) return array('picts'=>array());
//		}
//
//		$temoa = new temoa2\Temoa();
//		$temoa->GetPicts($file);
//		$pic = $temoa->GetPictsJson();
//		unset($temoa);
		
		$pict = json_decode($pic, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		foreach($pict as $p) {
			$f = $p->folio;
			$p->pos = sprintf("%04d", substr($f, 0, strlen($f)-1)).substr($f, -1, 1);
		}
		
		return array('dir'=>$d, 'picts'=>$pict);
	}
	
	static function GetTraduction($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		switch($args['trad']) {
			case 0:
				$doc = $t->Trad1Html;
				$not = $t->Trad1NotesCount;
				$mrk = $t->Trad1Marks;
				$lin = $t->Trad1Lines;
				break;
			case 1: 
				$doc = $t->Trad2Html;
				$not = $t->Trad2NotesCount;
				$mrk = $t->Trad2Marks;
				$lin = $t->Trad2Lines;
				break;
		}
		
//		$c = $t->Code;
//		$a = explode('/', $t->ZipFile);
//		$f = getcwd()."/Home/$a[1]/CEN/Temoa/$c/$c";
//		
//		switch($args['trad']) {
//			case 0: $f .= "_trad.rtf"; break;
//			case 1: $f .= "_trad2.rtf"; break;
//			case 2: $f .= "_trad3.rtf"; break;
//		}
//		
//		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');
//		$temoa = new temoa2\Temoa();
//		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
//		$doc = $temoa->GetHTML($f);
//		$not = $temoa->GetNoteCount();
//		$mrk = $temoa->GetMarksJson();
//		$lin = $temoa->GetLinesJson();
//		unset($temoa);
		
		$mark = json_decode($mrk, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$line = json_decode($lin, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);

		return array('doc'=>utf8_encode($doc),'notes'=>$not,'marks'=>$mark, 'lines'=>$line);
	}
	

}