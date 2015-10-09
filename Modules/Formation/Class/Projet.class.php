<?php
class Projet extends genericClass {
    /**
     * Synchro
     * Syncrhonisation de la session avec le central pour aggrégation des données
     */
    public function getJson() {
        //préparation des données
        $data = '{
            "success": false,
            "date": '.$this->tmsEdit.',
            "nom" : "'.$this->Nom.'",
            "categories" : [';
        $categories = Sys::getData('Formation','Projet/'.$this->Id.'/Categorie/*',0,1000,'ASC','Id');
        $virgule = '';
        foreach ($categories as $c){
            $parent = Sys::getOneData('Formation','Categorie/Categorie/'.$c->Id);
            $parentid = (is_object($parent))?$parent->Id: -1;
            $data .= $virgule.'{
                    "id": '.$c->Id.',
                    "parent": "'.$parentid.'",
                    "nom": "'.$c->Nom.'",
                    "ordre": "'.$c->Ordre.'",
                    "prefixe": "'.$c->Prefixe.'",
                    "afficher": '.$c->Afficher.',
                    "bloque": "'.$c->Bloque.'",
                    "position": "'.$c->Position.'",
                    "posx": "'.$c->PosX.'",
                    "posy": "'.$c->PosY.'",
                    "etapes": [';
            $etapes = $c->getChildren('Etape');
            $virgule2 = '';
            foreach ($etapes as $e) {
                $data .= $virgule2 . '{
                    "id": "' . $e->Id . '",
                    "numero": "' . $e->Numero . '",
                    "titre": "' . $e->Titre . '",
                    "debloquage": "' . $e->Debloquage . '"
                 }';
                $virgule2 = ',';
            }
            $data .= '],';

            $data .= '"questions": [';
            $questions = $c->getChildren('Question');
            $virgule2 = '';
            foreach ($questions as $q){
                $data .= $virgule2.'{
                    "id": "'.$q->Id.'",
                    "prefixe": "'.$q->Prefixe.'",
                    "ordre": "'.$q->Ordre.'",
                    "nom":"'.str_replace('"', '\"',$q->Nom).'",
                    "typequestions": [';
                $typequestions = $q->getChildren('TypeQuestion');
                $virgule3 = '';
                foreach ($typequestions as $tq){
                    $data .= $virgule3.'{
                            "id": "'.$tq->Id.'",
                            "typereponse": "'.$tq->TypeReponse.'",
                            "ordre": "'.$tq->Ordre.'",
                            "afficheoui": "'.$tq->AfficheOui.'",
                            "affichenon": "'.$tq->AfficheNon.'",
                            "nom":"'.str_replace('"', '\"',$tq->Nom).'",
                            "typequestionvaleurs": [';
                    $typequestionvaleurs = $tq->getChildren('TypeQuestionValeur');
                    $virgule4 = '';
                    foreach ($typequestionvaleurs as $tqv){
                        $data .= $virgule4.'{
                                            "id": "'.$tqv->Id.'",
                                            "ordre": "'.$tqv->Ordre.'",
                                            "valeur":"'.str_replace('"', '\"',$tqv->Valeur).'"
                                        }';
                        $virgule4 = ',';
                    }
                    $data.= ']}';
                    $virgule3 = ',';
                }
                $data.= ']}';
                $virgule2 = ',';
            }
            $virgule = ',';
            $data.=']}';
        }
        $data .='
            ],
            "maps" : [';

        $maps = $this->getChildren('Map');
        $virgule = '';
        foreach ($maps as $m) {
            $data .= $virgule . '{
                    "id": ' . $m->Id . ',
                    "nom": "' . $m->Nom . '",
                    "fichier": "' . $m->Fichier . '",
                    "largeur": "' . $m->Largeur . '",
                    "hauteur": "' . $m->Hauteur . '",
                    "categories": [';
            $cs = $m->getChildren('Categorie');
            $virgule2 = '';
            foreach ($cs as $c){
                $data .=$virgule2.'"'.$c->Id.'"';
                $virgule2 = ',';
            }
            $data .='
                    ]
            }';
            $virgule = ',';
        }
        $data .='],
        "fichiers" : [';

        $fichiers = $this->getChildren('Fichier');
        $virgule = '';
        foreach ($fichiers as $f) {
            $data .= $virgule . '{
                "id": ' . $f->Id . ',
                "nom": "' . $f->Nom . '",
                "fichier": "' . $f->Fichier . '",
                "type": "' . $f->Type . '"
            }';
            $virgule = ',';
        }
        $data .='
            ],
        "regions" : [';

        $regions = Sys::getData('Formation','Region');
        $virgule = '';
        foreach ($regions as $r) {
            $data .= $virgule . '{
                "id": ' . $r->Id . ',
                "nom": "' . $r->Nom . '"
            }';
            $virgule = ',';
        }
        $data .='
            ]
        }';

        return $data;
    }

    /**
     * Reception
     * Syncrhonisation des sessions depuis les boitiers
     *
     */
    public function synchro() {
        //sending informations
        $data = '{
            "id": '.$this->Id.',
             "date": '.$this->tmsEdit.'
        }';
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $data,
                'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create( $options );

        $result = file_get_contents( 'http://erdf.e-p.consulting/Formation/Projet/'.$this->Id.'/getJson.htm', false, $context );
        $response = json_decode( $result );
        if ($response->success){
            return "OK à jour";
        }
        $json = $response;
        try {
            $this->Nom = $json->nom;

            //nettoyage du projet
            $cats = $this ->getChildren('Categorie/*');
            foreach ($cats as $c) $c->Delete();
            $maps = $this ->getChildren('Map');
            foreach ($maps as $c) $c->Delete();
            $fichier = $this ->getChildren('Fichier');
            foreach ($fichier as $c) $c->Delete();

            //on force linsertion avec id
            Sys::$FORCE_INSERT = true;

            //ajout des categories
            foreach ($json->categories as $c) {
                $ca = genericClass::createInstance('Formation','Categorie');
                if ($c->parent=="-1")
                    $ca->addParent($this);
                else $ca->addParent('Formation/Categorie/'.$c->parent);
                $ca->Id = $c->id;
                $ca->Nom = $c->nom;
                $ca->Ordre = $c->ordre;
                $ca->Prefixe = $c->prefixe;
                $ca->Afficher = $c->afficher;
                $ca->Bloque = $c->bloque;
                $ca->Position = $c->position;
                $ca->PosX = $c->posx;
                $ca->PosY = $c->posy;
                $ca->Save();
                foreach ($c->etapes as $e) {
                    $et = genericClass::createInstance('Formation','Etape');
                    $et->AddParent($ca);
                    $et->Id = $e->id;
                    $et->Numero = $e->numero;
                    $et->Titre = $e->titre;
                    $et->Debloquage = $e->debloquage;
                    $et->Save();
                }
                foreach ($c->questions as $q) {
                    $qu = genericClass::createInstance('Formation','Question');
                    $qu->AddParent($ca);
                    $qu->Id = $q->id;
                    $qu->Prefixe = $q->prefixe;
                    $qu->Ordre = $q->ordre;
                    $qu->Nom = $q->nom;
                    $qu->Save();
                    foreach ($q->typequestions as $tq) {
                        $tqu = genericClass::createInstance('Formation','TypeQuestion');
                        $tqu->AddParent($qu);
                        $tqu->Id = $tq->id;
                        $tqu->TypeReponse = $tq->typereponse;
                        $tqu->Ordre = $tq->ordre;
                        $tqu->Nom = $tq->nom;
                        $tqu->AfficheOui = $tq->afficheoui;
                        $tqu->AfficheNon = $tq->affichenon;
                        $tqu->Save();
                        foreach ($tq->typequestionvaleurs as $tqv) {
                            $tquv = genericClass::createInstance('Formation','TypeQuestionValeur');
                            $tquv->AddParent($tqu);
                            $tquv->Id = $tqv->id;
                            $tquv->Valeur = $tqv->valeur;
                            $tquv->Ordre = $tqv->ordre;
                            $tquv->Save();
                        }
                    }
                }
            }

            Sys::$FORCE_INSERT = false;
            Sys::$Modules['Formation']->Check();
            Sys::$FORCE_INSERT = true;

            foreach ($json->maps as $m) {
                $ma = genericClass::createInstance('Formation','Map');
                $ma->AddParent($this);
                $ma->Id = $m->id;
                $ma->Nom = $m->nom;
                $ma->Fichier = $m->fichier;
                if (!file_exists($m->fichier))
                    file_put_contents($m->fichier, file_get_contents("http://erdf.e-p.consulting/".$m->fichier));
                $ma->Largeur = $m->largeur;
                $ma->Hauteur = $m->hauteur;
                $ma->Save();

                Sys::$FORCE_INSERT = false;
                foreach ($m->categories as $cs) {
                    $cat = Sys::getOneData('Formation','Categorie/'.$cs);
                    if (is_object($cat)){
                        $cat->addParent($ma);
                        $cat->Save();
                    }/*else{
                        echo "ERROR!!!! $cs";
                        $cats = Sys::getData('Formation','Projet/2/Categorie/*');
                        print_r($cats);
                    }*/
                }
                Sys::$FORCE_INSERT = true;
            }

            foreach ($json->fichiers as $f) {
                $fi = genericClass::createInstance('Formation','Fichier');
                $fi->AddParent($this);
                $fi->Id = $f->id;
                $fi->Nom = $f->nom;
                $fi->Type = $f->type;
                $fi->Fichier = $f->fichier;
                if (!file_exists($f->fichier))
                    file_put_contents($f->fichier, file_get_contents("http://erdf.e-p.consulting/".$f->fichier));
                $fi->Save();
            }
            foreach ($json->regions as $r) {
                $re=Sys::getOneData('Formation','Region/'.$r->id);
                if (!is_object($re)){
                    Sys::$FORCE_INSERT = true;
                    $re = genericClass::createInstance('Formation','Region');
                    $re->Id = $r->id;
                    $re->Nom = $r->nom;
                    $re->Save();
                }else {
                    Sys::$FORCE_INSERT = false;
                    $re->Nom = $r->nom;
                    $re->Save();
                }
            }

            Sys::$FORCE_INSERT = false;

            //forcer la mise à jour

            $this->Save();
            //ajout du commentaire de synchro
            /*$h = genericClass::createInstance('Formation','SynchroHisto');
            $h->Description = date('d/m/Y H:i:s').' Synchro session '.$this->Nom.' OK from '.$_SERVER['REMOTE_ADDR'];
            $h->addParent($boitier);
            $h->Save();*/
            return true;
        } catch (Exception $e) {
            print_r($e);
            return false;
        }
    }

    /**
     * checkUpdate
     * Verification des mises à jour
     */
    public function checkUpdate () {
        $json = file_get_contents('php://input');
        $json = json_decode ($json);
        /*if ($json->date>=$this->tmsEdit) return false;
        else return true;*/
        //force update
        return true;
    }
}