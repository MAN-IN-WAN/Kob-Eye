<?php
/**
 * Conversion d'un nombre quelconque en lettres.
 * 
 * @author Antoine MATTEI (a.mattei@free.fr)
 * @version 0.2
 */
class nuts {
  const DEBUG = FALSE;
  private $nb, $decSep, $unit;
  private $parts = array();
  private $separators =
      array(
        "fr-FR" => array(', ', '-', ' ', ' '),
        "en-EN" => array(', ', '-', ' ', ' '),
        "pt-PT" => array(' e ', ' e ', ' e ', ' e ')
      );
  private $units =
      array(
        "USD" => array(
          100,
          "fr-FR" => array('dollar', 'dollars', 'centime', 'centimes', '', ''),
          "pt-PT" => array('dollar', 'dollars', 'cêntimo', 'cêntimos', 'm', 'm'),
          "en-EN" => array('dollar', 'dollars', 'cent', 'cents', '')
        ),
        "EUR" => array(
          100,
          "fr-FR" => array('euro', 'euros', 'centime', 'centimes', '', ''),
          "pt-PT" => array('euro', 'euros', 'cêntimo', 'cêntimos', 'm', 'm'),
          "en-EN" => array('euro', 'euros', 'cent', 'cents', '', '')
        ),
        "t"  => array(
          1000,
          "fr-FR" => array('tonne', 'tonnes', 'kilo', 'kilos', '', ''),
          "pt-PT" => array('tonelada', 'toneladas', 'quilo', 'quilos', 'f', 'm'),
          "en-EN" => array('ton', 'tons', 'kilogram', 'kilograms', '', '')
        )
      );
  private $numbers =
      array(
        "fr-FR" => array(
          0 => array('zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'),
          1 => array('dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingts', 'quatre-vingt-dix'),
          2 => array('cent', 'cents'),
          3 => array('mille', 'mille'),						/* Règle 3 */
          6 => array('million', 'millions'),
          9 => array('milliard', 'milliards')
          ),
        "pt-PT" => array(
          0 => array('zero', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'),
          1 => array('dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'),
          2 => array('cem', 'cem', 'f'),
          3 => array('mil', 'mil', 'f'),
          6 => array('milhão', 'milhões', 'm'),
          9 => array('mil milhões', 'mil milhões', 'm')
        ),
        "en-EN" => array(
          0 => array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'),
          1 => array('ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'),
          2 => array('hundred', 'hundred'),
          3 => array('thousand', 'thousand'),								
          6 => array('million', 'millions'),
          9 => array('billion', 'billions')
        )
      );
  private $localExceptions =
      array(
        "fr-FR" => array(
          array("/dix-un/", "onze"),
          array("/dix-deux/", "douze"),
          array("/dix-trois/", "treize"),
          array("/dix-quatre/", "quatorze"),
          array("/dix-cinq/", "quinze"),
          array("/dix-six/", "seize"),
          array("/-un/", " et un"),							/* Règle 1 */
          array('/^et /', ''),
          array("/soixante-onze/", "soixante et onze"),		/* Règle 2 */
          array('/^-/', ''),
          array('/ zéro$/', ""),
          array("/-zéro/", ""),
          array("/cents /", "cent "),							/* Règle 4 */
          array('/cent et/', 'cent'),
          array('/cents et/', 'cents'),
          array('/-$/', ""),								
          array("/vingts-/", "vingt-"),						/* Règle 4 */
          array("/un cent/", "cent"),
          array("/^un mille/", "mille"),
          array("/cent millions/", "cents millions"),			/* Règle 5 */
          array("/cent milliards/", "cents milliards")		/* Règle 5 */
        ),		
        "pt-PT" => array(
          array('/^ e zero$/', 'zero'),
          array('/ zero$/', ''),
          array('/ e zero/', ''),
          array("/dez e um/", "onze"),
          array("/dez e dois/", "doze"),
          array("/dez e três/", "treze"),
          array("/dez e quatro/", "catorze"),
          array("/dez e cinco/", "quinze"),
          array("/dez e seis/", "dezasseis"),
          array("/dez e sete/", "dezassete"),
          array("/dez e oito/", "dezoito"),
          array("/dez e nove/", "dezenove"),
          array("/um e cem/", "cem"),
          array("/dois e cem/", "e duzentos"),
          array('/e duzentos e /', 'duzentos e '),
          array('/três e cem/', 'e trezentos'),
          array('/e trezentos e /', 'trezentos e '),
          array("/quatro e cem/", "e quatro centos"),
          array('/e quatro centos e /', 'quatro centos e '),
          array('/cinco e cem/', 'e quinhentos'),
          array('/e quinhentos e /', 'quinhentos e '),
          array("/seis e cem/", "e seiscentos"),
          array('/e seiscentos e /', 'seiscentos e '),
          array("/sete e cem/", "e setecentos"),
          array('/e setecentos e /', 'setecentos e '),
          array("/oito e cem/", "e oitocentos"),
          array('/e oitocentos e /', 'oitocentos e '),
          array("/nove e cem/", "e novecentos"),
          array('/e novecentos e /', 'novecentos e '),
          array('/cem e /', 'cento e '),
          array('/ e$/', ''),
          array('/^e$/', ''),
          array('/um mil$/', 'mil')
          ),		
        "en-EN" => array(
          array("/ten-one/", "eleven"),
          array("/ten-two/", "twelve"),
          array("/ten-three/", "thirteen"),
          array("/ten-four/", "fourteen"),
          array("/ten-five/", "sixteen"),
          array("/ten-six/", "sixteen"),
          array("/ten-seven/", "seventeen"),
          array("/ten-height/", "eighteen"),
          array("/ten-nine/", "nineteen"),
          array('/^-/', ''),
          array("/-zero/", ""),
          array("/hundred /", "hundred and "),
          array('/hundred-/', 'hundred and '),
          array('/hundred and thousand/', 'hundred thousand'),
          array("/ and zero/", "")
        )
      );
  private $partExceptions = 
      array(
        "fr-FR" => array(
          array("/ zéro/", ""),
          array("/[[:blank:]].zéro/", ""),
          array('/^-/', ''),
          array('/ -/', ' '),
          array('/million$/', "million de"),
          array('/millions$/', "millions de"),
          array('/milliard$/', "milliard de"),
          array('/milliards$/', "milliards de")
        ),
        "pt-PT" => array(
          array('/ zero/', ""),
          array('/^e /', ''),
          array('/milhões$/', 'milhões de'),
          array('/milhão$/', 'milhão de')
          ),
        "en-EN" => array(
          array("/ zero/", ""),
          array("/[[:blank:]].zero/", "")
        )
      );
  private $genderExceptions = 
      array(
        "fr-FR" => array(),
        "pt-PT" => array(
          array('/um/', 'uma'),
          array('/dois/', 'duas'),
          array('/entos/', 'entas')
        ),
        "en-EN" => array()
      );
  private $globalExceptions = 
      array(
        "fr-FR" => array(
          array("/de e/", "d'e")
        ),
        "pt-PT" => array(
        ),
        "en-EN" => array()
      );
                        
  /**
   * @param real $nb Nombre à convertir.
   * @abstract Formats acceptés : 1234 | 12,34 | 12.34 | 12 345.
   * Un seul caractère non numérique sera accepté et considéré comme le séparateur décimal en entrée.
   * @example
   * $obj = new nuts("12345.67", "EUR");
   * $text = $obj->convert("fr-FR");
   * $nb = $obj->getFormated(" ", ",");
   */
  function __construct($nb, $unit){
    // Nettoyages.
    $this->nb = str_replace(' ', '', $nb);						// Suppession de tous les espaces.
    $this->nb = preg_replace("/[A-Za-z]/", "", $this->nb);	
    $this->nb = preg_replace("/^0+/", "", $this->nb);			// Suppression des 0 de tête.
    
    if ($this->nb == '') $this->nb = '0';
    
    $this->unit = $unit;
    
    // Séparateur.
    $this->decSep = preg_replace("/[0-9]/", '', $this->nb);		// On ne garde que ce qui n'est pas numérique
    $this->decSep = substr($this->decSep, -1);					// et on prend le dernier des caractères restants.
    
    // Partie entière et partie décimale.
    if ($this->decSep == '') {
      // Pas de partie décimale.
      $this->parts[] = $this->nb;
    } else {
      // Ajout d'un 0 quand il manque devant le séparateur décimal.
      // Noter le double \ pour échapper le séparateur . qui est un opérateur dans les expressions régulières.
      $this->nb = preg_replace("/^\\" . $this->decSep . "/", "0" . $this->decSep, $this->nb);	
    
      $this->parts = explode($this->decSep, preg_replace("/^[0-9] " . $this->decSep . "/", '', $this->nb));
      
      // Nettoyage partie décimale.
      if ($this->parts[1] == ''){
        unset($this->parts[1]);
        $this->decSep = '';
      } else {
        // On coupe la partie décimale au nombre de caractères en fonction du rapport entre unité et sous-unité.
        $this->parts[1] = substr($this->parts[1], 0, strlen($this->units[$this->unit][0]) - 1);
        
        // On bourre avec des 0 de fin.
        while (strlen($this->parts[1]) < strlen($this->units[$this->unit][0]) - 1){
          $this->parts[1] .= '0';
        }
      }
    }
    
    if (nuts::DEBUG) echo "construct : [" . $this->nb . "]";
  }
  
  /**
   * 
   */
  function __destruct(){
  }
  
  /**
   * Module de traduction d'un groupe de 3 digits.
   * 
   * @param string $group Groupe de 3 digits.
   * @param integer $unit Indice de l'unité concernée par $group.
   * @param string $language Langue demandée.
   * @param integer $gender Indice du genre dans le tableau (4 pour l'unité de mesure, 5 pour la sous-unité).
   */
  private function getThree($group, $unit, $language, $gender){
    $return = "";
    
    if ($group == '') $group = 0;
    
    // Centaines.
    if ($group >= 100) {
      $hundreds = floor($group / 100);;
      if ($hundreds == 1) {
        $return .= $this->numbers[$language][0][$hundreds] . $this->separators[$language][3] . $this->numbers[$language][2][0];
      } else {
        $return .= $this->numbers[$language][0][$hundreds] . $this->separators[$language][3] . $this->numbers[$language][2][1];
      }
      
      $tens = $group % 100;		// On enlève les centaines.
    } else {
      $tens = $group;
    }
    
    // Dizaines et unités.
    if ($tens >= 10) {
      $return .= $this->separators[$language][2] . $this->numbers[$language][1][floor($tens / 10) - 1] . $this->separators[$language][1] . $this->numbers[$language][0][$tens % 10];
    } else {
      $return .= $this->separators[$language][1] . $this->numbers[$language][0][(int) $tens];
    }
    
    if ($unit < 3){
      // [0..999].
    } else {
      // 0, 1 ou n ?
      if ($group == 0) {
      } elseif ($group == 1){
        $return .= ' ' . $this->numbers[$language][$unit][0];
      } else {
        $return .= ' ' . $this->numbers[$language][$unit][1];
      }
    }
    
    if (nuts::DEBUG) echo "<br>local a [" . $return . "] ";
    // Exceptions.
    for ($i = 0; $i < count($this->localExceptions[$language]); $i++){
      $return = trim(preg_replace($this->localExceptions[$language][$i][0], $this->localExceptions[$language][$i][1], $return));
      //echo " $i [" . $return . "]";
    }
    
    if (nuts::DEBUG) echo "<br>local b $unit [" . $return . "] ";
    // Exceptions de genre.
    if (   ($this->units[$this->unit][$language][$gender] == 'f' && $unit < 3)
      || ($this->units[$this->unit][$language][$gender] == 'f' && $this->numbers[$language][$unit][2] == 'f')) {
      // L'unité (tonelada) peut être de genre féminin mais les milliers ou millards peuvent être invariables en genre.
      for ($i = 0; $i < count($this->genderExceptions[$language]); $i++){
        $return = trim(preg_replace($this->genderExceptions[$language][$i][0], $this->genderExceptions[$language][$i][1], $return));
      }
    }
    
    if (nuts::DEBUG) echo "<br>local c [" . $return . "] ";
    return $return;
  }
  
  /**
   * Formate la sortie numérique avec séparateurs décimal et des milliers souhaités.
   * 
   * @param string $tSep Séparateur des milliers, en sortie.
   * @param string $dSep Séparateur décimal, en sortie.
   */
  function getFormated($tSep = '', $dSep = '.', $language){
    if ($tSep == $dSep) {
      // Les 2 séparateurs ne peuvent être identiques > valeurs par défaut.
      $tSep = '';
      $dSep = '.';
    }
    
	$unit = $this->unit;
	if(isset($language)) $unit = $this->units[$this->unit][$language][$this->nb > 1 ? 1 : 0]; 
		
    $return = $this->format($this->parts[0], $tSep);
    if ($this->decSep == '') {
      // Pas de partie décimale.
      $return .= ' ' . $unit;
    } else {
      $return .= $dSep . $this->parts[1] . ' ' . $unit;
    }
    
    return $return;
  }
  
  /**
   * Formatage par groupe de 3 digits avec séparateur.
   *
   * @param string $nb Nombre à formater.
   * @param string $sep Séparateur de milliers (espace, virgule).
   * @todo Il faudra s'assurer que le séparateur n'est pas le même que le séparateur décimal qui a été identifié automatiquement.
   */
  private function format($nb, $sep){
    $nb = strrev($nb);
    $n = 0;
    for ($i = 2; $i < strlen($nb); $i++){
      if ($i % 3 == 0){
        $nb = substr($nb, 0, $i + $n) . $sep . substr($nb, $i + $n);
        $n++;
      }
    }
    return strrev(trim($nb));
  }
  
  /**
   * Effectue la conversion en texte de la partie entière ou décimale.
   * 
   * @param string $nb Partie entière ou décimale.
   * @param string $language Langue à utiliser pour la sortie.
   * @param integer $gender Indice du genre dans le tableau (4 pour l'unité de mesure, 5 pour la sous-unité).
   */
  private function part($nb, $language, $gender){
    $return = "";
    
    // Décodage par blocs de 3.
    $groups = explode(' ', $this->format($nb, ' '));
    for ($i = 0; $i < count($groups) ; $i++){
      $return .= $this->getThree($groups[$i], (count($groups) - 1 - $i) * 3, $language, $gender) . ' ';
    }
    $return = trim($return);

    if (nuts::DEBUG) echo "<br>part a : [" . $return . "]";
    // Exceptions.
    for ($i = 0; $i < count($this->partExceptions[$language]); $i++){
      $return = trim(preg_replace($this->partExceptions[$language][$i][0], $this->partExceptions[$language][$i][1], $return));
    }
    
    if (nuts::DEBUG) echo "<br>part b : [" . $return . "]";
    return $return;
  }

  /**
   * Demande la conversion en texte de chaque partie et ajoute les unités.
   * 
   * @param string $language Langue à utiliser pour la sortie.
   */
  function convert($language){
    // Partie entière.
    $return = $this->part($this->parts[0], $language, 4) . " ";
    $return .= ($this->parts[0] > 1) ? $this->units[$this->unit][$language][1] : $this->units[$this->unit][$language][0];
    
    // Exceptions.
    for ($i = 0; $i < count($this->globalExceptions[$language]); $i++){
      $return = trim(preg_replace($this->globalExceptions[$language][$i][0], $this->globalExceptions[$language][$i][1], $return));
    }
    
    // Partie décimale.
    if (count($this->parts) == 2){
      $return .= $this->separators[$language][0] . $this->part($this->parts[1], $language, 5) . " ";
      $return .= ($this->parts[1] > 1) ? $this->units[$this->unit][$language][3] : $this->units[$this->unit][$language][2];
    }
    
    if (nuts::DEBUG) echo "<br>";
    return $return;
  }
}
