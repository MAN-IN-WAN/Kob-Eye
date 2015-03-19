<?php
// ce fichier s'appelle par exemple EAN13.php
// dans votre programme il suffit d'appeler l'image comme suit:
// <img src="EAN13.php?numero=3149025043092&dimension=5">
// et c'est tout
// $form_numero='0123456789012'; //le code EAN13 à 13 chiffres
// $form_dimension=4.5; // multiplicateur de la taille de l'image initiale (120 pixel x 70 pixel)
// // ce qui donne avec la valeur de 4.5 une image en 300 dpi de 4.57 cm x 2.57 cm (540 pixel x 315 pixel)
// import_request_variables("gP", "form_");
/* ***** BEGIN LICENSE BLOCK *****
* Version: MPL 1.1
*
* * The contents of this file are subject to the Mozilla Public License Version
* * 1.1 (the "License"); you may not use this file except in compliance with
* * the License. You may obtain a copy of the License at
* * http://www.mozilla.org/MPL/
* *
* * Software distributed under the License is distributed on an "AS IS" basis,
* * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
* * for the specific language governing rights and limitations under the
* * License.
* *
* * The Original Code is : Debora, un générateur de codes barre.
* *
* * The Initial Developer of the Original Code is
* * Olivier Meunier.
* * Portions created by the Initial Developer are Copyright (C) 2003
* * the Initial Developer. All Rights Reserved.
* *
* * Contributor(s):
* * Rémi Chéno (ajout des séparateurs gauche, centre et droite)
* *
* * ***** END LICENSE BLOCK ***** */

 class debora{
	/**
	* Déclaration des propriétés
	*/
	var $form_dimension=1;

	var $arryGroup = array('A' => array(
	0 => "0001101", 1 => "0011001",
	2 => "0010011", 3 => "0111101",
	4 => "0100011", 5 => "0110001",
	6 => "0101111", 7 => "0111011",
	8 => "0110111", 9 => "0001011"
	),
	'B' => array(
	0 => "0100111", 1 => "0110011",
	2 => "0011011", 3 => "0100001",
	4 => "0011101", 5 => "0111001",
	6 => "0000101", 7 => "0010001",
	8 => "0001001", 9 => "0010111"
	),
	'C' => array(
	0 => "1110010", 1 => "1100110",
	2 => "1101100", 3 => "1000010",
	4 => "1011100", 5 => "1001110",
	6 => "1010000", 7 => "1000100",
	8 => "1001000", 9 => "1110100"
	)
	);
	
	var $arryFamily = array(
	0 => array('A','A','A','A','A','A'),
	1 => array('A','A','B','A','B','B'),
	2 => array('A','A','B','B','A','B'),
	3 => array('A','A','B','B','B','A'),
	4 => array('A','B','A','A','B','B'),
	5 => array('A','B','B','A','A','B'),
	6 => array('A','B','B','B','A','A'),
	7 => array('A','B','A','B','A','B'),
	8 => array('A','B','A','B','B','A'),
	9 => array('A','B','B','A','B','A')
	);
	
	/**
	* Constructeur
	*
	* Initialise la classe
	*
	* @EAN13 string code EAN13
	*
	* return void
	*/
	function debora($EAN13)
	{
	settype($EAN13,'string');
	
	//Transformation de la chaine EAN en tableau
	for($i=0;$i<13;$i++)
	{
	$this->EAN13[$i] = substr($EAN13,$i,1);
	}
	
	$this->strCode = $this->makeCode();
	}
	
	
	/**
	* Création du code binaire
	*
	* Crée une chaine contenant des 0 ou des 1 pour indiquer les espace blancs ou noir
	*
	* return string Chaine résultante
	*/
	function makeCode()
	{
	//On récupère la classe de codage de la partie qauche
	$arryLeftClass = $this->arryFamily[$this->EAN13[0]];
	
	//Premier séparateur (101)
	$strCode = '101';
	
	//Codage partie gauche
	for ($i=1; $i<7; $i++)
	{
	$strCode .= $this->arryGroup[$arryLeftClass[$i-1]][$this->EAN13[$i]];
	}
	
	//Séparateur central (01010)
	$strCode .= '01010';
	
	//Codage partie droite (tous de classe C)
	for ($i=7; $i<13; $i++)
	{
	$strCode .= $this->arryGroup['C'][$this->EAN13[$i]];
	}
	
	//Dernier séparateur (101)
	$strCode .= '101';
	
	return $strCode;
	}
	
	
	/**
	* Création de l'image
	*
	* Crée une image GIF ou PNG du code généré par giveCode
	*
	* return void
	*/
	function makeImage($imageType="png",$Chemin=""){
		$form_dimension = $this->form_dimension;
		//Initialisation de l'image
		//$img=imagecreate(120, 70);
		
		$width=120;
		$height=70;
		$img=imagecreate($width, $height);
		
		$color[0] = ImageColorAllocate($img, 255,255,255);
		$color[1] = ImageColorAllocate($img, 0,0,0);
		
		$coords[0] = 15;
		$coords[1] = 10;
		$coords[2] = 1;
		$coords[3] = 40;
		
		imagefilledrectangle($img, 0, 0, 95, 80, $color[0]);
		
		for($i=0;$i<strlen($this->strCode);$i++)
		{
		$posX = $coords[0];
		$posY = $coords[1];
		$intL = $coords[2];
		$intH = $coords[3];
		
		$fill_color = substr($this->strCode,$i,1);
		
		# Allongement des 3 bandes latérales et centrales
		# sur une idée de Rémi Chéno
		if ($i < 3 || ($i >= 46 && $i < 49) || $i >= 92) {
		$intH = $intH + 8;
		}
		
		imagefilledrectangle($img, $posX, $posY, $posX, ($posY+$intH), $color[$fill_color]);
		
		//Deplacement du pointeur
		$coords[0] = $coords[0] + $coords[2];
		}
		
		# Affichage du code (Rémi Chéno)
		imagestring($img, 3, 5, 50, $this->EAN13[0], $color[1]);
		imagestring($img, 3, 19, 50, implode('', array_slice($this->EAN13,1, 6)), $color[1]);
		imagestring($img, 3, 65, 50, implode('', array_slice($this->EAN13,7)), $color[1]);
		
		// Calcul des nouvelles dimensions
		$newwidth = $width* $form_dimension;
		$newheight = $height * $form_dimension;
		
		// Chargement
		$thumb = imagecreatetruecolor($newwidth, $newheight);
		
		// Redimensionnement
		imagecopyresized($thumb, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		
		//Header( "Content-type: image/".$imageType);
		
		$func_name = 'image'.$imageType;
		
		$func_name($thumb,$Chemin);
		imagedestroy($img);
		imagedestroy($thumb);
	}
	

 }//Fin de la classe
//  $ean13 = new debora($form_numero);
//  $ean13-> makeImage();

?>