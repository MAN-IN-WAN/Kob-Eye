<?php
class Bandeau extends genericClass
{
    public function Save()
    {
        $adh = $GLOBALS["Systeme"]->getRegVars("VetoAdh");
        if ($adh){
            $this->addParent($adh);
        }
        return parent::Save();
    }

    public function Delete()
    {
        $obj = Sys::getOneData('Vetoccitan','Bandeau/'.$this->Id."","","","","","","",true);
        $child = Sys::getData('Vetoccitan',"Bandeau/".$obj->Id."/Media","","","","","","",true);
        foreach($child as $items){
            $items->Delete();
        }
        return parent::Delete();
    }

    public function AjoutBandeau($params){
        $step = 0;
        if(!empty($params['step']))
            $step = $params['step'];
        if(!empty($params['type']))
            $step = $params['type'];

        switch($step){
            case 1 : //Evenements sur toute une journée avec  jours d'ouverture
                return array (
                    'template'=>"ajoutBandeau",
                    'step'=>3,
                    'callNext'=>array (
                        'nom'=>'AjoutBandeau',
                        'title'=>'Choix du bandeau à dupliquer'
                    ),
                    'funcTempVars' => array(
                        'step'=> $step
                    )
                );
                break;
            case 3 : //Evenements sur toute une journée avec  jours d'ouverture
                $objet = Sys::getOneData("Vetoccitan","Bandeau/".$params['bandoId'],"","","","","","",true);
                $newBando = $objet->getClone();

                if($newBando->Save()){
                    $media = Sys::getOneData("Vetoccitan","Bandeau/".$params['bandoId']."/Media","","","","","","",true);
                    $newMedia = $media->getClone();
                    $newMedia->addParent($newBando);
                    if ($newMedia->Save()){
                        return array("data"=>"Le bandeau à été cloné avec succès ! Il ne vous reste plus qu'à le modifier selon vos souhaits.",
                                    "callBack" => array("nom"=>"VetoccitanBandeau.store.refresh",
                                                        "args"=>array())
                        );
                    }else{
                        $newBando->Delete();
                        return array (
                            'template'=>"ajoutBandeau",
                            'step'=>3,
                            'callNext'=>array (
                                'nom'=>'AjoutBandeau',
                                'title'=>'Choix du bandeau à dupliquer'
                            ),
                            'funcTempVars' => array(
                                'step'=> 1
                            ),
                            'error' => "Oups il y a eu une erreur de média! Veuillez réessayer."
                        );
                    }

                }else{
                    return array (
                        'template'=>"ajoutBandeau",
                        'step'=>3,
                        'callNext'=>array (
                            'nom'=>'AjoutBandeau',
                            'title'=>'Choix du bandeau à dupliquer'
                        ),
                        'funcTempVars' => array(
                            'step'=> 1
                        ),
                        'error' => "Oups il y a eu une erreur de bandeau! Veuillez réessayer."
                    );
                }
                break;
            case 2 : //Evenements sur toute une journée avec  jours d'ouverture

                return array (
                    'template'=>"ajoutBandeau",
                    'step'=>4,
                    'callNext'=>array (
                        'nom'=>'AjoutBandeau',
                        'title'=>'Définition des paramètres '
                    ),
                    'funcTempVars' => array(
                        'step'=> $step
                    ),
                    "callBack" => array("nom"=>"initBandeau",
                        "args"=>array())
                );
                break;
            case 4 :
//                print_r($params);

                $newBando = genericClass::createInstance("Vetoccitan","Bandeau");

                foreach ($params['values'] as $k=>$p){
                    $newBando->{$k} = $p;
                }
//                print_r($newBando);


                if($newBando->Save()){
                    $newMedia = genericClass::createInstance("Vetoccitan","Media");
                    $newMedia->Image = $params['values']["Image"];
                    $newMedia->Titre = "Bandeau ".$newBando->Id;
                    $newMedia->addParent($newBando);
                    if ($newMedia->Save()){
                        return array("data"=>"Le bandeau à été créé avec succès !",
                            "callBack" => array("nom"=>"VetoccitanBandeau.store.refresh",
                                "args"=>array())
                        );
                    }else{
                        $newBando->Delete();
                        return array (
                            'template'=>"ajoutBandeau",
                            'step'=>4,
                            'callNext'=>array (
                                'nom'=>'AjoutBandeau',
                                'title'=>'Définition des paramètres'
                            ),
                            'funcTempVars' => array(
                                'step'=> 2
                            ),
                            'error' => "Oups il y a eu une erreur de média! Veuillez réessayer."
                        );
                    }

                }else{
                    return array (
                        'template'=>"ajoutBandeau",
                        'step'=>4,
                        'callNext'=>array (
                            'nom'=>'AjoutBandeau',
                            'title'=>'Définition des paramètres'
                        ),
                        'funcTempVars' => array(
                            'step'=> 2
                        ),
                        'error' => "Oups il y a eu une erreur de bandeau! Veuillez réessayer."
                    );
                }
                break;
            //-------------------------------------------------------------------
            default: //Initialisation
                return array (
                    'template'=>"ajoutBandeau",
                    'step'=>1,
                    'callNext'=>array (
                        'nom'=>'AjoutBandeau',
                        'title'=>'Réglages'
                    ),
                    'funcTempVars' => array(
                        'step'=> $step
                    )
                );
        }
    }
}