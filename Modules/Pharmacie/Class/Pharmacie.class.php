<?php
class Pharmacie extends Module
{
    /**
     * Consitution de la base claude bernard
     * @param string $path
     * @throws Exception
     */
    public static function synchroClaudeBernard($path = '../stock/easy.txt')
    {
        //categorie de base
        $basecat = Sys::getOneData('Boutique', 'Categorie/Id=1027');
        $tabcat = Array();

        //desactivation de la generation des mots clefs.
        Sys::disableKeywordsProcessing();

        //récupération du fichier
        if (!file_exists($path)) {
            throw new Exception('Impossible d\'ouvrir le fichier ' . $path);
        }
        $f = fopen($path, 'r');
        $i = 0;
        $j = 0;

        //reinitialisation des Produits actifs
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        $GLOBALS['Systeme']->Db[0]->query('UPDATE `' . MAIN_DB_PREFIX . 'Boutique-Produit` SET Actif=0, Display=0');

        while (!feof($f)) {
            //pour chaque ligne
            $l = utf8_encode(fgets($f));
            $l = explode(';', $l);

            //test intégrité
            if (sizeof($l) <= 40) continue;

            //on récupère la classification
            $BCB = trim($l[10]);
            $BCBnom = trim($l[37]);

            $tabcat[$BCB] = $BCBnom;
        }
        ksort($tabcat);
        print_r($tabcat);

        //création des catégories
        foreach ($tabcat as $BCB => $nom) {
            //on recherche si rla catégorie existe
            $cat = Sys::getOneData('Boutique', 'Categorie/*/Classification=' . $BCB);
            if (!$cat) {
                //alors recherche récursive
                $cat = Pharmacie::getRecursivClaudeBernard($BCB, $basecat, $tabcat);
            }
        }
    }

    static function getRecursivClaudeBernard($BCB, $catparent, $allcat, $level = 1)
    {
        //recherche de la catégorie parente
        if (!$catparent) throw new Exception('Categorie de base introuvable', 404);

        //on récupère la classification à recherche correspondante au niveau
        $cls = substr($BCB, 0, $level);

        //on vérifie l'existence
        $cat = Sys::getOneData('Boutique', 'Categorie/' . $catparent->Id . '/Categorie/Classification=' . $cls);
        if ($cat) {
            if (strlen($BCB) > $level)
                //ok donc on recherche au niveau 2
                return Pharmacie::getRecursivClaudeBernard($BCB, $cat, $allcat, $level + 1);
            else
                //on a trouvé on renvoi
                return $cat;
        } else {
            //la catégorie n'existe pas donc on la créée
            $cat = genericClass::createInstance('Boutique', 'Categorie');
            $cat->Classification = $cls;
            $cat->Nom = (isset($allcat[$cls])) ? $allcat[$cls] : 'CODE ' . $cls;
            $cat->addParent($catparent);
            $cat->Save();
            echo "$level | $cls creation cat $cls => " . $cat->Nom . "\r\n";
            if (strlen($BCB) > $level)
                return Pharmacie::getRecursivClaudeBernard($BCB, $cat, $allcat, $level + 1);
            else return $cat;
        }
    }

