<?php

class Reglement extends genericClass {
	
	function Save($mode = false) {
		$ret = parent::Save();
		if(! $mode) {
			$adh = $this->getOneParent('Adherent');
			$adh->SaveAnnee(new stdClass(), 2);	
		}
		return $ret;
	}

	function PrintReglement($obj) {
		require_once ('PrintReglement.class.php');

		$annee  = Cadref::$Annee;
		$menus = ['impressionslistereglements','impressionsreglementsdifferes','impressionsdifferesnonencaisses'];
		$mode = array_search($obj['CurrentUrl'], $menus);
		
		$type = $obj['ModeReglement'];
		$ordre = $obj['Ordre'];
		
		$user = $obj['Utilisateur'];
		if($user == 'Tous') $user = '';
		$file = 'Home/tmp/';
		switch($mode) {
			case 0:
				$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateDebut'].' 00:00:00')->getTimestamp(); 
				$dfin = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateFin'].' 23:59:59')->getTimestamp();
				$where = "r.DateReglement>=$ddeb and r.DateReglement<=$dfin and r.Annee='$annee' and r.Encaisse=1";
				$title = 'Règlements '.$user.' '.$obj['DateDebut'].'-'.$obj['DateFin'];
				$file .= 'Reglements_'.$user.'_'.date('Ymd', $ddeb).'_'.date('Ymd', $dfin).'_'.date('YmdHis').'.pdf';
				break;
			case 1:
				$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00')->getTimestamp();
				$dfin = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00'); 
				$dfin->add(DateInterval::createFromDateString('1 month'));
				$dfin->add(DateInterval::createFromDateString('1 second'));
				$dfin = $dfin->getTimestamp();
				$where = "r.DateReglement>=$ddeb and r.DateReglement<$dfin and r.Differe=1";
				$title = 'Différés '.$obj['DateDebut'];
				$file .= 'Differes_'.substr($obj['DateDebut'], 3).substr($obj['DateDebut'], 0, 2).'_'.date('YmdHis').'.pdf';
				break;
			case 2:
				$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00')->getTimestamp(); 
				$where = "r.DateReglement<$ddeb and r.Differe=1 and r.Encaisse=0";
				$title = 'Différés non encaissés '.$obj['DateDebut'];
				$file .= 'Differes_non_encaisses_'.substr($obj['DateDebut'], 3).substr($obj['DateDebut'], 0, 2).'_'.date('YmdHis').'.pdf';
		}

		$sql = "
select r.Utilisateur,r.DateReglement,r.Montant,r.ModeReglement,h.Numero,h.Nom,h.Prenom,r.Differe,r.Encaisse,h.IBAN,h.BIC,h.DateRUM
from `##_Cadref-Reglement` r 
left join `##_Cadref-Adherent` h on h.Id=r.AdherentId
where ".$where;
		$sql .= " and r.Supprime=0 ";	
		if($user != '') $sql .= " and r.Utilisateur='$user' ";
		if($type != 'T') $sql .= " and r.ModeReglement='$type'";
		if($type == 'P' and $obj['SEPA'] == 'N') $sql .= " and r.SEPA=0";
		
		if($type == 'T') $sql .= " order by r.ModeReglement";
		else {
			if($ordre == 'C') $sql .= " order by r.Id,h.Nom, h.Prenom";
			else $sql .= " order by h.Nom, h.Prenom, r.DateReglement";
		}

		
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);

		$pdf = new PrintReglement($mode, $type, $user, $obj['DateDebut'], $obj['DateFin']);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',$title));

		$pdf->AddPage();
		$pdf->PrintLines($pdo);

		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();
		
