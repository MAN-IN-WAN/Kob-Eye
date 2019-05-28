<?php
require_once 'Modules/AbtelBackup/Class/Job.class.php';

class VmJob extends Job {
    protected $tag = '[VMJOB]';
    protected static $KEObj = 'VmJob';
    protected static $desc = 'Job Machine virtuelle';
    protected $pStarts = array(
                            0,
                            10,
                            20,
                            45,
                            75,
                            100
                        );

    /**
     * Save
     * Surcharge de la fonction Save
     */
    public function Save() {
        //on vérfiei que la compression est désactivée pour activer la fonction PageFile
        if ($this->PageFile) $this->Compression=false;
        return parent::Save();
    }


    public static function execute() {
        //intialisation des dates
        $d = time();
        $week = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        $weekday = $week[date('w',$d)];
        $hour = date('H',$d);
        $minute = intval(date('i',$d));
        $month = intval(date('m',$d));
        $monthday = date('j',$d);
        $jobs = Sys::getData('AbtelBackup',static::$KEObj.'/Enabled=1&(!Minute=*+Minute='.$minute.'!)&(!Heure=*+Heure='.$hour.'!)&(!Jour=*+Jour='.$monthday.'!)&(!Mois=*+Mois='.$month.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$weekday.'=1!)!)');

        foreach ($jobs as $j) {
            $task = genericClass::createInstance('Systeme', 'Tache');
            $task->Type = 'Fonction';
            $task->Nom = 'Job machine virtuelle :' . $j->Titre.'. éxécution du '.date('d/m/Y H:i:s');
            $task->TaskModule = 'AbtelBackup';
            $task->TaskObject = 'VmJob';
            $task->TaskType = 'backup';
            $task->TaskId = $j->Id;
            $task->TaskFunction = 'run';
            $task->addParent($j);
            $task->Save();
        }
    }

