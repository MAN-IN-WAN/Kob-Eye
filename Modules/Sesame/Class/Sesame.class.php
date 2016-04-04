<?php
class Sesame extends Module {
    /**
     * @param $qr
     * format de la chaine d'ouverture
     * NOM_CHAINE;NOM_CLIENT;EMPLACEMENT;TMS_DEBUT;TMS_FIN
     * ex: HIP;aaa;001;1454320497;1480586097
     */
    static function checkQrCode ($qr) {
        klog::l('decryptage '.$qr);

        $h = genericClass::createInstance('Sesame', 'QrCode');
        $h->QrCode = $qr;

        if (empty($qr)){
            $h->Resultat = 'La chaine est vide';
            $h->Save();
            return;
        }

        //sinon on décrypte le code pour valider la chaîne
        $key = Sys::getOneData('Sesame', 'Dictionnaire/Nom=AES_KEY');
        // test ZBCKbyX6RgJA8wRTbE5a4SQeVv3ccP0ISng3iEry3qU=  ====> test de phrase ===> passe partout
        // test ov4dxzJAqeWtyMKWKCHuOHjImA2mV+7opAIm8jJ99kmZu5XdnotMb03BbxO6qwe/tcIXRJ30MMzOobVRqmH3rA== => MICHEL MANOLIOS;15122015;12:00;18122016;12:00;SB125;048 => chaine valide
        //$qr2 = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key->Valeur,'MICHEL MANOLIOS;15122015;12:00;18122016;12:00;SB125;048',MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"));
        //echo $qr2."<br />\r\n";
        //echo base64_encode(base64_decode($qr))."<br />\r\n";
        //die('zob');
        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key->Valeur, base64_decode($qr), MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
        $str = $h->Decode = trim($str);


        //on vérifie le passe partout
        if (Sys::getCount('Sesame','PassePartout/Code='.$qr)){
            $cc = Sys::getOneData('Sesame','PassePartout/Code='.$qr);
            $h->Decode = $qr;
            $h->Resultat = 'Ouverture passe partout '.(($cc->CodeDirecteur) ? ' avec un code de cloture': '');
            if ($cc->CodeDirecteur)
                Sesame::closeCurrentSejour();
            else
                Sesame::Ouverture();
        }else {

            $t = explode(';', $str);
            klog::l('decryptage ' . $str, $t);

            if (sizeof($t) >= 5) {
                //[0] = > NOM CLIENT
                //[1] = > DATE DEBUT
                //[2] = > HEURE DEBUT
                //[3] = > DATE FIN
                //[4] = > HEURE FIN
                //[5] = > NUM BOITIER
                //[6] = > EMPLACEMENT

                //on vérifie la chaine
                $emp = Sys::getOneData('Sesame', 'Dictionnaire/Nom=EMPLACEMENT');
                $idb = Sys::getOneData('Sesame', 'Dictionnaire/Nom=ID_BOITIER');

                //calcul des timestamp sebut et fin
                $tmsdeb = mktime(substr($t[2],0,2),substr($t[2],3,2),0,substr($t[1],2,2),substr($t[1],0,2),substr($t[1],4,4));
                $tmsfin = mktime(substr($t[4],0,2),substr($t[4],3,2),0,substr($t[3],2,2),substr($t[3],0,2),substr($t[3],4,4));

                if ($idb->Valeur == $t[5] && (string)$emp->Valeur == (string)$t[6] && $tmsdeb < time() && $tmsfin > time()) {
                    klog::l('OUVERTURE PORTE !!');
                    $h->Resultat = 'Ouverture classique';
                    Sesame::CreateSejour($tmsdeb,$tmsfin,$t);
                } elseif ($idb->Valeur != $t[5]) {
                    klog::l('ERROR >> ID_BOITIER INCORRECTE ');
                    $h->Resultat = 'ERROR >> ID_BOITIER INCORRECTE ';
                } elseif ((string)$emp->Valeur != (string)$t[6]) {
                    klog::l('ERROR >> EMPLACEMENT INCORRECT'.$emp->Valeur.'!='.$t[6]);
                    $h->Resultat = 'ERROR >> EMPLACEMENT INCORRECT '.$emp->Valeur.'!='.$t[6];
                } else {
                    klog::l('ERROR >> DATES INCORRECTES');
                    $h->Resultat = 'ERROR >> DATES INCORRECT '.$tmsdeb.' '.$tmsfin;
                }
            } else $h->Resultat = 'Impossible de décoder la chaine';
        }
        $h->Save();
        echo $h->Resultat;
    }
    static function CreateSejour ($datedeb,$datefin,$all) {
        exec('/usr/local/bin/ouverturePermanente > /dev/null 2>/dev/null &');
        $sej = genericclass::createInstance('Sesame','Sejour');
        $sej->Nom = 'Séjour du '.date('d/m/Y H:i:s',$datedeb).' au '.date('d/m/Y H:i:s',$datefin);
        $sej->DateDebut = $datedeb;
        $sej->DateFin = $datefin;
        $sej->Actif = true;

        if ($cs = Sesame::getCurrentSejour()){
            $cs->Actif = 0;
            $cs->Save();
        }
        $sej->Save();
        Sesame::Ouverture();
    }
    static function OuvertureFermeture () {
        exec('/usr/local/bin/ouvreetferme > /dev/null 2>/dev/null &');

    }
    static function Ouverture () {
        exec('/usr/local/bin/ouverture > /dev/null 2>/dev/null &');

    }
    static function Fermeture () {
        exec('/usr/local/bin/fermeture > /dev/null 2>/dev/null &');
    }

    public static function getCurrentSejour(){
        return Sys::getOneData('Sesame','Sejour/Actif=1');
    }

    public static function checkSejour(){
        $cs = Sesame::getCurrentSejour();
        if ($cs)
            if ($cs->DateDebut < time() && $cs->DateFin > time()) {
                echo 'Porte ouverte';
                Sesame::Ouverture();
            }else {
                echo 'Fin du sejour - Fermeture';
                Sesame::closeCurrentSejour();
            }
        else {
            echo 'Porte fermée';
            Sesame::Fermeture();
        }
    }

    public static function closeCurrentSejour(){
        $cs = Sesame::getCurrentSejour();
        $cs->Actif = 0;
        $cs->Save();
        Sesame::Fermeture();
    }

    function Check() {
        parent::Check();
        // Vérification de l'existence d'un pass partout par Defaut
        if (!Sys::getCount('Sesame','PassePartout')) {
            $D=genericClass::createInstance('Sesame','PassePartout');
            $D->Code = 'ne pas oublier de saisir les avis';
            $D->Save();
        }
        // Vérification de l'existence d'une clef par Defaut
        if (!Sys::getCount('Sesame','Dictionnaire')) {
            $D=genericClass::createInstance('Sesame','Dictionnaire');
            $D->Nom = 'EMPLACEMENT';
            $D->Valeur = '048';
            $D->Save();

            $D=genericClass::createInstance('Sesame','Dictionnaire');
            $D->Nom = 'AES_KEY';
            $D->Valeur = 'HipVillagesUntempsd;avance';
            $D->Save();

            $D=genericClass::createInstance('Sesame','Dictionnaire');
            $D->Nom = 'ID_BOITIER';
            $D->Valeur = 'SB125';
            $D->Save();
        }
    }
}