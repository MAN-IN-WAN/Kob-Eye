<?php
class Photo extends genericClass{
    /**
     * Delete
     * Supprime une photo et son fichier
     */
    function Delete() {
        unlink($this->Final);
        unlink($this->Photo);
        parent::Delete();
    }
    /**
     * create
     * télécharge un photo et créé l'objet correspondant
     * @param $url
     * @return mixed
     */
    static function create($url) {
        //récupération de la session en cours
        $sess = PhotoSession::getCurrent();
        $apn = Apn::getCurrent();
        $photo = genericClass::createinstance('LightBox','Photo');
        $photo->Titre = date('d/m/Y H:i:s');
        $photo->addParent($apn);
        $photo->addParent($sess);
        $u = 'Home/Sessions/'.$sess->Id;
        //creation du dossier
        Root::mk_dir($u);
        //ajout du nom de la photo
        $photo->Save();
        $u.='/'.$photo->Id.'.jpg';
        $photo->Photo = $u;
        $photo->Save();
        file_put_contents($u,file_get_contents($url));

        //DIRTY WORK AROUND
        $photo->resize_image_crop($u,'1920','1200');
        //applyMasque
        if (!empty($sess->Masque)) $photo->applyMask();
        else{
            $photo->Final = $photo->Photo;
            $photo->Save();
        }
        return $u;
    }

    function applyMask() {
        $sess = PhotoSession::getCurrent();
        $png = imagecreatefrompng($sess->Masque);
        $jpeg = imagecreatefromjpeg($this->Photo);

        list($width, $height) = getimagesize($this->Photo);
        list($newwidth, $newheight) = getimagesize($sess->Masque);
        $out = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagecopyresampled($out, $png, 0, 0, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
        $mask = $this->Photo.'.mask.jpg';
        imagejpeg($out, $mask, 100);
        $this->Final = $mask;
        $this->Processed = true;
        $this->Save();
    }
    function resize_image_crop($url,$width,$height) {
        $image = ImageCreateFromJpeg($url);
        $w = @imagesx($image); //current width
        $h = @imagesy($image); //current height
        if ((!$w) || (!$h)) {
            die('ERROR resize image crop '.$w.' '.$h);
        }
        if (($w == $width) && ($h == $height)) {
            die('ERROR same image '.$w.' '.$h);
            return $image;
        } //no resizing needed

        //try max width first...
        $ratio = $width / $w;
        $new_w = $width;
        $new_h = $h * $ratio;

        //if that created an image smaller than what we wanted, try the other way
        if ($new_h < $height) {
            $ratio = $height / $h;
            $new_h = $height;
            $new_w = $w * $ratio;
        }

        $image2 = imagecreatetruecolor ($new_w, $new_h);
        imagecopyresampled($image2,$image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

        //check to see if cropping needs to happen
        if (($new_h != $height) || ($new_w != $width)) {
            $image3 = imagecreatetruecolor ($width, $height);
            if ($new_h > $height) { //crop vertically
                $extra = $new_h - $height;
                $x = 0; //source x
                $y = round($extra / 2); //source y
                imagecopyresampled($image3,$image2, 0, 0, $x, $y, $width, $height, $width, $height);
            } else {
                $extra = $new_w - $width;
                $x = round($extra / 2); //source x
                $y = 0; //source y
                imagecopyresampled($image3,$image2, 0, 0, $x, $y, $width, $height, $width, $height);
            }
            imagedestroy($image2);
            imagejpeg($image3, $url);
        } else {
            imagejpeg($image2, $url);
        }
    }

}