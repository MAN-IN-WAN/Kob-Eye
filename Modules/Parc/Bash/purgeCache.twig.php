<h1>PURGE CACHE PROXY</h1>
<?php
if (isset($_GET['host'])&&!empty($_GET['host'])){
    //recherche de l'hébrgrement
    $host = Sys::getOneData('Parc','Host/NomLDAP='.$_GET['host']);
    if (is_object($host)){
        //lancement du vidage de cache
        $host->emptyProxyCacheTask();
        echo 'Le cache de l\'hébergement '.$host->Nom.' sera purgé dans quelques secondes.';
    }else{
        echo'Hébergement '.$_GET['host'].' introuvable';
    }
}else  echo'<h3>USAGE:  http://admin.maninwan.fr/Parc/Bash/purgeCache.htm?host=NOM_HEBERGEMENT </h3><p> Vous trouverez le nom de l\'hébergement sur la fiche hébergement du management. </p><br /><br /><br /><img src="http://admin.maninwan.fr/Home/heb_name.png" />';
