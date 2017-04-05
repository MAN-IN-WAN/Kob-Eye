<?php
class Colonne extends genericClass{
	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
        
    public function getType(){

            $imgs = $this->getChildren('Image');
            if(is_array($imgs) && sizeof($imgs))
                    return 'Image';

            $txts = $this->getChildren('Texte');
            if(is_array($txts) && sizeof($txts))
                    return 'Texte';

            return 'Empty';
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