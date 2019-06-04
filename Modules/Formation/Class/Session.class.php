<?php
class FormationSession extends genericClass {

    /**
     * Save
     */
    function Save(){
        parent::Save();
        //Si pas d'étape alors on génère
        if (!Sys::getCount('Formation','Session/'.$this->Id.'/Etape')){
            $this->initCatBloque();
        }
        //Si pas dde donnees alors on génère
        if (!Sys::getCount('Formation','Session/'.$this->Id.'/Donnee')){
            $this->initDonnee();
        }
        //on check également l'intégrité des réponses par équipe
        $this->checkReponse();
    }
    /**
     * checkReponse
     * Relie les réponse avec les typeQuestions correspondants
     */
    function checkReponse() {
        $dirty=false;;

        //récupération des données
        $donn = Sys::getData('Formation','Session/'.$this->Id.'/Donnee',0,1000,'ASC','Numero');
        if (!sizeof($donn)){
            $this->initDonnee();
            $donn = Sys::getData('Formation','Session/'.$this->Id.'/Donnee',0,1000,'ASC','Numero');
        }

        //pour chaque equipe on vérifie les questions
        $eq = $this->getChildren('Equipe');
        foreach ($eq as $e){
            $reps = Sys::getData('Formation','Equipe/'.$e->Id.'/Reponse',0,1000,'ASC','Id');
            for ($i=0,$si=sizeof($reps); $i<$si; $i++) {
                //on vérifie qu'il y a bien un typequestion par réponse
                if (!Sys::getCount('Formation', 'TypeQuestion/Reponse/' . $reps[$i]->Id)) {
                    //récupration du type question correspondant
                    $tq = $donn[$i]->getParents('TypeQuestion');
                    $tq = $tq[0];
                    $reps[$i]->addParent($tq);
                    $reps[$i]->Save();
                    $dirty = true;
                }
            }
        }

        //si il y avait un probleme alors on resynchronise
        if ($dirty){
            $this->Synchro = false;
            genericClass::Save();
        }

    }
    /**
     * initDonnee
     */
    function initDonnee() {
        $p = $this->getProjet();
        $num=1;
        $questions = Sys::getData('Formation','Projet/'.$p->Id.'/Categorie/*/Question',0,200,'ASC','Ordre');
        foreach ($questions as $q){
            $tps = $q->getChildren('TypeQuestion');
            foreach ($tps as $o) {
                $e = genericClass::createInstance('Formation', 'Donnee');
                $e->addParent($this);
                $e->addParent($o);
                $e->Numero = $num;
                $e->Titre = $q->Nom;
                $e->TypeReponse = $o->TypeReponse;
                $num++;
                $e->Save();
            }
        }
    }
    /**
     * getCatBloque
     */
    function initCatBloque() {
        $p = $this->getProjet();
        $out = $this->recursivCat($p);
        $num=1;
        //on générère les Etapes
        foreach ($out as $o) {
            $e = genericClass::createInstance('Formation','Etape');
            $e->addParent($this);
            $e->addParent($o);
            $e->Numero = $num;
            $e->Titre = $o->Nom;
            $num++;
            $e->Save();
        }
    }
    /**
     * recursiveCat
     * recherche recursivement dans les categories
     */
    function recursivCat($p) {
        if (!is_object($p)) return array();
        $cats = $p->getChildren('Categorie');
        $out = array();
        foreach ($cats as $c){
            if ($c->Bloque){
                array_push($out, $c);
            }else{
                $out = array_merge($out, $this->recursivCat($c));
            }
        }
        return $out;
    }
    /**
     * @return bool|void
     */
    function Delete() {
        // suppression de toutes les réponses
        $t = $this->getChildren('Equipe');
        foreach ($t as $r) {
            $r->Delete();
        }
        $t = $this->getChildren('Etape');
        foreach ($t as $r) {
            $r->Delete();
        }
        $t = $this->getChildren('Donnee');
        foreach ($t as $r) {
            $r->Delete();
        }
        parent::Delete();
    }
    /**
     * Demarre
     * Démarre une session
     * Vérifie qu'aucune autre session est en cours, sinon termine l'autre session
     *
     */
    function Demarre() {
        //Recherche d'une session en cours.
        $sess = Sys::getData('Formation','Session/EnCours=1');
        foreach ($sess as $s){
            //on termine les sessions
            $s->Termine();
        }
        //démarre cette session
        $this->EnCours = 1;
        $this->Termine = 0;
        $this->Synchro = 0;
        $this->Date = time();
        $this->Save();
    }
    /**
     * Termine
     * Termine une session
     *
     */
    function Termine() {
        //termine cette session
        $this->EnCours = 0;
        $this->Termine = 1;
        $this->TermineLe = time();
        $this->Save();

        $GLOBALS["Systeme"]->Db[0]->query('COMMIT');

        //backup
        Formation::Backup();
    }
    /**
     * setTeam
     * Ajoute une équipe à la session
     */
    function setTeam($num) {
        if (!(int) $num>0) return false;
        //Verification de l'existence de l'equipe dans la base de donnée
        $t = Sys::getOneData('Formation', 'Session/'.$this->Id.'/Equipe/Numero='.$num);
        //si existe => erreur
        if (is_object($t)&&$t->Description!=$_SERVER['REMOTE_ADDR']) return false;
        elseif (is_object($t)&&$t->Description==$_SERVER['REMOTE_ADDR']) return true;
        //si existe pas on ajoute
        else {
            $t = genericClass::createInstance('Formation','Equipe');
            $t->addParent($this);
            $t->Numero = $num;
            $t->Description = $_SERVER['REMOTE_ADDR'];
            $t->Save();
            return true;
        }
    }
    /**
     * getCurrentQuestion
     * Retourne la question courante pour une session
     */
    function getCurrentQuestion ($num) {
        $next = null;
        //Verification de l'existence de l'equipe dans la base de donnée
        $t = Sys::getOneData('Formation', 'Session/'.$this->Id.'/Equipe/Numero='.$num);
        //calcul de la dernière réponse
        $r = Sys::getOneData('Formation', 'Equipe/'.$t->Id.'/Reponse',0,1,'DESC','Id');
        if (is_object($r)) {
            //récupération du type question
            $tq = Sys::getOneData('Formation', 'TypeQuestion/Reponse/' . $r->Id);
            //récupération de la question
            $q = Sys::getOneData('Formation', 'Question/TypeQuestion/' . $tq->Id);
            if(!empty($q->Parametres)){
                $params = json_decode($q->Parametres,true);
                if(!empty($params['goto'])){
                    if(is_array($params['goto']) && !empty($params['goto'][$r->Valeur] )){
                        $next = $params['goto'][$r->Valeur];
                    } elseif ( !is_array( $params['goto'] )){
                        $next = $params['goto'];
                    }
                }
            }
            if($next) return $next;
            return $q->Ordre + 1;
        }else return 1;

    }
    /**
     * checkEtape
     * vérification du débloquage de l'étape
     *
     */
    function checkEtape($equipe,$session,$question){
        //recherche de la categorie bloquante de la question
        $q = Sys::getOneData('Formation','Question/'.$question);
        $cb = $q->getCategorieBloquante();
        if (!$cb) return true;
        $blo = Sys::getOneData('Formation','Categorie/'.$cb->Id.'/Etape/SessionId='.$this->Id.'');
        if ($blo->Debloquage) return true;
        else return false;
    }
    /**
     * checkSessionTeam
     * Verifie la validité d'une session et de l'equipe par rapport aux informations fournies
     */
    function checkSessionTeam($equipeId,$sessionId){
        if ($sessionId!=$this->Id) return false;
        if (!$this->EnCours) return false;
        if (!Sys::getCount('Formation','Session/'.$this->Id.'/Equipe/Numero='.$equipeId)) return false;
        return true;
    }
    /**
     * checkSession
     * Verifie la validité d'une session par rapport aux informations fournies
     */
    function checkSession($sessionId){
        if ($sessionId!=$this->Id) return false;
        if (!$this->EnCours) return false;
        return true;
    }
    /**
     * getProjet
     * Renvoie le projet de la session
     */
    function getProjet() {
        if (isset($this->_Projet))return $this->_Projet;
        $p = $this->getParents('Projet');
        foreach ($p as $pr) {
            $this->_Projet = $pr;
            break;
        }
        return $this->_Projet;
    }
    /**
     * getCategories
     * Renvoie les categories du projet de la session en cours.
     */
    function getCategories() {
        //recuperation du projet
        $p = $this->getProjet();
        return Sys::getData('Formation','Projet/'.$p->Id.'/Categorie/*',0,100,'ASC','Id');
    }
    /**
     * getMaps
     * Renvoie les maps du projet de la session en cours.
     */
    function getMaps() {
        //recuperation du projet
        $p = $this->getProjet();
        return $p->getChildren('Map');
    }
    /**
     * getQuestions
     * Renvoie les Questions du projet de la session en cours.
     */
    function getQuestions() {
        //recuperation du projet
        $p = $this->getProjet();
        return $p->getChildren('Categorie/*/Question');
    }

