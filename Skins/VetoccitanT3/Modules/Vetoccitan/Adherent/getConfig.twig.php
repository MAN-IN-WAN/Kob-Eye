<?php
// Récupération des valeurs du minisite
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=" . Sys::$domain);

function formatEquipe($Equipier){
    // Recuperation tous les postes pour tri d'affichage
    $allPoste = Sys::getData("Vetoccitan","Poste","","","","","","",true);
    $tabOrdrePost =array();
    foreach ($allPoste as $p){
        $tabOrdrePost[] = array('Nom'=>$p->Nom,'Ordre'=>$p->Ordre);
    }
    usort($tabOrdrePost,function($a,$b){
        if ($a['Ordre'] > $b['Ordre']) return 1;
        if ($a['Ordre'] < $b['Ordre']) return -1;
        if ($a['Ordre'] == $b['Ordre']){
            if ($a['Nom'] > $b['Nom']) return 1;
            if ($a['Nom'] < $b['Nom']) return -1;
        }
        return 0;
    });

    //        $lAdherent = $LeClient->getOneChild("Adherent");
    $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
//Fichier Poste
//Fichier Personnel

    $post = array();

    foreach($Equipier as $items){
        if (isset($post[$items->Poste])){
            $post[$items->Poste][] = $items;
        }else{
            $post[$items->Poste] = array($items);
        }
    }
    $tabOrdrePost = array_filter($tabOrdrePost,function($a)use($post){
        if (in_array($a['Nom'],array_keys($post))){
            return true;
        }
        return false;
    });


    return array(
        "Poste" => $post,
        "OrdrePoste" => $tabOrdrePost,
    );

}

