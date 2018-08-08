<?php
class Cuve extends genericClass {
	
	
	function Save() {
		$id = $this->Id;
		$this->Occupation = floor($this->Volume/$this->Capacite*100);
		if($this->Etat == STC_VIDE || $this->Etat == STC_RINCE || $this->Etat == STC_DETARTRE || $this->Etat == STC_DEROUGI) $this->Vide = 1;
		if($this->Vide) $this->resetParents('Lot');
		parent::Save();
		$res = array('Occupation'=>$this->Occupation);
		$sts[] = array($id ? 'edit' : 'add', 1, $this->Id, 'Cave', 'Cuve', '', '', null, array('dataValues'=>$res));
		return $sts;
	}
	
	function createHourlyAlerts($time) {
		$t0 = $time - 86400;
		$t1 = $t0 + 60 * 60;
		$cvs = Sys::getData('Cave',"Cuve:NOVIEW/EtatCuveId=12&tmsEdit>=$t0&tmsEdit<$t1");
		foreach($cvs as $c) {
			AlertUser::addAlert('Cuve sale depuis 24 heures : '.$c->Cuve,'CU'.$c->Id,'Cave','Cuve',$c->Id,null,'CAVE','');
		}
		$t0 = $time - 7 * 86400;
		$t1 = $t0 + 60 * 60;
		$cvs = Sys::getData('Cave',"Cuve:NOVIEW/(!EtatCuveId=10+EtaCudeId>12!)&tmsEdit>=$t0&tmsEdit<$t1");
		foreach($cvs as $c) {
			AlertUser::addAlert('Cuve vide depuis 7 jours : '.$c->Cuve,'CU'.$c->Id,'Cave','Cuve',$c->Id,null,'CAVE','');
		}
		return null;
	}

	function ImageCave($dat) {
		$items = $this->imageCuves($dat);
		$c = count($items);
		return WebService::WSData('',0,$c,$c,'','','','','',$items);
	}
	
	function PrintImageCave($dat) {
		require_once('PrintImageCave.class.php');

		$pdf = new PrintImageCave($dat,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle("ImageCave ".date('ymd',$dat));
		
		$pdf->AddPage();
		$pdf->PrintLines($this->imageCuves($dat));
		$pdf->PrintTotal();
		// save pdf
		$file = 'Home/tmp/ImageCave'.date('ymd',$dat).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		$res = array(printFiles=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}

	private function imageCuves($dat) {
		$items = array();
		$cvs = Sys::getData('Cave', 'Cuve:NOVIEW/EnService=1');
		foreach($cvs as $c) {
			$r = $this->imageCuve($c->Id,$dat);
			$item = array('Cuve'=>$c->Cuve,'Capacite'=>$c->Capacite,'Volume'=>$r[0],'Date'=>$r[1],'VolumeOperation'=>$r[5], 'Degre'=>$r[6]);
			if($r[2]) {
				$t = Sys::getData('Cave', 'Type/'.$r[2]);
				$item['Type'] = $t[0]->Type;
			}
			if($r[3]) {
				$t = Sys::getData('Cave', 'SousType/'.$r[3]);
				$item['SousType'] = $t[0]->SousType;
			}
			if($r[4]) {
				$t = Sys::getData('Cave', 'Lot/Id='.$r[4]);
				$item['Lot'] = $t[0]->Lot;
				$item['Categorie'] = $t[0]->Categorie;
				$item['Couleur'] = $t[0]->Couleur;
			}
			$items[] = $item;
		}
		return $items;
	}
	
	private function imageCuve($id, $dat) {
		$dinv = 0;
		$did = 0;
		$vol = 0;
		$lot = 0;
		$dop = 0;
		$inv = Sys::getData('Cave', "Operation/OperationCuveId=$id&Date<=$dat&(!SousTypeId=51+TypeId=4!)", 0, 1, 'DESC,DESC', 'Date,Id');
		if(count($inv)) {
			$inv = $inv[0];
			$did = $inv->Id;
			$dinv = $inv->Date;
			$vol = $inv->VolumeReel;
			$dop = $inv->Date;
			$vop = $inv->VolumeReel;
			$lot = $inv->OperationLotId;
			$typ = $inv->TypeId;
			$sty = $inv->SousTypeId;
			$deg = $inv->Degre;
		}
		$rec = Sys::getData('Cave', 'SousType/Transfert=1',0,0,'','','Id');
		$styp = array();
		foreach($rec as $r) $styp[] = $r->Id;
		$ops = Sys::getData('Cave', "Operation/OperationCuveId=$id&Id>$did&Date<=$dat&Date>=$dinv&SousTypeId!=51", 0, 9999, 'ASC,ASC', 'Date,Id');
		foreach($ops as $op) {
			$oid = $op->Id;
			$dop = $op->Date;
			$vop = $op->VolumeReel;
			$lot = $op->OperationLotId;
			$typ = $op->TypeId;
			$sty = $op->SousTypeId;
			$deg = $op->Degre;
			if(in_array($sty, $styp)) {
				if($sty == 1 || $sty == 21 || $sty == 52) $vol += $op->VolumeReel;
				else $vol -= $op->VolumeReel;
			}
		}
		return array($vol, $dop, $typ, $sty, $lot, $vop, $deg, $dinv);
	}

	function PrintStock() {
		require_once('PrintStock.class.php');
		
		$dat = time();
		$pdf = new PrintStock($dat,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle("Stock ".date('ymd',$dat));
		
		$pdf->AddPage();
		$pdf->PrintLines(Sys::getData('Cave', 'Cuve/EtatCuveId=11&Volume>0&CuveLotId>0', 0, 999, 'ASC,ASC,ASC', 'Couleur4.Id,Categorie3.Categorie,Cuve'));
		$pdf->PrintTotal();
		// save pdf
		$file = 'Home/tmp/Stock'.date('ymd',$dat).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		$res = array(printFiles=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	

}