    /**
     * getOrderedQuestions
     * Renvoie les Questions du projet ordonnées en fonction des catégories parentes.

    function getOrderedQuestions() {
        //recuperation du projet
        $p = $this->getProjet();
        $questions = array();
        $cats = $p->getChildren('Categorie');
        $this->recursiveGetQuestion($cats,$questions);
        array_walk($questions,function(&$q,$k){
            $q->Ordre = $k+1;
        });

        return $questions;
    }
    //trie les cats suivant leur ordre puis recupère les question de manière ordonnée
    function recursiveGetQuestion($cats,&$questions){
        usort($cats,function($a,$b){
            $a = $a->Ordre;
            $b = $b->Ordre;
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        foreach($cats as $cat){
            $qs = $cat->getChildren('Question');
            if($qs){
                usort($qs,function($a,$b){
                    $a = $a->Ordre;
                    $b = $b->Ordre;
                    if ($a == $b) {
                        return 0;
                    }
                    return ($a < $b) ? -1 : 1;
                });
                foreach($qs as $q){
                    $questions[] = $q;
                }
            }
            $sCats = $cat->getChildren('Categorie');
            if($sCats){
                $this->recursiveGetQuestion($sCats,$questions);
            }
        }
    }*/


    /**
     * getTypeQuestions
     * Renvoie les Questions du projet de la session en cours.
     */
    function getTypeQuestions() {
        //recuperation du projet
        $p = $this->getProjet();
        return $p->getChildren('Categorie/*/Question/*/TypeQuestion');
    }
    /**
     * getTypeQuestionValeurs
     * Renvoie les Valeurs des types de Questions du projet de la session en cours.
     */
    function getTypeQuestionValeurs() {
        //recuperation du projet
        $p = $this->getProjet();
        return $p->getChildren('Categorie/*/Question/*/TypeQuestion/*/TypeQuestionValeur');
    }
    /**
     * getTypeReponses
     * Renvoie les Questions du projet de la session en cours.
     */
    function getTypeReponses() {
        //recuperation du projet
        $p = $this->getProjet();
        return Sys::getData('Formation','TypeReponse');
    }
    /**
     * getRegions
     * Renvoie les Questions du projet de la session en cours.
     */
    function getRegions() {
        //recuperation du projet
        $p = $this->getProjet();
        return  $p->getChildren('InterRegion/*/Region');
    }
    /**
     * saveResult
     * Sauvegarde des réponse en fonction d'une session d'une equipe et des id de question Id
     * @equipe  int numéro d'equipe
     */
    function saveResult($equipe) {
        $next = null;
        //vérificaiton de la validité de l'equipe
        $eq = $this->getChildren('Equipe/Numero='.$equipe);
        if (sizeof($eq)) {
            $eq = $eq[0];
            //L'equipe existe on enregistre les résultats
            foreach ($_POST as $key=>$valeur){
                if (preg_match('#^qi_([0-9]+)$#',$key,$out)){
                    $question_id = $out[1];

                    //vérification de la non existence de la réponse sinon on sort.
                    $nb = Sys::getCount('Formation','Reponse/EquipeId='.$eq->Id.'&TypeQuestionId='.$question_id.'');
                    if ($nb){
                        //erreur la réponse existe déjà
                        $this->addError(Array('Prop'=>'Zob','Message'=>"La réponse existe déjà $nb ".'  Reponse/TypeQuestion.TypeQuestionId('.$question_id.')&Equipe.EquipeId('.$equipe.')'));
                        return false;
                    }

                    //génération de la réponse
                    $rep = genericClass::createInstance('Formation','Reponse');
                    $rep->Valeur = $valeur;
                    $rep->addParent('Formation/Equipe/'.$eq->Id);
                    $rep->addParent('Formation/TypeQuestion/'.$question_id);
                    $rep->Save();

                    $question = Sys::getOneData('Formation','Question/TypeQuestion/'.$question_id);
                    if(!empty($question->Parametres)){
                        $params = json_decode($question->Parametres,true);
                        if(!empty($params['goto'])){
                            if(is_array($params['goto']) && !empty($params['goto'][$valeur] )){
                                $next = $params['goto'][$valeur];
                            } elseif ( !is_array( $params['goto'] )){
                                $next = $params['goto'];
                            }
                        }
                    }
                }
            }
            if($next) return $next;
            return true;
        }
    }
    /**
     * getBoitier
     * Récupère l'objet boitier
     */
    public function getBoitier() {
        //Vérification de l'existence du numéro de boitier
        $boitier = Sys::getOneData('Formation', 'Boitier');
        if (!is_object($boitier)){
            //création d'un numéro de boitier
            $options = array(
                'http' => array(
                    'method'  => 'GET',
                    'header'=>  "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n"
                )
            );

            $context  = stream_context_create( $options );
            $result = file_get_contents( 'http://edf.e-p.consulting/Formation/Boitier/getNumero.json', false, $context );
            $response = json_decode( $result );
            if ($response->success){
                //réception du numéro
                $boitier = genericClass::createInstance('Formation','Boitier');
                $boitier->Numero = $response->numero;
                $boitier->Save();
            }
        }
        return $boitier;
    }
    /**
     * Synchro
     * Syncrhonisation de la session avec le central pour aggrégation des données
     */
    public function Synchro() {
        $this->checkReponse();
        $boitier = $this->getBoitier();
        if (!is_object($boitier)) return;
        $projet = $this->getParents('Projet');
        $projet = $projet[0];
        $region = $this->getParents('Region');
        $region = $region[0];
        //préparation des données
        $data = '{
            "date": '.$this->Date.',
            "boitier" : "'.$boitier->Numero.'",
            "region" : "'.$region->Id.'",
            "projet" : "'.$projet->Id.'",
            "nom" : "'.str_replace('"', '\"',$this->Nom).'",
            "equipes" : [';
        $equipe = $this->getChildren('Equipe');
        $virgule = '';
        foreach ($equipe as $e){
            $data .= $virgule.'{
                    "table": '.$e->Numero.',
                    "reponses": [';
                $reponses = $e->getChildren('Reponse');
                $virgule2 = '';
                foreach ($reponses as $r){
                    $tq = $r->getParents('TypeQuestion');
                    if (sizeof($tq)) {
                        $tq = $tq[0];
                        $data .= $virgule2 . '{"typequestion": "' . $tq->Id . '", "valeur":"' . $this->textToJson($r->Valeur) . '"}';
                        $virgule2 = ',';
                    }
                }
            $virgule = ',';
            $data.=']}';
        }
        $data .='
            ]
        }';

        //sending informations
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $data,
                'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create( $options );
        $result = file_get_contents( 'http://edf.e-p.consulting/Formation/Session/Reception.htm', false, $context );
        $response = json_decode( $result );
        if (isset($response->success)&&$response->success){
            $this->Synchro = 1;
            $this->Save();
        }
    }

