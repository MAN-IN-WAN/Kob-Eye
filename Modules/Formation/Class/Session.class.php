<?php
class FormationSession extends genericClass {
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
    }
    /**
     * setTeam
     * Ajoute une équipe à la session
     */
    function setTeam($num) {
        if (!(int) $num>0) return false;
        //Verification de l'existence de l'equipe dans la base de donnée
        $t = Sys::getCount('Formation', 'Session/'.$this->Id.'/Equipe/Numero='.$num);
        //si existe => erreur
        if ($t) return false;
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
     * checkSessionTeam
     * Verifie la validité d'une session par rapport aux informations fournies
     */
    function checkSessionTeam($equipeId,$sessionId){
        if ($sessionId!=$this->Id) return false;
        if (!$this->EnCours) return false;
        if (!Sys::getCount('Formation','Session/'.$this->Id.'/Equipe/Numero='.$equipeId)) return false;
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
}