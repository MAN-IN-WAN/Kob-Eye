<?php
class EmailProfile extends genericClass {

    /**
     * forceActive
     * Active toutes les boites liées à ce profil
     */
    public function forceActive() {
        $mails = $this->getChildren('CompteMail');
        foreach($mails as $mail){
            $mail->Status = 'active';
            $mail->Save();
        }
        $this->Locked = false;
        $this->Save();

        return true;
    }

    /**
     * forceActive
     * Désactive toutes les boites liées à ce profil
     */
    public function forceDesactive() {
        $mails = $this->getChildren('CompteMail');
        foreach($mails as $mail){
            $mail->Status = 'locked';
            $mail->Save();
        }
        $this->Locked = true;
        $this->Save();

        return true;
    }

    /**
     * checkDD
     * Vérifie le droit à la déconnexion
     */
    public static function checkDD(){
        $profiles = Sys::getData('Parc','EmailProfile/Enabled=1&DroitDeconnexion=1');
        $curDate = date('Y-m-d');
        $curTms = time();
        //file_put_contents('/tmp/DD','---------------------------------------'.PHP_EOL.$curTms.PHP_EOL,8);

        foreach ($profiles as $profile){
            $dateDebut = $curDate.' '.$profile->HeureDebut;
            $tmsDebut = strtotime($dateDebut);
            $dateFin = $curDate.' '.$profile->HeureFin;
            $tmsFin = strtotime($dateFin);
            file_put_contents('/tmp/DD',$tmsDebut.PHP_EOL,8);
            file_put_contents('/tmp/DD',$tmsFin.PHP_EOL,8);

            if($curTms >= $tmsFin && !$profile->Locked){
                file_put_contents('/tmp/DD','Deactivate'.PHP_EOL,8);
                $profile->forceDesactive();
            }
            if($curTms >= $tmsDebut && $curTms < $tmsFin && $profile->Locked){
                file_put_contents('/tmp/DD','Activate'.PHP_EOL,8);
                $profile->forceActive();
            }
        }
    }

}