    /**
     * Reception
     * Syncrhonisation des sessions depuis les boitiers
     *
     */
    public function reception() {
        $tmpjson = file_get_contents('php://input');
        try {
            $json = json_decode ($tmpjson);
            //recherche de la session
            $sess = Sys::getOneData('Formation', 'Session/Date='.$json->date);
            if (is_object($sess)){
                //alors on supprime on reinsère
                $sess->Delete();
            }
            //recherche du boitier
            $boitier = Sys::getOneData('Formation','Boitier/'.$json->boitier);
            //création
            $sess = genericClass::createInstance('Formation','Session');
            $sess->AddParent('Formation/Region/'.$json->region);
            $sess->AddParent('Formation/Projet/'.$json->projet);
            $sess->AddParent($boitier);
            $sess->Date = $json->date;
            $sess->Nom = $json->nom;
            $sess->Termine = true;
            $sess->Save();
            //ajout des equipes
            foreach ($json->equipes as $e) {
                $eq = genericClass::createInstance('Formation','Equipe');
                $eq->AddParent($sess);
                $eq->Numero = $e->table;
                $eq->Save();
                foreach ($e->reponses as $r) {
                    $rep = genericClass::createInstance('Formation','Reponse');
                    $rep->AddParent($eq);
                    $rep->AddParent('Formation/TypeQuestion/'.$r->typequestion);
                    $rep->Valeur = $r->valeur;
                    $rep->Save();
                }
            }

            //ajout du commentaire de synchro
            $h = genericClass::createInstance('Formation','SynchroHisto');
            $h->Description = date('d/m/Y H:i:s').' Synchro session '.$sess->Nom.' OK from '.$_SERVER['REMOTE_ADDR'];
            $h->Data = $tmpjson;
            $h->addParent($boitier);
            $h->Save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * clearTexte
     * text to json converter
     */
    public function textToJson($text) {
        $text = str_replace('"', '\"',$text);
        $text = str_replace("\n", ' ',$text);
        $text = str_replace("\r", '',$text);
        $text = str_replace("\t", '',$text);
        return $text;
    }
}