    /**
     * stop
     * Stoppe un job de backup
     */
    public function stop()
    {
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Collecteur';
        $task->Nom = 'Stop :' . $this->Titre;
        $task->addParent($this);
        $task->Save();

        $v = Sys::getOneData('AbtelBackup', 'EsxVm/' . $this->CurrentVm);
        if ($v) {
            $act = $task->createActivity($v->Titre . ' > Arret Utilisateur: Step ' . $this->Step,'Info');
            $act->addDetails($v->Titre . " > Arret Utilisateur", 'red', true);
        } else{
            $act = $task->createActivity(' > Vm non définie > Arret Utilisateur: Step ' . $this->Step, 'Info');
            $act->addDetails(" Arret Utilisateur", 'red', true);
        }

        if ($this->Running){
            switch ($this->Step){
                case 1:
                    $this->addError(Array('Message'=>'Impossible de stopper le job pendant l\'initialisation.'));
                    $act->addDetails("Impossible de stopper le job pendant l'initialisation.", 'red', true);
                    $act->Terminate(false);

                    return false;
                break;
                case 2:
                    $this->addError(Array('Message'=>'Impossible de stopper le job pendant la configuration.'));
                    $act->addDetails("Impossible de stopper le job pendant la configuration.", 'red', true);
                    $act->Terminate(false);

                    return false;
                break;
                case 3:
                    $this->addError(Array('Message'=>'Impossible de stopper le job pendant le clonage.'));
                    $act->addDetails("Impossible de stopper le job pendant le clonage.", 'red', true);
                    $act->Terminate(false);

                    return false;
                break;
                case 4:
                    if (AbtelBackup::getPid('bsdtar')){
                        $this->clearAct(false);
                        AbtelBackup::localExec('sudo killall -9 bsdtar');
                        $this->addSuccess(Array('Message'=>'Compression stoppée avec succès.'));
                        $act->Terminate();
                    }else{
                        $this->clearAct(true);
                        $this->addWarning(Array('Message'=>'Le processus n\'a pas été trouvé.'));
                        $act->addDetails(" > Le processus n'a pas été trouvé.", 'red', true);
                        $act->Terminate(false);
                    }
                    $this->Running = false;
                    parent::Save();
                    return true;
                break;
                case 5:
                    if (AbtelBackup::getPid('borg')) {
                        $this->clearAct(false);
                        AbtelBackup::localExec('sudo killall -9 borg');
                        $vms = Sys::getData('AbtelBackup','VmJob/'.$this->Id.'/EsxVm');
                        foreach ($vms as $v){
                            $borg = $v->getOneParent('BorgRepo');
                            AbtelBackup::localExec('sudo rm '.$borg->Path.'/lock.* -Rf');
                        }
                        $this->addSuccess(Array('Message' => 'Déduplication stoppée avec succès.'));
                        $act->Terminate();

                    }else{
                        $this->clearAct(true);
                        $this->addWarning(Array('Message'=>'Le processus n\'a pas été trouvé.'));
                        $act->addDetails(" > Le processus n'a pas été trouvé.", 'red', true);
                        $act->Terminate(false);
                    }
                    $this->Running = false;
                    parent::Save();
                    return true;
                break;
                default:
                    $this->clearAct(true);
                    $this->resetState();
                    parent::Save();
                    $this->addSuccess(Array('Message' => 'Job stoppé avec succès.'));
                    $act->Terminate();

                    return true;
                break;
            }
        }else{

            $act->addDetails("Arret Utilisateur : Impossible de stopper le job $this->Titre. Il n'est pas démarré.", 'red',true);
            $act->Terminate(false);

            $this->addError(Array('Message'=>'Impossible de stopper le job. Il n\'est pas démarré.'));
            return false;
        }
    }
    /**
     * run
     * Demarre ou resume un job de backup de vm
     */
    public function run($task) {
        //test running
        if ($this->Running) {
            $act = $task->createActivity(' > Impossible de démarrer, le job est déjà en cours d\'éxécution');
            $act->Terminate(false);
            return;
        }
        $this->resetState();
        $this->Running = true;
        parent::Save();
        //init
        Klog::l('DEBUG demarrage vm');
        $act = $task->createActivity(' > Demarrage du Job Vm : '.$this->Titre.' ('.$this->Id.')','Info');
        $act->Terminate();
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");

        //pour chaque vm
        $vms = Sys::getData('AbtelBackup','VmJob/'.$this->Id.'/EsxVm');

        //calcul des progress span
        $pSpan = array();
        for($n =0; $n < count($this->pStarts);$n++){
            $pSpan[] = ($this->pStarts[$n+1] - $this->pStarts[$n])/count($vms);
        }



        foreach ($vms as $v){
            Klog::l('DEBUG vm ==> '.$v->Id.' STEP: '.$this->Step);
            $act = $task->createActivity(' > Demarrage de la VM : '.$v->Titre.' ('.$v->Id.')','Info');
            $act->Terminate();
            //définition de la vm en cours
            $this->setStep(1);
            $this->setCurrentVm($v->Id);
            $esx = $v->getOneParent('Esx');
            $borg = $v->getOneParent('BorgRepo');
            try {
                //nettoyage
                if (intval($this->Step)<=1){
                    unset($act);
                    $act = $task->createActivity($v->Titre.' > Nettoyage des archives','Exec',$pSpan[0]);
                    $this->initJob($v,$esx,$act);
                }

                //configuration
                if (intval($this->Step)<=2){
                    unset($act);
                    $act = $task->createActivity($v->Titre.' > Configuration vmjob','Exec',$pSpan[1]);
                    $act = $this->configJob($v,$esx,$act);
                }

                //clonage
                if (intval($this->Step)<=3){
                    unset($act);
                    $act = $task->createActivity($v->Titre.' > Clonage vmjob','Exec',$pSpan[2]);
                    $act = $this->cloneJob($v,$esx,$act);
                }

                //compression
                if (intval($this->Step)<=4){
                    if ($this->Compression) {
                        unset($act);
                        $act = $task->createActivity($v->Titre . ' > Compression vmjob', 'Exec', $pSpan[3]);
                        $act = $this->compressJob($v, $act);
                    }elseif($this->PageFile){
                        $act = $task->createActivity($v->Titre . ' > Supression PageFile / HiberFile', 'Exec', $pSpan[3]);
                        $act = $this->removePageFileFromVm($v, $act);
                    }
                }

                //déduplication
                if (intval($this->Step)<=5){
                    unset($act);
                    $act = $task->createActivity($v->Titre.' > Déduplication vmjob','Exec',$pSpan[4]);
                    $act = $this->deduplicateJob($v,$borg,$act);
                }

                $act = $task->createActivity(' > Fin de la VM : '.$v->Titre.' ('.$v->Id.')','Info');
                $act->Terminate();

            }catch (Exception $e){
                if(!$act) $act = $task->createActivity($v->Titre.' > Exception: Step '.$this->Step,'Info');
                $act->addDetails($v->Titre." ERROR => ".$e->getMessage(),'red');
                $act->Terminate(false);
                //opération terminée
                $this->Running = false;
                $this->Errors = true;
                parent::Save();
                return false;
            }
        }
        //opération terminée
        $this->resetState();

        ///tache de retention
        $this->createRetentionTask();
        return true;
    }
    /**
     * createRetentionTask
     * Création de la tache de retention
     */
    public function createRetentionTask() {
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Rotation job machine virtuelle :' . $this->Titre.'. rotation du '.date('d/m/Y H:i:s');
        $task->TaskModule = 'AbtelBackup';
        $task->TaskObject = 'VmJob';
        $task->TaskType = 'backup';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'rotate';
        $task->addParent($this);
        $task->Save();
    }

