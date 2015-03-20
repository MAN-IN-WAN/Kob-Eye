<?php
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * @property mixed TypeProduit
 */
class BoutiqueProduit extends genericClass
{
    var $_getUrl;

    /**
     * Enregistrement d'un produit
     * -> Check Reference
     * -> Enregistre les mots clés
     * @return    void
     */
    public function Save($recurs = true)
    {
        parent::Save();
        $this->SaveRef();
        // ajout de ce test car une carte configurable n'a qu'un ta
        if ($this->TypeProduit != '5') $this->UpdateStartPrice();
        $this->UpdateReferences();
        //test image
        if (empty($this->Image))
            $this->Image = 'Skins/AdminV2/Img/default.jpg';
        parent::Save();
    }

    /**
     * Suppression propre du produit
     */
    public function Delete()
    {
        $refs = $this->getChildren('Reference');
        foreach ($refs as $r) $r->Delete();
        $donnees = $this->getChildren('Donnee');
        foreach ($donnees as $d) $d->Delete();
        $cpks = $this->getChildren('ConfigPack');
        foreach ($cpks as $cpk) $cpk->Delete();
        parent::Delete();
    }

    public function genererReferences() {
        //si le produit possède des références alors il faut faire une boucle
        if (Sys::getCount('Boutique','Produit/'.$this->Id.'/Attribut')){
            //génération des attributs et declinaisons
            $attrs = $this->getChildren('Attribut');
            $refs = Array();
            $first = 1;
            for ($i=0;$i<sizeof($attrs);$i++){
                //klog::l('- attribut '.$attrs[$i]->Nom);
                $attrs[$i]->Declinaisons = $attrs[$i]->getChildren('Declinaison');
                $beginrefs = $refs;
                $refs = Array();
                for ($j=0; $j<sizeof($attrs[$i]->Declinaisons);$j++){
                    //klog::l('- declinaison '.$attrs[$i]->Declinaisons[$j]->Nom);
                    if ($first) {
                        $o = genericClass::createInstance('Boutique','Reference');
                        //$o->Reference = $this->Reference . "-" . $attrs[$i]->Code .  "-" . $attrs[$i]->Declinaisons[$j]->Code;
                        $o->Reference = $this->Reference . "-" . $this->Id . "-" . $attrs[$i]->Id .  "-" . $attrs[$i]->Declinaisons[$j]->Id;
                        $o->Actif =1;
                        $o->StockPermanent =1;
                        $o->AddParent($this);
                        $o->AddParent($attrs[$i]->Declinaisons[$j]);
                        $refs[] = $o;
                    }else{
                        $tmpref = unserialize(serialize($beginrefs));
                        //a chaque declinaison on clone le tableau d'origine
                        for ($k=0; $k<sizeof($tmpref);$k++){
//							$tmpref[$k]->Reference.= "-" . $attrs[$i]->Code .  "-" . $attrs[$i]->Declinaisons[$j]->Code;
                            $tmpref[$k]->Reference.= "-" . $attrs[$i]->Id .  "-" . $attrs[$i]->Declinaisons[$j]->Id;
                            $tmpref[$k]->AddParent($attrs[$i]->Declinaisons[$j]);
                            $refs[] = $tmpref[$k];
                        }
                    }
                }
                $first=0;
            }
            //génération des références
            //klog::l('*********génération reference*********');
            //foreach ($refs as $ref){
            //	klog::l('   - '.$ref->Reference);
            //}
            //recherche des références existantes
            $origrefs = $this->getChildren('Reference');
            //on désactive toutes les références par défaut
            for ($i=0;$i<sizeof($origrefs);$i++){
                $origrefs[$i]->Actif = 0;
            }
            //on vérifie l'existence de chaque référence
            // - on active quand ca correspond.
            // - on ajoute quand ca n'existe pas.
            //klog::l('*********comparaison reference*********');
            for ($i=0;$i<sizeof($refs);$i++){
                $exists = false;
                for ($j=0;$j<sizeof($origrefs);$j++){
                    if ($refs[$i]->Reference==$origrefs[$j]->Reference){
                        //si existe on active
                        //klog::l('   - on active '.$origrefs[$j]->Reference);
                        $exists = true;
                        $origrefs[$j]->Actif = 1;
                    }
                }
                if (!$exists){
                    //ajout de la référence
                    $refs[$i]->Actif = true;
                    $origrefs[] = $refs[$i];
                    //klog::l('   - on ajoute '.$ref->Reference);
                }
            }
            //on sauvegarde toutes les références
            //klog::l('*********sauvegarde reference*********');
            for ($i=0;$i<sizeof($origrefs);$i++){
                //klog::l('   - on sauvegarde '.$origrefs[$i]->Reference.' Actif: '.$origrefs[$i]->Actif);
                $origrefs[$i]->Save();
            }
            return true;
        }else{
            if (!Sys::getCount('Boutique','Produit/'.$this->Id.'/Reference')){
                //génération de la première référence
                $o = genericClass::createInstance('Boutique','Reference');
                $o->Reference = $this->Reference;
                $o->AddParent($this);
                $o->Save();
                //klog::l('génération reference');
            }else{
                //vérification des références
            }
        }
    }
    /**
     * Création d'une référence si c'est un nouveau produit
     * @return    void
     */
    private function SaveRef()
    {
        if (empty($this->Reference)) {
            $ref = $this->Id;
            while (strlen($ref) < 5) $ref = '0' . $ref;;
            $this->Reference = 'PR' . $ref;
        }
    }

