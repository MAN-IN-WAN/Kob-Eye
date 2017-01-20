<?php
    $m = genericClass::createInstance('Systeme','Menu');
    $ms = $m->getMainMenus();
    for ($i=0; $i<sizeof($ms);$i++){
        if ($i>3)
            $modulo = $i%3;
        else $modulo = $i;
        switch ($modulo){
            case 1:
                $ms[$i]->Couleur = 'orange';
                break;
            case 2:
                $ms[$i]->Couleur = 'vert';
                break;
            default:
                $ms[$i]->Couleur = 'bleu';
                break;
        }
        //TODO Faire la requete des publicite Systeme/Menu/m.Id/Donnee/Type=Pub
        
    }
    $vars['MainMenus'] = $ms;
?>