<?php
class VmJob extends genericClass {
    public static function execute() {
        //intialisation des dates
        $d = time();
        $week = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        $weekday = $week[date('w',$d)];
        $hour = date('H',$d);
        $minute = intval(date('i',$d));
        $month = intval(date('m',$d));
        $monthday = date('j',$d);
        $jobs = Sys::getData('AbtelBackup','VmJob/Enabled=1&(!Minute=*+Minute='.$minute.'!)&(!Heure=*+Heure='.$hour.'!)&(!Jour=*+Jour='.$monthday.'!)&(!Mois=*+Mois='.$month.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$weekday.'=1!)!)');

        foreach ($jobs as $j) {
            $j->run();
        }
    }
    public function createActivity($title,$vm) {

        $act = genericClass::createInstance('AbtelBackup','Activity');
        $act->addParent($this);
        $act->addParent($vm);
        $act->Titre = '[VMJOB] '.date('d/m/Y H:i:s').' > '.$this->Titre.' > '.$title;
        $act->Started = true;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }
    public function runNow() {
        if ($this->Running){
            $this->addError(Array('Message'=>'Impossible de démarrer le job. Il est déjà en cours.'));
            return false;
        }
        $cmd = 'bash -c "exec nohup setsid php cron.php backup.abtel.local AbtelBackup/VmJob/'.$this->Id.'/run.cron > /dev/null 2>&1 &"';
        exec($cmd);
        return true;
    }
    /**
     * stop
     * Stoppe un job de backup
     */
    public function stop() {
        $vms = Sys::getData('AbtelBackup','VmJob/'.$this->Id.'/EsxVm');
        if ($this->Running){
            foreach ($vms as $v) {
                $act = $this->createActivity($v->Titre . ' > Arret Utilisateur: Step ' . $this->Step, $v);
                $act->addDetails($v->Titre . "Arret Utilisateur", 'red',true);
                $act->setProgression(100);
                $act->Success = true;
                $act->Save();
            }
            switch ($this->Step){
                case 1:
                    $this->addError(Array('Message'=>'Impossible de stopper le job pendant l\'initialisation.'));
                    return false;
                break;
                case 2:
                    $this->addError(Array('Message'=>'Impossible de stopper le job pendant la configuration.'));
                    return false;
                break;
                case 3:
                    $this->addError(Array('Message'=>'Impossible de stopper le job pendant le clonage.'));
                    return false;
                break;
                case 4:
                    if (AbtelBackup::getPid('bsdtar')){
                        $this->clearAct(false);
                        AbtelBackup::localExec('sudo killall -9 bsdtar');
                        $this->addSuccess(Array('Message'=>'Compression stoppée avec succès.'));
                    }else{
                        $this->clearAct(true);
                        $this->addWarning(Array('Message'=>'Le processus n\'a pas été trouvé.'));
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
                    }else{
                        $this->clearAct(true);
                        $this->addWarning(Array('Message'=>'Le processus n\'a pas été trouvé.'));
                    }
                    $this->Running = false;
                    parent::Save();
                    return true;
                break;
                default:
                    $this->Running = false;
                    $this->clearAct(true);
                    parent::Save();
                    $this->addSuccess(Array('Message' => 'Job stoppé avec succès.'));
                    return true;
                break;
            }
        }else{
            foreach ($vms as $v) {
                $act = $this->createActivity($v->Titre . ' > Arret Utilisateur: Step ' . $this->Step . ' > Echec' , $v);
                $act->addDetails($v->Titre . "Arret Utilisateur : Impossible de stopper le job. Il n'est pas démarré.", 'red',true);
                $act->Errors = true;
                $act->Save();
            }
            $this->addError(Array('Message'=>'Impossible de stopper le job. Il n\'est pas démarré.'));
            return false;
        }
    }
    /**
     * run
     * Demarre ou resume un job de backup de vm
     */
    public function run() {
        //test running
        if ($this->Running) {
            $act = $this->createActivity(' > Impossible de démarrer, le job est déjà en cours d\'éxécution');
            $act->Terminate();
            return;
        }
        $this->Running = true;
        parent::Save();
        //init
        Klog::l('DEBUG demarrage vm');
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        //pour chaque vm
        $vms = Sys::getData('AbtelBackup','VmJob/'.$this->Id.'/EsxVm');
        foreach ($vms as $v){
            Klog::l('DEBUG vm ==> '.$v->Id.' STEP: '.$this->Step);
            //définition de la vm en cours
            $this->setStep(1);
            $this->setCurrentVm($v->Id);
            $esx = $v->getOneParent('Esx');
            $borg = $v->getOneParent('BorgRepo');
            try {
                //nettoyage
                if (intval($this->Step)<=1){
                    unset($act);
                    $act = $this->createActivity($v->Titre.' > Nettoyage des archives',$v);
                    $this->initJob($v,$esx,$act);
                }

                //configuration
                if (intval($this->Step)<=2){
                    unset($act);
                    $act = $this->createActivity($v->Titre.' > Configuration vmjob',$v);
                    $act = $this->configJob($v,$esx,$act);
                }

                //clonage
                if (intval($this->Step)<=3){
                    unset($act);
                    $act = $this->createActivity($v->Titre.' > Clonage vmjob',$v);
                    $act = $this->cloneJob($v,$esx,$act);
                }

                //compression
                if (intval($this->Step)<=4){
                    unset($act);
                    $act = $this->createActivity($v->Titre.' > Compression vmjob',$v);
                    $act = $this->compressJob($v,$act);
                }

                //déduplication
                if (intval($this->Step)<=5){
                    unset($act);
                    $act = $this->createActivity($v->Titre.' > Déduplication vmjob',$v);
                    $act = $this->deduplicateJob($v,$borg,$act);
                }

            }catch (Exception $e){
                if(!$act) $act = $this->createActivity($v->Titre.' > Exception: Step '.$this->Step,$v);
                $act->addDetails($v->Titre." ERROR => ".$e->getMessage(),'red');
                $act->Terminated = true;
                $act->Errors = true;
                $act->Save();
                //opération terminée
                $this->Running = false;
                $this->Errors = true;
                parent::Save();
                return;
            }
        }
        //opération terminée
        $this->resetState();
    }
    /**
     * setStep
     * Définie l'étape en cours.
     * Permet une reprise en repartant de l'étape en erreur
     */
    private function setStep($step){
        $this->Step = $step;
        parent::Save();
    }
    /**
     * setCurrentVm
     * Déinfition de la vm en cours de traitement
     */
    private function setCurrentVm($v){
        $this->currentVm = $v;
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
        $this->Progression = 1.5;
        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression des snapshots');
        $esx->remoteExec("vim-cmd vmsvc/snapshot.removeall ".$v->RemoteId." && sleep 5");
        $act->addProgression(15);
        $this->Progression = 3;
        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression du fichier .work');
        $esx->remoteExec("if [ -d '/tmp/ghettoVCB.work' ]; then rm -Rf '/tmp/ghettoVCB.work'; fi");
        $act->addProgression(15);
        $this->Progression = 4.5;
        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression de la complete');
        //AbtelBackup::localExec("if [ -d '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre."' ]; then sudo rm -Rf '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre."'; fi");
        //$act->addProgression(15);
        AbtelBackup::localExec("if [ -d '/backup/nfs/".$v->Titre."' ]; then sudo rm -Rf '/backup/nfs/".$v->Titre."'; fi");
        $act->addProgression(30);
        $this->Progression = 7.5;
        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression archive');
        //AbtelBackup::localExec("if [ -f '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar; fi");
        AbtelBackup::localExec("if [ -f '/backup/nfs/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/".$v->Titre.".tar; fi");
        $act->addProgression(25);
        $this->Progression = 10;
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
NFS_SERVER='.AbtelBackup::getMyIp().'
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
        $this->Progression = 15;
        parent::Save();
        $act->addDetails($v->Titre.' ---> copy du script ghettoVCB');
        $esx->copyFile('ghettoVCB.sh');
        $act->addProgression(40);
        $this->Progression = 19;
        parent::Save();
        $act->addDetails($v->Titre.' ---> montage du NFS');
        $esx->remoteExec("esxcfg-nas -a ABTEL_BACKUP -o ".AbtelBackup::getMyIp()." -s /backup/nfs",null,true);
        $act->addProgression(10);
        $this->Progression = 20;
        parent::Save();
        return $act;
    }
    /**
     * cloneJob
     * Clonage de la vm
     */
    private function cloneJob($v,$esx,$act){
        $iProg = $this->Progression;
        $fProg = 45;
        $sProg = $fProg - $iProg;
        $progData = array( 'init' => $iProg, 'span' => $sProg, 'job' => $this);

        $this->setStep(3); //'Clonage'
        $act->addDetails($v->Titre.' ---> clonage de la vm');
        $esx->remoteExec('sh ghettoVCB.sh -m "' . $v->Titre . '" -g ghettovcb.conf',$act ,false ,$progData);
        $act->setProgression(100);
        $this->Progression = $fProg;
        parent::Save();
        return $act;
    }
    /**
     * compressJob
     * Compression de la vm
     */
    private function compressJob($v,$act){
        $iProg = $this->Progression;
        $fProg = 75;
        $sProg = $fProg - $iProg;
        $progData = array( 'init' => $iProg, 'span' => $sProg, 'job' => $this);

        $total = AbtelBackup::getSize('/backup/nfs/'.$v->Titre);
        $this->setStep(4); //'Compression'
        $act->addDetails($v->Titre.' ---> compression du clone TOTAL:'.$total);
        AbtelBackup::localExec("sudo bsdtar cSf '/backup/nfs/".$v->Titre.".tar' '/backup/nfs/".$v->Titre."/".$v->Titre."-A'",$act,$total,'/backup/nfs/'.$v->Titre.'.tar',$progData);
        $this->Progression = $fProg;
        parent::Save();
        $act->addProgression(100);
        return $act;
    }
    /**
     * deduplicateJob
     * Déduplication de la vm
     */
    private function deduplicateJob($v,$borg,$act){
        $iProg = $this->Progression;
        $fProg = 100;
        $sProg = $fProg - $iProg;
        $progData = array( 'init' => $iProg, 'span' => $sProg, 'job' => $this);

        $this->setStep(5); //'Déduplication'
        AbtelBackup::localExec('borg break-lock '.$borg->Path); //Supression des locks borg
        //AbtelBackup::localExec('borg delete --cache-only '.$borg->Path); //Supression du cache eventuellement corrompu
        AbtelBackup::localExec('sudo chown -R backup:backup '.$borg->Path.''); //On s'assure que les fichiers borg ne soient pas en root
        $total = AbtelBackup::getSize('/backup/nfs/'.$v->Titre.'.tar');
        $act->addDetails($v->Titre.' ---> TOTAL (Mo):'.$total);
        $act->addDetails($v->Titre.' ---> déduplication de la vm');
        //AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".time()." '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar'",$act);


        //Recup taille pour graphique/progression
        $v->Size = $total;

        $point = time();
        //file_put_contents('tototoottoto',"export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".$point." '/backup/nfs/".$v->Titre.".tar'");
        $det = AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".$point." '/backup/nfs/".$v->Titre.".tar'", $act, $total,null,$progData);

        //Recup taille pour graphique/progression
        $v->BackupSize = AbtelBackup::getSize($borg->Path);
        parent::Save();

        //création du point de restauration
        $v->createRestorePoint($point,$det);
        $act->setProgression(100);
        $act->addDetails($v->Titre.' ---> suppression archive');
        //AbtelBackup::localExec("if [ -f '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar; fi");
        AbtelBackup::localExec("if [ -f '/backup/nfs/".$v->Titre.".tar' ]; then sudo rm -f /backup/nfs/".$v->Titre.".tar; fi");
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

    private function clearAct($full = true){
        $acts = $this->getChildren('Activity/Started=1&Errors=0&Success=0');
        foreach($acts as $act){
            //print_r($act);
            if($full)
                $act->Errors = 1;

            $act->addDetails(' ---> Arrêt Utilisateur','cyan',true);
            //print_r($act);

        }
    }
}