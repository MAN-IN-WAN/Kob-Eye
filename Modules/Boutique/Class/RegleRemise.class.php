<?php
class RegleRemise extends genericClass {
    /**
     *  initCategoryProducts
     *  Initialisation des produits et categories relatifs aux règles
     */
    public function initCategoryProducts() {
        $this->_Categories = $this->getChildren('Categorie');
        $this->_Produits = $this->getChildren('Produit');
    }
    
    /**
     *  checkProduct
     *  Vérifie si cette règle correspond à ce produit
     */
    public function checkProduct ($prod,$qte=1) {
        foreach ($this->_Produits as $p){
            if ($p->Id==$prod->Id&&$this->QuantiteMinimale<=$qte) return true;
        }
        return false;
    }
    /**
     *  checkCategory
     *  Vérifie si cette règle correspond à cette categorie
     */
    public function checkCategory ($cat,$qte=1) {
        foreach ($this->_Categories as $c){
            if ($c->Id==$cat->Id&&$this->QuantiteMinimale<=$qte) return true;
        }
        return false;
    }
}