// Récupération de l'adherent
switch ($_GET['confType']) {
    case 'homeSlider':
        $LeClient = Sys::getOneData("Parc", "Client/MiniSite/" . $Minisite->Id);

//        $lAdherent = $LeClient->getOneChild("Adherent");
        $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
        $lAdherent->Module = "Vetoccitan";

// Recupération des publicités
        $CategsAdherent = array();
        $PubsAdherent = array();

// recherche Activités de l'adhérent
        $ActivitesAdherent = Sys::getData('Vetoccitan', "Activite/Adherent/" . $lAdherent->Id);


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
        $objet = 'Bandeau';
        if ($_GET['POSITION'] == 'bandoHaut') {
            $objet = 'Bandeau';
        } elseif ($_GET['POSITION'] == 'bandoBAS') {
            $objet = 'Publicite';
        }
// Recherche des Publicités liées aux catégories
        foreach ($CategsAdherent as $CATAD) {
            $PubsCategs = $CATAD->getChildren($objet . "/(!(!DateDebut<=" . time() . "&&DateFin>=" . time() . "!)++(!DateFin=0!)!)&&Publier=1");
            foreach ($PubsCategs as $Pubs) {
                foreach ($PubsAdherent as $Pads) {
                    if ($Pubs->Id == $Pads->Id) {
                        continue 2;
                    }
                }
                $PubsAdherent[] = $Pubs;
            }


        }
// recherche les Publicité liés directement à l'adhérent
        $PubsAdherentDirect = $lAdherent->getChildren($objet . "/(!(!DateDebut<=" . time() . "&&DateFin>=" . time() . "!)++(!DateFin=0!)!)&&Publier=1");
// ajout aux autres publicités

        foreach ($PubsAdherentDirect as $Pubs) {
            foreach ($PubsAdherent as $Pads) {
                if ($Pubs->Id == $Pads->Id) {
                    continue 2;
                }
            }
            $PubsAdherent[] = $Pubs;
        }

// Recherche les Médias de nos publicités
        foreach ($PubsAdherent as &$PUBS) {
            $PubsImg = $PUBS->getOneChild("Media");
            $PUBS->Media = $PubsImg;
        }


        shuffle($PubsAdherent);

        $vars['Publicites'] = $PubsAdherent;

        echo json_encode(array('data' => $vars['Publicites']));

        break;
    case 'homeBloc':
        $LeClient = Sys::getOneData("Parc", "Client/MiniSite/" . $Minisite->Id);

//        $lAdherent = $LeClient->getOneChild("Adherent");
        $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
        $lAdherent->Module = "Vetoccitan";

        echo json_encode(array('data' => $lAdherent));
        break;
    case 'homeHoraires':
        $LeClient = Sys::getOneData("Parc", "Client/MiniSite/" . $Minisite->Id);

//        $lAdherent = $LeClient->getOneChild("Adherent");
        $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
        $lAdherent->Module = "Vetoccitan";
        $horaires = $lAdherent->getOneChild("Horaire");

        $params = $Minisite->getParamsValues();
        $texte = "";
        $params->detailHoraires = false;
        if (!$params->detailHoraires) {
            $hs = array(
                array(
                    "jour" => "Lundi",
                    "horaire" => $horaires->Lundi
                ),
                array(
                    "jour" => "Mardi",
                    "horaire" => $horaires->Mardi
                ),
                array(
                    "jour" => "Mercredi",
                    "horaire" => $horaires->Mercredi
                ),
                array(
                    "jour" => "Jeudi",
                    "horaire" => $horaires->Jeudi
                ),
                array(
                    "jour" => "Vendredi",
                    "horaire" => $horaires->Vendredi
                ),
                array(
                    "jour" => "Samedi",
                    "horaire" => $horaires->Samedi
                ),
                array(
                    "jour" => "Dimanche",
                    "horaire" => $horaires->Dimanche
                )
            );
            $days = array();
            $horaires = null;
            // console.log(hs);
            foreach ($hs as $key => $d) {
                if ($horaires == null) {
                    $days[] = $d["jour"];
                    $horaires = $d["horaire"];
                } else if ($horaires == $d["horaire"]) {
                    $days[] = $d["jour"];
                } else if ($horaires != $d["horaire"] && !empty($d["horaire"])) {
                    if (count($days) > 1) {
                        $texte .= "Du " . $days[0] . " au " . end($days) . " : " . $horaires . "<br>";
                    } else {
                        $texte .= "Le  " . $days[0] . " : " . $horaires . "<br>";
                    }
                    $days = [$d["jour"]];

                    $horaires = $d["horaire"];
                }
                if ($key == count($hs) - 1) {
                    if (count($days) > 1) {
                        $texte .= "Du " . $days[0] . " au " . end($days) . " : " . $horaires . "<br>";
                    } else {
                        $texte .= "Le  " . $days[0] . " : " . $horaires . "<br>";
                    }
                }
                // console.log(d,days,horaires);
            }
        } else {
            $texte .= !empty($horaires->Lundi)?"Le  Lundi : " . $horaires->Lundi . "<br>":"";
            $texte .= !empty($horaires->Mardi)?"Le  Mardi : " . $horaires->Mardi . "<br>":"";
            $texte .= !empty($horaires->Mercredi)?"Le  Mercredi : " . $horaires->Mercredi . "<br>":"";
            $texte .= !empty($horaires->Jeudi)?"Le  Jeudi : " . $horaires->Jeudi . "<br>":"";
            $texte .= !empty($horaires->Vendredi)?"Le  Vendredi : " . $horaires->Vendredi . "<br>":"";
            $texte .= !empty($horaires->Samedi)?"Le  Samedi : " . $horaires->Samedi . "<br>":"";
            $texte .= !empty($horaires->Dimanche)?"Le  Dimanche : " . $horaires->Dimanche. "<br>":"";
        }

        echo json_encode(array('data' => array(
            'horaires' => $texte,
            'adresse' =>  $lAdherent->Nom ."<br> " .$lAdherent->Adresse . " " . $lAdherent->CodePostal ." " . $lAdherent->Ville
        )));
        break;
    case 'homeEquipe':
        $LeClient = Sys::getOneData("Parc", "Client/MiniSite/" . $Minisite->Id);

//        $lAdherent = $LeClient->getOneChild("Adherent");
        $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
        $lAdherent->Module = "Vetoccitan";

        $perso = $lAdherent->getChildren("Personnel/EnAvant=1");

        $equipTri = formatEquipe($perso);

        echo json_encode(
            array('data' => $equipTri)
        );

        break;
    case 'fullEquipe':

        $LeClient = Sys::getOneData("Parc", "Client/MiniSite/" . $Minisite->Id);

//        $lAdherent = $LeClient->getOneChild("Adherent");
        $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
        $lAdherent->Module = "Vetoccitan";

        $perso = $lAdherent->getChildren("Personnel");

        $equipTri = formatEquipe($perso);

        echo json_encode(
            array('data' => $equipTri)
        );
        break;
    case 'clinique':

        $params = $Minisite->getPagesParamsValues();

//        var_dump($params);
        $res = array();
        foreach ($params as $item){
            if ($item["page"]->MenuUrl == 'clinique' ){
                foreach ($item["params"] as $p){
                    $res[$p->Nom] = $p->vms;
                }
                break;
            }
        }
        $tabRes = array(
            "Description" => $res["Description_Clinique"],
            "Titre_image" => $res["Titre_image_Clinique"],
            "Image" => $res["Image_Clinique"]
        );

        echo json_encode(
            array('data' => $tabRes)
        );

        break;
    case 'Nsc':
        $demande = $_GET["Choix"];
        $c = $_GET["C"];
        $lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
//        $LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);
//        $lAdherent = $LeClient->getOneChild("Adherent");
        $lAdherent->Module="Vetoccitan";


        if ($demande == "services"){
            $donnees = $lAdherent->Recup_Service($lAdherent);
        }elseif ($demande == "conseils"){
            $donnees = $lAdherent->Recup_InfosVeto($lAdherent,"Conseil");
        }else{
            $donnees = $lAdherent->Recup_InfosVeto($lAdherent,"News");
        }

        array_walk($donnees,function(&$a){
            $cats = $a->getParents("Categorie");
            $a->cats = array();
            foreach ($cats as $cat) {
                $a->cats[] = $cat->Id;
            }
        });



        echo json_encode(
            array('data' => $donnees)
        );

        break;
    default :
        echo '{"data":"none"}';
}
