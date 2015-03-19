// (c) 2007 Marc Laffitte - www.laffitte.com

var gImages = new Array();

function chargerImage(urlImage)
{
  var img = new Image();
  gImages.push(img);
  img.src = urlImage;
}

function chargerImagesDansTagsA()
{
  var extensions = new Array("jpg", "gif", "png");  // ajouter ici des extensions si nécessaire
  var marqueursDebutImage = new Array("'", "\"");
  
  var nbLiens = document.links.length;
  var nbExtensions = extensions.length;
  var nbMarqueurs = marqueursDebutImage.length;
  
  for ( i=0; i<nbLiens; i++ )
  {
    var lien = document.links[i];
    var textOnClick = lien.onclick;
    var oTextOnClick = new String(textOnClick);
    for ( j=0; j<nbExtensions; j++ )
    {
      var extension = "." + extensions[j];
      var positionExtension = oTextOnClick.search(extension);
      if ( positionExtension > 0 )
      {
        oText = oTextOnClick.substring(0, positionExtension);  // contient la valeur de onclick jusqu'au nom de l'image sans l'extension
        for ( k=0; k<nbMarqueurs; k++ )
        {
          var positionDernierMarqueur = oText.lastIndexOf(marqueursDebutImage[k]);
          if ( positionDernierMarqueur >= 0 )
          {
            oText = oText.substring(positionDernierMarqueur+1);
            urlImage = oText + extension;
            chargerImage(urlImage);
            
            // On va mettre un onclick qui va bien
            // Petit test sur le onclick pour éviter de remplacer ce qu'il ne faut pas.
            // Il doit contenir window.open et pop.print().
            if ( oTextOnClick.search("window.open") != -1 && oTextOnClick.search("pop.print()") != -1 )
            {
              // mais on doit d'abord stocker les infos dont on a besoin
              lien.grandeImage = urlImage;  // trop cool, on peut ajouter des propriétés aux objets comme on veut
              // Le nouveau onclick pour le lien (cf. http://www.brainjar.com/dhtml/events/ )
              lien.onclick = afficherEtImprimerGrandeImage;
            }
            // on a trouvé le nom de l'image donc:
            j = nbExtensions;  // pas la peine de chercher une image avec une autre extension => on sortira de la boucle sur les extensions
            break;             // on sort de la boucle en cours
          }
        }
      }
    }
  }
}

function afficherEtImprimerGrandeImage(event)
{
  afficherEtImprimerImage(this.grandeImage);
}

// On aurait pu aussi ouvrir un fichier php à qui on aurait passé en paramètre le nom de l'image à afficher.
// Mais ça aurait fait un fichier en plus à mettre sur le site, et là ça marche sans php
function afficherEtImprimerImage(urlFichier)
{
  win = window.open();
  doc = win.document;
  doc.writeln('<html>');
  doc.writeln('<head>');
  doc.writeln('<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">');
  doc.writeln('<title>Image à imprimer</title>');
  doc.writeln('</head>');
  doc.writeln('<body style="margin:0; padding:0">');
  // setTimeout ci-dessous nécessaire avec FireFox PC: sinon, affichage d'une fenêtre grise sous la boîte de dialogue d'impression
  doc.writeln('<img src="' + urlFichier + '" alt="" onload="window.setTimeout(\'window.print()\', 100);">');
  doc.writeln('</body>');
  doc.write('</html>');
  doc.close();
  return false;
}