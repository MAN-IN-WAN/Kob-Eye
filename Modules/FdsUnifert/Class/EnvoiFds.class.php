<?PHP

class EnvoiFds extends genericClass{
	
	/**
	 * sendMail
	 * Envoie un mail à un client signifiant qu'une nouvelle fiche de sécurité est mise à sa disposition.
	 * @param $first Boolean Premier envoi/rappel
	 * @return 
	 */
        public function sendMail($first = 0){
                $adminMail = $GLOBALS['Systeme']->Conf->get('GENERAL::INFO::ADMIN_MAIL');
                //On recupère les parents qui ont les information dont on a besoin
                $client = $this->getParents('Client');
                if(!sizeof($client))
                        return false;
                $client = $client[0];
                $mailTo = '';
                $temp = array();
                
                $contacts = $client->getChildren('Contact');
                foreach ($contacts as $contact){
                        if($contact->Fds){
                                $temp[] = $contact->Mail;
                        }
                }
                $mailTo = implode(', ',$temp);
                $mailTo = $mailTo != '' ? $mailTo : $client->Mail;
                
                $fds = $this->getParents('Fds');
                if(!sizeof($fds))
                        return false;
                $fds = $fds[0];
                
                if($first){
                      	$content = '    Madame, Monsieur, Cher client,<br />
                                        <br />
                                        Pour le compte : '. $client->Societe .', code client : '. $client->Code .'.
                                        <br />
                                        Conformément aux règlements en vigueur, nous vous informons de la mise à disposition dans votre espace client de la fiche de sécurité '.$this->VersionFDS.' : '.$fds->Description.'<br />
                                        <br />
                                        D\'après notre système d\'envoi des fiches de données de sécurité, vous êtes responsable de la réception de ces informations pour votre société.<br />
                                        Si ce n\'était pas le cas, nous vous prions de bien vouloir nous faire connaître le nom et l’adresse e-mail de la personne en question, en renseignant le formulaire de contact disponible sur internet accessible à cette adresse : <a href="http://www.unifert.fr/Contact">www.unifert.fr/Contact</a>.<br />
                                        <br /> 
                                        Vous en souhaitant bonne réception, nous vous prions de bien vouloir agréer, Madame, Monsieur, Cher client, l’expression de nos salutations distinguées.<br />
                                        <br /> 
                                        L’équipe d’UNIFERT France SAS<br />
                                        Ce mail est envoyé automatiquement, merci de ne pas y répondre.<br />
                                        Pour nous contacter : <a href="mailto:'.$adminMail.'">'.$adminMail.'</a> <br />';
                        
                        
                        $sujet ='Unifert : Nouvelle FDS '.$fds->Nom;
                } else {
                      	$content = '    Madame, Monsieur, Cher client,<br />
                                        <br />
                                        Pour le compte : '. $client->Societe .', code client : '. $client->Code .'.
                                        <br />
                                        Conformément aux règlements en vigueur, nous vous informons de la mise à disposition dans votre espace client de la nouvelle version de la FDS '.$this->VersionFDS.' : '.$fds->Description.'<br />
                                        <br />        
                                        D\'après notre système d\'envoi des fiches de données de sécurité, vous êtes responsable de la réception de ces informations pour votre société.<br />
                                        Si ce n\'était pas le cas, nous vous prions de bien vouloir nous faire connaître le nom et l\'adresse e-mail de la personne en question, en renseignant le formulaire de contact disponible sur internet accessible à cette adresse : <a href="http://www.unifert.fr/Contact">www.unifert.fr/Contact</a>.<br />
                                        Vous en souhaitant bonne réception, nous vous prions de bien vouloir agréer, Madame, Monsieur, Cher client, l\'expression de nos salutations distinguées.<br />
                                        <br />
                                        L\'équipe d’UNIFERT France SAS<br />
                                        Ce mail est envoyé automatiquement, merci de ne pas y répondre.<br />
                                        Pour nous contacter : <a href="mailto:'.$adminMail.'">'.$adminMail.'</a> <br />';
                                        
                        $sujet ='Unifert : Mise a jour de la FDS '.$fds->Nom;
                }
		
                
                //Prise en compte de Mail.bl
                $bloc = new Bloc();
                $bloc->setFromVar("Mail",$content,array("BEACON"=>"BLOC"));
                $Pr = new Process();
                $bloc->init($Pr);
                $bloc->generate($Pr);
                
                
                //Creation du mail.
                $mail = new Mail();
                $mail->Subject($sujet);
                $mail->From($adminMail);
                //$mail->To('gcandella@abtel.fr'); //TEST
                
                $mail->To($mailTo); //PROD
                $mail->ReplyTo($adminMail);
                //$mail->Body($content);
                $mail->Body($bloc->Affich());
                $mail->Priority('1');
                $mail->BuildMail();
                $mail->Send();
				
                // Ajout création d'un compte pour garder une preuve
                //Creation du mail special
                $mail2 = new Mail();
		$sujet .=" : Mail envoyé à " . $mailTo ;
                $mail2->Subject($sujet);
                $mail2->From($adminMail);
                $mail2->To('fds@unifert.fr'); 
                $mail2->Body($bloc->Affich());
                $mail2->Priority('1');
                $mail2->BuildMail();
                $mail2->Send();
				
                //On verifie tout les envois non vus et si il correspondent au meme client et fds on passe en obsolete
                if($first){
                        $envs = $client->getChildren('EnvoiFds/Obsolete=0&&DateLecture=0&&Id!='.$this->Id);
                        if(!sizeof($envs))
			return true;
			foreach($envs as $env){
				$fdsEnv = $env->getParents('Fds');
				if(!sizeof($fdsEnv)) continue;
				if($fdsEnv[0]->Id == $fds->Id){
					$env->Obsolete = 1;
					$env->Save();
					return true;
				}
			}
		}
		return true;
	}
}