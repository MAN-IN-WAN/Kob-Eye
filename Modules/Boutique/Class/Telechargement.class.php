<?php
class Telechargement extends genericClass{
	/**
	 * getFileDownload
	 * Télécharge le fichier
	 */
	public function getFileDownload() {
        $mimeTypes = array(
                'pdf' => 'application/pdf',
                'txt' => 'text/plain',
                'html' => 'text/html',
                'exe' => 'application/octet-stream',
                'zip' => 'application/zip',
                'doc' => 'application/msword',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',
                'gif' => 'image/gif',
                'png' => 'image/png',
                'jpeg' => 'image/jpg',
                'jpg' => 'image/jpg',
                'php' => 'text/plain'
            );

            //récupération du client.
            $cli = Sys::getData('Boutique','Client/UserId='.Sys::$User->Id);
            //verification du client connecté
            if (isset($cli[0])&&is_object($cli[0])){
                $cli = $cli[0];
                //vérification de l'achat de ce téléchargement
                if (Sys::getCount('Boutique','Client/'.$cli->Id.'/Telechargement/'.$this->Id)>0){
                    // Send Headers
                    //-- next line fixed as per suggestion --
                    header('Content-Type: ' . $mimeTypes['pdf']); 
//                    header('Content-Disposition: attachment; filename="' . $this->Nom . '"');
                    header('Content-Disposition: attachment; filename="' . $this->Fichier . '"');

                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    header('Cache-Control: private');
                    header('kirigami: private');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    readfile($this->Fichier);
                }else die('Fichier introuvable');
            }die('Fichier introuvable');
    	}


	/**
	 * getFileDownload
	 * Télécharge le fichier
	 */
	public function getFileDownloadAbo($Fichier) {
        	$mimeTypes = array(
                	'pdf' => 'application/pdf',
                	'txt' => 'text/plain',
                	'html' => 'text/html',
                	'exe' => 'application/octet-stream',
                	'zip' => 'application/zip',
                	'doc' => 'application/msword',
                	'xls' => 'application/vnd.ms-excel',
                	'ppt' => 'application/vnd.ms-powerpoint',
                	'gif' => 'image/gif',
                	'png' => 'image/png',
                	'jpeg' => 'image/jpg',
                	'jpg' => 'image/jpg',
                	'php' => 'text/plain'
            	);
            	header('Content-Type: ' . $mimeTypes['pdf']); 
		header('Content-Disposition: attachment; filename="eee"');	
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
                header('Cache-Control: private');
                header('kirigami: private');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    readfile($Fichier);
             
    	}

 

}