    /**
     * analyse du fcihier de stock en export de periphar.
     * Mise à jour des tarifs et des stocks.
     * Array
     * (
     * [0] => CIP 7
     * [1] => CIP 13
     * [2] => nom
     * [3] => Qt stock
     * [4] => PVte TTC
     * [5] => PA rep HT
     * [6] => PA der cmde HT
     * [7] => TVA
     * [8] => Date der vte
     * [9] => Laboratoire
     * [10] => Famille gestion
     * [11] => Famille gestion2
     * [12] => QVM mois -24
     * [13] => QVM mois -23
     * [14] => QVM mois -22
     * [15] => QVM mois -21
     * [16] => QVM mois -20
     * [17] => QVM mois -19
     * [18] => QVM mois -18
     * [19] => QVM mois -17
     * [20] => QVM mois -16
     * [21] => QVM mois -15
     * [22] => QVM mois -14
     * [23] => QVM mois -13
     * [24] => QVM mois -12
     * [25] => QVM mois -11
     * [26] => QVM mois -10
     * [27] => QVM mois -9
     * [28] => QVM mois -8
     * [29] => QVM mois -7
     * [30] => QVM mois -6
     * [31] => QVM mois -5
     * [32] => QVM mois -4
     * [33] => QVM mois -3
     * [34] => QVM mois -2
     * [35] => QVM mois -1
     * [36] => Total QVM
     * [37] => Libell FamG1
     * [38] => Libell FamG2
     * [39] => EAN13
     * [40] =>
     *
     * )
     */
    public static function synchroPeriphar($path = '../stock/easy.txt')
    {
        //desactivation de la generation des mots clefs.
        Sys::disableKeywordsProcessing();

        //récupération du fichier
        if (!file_exists($path)) {
            throw new Exception('Impossible d\'ouvrir le fichier ' . $path);
        }
        $f = fopen($path, 'r');
        $i = 0;
        $j = 0;

        //reinitialisation des Produits actifs
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        $GLOBALS['Systeme']->Db[0]->query('UPDATE `' . MAIN_DB_PREFIX . 'Boutique-Produit` SET Actif=0, Display=0');

        while (!feof($f)) {
            //pour chaque ligne
            $l = utf8_encode(fgets($f));
            $l = explode(';', $l);

            //test intégrité
            if (sizeof($l) <= 40) continue;

            //recherche du produit en base
            $query = '';
            $EAN = trim($l[39]);
            //3282 779 047 241
            if (strlen($EAN) == 13) {
                if (!empty($query)) $query .= '+';
                $query .= 'EAN=' . $EAN;
            }
            $CIP7 = trim($l[0]);
            if (strlen($CIP7) == 7) {
                if (!empty($query)) $query .= '+';
                $query .= 'CIP7=' . $CIP7;
            }
            $CIP13 = trim($l[1]);
            if (strlen($CIP13) == 13) {
                if (!empty($query)) $query .= '+';
                $query .= 'CIP13=' . $CIP13;
            }
            if (empty($query)) continue;
            $p = Sys::getOneData('Boutique', 'Produit/' . $query);
            if (is_object($p)) {

                //mise à jour des codes produit
                if (empty($p->EAN)) $p->EAN = $EAN;
                if (empty($p->CIP13)) $p->CIP13 = $CIP13;
                if (empty($p->CIP7)) $p->CIP7 = $CIP7;

                //mise à jour de la classification
                $BCB = trim($l[10]);
                $p->Classification = $BCB;

                //on recherche la cétegorie correspondate
                if (empty($p->Classification)) {
                    $cat = Sys::getOneData('Boutique', 'Categorie/*/Classification=' . $BCB);
                    if ($cat) {
                        $p->AddParent($cat);
                    }
                }

                //mise à jour des tarifs
                $TTC = trim($l[4]);
                $TTC = floatval(str_replace(',', '.', $TTC));

                echo "-> $i produit stock: " . $l[3] . " Nom " . $p->Nom . " tarif en ligne: " . $p->Tarif . " TVA: " . trim($l[7]) . " tarif : " . $TTC . " ean: " . $p->EAN . " cip: " . $p->CIP7 . " cip13: " . $p->CIP13 . " ";

                //mise à jour de la TVA
                $TVA = trim($l[7]);
                if ($TVA == "20,00") {
                    $HT = $TTC / 1.20;
                    $p->TypeTva = 1;
                } elseif ($TVA == "5,50") {
                    $HT = $TTC / 1.055;
                    $p->TypeTva = 2;
                } elseif ($TVA == "10,00") {
                    $HT = $TTC / 1.10;
                    $p->TypeTva = 3;
                } elseif ($TVA == "2,10") {
                    $HT = $TTC / 1.021;
                    $p->TypeTva = 4;
                }
                if ($p->Tarif != $HT)
                    $p->setPriceForce($HT);

                //mise à jour des stocks
                $ref = Sys::getOneData('Boutique', 'Produit/' . $p->Id . '/Reference');
                $qte = intval(trim($l[3]));
                if ($qte > 0) {
                    $p->Display = true;
                    $p->Actif = true;
                    $p->StockReference = $qte;
                    $ref->Quantite = $qte;
                    $ref->Actif = true;
                    $ref->Save(false);
                } else {
                    $p->StockReference = 0;
                    $ref->Quantite = 0;//$qte;
                    $ref->Actif = false;
                    $ref->Save(false);

                    //on désactive le produit
                    $p->Display = 0;
                    $p->Actif = 0;
                }

                //vérification de l'existence de la marque
                $lab = trim($l[9]);
                $ma = Sys::getOneData('Boutique', 'Marque/Nom=' . Utils::KEAddSlashes(Array($lab)));
                if (is_object($ma)) {
                    //la marque existe on vérifie qu'elle est bien liée au produit
                    if (!Sys::getCount('Boutique', 'Marque/' . $ma->Id . '/Produit/' . $p->Id)) {
                        $p->addParent($ma);
                    }
                } else {
                    //il faut créer la marque
                    $ma = genericClass::createInstance('Boutique', 'Marque');
                    $ma->Nom = $lab;
                    $ma->Save();
                    $p->addParent($ma);
                }

                //sauvegarde du produit
                $p->Save(false);

                echo "OK \r\n";

                //mise à jour du nombre de produit
                //if ($qte>0)
                $i++;
            } else $j++;

            //retour anticipé
            /*if ($i>=1){
                break;
            }*/
        }
        echo "$i produits trouvés et $j produits non trouvés";
    }

    public static function registerDevice($KEY)
    {
        //on vérifie l'existence de l'appareil
        if (!Sys::getCount('Pharmacie', 'Device/Key=' . $KEY)) {
            //on ajoute le device
            $d = genericClass::createinstance('Pharmacie', 'Device');
            $d->Set('Key', $KEY);
            $d->Save();
        }
    }

    public static function testNotification()
    {
        // API access key from Google API's Console
        define('API_ACCESS_KEY', 'AIzaSyCGGUR9EbkicdM7IUXp1l-Z2sHFQCnLp-A');

        //recherche des périphériques à associer.
        $dev = Sys::getData('Pharmacie','Device');
        $registrationIds = array();
        foreach ($dev as $d){
            $registrationIds[] = $d->Key;
        }

        // prep the bundle
        $msg = array
        (
            'message' => 'here is a message. message',
            'title' => 'This is a title. title',
            'subtitle' => 'This is a subtitle. subtitle',
            'tickerText' => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate' => 1,
            'sound' => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );
        $fields = array
        (
            'registration_ids' => $registrationIds,
            'data' => $msg
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        echo 'send message';
        print_r($msg);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
    }
}