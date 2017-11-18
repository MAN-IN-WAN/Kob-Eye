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


        $store = Sys::getData('AbtelBackup','BackupStore/Titre=Sauvegarde Locale');
        if (!sizeof($store)){
            $s = genericClass::createInstance('AbtelBackup','BackupStore');
            $s->Titre = 'Sauvegarde Locale';
            $s->Type = 'Local';
            $s->Save();
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
    /**
     * UTILS FUNCTIONS
     */
    static public function localExec( $command, $activity = null,$total=0){
        /*exec( $command,$output,$return);
        if( $return ) {
            throw new RuntimeException( "L'éxécution de la commande locale a échoué. commande : ".$command." \n ".print_r($output,true));
        }
        return implode("\n",$output);*/
        $proc = popen("$command 2>&1 ; echo Exit status : $?", 'r');
        $complete_output = "";
        while (!feof($proc)){
            $buf     = fread($proc, 4096);
            //cas borg
            if (preg_match('#^([0-9\.]+)? MB O#',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1]))/$total;
                $activity->setProgression($progress);
            }
            if (preg_match('#^([0-9\.]+)? GB O#',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1])*1000)/$total;
                $activity->setProgression($progress);
            }
            if (preg_match('#^([0-9\.]+)? TB O#',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1])*1000000)/$total;
                $activity->setProgression($progress);
            }
            $complete_output .= $buf;
        }
        pclose($proc);
        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        if( $matches[0] !== "0" ) {
            throw new RuntimeException( $complete_output, (int)$matches[0] );
        }
        return str_replace("Exit status : " . $matches[0], '', $complete_output);
    }

    static public function getPid($prog){
        try {
            $output = AbtelBackup::localExec('sudo pgrep ' . $prog);
        }catch (Exception $e){
            return false;
        }
        return $output;
    }
    static public function getMyIp(){
        $output = AbtelBackup::localExec('/usr/sbin/ifconfig');
        preg_match('#inet ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)#',$output,$out);
        return $out[1];
    }
    static function getFileSize($path){
        $output = AbtelBackup::localExec('/usr/bin/ls -l "'.$path.'"');
        preg_match('#^[rwx-]+ [0-9]{1} [^ ]+ [^ ]+ ([0-9]+)#',$output,$out);
        return $out[1];
    }
    static function resetData(){
        //Suppression des fichiers
        AbtelBackup::localExec('sudo rm /backup/nfs/* -Rf');
        AbtelBackup::localExec('sudo rm /backup/borg/* -Rf');
        AbtelBackup::localExec('sudo rm /backup/restore/* -Rf');
        //AbtelBackup::localExec('sudo rm /backup/samba/* -Rf');
        //vidage des tables
        $GLOBALS["Systeme"]->Db[0]->query('TRUNCATE `kob-AbtelBackup-Activity`;TRUNCATE `kob-AbtelBackup-BackupStore`;TRUNCATE `kob-AbtelBackup-BorgRepo`;TRUNCATE `kob-AbtelBackup-Esx`;TRUNCATE `kob-AbtelBackup-EsxVm`;TRUNCATE `kob-AbtelBackup-EsxVmRestorePointId`;TRUNCATE `kob-AbtelBackup-RemoteJob`;TRUNCATE `kob-AbtelBackup-RestorePoint`;TRUNCATE `kob-AbtelBackup-SambaJob`;TRUNCATE `kob-AbtelBackup-SambaShare`;TRUNCATE `kob-AbtelBackup-VmJob`;');
        return true;
    }
    static function initFolders(){
        //Suppression des fichiers
        AbtelBackup::localExec('if [ ! -d /backup ]; then sudo mkdir /backup; fi');
        AbtelBackup::localExec('if [ ! -d /backup/nfs ]; then sudo mkdir /backup/nfs; fi');
        AbtelBackup::localExec('sudo chown nfsnobody:nfsnobody /backup/nfs');
        AbtelBackup::localExec('if [ ! -d /backup/borg ]; then sudo mkdir /backup/borg; fi');
        AbtelBackup::localExec('sudo chown backup:backup /backup/borg');
        AbtelBackup::localExec('if [ ! -d /backup/restore ]; then sudo mkdir /backup/restore; fi');
        AbtelBackup::localExec('sudo chown backup:backup /backup/restore');
        AbtelBackup::localExec('if [ ! -d /backup/samba ]; then sudo mkdir /backup/samba; fi');
        AbtelBackup::localExec('sudo chown backup:backup /backup/samba');
        //redemarrge des services
        AbtelBackup::localExec('sudo systemctl restart nfs-server');
        return true;
    }
}
