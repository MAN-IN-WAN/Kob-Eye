<?php
class CmsLigne extends genericClass{
    function __construct($Mod,$Tab){
        genericClass::__construct($Mod,$Tab);
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