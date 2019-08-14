<?php
$temoa = new temoa2\Temoa();
$s = "/home/paul/wks/Temoa/Temoa_nahuatl_XVI.rul";
$temoa->SetRules($s);
$temoa->SetCorpus("/home/paul/wks/Temoa/3CHIMAL.RTF;");
$temoa->AddArrow("calli");
$temoa->Search();
echo "\ntargets".$temoa->TargetCount()."\n";
echo $temoa->GetTargetText(2)."\n";
echo $temoa->GetTargetText(5)."\n";
echo "-------------------------\n";
echo $temoa->GetTargetsJson()."\n";