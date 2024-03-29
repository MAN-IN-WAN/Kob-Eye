<?php

class Temoa extends genericClass {

	// importation d'un dictionnaire
	function Save() {
		$fok = $this->ZipFile != '';
		$id = $this->Id;
		
		if( FALSE && $id) {
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
		if($this->ZipFile) {
			$tmp = explode('/', $this->ZipFile);
			$dir = "/Home/".$tmp[1]."/CEN/Temoa/".$this->Code;
			$f = getcwd()."$dir/$this->Code";
			$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');

			$temoa = new temoa2\Temoa();
			$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
			
			//klog::l($f);

			$this->DocHtml = ($temoa->GetHTML($f.".rtf"));
			$this->DocNoteCount = $temoa->GetNoteCount();
			$this->DocNotes = ($temoa->GetNotesJson());
			$this->DocMarks = $temoa->GetMarksJson();
			$this->DocLines = $temoa->GetLinesJson();

			if(!$temoa->GetPicts($f.'_esp.rtf')) $temoa->GetPicts($f.'_fra.rtf');
			$this->DocPictCount = $temoa->GetPictCount();
			$this->DocPictures = $temoa->GetPictsJson();

			$this->Trad1Html = ($temoa->GetHTML($f."_trad.rtf"));
			if($this->Trad1Html) {
				$this->Trad1NoteCount = addslashes($temoa->GetNoteCount());
				$this->Trad1Notes = $temoa->GetNotesJson();
				$this->Trad1Marks = $temoa->GetMarksJson();
				$this->Trad1Lines = $temoa->GetLinesJson();
			}

			$this->Trad2Html = ($temoa->GetHTML($f."_trad2.rtf"));
			if($this->Trad2Html) {
				$this->Trad2NoteCount = ($temoa->GetNoteCount());
				$this->Trad2Notes = $temoa->GetNotesJson();
				$this->Trad2Marks = $temoa->GetMarksJson();
				$this->Trad2Lines = $temoa->GetLinesJson();
			}
			unset($temoa);

			$this->importRtf($f.'_esp.rtf', 'PresentationEs'); 
			$this->importRtf($f.'_fra.rtf', 'PresentationFr'); 
			$this->importRtf($f.'_ang.rtf', 'PresentationEn'); 
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
	
	// liste des mots dans le documents
	static function GetTargets($args) {
		CEN::searchLog($args);

		$corpus = $args['corpus'];
		if($corpus == 'all') $corpus = '';
		else $corpus = "and Id in ($corpus)";
		$ortho = $args['ortho'];
		
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
		$temoa->SetOrtho($ortho);


		$genor = false;
		if($args['arrows']) $temoa->AddArrows($args['arrows']);
		else {
			$temoa->AddArrow($args['word']);
			$o = json_decode($ortho);
			if($o->spelling == '3') {
				$genor = true;
				$arrs = $temoa->GetGenorJson($o->genor);
			}
		}

		
		if($temoa->Search()) {
			$s = $temoa->GetTargetsJson();
		}
		unset($temoa);

		$o = json_decode($s, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$words = count($o);
		$occur = 0;
		$docs = array();
		if($o) {
			$r = '';
			$t = null;
			foreach($o as $a) {
				if($r !==  $a->doc) {
					$r = $a->doc;
					$t = Sys::getOneData('CEN', 'Temoa/Code='.$r);
				}
				$a->text = preg_replace_callback("/&#([0-9]+);/u", function($m) {
					return iconv('cp1250', 'utf-8', chr($m[1]));
				}, $a->text);
				//$a->text = html_entity_decode($a->text, ENT_NOQUOTES | ENT_HTML401, UTF-8);
				$a->id = $t->Id;
				$docs[$t->Id] = '';
				$a->title = $t->Nom;
				$occur += $a->count;
				$a->pict = $t->DocPictCount > 0;
				$a->trad = !empty($t->Trad1Html);
				$a->trad2 = !empty($t->Trad2Html);
			}
		}
		$ret = array('temoa'=>$o,'words'=>$words,'occur'=>$occur,'docs'=>count($docs));	
		if($genor) $ret['arrows'] = json_decode($arrs, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);;
		return $ret;			
	}

	// liste des documents //et des filtres
	static function GetDocs() {
		$docs = Sys::getData('CEN', 'Temoa', 0, 999, 'ASC', 'Nom');
		$docId= array();
		$doc = array();
		foreach($docs as $d) {
			$id = $d->Id;
			$info = ($d->Trad1Html != '' ? 't' : '').($d->Trad2Html != '' ? 't' : '').($d->DocPictCount ? 'i' : '');
			$doc[] = array('id'=>$d->Id, 'title'=>$d->Nom, 'selected'=>true, 'info'=>$info);
			$docId[$d->Id] = $d->Nom;
		}
//klog::l("GetDocs",array('documentsId'=>$docId, 'documents'=>$doc,));
		return array('documentsId'=>$docId, 'documents'=>$doc); // 'filters'=>$filt);		
	}
	
	
	// charge un document
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
		
		$mark = json_decode($mrk, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$line = json_decode($lin, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		
		return array('doc'=>utf8_encode($doc),'marks'=>$mark,'lines'=>$line,'trad'=>$trd,'trad2'=>$tr2,'notes'=>$not,'picts'=>$pic);
	}
	
	
	// liste des notes
	static function GetNotes($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		switch($args['num']) {
			case 0: $not .= $t->DocNotes; break;
			case 1: $not .= $t->Trad1Notes; break;
			case 2: $not .= $t->Trad2Notes; break;
		}
		
		$note = json_decode($not, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		foreach($note as &$n) $n->text = utf8_encode($n->text);
		return array('notes'=>$note);
	}
	
	
	// liste des images
	static function GetPicts($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		$pic = $t->DocPictures;
		
		$c = $t->Code;
		$a = explode('/', $t->ZipFile);
		$d = "/Home/$a[1]/CEN/Temoa/$c/";
		
		$pict = json_decode($pic, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		foreach($pict as $p) {
			$f = $p->folio;
			$p->pos = sprintf("%04d", substr($f, 0, strlen($f)-1)).substr($f, -1, 1);
		}
		
		return array('dir'=>$d, 'picts'=>$pict);
	}
	
	// charge une traduction
	static function GetTraduction($args) {
		$id = $args['id'];
		$t = Sys::getOneData('CEN', "Temoa/$id");
		
		switch($args['trad']) {
			case 0:
				$doc = $t->Trad1Html;
				$not = $t->Trad1NoteCount;
				$mrk = $t->Trad1Marks;
				$lin = $t->Trad1Lines;
				break;
			case 1: 
				$doc = $t->Trad2Html;
				$not = $t->Trad2NoteCount;
				$mrk = $t->Trad2Marks;
				$lin = $t->Trad2Lines;
				break;
		}
		
		$mark = json_decode($mrk, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		$line = json_decode($lin, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);

		return array('doc'=>utf8_encode($doc),'notes'=>$not,'marks'=>$mark, 'lines'=>$line);
	}
	
	// liste des mots générés Genor
	static function GetGenor($args) {
		$rule = Sys::getOneData('CEN', 'Regle/Code=Temoa');

		$temoa = new temoa2\Temoa();
		$ret = $temoa->SetRules(getcwd().'/'.$rule->FilePath);
		$temoa->AddArrows($args['arrows']);
		$g = $temoa->GetGenorJson($args['level']);
		unset($temoa);
		
		$gen = json_decode($g, false, 512, JSON_INVALID_UTF8_SUBSTITUTE);
		return array('arrows'=>$gen);
	} 
	

}