    /**
     * setCurrentVm
     * Déinfition de la vm en cours de traitement
     */
    private function setCurrentVm($v){
        $this->CurrentVm = $v;
        parent::Save();
    }
    /**
     * Nettoyage de l'esx et du stor elocal
     */
    private function initJob($v,$esx,$act) {
        Klog::l('DEBUG Test INIT JOB');
        $this->setStep(1); //Initialisation
        $act->addDetails('Suppression du script ghettoVCB','yellow');
        //nettoyage sauvegarde précédentes
        $act->addDetails($v->Titre.' ---> suppression du script ghettoVCB');
        $esx->remoteExec("if [ -f /ghettoVCB.sh ]; then rm /ghettoVCB.sh; fi");
        $act->addProgression(15);

        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression des snapshots');
        $esx->remoteExec("vim-cmd vmsvc/snapshot.removeall ".$v->RemoteId." && sleep 5");
        $act->addProgression(15);

        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression du fichier .work');
        $esx->remoteExec("if [ -d '/tmp/ghettoVCB.work' ]; then rm -Rf '/tmp/ghettoVCB.work'; fi");
        $act->addProgression(15);

        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression de la complete');
        //AbtelBackup::localExec("if [ -d '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre."' ]; then sudo rm -Rf '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre."'; fi");
        //$act->addProgression(15);
        AbtelBackup::localExec("if [ -d '/backup/nfs/".$v->Titre."' ]; then sudo rm -Rf '/backup/nfs/".$v->Titre."'; fi");
        $act->addProgression(30);

        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression archive');
        //AbtelBackup::localExec("if [ -f '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar; fi");
        AbtelBackup::localExec("if [ -f '/backup/nfs/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/".$v->Titre.".tar; fi");
        $act->addProgression(25);

        parent::Save();
        return $act;
    }
    /**
     * configJob
     * Confioguration du job et de l'esx
     */
    private function configJob($v,$esx,$act){
        $this->setStep(2); //Configuration

        //creation de la configuration
        $config='VM_BACKUP_VOLUME=/vmfs/volumes/ABTEL_BACKUP/
DISK_BACKUP_FORMAT=thin
VM_BACKUP_ROTATION_COUNT=1
POWER_VM_DOWN_BEFORE_BACKUP=0
ENABLE_HARD_POWER_OFF=0
ITER_TO_WAIT_SHUTDOWN=3
POWER_DOWN_TIMEOUT=5
ENABLE_COMPRESSION=0
VM_SNAPSHOT_MEMORY=0
VM_SNAPSHOT_QUIESCE=0
ALLOW_VMS_WITH_SNAPSHOTS_TO_BE_BACKEDUP=0
ENABLE_NON_PERSISTENT_NFS=0
UNMOUNT_NFS=0
NFS_SERVER='.AbtelBackup::getMyIp(true).'
NFS_VERSION=nfs
NFS_MOUNT=/backup/nfs
NFS_LOCAL_NAME=ABTEL_BACKUP
NFS_VM_BACKUP_DIR=EsxVm/'.$esx->IP.'/
SNAPSHOT_TIMEOUT=15
EMAIL_ALERT=0
EMAIL_LOG=0
EMAIL_SERVER=
EMAIL_SERVER_PORT=25
EMAIL_DELAY_INTERVAL=1
EMAIL_TO=
EMAIL_ERRORS_TO=
EMAIL_FROM=
WORKDIR_DEBUG=0
VM_SHUTDOWN_ORDER=
VM_STARTUP_ORDER=
';
        $act->addDetails('-> configuration vmjob','yellow');
        $act->addDetails($v->Titre.' ---> creation de la config ghettoVCB');
        $esx->remoteExec("echo '$config' > /ghettovcb.conf");
        $act->addProgression(50);
        parent::Save();
        $act->addDetails($v->Titre.' ---> copy du script ghettoVCB');
        $esx->copyFile('ghettoVCB.sh');
        $act->addProgression(40);
        parent::Save();
        $act->addDetails($v->Titre.' ---> montage du NFS');
        $esx->remoteExec("esxcfg-nas -a ABTEL_BACKUP -o ".AbtelBackup::getMyIp(true)." -s /backup/nfs",null,true);
        $act->addProgression(10);
        parent::Save();
        return $act;
    }
    /**
     * cloneJob
     * Clonage de la vm
     */
    private function cloneJob($v,$esx,$act){
        $this->setStep(3); //'Clonage'
        $act->addDetails($v->Titre.' ---> clonage de la vm');
        $esx->remoteExec('sh ghettoVCB.sh -m "' . $v->Titre . '" -g ghettovcb.conf',$act ,false);
        $act->setProgression(100);
        parent::Save();
        return $act;
    }
    /**
     * compressJob
     * Compression de la vm
     */
    private function compressJob($v,$act){
        $total = AbtelBackup::getSize('/backup/nfs/'.$v->Titre);
        $this->setStep(4); //'Compression'
        $act->addDetails($v->Titre.' ---> compression du clone TOTAL:'.$total);
        AbtelBackup::localExec("sudo bsdtar cSf '/backup/nfs/".$v->Titre.".tar' '/backup/nfs/".$v->Titre."/".$v->Titre."-A'",$act,$total,'/backup/nfs/'.$v->Titre.'.tar');
        parent::Save();
        $act->addProgression(100);
        return $act;
    }
    /**
     * deduplicateJob
     * Déduplication de la vm
     */
    private function deduplicateJob($v,$borg,$act){
        $this->setStep(5); //'Déduplication'
        $act->addDetails($v->Titre.' Redéfinition des droits '.$borg->Path);
        //AbtelBackup::localExec('borg delete --cache-only '.$borg->Path); //Supression du cache eventuellement corrompu
        AbtelBackup::localExec('sudo chown -R backup:backup '.$borg->Path.''); //On s'assure que les fichiers borg ne soient pas en root
        $act->addDetails($v->Titre.' Suppression du borg lock '.$borg->Path);
        AbtelBackup::localExec('borg break-lock '.$borg->Path); //Supression des locks borg
        $act->addDetails($v->Titre.' Redéfinition des droits de la vm'.$borg->Path);
        AbtelBackup::localExec("sudo chmod -R 705 '/backup/nfs/".$v->Titre."'"); //On s'assure que les fichiers borg ne soient pas en root
        if ($this->Compression) {
            $act->addDetails($v->Titre.' Calcul de la taille du fichier '.'/backup/nfs/' . $v->Titre . '.tar');
            $total = AbtelBackup::getSize('/backup/nfs/' . $v->Titre . '.tar');
        }else{
            $act->addDetails($v->Titre.' Calcul de la taille du dossier '.'/backup/nfs/' . $v->Titre);
            $total = AbtelBackup::getSize('/backup/nfs/'.$v->Titre);
        }
        $act->addDetails($v->Titre.' ---> TOTAL (Mo):'.$total);
        $act->addDetails($v->Titre.' ---> déduplication de la vm');
        //AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".time()." '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar'",$act);


        //Recup taille pour graphique/progression
        $v->Size = $total;
        $v->Save();

        $point = time();
        //file_put_contents('tototoottoto',"export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".$point." '/backup/nfs/".$v->Titre.".tar'");
        if ($this->Compression)
            $cmd = "export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".$point." '/backup/nfs/".$v->Titre.".tar'";
        else $cmd = "export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".$point." '/backup/nfs/".$v->Titre."'";
        $act->addDetails($cmd);
        $det = AbtelBackup::localExec($cmd, $act, $total,null);


        //Recup taille pour graphique/progression
        $v->BackupSize = AbtelBackup::getSize($borg->Path);
        $v->Save();

        //création du point de restauration
        $v->createRestorePoint($point,$det);
        $act->setProgression(100);
        if ($this->Compression) {
            $act->addDetails($v->Titre . ' ---> suppression archive');
            //AbtelBackup::localExec("if [ -f '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar; fi");
            AbtelBackup::localExec("if [ -f '/backup/nfs/" . $v->Titre . ".tar' ]; then sudo rm -f /backup/nfs/" . $v->Titre . ".tar; fi");
        }
        return $act;
    }
    /**
     * resetState
     * Reinitialisation du job
     */
    private function resetState(){
        $this->setStep(0); //'Attente'
        $this->setCurrentVm('');
        $this->Running = false;
        $this->Progression = 0;
        parent::Save();
    }

