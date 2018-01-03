<?php
	class CmsPage extends genericClass{
		function getModele (){
			$out = Array();
			//skin
			$dirs = $this->listDirectory('Skins');
			if (is_array($dirs))foreach ($dirs as $dir){
				if ($dir!=SHARED_SKIN)
					$out = array_merge($out,$this->listFilesDirectory('Skins/'.$dir.'/Modules/Cms/Modeles',"$dir > "));
			}
			//Modules
			$out = array_merge($out,$this->listFilesDirectory('Modules/Cms/Modeles',"module > "));
			//Common
			$out = array_merge($out,$this->listFilesDirectory('Skins/'.SHARED_SKIN.'/Modules/Cms/Modeles',"commun > "));
			return $out;
		}
		function listFilesDirectory($dirname,$prefixe=""){
			$out=Array();
			if (file_exists($dirname)){
				$dir = opendir($dirname); 
				while($file = readdir($dir)) {
					if($file != '.' && $file != '..' && !is_dir($dirname.'/'.$file) && !preg_match("#^\..*#", $file)){
						preg_match("#(.*)\.(.*)$#", $file,$fi);
						if ($fi[2]=="md")
							$out[$fi[1]] = $prefixe.$fi[1];
					}
				}
				closedir($dir);
			}
			return $out;
		}
		function listDirectory($dirname){
			$out=Array();
			if (file_exists($dirname)){
				$dir = opendir($dirname); 
				while($file = readdir($dir)) {
					if($file != '.' && $file != '..' && is_dir($dirname.'/'.$file)&& !preg_match("#^\..*#", $file)){
						$out[] = $file;
					}
				}
				closedir($dir);
			}
			return $out;
		}

        /**
         * getAncestry
         * Build array listing all parents
         * @param $id Integer Optional Id de l'article de base
         * @param $full Boolean Optional Objet complet/simpleId
         * @return Mixed
         */
        public static function getAncestry($id=null,$full=true){
            if(!$id) return false;


            $obj=Sys::getData('Cms','Page/'.$id);

            if(isset($obj[0])){
                $base=$obj[0];
            } else {
                return array();
            }

            $list = $full ? array($base) : array($base->Id);
            $par = $base->getParents('Page');
            if(sizeof($par) && isset($par[0])){
                $prev =  CmsPage::getAncestry($par[0]->Id,$full);
                return array_merge($prev,$list);
            }

            return $list;
        }

        /**
         * Delete
         * Delete this function
         * @return Boolean
         */
        public function Delete(){
            $ch = $this -> getChildTypes();
            if (is_array($ch)) {
                foreach ($ch as $c) {
                    $chs = $this->getChilds($c["Titre"]);
                    if (is_array($chs))
                        foreach ($chs as $cs)
                            $cs->Delete();
                }
            }
            parent::Delete();
        }
	}
?>