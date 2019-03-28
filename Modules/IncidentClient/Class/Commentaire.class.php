<?php

class Commentaire extends genericClass {

 public function Save(){

     $uid = $this->userCreate;
     $cli = Sys::getOneData('IncidentClient','Contact/UserId='.$uid);
     if($cli) $this->addParent($cli);

     $tech = Sys::getOneData('IncidentClient','Technicien/UserId='.$uid);
     if($tech) $this->addParent($tech);

     return parent::Save();
 }

}