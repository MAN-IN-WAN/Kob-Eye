<?php
class ConfigPack extends genericClass {
    function Save() {
        parent::Save();
        //recalcul du total produit
        $prod = $this->getParents('Produit');
        if (is_array($prod))foreach ($prod as $p){
            //calcul du total des configpacks
            $cps = $p->getChildren('ConfigPack');
            $tt = 0;
            if (is_array($cps))foreach ($cps as $cp){
                $tt += $cp->TarifHT;
            }
            //affectation sur la reference
            $ref = $p->getChildren('Reference');
            if (isset($ref[0])&&is_object($ref[0])){
                $ref[0]->Tarif = $tt;
                $ref[0]->Save();
            }
        }
    }
}