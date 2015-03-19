<?php

class FactureCommission extends genericClass {

	/**
	 * Enregistrement d'une facture de commission à chaque ligne de commande
	 * -> Check référence
	 * @return	void
	 */
	public function Save() {
		parent::Save();
		$this->pdf_facture_commission( );
	}

	public function Delete() {
		parent::Delete();
	}

	function LePrice($Montant) {
		return number_format($Montant, 2 , '.','') ;

	}
	function pdf_facture_commission( ){
		$Etat =array('','Neuf','Comme neuf','Excellent','Bon','Moyen');
		// on va chercher la facture concernée
		$ligcde= $this->storproc('Boutique/LigneCommande/FactureCommission/'. $this->Id,false,0,1);
		$cde= $this->storproc('Boutique/Commande/LigneCommande/'.$ligcde[0]['Id'],false,0,1);
		$ref= $this->storproc('Boutique/Reference/LigneCommande/'.$ligcde[0]['Id'],false,0,1);
		$cli= $this->storproc('Boutique/Client/LigneCommande/'  . $ligcde[0]['Id'],false,0,1);
		
		//print_r($this);

		$numfact=$this->NumFactComm;
		$DateFact= Utils::getDate(array('d/m/Y',$this->tmsCreate));

		$Client = array($cli[0]['Civilite'] . ' ' . $cli[0]['Prenom'] . ' ' . $cli[0]['Nom'] , $cli[0]['Adresse'], $cli[0]['CodPos']. ' ' .  $cli[0]['Ville']);
		$Desi = array($ref[0]['Nom'],'Tarif  : ' .$this->LePrice($ref[0]['Tarif']),'Etat  : ' . $Etat[$ref[0]['Etat']], str_replace('<br/>',' ', $ref[0]['DescriptionTech']) ,'Description  : ' . $ref[0]['Description']);
		$calculMontant = $this->MontantHt;
		$totalCommHT   = $this->LePrice($calculMontant) ;
		$calculMontant = $this->MontantHt + ($this->MontantHt*($this->TypeTva/100));
		$totalCommTTC  = $this->LePrice($calculMontant)  ;
		$calculMontant = $this->MontantHt*($this->TypeTva/100);
		$totalCommTVA  = $this->LePrice($calculMontant) ;
		$tauxTva       = $this->TypeTva;

	        $this->name = "Games Avenue";
        	$this->description = "Factures Commissions";
        	$this->error = "";

	        // Dimension page pour format A4
        	$this->type = 'pdf';
        	$this->page_largeur = 210;
        	$this->page_hauteur = 297;
        	$this->format = array($this->page_largeur,$this->page_hauteur);
        	$this->marge_gauche=10;
        	$this->marge_droite=10;
        	$this->marge_haute=10;
        	$this->marge_basse=10;
	        $this->option_logo = 1;                    // Affiche logo
	        $this->option_tva = 1;                     // Gere option tva FACTURE_TVAOPTION
     
		// Defini position des colonnes
		$this->posxdesc=$this->marge_gauche+1;
		$this->posxtva=113;
		$this->posxup=126;
		$this->posxqty=145;
		$this->posxdiscount=162;
		$this->postotalht=174;
	
		$this->tva=array();
		require_once('Class/Lib/fpdf/fpdf.php');
                $pdf=new FPDF('P','mm',$this->format);

                $pdf->Open();
                $pagenb=0;
                $pdf->SetDrawColor(128,128,128);

                $pdf->SetTitle("Facture Commission");
                $pdf->SetCreator("Games Avenue ");
                $pdf->SetCompression(false);

                $pdf->SetMargins($this->marge_gauche, $this->marge_haute, $this->marge_droite);   // Left, Top, Right
                $pdf->SetAutoPageBreak(1,0);
                // New page
                $pdf->AddPage();
                $pagenb++;
                $this->_pagehead($pdf, $numfact,$DateFact, $totalCommHT,$totalCommTTC,$Desi,$Client);

                $this->_pagefoot($pdf, $totalCommHT,$totalCommTTC,$totalCommTVA,$tauxTva);

 		//Hauteur Totale du tableau des produits
                $tab_top = 60;
                $tab_height = 180;               
               
                $pdf->SetFillColor(240,240,240);
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFont('Arial','', 7);
                $pdf->SetXY (10, $tab_top + 5 );
           
                $iniY = $pdf->GetY();
                $curY = $pdf->GetY();
                $nexY = $pdf->GetY();
		$dir = 'Home/FactureCommission';
		$file = $dir . "/FactCommissionPdf_" . $this->NumFactComm . ".pdf";
 		$pdf->Output($file);


    }
   
   
    function _pagefoot(&$pdf, $totht, $totttc, $tottva,$tauxTva){

 	define(FAC_PDF_SOCIETE , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCNOM"));
	define(FAC_PDF_ADRESSE1 , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCADRESSE1"));
	define(FAC_PDF_ADRESSE2 , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCADRESSE2"));
	define(FAC_PDF_CODEPOSTAL , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCCODEPOSTAL"));
	define(FAC_PDF_VILLE, $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCVILLE"));
	define(FAC_PDF_CONTACT, $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::CONTACT"));
  	define(FAC_PDF_SIRET , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCSIRET"));
	define(FAC_PDF_APE , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCAPE"));
	define(FAC_PDF_TVAINTRACOM , $GLOBALS["Systeme"]->Conf->get("MODULE::SYSTEME::SOCTVAINTRACOMM"));
	$euro = chr(128);

	// LIGNE DU BAS DE PAGE
  	$pdf->SetXY(5,285);
        $pdf->SetFont('Arial','',7);
        $pdf->MultiCell(0, 8, FAC_PDF_SOCIETE . ' ' .FAC_PDF_ADRESSE1 . ' ' .FAC_PDF_CODEPOSTAL . ' ' . FAC_PDF_VILLE . " - SIRET " . FAC_PDF_SIRET . " - Code APE " . FAC_PDF_APE . " - TVA INTRACOMM " .  FAC_PDF_TVAINTRACOM, '' , 'C');

	// TRAIT HORIZONTAL UN
	$pdf->SetTextColor(0,0,0);
	$pdf->Line(120, 220, 200 , 220);
	// TRAIT HORIZONTAL  DEUX
	$pdf->SetTextColor(0,0,0);
	$pdf->Line(120, 250, 200 , 250);
	// TRAIT VERTICAL UN
	$pdf->SetTextColor(0,0,0);
	$pdf->Line(120, 220, 120 , 250);
	// TRAIT VERTICAL DEUX
	$pdf->SetTextColor(0,0,0);
	$pdf->Line(200, 220, 200 , 250);

	// LIBELLES MONTANTS
	// TOT HT
  	$pdf->SetXY(122,225);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(100, 4, "MONTANT TOTAL HT" , '' , 'L');
	// TOT TVA
  	$pdf->SetXY(122,235);
        $pdf->SetFont('Arial','I',11);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(100, 4, "TVA " . $tauxTva . ' % ', '' , 'L');
	// TOT TTC
  	$pdf->SetXY(122,245);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(255,0,0);
        $pdf->MultiCell(100, 4, "MONTANT TOTAL TTC" , '' , 'L');

	// TOT HT Valeur
  	$pdf->SetXY(180,225);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(20, 4, $totht . ' ' . $euro, '' , 'R');
	// TOT TVA
  	$pdf->SetXY(180,235);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(20, 4, $tottva . ' ' . $euro, '' , 'R');
	// TOT TTC
  	$pdf->SetXY(180,245);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTextColor(255,0,0);
        $pdf->MultiCell(20, 4, $totttc . ' ' . $euro, '' , 'R');



    }

    function _pagehead(&$pdf, $numFact,$DateFact, $totalCommHT,$totalCommTTC,$Desi,$Client){

	$euro = chr(128);
        //*********************LOGO****************************
        $pdf->SetXY(5,5);
        $pdf->Image("Skins/gamesavenue/Images/GA_Logo_Pdf.jpg", 0, 0 , 5, 5, 'jpg');
        //*********************Entete****************************
        $pdf->SetXY(140,10);
        $pdf->SetFont('Arial','B',11);
        $pdf->MultiCell(140, 4, $Client[0], '' , 'L');
        $pdf->SetXY(140,20);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(140, 4, $Client[1], '' , 'L');
        $pdf->SetXY(140,25);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(140, 4, $Client[2], '' , 'L');

	// Trait au dessus du numéro de facture
        $pdf->SetXY(140,30);
	$pdf->Line(140, 30, 200 , 30);

	//Numéro Facture
        $pdf->SetXY(140,35);
        $pdf->SetTextColor(255,0,0);
	$pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(140, 4, "Facture N° : ". $numFact, '' , 'L');

         //Date Facture
        $pdf->SetXY(140,45);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(140, 4, "Date : " . $DateFact , '' , 'L');
  	//Facture réglée
        $pdf->SetXY(140,49);
        $pdf->SetFont('Arial','I',7);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(140, 4, "(Facture réglée le: " . $DateFact .')' , '' , 'L');
	// Trait en dessous de la date
        $pdf->SetXY(140,55);
	$pdf->Line(140, 55, 200 , 55);

	// TABLEAU 3 COLONNES les traits
        // TRAIT HORIZONTAL UN
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(5, 60, 200 , 60);
        // TRAIT HORIZONTAL  DEUX
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(5, 70, 200 , 70);
	// TRAIT HORIZONTAL TROIS
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(5, 210, 200 ,210);
        // TRAIT VERTICAL UN
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(5, 60, 5 , 210);
	// TRAIT VERTICAL DEUX
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(120, 60, 120 , 210);
	// TRAIT VERTICAL TROIS
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(160, 60, 160 , 210);
	// TRAIT VERTICAL QUATRE
        $pdf->SetTextColor(0,0,0);
	$pdf->Line(200, 60, 200 , 210);

	// TABLEAU 3 COLONNES  les entetes
	// DESIGNATION
  	$pdf->SetXY(10,62);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(50, 4, "DESIGNATION" , '' , 'L');

	// commission ht
  	$pdf->SetXY(125,62);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(50, 4, "COMMISSION HT" , '' , 'L');
 	// commission ht
  	$pdf->SetXY(165,62);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(50, 4, "COMMISSION TTC" , '' , 'L');

 	// TABLEAU 3 COLONNES  le détail
	// DÉSIGNATION
	$pdf->SetXY(10,75);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(150, 4, $Desi[0] , '' , 'L');
	$pdf->SetXY(10,80);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(150, 4, $Desi[1] . ' ' . $euro  , '' , 'L');
	$pdf->SetXY(10,85);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(150, 4, $Desi[2] , '' , 'L');
	$pdf->SetXY(10,90);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(165, 4, $Desi[3] , '' , 'L');
	$pdf->SetXY(10,95);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(110,4, $Desi[4] , '' , 'L');
	// commission ht
  	$pdf->SetXY(130,75);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(20,5, $totalCommHT . ' ' . $euro, 0 , 'R',0);
 	// commission tTC
  	$pdf->SetXY(170,75);
        $pdf->SetFont('Arial','B',9);
        $pdf->SetTextColor(0,0,0);
        $pdf->MultiCell(20, 4, $totalCommTTC . ' ' . $euro, 0 , 'R',0);


      
    }
 

    function _tableau_tot(&$pdf, $object, $deja_regle, $posy, $outputlangs){
 
    }

    /**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
    */
   private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
	return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
   }

		//---------------------

}