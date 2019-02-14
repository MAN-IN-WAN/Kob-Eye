<?php
$stores = array();
$children = array();
foreach (Sys::$Modules as $k=>$mod){
    foreach ($mod->getObjectClass() as $ap){
        $tmp['identifier'] = $mod->Nom . $ap->titre;
        $tmp['module'] = $mod->Nom;
        $tmp['objecttype'] = $ap->titre;
        $o = genericClass::createInstance($mod->Nom, $ap->titre);
        $obj = $o->getObjectClass();
        $formfields = $o->getElementsByAttribute('form','',true);
        $out = array();
        if (is_array($formfields))foreach ($formfields as $f){
            if (isset( $o->{$f['name']})) {
				// PGF 20181115 type cast
				$v = $o->{$f['name']};
				switch($f['type']) {
					case 'int':
					case 'bigint':
					case 'boolean':
						$v = (int)$v; break;
					case 'float':
						$v = (double)$v; break;
				}
                $out[$f['name']] = $v;
			}
        }
        $tmp['newData'] = json_encode($out);
        $tmp['logEvent'] = $obj->logEvent;
        $tmp['childrenelements'] = array();
        foreach ($obj->getChildElements() as $sub){
            if ($sub['objectModule'] . $sub['objectName']!=$tmp['identifier']) {
                $tmpchildren = $sub;
                $tmpchildren['identifier'] = $sub['objectModule'] . $sub['objectName'];
                $tmp['childrenelements'][] = $tmpchildren;
            }else{
                $tmp['recursivelement'] = $sub;
                $tmp['recursivelement']['identifier'] = $sub['objectModule'] . $sub['objectName'];
            }
        }

        $flt =$o->getCustomFilters();
        $tmp['baseFilter'] = false;
        if(is_array($flt) && sizeof($flt) && $flt[0]->filter != '')
            $tmp['baseFilter'] = $flt[0]->filter;

        $stores[$tmp['identifier'].'Store'] = $tmp;
    }
}
$vars['stores'] = $stores;
?>