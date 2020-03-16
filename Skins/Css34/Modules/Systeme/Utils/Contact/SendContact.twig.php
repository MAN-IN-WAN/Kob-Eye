<?php
$data = json_decode(file_get_contents('php://input'),true);
$data = $data['data'];
$res = array('success'=>1,'error'=>0);
if(($data['a']+$data['b'])!=$data['c']){
    $res['success']=false;
    $res['error']=true;
    $res['C_Calc_Error'] = true;
    echo json_encode($res);
    die();
}
$err=false;
if(empty($data['nom'])) {
    $res['success']=false;
    $res['error']=true;
    $res['C_Nom_Error'] = true;
    $err = true;
}
if(empty($data['mail']) || !Utils::isMail($data['mail'])){
    $res['success']=false;
    $res['error']=true;
    $res['C_Mail_Error'] = true;
    $err = true;
}
if(!$err){
    require_once ("Class/Lib/Mail.class.php");

//Mail a css34
    $Mail = new Mail();
    $Mail->Subject("Message de ".$vars['Domaine']." - ".$data['objet']);
    $Mail -> From( $data['mail']);
    $Mail -> ReplyTo($data['mail']);
    $Mail -> To($GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::MAIL'));
    /*$Mail -> Bcc('web@abtel.fr');*/
    $bloc = new Bloc();
    $mailContent = '
                    <strong>Objet de la demande</strong> : '.(!empty($data['objet'])?$data['objet']:'Non précisé').'<br/>

                    <strong>Envoyé par</strong> : <span style=\"text-transform:uppercase\">'.$data['nom'].'</span> '.$data['prenom'].'<br/>
                    
                    <strong>Adresse</strong> : '.(!empty($data['adresse'])?$data['adresse']:'Non précisé').'<br/>
                    <strong>Code postal</strong> : '.(!empty($data['cp'])?$data['cp']:'Non précisé').'<br/>
                    <strong>Ville</strong> : '.(!empty($data['ville'])?$data['ville']:'Non précisé').'<br/>

                    <strong>Numéro de téléphone</strong> '.(!empty($data['phone'])?$data['phone']:'Non précisé').' <br/>

                    <strong>Message</strong> : '.(!empty($data['message'])?$data['message']:'Non précisé').'<br />
                   
					<strong>Adresse e-mail</strong> : '.$data['mail'].'<br/>';

    $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
    $Pr = new Process();
    $bloc -> init($Pr);
    $bloc -> generate($Pr);
    $Mail -> Body($bloc -> Affich());
    $Mail -> Send();


//Mail à l'auteur du message
    $Mail = new Mail();
    $Mail->Subject("Message de ".$vars['Domaine']." - Confirmation");
    $Mail -> From($GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::MAIL') );
    $Mail -> ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::RESERVATION::MAIL'));
    $Mail -> To($data['mail']);
   /* $Mail -> Bcc('web@abtel.fr');*/
    $bloc = new Bloc();
    $mailContent = 'Bonjour '.$data['prenom'].' <span style="text-transform:uppercase">'.$data['nom'].'</span>,<br />
					Nous avons bien reçu votre demande par email et vous remercions de votre confiance.<br />
					Nous traitons votre demande dans les plus brefs délais.';

    $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
    $Pr = new Process();
    $bloc -> init($Pr);
    $bloc -> generate($Pr);
    $Mail -> Body($bloc -> Affich());
    $Mail -> Send();
}

echo json_encode($res);