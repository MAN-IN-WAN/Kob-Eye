<?php
class _Fichier extends genericClass {
    public function rename($newName = false){
        //Chemin original du fichier
        $base = dirname($this->Url).'/';

        //Extensions
        $extensions = array('image/jpeg' => 'jpg','image/png' => 'png'  ,'image/gif' => 'gif');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $this->Url);
        finfo_close($finfo);
        $ext = '.'.$extensions[$mimetype];

        //Si pas de nom fourni on cleane juste l'actuel
        if(!$newName){
            $newName = $this->checkName($this->Nom);
        }
        //On vire les extensions
        $newName = str_ireplace(array('.jpg','.png','.gif'),'',$newName);

        //Special Artisans
        $newName = preg_replace_callback('/^([0-9]+)(_)*/',function($m){ return sprintf("%04d",$m[1])."_"; },$newName);


        //On verifie qu'il n'y ait pas déjà de fichier portant ce nom
        if(is_file($base.$newName.$ext)) {
            $n=0;
            while (is_file($base . $newName . '_' . $n . $ext)) {
                $n+=1;
            }
            $newName = $newName . '_' . $n;
        }
        //On renomme
        return rename($this->Url,$base.$newName.$ext);
    }

    function checkName($chaine){
        //On enleve les accents
        $chaine= strtr($chaine,  "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿ#Ññ","aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn" );
        //On remplace tous ce qui n est pas une lettre ou un chiffre par un _
        $chaine = preg_replace('/([^.a-z0-9]+)/i', '_', $chaine);
        //On ajoute le timestamp au nom de fichier
        // 		$chaine = preg_replace('#(.+)\.[a-z]{3})#i','$1-'.time().'.$2',$chaine);
        return $chaine;
    }

}