<?php

class Parc_Action extends genericClass
{

    public function Save($syncGestion = false){
        if(!$this->Titre){
            if($this->UserCrea == "ZZ"){
                $this->Titre = 'Communication Client';
            }else{
                $this->Titre = 'Communication Abtel';
            }
        }
        $tick = $this->getOneParent('Ticket');
        $contrat = $this->getOneParent('Contrat');

        if(!$contrat) {
            $ct = $tick->getOneParent('Contrat');
            if ($ct)
                $this->addParent($ct);
        }

        if(!empty($this->pj)){
            $this->Note .= PHP_EOL.PHP_EOL.'<hr>';
            $this->Titre .= '( Ajout d\'une pièce jointe )';
            $ext = pathinfo($this->pj,PATHINFO_EXTENSION);
            $img = array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF');
            if(in_array($ext,$img)){
                $this->Note .= '<div class="row uploadItem"><div class="col-md-5 uploadItemThumb"><img src="'.$this->pj.'.limit.250x40.'.$ext.'"></div><div class="col-md-7 uploadItemLink"><a href="'.$this->pj.'" target="_blank" title="Voir l\'image">Voir l\'image</a></div></div>';
            } else{
                $this->Note .= '<div class="row uploadItem"><div class="col-md-5 uploadItemThumb"><i class="icmn-file-empty2"></i></div><div class="col-md-7 uploadItemLink"><a href="'.$this->pj.'" target="_blank" title="Voir le fichier">Voir le fichier</a></div></div>';
            }
        }


        if($syncGestion){

            //TODO
        }


        return parent::Save();
    }

    public function Verify(){

        if(!empty($this->NumeroTicket)){
            $tick = Sys::getOneData('Parc','Ticket/Numero='.$this->NumeroTicket);
            if(!$tick) {
                $this->addError(array("Message"=>"Ticket introuvable dans la base du Parc"));
                return false;
            }

            $par = $this->getOneParent('Ticket');
            if($par && $par->Id != $tick->Id){
                $this->delParent($par);
            }
            $this->addParent($tick);
        } else{
            $paTi = $this->getOneParent('Ticket');
            if($paTi)
                $this->NumeroTicket = $paTi->Numero;
        }

        if(empty($this->DateCrea))
            $this->DateCrea = time();



        return parent::Verify();
    }

}