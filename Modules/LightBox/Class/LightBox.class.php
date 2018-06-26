<?php
class LightBox extends Module{
    static $currentApn=null;
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
     * Initilisation des variables globales disponibles pour la boutique
     */
    private function initGlobalVars(){

    }
    /**
     * UTILS FUNCTIONS
     */
    static public function localExec( $command, $activity = null,$total=0,$path=null)
    {
        /*exec( $command,$output,$return);
        if( $return ) {
            throw new RuntimeException( "L'éxécution de la commande locale a échoué. commande : ".$command." \n ".print_r($output,true));
        }
        return implode("\n",$output);*/
        $proc = popen($command.' 2>&1 ; echo Exit status : $?', 'r');
        $complete_output = "";
        if ($path && is_file($path) && is_readable($path)){
            //On fork le process pour calculer le progress en parallele
            switch ($pid = pcntl_fork()) {
                case -1:
                    // @fail
                    $activity->addDetails('No Fork , No Progress');
                    break;

                case 0:
                    // @child: Include() misbehaving code here
                    while (!feof($proc)) {
                        $size = AbtelBackup::getSize($path);
                        $progress = floatval($size)*100/$total;
                        $progress = intval($progress);
                        if ($progress != $activity->Progression){
                            $activity->setProgression($progress);
                        }

                        sleep(5);
                    }
                    exit;
                    break;

                default:
                    // @parent
                    break;
            }
        }

        while (!feof($proc)){
            $buf     = fread($proc, 4096);
            $progress = 0;

            //cas borg
            if (preg_match('#O ([0-9\.]+)? MB C#',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1]))/$total;
                $buf = '';
            }
            //347.08 GB O 285.33 GB C 212.73 G
            if (preg_match('#O ([0-9\.]+)? GB C#',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1])*1024)/$total;
                $buf = '';
            }
            if (preg_match('#O ([0-9\.]+)? TB C#',$buf,$out)&&$activity&&$total) {
                $progress = (floatval($out[1])*1048576)/$total;
                $buf = '';
            }
            //cas rsync
            if (preg_match('#([0-9]+)?%#',$buf,$out)&&$activity) {
                $progress = intval($out[1])/100;
                $buf = '';
            }
            if($progress&&intval($progress*100)!=$activity->Progression){
                $activity->setProgression($progress*100);
            }


            $complete_output .= $buf;
        }


        if($path){
            //On tue le fork pour eviter les process zombies
            if($pid > 0){
                posix_kill ( $pid , SIGKILL );
                //Si le fork a marché on attend la mort de l'enfant
                pcntl_waitpid($pid, $status);
            }
        }

        pclose($proc);
        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        if( $matches[0] !== "0") {
            throw new RuntimeException( $complete_output, (int)$matches[0] );
        }
        return str_replace("Exit status : " . $matches[0], '', $complete_output);
    }

    static public function getPid($prog){
        try {
            $output = LightBox::localExec('sudo pgrep ' . $prog);
        }catch (Exception $e){
            return false;
        }
        return $output;
    }
    static public function getUptime(){
        try {
            $output = LightBox::localExec('uptime -s');
        }catch (Exception $e){
            return false;
        }
        $tms = strtotime($output);
        return $tms;
    }
    static public function getMyIp(){
        $output = LightBox::localExec('/sbin/ifconfig');
        preg_match('#inet ([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)#',$output,$out);
        return $out[1];
    }
    static function getFileSize($path){
        $output = LightBox::localExec('/usr/bin/ls -l "'.$path.'"');
        preg_match('#^[rwx-]+ [0-9]{1} [^ ]+ [^ ]+ ([0-9]+)#',$output,$out);
        return $out[1];
    }
    static public function checkPort($ip,$port){
        $time = 2;
        $connection = @fsockopen($ip, $port,$errno, $errstr, $time);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }else{
            return false;
        }
    }

    static public function isUsbAvailable() {
        //$output = LightBox::localExec('if [ -e /dev/sdb ]; then  echo 1; else echo 0; fi');
        $output = LightBox::localExec('ls -l /dev/sd* | tail -n 1 | awk \'{print $NF}\' | wc -l');
        error_log('is usb available '.intval($output));
        if (intval($output)) return true;
        else return false;
    }
    static public function mountUsb() {
        try {
            //@LightBox::localExec('sudo umount /home/lightbox/usb');
            //sleep(2);
            $drive = LightBox::localExec('ls -l /dev/sd* | tail -n 1 | awk \'{print $NF}\'');
            $drive = trim($drive);
            error_log('drive found '.$drive);
        }catch (Exception $e){
            return false;
        }
        try {
            error_log('executing '.'sudo  mount '.$drive.' /home/lightbox/usb');
            LightBox::localExec('sudo bash -c "source ~/.bashrc && mount -tvfat -o rw,users,umask=000 '.$drive.' /home/lightbox/usb"');
        }catch (Exception $e){
            error_log('error while mouting '.$e->getMessage());
            return false;
        }
        error_log('/home/lightbox/usb mounted successfully. '.LightBox::localExec('df -h | grep usb'));
        return true;
    }
    static public function getFreeMem() {
        $output = LightBox::localExec('cat /proc/meminfo | grep MemFree | awk \'{ print $2 }\'');
        return intval($output);
    }
    static public function emptyCache() {
        LightBox::localExec('sudo rm -rf /home/kiosk/.{config,cache}/google-chrome/');
        return true;
    }
    static public function restartBrowser() {
        LightBox::localExec('sudo killall -9 chrome && sudo /opt/kiosk.sh');
        return true;
    }

    static function getUsedSpace() {
        $output = LightBox::localExec('df -h | grep lightbox | awk \'{print $5 }\' | sed -e \'s/%//\'');
        return intval($output);
    }
}