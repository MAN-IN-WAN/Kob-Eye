<?php
class Equipe extends genericClass{
    function Delete() {
        // suppression de toutes les réponses
        $t = $this->getChildren('Reponse');
        foreach ($t as $r) {
            $r->Delete();
        }

        parent::Delete();
    }
}