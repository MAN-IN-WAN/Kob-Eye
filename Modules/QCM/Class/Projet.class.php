<?php
class Projet extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}

	function Delete() {
		$res = $this->getChildren('Page');
		foreach($res as $r) $r->Delete();
		return parent::Delete();
	}
	
	function GetProjet() {
		$rec = Sys::$Modules['QCM']->callData("Projet/Id=".(!$this->Id ? '0' : $this->Id),false,0,1);
		$c = count($rec);
		return WebService::WSData('',0,$c,$c,'','','','','',$rec);
	}

	function SendProjet($add) {
		$url = '<a href="'.QCMURL.$this->Url.'">';
		$url .= $title.'</a>';
		$bl = new Bloc();
		$bl->setFromVar('Mail', $url, 'BLOC');
		$bl->init();
		$pr = new Process();
		$bl->generate($pr);
		
		$m = new PHPMailer();
		$m->SetFrom('noreply@unibio.fr','');
		$m->AddAddress($add, 'Enquête interne');
		$m->Subject = 'Enquête interne';
		$m->IsHTML(true);
		$m->Body = $bl->Affich();
		$res = $m->Send();
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, null);
	}

	function PrintProjet($rep, $id=0) {
		require_once('PrintProjet.class.php');

		if($id) {
			$par = Sys::getData('QCM','Participation/Id='.$id);
			$par = $par[0];
			$prj = $par->getParents('Projet');
			$prj = $prj[0];
		}
		else $prj = $this;
		$pdf = new PrintProjet($prj,$par,$rep,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle($this->Nom.' '.date('ymd',$dat));
		$pdf->PrintPages();
		// save pdf
		$file = 'Home/tmp/projet'.date('ymd',$dat).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		$res = array(printFiles=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	function PrintRapport($rep, $id=0) {
		require_once('PrintRapport.class.php');

		$pdf = new PrintRapport($this,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle($this->Nom.' '.date('ymd',$dat));
		$pdf->PrintPages();
		// save pdf
		$file = 'Home/tmp/rapport'.date('ymd',$dat).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		$res = array(printFiles=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	function Dupliquer($id) {
		if(! $id) return WebService::WSStatus('method',0,0,'QCM','Projet','','',array(array('message'=>"Sélectionner un projet à dupliquer.")),null);
	
		$p = genericClass::createInstance('QCM', 'Projet');
		$p->initFromId($id);
		$pgs = $p->getChildren('Page');
		unset($p->Id);
		$p->Nom .= ' (Dupliqué)';
		$p->Date = time();
		$p->Archive = 0;
		$p->Save();
		foreach($pgs as $pg) {
			$qs = $pg->getChildren('Question');
			unset($pg->Id);
			$pg->addParent($p);
			$pg->Save();
			foreach($qs as $q) {
				$rs = $q->getChildren('Reponse');
				unset($q->Id);
				$q->addParent($pg);
				$q->Save();
				foreach($rs as $r) {
					unset($r->Id);
					$r->addParent($q);
					$r->Save();
				}
			}
		}
		return WebService::WSStatus('add',1,$p->Id,'QCM','Projet','','',null,null);
	}
	
	function ApercuProjet() {
		$url = QCMURL.$this->Url.'?Test=1';
		$res = array(navigateToURL=>$url);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	function DateLimite($dat, $val) {
		$lim = null;
		if($dat && $val) $lim = strtotime("+$val day", $dat);
		$data = array('DateLimite'=>$lim);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
	function Reponses() {
		$rs = $this->getChildren('Participation');
		$t = count($rs);
		$n = 0;
		foreach($rs as $r) if($r->Valide) $n++;
		$res = array('reponses'=>"$n / $t");
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, array('dataValues'=>$res));
	}
}