		return array('pdf'=>$file, 'sql'=>$sql);
	}
	
	private function formate($s, $tab) {
		$i = 1;
		foreach($tab as $t) {
			$s = str_replace("%$i", $t, $s);
			$i++;
		}
		return $s;
	}
	
	private function sepaPrl1($user,$remet,$ddeb,$dfin,$time,$cSeq,$nSeq,$cPrl2,$nonSEPA) {
		$iban = Cadref::GetParametre('BANQUE', 'COMPTE', 'IBAN');
		$bic = Cadref::GetParametre('BANQUE', 'COMPTE', 'BIC');
		$ics = Cadref::GetParametre('BANQUE', 'COMPTE', 'ICS');
		
		$sql = "
select count(*) as cnt,sum(round(r.Montant,2)) as tot
from `##_Cadref-Reglement` r
inner join `##_Cadref-Adherent` a on a.Id=r.AdherentId
where DateReglement>=$ddeb and DateReglement<$dfin and ModeReglement='P' and Montant>0 and Encaisse=0 and a.EtatRUM=$nSeq
";
		if($user != 'Tous') $sql .= " and r.Utilisateur='$user'";
		if($nonSEPA) $sql .= " and r.SEPA=0";


		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$nbre		= $p['cnt'];
			$total		= $p['tot'];
		}
		if($nbre == 0) return '';

		$iban = Cadref::GetParametre('BANQUE', 'COMPTE', 'IBAN')->Valeur;
		$bic = Cadref::GetParametre('BANQUE', 'COMPTE', 'BIC')->Valeur;
		$ics = Cadref::GetParametre('BANQUE', 'COMPTE', 'ICS')->Valeur;
		$nume = Cadref::GetParametre('BANQUE', 'COMPTE', 'PRELEVEMENT')->Valeur;
		$nume++;
		Cadref::SetParametre('BANQUE', 'COMPTE', 'PRELEVEMENT', $nume);

		$tmp = date("Y-m-d");
		$Sepa = $this->formate($cPrl2,[$nume.'00'.$nSeq,$nbre,$total,$cSeq,$tmp,$remet, str_replace(" ","",$iban),$bic,$ics]);

		return $Sepa;
	}

	private function sepaPrl2($user,$ddeb,$dfin,$cSeq,$nSeq,$cPrl3,$nonSEPA) {
		$Sepa = '';
		$sql = "
select a.Numero,r.Montant,a.IBAN,a.BIC,a.DateRUM,a.Nom,a.Prenom,r.DateReglement,r.Id
from `##_Cadref-Reglement` r
inner join `##_Cadref-Adherent` a on a.Id=r.AdherentId
where DateReglement>=$ddeb and DateReglement<$dfin and ModeReglement='P' and Montant>0 and Encaisse=0 and a.EtatRUM=$nSeq
";
		if($user != 'Tous') $sql .= " and r.Utilisateur='$user'";
		if($nonSEPA) $sql .= " and r.SEPA=0";
		$sql .= " order by a.Nom,a.Prenom";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$id = $p['Id'];
			$nume = $p['Numero'];
			$mont = round($p['Montant'],0);
			$iban = strtoupper(str_replace(' ','',$p['IBAN']));
			$bic = strtoupper($p['BIC']);
			$drum = strtoupper($p['DateRUM']);
			$nom = strtoupper($p['Nom']);
			$pren = strtoupper($p['Prenom']);
			$dreg = $p['DateReglement'];
			$tmp = date('YmdHis',$dreg).'/'.$nume;
			$tmp2 = date('Y-m-d', $drum);
			$Sepa .= $this->formate($cPrl3,[$tmp,$mont,$nume.'-'.$tmp2,$tmp2,$bic,substr($nom.' '.$pren,0,35),$iban,$nume]);
			$sql = "update `##_Cadref-Reglement` set SEPA=1 where Id=$id";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$GLOBALS['Systeme']->Db[0]->exec($sql);
		}
		return $Sepa;
	}

	function Prelevements($obj) {
		$user = $obj['Utilisateur'];
		$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00')->getTimestamp();
		$dfin = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00'); 
		$dfin->add(DateInterval::createFromDateString('1 month'));
		$dfin->add(DateInterval::createFromDateString('1 second'));
		$dfin = $dfin->getTimestamp();
		$nonSEPA = $obj['SEPA'] == 'N';
		
		$cPrl0 = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<Document xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"urn:iso:std:iso:20022:tech:xsd:pain.008.001.02\">
	<CstmrDrctDbtInitn>
		<GrpHdr>
			<MsgId>%1</MsgId>
			<CreDtTm>%2</CreDtTm>
			<NbOfTxs>%3</NbOfTxs>
			<CtrlSum>%4</CtrlSum>
			<InitgPty>
				<Nm>%5</Nm>
			</InitgPty>
		</GrpHdr>
";
		$cPrl1 = "
	</CstmrDrctDbtInitn>
</Document>
";
		$cPrl2 = "
		<PmtInf>
			<PmtInfId>%1</PmtInfId>
			<PmtMtd>DD</PmtMtd>
			<NbOfTxs>%2</NbOfTxs>
			<CtrlSum>%3</CtrlSum>
			<PmtTpInf>
				<SvcLvl>
					<Cd>SEPA</Cd>
				</SvcLvl>
				<LclInstrm>
					<Cd>CORE</Cd>
				</LclInstrm>
				<SeqTp>%4</SeqTp>
			</PmtTpInf>
			<ReqdColltnDt>%5</ReqdColltnDt>
			<Cdtr>
				<Nm>%6</Nm>
			</Cdtr>
			<CdtrAcct>
				<Id>
					<IBAN>%7</IBAN>
				</Id>
			</CdtrAcct>
			<CdtrAgt>
				<FinInstnId>
					<BIC>%8</BIC>
				</FinInstnId>
			</CdtrAgt>
			<ChrgBr>SLEV</ChrgBr>
			<CdtrSchmeId>
				<Id>
					<PrvtId>
						<Othr>
							<Id>%9</Id>
							<SchmeNm>
								<Prtry>SEPA</Prtry>
							</SchmeNm>
						</Othr>
					</PrvtId>
				</Id>
			</CdtrSchmeId>
";
		$cPrl3 = "
			<DrctDbtTxInf>
				<PmtId>
					<InstrId>%1</InstrId>
					<EndToEndId>REGLEMENT</EndToEndId>
				</PmtId>
				<InstdAmt Ccy=\"EUR\">%2</InstdAmt>
				<DrctDbtTx>
					<MndtRltdInf>
						<MndtId>%3</MndtId>
						<DtOfSgntr>%4</DtOfSgntr>
					</MndtRltdInf>
				</DrctDbtTx>
				<DbtrAgt>
					<FinInstnId>
						<BIC>%5</BIC>
					</FinInstnId>
				</DbtrAgt>
				<Dbtr>
					<Nm>%6</Nm>
				</Dbtr>
				<DbtrAcct>
					<Id>
						<IBAN>%7</IBAN>
					</Id>
				</DbtrAcct>
				<RmtInf>
					<Ustrd>%8</Ustrd>
				</RmtInf>
			</DrctDbtTxInf>
";
		$cPrl4 = "
		</PmtInf>
";


		// nombre et total general
		$sql = "
select count(*) as cnt,sum(round(Montant,2)) as tot
from `##_Cadref-Reglement`
where DateReglement>=$ddeb and DateReglement<$dfin and ModeReglement='P' and Montant>0 and Encaisse=0
";
		if($user != 'Tous') $sql .= " and Utilisateur='$user'";
		if($nonSEPA) $sql .= " and SEPA=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$nbre = $p['cnt'];
			$total = $p['tot'];
		}

		$time = time();
		$remet = Cadref::GetParametre('BANQUE', 'COMPTE', 'REMETTANT')->Valeur;
		$tmp = substr(date("d/m/Y H:i:s", $time).$remet,0,35);
		$tmp1 = date("Y-m-j\Th:i:s", $time);
		$Sepa = $this->formate($cPrl0,[$tmp,$tmp1,$nbre,$total,$remet]);

		$nSeq = 0;
		$cSeq = "FRST";
		$tmp = $this->sepaPrl1($user,$remet,$ddeb,$dfin,$time,$cSeq,$nSeq,$cPrl2,$nonSEPA);
		if(!empty($tmp)) {
			$Sepa .= $tmp;
			$Sepa .= $this->sepaPrl2($user,$ddeb,$dfin,$cSeq,$nSeq,$cPrl3,$nonSEPA);
			$Sepa .= $cPrl4;
		}
		$nSeq = 1;
		$cSeq = "RCUR";
		$tmp = $this->sepaPrl1($user,$remet,$ddeb,$dfin,$time,$cSeq,$nSeq,$cPrl2,$nonSEPA);
		if(!empty($tmp)) {
			$Sepa .= $tmp;
			$Sepa .= $this->sepaPrl2($user,$ddeb,$dfin,$cSeq,$nSeq,$cPrl3,$nonSEPA);
			$Sepa .= $cPrl4;
		}

		$Sepa .= $cPrl1;

		// fichier sepa
		$file	= "/Home/tmp/PRLV_".date('YmdHis',$time).".prlv";
		file_put_contents(getcwd().$file, $Sepa);
		
		return array('file'=>$file, 'total'=>$total, 'count'=>$nbre);
	}

	function Encaissements($obj) {
		$user = $obj['Utilisateur'];
		$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00')->getTimestamp();
		$dfin = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00'); 
		$dfin->add(DateInterval::createFromDateString('1 month'));
		$dfin->add(DateInterval::createFromDateString('1 second'));
		$dfin = $dfin->getTimestamp();
		$sql = "
select a.id as adhId, r.Id as regId
from `##_Cadref-Reglement` r
inner join `##_Cadref-Adherent` a on a.Id=r.AdherentId
where DateReglement>=$ddeb and DateReglement<$dfin and ModeReglement='P' and Montant>0 and Encaisse=0 and SEPA=1
";
		if($user != 'Tous') $sql .= " and r.Utilisateur='$user'";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tot = 0;
		$nbr = 0;
		foreach($pdo as $p) {
			$reg = Sys::getOneData('Cadref', 'Reglement/'.$p['regId']);
			$reg->Encaisse = 1;
			$reg->SEPA = 1;
			$reg->Save();
			$adh = Sys::getOneData('Cadref', 'Adherent/'.$p['adhId']);
			$adh->EtatRUM = 1;
			$adh->Save();
			$nbr++;
			$tot += $reg->Montant;
		}
		return array('total'=>$tot, 'count'=>$nbr, 'sql'=>$sql);
	}
	
}
