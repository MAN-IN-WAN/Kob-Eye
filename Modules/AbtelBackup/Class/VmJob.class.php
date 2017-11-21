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
        if ($this->Running){
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
                        AbtelBackup::localExec('sudo killall -9 bsdtar');
                        $this->addSuccess(Array('Message'=>'Compression stoppée avec succès.'));
                    }else{
                        $this->addWarning(Array('Message'=>'Le processus n\'a pas été trouvé.'));
                    }
                    $this->Running = false;
                    parent::Save();
                    return true;
                break;
                case 5:
                    if (AbtelBackup::getPid('borg')) {
                        AbtelBackup::localExec('sudo killall -9 borg');
                        $this->addSuccess(Array('Message' => 'Déduplication stoppée avec succès.'));
                    }else{
                        $this->addWarning(Array('Message'=>'Le processus n\'a pas été trouvé.'));
                    }
                    $this->Running = false;
                    parent::Save();
                    return true;
                break;
                default:
                    $this->Running = false;
                    parent::Save();
                    $this->addSuccess(Array('Message' => 'Job stoppé avec succès.'));
                    return true;
                break;
            }
        }else{
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
            $act = $this->createActivity(' > Impossible de démarrer, lejob est déjà en cours d\'éxécution');
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
            $this->setCurrentVm($v->Id);
            $esx = $v->getOneParent('Esx');
            $borg = $v->getOneParent('BorgRepo');
            try {
                //nettoyage
                if (intval($this->Step)<=1)$act = $this->initJob($v,$esx);
                //configuration
                if (intval($this->Step)<=2)$act = $this->configJob($v,$esx);
                //clonage
                if (intval($this->Step)<=3)$act = $this->cloneJob($v,$esx);
                //compression
                if (intval($this->Step)<=4)$act = $this->compressJob($v);
                //déduplication
                if (intval($this->Step)<=5)$act = $this->deduplicateJob($v,$borg);
            }catch (Exception $e){
                $act->addDetails($v->Titre." ERROR => ".$e->getMessage(),'red');
                $act->Terminated = true;
                $act->Errors = true;
                $act->Save();
                //opération terminée
                $this->Running = false;
                $this->Errors = true;
                parent::Save();
            }
            //opération terminée
            $this->resetState();
        }
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
    private function initJob($v,$esx) {
        Klog::l('DEBUG Test INIT JOB');
        $this->setStep(1); //Initialisation
        $act = $this->createActivity($v->Titre.' > Nettoyage des archives',$v);
        $act->addDetails('Suppression du script ghettoVCB','yellow');
        //nettoyage sauvegarde précédentes
        $act->addDetails($v->Titre.' ---> suppression du script ghettoVCB');
        $esx->remoteExec("if [ -f /ghettoVCB.sh ]; then rm /ghettoVCB.sh; fi");
        $act->addProgression(15);
        $this->Progression = 5;
        parent::Save();
        $act->addDetails($v->Titre.' ---> suppression des snapshots');
        $esx->remoteExec("vim-cmd vmsvc/snapshot.removeall ".$v->RemoteId." && sleep 5");
        $act->addProgression(15);
        $act->addDetails($v->Titre.' ---> suppression du fichier .work');
        $esx->remoteExec("if [ -d '/tmp/ghettoVCB.work' ]; then rm -Rf '/tmp/ghettoVCB.work'; fi");
        $act->addProgression(15);
        $act->addDetails($v->Titre.' ---> suppression de la complete');
        //AbtelBackup::localExec("if [ -d '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre."' ]; then sudo rm -Rf '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre."'; fi");
        //$act->addProgression(15);
        AbtelBackup::localExec("if [ -d '/backup/nfs/".$v->Titre."' ]; then sudo rm -Rf '/backup/nfs/".$v->Titre."'; fi");
        $act->addProgression(30);
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
    private function configJob($v,$esx){
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
        $act = $this->createActivity($v->Titre.' > Configuration vmjob',$v);
        $act->addDetails('-> configuration vmjob','yellow');
        $act->addDetails($v->Titre.' ---> creation de la config ghettoVCB');
        $esx->remoteExec("echo '$config' > /ghettovcb.conf");
        $act->addProgression(50);
        $this->Progression = 15;
        parent::Save();
        $act->addDetails($v->Titre.' ---> copy du script ghettoVCB');
        $esx->copyFile('ghettoVCB.sh');
        $act->addProgression(40);
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
    private function cloneJob($v,$esx){
        $this->setStep(3); //'Clonage'
        $act = $this->createActivity($v->Titre.' > Clonage vmjob');
        $act->addDetails($v->Titre.' ---> clonage de la vm');
        $esx->remoteExec('sh ghettoVCB.sh -m "' . $v->Titre . '" -g ghettovcb.conf',$act);
        $act->setProgression(100);
        $this->Progression = 45;
        parent::Save();
        return $act;
    }
    /**
     * compressJob
     * Compression de la vm
     */
    private function compressJob($v){
        $total = AbtelBackup::getSize('/backup/nfs/'.$v->Titre);
        $this->setStep(4); //'Compression'
        $act = $this->createActivity($v->Titre.' > Compression vmjob',$v);
        $act->addDetails($v->Titre.' ---> compression du clone TOTAL:'.$total);
        AbtelBackup::localExec("sudo bsdtar cSf '/backup/nfs/".$v->Titre.".tar' '/backup/nfs/".$v->Titre."/".$v->Titre."-A'",$act,'tar');
        $this->Progression = 75;
        parent::Save();
        $act->addProgression(100);
        return $act;
    }
    /**
     * deduplicateJob
     * Déduplication de la vm
     */
    private function deduplicateJob($v,$borg){
        $this->setStep(5); //'Déduplication'
        $act = $this->createActivity($v->Titre.' > Déduplication vmjob TOTAL:'.$total,$v);
        $act->addDetails($v->Titre.' ---> déduplication de la vm');
        //AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".time()." '/backup/nfs/EsxVm/".$esx->IP."/".$v->Titre.".tar'",$act);
        $total = AbtelBackup::getFileSize('/backup/nfs/'.$v->Titre.'.tar');

        //Recup taille pour graphique/progression
        $this->Size = AbtelBackup::getSize('/backup/nfs/'.$v->Titre.'.tar');

        $point = time();
        $det = AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && borg create --progress --compression lz4 ".$borg->Path."::".$point." '/backup/nfs/".$v->Titre.".tar'",$act,$total,'borg');

        //Recup taille pour graphique/progression
        $this->BackupSize = AbtelBackup::getSize('/backup/borg/EsxVm/'.$v->Titre);
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
}