    public function initFromReference($Ref)
    {
        $references = Sys::getData('Boutique', 'Reference/Reference=' . $Ref);
        if (is_array($references) && sizeof($references)) {
            $refer = $references[0];
            return $refer->getProd();
        }
    }

    /**
     * Garde le prix minimum trouvé (mais non vide) dans les références
     * @return    void
     */
    private function UpdateStartPrice()
    {
        $minPrice = -1;
        $tarifs = array();
        // Récupération de tous les tarifs
        $refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference');
        if (is_array($refs)) foreach ($refs as $r) {
            if ($r['Tarif'] > 0) {
                $prix = $r['Tarif'];
                if ($minPrice == -1 || $prix < $minPrice) $minPrice = $prix;
                $tarifs[$prix] = 1;
            }
            // Affectation du prix minimum trouvé
            if ($minPrice != -1) $this->Tarif = $minPrice;
            // Il y a plusieurs tarifs pour ce produit
            $this->MultiTarif = (sizeof($tarifs) > 1) ? 1 : 0;
        }
        //dans le cas ou il s'agit dun produit configurable et que un configpack possède une variation de prix, il faut afficher le MultiTarif
        $cps = $this->getChildren('ConfigPack/TarifPack=1');
        if (sizeof($cps) > 0) $this->MultiTarif = 1;
    }

    /**
     * Affecte le prix du produit (si non vide) aux références si leur prix est vide
     * Garde le prix minimum trouvé dans les références
     * Cumul des stocks des références sous jacentes dans le champs StockReference
     * @return    void
     */
    private function UpdateReferences()
    {
        $this->StockReference = 0;
        $refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference');
        if (is_array($refs)) foreach ($refs as $r) {
            $reference = genericClass::createInstance('Boutique', $r);
            if ($reference->Tarif == 0 && $this->Tarif > 0) {
                $reference->Tarif = $this->Tarif;
                if ($this->TarifPack = 0 || $this->TarifPack = '') $reference->TarifPack = $this->Tarif;
                $reference->Save(false);
            }
            $this->StockReference += $reference->Quantite;
        }
    }

