<?php
class NewsletterContact extends genericClass {
	
	function explodeCSV( $content ) {
		return explode(PHP_EOL, $content);
	}

	function sendHeader() {
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=\"export.csv\"");
	}

  	function addTitre($ctc) {
   		echo $ctc . ";"  . "\r\n";
		echo "Groupe;Nom;Prenom;Email;Campagne\r\n" ;
  	}

  	function addContact($ctc, $Groupe) {
    		echo strtoupper(utf8_decode($Groupe)) . ";" . strtoupper(utf8_decode($ctc->Nom)) . ";" . ucwords(utf8_decode($ctc->Prenom)) . ";" . strtolower(utf8_decode($ctc->Email))  . ";" . strtolower(utf8_decode($ctc->Campagne)) . "\r\n";
  	}
 	function addRappel($ctc) {
    		echo "\r\n";
    		echo "Nombre de contacts" . ";" . $ctc . "\r\n";
  	}
	
	function addTotal($ctc, $Groupe='') {
    		echo "\r\n";
			if ($Groupe=='') $Groupe = "de tous les groupes : ";
    		echo "Total contacts " . ";" . $Groupe . ";" . $ctc . "\r\n";
   			echo "\r\n";
   	}

	function rc() {
		echo "\r\n";
	}
	
	
	
}
?>
