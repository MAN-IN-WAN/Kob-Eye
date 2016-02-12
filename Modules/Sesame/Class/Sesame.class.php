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
        // test uA4mgVJcJGAo2AYWri89IjrJ5sRjlk0NESPYfvDsjyYKYflrJyT9NR2fzu6JaKmM0ouoeF2n8atbn/i3v4DKzA== => HIP;aaa;001;1454320497;1480586097 => chaine valide
        //$qr = base64_encode(mcrypt_encrypt('rijndael-256',$key->Valeur,'HIP;aaa;001;1454320497;1480586097','cbc', "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"));
        $str = mcrypt_decrypt('rijndael-256', $key->Valeur, base64_decode($qr), 'cbc', "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");

        $h->Decode = $str;


        //on vérifie le passe partout
        if (Sys::getCount('Sesame','PassePartout/Code='.$qr)){
            klog::l('OUVERTURE PASSE PARTOUT');
            $h->Resultat = 'Ouverture passe partout';
            Sesame::Ouverture();
        }else {

            $t = explode(';', $str);
            klog::l('decryptage ' . $str, $t);

            if (sizeof($t) >= 5) {
                //on vérifie la chaine
                $emp = Sys::getOneData('Sesame', 'Dictionnaire/Nom=EMPLACEMENT');
                $cli = Sys::getOneData('Sesame', 'Dictionnaire/Nom=NOM_CLIENT');
                $cha = Sys::getOneData('Sesame', 'Dictionnaire/Nom=NOM_CHAINE');

                if ($cha->Valeur == $t[0] && $cli->Valeur == $t[1] && $emp->Valeur == $t[2] && $t[3] < time() && $t[4] > time()) {
                    klog::l('OUVERTURE PORTE !!');
                    $h->Resultat = 'Ouverture classique';
                    Sesame::Ouverture();
                } elseif ($cha->Valeur != $t[0]) {
                    klog::l('ERROR >> NOM_CHAINE INCORRECTE ');
                    $h->Resultat = 'ERROR >> NOM_CHAINE INCORRECTE ';
                } elseif ($cli->Valeur != $t[1]) {
                    klog::l('ERROR >> CLIENT INCORRECT');
                    $h->Resultat = 'ERROR >> CLIENT INCORRECT';
                } elseif ($emp->Valeur != $t[2]) {
                    klog::l('ERROR >> EMPLACEMENT INCORRECT');
                    $h->Resultat = 'ERROR >> EMPLACEMENT INCORRECT';
                } else {
                    klog::l('ERROR >> DATES INCORRECTES');
                    $h->Resultat = 'ERROR >> DATES INCORRECT';
                }
            } else $h->Resultat = 'Impossible de décoder la chaine';
        }
        $h->Save();

    }
    static function Ouverture () {
        exec('/usr/local/bin/ouverture > /dev/null 2>/dev/null &');

    }
    static function Fermeture () {
        exec('/usr/local/bin/fermeture > /dev/null 2>/dev/null &');
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
            $D->Valeur = '003';
            $D->Save();

            $D=genericClass::createInstance('Sesame','Dictionnaire');
            $D->Nom = 'AES_KEY';
            $D->Valeur = 'HipVillagesUntempsd;avance';
            $D->Save();

            $D=genericClass::createInstance('Sesame','Dictionnaire');
            $D->Nom = 'NOM_CHAINE';
            $D->Valeur = 'HIP';
            $D->Save();

            $D=genericClass::createInstance('Sesame','Dictionnaire');
            $D->Nom = 'NOM_CLIENT';
            $D->Valeur = 'aaa';
            $D->Save();
        }
    }
}