    /**
     * rotate
     * Rotation des backups
     */
    public function rotate($task) {
        $act = $task->createActivity(' > Demarrage de la rotation du Job Vm : '.$this->Titre.' ('.$this->Id.')','Info');
        $act->Terminate();
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");

        //pour chaque vm
        $vms = Sys::getData('AbtelBackup','VmJob/'.$this->Id.'/EsxVm');

        foreach ($vms as $v){
            $act = $task->createActivity(' > Rotation de la VM : '.$v->Titre.' ('.$v->Id.')','Info');
            $act->Terminate();
            $esx = $v->getOneParent('Esx');
            $borg = $v->getOneParent('BorgRepo');
            try {
                $act->addDetails($v->Titre.' Redéfinition des droits '.$borg->Path);
                //AbtelBackup::localExec('borg delete --cache-only '.$borg->Path); //Supression du cache eventuellement corrompu
                AbtelBackup::localExec('sudo chown -R backup:backup '.$borg->Path.''); //On s'assure que les fichiers borg ne soient pas en root
                $act->addDetails($v->Titre.' Suppression du borg lock '.$borg->Path);
                AbtelBackup::localExec('borg break-lock '.$borg->Path); //Supression des locks borg

                //Recup taille pour graphique/progression
                $v->BackupSize = AbtelBackup::getSize($borg->Path);
                $v->Save();
                $prs = Sys::getData('AbtelBackup','EsxVm/'.$v->Id.'/RestorePoint/tmsCreate<'.(time()-(86400*intval($this->Retention))));
                foreach ($prs as $pr){
                    $pr->Delete();
                }

                //rotation du dépot pour nettoyer
                #TODO désactiver à terme ...
                $det = AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && borg prune -v --list --keep-within=".$this->Retention."d  ".$borg->Path."", $act);
                $act->addDetails($det);

                $act->setProgression(100);
                $act = $task->createActivity(' > Rotation Terminée : suppression de '.sizeof($prs).' version(s)','Info');
                $act->Terminate(true);
                return true;
            }catch (Exception $e){
                if(!$act) $act = $task->createActivity($v->Titre.' > Exception: Step '.$this->Step,'Info');
                $act->addDetails($v->Titre." ERROR => ".$e->getMessage(),'red');
                $act->Terminate(false);
                //opération terminée
                $this->Running = false;
                $this->Errors = true;
                parent::Save();
                return;
            }
        }
    }
    /**
     * createRemovePageFileTask
     * Création de la tache permettant de
     */
    public function createRemovePageFileTask() {
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Retrait des fichiers de pagination :' . $this->Titre.'. '.date('d/m/Y H:i:s');
        $task->TaskModule = 'AbtelBackup';
        $task->TaskObject = 'VmJob';
        $task->TaskType = 'backup';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'removePageFile';
        $task->addParent($this);
        $task->Save();
        return array('task'=>$task);
    }

