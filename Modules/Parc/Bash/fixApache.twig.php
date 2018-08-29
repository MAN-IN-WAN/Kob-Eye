<?php
$aps = Sys::getData('Parc','Apache',0,100000);
foreach ($aps as $ap) {
    $domains = explode(" ",$ap->getDomains());
    $radical = '';
    foreach ($domains as $domain) {
        $domain = trim($domain);
        if (empty($domain)) continue;
        //echo "----->".$domain."\n";
        if (preg_match('#.*\.(.*?)\.(.*)#',$domain,$out)){
            if (!in_array($out[1],array('azko','secibonline')))
            $radical = $out[1].'.'.$out[2];
        }
    }
    if (!empty($radical)){
        echo "->".$radical."\n";
        //on ajoute en serverAlias
        $ap->ApacheServerAlias.="\n".$radical;
        $ap->Save();
    }
}