    /**
     * Retourne l'url depuis la racine d'un produit donné
     * @return    URL
     */
    public function getUrl()
    {
        if (Sys::$User->Public) {
            return parent::getUrl();
        } elseif (!Sys::$User->Admin) {
            if (isset($this->_getUrl)&&!empty($this->_getUrl)) return '/'.$this->_getUrl;
            //recherche des categorie
            $cat = $this->storproc('Boutique/Categorie/*/Categorie/Produit/'.$this->Id);
            //on verifie qu'il n'y pas de menu sur chacune des categories
            $M = false;
            $lastCat = -1;
            if (!isset($this->Url)) return false;
            $U = 'Produit/' . $this->Url;
            if (is_array($cat)) foreach ($cat as $c) {
                if ('Boutique/Categorie/' . $c["Id"] != $GLOBALS["Systeme"]->getMenu('Boutique/Categorie/' . $c["Id"])) {
                    $this->_getUrl = $GLOBALS["Systeme"]->getMenu('Boutique/Categorie/' . $c["Id"]) . '/' . $U;
                    return '/'.$this->_getUrl;
                } elseif ($lastCat == -1 || ($lastCat['Bg'] > $c['Bg'] && $lastCat['Bd'] < $c['Bd'])) {
                    $U = $c["Url"] . '/' . $U;
                    $lastCat = $c;
                }
            }
//            $U = 'Categorie/' . $U;
            //recherche du magasin
            if (!isset($c)) return false;
            $mag = Magasin::getCurrentMagasin();

            if (is_object($mag) && 'Boutique/Magasin/' . $mag->Id != $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id . '/Categorie')) {
                $this->_getUrl = $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id.'/Categorie') . '/' . $U;
                return '/'.$this->_getUrl;
            }
            //recherche du magasin plus la categorie
            $Uc = 'Produit/' . $this->Url;
            $C = '/Categorie';
            if (is_array($cat)) for ($i = sizeof($cat) - 1; $i >= 0; $i--) {
                $C .= '/' . $cat[$i]["Id"];
                if (is_object($mag) && 'Boutique/Magasin/' . $mag->Id . $C != $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id . $C)) {
                    $this->_getUrl = $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id . $C) . '/Categorie';
                    for ($j = $i - 1; $j >= 0; $j--) $this->_getUrl .= '/' . $cat[$j]["Url"];
                    $this->_getUrl .= '/' . $Uc;
                    return '/'.$this->_getUrl;
                }
                //recherche du magasin plus la categorie
                $Uc = 'Produit/' . $this->Url;
                $C = '/Categorie';
                if (is_array($cat)) for ($i = sizeof($cat) - 1; $i >= 0; $i--) {
                    $C .= '/' . $cat[$i]["Id"];
                    if ('Boutique/Magasin/' . $mag->Id . $C != $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id . $C)) {
                        $this->_getUrl = $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id . $C) . '/Categorie';
                        for ($j = $i - 1; $j >= 0; $j--) $this->_getUrl .= '/' . $cat[$j]["Url"];
                        $this->_getUrl .= '/' . $Uc;
                        return '/'.$this->_getUrl;
                    }
                }
                return 'Boutique/' . $U;
            }
            return parent::getUrl();

        } else return parent::getUrl();
    }

    /**
     * Retourne l'url depuis la racine d'un produit donné
     * @return    URL
     */
    public function getDomaine()
    {
        if (!Sys::$User->Admin) {
            if (isset($this->_getUrl) && !empty($this->_getUrl)) return $this->_getUrl;
            //recherche des categorie
            $cat = $this->storproc('Boutique/Categorie/*/Categorie/Produit/' . $this->Id);
            //on verifie qu'il n'y pas de menu sur chacune des categories
            $M = false;
            $lastCat = -1;
            //$U='Produit/'.$this->Url;
            if (is_array($cat)) foreach ($cat as $c) {
                if ('Boutique/Categorie/' . $c["Id"] != $GLOBALS["Systeme"]->getMenu('Boutique/Categorie/' . $c["Id"])) {
                    break;
                    $laCat = $c["Id"];
                    //$this->_getUrl = $GLOBALS["Systeme"]->getMenu('Boutique/Categorie/'.$c["Id"]).'/'.$U;
                    //return $this->_getUrl;
                }
            }
            //$U = 'Categorie/'.$U;
            //recherche du magasin
            if (!isset($c)) return "";
            $mag = $this->storproc('Boutique/Magasin/Categorie/' . $c["Id"]);
            if (is_array($mag) && sizeof($mag)) {
                if ('Boutique/Magasin/' . $mag[0]["Id"] != $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag[0]["Id"])) {
                    $leMag = $mag[0]["Id"];
                }
                //recherche du magasin plus la categorie
                $Uc = 'Produit/' . $this->Url;
                $C = '/Categorie';
                if (is_array($cat)) for ($i = sizeof($cat) - 1; $i >= 0; $i--) {
                    $C .= '/' . $cat[$i]["Id"];

                    if ('Boutique/Magasin/' . $mag->Id . $C != $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag->Id . $C)) {
                        $this->_getUrl = $GLOBALS["Systeme"]->getMenu('Boutique/Magasin/' . $mag[0]["Id"] . $C) . '/Categorie';
                        for ($j = $i - 1; $j >= 0; $j--) $this->_getUrl .= '/' . $cat[$j]["Url"];
                        $this->_getUrl .= '/' . $Uc;
                        return $this->_getUrl;
                    }
                }
                return 'Boutique/' . $U;
            }
            return parent::getUrl();

        } else return parent::getUrl();
        // LE RETURN, il manquait le RETURN ^^ so6+
    }

    /**
     * Retourne le nombre de références  pour ce produit
     *
     * @return    Nombre
     */
    public function CheckStock()
    {
        if ($this->TypeProduit == '5') return true;
        $refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference/Quantite>0+StockPermanent=1');
        return is_array($refs) ? sizeof($refs) : 0;
    }

    /**
     * Retourne la reference disponible en fonction des declinaisons selectionnes
     * TODO Gestion des declinaisons
     * @return    Nombre
     */
    public function getReference()
    {
        $refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference');
        return genericClass::createInstance('Boutique', $refs[0]);
    }

    /**
     * Retourne le prix moyen pondéré d'achat
     * -> La moyenne à laquelle ce produit se vend
     * TODO    Attention, ne compter que les références qui ont effectivement été vendues
     * @return    Somme (ou -1 si il n'y a pas encore eu de vente)
     */
    function getPmpa()
    {
        $refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference');
        $nbRefs = count($refs);
        if ($nbRefs == 0) return -1;
        $total = 0;
        for ($i = 0; $i < $nbRefs; $i++) $total += $refs[$i]['Tarif'];
        return round($total / $nbRefs, 2);
    }

    /**
     * Retourne le prix minimum d'une référence de ce produit
     *
     * @return    Prix
     */
    public function getTarif()
    {
        // Calcul du prix parmi les références dispos
        $prixMini = $this->Tarif;
        $prixMini = $this->applyPromo($prixMini);
        $emb = $this->GetEmballage();
        if (is_object($emb) && $emb->Surcout > 0) $prixMini += $emb->Surcout;
        $remise = $this->getRemiseProduit(1);

        // decembre 2013 pris en compte de type de tva avec taux et zone
        $Montant = $prixMini * (100 - $remise) / 100;
        $letaux = $this->getTauxTva();
        if ($letaux > 0) $Montant += (($Montant * $letaux) / 100);
        return sprintf('%.2f', $Montant);
    }


    /**
     * Retourne le prix minimum d'une référence de ce produit hors promotion
     *
     * @return    Prix
     */
    public function getTarifHorsPromo()
    {
        // Calcul du prix parmi les références dispos
        $prixMini = $this->Tarif;
        $emb = $this->GetEmballage();

        if (is_object($emb) && $emb->Surcout > 0) $prixMini += $emb->Surcout;

        // decembre 2013 pris en compte de type de tva avec taux et zone
        $Montant = $prixMini;
        $letaux = $this->getTauxTva();
        if ($letaux > 0) $Montant += (($prixMini * $letaux) / 100);
        return sprintf('%.2f', $Montant);
    }

    /**
     * calcul du prix avec application tva
     * @return true ou false
     */
    public function applyTva($Montant, $config, $txremise = 1)
    {

        // decembre 2013 pris en compte de type de tva avec taux et zone
        $TTC = $Montant;
        if ($this->TypeProduit != 4 && $this->TypeProduit != 5) {
            $letaux = $this->getTauxTva();
            if ($letaux > 0) $TTC += (($Montant * $letaux) / 100);
        } else {
            //Calcul des tarifs sans reduction pour caluler le taux de tva anarchique
            $TTCTemp = 0;
            $HTTemp = 0;
            //parcourt des ConfigPack
            $cps = $this->getChildren('ConfigPack');
            foreach ($cps as $cp) {
                $tx = 1 + ($this->getTauxTva($cp->TauxTva) / 100);
                //echo "taux config pack ".$cp->Nom.":".$tx."\r\n";
                if ($cp->TarifPack) {
                    //récupération du taux en vigueur pour le configpack
                    if (isset($config[$cp->Id]) && !empty($config[$cp->Id])) {
                        //récupération du tarif de la référence sélectionnée
                        $re = genericClass::createInstance('Boutique', 'Reference');
                        $re->initFromId($config[$cp->Id]);
                        $TTCTemp += $re->TarifPack * $tx * $txremise;
                        $HTTemp += $re->TarifPack * $txremise;
                        //echo "montant ttc ref :".($re->TarifPack*$tx)."\r\n";
                    } else
                        $TTCTemp += $cp->TarifHT * $tx * $txremise;
                    $HTTemp += $cp->TarifHT * $txremise;
                    //echo "montant ttc cp :".($cp->TarifHT*$tx)."\r\n";
                } else {
                    $TTCTemp += $cp->TarifHT * $tx * $txremise;
                    $HTTemp += $cp->TarifHT * $txremise;
                    //echo "montant ttc cp :".($cp->TarifHT*$tx)."\r\n";
                }
            }
            //calcul du taux et application sur le montant fournis (potentiellement avec une remise)
            //$txtemp = $TTCTemp/$HTTemp;
            //$TTC = $TTC*$txtemp;
            $TTC = $TTCTemp;
            //echo "MONTANT HT INPUT :".$Montant." MONTANT TTC OUTPUT:".$TTC." MONTANT HT CALC:".$HTTemp." MONTANT TTC CALC:".$TTCTemp;
        }
        return $TTC;

    }

    /**
     * Retourne l'objet promotion pour un produit
     * TODO
     * @return    Objet Promo ou 0
     */
    public function GetPromo()
    {
        $promos = $this->storproc('Boutique/Produit/' . $this->Id . '/Promotion/DateDebutPromo<=' . time() . '&&DateFinPromo>=' . time(), '', '', '', 'DESC', 'tmsEdit');
        if (is_array($promos) && isset($promos[0])) return genericClass::createInstance('Boutique', $promos[0]);
        return 0;
    }


    public function getRemiseProduit($qte = 1)
    {
        $prod = $this;
        //Aucune remise sur les services
        if ($prod->NatureProduit == 2) return 0;
        $re = 0;
        $remises = Array();
        //gestion des remises si client connecté
        if (isset($GLOBALS["Systeme"]->RegVars["CurrentClient"]) && is_object($GLOBALS["Systeme"]->RegVars["CurrentClient"])) {
            $cl = $GLOBALS["Systeme"]->RegVars["CurrentClient"];
            $remises = $cl->_Remises;
            //récupération des remises pour ce produit
            $remises = array_merge($remises, $cl->checkRemiseProduit($prod, $qte));
        }
        //gestion des remises relatives uniquement au produit
        $rrs = $prod->getParents('RegleRemise');
        foreach ($rrs as $rr) {
            if ($rr->Public == 1 && $qte > $rr->QuantiteMinimale) {
                //on vérifie que les regles de remise ne sont pas connectées à un client ou un groupe de client
                $remises = array_merge($remises, array("Règle Remise " . $rr->Nom . " Produit " . $prod->Nom => $rr->Remise));
            }
        }
        //gestion des remises relatives à la catégorie du produit
        //pour chaque catégorie du produit on teste les regles
        $cats = $prod->getParents('Categorie/*/Categorie');
        foreach ($cats as $c) {
            $rrs = $c->getParents('RegleRemise');
            foreach ($rrs as $rr) {
                if ($rr->Public == 1 && $qte > $rr->QuantiteMinimale) {
                    //on vérifie que les regles de remise ne sont pas connectées à un client ou un groupe de client
                    $remises = array_merge($remises, array("Règle Remise " . $rr->Nom . " Categorie " . $c->Nom => $rr->Remise));
                }
            }
        }
        //application de la plus grosse remise
        foreach ($remises as $k => $r) {
            if ($re < $r) $re = $r;
        }
        //application de la remise
        return $re;
    }

    /**
     * Retourne l'objet promotion pour un produit
     * TODO
     * @return    Objet Promo ou 0
     */
    public function GetPromoQte($Qte = 1)
    {
        $promos = $this->storproc('Boutique/Produit/' . $this->Id . '/Promotion/DateDebutPromo<=' . time() . '&&DateFinPromo>=' . time(), '', '', '', 'DESC', 'APartirNbUnite');
        $yapromo = false;
        $prixRef = $this->Tarif;
        if (is_array($promos)) foreach ($promos as $promo):
            if ($promo['APartirNbUnite'] != '' && $promo['APartirNbUnite'] > $Qte) {
                //pas de promo
                //klog::l("pas de promo", $promo['Intitule']);
            } else {
                //klog::l("promo", $promo['Intitule']);
                if ($promo['PrixVariation'] != '0') {
                    if ($promo['TypeVariation'] == '1') {
                        // pourcentage
                        $PrixPromo = $prixRef - (($prixRef * $promo['PrixVariation']) / 100);
                        $yapromo = true;
                    }
                    if ($promo['TypeVariation'] == '2') {
                        // montant fixe
                        $PrixPromo = $prixRef - $promo['PrixVariation'];
                        $yapromo = true;
                    }

                } else {
                    // prixforcé renseigné donc le montant remplace le tarif
                    if ($promo['PrixForce'] != '0') {
                        $PrixPromo = $promo['PrixForce'];
                        $yapromo = true;
                    }
                }
            }
            if ($yapromo) break;
        endforeach;
        return $yapromo;

    }

    public function applyPromoQte($prixMini, $Qte = 1)
    {
        // il faut vérifier s'il y a une promotion
        $prixRef = $this->Tarif;
        $PrixPromo = $prixMini;
        //klog::l(" promo",$PrixPromo);
        $yapromo = false;
        $promos = $this->storproc('Boutique/Produit/' . $this->Id . '/Promotion/DateDebutPromo<=' . time() . '&&DateFinPromo>=' . time(), '', '', '', 'DESC', 'APartirNbUnite');
        if (is_array($promos)) foreach ($promos as $promo):
            if ($promo['APartirNbUnite'] != '' && $promo['APartirNbUnite'] > $Qte) {
                //pas de promo
                //klog::l("pas de promo", $promo['Intitule']);
            } else {
                //klog::l("promo", $promo['Intitule']);
                if ($promo['PrixVariation'] != '0') {
                    if ($promo['TypeVariation'] == '1') {
                        // pourcentage
                        $PrixPromo = $prixRef - (($prixRef * $promo['PrixVariation']) / 100);
                        $yapromo = true;
                    }
                    if ($promo['TypeVariation'] == '2') {
                        // montant fixe
                        $PrixPromo = $prixRef - $promo['PrixVariation'];
                        $yapromo = true;
                    }

                } else {
                    // prixforcé renseigné donc le montant remplace le tarif
                    if ($promo['PrixForce'] != '0') {
                        $PrixPromo = $promo->PrixForce;
                        $yapromo = true;
                    }
                }
            }
            if ($yapromo) break;
        endforeach;

        if ($yapromo) {
            if ($prixMini > $PrixPromo) $prixMini = $PrixPromo;
        }

        return $prixMini;

    }

    /**
     * Retourne le nombre de promo pour savoir si ce produit est en promo
     * TODO
     * @return    nbdePromo
     */
    public function EstenPromo()
    {
        $promos = $this->storproc('Boutique/Produit/' . $this->Id . '/Promotion/DateDebutPromo<=' . time() . '&&DateFinPromo>=' . time(), '', '', '', 'DESC', 'tmsEdit');
        return $promos;
    }

    public function applyPromo($prixMini)
    {
        //if ($this->NatureProduit==2) return false;
        // il faut vérifier s'il y a une promotion
        $PrixPromo = $prixMini;
        $promo = $this->GetPromo();
        if (is_object($promo)) {
            // on a un prixvariation qui est un pourcentage
            if ($promo->PrixVariation != '0') {
                if ($promo->TypeVariation == '1') {
                    // pourcentage
                    $PrixPromo = $prixMini - (($prixMini * $promo->PrixVariation) / 100);
                }
                if ($promo->TypeVariation == '2') {
                    // montant fixe
                    $PrixPromo = $prixMini - $promo->PrixVariation;
                }

            } else {
                // prixforcé renseigné donc le montant remplace le tarif
                $PrixPromo = $promo->PrixForce;
            }
        }

        if ($prixMini > $PrixPromo) {
            $prixMini = $PrixPromo;
        };

        return $prixMini;

    }

    /**
     * Retourne l'emballage pour un produit
     * TODO
     * @return    type d'emballage
     */
    public function GetEmballage()
    {
        $conds = $this->storproc('Boutique/Conditionnement/Produit/' . $this->Id);
        if (is_array($conds) && isset($conds[0])) return $conds[0];
        $conds = $this->storproc('Boutique/Conditionnement/ConditionnementDefaut=1', '', '', 1, 'DESC', 'tmsEdit');
        if (is_array($conds) && isset($conds[0])) return $conds[0];
        return '';
    }

    /**
     * Retourne le nombre d'unité vendu pour un produit
     * TODO
     * @return    Nb Unité vendu pour un produit
     */
    public function GetColisage()
    {
        $conds = $this->storproc('Boutique/Conditionnement/Produit/' . $this->Id);
        if (is_array($conds) && isset($conds[0])) return $conds[0]['Colisage'];
        $conds = $this->storproc('Boutique/Conditionnement/ConditionnementDefaut=1', '', '', 1, 'DESC', 'tmsEdit');
        if (is_array($conds) && isset($conds[0])) return $conds[0]['Colisage'];
        return '1';

    }

    /**
     * fonction qui duplique un produit avec ses references, ses attributs..... dans la même categorie
     * @return    false si erreur
     */

    public function getClone($noreset = false)
    {
        $prod = parent::getClone();
        $prod->Reference = "";
        $prod->Save();
        //provisionning children
        $donnees = $this->getChildren('Donnee');
        foreach ($donnees as $d) {
            $d2 = $d->getClone();
            $d2->addParent($prod);
            $d2->Save();
        }
        $attrs = $this->getChildren('Attribut');
        foreach ($attrs as $a) {
            $a->addParent($prod);
            $a->Save();
        }
        $promos = $this->getChildren('Promotion');
        foreach ($promos as $pm) {
            $pm->addParent($prod);
            $pm->Save();
        }

        $cps = $this->getChildren('ConfigPack');
        foreach ($cps as $cp) {
            $cp2 = $cp->getClone();
            $cp2->addParent($prod);
            $cp2->Save();
            $refs2 = $cp->getChildren('Reference');
            foreach ($refs2 as $ref2) {
                $ref2->addParent($cp2);
                $ref2->Save();
            }
            // les options ne sont pas clonées mais juste liées
            // donc pas besoin de faire options détails car déjà liées à otions
            $opts = $cp->getChildren('Options');
            foreach ($opts as $opt) {
                $opt->addParent($cp2);
                $opt->Save();
            }
        }

//-----------------------------------

        $marques = $this->getChildren('Marque');
        foreach ($marques as $m) {
            $m->addParent($prod);
            $m->Save();
        }
        /*		$refs = $this->getChildren('Reference');
                foreach ($refs as $r){
                    $r2 = $r->getClone();
                    $r2->Reference.='-'.$prod->Id;
                    $r2->addParent($prod);
                    $decls = $r->getParents('Declinaison');
                    foreach ($decls as $decl) $r2->addParent($decl);
                    $r2->Save();
                }
        */
        //positionning
        $cat = $this->getParents('Categorie');
        foreach ($cat as $c) $prod->addParent($c);
        $cond = $this->getParents('Conditionnement');
        foreach ($cond as $co) $prod->addParent($co);
        $prod->Actif = 0;
        //$prod->Display=0;
        $prod->Save();

    }

    // version qui est active dans boutique février 2015

    public function getCloneV2($NomP = '', $RefP = '', $PrefixeNomRef = '', $PrefixeRefRef = '')
    {

        $prod = parent::getClone();

        if ($NomP != '') $prod->Nom = $NomP;

        if ($RefP != '') $prod->Reference = $RefP;

        $prod->Save();

        //provisionning children
        $donnees = $this->getChildren('Donnee');
        foreach ($donnees as $d) {
            $d2 = $d->getClone();
            $d2->addParent($prod);
            $d2->Save();
        }

        $attrs = $this->getChildren('Attribut');
        foreach ($attrs as $a) {
            $a->addParent($prod);
            $a->Save();
        }

        $promos = $this->getChildren('Promotion');
        foreach ($promos as $pm) {
            $pm->addParent($prod);
            $pm->Save();
        }

        $cps = $this->getChildren('ConfigPack');
        foreach ($cps as $cp) {

            $cp2 = $cp->getClone();
            $cp2->addParent($prod);
            $cp2->Save();

            $refs2 = $cp->getChildren('Reference');
            foreach ($refs2 as $ref2) {
                $ref2->addParent($cp2);
                $ref2->Save();
            }
            // les options ne sont pas clonées mais juste liées
            // donc pas besoin de faire options détails car déjà liées à otions
            $opts = $cp->getChildren('Options');
            foreach ($opts as $opt) {
                $opt->addParent($cp2);
                $opt->Save();
            }
        }

        $marques = $this->getChildren('Marque');
        foreach ($marques as $m) {
            $m->addParent($prod);
            $m->Save();
        }

        $refs = $this->getChildren('Reference');

        foreach ($refs as $r) {

            //klog::l("IdRefe",$r->Id);
            $r2 = $r->getClone();

            $r2->addParent($prod);

            $decls = $r->getParents('Declinaison');

            $nom = $r->Nom;
            $ref = $r->Reference;
            $first = 0;
            foreach ($decls as $decl) {
                $r2->addParent($decl);
                if ($PrefixeNomRef != '') {
                    if (!$first) {
                        $nom = $PrefixeNomRef . ' "' . $NomP . '" ' . $decl->Code;
                    } else {
                        if (!strpos($decl->Code, $nom)) {
                            $nom .= ' ' . $decl->Code;
                        }
                    }
                }
                if ($PrefixeRefRef != '') {
                    $attr = $decl->getParents('Attribut');

                    if (!$first) {
                        $ref = $PrefixeRefRef . '-' . $attr[0]->Id . '-' . $decl->Id;
                    } else {
                        if (!strpos($decl->Code, $nom)) {
                            $ref .= '-' . $attr[0]->Id . '-' . $decl->Id;
                        }
                    }
                }
                $first++;
            }

            $ref .= '-' . $prod->Id;

            $r2->Nom = $nom;
            $r2->Reference = $ref;

            $r2->Save();
        }

        //positionning
        $cat = $this->getParents('Categorie');
        foreach ($cat as $c) $prod->addParent($c);
        $cond = $this->getParents('Conditionnement');
        foreach ($cond as $co) $prod->addParent($co);
        $prod->Actif = 0;
        //$prod->Display=0;
        $prod->Save();

    }


    public function getCloneMathilde($prod)
    {

        // recupérer la produit modele
//		klog::l("modèle", $prod->Id);


        $cpks = $this->getChildren('ConfigPack');
        foreach ($cpks as $cpk) {
            if ($cpk->EtapeVisu != 1 && $cpk->EtapeVisu != 3) {
                $cpk->Delete();
                //klog::l("on supprimerait",$cpk->Id . "- " .$cpk->Nom);
            }
        }


        $cps = $prod->getChildren('ConfigPack');
        foreach ($cps as $cp) {
            if ($cp->Id != 715 && $cp->Id != 711) {
                $cp2 = $cp->getClone();
                $cp2->addParent($this);
                $cp2->Save();
                $refs2 = $cp->getChildren('Reference');
                foreach ($refs2 as $ref2) {
                    $ref2->addParent($cp2);
                    $ref2->Save();
                }
                // les options ne sont pas clonées mais juste liées
                // donc pas besoin de faire options détails car déjà liées à otions
                $opts = $cp->getChildren('Options');
                foreach ($opts as $opt) {
                    $opt->addParent($cp2);
                    $opt->Save();
                }
            }
        }
        //$prod->Display=0;
        $this->Save();

    }

    public function getCloneMathildeV2($prod)
    {

        /*
        // recupérer la produit modele
        klog::l("modèle", $prod->Id);
        klog::l("maj", $this->Id);
        klog::l("accroche av ", $this->Accroche);
        $this->Accroche=$prod->Accroche;
        klog::l("accroche ap", $this->Accroche);

        klog::l("description av ", $this->Description);
        $this->Description=$prod->Description;
        klog::l("description ap", $this->Description);

        klog::l("tarif av ", $this->Tarif);
        $this->Tarif=$prod->Tarif;
        klog::l("tarif ap", $this->Tarif);

        $ref=$this->getReference();
        klog::l("refp", $ref->Id);
        klog::l("tarif av ", $ref->Tarif);
        $ref->Tarif=$this->Tarif;
        klog::l("tarif ap", $ref->Tarif);
        $ref->Save();


        //$prod->Display=0;
        $this->Save();*/

    }

    /**
     * Raccourci vers callData
     * @return    Résultat de la requete
     */
    private function storproc($Query, $recurs = '', $Ofst = '', $Limit = '', $OrderType = '', $OrderVar = '', $Selection = '', $GroupBy = '')
    {
        return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
    }


    /**
     * Retourne un montant dans un tableau avec valeur entier et decimale sur deux
     * TODO
     * @return    tableau deux valeurs numérique
     */
    public function DecoupePrix($Valeur)
    {

        /*$Montant['Entier']=floor($Valeur);
        $MontantDec=$Valeur-floor($Valeur);
        $Montant['2Decimale']=round($MontantDec,2)*100;*/
        $Montant = round($Valeur, 2);
        return sprintf('%.2f', $Montant);
    }

    /**
     * Attribution du taux de tva
     */
    public function getTauxTva($typetaux = null)
    {


        // le champ TypeTvaInterne est le champ tva de la fiche produit
        // on stocke le type de tva et dans type tva on a les taux actifs
        $letaux = 0;
        $lazone = 0;
        if (!$typetaux) {
            // changement du nom du champ
//			if ( $this->TypeTvaInterne == ''||$this->TypeTvaInterne==0) return 0;
            if ($this->TypeTva == '' || $this->TypeTva == 0) return 0;
            //------- //
            // recherche classique on le fait par défaut car on l'utilisera le plus souvent
            $typetaux = $this->TypeTva;
        }

        Boutique::initTableauTva();
        $tabtva = $GLOBALS['Systeme']->getRegVars('TX_TVA');
        if (!isset($tabtva[$typetaux])) return 0;
        $letaux = $tabtva[$typetaux];
        return $letaux;

    }


}