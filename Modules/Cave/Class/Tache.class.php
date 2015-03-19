<?php
class Tache extends genericClass {

	function PrintPlanning($oper,$deb,$fin) {
		$req = 'Tache/';
		if($oper) $req .= "OperateurId=$oper&";
		$req .= "Date>=$deb&Date<=$fin";
		$t = Sys::getData('Cave',$req,0,999,'ASC,ASC,DESC','OperateurId,Date,Heure');
		if(! is_array($t) || ! count($t)) return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>'Pas de données à imprimer')), null);

		require_once('PrintPlanning.class.php');

		$pdf = new PrintPlanning($t,$deb,$fin,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle("Planning");
		
		$pdf->AddPage();
		$pdf->PrintLines();
		// save pdf
		$file = 'Home/tmp/Planning.pdf';
		$pdf->Output($file);
		$pdf->Close();
		$res = array(printFiles=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
}
