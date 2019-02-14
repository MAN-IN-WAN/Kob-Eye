<?php

Class UtilsSkin{
        /*
         *Lit un fichier csv et l'importe depuis la skin meta pour ajouter/mettre à jour les titres/description des pages
         * arg1 : chemin du fichier
         * arg2 : separateur du csv : 'v' pour ',' et 'pv' pour ';'
         * arg3 : le site à mettre à jour
         */
           static public function getCsv($P){
                $uri = $P[0];
				$delimiter = ( isset($P[1]) && $P[1] == 'v') ? ',' : ';';
                $domaine = $P[2];
                                
                if(is_file($uri)){
 
                        //On recup le csv sous forme de tableau
                        $csv = array_map(function($i) use($delimiter){
                                return str_getcsv($i,$delimiter);
                        }, file($uri));
                        
                        //On passe les url des pages en clef de ces tableaux
                        $csvPages = array();
                        foreach($csv as $csvPage){
                             $csvPages[$csvPage[0]] = array($csvPage[1],$csvPage[2]);
                        }
                        //On recup les pages effective du site
                        $pages = Sys::getData('Systeme','Page');
                        $site = Sys::getData('Systeme','Site/Domaine='.$domaine);
                        $siteId = $site[0]->Id;
						
			$pages = array_filter($pages,function($a)use($siteId){
				$pa=$a->getParents('Site');
				return ($siteId == $pa[0]->Id);
			});
		
			
                        
                        //On traite les pages
                        foreach($pages as $page){
                                if(isset($csvPages[$page->Url])){
                                        $infos=$csvPages[$page->Url];
                                        $page->Set('Title',$infos[0]);
                                        $page->Set('Description',$infos[1]);
                                        $page->Save();
                                }
                        }
                        
                        
                        return;
                } else {
                        return array('Fichier non trouvé');
                }
        }
	
}

?>