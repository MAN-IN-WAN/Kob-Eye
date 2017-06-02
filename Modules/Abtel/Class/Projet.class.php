<?PHP
class Projet extends genericClass{
	
	function Save(){
		genericClass::Save();
                $this->mkLinkEntite();
                
	}
        
        /*
         * function mkLinkEntite
         * Crée / met à jour automatiquement les objets PELink qui font le lien entre les projets et les entités
         *
         */
        private function mkLinkEntite(){
                
                //On recup toutes les entités d'abtel
                $entites = Sys::getData('Abtel','Entite');
                $linkedEntities = array();
                //Pour ce projet on recup toutes les liaisons avec les entites puis les entités elles même
                $links = $this->getChildren('PELink');
                foreach($links as $link){
                        $linkedEntities = array_merge($linkedEntities,$link->getParents('Entite'));
                }
                //ON regarde si il y une difference
                $missings = array_udiff($entites,$linkedEntities, function($a,$b){
                        if($a->Id > $b->Id) return 1;
                        if($a->Id == $b->Id) return 0;
                        if($a->Id < $b->Id) return -1;
                });
                //Si différence on crée les liens kivonbien
                foreach($missings as $missing){
                        $temp = genericClass::createInstance('Abtel','PELink');
                        $temp->addParent($missing);
                        $temp->addParent($this);
                        $temp->Set('Nom',$missing->Nom .' / '. $this->Nom);
			$temp->Save();
                }
        }
         static public function getHomeProjects(){
                $projects = Sys::getData('Abtel','Projet/Accueil=1');
                
                array_walk($projects, function(&$Proj){
                        $links = $Proj->getChildren('PELink');
                        $tiers = $Proj->getParents('Tiers');
                        if(sizeof($tiers)){
                                $tiers = $tiers[0];
                        } else {
                                $tiers = false;
                        }
                        $Proj->Tiers = $tiers;
                        $Proj->Links = $links;
                });
                
                $htmlBack = '<div id="slideBack" class="carousel slide">
                                        <div class="carousel-inner">';
                $i = 1;
                $side = array();
                foreach ($projects as $key => $Proj){
                        $htmlBack .=            '<div class="item '. ($i == 1 ? 'active' : '') .'" data-project="'.$Proj->Id.'">
                                                        <img src="'.$Proj->BackImage.'" alt="'.$Proj->Nom.'">
                                                </div>';
                        $i++;
                        $tempLink = array();
                        foreach($Proj->Links as $Link){
                                $Ent = $Link->getParents('Entite');
                                $Ent = $Ent[0];
                                if ($Ent->CodeGestion == '00') continue;
                                
                                $tempLink[$Ent->CodeGestion] = array( 'name' => $Ent->Nom, 'value' => $Link->Value, 'color' => $Ent->CodeCouleur);    
                        }
                        $side[$Proj->Id] = array('progress' => $tempLink, 'logo'=> ($Proj->Tiers ? $Proj->Tiers->Logo : false), 'tiersNom' => ($Proj->Tiers ? $Proj->Tiers->Nom : false), 'desc' => $Proj->Description) ;
                }
                $htmlBack .= '            </div>
                            </div>';

                $html = array( 'back' =>  $htmlBack, 'side' => $side);
                $html = json_encode($html);           
                
                return $html;
        }
        
        //static public function getHomeProjects(){
        //        $projects = Sys::getData('Abtel','Projet/Accueil=1');
        //        
        //        array_walk($projects, function(&$Proj){
        //                $links = $Proj->getChildren('PELink');
        //                $tiers = $Proj->getParents('Tiers');
        //                if(sizeof($tiers)){
        //                        $tiers = $tiers[0];
        //                } else {
        //                        $tiers = false;
        //                }
        //                $Proj->Tiers = $tiers;
        //                $Proj->Links = $links;
        //        });
        //        
        //        $htmlBack = '<div id="slideBack" class="carousel slide">
        //                                <div class="carousel-inner">';
        //        $htmlSide = '<div id="projectDesc"class="carousel slide">
        //                                <div class="carousel-inner">';
        //        $i =1;
        //        foreach ($projects as $key => $Proj){
        //                $htmlBack .=            '<div class="item '. ($i == 1 ? 'active' : '') .'">
        //                                                <img src="'.$Proj->BackImage.'" alt="'.$Proj->Nom.'">
        //                                        </div>';
        //                $htmlSide .=            '<div class="item '. ($i == 1 ? 'active' : '') .'">
        //                                                <div class="tiersLogo">';
        //                if($Proj->Tiers)  {                              
        //                        $htmlSide .=            '       <img src="'.$Proj->Tiers->Logo.'" alt="'.$Proj->Tiers->Nom.'" class="img-responsive">';
        //                }
        //                $htmlSide .=            '       </div>';                              
        //                $htmlSide .=            '       <div class="projectResume">'.$Proj->Description.'</div>
        //                                                <div class="projectShare">';
        //
        //                foreach($Proj->Links as $Link){
        //                        $Ent = $Link->getParents('Entite');
        //                        $Ent = $Ent[0];
        //                        if ($Ent->CodeGestion == '00') continue;
        //                        $htmlSide .=    '               <p class="projectEntity" ><span class="entityPercent">XX%</span> '.$Ent->Nom.'<p>
        //                                                        <div class="progress">
        //                                                                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" data-width="60%" style="background-color:'.$Ent->CodeCouleur.';">
        //                                                                </div>
        //                                                        </div>';
        //                }                        
        //                $htmlSide .=             '      </div>
        //                                        </div>';
        //                $i++;
        //        }
        //        $htmlBack .= '            </div>
        //                    </div>';
        //        $htmlSide .= '             </div>
        //                                   <a href="#projectDesc" role="button" data-slide="next">></a>
        //                    </div>';
        //        
        //        $html = array( 'back' =>  $htmlBack, 'side' => $htmlSide);
        //        $html = json_encode($html);           
        //        
        //        return $html;
        //}
        
}
?>