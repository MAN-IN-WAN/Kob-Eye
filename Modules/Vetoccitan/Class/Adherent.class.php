<?php
class Adherent extends genericClass
{
    public function Save(){
        $cli = $this->getOneParent('Client');
        $grp = Sys::getOneData('Systeme','Group/Nom=VetoAdherent');
        if($cli && $grp){
//            $usr = Sys::getOneData('Systeme','User/Client/'.$cli->Id);
//            if($usr) {
//                $usr->addParent($grp);
//                $usr->Save();
//            }

            if ($grpDef = Sys::getOneData('Systeme','Group/*/Group/Nom='.strtoupper(Utils::KEAddSlashes($cli->Nom)))){
                $grpDef->resetParents('Group');
                $grpDef->addParent($grp);
                $grpDef->Save();
            }
        }


        return parent::Save();
    }

    public function Recup_InfosVeto ( $objet,$search ) {
/** OBJET : ADHÉRENT
 * SEARCH : INFOS À RÉCUPÉRER : NEWS, SERVICES, CONSEILS, PUB....
 */
        // recherche Activités de l'adhérent
        if ($_GET["C"]) {
            $SearchsAdherent = Sys::getData('Vetoccitan', "Categorie/".$_GET["C"]."/".$search."/Display=1");
            // RESTE À EXCLURE LES INFOS QUI SONT POUR D'AUTRES ADHÉRENTS !!

        } else {
            $ActivitesAdherent = Sys::getData('Vetoccitan', "Activite/Adherent/" . $objet->Id);

            // Recupération des Infos search
            $CategsAdherent = array();
            $SearchsAdherent = array();

            // recherche Activités de l'adhérent
            $ActivitesAdherent = $objet->getParents("Activite");

            // recherche Catégories pour chaque Activités de l'adhérent
            foreach ($ActivitesAdherent as $ACP) {
                $CategsActivite = $ACP->getChildren("Categorie");
                foreach ($CategsActivite as $Cats) {
                    foreach ($CategsAdherent as $Cads) {
                        if ($Cats->Id == $Cads->Id) {
                            continue 2;
                        }
                    }
                    $CategsAdherent[] = $Cats;
                }
            }

            // Recherche des Infos search  liées aux catégories
            foreach ($CategsAdherent as $CATAD) {
                $SearchsCat = $CATAD->getChildren($search."/Display=1");
                foreach ($SearchsCat as $CatCons) {
                    foreach ($SearchsAdherent as $Cads) {
                        if ($CatCons->Id == $Cads->Id) {
                            continue 2;
                        }
                    }
                    $SearchsAdherent[] = $CatCons;
                }
            }


            // recherche les Infos search liés directement à l'adhérent
            $ConsAdherentDirect = $objet->getChildren($search."/Display=1");
            // ajout aux autres services
            foreach ($ConsAdherentDirect as $Cons) {
                foreach ($SearchsAdherent as $Cads) {
                    if ($Cons->Id==$Cads->Id) {continue 2;}
                }
                $SearchsAdherent[]= $Cons;
            }

            $ConsToutAdherent = array();

            foreach ($SearchsAdherent as $Cons) {
                $ConsParent = Sys::getData('Vetoccitan', "Adherent/".$search."/" . $Cons->Id);
                foreach ($ConsParent as $Cads) {
                    $exclu = 0 ;
                    if ($Cads->Id != $objet->Id) {
                        $exclu = 1 ;
                    } else {
                        $ConsToutAdherent[]=$Cons;
                    }
                }
                if ($exclu == 0 ) {
                    $ConsToutAdherent[]=$Cons;
                }
            }

            $SearchsAdherent = $ConsToutAdherent ;


        }

        // Recherche les Médias de nos Infos search
        foreach ($SearchsAdherent as &$CONS) {
            $ConsImg = $CONS->getOneChild("Media");
            $CONS->Media =$ConsImg;
        }
        // on récupère l'adhérent et on renvoie un array avec les valeurs
        return($SearchsAdherent);

    }
}