    /**
     * removePageFile
     * Suppression des fichiers de pagination
     */
    public function removePageFile($task){
        $act = $task->createActivity(' > Suppression des fichiers pagefile hyberfile : '.$this->Titre.' ('.$this->Id.')','Info');
        $act->Terminate();
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");

        //pour chaque vm
        $vms = Sys::getData('AbtelBackup','VmJob/'.$this->Id.'/EsxVm');
        try {
            foreach ($vms as $v) {
                $act = $task->createActivity(' > Traitement de la VM : ' . $v->Titre . ' (' . $v->Id . ')');
                if (!$this->removePageFileFromVm($v,$act)) continue;
            }
            return true;
        }catch (Throwable $e){
            $act->addDetails('ERREUR => '.$e->getMessage().' ligne: '.$e->getLine().' code: '.$e->getCode().' file: '.$e->getFile().' trace: '.$e->getTraceAsString());
            $act->Terminate(false);
            return false;
        }
    }
    /**
     * removePageFileFromVm
     * Supprime les fichiers page des vms
     */
    public function removePageFileFromVm($v,$act){
        $this->setStep(4); //'Compression / Suppression Pagefile'
        $loop_number = 0;
        //vérification de la présence d'un clone
        if (!file_exists('/backup/nfs/' . $v->Titre)) {
            $act->addDetails('Le clone de la vm n\'est pas présent');
            $act->Terminate(false);
            return false;
        }
        //recherche des vmdks disques
        $clone_path = "/backup/nfs/".$v->Titre."/".$v->Titre."-A/";
        $cmd = 'sudo ls "' . $clone_path . '" | grep vmdk';
        $act->addDetails('Exec cmd: '.$cmd);
        $files = AbtelBackup::localExec($cmd);
        $files = explode("\n", $files);
        $act->addDetails(print_r($files, true));
        $act->Terminate(true);
        //on demonte les montages précédents
        //AbtelBackup::localExec('sudo dmsetup remove_all && sleep 5');
        //pour chaque fichier, on monte toutes les partitions
        foreach ($files as $f) {
            //si le nom est vide on conitune
            $f = trim($f);
            if (empty($f)) continue;
            //on vérifie que le fichier en soit pas juste u nfichier texte
            $type = AbtelBackup::localExec('sudo file "' . $clone_path . $f . '" | sed \'s/\([^:]*\): \(\.*\)/\2/\'');
            $act->addDetails('||'.$f.'||');
            if (trim($type) != "ASCII text") {
                //alors on monte le disque
                //$act = $task->createActivity(' > Montage du disque ' . $f, 'Info');
                $act->addDetails('disque -> '.$f);
                $type=explode(';',$type);
                foreach ($type as $t) {
                    $act->addDetails('       -> '.$t);
                }
                $act->addDetails('sudo losetup /dev/loop' . $loop_number . ' "' . $clone_path . $f . '"');
                try {
                    $mount = AbtelBackup::localExec('sudo losetup /dev/loop' . $loop_number . ' "' . $clone_path . $f . '"');
                }catch (Exception $e){
                    $act->addDetails($e->getMessage());
                    try {
                        $mount = AbtelBackup::localExec('sudo losetup /dev/loop' . $loop_number . ' "' . $clone_path . $f . '"');
                    }catch (Exception $e){
                        $act->addDetails($e->getMessage());
                    }
                }
                //on liste les partitions
                $act->addDetails('sudo cfdisk -P s /dev/loop'.$loop_number.' | grep NTFS  | sed -e \'s/^ \([0-9]\).*/\1/\'');
                $parts = AbtelBackup::localExec('sudo cfdisk -P s /dev/loop'.$loop_number.' | grep NTFS  | sed -e \'s/^ \([0-9]\).*/\1/\'');
                $parts = explode("\n",$parts);
                $act->addDetails(print_r($parts,true));
                $act->Terminate(true);
                try {
                    AbtelBackup::localExec('sudo partx -va /dev/loop' . $loop_number);
                }catch (Exception $e){
                    $act->addDEtails($e->getMessage());
                }
                foreach ($parts as $p){
                    if (empty($p))continue;
                    //$act = $task->createActivity(' >> Montage de la partition '.$p.' du disque ' . $f, 'Info');
                    //création des points de montage
                    $act->addDetails('if [ ! -d "' . $clone_path . 'part'.$p.'" ]; then sudo mkdir "' . $clone_path . 'part'.$p.'"; fi');
                    AbtelBackup::localExec('if [ ! -d "' . $clone_path . 'part'.$p.'" ]; then sudo mkdir "' . $clone_path . 'part'.$p.'"; fi');
                    //Montage des partitions
                    $act->addDetails('sudo mount /dev/loop'.$loop_number.'p'.$p.' "'.$clone_path.'part'.$p.'"');
                    AbtelBackup::localExec('sudo mount /dev/loop'.$loop_number.'p'.$p.' "'.$clone_path.'part'.$p.'"');
                    //Suppression des fichiers
                    if (file_exists($clone_path.'part'.$p.'/pagefile.sys')) {
                        $act->addDetails('Suppression du fichier "'.$clone_path.'part'.$p.'/pagefile.sys"');
                        AbtelBackup::localExec('sudo rm -f "' . $clone_path . 'part'.$p.'/pagefile.sys"');
                    }
                    if (file_exists($clone_path.'part1/hiberfil.sys')) {
                        $act->addDetails('Suppression du fichier "'.$clone_path.'part'.$p.'/hiberfil.sys"');
                        AbtelBackup::localExec('sudo rm -f "' . $clone_path . 'part'.$p.'/hiberfil.sys"');
                    }
                    //Demontage et suppression des points de montage
                    $act->addDetails('sudo umount "'.$clone_path.'part'.$p.'" && sudo rmdir "'.$clone_path.'part'.$p.'"');
                    AbtelBackup::localExec('sudo umount "'.$clone_path.'part'.$p.'" && sudo rmdir "'.$clone_path.'part'.$p.'"');
                    $act->Terminate(true);
                }

            }else{
                $act->addDetails('type '.$f.' -> |'.$type.'|');
            }
        }

        //$act = $task->createActivity(' >> Réinitialisation des points de montage', 'Info');
        $act->addDetails('sudo dmsetup remove_all');
        AbtelBackup::localExec('sudo dmsetup remove_all');
        $etat = AbtelBackup::localExec('sudo df -h');
        $act->addDetails($etat);
        $act->Terminate(true);
        return true;
    }
}