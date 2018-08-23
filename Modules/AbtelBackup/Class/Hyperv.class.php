<?php

/**
 * Class Hyperv
 * pour reproduire l'encodage base64 de power shell , il faut convertir la chaine en UTF-16
 * ex:
 * echo -n "zob"| iconv -f UTF8 -t UTF16LE | base64 -w 0
 *
 */

class Hyperv extends genericClass {
    function Save(){
        parent::Save();
        //getInventory();
        $this->getinventory();
        return true;
    }
    /**
     * getInventory
     * Récupère l'inventaire des vms à la sauce bastien.
     */
    public function getinventory() {
        $output = Hyperv::execPowershell('/var/www/html/Modules/AbtelBackup/PowerShell/ListingVm.ps1',$this->Login,$this->Password,$this->IP);
        $vms = explode("\r",$output);
        foreach ($vms as $vm){
            $nb = Sys::getCount('AbtelBackup','HypervVm/Titre='.$vm);
            if ($nb>0) continue;
            $v = genericClass::createInstance('AbtelBackup','HypervVm');
            $v->Titre = $vm;
            $v->addParent($this);
            $v->Save();
        }

    }

    /**
     * execPowershellScript
     * Encode un fichier powershell en base64 et UTF16 afin d'etre executer à distance par PSExec
     * @param string path
     * @param string user
     * @param string mdp
     * @param string ip
     */
    public static function execPowershell($path,$user,$mdp,$ip){
        $cmd = 'python /var/www/html/Modules/AbtelBackup/PSExec.py \''.$user.':'.$mdp.'@'.$ip.'\' "cmd /c powershell -EncodedCommand \"$(cat '.$path.' | iconv -f UTF8 -t UTF16LE | base64 -w 0)\""';
        $output = AbtelBackup::localExec($cmd, null, 0, null, true);
        $output = str_replace("\n","####!<>!####",$output);
        preg_match('#commands(.*?)\<Objs#',$output,$out);
        $output = str_replace("####!<>!####","\r",$out[1]);
        return trim($output);
    }
}