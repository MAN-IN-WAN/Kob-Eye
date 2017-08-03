<?php
/**
 * Class AbtelBackup
 * Installation des taches plafnifiées
 * 0 6 * * * apache /usr/bin/php /var/www/html/cron.php parc.azko.fr Parc/Bash/Renew.cron
 * *\/5 * * * * apache /usr/bin/php /var/www/html/cron.php parc.azko.fr Parc/Bash/Execute.cron
 */
class AbtelBackup extends Module{
    public $classLoader=null;
    /**
     * Surcharge de la fonction init
     * Avant l'authentification de l'utilisateur
     * @void
     */
    function init (){
        parent::init();
    }
    /**
     * Surcharge de la fonction postInit
     * Après l'authentification de l'utilisateur
     * Toutes les fonctionnalités sont disponibles
     * @void
     */
    function postInit (){
        parent::postInit();
        //chargement des variables globales par défaut pour le module boutique
        $this->initGlobalVars();
    }
    /**
     * Surcharge de la fonction Check
     * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
     * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
     */
    function Check () {
        parent::Check();
        //teste le role
        $r = Sys::getData('Systeme','Role/Title=BACKUP_ADMIN');
        if (sizeof($r)){
            $r = $r[0];
            //teste le groupe
            $g = $r->getChildren('Group');
            if (sizeof($g)){
                //test user
                $u = $g[0]->getChildren('User');
                if (!sizeof($u))
                    $this->createUser($g[0]);
            }else{
                $g = $this->createGroup($r);
                $this->createUser($g);
            }
        }else{
            //il faut tout créer
            //création du role
            $r = genericClass::createInstance('Systeme','Role');
            $r->Title = "BACKUP_ADMIN";
            $r->Save();
            //création du groupe
            $g = $this->createGroup($r);
            $this->createUser($g);
        }
    }
    /**
     * Creation du groupe et de tout ses menus
     */
    private function createGroup($role){
        //creation du groupe
        $g = genericClass::createInstance('Systeme','Group');
        $g->Nom = "[BACKUP] Accès admin";
        $g->Skin = "AngularAdmin";
        $g->AddParent($role);
        $g->Save();
        //création des menus
        $m = genericClass::createInstance('Systeme','Menu');
        $m->Titre = "Tableau de bord";
        $m->Alias = "Systeme/User/DashBoard";
        $m->AddParent($g);
        $m->Save();
        return $g;
    }
    /**
     * Creation du groupe et de tout ses menus
     */
    private function createUser($group){
        //creation du groupe
        $g = genericClass::createInstance('Systeme','User');
        $g->Mail = "backup@abtel.fr";
        $g->Nom = "Backup";
        $g->Prenom = "Admin";
        $g->Login = "Backup";
        $g->Pass = md5("backup");
        $g->Actif = 1;
        $g->AddParent($group);
        $g->Save();
        return $g;
    }
    /**
     * Initilisation des variables globales disponibles pour la boutique
     */
    private function initGlobalVars(){
    }
}
