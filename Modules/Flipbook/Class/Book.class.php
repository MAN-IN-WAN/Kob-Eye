<?php


define("ALIGN_LEFT", "left");
define("ALIGN_CENTER", "center");
define("ALIGN_RIGHT", "right");
define("VALIGN_TOP", "top");
define("VALIGN_MIDDLE", "middle");
define("VALIGN_BOTTOM", "bottom");

class Book extends genericClass
{
	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	
	function setQuery($string){
		$part = explode('/', $string);
		$query='';
		for($i=0; $i<count($part)-2; $i++){
			if($i == count($part)-3)
				$query .= $part[$i];
			else
				$query .= $part[$i].'/';
		}

		return $query;
	}
	
	function getPage($string){
		$part = explode('/', $string);

		return $part[4];
	}
	
	function saveToPdf($pageString, $tmsEdit, $titre, $HD, $pageTitle){
		require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
		try{
			$pages = explode(';', $pageString);
			$Edits = explode(';', $tmsEdit);
			$regenerate = true;
			if($HD == 'true'){
				if($pageTitle != "")
					$file = 'Home/Flipbook/pdf/flipbook_'.str_replace(' ', '-', $titre).'_HD('.$pageTitle.').pdf';
				else
					$file = 'Home/Flipbook/pdf/flipbook_'.str_replace(' ', '-', $titre).'_HD.pdf';
			}
			else{
				if($pageTitle != "")
					$file = 'Home/Flipbook/pdf/flipbook_'.str_replace(' ', '-', $titre).'('.$pageTitle.').pdf';
				else
					$file = 'Home/Flipbook/pdf/flipbook_'.str_replace(' ', '-', $titre).'.pdf';
			}

			if(file_exists($file)){
				for($i=0; $i<count($Edits)-1; $i++){
					if($Edits[$i]>filemtime($file)){
						$regenerate = true;
						break;
					}
				}
			}
			
			if($regenerate || !file_exists($file)){
				$pdf = new FPDF('P','mm','A4');
				for($i=0; $i<count($pages)-1; $i++){
					$pdf->AddPage();
//					$url = "http://nbcom.hkoval.com/".$pages[$i];
					$url = $pages[$i];
					$pdf->Image($url,0,0, 210, 297);
				}
				$pdf->Close();
				
				$pdf->Output($file, 'F');
				@chmod($file, 0777);
			}
			
			if(file_exists($file) && !$autoPrint){
					if($HD == 'true'){
						if($pageTitle != "")
							$name = 'flipbook_'.str_replace(' ', '-', $titre).'_HD('.$pageTitle.').pdf';
						else
							$name = 'flipbook_'.str_replace(' ', '-', $titre).'_HD.pdf';
					}
					else{
						if($pageTitle != "")
							$name = 'flipbook_'.str_replace(' ', '-', $titre).'('.$pageTitle.').pdf';
						else
							$name = 'flipbook_'.str_replace(' ', '-', $titre).'.pdf';
					}
					
					$header = "Content-Disposition: attachment; "; 
			      	$header .= "filename=$name\n"; 
			     	header($header); 
			      	//Envoyer l'en-tête de type MIME (ici pdf).
			      	header("Content-Type: application/pdf\n"); 
			      	//Envoyer le document. Pas d'encodage magic_quotes.
			      	set_magic_quotes_runtime(0); 
			      	readfile($file);
			}
				
		}
		catch(Exception $e){
			echo 'Exception reçue : ',  $e->getMessage(), "\n";
		}
	}	
}
?>