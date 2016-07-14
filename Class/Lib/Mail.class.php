<?php
include 'Class/Lib/PHPMailer.class.php';
if (!defined('MAIL_TYPE'))define('MAIL_TYPE','mail');
class Mail extends PHPMailer{
    public $From              = MAIL_FROM;
    public $Sender            = MAIL_FROM;
    public $Mailer            = MAIL_TYPE;
    public $Host          = MAIL_SERVER;
    public $Port          = MAIL_PORT;
    public $Username      = MAIL_USER;
    public $Password      = MAIL_PASS;
    public $SMTPDebug     = false;

    function Mail(){
		parent::__construct(false);
	}
	function Subject( $subject ){
		$this->Subject = $subject;
	}
	function From( $from ){
		$this->SetFrom($from);
	}
	function ReplyTo( $address ){
		$this->AddReplyTo($address);
	}
	function To( $to ){
		$this->AddAddress($to);
	}
	function Cc( $cc ){
		$this->AddCC($cc);
	}
	function Bcc( $bcc ){
		$this->addBCC($bcc);
	}
	function Body($body){
        $this->isHTML(true);
		$this->Body = $body;
        $this->AltBody = strip_tags($body);
	}
	function Attach( $filename, $nomclair="", $filetype = "", $disposition = "attachment" ){
		$this->AddAttachment($filename,$nomclair);
	}
    function BuildMail(){}
    function Priority(){}
}
?>