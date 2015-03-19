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
		$this->SaveTypeSupport();
		$this->PrixAPartirDe();		

		parent::Save();
	}

	/**
	 * Enregistrement du type de support
	 * @return	void
	 */
	private function SaveTypeSupport() {
		$Tab2 = $this->storproc("Boutique/Categorie/Produit/".$this->Id,false,0,1);
		if($Tab2[0]) {
			if ($Tab2[0]['Nom']!="Jeux Video"){
				$this->TypeSupport = $Tab2[0]["TypeSupport"];
			}else{
				$Tab = $this->storproc("Boutique/Categorie/Categorie/".$Tab2[0]["Id"],false,0,1);
				if($Tab[0]) {
					$this->TypeSupport = $Tab[0]["TypeSupport"];
				}
			}
		}
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
					$Mcf = genericClass::createInstance('Boutique', $Tab2[0]);
				}
				else {
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
		$Props = $this->SearchOrder();
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
		$params['min_word_length'] = 2;  //minimum length of single words
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
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference/Etat=Neuf&&Actif=1');
		return is_array($refs) ? sizeof($refs) : 0;
	}

	/**
	 * Retourne le nombre de références "occasion" pour ce produit
	 * TODO Vérifier que ce soit bien Etat!=Neuf
	 * @return	Nombre
	 */
	public function getNbOccasions() {
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference/Etat!=Neuf&&Actif=1');
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
	private function PrixAPartirDe() {
		// Calcul du prix parmi les références dispos
		$refs = $this->storproc('Boutique/Produit/' . $this->Id . '/Reference/Actif=1');
		$nbRefs = count($refs);
		$prixMini = -1;
		for($i=0; $i<$nbRefs; $i++) :
			if($refs[$i]['Tarif'] < $prixMini or $prixMini == -1) :
				$prixMini = $refs[$i]['Tarif'];
			endif;
		endfor;
		return $this->APartirDe = $prixMini;
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
		$produits = $this->storproc($req, '', '0', '20', 'DESC', 'Ventes');
		if(empty($produits)) return array();
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
		$produits = $this->storproc($req, '', '0', '20', 'ASC', 'APartirDe');
		if(empty($produits)) return array();
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
		$prod->Nom = $p['Nom'] ;
		$prod->Prix = $p['APartirDe'] ;
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
		/* Position actuelle dans la boucle*/
		$Console = (isset($_GET['Console'])) ? $_GET['Console'] : 0;
		$Page = (isset($_GET['Page'])) ? $_GET['Page'] : 1;

		/* Récupération des infos sur la console à mettre à jour */
		$req = $this->storproc('Boutique/ConsoleLJV', '', $Console, 1);
		if($req == null or empty($req)) return; // Mise à jour terminée
		$Urlconsole = $req[0]['Url'];

		/* Récupération de la catégorie correspondante */
		$req = $this->storproc('Boutique/Categorie/ConsoleLJV/Nom=' . $req[0]['Nom']);
		$categorieID = $req[0]['Id'];

		/* Mise à jour de la console */
		$result = $this->socketUpdateJeux($Urlconsole, $categorieID, $Page);

		/* Redirection selon result = console terminée */
		if($result) header('Location: ' . $_SERVER['REDIRECT_URL'] . '?Console=' . ($Console + 1));
		else header('Location: ' . $_SERVER['REDIRECT_URL'] . '?Console=' . $Console . '&Page=' . ($Page + 1));
	}

	/**
	 * Mise à jour des jeux pour une console
	 * @param	String	URL de la console
	 * @return	True si la console est terminée / False sinon
	 */
	function socketUpdateJeux( $Urlconsole, $IDCategorie, $Page ) {
		// Récupération des jeux, s'il n'y en a aucun c'est qu'on a fini la console
		$jeux = $this->socketGetJeux($Urlconsole, $Page);
		if(empty($jeux)) return true;

		// Enregistrement des jeux
		foreach($jeux as $jeu) :
			$jeu['GenreId'] = $this->getGenreId($jeu['Genre']);
			$jeu['CategorieId'] = $IDCategorie;
			$req = 'Boutique/Categorie/' . $jeu['CategorieId'] . '/Produit/Nom=' . addslashes($jeu['Titre']);
			$produit = $this->storproc($req);
			if($produit) {
				// Le jeu existe déjà pas besoin de le créer de nouveau - Trace
				file_put_contents(dirname(__FILE__).'/trace.txt', $jeu['Titre'] . " existe déjà\r\n----\r\n", FILE_APPEND);
				// MAJ Genre
				$obj = genericClass::createInstance('Boutique', $produit[0]);
				$obj->AddParent('Boutique/Genre/' . $jeu['GenreId']);
				$obj->Save();
			}
			else {
				file_put_contents(dirname(__FILE__).'/trace.txt', $jeu['Titre'] . " ajouté\r\n", FILE_APPEND);
				// Le jeu n'existe pas on l'ajoute dans un fichier temporaire si il n'y est pas encore
				$fichier = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MiseAJour' . DIRECTORY_SEPARATOR . 'newGames_' . $IDCategorie . '.temp';
				$actual = is_file($fichier) ? file_get_contents($fichier) : '';
				$list = empty($actual) ? array() : unserialize($actual);
				$garder = true;
				$jeu['tempID'] = md5($jeu['Titre']);
				for($i=0; $i<sizeof($list) && $garder; $i++) $garder = ($j['tempID'] != $jeu['tempID']);
				if($garder) :
					$list[] = $jeu;
					file_put_contents($fichier, serialize($list));
				endif;
			}
		endforeach;
	}

	/**
	 * Récupère les infos d'un jeu dans le fichier sérialisé,
	 * l'enregistre en tant que produit et le retire de la liste
	 */
	private function enregistrerJeuxCron( $catID, $hash ) {
		$fichier = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MiseAJour' . DIRECTORY_SEPARATOR . 'newGames_' . $catID . '.temp';
		$list = unserialize(file_get_contents($fichier));
		foreach( $list as $i => $jeu ) :
			if($jeu['tempID'] == $hash) :
				// C'est ce jeu que l'on veut enregistrer
				$obj = genericClass::createInstance('Boutique', 'Produit');
				$obj->AddParent('Boutique/Genre/' . $jeu['GenreId']);
				$obj->AddParent('Boutique/Categorie/' . $jeu['CategorieId']);
				// Gestion de l'image
				if(!empty($jeu['Image'])) {
					$name = 'Home/ProduitsImg/' . fileDriver::checkName($jeu['Titre']) . '-' . time() . '.jpg';
					$bits = "";
					while(empty($bits)) $bits = @file_get_contents($jeu['Image']);
					file_put_contents($name, $bits);
					$jeu['Image'] = $name;
					$obj->Set('Image', $jeu['Image']);
				}
				// Enregistrement des champs
				$obj->Set('Nom', stripslashes($jeu['Titre']));
				$obj->Set('Editeur', $jeu['Editeur']);
				$obj->Set('Description', $jeu['Description']);
				$obj->Set('Annee', '01/01/' . $jeu['Annee']);
				$obj->Set('Joueur', $jeu['NbJoueurs']);
				$obj->Set('Age', $jeu['Age']);
				$obj->Set('Note', $jeu['Note']);
				$obj->Save();
				// Enregistrement des photos pour la galerie
				foreach($jeu['Images'] as $k => $img) {
					$name = 'Home/GalerieImg/' . fileDriver::checkName($jeu['Titre']) . '-' . ($k + 1)  . '-' . time() . '.jpg';
					$bits = file_get_contents($img);
					file_put_contents($name, $bits);
					$objImg = genericClass::createInstance('Boutique', 'Photo');
					$objImg->Set('Nom', $jeu['Titre'] . ' - Image ' . ($k + 1));
					$objImg->Set('Image', $name);
					$objImg->AddParent('Boutique/Produit/' . $obj->Id);
					$objImg->Save();
				}
				unset($list[$i]);
				file_put_contents($fichier, serialize($list));
				return;
			endif;
		endforeach;
	}

	public function saveProductsToUpdate( $catID ) {
		if(!isset($_POST['jeux'])) return;
		foreach($_POST['jeux'] as $hash) $this->enregistrerJeuxCron( $catID, $hash );
	}

	/**
	 * Retourne la liste des produits qui ont été enregistrés lors du précédent CRON
	 * @param	int	ID de la catégorie concernée
	 * @return	array
	 */
	public function getProductsToUpdate( $catID ) {
		$fichier = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MiseAJour' . DIRECTORY_SEPARATOR . 'newGames_' . $catID . '.temp';
		$content = is_file($fichier) ? file_get_contents($fichier) : '';
		return empty($content) ? array() : unserialize($content);
	}


	/**
	 * Retourne la liste des jeux d'une console avec le détail à chaque fois
	 * @param	String	URL de la Console
	 * @param	int		ID de la Catégorie de la console
	 * @param	int		Page en cours
	 * @return	Liste des jeux ou vide si l'on a atteint une page qui n'existe pas
	 */
	private function socketGetJeux( $Urlconsole, $Page ) {

		// Récupération des jeux par expression régulière + trace
		$Urlconsole .= '?PageNum=' . $Page;
		$content = file_get_contents($Urlconsole);
		preg_match_all('/<p class="pdt_nom hover2"><a href="(.*?)">(.*?)<\/a><\/p>/', $content, $matches);
		file_put_contents(dirname(__FILE__).'/trace.txt', "URL " . $Urlconsole . "\r\n", FILE_APPEND);

		// Construction du résultat
		$jeux = array();
		$nbJeux = sizeof($matches[0]);

		for($i=0; $i<$nbJeux; $i++) {
			// Sortie de secours
			if(is_file(dirname(__FILE__).'/stop.txt')) die;

			// Détails directement accessibles
			$jeu = array();
			$jeu['Titre'] = $this->socketCleanJeu($matches[2][$i]);
			$jeu['Url'] = $matches[1][$i];

			// Autre détails : page détail du jeu
			$infosComp = "";
			while(empty($infosComp)) $infosComp = @file_get_contents('http://www.2xmoinscher.com' . $jeu['Url']);

			// Editeur
			preg_match('/<td> Editeur<\/td>\s*<td> <a HREF="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['Editeur'] = trim($details[2]);

			// Développeur
			preg_match('/<td> Développeur<\/td>\s*<td> <a HREF="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['Developpeur'] = trim($details[2]);

			// Genre
			preg_match('/<td> Genre<\/td>\s*<td> <a HREF="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['Genre'] = trim($details[2]);

			// Annee
			preg_match('/<td> Année de sortie<\/td>\s*<td> (.*?)<\/td>/', $infosComp, $details);
			$jeu['Annee'] = trim($details[1]);

			// Nb de joueurs
			preg_match('/<td> Nombre de joueurs<\/td>\s*<td> <a HREF="(.*?)">(.*?)<\/a>/', $infosComp, $details);
			$jeu['NbJoueurs'] = trim($details[2]);

			// Age PEGI
			// -> Il est précisé sur la page
			preg_match('/<img class="pegi" height="48" width="40" src="http:\/\/imgcss.2xmoinscher.com\/site\/template\/2X\/image\/icones\/age(.*?).gif">/', $infosComp, $detailsAge);
			$jeu['Age'] = trim(@$detailsAge[1]);
			// -> Sinon on va le chercher sur Pegi.info
			// if(empty($jeu['Age'])) $jeu['Age'] = $this->socketGetPegi($jeu['Titre']);

			// Image(s)
			preg_match_all('/<a rel="imgproduct"(.*?)href="(.*?)"/', $infosComp, $details);
			$jeu['Image'] = $details[2][0];
			if($details[2]) unset($details[2][0]); // On enlève la première image qui est la jaquette
			$jeu['Images'] = $details[2];

			// Description
			$sansLigne = str_replace("\r", "", $infosComp);
			$sansLigne = str_replace("\n", "", $infosComp);
			preg_match('/<div id="cont_descriptif">(.*?)<\/div>/', $sansLigne, $details);
			preg_match('/<p>(.*?)<\/p>/', $details[1], $detailsDesc);
			$jeu['Description'] = strip_tags($detailsDesc[1], '<br>');

			// Note
			preg_match('/<input class="auto-submit-star {half:true}" value="([0-9])" name="star1" type="radio" checked="checked" \/>/', $infosComp, $details);
			$jeu['Note'] = $details[1];

			// Ajout au tableau
			$jeux[] = $jeu;
		}
		return $jeux;
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
		$str = str_replace('+', '', $str);
		$str = str_replace('&amp;', 'et', $str);
		$str = str_replace('&', 'et', $str);
		return $str;
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

}