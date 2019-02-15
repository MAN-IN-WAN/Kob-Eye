<?php
class Enseignant extends genericClass {
	
	function Delete() {
		$rec = $this->getChildren('Classe');
		if(count($rec)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}
		$rec = $this->getChildren('Absence');
		foreach($rec as $r)
			$r->Delete();
		
		return parent::Delete();
	}

	function SendMessage($params) {
		if(!isset($params['step'])) $params['step'] = 0;
		switch($params['step']) {
			case 0:
				return array(
					'step'=>1,
					'template'=>'sendMessage',
					'callNext'=>array(
						'nom'=>'SendMessage',
						'title'=>'Message suite',
						'needConfirm'=>false,
						'args'=>array('identifier'=>'CadrefEnseignant')
					)
				);
				break;
			case 1:
				$ret = Cadref::SendMessage($params['Msg']);
				return array(
					'data'=>'Message envoyé',
					'params'=>$params['Msg'],
					'success'=>true,
					'callNext'=>false
				);
				break;
		}
	}

	function PrintEtiquettes($obj) {
		require_once('Class/Lib/pdfb/fpdf_fpdi/PDF_label.php');

		$sql .= "select Nom, Prenom, Adresse1, Adresse2, CP, Ville from `##_Cadref-Enseignant` order by CP, Ville, Nom, Prenom";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return false;

		$f = array('paper-size'=>'A4',
			'metric'=>'mm',
			'marginLeft'=>0,
			'marginTop'=>8.5,
			'NX'=>2,
			'NY'=>8,
			'SpaceX'=>0,
			'SpaceY'=>0,
			'width'=>105,
			'height'=>37.125,
			'font-size'=>9);
		$pdf = new PDF_label($f);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Etiquettes enseignants');

		$pdf->AddPage();
		foreach($pdo as $l) {
			$s = $l['Nom'].'  '.$l['Prenom']."\n".$l['Adresse1']."\n".$l['Adresse2']."\n".$l['CP']."  ".$l['Ville'];
			$pdf->Add_Label($s);
		}

		$file = 'Home/tmp/EtiquetteEnseignant_'.date('YmdHi').'.pdf';
		$pdf->Output(getcwd() . '/' . $file);
		$pdf->Close();

		return array('pdf'=>$file);
	}
	
}
