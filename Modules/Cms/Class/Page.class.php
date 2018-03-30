<?php
	class CmsPage extends genericClass{
        var $TemplateObject;		//Object Template Beacon
        var $Screen;			//Screenshot issu de la config XML
        /*_________________________________________________________________________________________
                                                    PRIVATE
        */
        /**
         * Initialisation de la template beacon avec chargement de la config
         * Declenchement automatique
         */
        public function initTemplate() {
            //Chargement de la template beacon afin de gérer les interactions
            $this->TemplateObject = Template::initFrom($this);
            $this->Screen = $this->TemplateObject->Screen;
        }
        /*_________________________________________________________________________________________
                                                     PUBLIC
        */

        public function getTemplates() {
            $dir = "Modules/Cms/Templates/";
            $out=Array();
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && is_dir($dir.'/'.$file) && !preg_match("#^\..*#",$file)) {
                        $out[] = $file;
                    }
                }
                closedir($handle);
            }
            return $out;
        }

        /**
         * Enregistrement et verification des données
         */
        public function Save() {
            if (isset($this->CmsTemplate)&&!empty($this->CmsTemplate)&&empty($this->TemplateConfig)){
                //Si la configuration est vide alors on charge la configuration de la template
                $Path = 'Modules/Cms/Templates/'.$this->CmsTemplate;
                if (is_dir($Path)){
                    //On verifie que le chemin existe
                    $Path .= '/Template.conf';
                    $this->TemplateConfig = @file_get_contents($Path);
                } else{
                    $this->TemplateConfig = @file_get_contents('Modules/Cms/Templates/Default/Template.conf');
                }
            } elseif (!isset($this->CmsTemplate)||empty($this->CmsTemplate)&&empty($this->TemplateConfig)){
                $this->TemplateConfig = @file_get_contents('Modules/Cms/Templates/Default/Template.conf');
            }
            if (isset($this->CmsTemplate)&&!empty($this->CmsTemplate)&&empty($this->HtmlConfig)){
                //Si la configuration est vide alors on charge la configuration de la template
                $Path = 'Modules/Cms/Templates/'.$this->CmsTemplate;
                if (is_dir($Path)){
                    //On verifie que le chemin existe
                    $Path .= '/Default.md';
                    $this->HtmlConfig = @file_get_contents($Path);
                } else{
                    $this->HtmlConfig = @file_get_contents('Modules/Cms/Templates/Default/Default.md');
                }
            }elseif (!isset($this->CmsTemplate)||empty($this->CmsTemplate)&&empty($this->HtmlConfig)){
                $this->HtmlConfig = @file_get_contents('Modules/Cms/Templates/Default/Default.md');
            }

            //Enregistrement de la nouvelle config
            if (isset($this->TemplateObject)) $this->TemplateConfig = $this->TemplateObject->ExportXml();
            parent::Save();

            $men = $this->getOneParent('Menu');
            $object = $this->getObjectClass();
            if(!$men){
                $men = genericClass::createInstance('Systeme','Menu');
                $men->Alias = 'Cms/Page/'.$this->Id;
            }
            if(!$this->Home){
                $men->Url =  $object->autoLink('Url',get_object_vars($this),'',true);
            } else{
                $men->Url = '';
            }
            $men->Titre = $this->Nom;
            $men->Ordre = $this->Ordre;
            $men->PageTitre = $this->Titre;
            $men->PageDescription = $this->Description;
            $men->Save();
            $this->addParent($men);

            $par = $this->getOneParent('Page');
            if($par){
                $pMen = $par->getOneParent('Menu');
                $men->addParent($pMen);
                $men->Save();
            }

            parent::Save();

            return true;
        }


        /**
         * Enregistrement et verification des données
         */
        public function Delete() {
            $menu = $this->getOneParent('Menu');
            if($menu)
                $menu->Delete();
            parent::Delete();
        }







        /**
         * getZones
         * sans parametre, renvoie la liste des zones de la template
         * @param $z Tag zone
         * Avec le parametre , renvoie une zone en particulier identifiée par son tag
         */
        public function getZones($t=""){
            if (!isset($this->TemplateObject))$this->initTemplate();
            return $this->TemplateObject->getZones($t);
        }
        /**
         * Screen
         * initise la template Object et renvoie le screenshot
         * @return String
         */
        public function getScreen() {
            if (!isset($this->TemplateObject))$this->initTemplate();
            return $this->TemplateObject->Screen;
        }












        /**
         * Ajoute un composant au template courant (selon ce qui est validé dans le formulaire)
         * @param	String	Description du composant sous la form Module/Composant
         * @param	String	Zone dans laquelle on ajoute
         * @return	void
         */
        function addComponent( $Path, $z ) {
            $cpt = Component::getInstance(array($Path));
            $str = 'tag="'.$z.'">';
            $pos = strpos($this->TemplateConfig, $str);
            if($pos !== FALSE) :
                // Decoupe Avant / Apres
                $before = substr($this->TemplateConfig, 0, $pos + strlen($str));
                $after = substr($this->TemplateConfig, $pos + strlen($str));

                // Params
                $params = "";
                foreach($cpt->Proprietes as $Prop) :
                    $nom = $Prop['Nom'];
                    $params .= "\r\n\t\t\t\t\t<PARAM name=\"$nom\" type=\"".$Prop['Type']."\" description=\"".$Prop["description"]."\"><![CDATA[".addslashes($_POST['CC_'.$nom])."]]></PARAM>";
                endforeach;
                if(!empty($params)) $params .= "\r\n\t\t\t\t";

                // Insertion
                $this->TemplateConfig = $before . '
			<COMPONENT title="'.$cpt->Title.'" module="'.$cpt->Module.'">
				<TITLE>'.$cpt->Name.'</TITLE>
				<CSS>Modules/'.$cpt->Module.'/Components/'.$cpt->Title.'/style.css</CSS>
				<SCREEN></SCREEN>
				<PARAMS>'.$params.'</PARAMS>
			</COMPONENT>' . $after;
            else :
                // Erreur
                echo 'ERREUR : IMPOSSIBLE DE TROUVER LA ZONE DANS LA CONFIGURATION DU TEMPLATE !'; die;
            endif;
        }

        /**
         * Retourne les détails d'un composant
         * @param	String	Zone que l'on cible
         * @param	int		Indice de l'élement que l'on veut récupérer
         * @return	objet composant
         */
        function getComponent( $z, $cmp ) {
            $x = new xml2array($this->TemplateConfig);
            $TC = $x->Tableau;
            $zones = $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'];
            $idxZone = -1;
            foreach($zones as $kZ => $zone) if($zone['@']['tag'] == $z) $idxZone = $kZ;
            if($idxZone < 0) exit('Zone non trouvée');
            $component = array("COMPONENT" => $zones[$idxZone]['#']['COMPONENT'][$cmp]);
            $cmpConfig = array2xml::convertToXML($component);
            $return = new Component();
            $return->setConfig($cmpConfig);
            return $return;
        }

        /**
         * Modifie les paramètres d'un composant de la zone indiquée pour ce template
         * ( à partir des données POST )
         * @param	String	Zone que l'on cible
         * @param	int		Indice de l'élement à supprimer
         * @return	void
         */
        function updateComponent( $z, $cmp ) {
            $x = new xml2array($this->TemplateConfig);
            $TC = $x->Tableau;
            $zones = $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'];
            $idxZone = -1;
            foreach($zones as $kZ => $zone) if($zone['@']['tag'] == $z) $idxZone = $kZ;
            if($idxZone < 0) exit('Zone non trouvée');
            $component = $zones[$idxZone]['#']['COMPONENT'][$cmp];
            $cpmModel = Component::getInstance(array($component['@']['module'].'/'.$component['@']['title']));
            foreach($cpmModel->Proprietes as $Prop) $this->updateProp($Prop, $component);
            $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'][$idxZone]['#']['COMPONENT'][$cmp] = $component;
            $this->TemplateConfig = array2xml::convertToXML($TC);
        }

        /**
         * Met à jour une propriété dans un composant
         * @param	array	Propriété à mettre à jour
         * @param	array	! REFERENCE ! Composant
         * @return	void
         */
        function updateProp( $Prop, &$Cmp ) {
            // Déjà présente on l'update si elle est renseignée
            foreach($Cmp['#']['PARAMS'][0]['#']['PARAM'] as $k => $Param) :
                if($Param['@']['name'] == $Prop['Nom']) :
                    if(isset($_POST['CC_'.$Prop['Nom']])){
                        $Cmp['#']['PARAMS'][0]['#']['PARAM'][$k]['#'] = addslashes($_POST['CC_'.$Prop['Nom']]);
                    }
                    return;
                endif;
            endforeach;
            // N'existait pas encore, on l'ajoute
            $Cmp['#']['PARAMS'][0]['#']['PARAM'][] = array('#' => addslashes($_POST['CC_'.$Prop['Nom']]), '@' => array('name' => $Prop['Nom'], 'type' => $Prop['Type']));
        }

        /**
         * Réorganise une zone pour ce template en modifiant le positionnement d'un composant
         * @param	int		Indice de l'élement à déplacer
         * @param	int		Son nouvel indice
         * @param	String	Zone d'origine
         * @param	String	Zone de destination
         * @return	void
         */
        function reOrder( $from, $to, $fromZone, $toZone ) {
            $x = new xml2array($this->TemplateConfig);
            $TC = $x->Tableau;
            $zones = $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'];
            $idxZoneFrom = $idxZoneTo = -1;

            foreach($zones as $kZ => $zone) :
                if($zone['@']['tag'] == $fromZone) $idxZoneFrom = $kZ;
                if($zone['@']['tag'] == $toZone) $idxZoneTo = $kZ;
            endforeach;
            if($idxZoneFrom < 0 and $idxZoneTo < 0) exit('Zone non trouvée');

            $cmp = array($zones[$idxZoneFrom]['#']['COMPONENT'][$from]);
            unset($zones[$idxZoneFrom]['#']['COMPONENT'][$from]);
            if(empty($zones[$idxZoneFrom]['#']['COMPONENT'])) $zones[$idxZoneFrom]['#'] = "";
            if(!is_array($zones[$idxZoneTo]['#'])) $zones[$idxZoneTo]['#'] = array('COMPONENT' => array());
            array_splice($zones[$idxZoneTo]['#']['COMPONENT'], $to, 0, $cmp);


            $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'] = $zones;
            $this->TemplateConfig = array2xml::convertToXML($TC);
        }


        /**
         * Supprime un composant de la zone indiquée pour ce template
         * @param	String	Zone que l'on cible
         * @param	int		Indice de l'élement à supprimer
         * @return	void
         */
        function removeComponent( $z, $cmp ) {
            $x = new xml2array($this->TemplateConfig);
            $TC = $x->Tableau;
            $zones = $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'];
            $idxZone = -1;
            foreach($zones as $kZ => $zone) if($zone['@']['tag'] == $z) $idxZone = $kZ;
            if($idxZone < 0) exit('Zone non trouvée');
            $components = $zones[$idxZone]['#']['COMPONENT'];
            unset($components[$cmp]);
            $TC['TEMPLATE']['#']['ZONES'][0]['#']['ZONE'][$idxZone]['#']['COMPONENT'] = $components;
            $this->TemplateConfig = array2xml::convertToXML($TC);
        }

	}
?>