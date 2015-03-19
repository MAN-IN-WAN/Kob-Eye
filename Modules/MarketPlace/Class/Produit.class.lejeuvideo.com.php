<?php
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class Produit extends genericClass {

	/**
	 * Enregistrement d'un produit
	 * -> Check Reference
	 * -> Enregistre les mots clés
	 * @return	void
	 */
	public function Save() {
		parent::Save();

		$this->SaveRef();
		$this->SaveKeywords();

		parent::Save();
	}

	/**
	 * Création d'une référence si c'est un nouveau produit
	 * @return	void
	 */
	private function SaveRef() {
		if(empty($this->Reference)) {
			$ref = $this->Id;
			while(strlen($ref) < 5) $ref = '0' . $ref;;
		 	$this->Reference = 'PR' . $ref;
		}
	}

	/**
	 * Enregistre les mots-clés pour ce produit
	 * @return	void
	 */
	function SaveKeywords() {
		$Mcs = $this->genKeyWords();
		if(is_array($Mcs)) {
			foreach($Mcs as $Mc) {
				//On verifie d'abord si il n'existe pas dans la base des mots clefs en tant que canonique
				$Tab2 = $this->storproc("Boutique/MotClef/Canon=".Utils::Canonic($Mc));
				if($Tab2[0]) {
					// Il existe déjà, il suffit de lui rattacher un nouveau parent
					//$req = $this->storproc('Boutique/MotClef/' . $Tab2[0]['Id']);
					$Mcf = genericClass::createInstance('Boutique', $Tab2[0]);
				}else {
					// Il n'existe pas, on le créé
					$Mcf = new genericClass("Boutique","MotClef");
					$Mcf->Set("Nom",$Mc);
					$Mcf->Set("Canon",Utils::Canonic($Mc));
				}
				$Mcf->AddParent("Boutique/Produit/".$this->Id);
				$Mcf->Save();
			}
		}
	}


	/**
	 * Génère les keywords à partir de tous les champs textuels
	 * @return	Tableau de mots clés
	 */
	private function genKeyWords() {
		// Inclusion de la classe
		include_once("Class/Lib/class.autokeyword.php");

		// Recensement des champs textuels
		$Props = $this->Proprietes(false,true);
		$T="";
		if (is_array($Props)) foreach ($Props as $p){
			//Verification de la valeur
			switch ($p["Type"]) {
				case "titre":
				case "text":
				case "varchar":
					// Type text : on concatene
					$T .= ' ' . $this->$p["Titre"];
				break;
			}
		}
		//Extraction des mots clefs
		$params['content'] = $T; //page content
		//set the length of keywords you like
		$params['min_word_length'] = 3;  //minimum length of single words
		$params['min_word_occur'] = 1;  //minimum occur of single words
		
		$params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
		$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
		$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase
		
		$params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
		$params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
		$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase
		
		$keyword = new autokeyword($params, "UTF-8");
		$Result = explode(", ",$keyword->parse_words());
		if (is_array($Result))foreach ($Result as $Mc){
			if ($Mc!=""){
				$Tab = $this->storproc("Boutique/BlackList/Titre=".$Mc,"",0,1,"","","COUNT(DISTINCT(m.Id))");
				if ($Tab[0]["COUNT(DISTINCT(m.Id))"]==0) $Out[] = $Mc;
			}
		}
		return $Out;
	}

	/**
	 * Retourne le nombre de références "neuves" pour ce produit
	 * TODO Vérifier que ce soit bien Etat=Neuf
	 * @return	Nombre
	 */
	public function getNbNeufs() {
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference/Etat=Neuf');
		return is_array($refs) ? sizeof($refs) : 0;
	}

	/**
	 * Retourne le nombre de références "occasion" pour ce produit
	 * TODO Vérifier que ce soit bien Etat!=Neuf
	 * @return	Nombre
	 */
	public function getNbOccasions() {
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference/Etat!=Neuf');
		return is_array($refs) ? sizeof($refs) : 0;
	}
	
	/**
	 * Retourne le prix moyen pondéré d'achat
	 * -> La moyenne à laquelle ce produit se vend
	 * TODO	Attention, ne compter que les références qui ont effectivement été vendues
	 * @return	Somme (ou -1 si il n'y a pas encore eu de vente)
	 */
	function getPmpa() {
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference');
		$nbRefs = count($refs);
		if($nbRefs == 0) return -1;
		$total = 0;
		for($i=0; $i<$nbRefs; $i++) $total += $refs[$i]['Tarif'];
		return round($total / $nbRefs, 2);
	}

	/**
	 * Retourne le prix minimum d'une référence de ce produit
	 * TODO	Attention, ne compter que les références qui sont actuellement en vente
	 * @return	Prix (ou -1 si il n'y a pas encore eu de vente)
	 */
	function PrixAPartirDe() {
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference');
		$nbRefs = count($refs);
		$prixMini = -1;
		for($i=0; $i<$nbRefs; $i++) :
			if($refs[$i]['Tarif'] < $prixMini or $prixMini == -1) :
				$prixMini = $refs[$i]['Tarif'];
			endif;
		endfor;
		return $prixMini;
	}


	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return $GLOBALS['Systeme']->Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

	/**************************************************
	***************************************************

							Top 10

	***************************************************
	***************************************************/

	/**
	 * Returne les 10 produits les plus vendus
	 * @param	string	console particulière
	 * @return	tableau des produits
	 */
	public function getTop10Ventes( $console = '' ) {
		$path = dirname(dirname(dirname(dirname(__FILE__)))).DS.'Data'.DS.'Boutique';
		$file = 'top10'.$console.'.dat';

		// Fichier cache, on ne fait la requete qu'une seule fois par jour sinon on renvoi le cache
		if(is_file($path.DS.$file) and (filemtime($path.DS.$file)>time() - 86400))
		return unserialize(file_get_contents($path.DS.$file));

		// Top 10 des ventes
		$top = array();
		$req = (empty($console)) ? 'Boutique/Produit' : 'Boutique/Categorie/' . $console . '/Categorie/*/Produit';
		$produits = $this->storproc($req, '', '0', '10', 'DESC', 'Ventes');
		foreach($produits as $p) $top[] = $this->getDetailsProduit($p);

		// Ecriture fichier cache
		if(!is_dir($path)) mkdir($path);
		file_put_contents($path.DS.$file, serialize($top));

		return $top;
	}

	/**
	 * Returne les 10 produits les plus vendus
	 * @param	string	console particulière
	 * @return	tableau des produits
	 */
	public function getTop10Nouveautes( $console = '' ) {
		$path = dirname(dirname(dirname(dirname(__FILE__)))).DS.'Data'.DS.'Boutique';
		$file = 'top10nouveautes'.$console.'.dat';

		// Fichier cache, on ne fait la requete qu'une seule fois par jour sinon on renvoi le cache
		if(is_file($path.DS.$file) and (filemtime($path.DS.$file)>time() - 86400))
		return unserialize(file_get_contents($path.DS.$file));

		// Top 10 des ventes
		$top = array();
		$req = (empty($console)) ? 'Boutique/Produit' : 'Boutique/Categorie/' . $console . '/Categorie/*/Produit';
		$produits = $this->storproc($req, '', '0', '10', 'DESC', 'Annee');
		foreach($produits as $p) $top[] = $this->getDetailsProduit($p);

		// Ecriture fichier cache
		if(!is_dir($path)) mkdir($path);
		file_put_contents($path.DS.$file, serialize($top));

		return $top;
	}

	/**
	 * Récupère le détail d'un produit pour affichage dans le top 10
	 * @param	array	Détail du produit
	 * @return	Objet détaillé
	 */
	private function getDetailsProduit( $p ) {
		$categConsole = '';
		$categEnCours = '';
		$categorieProduit = $this->storproc('Boutique/Categorie/*/Categorie/Produit/' . $p['Id']);
		foreach($categorieProduit as $cp) :
			if(empty($categConsole)) $categConsole .= $cp['Nom'];
			$categEnCours .= '/' . $cp['Url'];
		endforeach;
		// Construction Objet
		$prod = new stdClass();
		$prod->Nom = $p['Nom'];
		$prod->Description = substr($p['Description'], 0, 40);
		$prod->Console = $categConsole;
		$prod->Url = '/GamesAvenue' . $categEnCours . '/Produit/' . $p['Url'];
		return $prod;
	}

	/**************************************************
	***************************************************

					Connexion Socket

	***************************************************
	***************************************************/

	function socketUpdateAll() {
		/* Intitulé exact des consoles sur le site lejeuvideo.com */
		$consoles = array();
		$res = $this->storproc('Boutique/ConsoleLJV');
		foreach($res as $consLJV) $consoles[] = $consLJV['Nom'];
		$socketConsoleId = isset($_GET['socketConsole']) ? array_search($_GET['socketConsole'], $consoles) : 0;
		$socketConsole = $consoles[$socketConsoleId];
		// Trace
		file_put_contents(dirname(__FILE__).'/trace.txt', "\r\n\r\nconsole $socketConsole \r\n", FILE_APPEND);
		// Exécution de l'update pour cette console : on note les headers AVANT et APRES
		$size = sizeof(headers_list());
		$this->socketUpdateJeux($socketConsole);
		$size2 = sizeof(headers_list());
		// Il nous reste des consoles ET on a pas redirigé par rapport à la limite
		if($socketConsoleId < sizeof($consoles) - 1 and $size == $size2) {
			header('Location: ' . $_SERVER['REDIRECT_URL'] . '?socketConsole=' . $consoles[$socketConsoleId + 1]);
			exit;
		}
	}


	/** 
	 * Met à jour la liste des genres
	 * @return	void
	 */ 
	function socketUpdateGenres() {
		$genres = $this->socketGetGenres();
		foreach($genres as $firstLevel => $childs) :
			// Genre de premier niveau
			$res = $this->storproc('Boutique/Genre/Nom=' . addslashes($firstLevel) . '&GenreId=0');
			if(!$res) {
				$obj = new genericClass('Boutique', 'Genre');
				$obj->Set('Nom', $firstLevel);
				$obj->Save();
				// Maintenant il existe
				$res = $this->storproc('Boutique/Genre/Nom=' . addslashes($firstLevel) . '&GenreId=0');
			}
			// Genres de second niveau
			if(!empty($childs)) {
				foreach($childs as $secondLevel) :
					$res2 = $this->storproc('Boutique/Genre/Nom=' . addslashes($firstLevel) . '/Genre/Nom=$secondLevel');
					if(!$res2) {
						$obj = new genericClass('Boutique', 'Genre');
						$obj->Set('Nom', $secondLevel);
						$obj->AddParent('Boutique/Genre/' . $res[0]['Id']);
						$obj->Save();
					}
				endforeach;
			}
		endforeach;
	}

	/**
	 * Récupère les genres sur le site lejeuvideo.com
	 * @return	La liste sous forme
	 * 	array(
	 * 		'Action' => array('Aventure', 'Course'...)
	 * 		'Adresse' => array()
	 * 		...
	 * 	)
	 */
	private function socketGetGenres() {
		// Récupération des genres
		$url = 'http://www.lejeuvideo.com/game_browser.php';
		$content = utf8_encode(file_get_contents($url));
		preg_match_all('/<a href="catalogue\/style\/(.*?)>(.*?)<\/a>/', $content, $matches);
		// Structure genre  / sous-genres
		$genres = array();
		foreach($matches[2] as $genre) :
			// Exception
			if($genre == 'N/C') $genre = 'Autres';
			// Séparation
			$arr = explode('/', $genre);
			$genre1 = @$this->socketCleanGenre($arr[0]);
			$genre2 = @$this->socketCleanGenre($arr[1]);
			if(empty($genre2)) {
				if(!isset($genres[$genre1])) {
					$genres[$genre1] = array();
				}
			}
			else {
				$genres[$genre1][] = $genre2;
			}
		endforeach;
		return $genres;
	}

	/**
	 * Récupère l'ID d'un genre à partir de son intitulé
	 * @param	Genre sous forme de chaine
	 * @return	L'ID du genre (ou -1 s'il n'est pas trouvé)
	 */
	private function getGenreId( $str ) {
		// Exception
		if($str == 'N/C') $str = 'Autres';
		// Séparation
		$arr = explode('/', $str);
		$genre1 = @$this->socketCleanGenre($arr[0]);
		$genre2 = @$this->socketCleanGenre($arr[1]);
		if(empty($genre2)) $req = 'Boutique/Genre/Nom=' . addslashes($genre1) . '&GenreId=0';
		else $req = 'Boutique/Genre/Nom=' . addslashes($genre1) . '/Genre/Nom=' . addslashes($genre2);
		$res = $this->storproc($req);
		if($res) return $res[0]['Id'];
		else return -1;
	}

	/**
	 * Gère les exceptions au niveau des genres
	 * @param	Genre à uniformiser
	 * @return	Genre OK
	 */
	private function socketCleanGenre( $str ) {
		$str = strtolower(stripslashes(trim($str)));
		$str = str_replace('&eacute;', 'é', $str);
		$str = str_replace('&ocirc;', 'ô', $str);
		$str = str_replace('&amp;', '&', $str);
		if($str == 'jeu de rôles') $str = 'jeu de rôle';
		if($str == 'point & click') $str = 'point and click';
		if($str == 'point&click') $str = 'point and click';
		if($str == "shoot 'em up") $str = "shoot'em up";
		if($str == "puzzle-game") $str = "puzzle game";
		$str = ucwords($str);
		return $str;
	}

	/**
	 * Mise à jour des jeux pour une console
	 * @param	Console
	 * @return	void
	 */
	function socketUpdateJeux( $console ) {
		$jeux = $this->socketGetJeux($console);
		$catid = $this->getCategorieId($console);
		foreach($jeux as $jeu) :
			$jeu['GenreId'] = $this->getGenreId($jeu['Genre']);
			$jeu['CategorieId'] = $catid;
			$req = 'Boutique/Categorie/' . $jeu['CategorieId'] . '/Produit/Nom=' . addslashes($jeu['Titre']);
			$produit = $this->storproc($req);
			if($produit) {
				// Le jeu existe déjà on le charge pour le mettre à jour
				$obj = genericClass::createInstance('Boutique', $produit[0]);
				file_put_contents(dirname(__FILE__).'/trace.txt', $jeu['Titre'] . " mis a jour\r\n", FILE_APPEND);
			}
			else {
				// Le jeu n'existe pas on l'ajoute
				$obj = genericClass::createInstance('Boutique', 'Produit');
				$obj->AddParent('Boutique/Genre/' . $jeu['GenreId']);
				$obj->AddParent('Boutique/Categorie/' . $jeu['CategorieId']);
				// Gestion de l'image
				if(!empty($jeu['Image'])) {
					$name = 'Home/ProduitsImg/' . fileDriver::checkName($jeu['Titre']) . '-' . time() . '.jpg';
					$bits = file_get_contents($jeu['Image']);
					file_put_contents($name, $bits);
					$jeu['Image'] = $name;
					$obj->Set('Image', $jeu['Image']);
				}
				// Champs à ne prendre qu'à la création
				$obj->Set('Note', $jeu['Note']);
				file_put_contents(dirname(__FILE__).'/trace.txt', $jeu['Titre'] . " ajouté\r\n", FILE_APPEND);
			}
			// Enregistrement des champs
			$obj->Set('Nom', stripslashes($jeu['Titre']));
			$obj->Set('Editeur', $jeu['Editeur']);
			$obj->Set('Description', $jeu['Description']);
			$obj->Set('Annee', strtotime($jeu['Annee']));
			$obj->Set('Joueur', $jeu['NbJoueurs']);
			$obj->Set('Age', $jeu['Age']);
			$obj->Save();
			// Enregistrement des photos uniquement si on n'en a pas encore
			$req = 'Boutique/Produit/' . $obj->Id . '/Photo';
			$photo = $this->storproc($req);
			if(!$photo) {
				foreach($jeu['Images'] as $k => $img) {
					$big = str_replace('miniature/', '', $img);
					$name = 'Home/GalerieImg/' . fileDriver::checkName($jeu['Titre']) . '-' . ($k + 1)  . '-' . time() . '.jpg';
					$bits = file_get_contents($big);
					file_put_contents($name, $bits);
					$objImg = genericClass::createInstance('Boutique', 'Photo');
					$objImg->Set('Nom', $jeu['Titre'] . ' - Image ' . $k);
					$objImg->Set('Image', $name);
					$objImg->AddParent('Boutique/Produit/' . $obj->Id);
					$objImg->Save();
				}
			}
		endforeach;
	}

	/**
	 * Retourne la liste des jeux d'une console avec le détail à chaque fois
	 * @param	Console
	 * @return	Liste des jeux
	 */
	private function socketGetJeux( $console ) {
		// Récupération des jeux
		$url = 'http://www.lejeuvideo.com/catalogue_jeux_support/1/' . $console . '.html';
		$content = utf8_encode(file_get_contents($url));
		preg_match_all('/<TD id="texte_jeu_tableau">\s*<a href="(.*?)">(.*?)<\/a>/', $content, $matches);
		// Construction du résultat
		$jeux = array();
		$nbJeux = sizeof($matches[0]);
		$socketStart = !isset($_GET['socketStart']) ? 0 : $_GET['socketStart'];
		$limit = 30;
		file_put_contents(dirname(__FILE__).'/trace.txt', "start " . $socketStart . "\r\n", FILE_APPEND);
		for($i=$socketStart; $i<$nbJeux; $i++) {
			// Sortie
			if(is_file(dirname(__FILE__).'/stop.txt')) die;
			// Redirection si limite atteinte
			if($i == ($socketStart + $limit)) {
				header('Location: ' . $_SERVER['REDIRECT_URL'] . '?socketConsole=' . $console . '&socketStart=' . $i);
				break;
			}
			$jeu = array();
			// Détails directement accessibles
			$jeu['Console'] = $console;
			$jeu['Titre'] = $this->socketCleanJeu($matches[2][$i]);
			$jeu['Url'] = $matches[1][$i];
			// Autre détails : page détail du jeu
			$infosComp = @utf8_encode(file_get_contents($jeu['Url']));
			// Editeur
			preg_match('/<TD id="texte_a_propos_gauche">\s*Editeur\s*<\/TD>\s*<TD id="texte_a_propos_droite">\s*<a href="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['Editeur'] = $details[2];
			// Genre
			preg_match('/<TD id="texte_a_propos_gauche">\s*Catégorie\s*<\/TD>\s*<TD id="texte_a_propos_droite">\s*<a href="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['Genre'] = $details[2];
			// Année
			preg_match('/<TD id="texte_a_propos_gauche">\s*Sortie france\s*<\/TD>\s*<TD id="texte_a_propos_droite">\s*<a href="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['Annee'] = $details[2];
			// Nb de joueurs
			preg_match('/<TD id="texte_a_propos_gauche">\s*Joueurs max\s*<\/TD>\s*<TD id="texte_a_propos_droite">\s*(.*?)\s*<\/TD>/', $infosComp, $details);
			$jeu['NbJoueurs'] = $details[1];
			// Description
			preg_match('/<TD width="90%" valign="top" id="texte_description">\s*(.*?)\s*<\/TD>/', $infosComp, $details);
			$jeu['Description'] = $details[1];
			// Image
			preg_match('/<TD id="image_jeu_fiche_gauche"(.*?)>\s*<img(.*?)src="(.*?)">\s*<\/TD>/', $infosComp, $details);
			$jeu['Image'] = $details[3];
			// Note
			preg_match('/<TD id="texte_note_globale">\s*(.*?)\s*<\/TD>/', $infosComp, $details);
			$jeu['Note'] = substr($details[1], 0, -1) / 10;
			// Age PEGI
			$jeu['Age'] = $this->socketGetPegi($jeu['Titre']);
			// Images
			preg_match_all('/class="img_test_centre"  src="(.*?)"/', $infosComp, $images);
			$jeu['Images'] = $images[1];
			file_put_contents(dirname(__FILE__).'/trace.txt', $i . "/" . ($socketStart + $limit) . " infos récupérée pour " . $jeu['Titre'] . "\r\n", FILE_APPEND);
			// Ajout au tableau
			$jeux[] = $jeu;
		}
		return $jeux;
	}

	private function socketGetPegi( $jeu ) {
		$jeuUrl = urlencode($jeu);
		$jeuUrl = str_replace('%20', '+', $jeuUrl);
		$infos = file_get_contents('http://www.pegi.info/fr/index/global_id/505/?searchString=' . $jeuUrl);
		preg_match('/icon(\d+).gif/', $infos, $pegi);
		$age = $pegi[1];
		// Exceptions portugaises
		if($age == 6) $age = 7;
		if($age == 4) $age = 3;
		return $age;
	}

	/**
	 * Gère les exceptions au niveau des jeux
	 * / devient -
	 * ! devient [null]
	 * @param	Jeu à uniformiser
	 * @return	Jeu OK
	 */
	private function socketCleanJeu( $str ) {
		$str = trim($str);
		$str = str_replace('/', '-', $str);
		$str = str_replace('!', '', $str);
		return $str;
	}

	/**
	 * Retourne l'identifiant de la catégorie Console/Jeux Video
	 * @param	Console
	 * @return	L'ID de la catégorie où placer ou -1 si non trouvé
	 */
	private function getCategorieId( $console ) {
		$res = $this->storproc('Boutique/Categorie/ConsoleLJV/Nom=' . $console);
		if($res) return $res[0]['Id'];
		else return -1;
	}

}