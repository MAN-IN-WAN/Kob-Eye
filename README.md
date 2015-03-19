# Kob-Eye
Kob-Eye est un framework de développement d eprojet web opensource.
Il n'est pas exactement un CMS mais plutot une base de développement fournissant base de donnée, authentification et back office auto-généré.
## Modules:
Chaque module est définie par un fichier XML de définition regroupant:
- L'ensemble des objets du module.
- L'ensemble des propriétés de l'objet.
- L'ensemble des clefs et relation entre chaque objet.
- La définition des structures de contrôle (int / email / range / date / mot de passe / prix / pourcentage etc ...).
- L'ensemble des vues pour chaque objet.
- L'ensemble des filtres prédéfinies.
- Les surcharges de classes en php.
- L'ensemble des attributs spécifiques pour chaque objet et/ou propriétés permettant la génération des interfaces et formaulaires

## Skins
La gestion des interfaces est organisé en Skin dont voici un bref apercu des fonctionnalités:
- Le controleur est définie par l'arborescence des dossiers. (ex: Modules/Boutique/Categorie/List.md définie l'affichage des listes des categories du module boutique.
)
- Les fichiers avec l'extension .md sont dédiés à l'affichage des objet d'un module.
- Les fichiers avec l'extension .bl dont dédiés à l'affichage récurrant d'élément encadrés (ex: Structure d'un mail,d'un panneau etc...).
- Les fichiers md, sont écris dans un language de type macro (KEML) fournissant l'essentiel des fonctionnalités d'affichage (il n'a pas vocation à remplacer un language de programmation mais plutot à simplifier l'intégration et à maîtriser la sécurite).
- Les intégrateurs n'écrivent pas de PHP ni de SQL. Mais uniquement HTML/JS/CSS/KEML.

## Avantages
- Modification du modèle et application des changements sur les interfaces quasi instantanés. L'ajout d'un champ ou d'une clef dans le fchier XML est très rapide, ansuite la synchronisation des modèles corrige automatiquement les interfaces du FRONT et du BACK.
- Le poste intégrateur est protégé par l'utilisation du language de macro. Une skin n'a pas à être maintenue en cas de mise à jour du coeur.
- Le systeme de requete interne (ORM) permet une abstration totale de la gestion des bases de données.
- Systeme de pilote (Mysql/ Sqlite / Flat file / Ldap / Sql Server etc ...) permettant un design des appels en toute liberté en fonction des besions de volume et/ou de performance.
- Gestion des médias : redimensionnement / echelle / optimisation et cache intégrés (appel du type /monfichier.jpg.mini.80x80.jpg). transtypage également disponible (ex: monfichier.jpg.mini.80x80.png)
- Secuité : La gestion des routes est obligatoire, il est impossible d'appeler un fichier php en direct.
- Plusieurs interfaces de back office dynamique déjà disponibles: Bootstrap.2.3 / Bootstrap.3.0 / XHTML / Sencha / Sencha Touch / Flex (appaloosa)
- Génération Serveur SOAP automatisée par module.
- Souplesse d'évolution.
- Plus de 300 références en production.
- Plus de 30 Modules: 
  -Systeme (Groupes / Utilisateurs / Menus / Securité / Recherche)
  -Redaction (Categorie / Article)
  -Explorateur (Dossier / Fichier)
  -Boutique (Categorie / Produit / Références)
  -Fiscalités (Gestion des taxes)
  -Forum
  -Stock
  -Blog
  - etc ...
- 10 ans d'ancienneté.

## A venir
- Documentation à terminée (ex: http://wiki.kob-eye.com)
- API à faire
- 
