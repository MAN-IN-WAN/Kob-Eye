<?php

class Horaire extends genericClass
{
    public function Save()
    {
        $adh = $GLOBALS["Systeme"]->getRegVars("VetoAdh");
        if ($adh){
            $this->addParent($adh);
        }
        return parent::Save();
    }
}