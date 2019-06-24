<?php
class TypeQuestion extends genericClass {
    var $_params = Array(
        'min_word_length' => 4,
        'min_word_occur' => 1,

        'min_2words_length' => 2,
        'min_2words_phrase_length' => 2,
        'min_2words_phrase_occur' => 1,

        'min_3words_length' => 2,
        'min_3words_phrase_length' => 5,
        'min_3words_phrase_occur' => 1
    );
    public function getAllAnswers() {
        if (isset($this->_txt)) return $this->_txt;
        $txt='';
        $reps = Sys::getData('Formation', 'TypeQuestion/'.$this->Id.'/Reponse');
        foreach ($reps as $r){
            $txt.=json_decode($r->Valeur).' ';
        }
        $this->_txt = $txt;
        return $txt;
    }
    public function getKeywords() {
        $Out= Array();

        // Inclusion de la classe
        include_once("Class/Utils/autokeyword.class.php");

        $T = $this->getAllAnswers();
        if (empty($T)) return Array();
        //Extraction des mots clefs
        $params = $this->_params;
        $params['content'] = $T; //page content

        $keyword = new autokeyword($params, "UTF-8");
        //EM-20150218 Probleme de génération de mots clefs
        $mcs = $keyword->get_keywords();
        if (is_array($mcs))foreach ($mcs as $Mc=>$occ){
            if ($Mc!=""){
                $Nb = false;//Sys::getCount("Systeme","TagBlackList/Titre=".$Mc);
                if (!$Nb) $Out[$Mc] = $occ;
            }
        }
        return $Out;
    }

    public function getTwoKeywords() {
        $Out= Array();

        // Inclusion de la classe
        include_once("Class/Utils/autokeyword.class.php");

        $T = $this->getAllAnswers();
        if (empty($T)) return Array();
        //Extraction des mots clefs
        $params = $this->_params;
        $params['content'] = $T; //page content

        $keyword = new autokeyword($params, "UTF-8", false, true, false);
        //EM-20150218 Probleme de génération de mots clefs
        $mcs = $keyword->parse_2words();
        if (is_array($mcs))foreach ($mcs as $Mc=>$occ){
            if ($Mc!=""){
                $Nb = false;//Sys::getCount("Systeme","TagBlackList/Titre=".$Mc);
                if (!$Nb) $Out[$Mc] = $occ;
            }
        }
        return $Out;
    }
    public function getThreeKeywords() {
        $Out= Array();

        // Inclusion de la classe
        include_once("Class/Utils/autokeyword.class.php");

        $T = $this->getAllAnswers();
        if (empty($T)) return Array();
        //Extraction des mots clefs
        $params = $this->_params;
        $params['content'] = $T; //page content

        $keyword = new autokeyword($params, "UTF-8", false, false, true);
        //EM-20150218 Probleme de génération de mots clefs
        $mcs = $keyword->parse_3words();
        if (is_array($mcs))foreach ($mcs as $Mc=>$occ){
            if ($Mc!=""){
                $Nb = false;//Sys::getCount("Systeme","TagBlackList/Titre=".$Mc);
                if (!$Nb) $Out[$Mc] = $occ;
            }
        }
        return $Out;
    }

    function Delete() {
        $questions = $this->getChildren('TypeQuestionValeur');
        foreach ($questions as $q) $q->Delete();
        $questions = $this->getChildren('Donnee');
        foreach ($questions as $q) $q->Delete();
        parent::Delete();
    }
}